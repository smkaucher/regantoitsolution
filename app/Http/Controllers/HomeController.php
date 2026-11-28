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

    /**
     * /home2: a second, more animated landing page with the same content and colours. One laptop travels down the
     * page (assets/js/home2.js): it opens in the hero beside five service monitors, folds into a monitor that shows
     * the Services section's details, rides along the page and lands above the footer. The Services section is
     * rendered here (not lazily) so the monitor's place in it exists from the start.
     */
    public function home2()
    {
        $services = Service::active()->withCount('items')->with('items')->get();

        return view('home2', [
            'clients' => Client::active()->get(),
            'services' => $services,
            'heroServices' => $services->take(5),
        ]);
    }

    /** One home page section as plain HTML, fetched by assets/js/site.js shortly before it scrolls into view. */
    public function section(string $part)
    {
        $data = match ($part) {
            'services' => ['services' => Service::active()->withCount('items')->with('items')->get()],
            'work' => ['projects' => Project::active()->get()],
            'features' => [],
            'reviews' => ['testimonials' => Testimonial::active()->get()],
            'faq' => ['faqs' => Faq::active()->get()],
            default => abort(404),
        };

        return response(view('home.' . $part, $data))->header('Cache-Control', 'public, max-age=300');
    }
}
