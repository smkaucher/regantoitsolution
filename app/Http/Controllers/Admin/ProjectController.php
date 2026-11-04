<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index()
    {
        return view('admin.projects.index', ['projects' => Project::withCount('images')->orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.projects.form', ['project' => new Project(['is_active' => true, 'sort_order' => (int) Project::max('sort_order') + 1])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $project = DB::transaction(function () use ($request, $data) {
            $project = Project::create($this->attributes($request, $data));
            $this->addGallery($project, $request);

            return $project;
        });

        return redirect()->route('admin.projects.edit', $project)->with('ok', 'Project created.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', ['project' => $project->load('images')]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request, $project);
        DB::transaction(function () use ($request, $data, $project) {
            $project->update($this->attributes($request, $data, $project));
            $this->removeGallery($project, $request->input('remove_images', []));
            $this->addGallery($project, $request);
        });

        return redirect()->route('admin.projects.edit', $project)->with('ok', 'Project saved.');
    }

    public function toggle(Request $request, Project $project)
    {
        $field = $request->input('field') === 'featured' ? 'is_featured' : 'is_active';
        $project->update([$field => !$project->{$field}]);

        return back()->with('ok', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->load('images');
        $this->deleteFile($project->cover_image);
        foreach ($project->images as $img) {
            $this->deleteFile($img->path);
        }
        $project->delete(); // gallery rows cascade

        return redirect()->route('admin.projects.index')->with('ok', 'Project deleted.');
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:150',
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('projects', 'slug')->ignore($project)],
            'category' => 'nullable|string|max:80',
            'client' => 'nullable|string|max:150',
            'url' => 'nullable|url|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:20000',
            'cover_image' => 'nullable|image|max:4096',
            'remove_cover' => 'nullable|boolean',
            'gallery' => 'nullable|array|max:20',
            'gallery.*' => 'image|max:4096',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);
    }

    private function attributes(Request $request, array $data, ?Project $project = null): array
    {
        $attrs = [
            'title' => $data['title'],
            'slug' => $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title'], $project),
            'category' => $data['category'] ?? null,
            'client' => $data['client'] ?? null,
            'url' => $data['url'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ];

        if ($request->hasFile('cover_image')) {
            $this->deleteFile($project?->cover_image);
            $attrs['cover_image'] = \App\Support\ImageOptimizer::store($request->file('cover_image'), 'projects');
        } elseif ($request->boolean('remove_cover')) {
            $this->deleteFile($project?->cover_image);
            $attrs['cover_image'] = null;
        }

        return $attrs;
    }

    private function addGallery(Project $project, Request $request): void
    {
        $order = (int) $project->images()->max('sort_order');
        foreach ($request->file('gallery', []) as $file) {
            $project->images()->create(['path' => \App\Support\ImageOptimizer::store($file, 'projects/gallery'), 'sort_order' => ++$order]);
        }
    }

    /** Remove only images that belong to this project. */
    private function removeGallery(Project $project, array $ids): void
    {
        foreach ($project->images()->whereIn('id', $ids)->get() as $img) {
            $this->deleteFile($img->path);
            $img->delete();
        }
    }

    private function uniqueSlug(string $source, ?Project $project): string
    {
        $base = Str::slug($source) ?: 'project';
        $slug = $base;
        $i = 2;
        while (Project::where('slug', $slug)->when($project, fn ($q) => $q->where('id', '!=', $project->id))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** Only files uploaded through this panel (under projects/) are removed. */
    private function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'projects/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
