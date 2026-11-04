<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use App\Models\Stat;
use App\Models\TeamMember;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'stats' => Stat::active()->take(4)->get(),
            'team' => TeamMember::active()->take(4)->get(),
        ]);
    }

    public function services()
    {
        return view('pages.services', ['services' => Service::active()->with('items')->get()]);
    }

    public function service(string $slug)
    {
        $service = Service::active()->with('items')->where('slug', $slug)->firstOrFail();

        return view('pages.service', [
            'service' => $service,
            'others' => Service::active()->where('id', '!=', $service->id)->get(),
        ]);
    }

    public function projects()
    {
        $projects = Project::active()->get();

        return view('pages.projects', [
            'projects' => $projects,
            'categories' => $projects->pluck('category')->filter()->unique()->values(),
        ]);
    }

    public function project(string $slug)
    {
        $project = Project::active()->with('images')->where('slug', $slug)->firstOrFail();

        return view('pages.project', [
            'project' => $project,
            'related' => Project::active()->where('id', '!=', $project->id)->take(2)->get(),
        ]);
    }

    public function team()
    {
        return view('pages.team', ['team' => TeamMember::active()->get()]);
    }

    public function blogs()
    {
        return view('pages.blogs', ['blogs' => Blog::active()->get()]);
    }

    public function blog(string $slug)
    {
        $blog = Blog::active()->where('slug', $slug)->firstOrFail();

        return view('pages.blog', [
            'blog' => $blog,
            'more' => Blog::active()->where('id', '!=', $blog->id)->take(3)->get(),
        ]);
    }

    public function contact()
    {
        return view('pages.contact', ['services' => Service::active()->get()]);
    }

    public function submitRequest(Request $request)
    {
        // Honeypot: bots fill the hidden "website" field.
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('sent', true);
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:40',
            'company' => 'nullable|string|max:150',
            'service_id' => 'nullable|exists:services,id',
            'budget' => 'nullable|string|max:60',
            'message' => 'required|string|min:10|max:3000',
        ]);

        ServiceRequest::create($data);

        return redirect()->route('contact')->with('sent', true);
    }
}
