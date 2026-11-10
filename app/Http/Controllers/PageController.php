<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;
use App\Models\Stat;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'stats' => Stat::active()->take(4)->get(),
            'team' => collect(config('team.groups'))->flatten(1)->unique('name')->take(4)->values()->all(),
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
        $projects = Project::active()->with('images')->get();
        $tabs = collect(['Web Development', 'UI/UX Design', 'Software', 'Graphics & Branding'])
            ->filter(fn ($t) => $projects->contains(fn ($p) => in_array($t, $p->tags(), true)))->values();

        $data = $projects->map(fn ($p) => [
            'slug' => $p->slug,
            'title' => $p->title,
            'category' => $p->category,
            'tags' => array_values($p->tags()),
            'client' => $p->client,
            'url' => $p->url,
            'summary' => $p->short_description,
            'description' => $p->description,
            'images' => collect([$p->cover_image])->merge($p->images->pluck('path'))->filter()
                ->map(fn ($i) => \App\Support\Media::url($i))->unique()->values(),
        ])->values();

        return view('pages.projects', ['projects' => $projects, 'tabs' => $tabs, 'data' => $data]);
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
        $groups = collect(config('team.groups'))->map(fn ($members, $name) => ['name' => $name, 'members' => $members, 'cover' => config('team.covers.'.$name)])->values();

        return view('pages.team', ['groups' => $groups]);
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
        return view('pages.contact');
    }
}
