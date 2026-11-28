<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Support\Media;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * The home page, built in React (resources/js/Pages/Home.jsx): the /home2 design with the travelling 3D laptop.
     * The hero, logos and Services come with the page; "Recent work", reviews and FAQ follow in a second request
     * right after the page shows (Inertia deferred props), so the first answer stays small.
     */
    public function index()
    {
        $site = Setting::allSettings();
        $services = Service::active()->withCount('items')->with('items')->get();
        $serviceData = $services->map(fn ($s) => [
            'title' => $s->title,
            'slug' => $s->slug,
            'short' => $s->short_description,
            'items_count' => $s->items_count,
            'items' => $s->items->take(4)->pluck('title')->values(),
        ])->values();

        return Inertia::render('Home', [
            'meta' => [
                'title' => ($site['site_name'] ?? 'Reganto IT Solution') . ' – One studio. Every digital possibility.',
                'description' => $site['tagline'] ?? '',
                'jsonLd' => array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => $site['site_name'] ?? 'Reganto IT Solution',
                    'url' => url('/'),
                    'logo' => asset('assets/img/logo.png'),
                    'email' => $site['email'] ?? null,
                    'telephone' => $site['phone'] ?? null,
                    'sameAs' => array_values(array_filter([$site['facebook'] ?? null, $site['instagram'] ?? null,
                        $site['linkedin'] ?? null, $site['youtube'] ?? null, $site['tiktok'] ?? null])),
                ]),
            ],
            'clients' => Client::active()->get()->map(fn ($c) => ['name' => $c->name, 'logo' => Media::url($c->logo)])->values(),
            'services' => $serviceData,
            'projects' => Inertia::defer(fn () => $this->projects(), 'below'),
            'workOrder' => Inertia::defer(fn () => $this->workOrder(), 'below'),
            'testimonials' => Inertia::defer(fn () => Testimonial::active()->get()->map(fn ($t) => [
                'name' => $t->name,
                'role' => $t->role,
                'quote' => $t->quote,
                'rating' => max(1, min(5, (int) $t->rating)),
                'photo' => Media::url($t->photo),
            ])->values(), 'below'),
            'faqs' => Inertia::defer(fn () => Faq::active()->get(['question', 'answer']), 'below'),
        ]);
    }

    /**
     * /home2: the Blade version of the animated home page, kept as the reference the React home (/) is checked
     * against. One laptop travels down the page (assets/js/home2.js).
     */
    public function home2()
    {
        $services = Service::active()->withCount('items')->with('items')->get();

        return view('home2', [
            'clients' => Client::active()->get(),
            'services' => $services,
            'heroServices' => $services->take(5),
        ]);
    }

    /** One home page section as plain HTML, fetched by assets/js/site.js shortly before it scrolls into view. */
    public function section(string $part)
    {
        $data = match ($part) {
            'services' => ['services' => Service::active()->withCount('items')->with('items')->get()],
            'work' => ['projects' => Project::active()->get()],
            'features' => [],
            'reviews' => ['testimonials' => Testimonial::active()->get()],
            'faq' => ['faqs' => Faq::active()->get()],
            default => abort(404),
        };

        return response(view('home.' . $part, $data))->header('Cache-Control', 'public, max-age=300');
    }

    /** "Recent work" cards: projects with a cover photo, in the admin's order. */
    private function projects(): array
    {
        return Project::active()->get()
            ->map(fn ($p, $i) => [
                'i' => $i,
                'slug' => $p->slug,
                'title' => $p->title,
                'category' => $p->category,
                'cover' => Media::url($p->cover_image),
                'tags' => array_values($p->tags()),
                'portrait' => $p->category === 'Graphics Design',
            ])
            ->filter(fn ($p) => $p['cover'])->values()->all();
    }

    /**
     * Card order per "Recent work" tab (slugs), the same lists as resources/views/home/work.blade.php. Projects the
     * admin marks "Featured" that aren't in these lists go first in every tab they belong to.
     */
    private function workOrder(): array
    {
        $workTabs = ['All', 'Web Development', 'Software', 'UI/UX Design', 'Graphics & Branding'];
        $workOrder = [
            'All' => ['regantify-platform-landing', 'rareman-fashion-store', 'every-step-breathes', 'shopnet-multi-category-store', 'shopnet-mobile-home-ui', 'storepal-e-commerce-store', 'made-to-conquer-rain', 'rareman-mobile-ui', 'shopnet-ai-chat-ui', 'walk-with-authority', 'shopnet-mobile-categories-ui'],
            'Web Development' => ['regantify-platform-landing', 'rareman-fashion-store', 'shopnet-multi-category-store', 'storepal-e-commerce-store'],
            'Software' => ['storepal-e-commerce-store', 'regantify-platform-landing', 'shopnet-multi-category-store', 'rareman-fashion-store'],
            'UI/UX Design' => ['shopnet-ai-chat-ui', 'storepal-e-commerce-store', 'rareman-mobile-ui', 'regantify-platform-landing', 'shopnet-mobile-categories-ui', 'rareman-fashion-store', 'shopnet-mobile-home-ui', 'shopnet-multi-category-store'],
        ];
        $picked = collect($workOrder)->flatten()->unique();
        $newFeatured = Project::active()->get()->filter(fn ($p) => $p->is_featured && $p->cover_image && !$picked->contains($p->slug))
            ->sortBy('sort_order')->pluck('slug')->all();
        foreach ($workTabs as $tag) {
            $workOrder[$tag] = array_values(array_unique(array_merge($newFeatured, $workOrder[$tag] ?? [])));
        }

        return $workOrder;
    }
}
