<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\Client;
use App\Models\Faq;
use App\Models\Stat;
use App\Models\Testimonial;

/**
 * Admin "Home page content" resources. Each entry drives one generic CRUD:
 * fields => name => [label, type, rules, (hint)]. Types: text, textarea, number, url, image, datetime.
 * `cols` are the list columns; `title` is the field used as the row heading.
 * Every resource also has the standard sort_order and is_active fields (blogs have no sort_order).
 */
class ContentTypes
{
    public static function all(): array
    {
        return [
            'testimonials' => [
                'model' => Testimonial::class, 'label' => 'Testimonials', 'singular' => 'testimonial', 'title' => 'name',
                'cols' => ['role' => 'Role', 'rating' => 'Rating'],
                'fields' => [
                    'name' => ['Name', 'text', 'required|string|max:120'],
                    'role' => ['Role / company', 'text', 'nullable|string|max:150'],
                    'quote' => ['Quote', 'textarea', 'required|string|max:1500'],
                    'photo' => ['Photo', 'image', 'nullable|image|max:2048'],
                    'rating' => ['Rating (1-5)', 'number', 'required|integer|min:1|max:5'],
                ],
                'defaults' => ['rating' => 5],
            ],
            'faqs' => [
                'model' => Faq::class, 'label' => 'FAQs', 'singular' => 'FAQ', 'title' => 'question',
                'cols' => [],
                'fields' => [
                    'question' => ['Question', 'text', 'required|string|max:255'],
                    'answer' => ['Answer', 'textarea', 'required|string|max:3000'],
                ],
            ],
            'stats' => [
                'model' => Stat::class, 'label' => 'Stats', 'singular' => 'stat', 'title' => 'label',
                'cols' => ['value' => 'Value', 'suffix' => 'Suffix'],
                'fields' => [
                    'label' => ['Label', 'text', 'required|string|max:120'],
                    'value' => ['Value', 'number', 'required|numeric|min:0|max:9999999', 'Decimals allowed, e.g. 4.9'],
                    'suffix' => ['Suffix', 'text', 'nullable|string|max:10', 'e.g. + or ★'],
                ],
            ],
            'clients' => [
                'model' => Client::class, 'label' => 'Client logos', 'singular' => 'client', 'title' => 'name',
                'cols' => ['url' => 'Website'],
                'fields' => [
                    'name' => ['Client name', 'text', 'required|string|max:120'],
                    'logo' => ['Logo (optional)', 'image', 'nullable|image|max:2048'],
                    'url' => ['Website', 'url', 'nullable|url|max:255'],
                ],
            ],
            'blogs' => [
                'model' => Blog::class, 'label' => 'Blogs', 'singular' => 'article', 'title' => 'title', 'slug' => true, 'no_order' => true,
                'cols' => ['category' => 'Category', 'published_at' => 'Published'],
                'fields' => [
                    'title' => ['Title', 'text', 'required|string|max:200'],
                    'category' => ['Category', 'text', 'nullable|string|max:80'],
                    'published_at' => ['Publish date', 'datetime', 'nullable|date'],
                    'excerpt' => ['Excerpt', 'textarea', 'nullable|string|max:600'],
                    'content' => ['Content (HTML allowed)', 'textarea', 'nullable|string|max:50000'],
                    'image' => ['Cover image', 'image', 'nullable|image|max:4096'],
                ],
            ],
        ];
    }

    public static function get(string $type): array
    {
        return self::all()[$type] ?? abort(404);
    }
}
