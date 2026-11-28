<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Stat;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'stats' => Stat::active()->take(4)->get(),
            'team' => collect(\App\Models\TeamGroup::forSite()['groups'])->flatten(1)->unique('name')->take(4)->values()->all(),
        ]);
    }

    public function services()
    {
        return view('pages.services', ['services' => Service::active()->withCount('items')->get()]);
    }

    public function service(string $slug)
    {
        return $this->servicePage($slug, null);
    }

    /** The service cards of one category, fetched when its tab is opened on the Services tabs. */
    public function serviceCards(string $slug)
    {
        $service = Service::active()->with('items')->where('slug', $slug)->firstOrFail();

        return response(view('partials.service-cards', ['service' => $service]))->header('Cache-Control', 'public, max-age=300');
    }

    public function serviceItem(string $slug, string $item)
    {
        // The pop-up asks for just the card.
        if (request()->boolean('partial')) {
            $service = Service::active()->with('items')->where('slug', $slug)->firstOrFail();
            $current = $service->items->firstWhere('slug', $item);
            abort_if(!$current, 404);

            return view('partials.service-pop', ['service' => $service, 'item' => $current]);
        }

        // A service has no page of its own: the link shows its category page with the pop-up already open.
        return $this->servicePage($slug, $item);
    }

    private function servicePage(string $slug, ?string $itemSlug)
    {
        $service = Service::active()->with('items')->where('slug', $slug)->firstOrFail();
        $open = null;
        if ($itemSlug !== null) {
            $open = $service->items->firstWhere('slug', $itemSlug);
            abort_if(!$open, 404);
        }

        $categories = config('service_work.' . $service->slug, []);

        return view('pages.service', [
            'service' => $service,
            'open' => $open,
            'others' => Service::active()->where('id', '!=', $service->id)->get(),
            'work' => $categories ? Project::active()->whereIn('category', $categories)->get() : collect(),
        ]);
    }

    public function projects()
    {
        $projects = Project::active()->with('images')->get();
        $tabs = collect(['Web Development', 'UI/UX Design', 'Software', 'Graphics & Branding'])
            ->filter(fn ($t) => $projects->contains(fn ($p) => in_array($t, $p->tags(), true)))->values();

        // The pop-up is built in the browser from this data, so its text is translated here.
        $bn = app()->getLocale() === 'bn';
        $t = fn (?string $v) => $bn && $v ? \App\Support\Translator::text($v) : $v;
        $data = $projects->map(fn ($p) => [
            'slug' => $p->slug,
            'title' => $t($p->title),
            'category' => $t($p->category),
            'tags' => array_values($p->tags()),
            'client' => $p->client,
            'url' => $p->url,
            'summary' => $t($p->short_description),
            'description' => $bn && $p->description ? \App\Support\Translator::html(\App\Support\SafeHtml::clean($p->description)) : \App\Support\SafeHtml::clean($p->description),
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
        $team = \App\Models\TeamGroup::forSite();
        $groups = collect($team['groups'])->map(fn ($members, $name) => ['name' => $name, 'members' => $members, 'cover' => $team['covers'][$name] ?? null])->values();

        return view('pages.team', ['groups' => $groups]);
    }

    public function contact()
    {
        return view('pages.contact', ['serviceTitles' => \App\Models\Service::active()->pluck('title')]);
    }
}
