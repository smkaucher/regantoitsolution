<?php

namespace App\Support;

class Media
{
    /** Resolve a stored image value: full URL, a public/ asset path, or an uploaded storage path. */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }
        return asset('storage/' . ltrim($path, '/'));
    }
}
