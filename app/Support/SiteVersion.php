<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * A number that changes whenever the admin saves or deletes website content (see AppServiceProvider). Added to the
 * URLs of the parts the browser caches (home sections, service cards), so an edit shows on the next page load
 * instead of after the 5-minute browser cache. The language is part of it, so English and Bangla never mix.
 */
class SiteVersion
{
    public static function get(): string
    {
        return Cache::rememberForever('site.version', fn () => (string) time()) . '-' . app()->getLocale();
    }

    public static function bump(): void
    {
        Cache::forever('site.version', (string) microtime(true));
    }
}
