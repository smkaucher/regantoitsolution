<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores an uploaded image on the public disk, shrinking big photos first.
 * JPEG/PNG/WebP wider or taller than $max px are scaled down (EXIF rotation applied for JPEG),
 * and re-encoded; small files are stored untouched. Any other type, or any GD failure, falls back
 * to a plain store() so an upload never fails because of optimisation.
 */
class ImageOptimizer
{
    public static function store(UploadedFile $file, string $dir, int $max = 1600): string
    {
        try {
            $path = self::optimise($file, $dir, $max);
            if ($path) {
                return $path;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $file->store($dir, 'public');
    }

    private static function optimise(UploadedFile $file, string $dir, int $max): ?string
    {
        $mime = $file->getMimeType();
        $loaders = ['image/jpeg' => 'imagecreatefromjpeg', 'image/png' => 'imagecreatefrompng', 'image/webp' => 'imagecreatefromwebp'];
        if (!isset($loaders[$mime]) || !function_exists($loaders[$mime]) || !($info = @getimagesize($file->getRealPath()))) {
            return null;
        }

        [$w, $h] = $info;
        $needsResize = $w > $max || $h > $max;
        if (!$needsResize && $file->getSize() <= 800 * 1024) {
            return null; // already small enough
        }
        if ($w * $h > 40_000_000) {
            return null; // too many pixels to decode safely in memory
        }

        $img = @$loaders[$mime]($file->getRealPath());
        if (!$img) {
            return null;
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
            if ($angle) {
                $img = imagerotate($img, $angle, 0);
                [$w, $h] = [imagesx($img), imagesy($img)];
            }
        }

        if ($needsResize) {
            $scale = $max / max($w, $h);
            $img = imagescale($img, max(1, (int) round($w * $scale)), max(1, (int) round($h * $scale)), IMG_BICUBIC);
        }

        if ($mime === 'image/png') {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }

        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $name = trim($dir, '/') . '/' . bin2hex(random_bytes(16)) . '.' . $ext;
        $tmp = tempnam(sys_get_temp_dir(), 'img');
        $ok = match ($mime) {
            'image/jpeg' => imagejpeg($img, $tmp, 82),
            'image/png' => imagepng($img, $tmp, 7),
            'image/webp' => imagewebp($img, $tmp, 82),
        };
        imagedestroy($img);

        if (!$ok) {
            @unlink($tmp);

            return null;
        }
        Storage::disk('public')->put($name, file_get_contents($tmp));
        @unlink($tmp);

        return $name;
    }
}
