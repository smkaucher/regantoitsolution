<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Any change to website content gets a new SiteVersion, so browser-cached sections are fetched fresh.
        // Briefs, users and the like are not shown on the site, so they don't count.
        \Illuminate\Support\Facades\Event::listen(['eloquent.saved: *', 'eloquent.deleted: *'], function (string $event, array $payload) {
            $model = $payload[0] ?? null;
            if ($model && !$model instanceof \App\Models\ProjectRequest && !$model instanceof \App\Models\User) {
                \App\Support\SiteVersion::bump();
            }
        });

        // Share site settings and the service categories (top bar and footer) with the public pages.
        // Both are cached, so a page view does not hit the database for them; saving a service or a setting clears the cache.
        View::composer(['layouts.app', 'partials.*', 'pages.*', 'home.*'], function ($view) {
            static $data = null;
            if ($data === null) {
                $data = ['site' => [], 'navServices' => collect()];
                try {
                    if (Schema::hasTable('settings')) {
                        $data['site'] = Setting::allSettings();
                        $data['navServices'] = Cache::rememberForever(
                            'nav.services',
                            fn () => Service::active()->get(['id', 'title', 'slug', 'icon', 'sort_order'])
                        );
                    }
                } catch (\Throwable $e) {
                    // DB not ready: render with defaults.
                }
            }
            $view->with($data);
        });
    }
}
