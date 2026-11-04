<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Client;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Stat;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $projects = Project::active()->where('is_featured', true)->get();
        if ($projects->isEmpty()) {
            $projects = Project::active()->get();
        }

        return view('home', [
            'services' => Service::active()->with('items')->get(),
            'projects' => $projects,
            'workCategories' => $projects->pluck('category')->filter()->unique()->values(),
            'stats' => Stat::active()->take(4)->get(),
            'clients' => Client::active()->get(),
            'testimonials' => Testimonial::active()->get(),
            'faqs' => Faq::active()->get(),
            'blogs' => Blog::active()->take(3)->get(),
        ]);
    }
}
