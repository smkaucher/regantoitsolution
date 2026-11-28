<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Models\Setting;
use App\Support\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

/**
 * The public pages that are built in React (resources/js). It runs only on those routes (see routes/web.php), so
 * the admin panel and the Blade pages never go through it. Every page gets the site settings and the service
 * categories (top bar and footer), the same data AppServiceProvider gives the Blade layout.
 */
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'inertia';

    /** The settings the public pages show. Nothing else from the settings table is sent to the browser. */
    private const SITE_KEYS = ['site_name', 'tagline', 'email', 'phone', 'whatsapp', 'address', 'book_call_url',
        'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok', 'hero_subtitle', 'footer_text'];

    public function share(Request $request): array
    {
        $site = [];
        $navServices = [];
        try {
            if (Schema::hasTable('settings')) {
                $site = array_intersect_key(Setting::allSettings(), array_flip(self::SITE_KEYS));
                $navServices = Cache::rememberForever(
                    'nav.services',
                    fn () => Service::active()->get(['id', 'title', 'slug', 'icon', 'sort_order'])
                )->map(fn ($s) => ['title' => $s->title, 'slug' => $s->slug])->values()->all();
            }
        } catch (\Throwable $e) {
            // DB not ready: render with defaults.
        }

        return array_merge(parent::share($request), [
            'site' => $site + [
                'map_url' => config('site.map_url'),
                'address_fallback' => config('site.address'),
            ],
            'navServices' => $navServices,
            'locale' => app()->getLocale(),
            'year' => (int) date('Y'),
            'aiChatUrl' => route('ai.chat'),
        ]);
    }

    /** The Bangla word list for the browser (Bangla visitors only, first page load only: see inertia.blade.php). */
    public static function dictionary(): array
    {
        return Translator::map();
    }
}
