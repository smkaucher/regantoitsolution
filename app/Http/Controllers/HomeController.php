<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    /** The home page sends only what is visible first. The sections below load as the visitor scrolls (see section()). */
    public function index()
    {
        return view('home', ['clients' => Client::active()->get()]);
    }

    /** One home page section as plain HTML, fetched by assets/js/site.js shortly before it scrolls into view. */
    public function section(string $part)
    {
        $data = match ($part) {
            'services' => ['services' => Service::active()->withCount('items')->get()],
            'work' => ['projects' => Project::active()->get()],
            'features' => [],
            'reviews' => ['testimonials' => Testimonial::active()->get()],
            'faq' => ['faqs' => Faq::active()->get()],
            default => abort(404),
        };

        return response(view('home.' . $part, $data))->header('Cache-Control', 'public, max-age=300');
    }
}
