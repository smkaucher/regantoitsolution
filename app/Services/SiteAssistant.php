<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Answers visitor questions about this website.
 * 1) If AI_API_KEY is set, a small chat model is asked with a compact fact sheet (few tokens).
 * 2) Otherwise, or if that call fails, a built-in keyword engine answers from the same data for free.
 */
class SiteAssistant
{
    /** @param array<int, array{role:string, content:string}> $history */
    public function reply(string $message, array $history = []): string
    {
        if (config('ai.api_key')) {
            $text = $this->askModel($message, $history);
            if ($text !== null) {
                return $text;
            }
        }

        return $this->localReply($message);
    }

    /* ---------------- Model ---------------- */

    private function askModel(string $message, array $history): ?string
    {
        $messages = [['role' => 'system', 'content' => $this->systemPrompt()]];
        foreach (array_slice($history, -6) as $m) {
            if (in_array($m['role'] ?? '', ['user', 'assistant'], true) && is_string($m['content'] ?? null)) {
                $messages[] = ['role' => $m['role'], 'content' => mb_substr($m['content'], 0, 500)];
            }
        }
        $messages[] = ['role' => 'user', 'content' => mb_substr($message, 0, 500)];

        try {
            $res = Http::withToken(config('ai.api_key'))->timeout(config('ai.timeout'))
                ->post(rtrim(config('ai.base_url'), '/') . '/chat/completions', [
                    'model' => config('ai.model'),
                    'messages' => $messages,
                    'max_tokens' => config('ai.max_tokens'),
                    'temperature' => 0.4,
                ]);
            $text = trim((string) $res->json('choices.0.message.content'));

            return $res->successful() && $text !== '' ? $text : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function systemPrompt(): string
    {
        return "You are Reganto AI, the assistant on the website of {$this->facts()['name']}. "
            . 'Answer ONLY about this company and website using the facts below. Be friendly, concise (max 70 words), plain text, no markdown headings. '
            . 'If asked something unrelated or not in the facts, say so briefly and suggest contacting the team. Never invent prices; say pricing depends on the project and to contact us. '
            . "Reply in the language the user writes in (English or Bangla).\n\nFACTS:\n" . $this->factSheet();
    }

    /* ---------------- Facts ---------------- */

    /** @return array<string,mixed> */
    private function facts(): array
    {
        static $facts;
        if ($facts) {
            return $facts;
        }
        $s = Setting::allSettings();

        return $facts = [
            'name' => $s['site_name'] ?? 'Reganto IT Solution',
            'email' => $s['email'] ?? null,
            'phone' => $s['phone'] ?? null,
            'whatsapp' => $s['whatsapp'] ?? null,
            'address' => ($s['address'] ?? null) ?: config('site.address'),
            'map' => config('site.map_url'),
            'services' => Service::active()->with('items')->get(),
            'projects' => Project::active()->get(),
            'team' => collect(config('team.groups'))->flatten(1)->unique('name')->count(),
        ];
    }

    private function factSheet(): string
    {
        $f = $this->facts();
        $lines = ["Company: {$f['name']}, a digital studio building websites, software, UI/UX design, graphics and branding."];
        foreach ($f['services'] as $svc) {
            $lines[] = "Service {$svc->title}: " . $svc->items->pluck('title')->implode(', ') . '.';
        }
        $lines[] = 'Projects: ' . $f['projects']->take(12)->map(fn ($p) => "{$p->title} ({$p->category})")->implode('; ') . '. All are listed on the Projects page with filters.';
        $lines[] = "Team: {$f['team']} members, see the Team page.";
        $lines[] = 'Booking: no online form; book by WhatsApp, email or phone call. We reply within 30 minutes.';
        foreach (['email' => 'Email', 'phone' => 'Phone', 'whatsapp' => 'WhatsApp'] as $k => $label) {
            if ($f[$k]) {
                $lines[] = "{$label}: {$f[$k]}";
            }
        }
        $lines[] = "Office: {$f['address']}";
        $lines[] = 'Pages: Home, Projects, Services, About Us, Team, Blogs, Contact.';

        return implode("\n", $lines);
    }

    /* ---------------- Free local engine ---------------- */

    private function localReply(string $message): string
    {
        $f = $this->facts();
        $q = mb_strtolower($message);
        $has = fn (array $words) => (bool) array_filter($words, fn ($w) => str_contains($q, $w));

        // A specific service by its full name.
        foreach ($f['services'] as $svc) {
            if (str_contains($q, mb_strtolower($svc->title))) {
                return "{$svc->title}: " . $svc->items->pluck('title')->implode(', ') . ".\nDetails: /services/{$svc->slug}\nTo book, " . $this->bookLine();
            }
        }

        if ($has(['hi', 'hello', 'hey', 'salam', 'assalam', 'হাই', 'হ্যালো', 'সালাম']) && mb_strlen($q) < 25) {
            return "Hi! I'm Reganto AI. Ask me about our services, projects, team, pricing or how to book.";
        }
        if ($has(['whatsapp', 'phone', 'call', 'email', 'mail', 'contact', 'number', 'reach', 'যোগাযোগ', 'নম্বর', 'ফোন'])) {
            return $this->contactLine();
        }
        if ($has(['address', 'location', 'office', 'where', 'map', 'visit', 'ঠিকানা', 'কোথায়'])) {
            return "Our office: {$f['address']}.\nMap: {$f['map']}";
        }
        if ($has(['book', 'hire', 'order', 'start', 'quote', 'বুক'])) {
            return 'To book a service, ' . $this->bookLine();
        }
        if ($has(['price', 'cost', 'pricing', 'budget', 'charge', 'rate', 'how much', 'দাম', 'খরচ', 'টাকা'])) {
            return 'Pricing depends on the scope of each project, so we give a custom quote. Tell us what you need: ' . $this->bookLine();
        }
        if ($has(['project', 'portfolio', 'work', 'case', 'example', 'প্রজেক্ট', 'কাজ'])) {
            $list = $f['projects']->reject(fn ($p) => in_array($p->category, ['Graphics Design', 'Branding'], true))
                ->take(5)->map(fn ($p) => "• {$p->title} ({$p->category})")->implode("\n");

            return "Some of our recent work:\n{$list}\n\nWe also design posters and brand banners. Filter everything on the Projects page (/projects) and click a project to see its details.";
        }
        if ($has(['service', 'offer', 'what do you', 'what can', 'ui', 'ux', 'web', 'develop', 'software', 'app', 'brand', 'graphic', 'design', 'সার্ভিস', 'সেবা'])) {
            $list = $f['services']->map(fn ($s) => "• {$s->title}: " . $s->items->take(4)->pluck('title')->implode(', '))->implode("\n");

            return "We offer:\n{$list}\n\nSee all on the Services page (/services).";
        }
        if ($has(['team', 'who', 'founder', 'developer', 'designer', 'expert', 'টিম'])) {
            return "Our team has {$f['team']} designers and engineers. Meet them, with their skills and experience, on the Team page (/team).";
        }
        if ($has(['about', 'company', 'reganto', 'studio', 'কোম্পানি'])) {
            return "{$f['name']} is a digital studio: one team for UI/UX design, web and software development, graphics and branding, from first sketch to launch. Learn more on the About page (/about).";
        }
        if ($has(['time', 'how long', 'delivery', 'deadline', 'support', 'reply', 'respond', 'সময়'])) {
            return 'We reply within 30 minutes. Delivery time depends on the project size, and we share a clear timeline in the quote. To start, ' . $this->bookLine();
        }
        if ($has(['thank', 'thanks', 'ধন্যবাদ'])) {
            return "You're welcome! Anything else I can help with?";
        }

        return 'I can help with our services, projects, team, pricing, location and booking. For anything else, ' . $this->bookLine();
    }

    private function bookLine(): string
    {
        $f = $this->facts();
        $ways = array_values(array_filter([
            $f['whatsapp'] ? "WhatsApp ({$f['whatsapp']})" : null,
            $f['email'] ? "email {$f['email']}" : null,
            $f['phone'] ? "call {$f['phone']}" : null,
        ]));

        return $ways ? 'message us on ' . implode(', or ', $ways) . '. We reply within 30 minutes.' : 'use the Contact page (/contact). We reply within 30 minutes.';
    }

    private function contactLine(): string
    {
        $f = $this->facts();
        $out = [];
        foreach (['whatsapp' => 'WhatsApp', 'email' => 'Email', 'phone' => 'Phone'] as $k => $label) {
            if ($f[$k]) {
                $out[] = "{$label}: {$f[$k]}";
            }
        }

        return ($out ? implode("\n", $out) . "\n" : '') . "Office: {$f['address']}\nWe reply within 30 minutes.";
    }
}
