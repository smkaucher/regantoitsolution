<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\Setting;
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
        // Share site settings and nav services with the public layout.
        View::composer(['layouts.app', 'partials.*', 'pages.*'], function ($view) {
            static $data = null;
            if ($data === null) {
                $data = ['site' => [], 'navServices' => collect()];
                try {
                    if (Schema::hasTable('settings')) {
                        $data['site'] = Setting::allSettings();
                        foreach (['logo', 'favicon'] as $k) {
                            $data['site'][$k] = \App\Support\Media::url($data['site'][$k] ?? null);
                        }
                        $data['navServices'] = Service::active()->with('items')->get();
                    }
                } catch (\Throwable $e) {
                    // DB not ready: render with defaults.
                }
            }
            $view->with($data);
        });
    }
}
