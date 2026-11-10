<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Real "Recent Work" projects with screenshots from public/assets/img/results.
 * Safe to run again: projects are matched by slug and their gallery is rebuilt.
 *   php artisan db:seed --class=ProjectResultsSeeder
 */
class ProjectResultsSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $name) => 'assets/img/results/' . $name . '.png';

        // Old combined poster project and removed projects.
        Project::whereIn('slug', ['storepal-product-poster-designs', 'storepal-campaign-banner', 'gadget-depo-bd-store', 'regantify-gadget-depo-bd-store', 'excellence-visa-consultancy'])->delete();

        $web = [
            [
                'slug' => 'storepal-e-commerce-store', 'title' => 'StorePal E-Commerce Store', 'category' => 'Web Development',
                'url' => 'https://storepal.com.bd/', 'client' => 'StorePal',
                'short' => 'A footwear and accessories store with category menu, search and a clean product grid.',
                'desc' => '<p>StorePal is a fast Bangladeshi online store for shoes, bags, belts and accessories, with a searchable catalogue, category navigation and a WhatsApp order button.</p>',
                'cover' => 'storepal-store', 'gallery' => [],
            ],
            [
                'slug' => 'shopnet-multi-category-store', 'title' => 'ShopNet · Multi-category Store', 'category' => 'Web Development',
                'url' => 'https://shopnet.regantify.com/', 'client' => 'ShopNet',
                'short' => 'A fast, bilingual online store with category browsing, best sellers and an AI shopping assistant.',
                'desc' => '<p>ShopNet is a full e-commerce store built on the Regantify platform. Shoppers browse by category, discover best sellers and new arrivals, and check out on desktop or mobile.</p><ul><li>Bangla / English language switch</li><li>Category circles, hero slider and product carousels</li><li>Add to cart and Buy now on every product</li><li>AI shopping assistant that answers questions about products, delivery and orders</li><li>Fully responsive mobile layout</li></ul>',
                'cover' => 'shopnet-home', 'gallery' => ['shopnet-tools', 'shopnet-mobile-home', 'shopnet-mobile-products', 'shopnet-ai-chat'],
            ],
            [
                'slug' => 'rareman-fashion-store', 'title' => 'RareMan · Fashion Store', 'category' => 'Web Development',
                'url' => null, 'client' => 'RareMan',
                'short' => 'A premium menswear storefront with glass-style cards, filters and a day / night mode.',
                'desc' => '<p>RareMan is a menswear store designed around bold photography and a deep burgundy brand colour.</p><ul><li>Day and night theme toggle</li><li>Filters by type, maximum price and discount</li><li>Bestseller, New and Limited badges with live discount percentages</li><li>Wishlist, order tracking and quick add to cart</li><li>Mobile-first layout with a WhatsApp chat button</li></ul>',
                'cover' => 'rareman-new-arrivals', 'gallery' => ['rareman-all-shirts', 'rareman-mobile'],
            ],
            [
                'slug' => 'regantify-platform-landing', 'title' => 'Regantify · Platform Landing Page', 'category' => 'Software',
                'url' => 'https://regantify.com/', 'client' => 'Regantify',
                'short' => 'A dark, animated landing page for Regantify, the all-in-one commerce platform for Bangladeshi sellers.',
                'desc' => '<p>The marketing site for Regantify: "We handle everything for growing your business." An interactive 3D card carousel walks visitors through the shop dashboard, courier booking, payments and AI product listing.</p><ul><li>Light / dark switch and Bangla / English toggle</li><li>Feature carousel with illustrated scenes</li><li>Clear Start for free call to action</li></ul>',
                'cover' => 'regantify-landing', 'gallery' => [],
            ],
        ];

        $ui = [
            ['rareman-mobile-ui', 'RareMan · Mobile Storefront', 'ui-rareman-mobile', 'A dark, glassy mobile storefront with bestseller badges, discounts and a sticky WhatsApp button.'],
            ['shopnet-mobile-home-ui', 'ShopNet · Mobile Home', 'ui-shopnet-mobile-home', 'A clean mobile home screen: hero slider, category circles and a one-tap AI helper.'],
            ['shopnet-ai-chat-ui', 'ShopNet · AI Chat Assistant', 'ui-shopnet-ai-chat', 'A friendly Bangla shopping assistant with quick-reply chips, voice and photo input.'],
            ['shopnet-mobile-categories-ui', 'ShopNet · Mobile Categories', 'ui-shopnet-mobile-categories', 'Category rows with large product cards, clear prices and Add to cart / Buy now buttons.'],
        ];

        $graphics = [
            ['every-step-breathes', 'Every Step Breathes', 'Graphics Design', 'poster-green-sneaker', 'A fresh, airy sneaker poster: a pale green shoe floating over a green field under a blue sky.'],
            ['made-to-conquer-rain', 'Made To Conquer Rain', 'Graphics Design', 'poster-rain-slides', 'A moody product shot of white slides on wet stone, with a bold two-tone headline.'],
            ['walk-with-authority', 'Walk With Authority', 'Graphics Design', 'poster-leather-shoes', 'Warm, premium lighting on a pair of tan brogues with an elegant serif headline.'],
            ['pack-your-passion', 'Pack Your Passion', 'Graphics Design', 'poster-red-backpack', 'A lifestyle backpack ad with a red-on-grey headline and a travel-ready street scene.'],
            ['carry-the-canvas', 'Carry The Canvas', 'Graphics Design', 'poster-canvas-bag', 'A hand-painted style handbag poster with a loud magenta headline on a soft garden backdrop.'],
            ['chase-frozen-horizons', 'Chase Frozen Horizons', 'Graphics Design', 'poster-frozen-horizons', 'A dramatic winter sneaker poster: a black shoe racing a horse through snow.'],
            ['majestic-designed-to-impress', 'Majestic · Designed To Impress', 'Graphics Design', 'poster-majestic', 'A fashion-magazine style cover for a premium handbag, with a large serif masthead.'],
            ['built-for-explorers', 'Built For Explorers', 'Graphics Design', 'poster-explorers-backpack', 'An outdoor backpack poster with spec details, set against warm golden-hour grass.'],
            ['hold-your-style', 'Hold Your Style', 'Graphics Design', 'poster-leather-belt', 'A leather belt poster with a script and block headline over soft shadow light.'],
            ['carry-your-signature-banner', 'Carry Your Signature · Banner', 'Branding', 'banner-handbags', 'A wide website slider banner that gives the handbag collection a premium, consistent brand look.'],
        ];

        $items = $web;
        foreach ($ui as [$slug, $title, $file, $short]) {
            $items[] = [
                'slug' => $slug, 'title' => $title, 'category' => 'UI/UX Design', 'url' => null, 'client' => null,
                'short' => $short,
                'desc' => '<p>' . $short . '</p><p>A mobile-first UI/UX design screen from our e-commerce work.</p>',
                'cover' => $file, 'gallery' => [],
            ];
        }
        foreach ($graphics as [$slug, $title, $cat, $file, $short]) {
            $items[] = [
                'slug' => $slug, 'title' => $title, 'category' => $cat, 'url' => null, 'client' => 'StorePal',
                'short' => $short,
                'desc' => '<p>' . $short . '</p><p>Made for StorePal sellers: one hero product, a strong headline and consistent branding, ready for Facebook and Instagram ads and website sliders.</p>',
                'cover' => $file, 'gallery' => [], 'featured' => false,
            ];
        }

        foreach ($items as $n => $p) {
            $project = Project::updateOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'], 'category' => $p['category'], 'client' => $p['client'], 'url' => $p['url'],
                'short_description' => $p['short'], 'description' => $p['desc'],
                'cover_image' => $img($p['cover']),
                'is_featured' => $p['featured'] ?? true, 'is_active' => true,
                'sort_order' => $n + 1,
            ]);
            $project->images()->delete();
            foreach ($p['gallery'] as $i => $name) {
                $project->images()->create(['path' => $img($name), 'sort_order' => $i + 1]);
            }
        }
    }
}
