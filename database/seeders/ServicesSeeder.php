<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * The service catalogue shown in the menu, on /services and on every service page.
 * The content lives in database/seeders/data/services.txt:
 *   "01. Category" + a subtitle line, then "1. Service" (+ optional description line) and "- point" lines.
 * Safe to run again: categories and services are matched by slug and rebuilt.
 *   php artisan db:seed --class=ServicesSeeder
 */
class ServicesSeeder extends Seeder
{
    /** One line per category, in file order: icon and the short description shown on cards. */
    private array $meta = [
        ['gear', 'Websites, online stores and business systems built around the way you work.'],
        ['sparkle', 'Interfaces, identities and visuals that make your business look like one brand.'],
        ['chart', 'Search, ads and email that bring in leads and sales you can measure.'],
        ['shield', 'Find the weak spots in your site, code and servers before attackers do.'],
        ['users', 'Your pages run every day: planned, posted and answered by our team.'],
    ];

    public function run(): void
    {
        $categories = $this->parse(file_get_contents(__DIR__ . '/data/services.txt'));

        $slugs = [];
        foreach ($categories as $i => $cat) {
            [$icon, $short] = $this->meta[$i] ?? ['rocket', $cat['subtitle']];
            $slug = Str::slug($cat['title']);
            $slugs[] = $slug;

            $service = Service::updateOrCreate(['slug' => $slug], [
                'title' => $cat['title'],
                'icon' => $icon,
                'short_description' => $short,
                'details' => '<p>' . e($short) . '</p>',
                'sort_order' => $i + 1,
                'is_active' => true,
            ]);
            $service->items()->delete();

            foreach ($cat['items'] as $j => $item) {
                $itemSlug = Str::slug($item['title']);
                $image = 'assets/img/services/' . $itemSlug . '.webp';
                $service->items()->create([
                    'title' => $item['title'],
                    'slug' => $itemSlug,
                    'description' => $item['description'],
                    'image' => is_file(public_path($image)) ? $image : null,
                    'points' => $item['points'],
                    'sort_order' => $j + 1,
                ]);
            }
        }

        // Categories from older catalogues are removed.
        Service::whereNotIn('slug', $slugs)->get()->each->delete();
    }

    private function parse(string $text): array
    {
        $categories = [];
        $cat = null;
        $item = null;
        $expectSubtitle = false;

        foreach (preg_split('/\R/', $text) as $raw) {
            $line = trim($raw);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^0\d\.\s+(.+)$/', $line, $m)) {
                if ($item !== null) {
                    $cat['items'][] = $item;
                }
                if ($cat !== null) {
                    $categories[] = $cat;
                }
                $cat = ['title' => $m[1], 'subtitle' => '', 'items' => []];
                $item = null;
                $expectSubtitle = true;
            } elseif ($expectSubtitle) {
                $cat['subtitle'] = $line;
                $expectSubtitle = false;
            } elseif (preg_match('/^\d+\.\s+(.+)$/', $line, $m)) {
                if ($item !== null) {
                    $cat['items'][] = $item;
                }
                $item = ['title' => $m[1], 'description' => null, 'points' => []];
            } elseif ($line[0] === '-' && $item !== null) {
                $item['points'][] = trim(substr($line, 1));
            } elseif ($item !== null && $item['description'] === null) {
                $item['description'] = $line;
            }
        }
        if ($item !== null) {
            $cat['items'][] = $item;
        }
        if ($cat !== null) {
            $categories[] = $cat;
        }

        return $categories;
    }
}
