<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = [];
        foreach (['/', '/about', '/services', '/projects', '/team', '/blog', '/contact'] as $path) {
            $urls[] = [url($path), null];
        }
        foreach (Service::active()->get(['slug', 'updated_at']) as $s) {
            $urls[] = [url('/services/' . $s->slug), $s->updated_at];
        }
        foreach (Project::active()->get(['slug', 'updated_at']) as $p) {
            $urls[] = [url('/projects/' . $p->slug), $p->updated_at];
        }
        foreach (Blog::active()->get(['slug', 'updated_at']) as $b) {
            $urls[] = [url('/blog/' . $b->slug), $b->updated_at];
        }

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $body = "User-agent: *\nDisallow: /admin\nDisallow: /admin/\n\nSitemap: " . url('/sitemap.xml') . "\n";

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
