<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Client;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Stat;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First admin login, created only if it does not exist yet (re-seeding never resets a password).
        // Set ADMIN_EMAIL / ADMIN_PASSWORD in .env; with no ADMIN_PASSWORD a random one is generated and printed once.
        $adminEmail = env('ADMIN_EMAIL') ?: 'admin@regantoitsolution.com';
        $adminPassword = env('ADMIN_PASSWORD') ?: Str::random(16);
        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            ['name' => 'Admin', 'password' => $adminPassword, 'is_admin' => true]
        );
        if ($admin->wasRecentlyCreated) {
            $this->command?->info("Admin account created: {$adminEmail} / " . (env('ADMIN_PASSWORD') ? '(password from .env)' : $adminPassword));
        }

        $settings = [
            'site_name' => 'Reganto IT Solution',
            'tagline' => 'We design and build digital products that grow your business.',
            'logo' => 'https://regantoitsolution.com/assets/images/logos/logo.png',
            'favicon' => '',
            'email' => 'info@regantoitsolution.com',
            'phone' => '+1 (929) 699-8935',
            'whatsapp' => 'https://wa.me/19296998935',
            'address' => '',
            'book_call_url' => 'https://regantoitsolution.com/client/book-call',
            'facebook' => 'https://www.facebook.com/RegantoITSolution',
            'instagram' => 'https://www.instagram.com/regantoitsolution',
            'linkedin' => 'https://www.linkedin.com/',
            'youtube' => 'https://www.youtube.com/@RegantoITSolution',
            'tiktok' => 'https://www.tiktok.com/@regantoitsolution',
            'hero_title' => 'Your Trusted IT Solution Partner',
            'hero_subtitle' => 'Software, UI/UX design, branding, marketing and cyber security for startups and growing companies.',
            'footer_text' => 'Reganto IT Solution. All rights reserved.',
        ];
        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        $this->call(ServicesSeeder::class);

        $projects = [
            ['StorePal E-Commerce Store', 'Web Development', 'https://storepal.com.bd/', true],
        ];
        Project::whereIn('slug', ['storepal', 'gadget-depo-bd', 'excellence-visa-consultancy', 'regantify-gadget-depo-bd-store'])->delete();
        foreach ($projects as $i => [$title, $cat, $url, $featured]) {
            Project::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title, 'category' => $cat, 'url' => $url,
                'short_description' => "{$title}: a {$cat} project delivered by Reganto IT Solution.",
                'is_featured' => $featured, 'sort_order' => $i + 1,
            ]);
        }

        $faqs = [
            ['What type of UI/UX projects do you specialize in?', 'SaaS dashboards, web apps, mobile apps, and conversion-focused websites — clean UX with premium UI.'],
            ['Do you provide full website development along with design?', 'Yes. End-to-end UI/UX plus full-stack development, with custom builds or your preferred CMS/stack.'],
            ['What makes Reganto IT Solution different?', 'Strategy-first UX, premium UI, production-grade development and a clear, transparent delivery process.'],
            ['Can you redesign an existing website or product?', 'Absolutely. We run a UX audit, redesign key screens, improve flows and modernize the UI while keeping what works.'],
            ['How do you handle timelines and delivery?', "Clear milestones, weekly updates and transparent scope — you always know what's in progress and what's next."],
            ['Who will I work with?', 'A dedicated team of designer(s), developer(s) and a project coordinator for fast, clear communication.'],
        ];
        Faq::query()->delete();
        foreach ($faqs as $i => [$q, $a]) {
            Faq::create(['question' => $q, 'answer' => $a, 'sort_order' => $i + 1]);
        }

        $testimonials = [
            ['Shariful Islam', 'Asst. Manager, Ratul Properties Ltd', 'The work quality exceeded expectations and the process was smooth from start to finish.'],
            ['Saddam Ali', 'Founder & MD, Al Safa', 'Their approach was strategic, the designs were premium, and the team was responsive throughout.'],
            ['Abrar', 'CEO & Owner, AKC Play', 'We saw improvements in engagement and overall user experience. The team delivered consistently.'],
            ['Sajjad', 'Owner, Burger Overloaded × Pitzah', 'Delivered ahead of schedule and handled feedback with care. Professional and highly reliable.'],
            ['Ahmad Ibn Saif', 'Assistant Manager, ULTIMA', 'They helped us standardize our design system across campaigns. Support made a real difference.'],
        ];
        Testimonial::query()->delete();
        foreach ($testimonials as $i => [$n, $r, $qt]) {
            Testimonial::create(['name' => $n, 'role' => $r, 'quote' => $qt, 'sort_order' => $i + 1]);
        }

        $stats = [['Startups Supported', 40, '+'], ['Projects Designed', 80, '+'], ['Years of Experience', 3, '+'], ['Clutch Rating · 30+ Reviews', 4.9, '★']];
        Stat::query()->delete();
        foreach ($stats as $i => [$l, $v, $s]) {
            Stat::create(['label' => $l, 'value' => $v, 'suffix' => $s, 'sort_order' => $i + 1]);
        }

        Client::query()->delete();
        foreach (['Ratul Properties', 'Al Safa', 'AKC Play', 'Burger Overloaded', 'Pitzah', 'ULTIMA'] as $i => $n) {
            Client::create(['name' => $n, 'sort_order' => $i + 1]);
        }

        Blog::query()->delete();
        $blogs = [
            ['Voice UI (VUI): Designing Experiences Beyond the Screen', 'assets/img/bg.webp', 8],
            ['AI in UI/UX: Practical Use-Cases That Improve Products', 'assets/img/bg2.webp', 5],
            ['UI/UX Trends: What Modern Products Are Doing Differently', 'assets/img/bg3.webp', 10],
        ];
        foreach ($blogs as $i => [$t, $img, $min]) {
            Blog::create([
                'title' => $t, 'slug' => Str::slug($t), 'category' => "{$min} min read", 'image' => $img,
                'excerpt' => 'A short sample article. Edit it from the admin panel.',
                'content' => '<p>Sample content.</p>',
                'published_at' => now()->subDays($i * 7),
            ]);
        }
    }
}
