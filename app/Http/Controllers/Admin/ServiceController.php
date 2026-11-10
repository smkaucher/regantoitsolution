<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', ['services' => Service::withCount('items')->orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.services.form', ['service' => new Service(['is_active' => true, 'sort_order' => (int) Service::max('sort_order') + 1])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $service = DB::transaction(function () use ($request, $data) {
            $service = Service::create($this->attributes($request, $data));
            $this->syncItems($service, $data['items'] ?? []);

            return $service;
        });

        return redirect()->route('admin.services.edit', $service)->with('ok', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', ['service' => $service->load('items')]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request, $service);
        DB::transaction(function () use ($request, $data, $service) {
            $service->update($this->attributes($request, $data, $service));
            $this->syncItems($service, $data['items'] ?? []);
        });

        return redirect()->route('admin.services.edit', $service)->with('ok', 'Service saved.');
    }

    public function toggle(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return back()->with('ok', $service->title . ($service->is_active ? ' is now visible.' : ' is now hidden.'));
    }

    public function destroy(Service $service)
    {
        $this->deleteImage($service->image);
        $service->delete(); // items cascade

        return redirect()->route('admin.services.index')->with('ok', 'Service deleted.');
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:150',
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('services', 'slug')->ignore($service)],
            'short_description' => 'nullable|string|max:500',
            'details' => 'nullable|string|max:20000',
            'image' => 'nullable|image|max:3072',
            'remove_image' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
            'items' => 'nullable|array|max:40',
            'items.*.title' => 'nullable|string|max:150',
            'items.*.description' => 'nullable|string|max:600',
        ]);
    }

    private function attributes(Request $request, array $data, ?Service $service = null): array
    {
        $attrs = [
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['slug'] ?? '' ?: $data['title'], $service),
            'short_description' => $data['short_description'] ?? null,
            'details' => $data['details'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($service?->image);
            $attrs['image'] = \App\Support\ImageOptimizer::store($request->file('image'), 'services');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($service?->image);
            $attrs['image'] = null;
        }

        return $attrs;
    }

    private function uniqueSlug(string $source, ?Service $service): string
    {
        $base = Str::slug($source) ?: 'service';
        $slug = $base;
        $i = 2;
        while (Service::where('slug', $slug)->when($service, fn ($q) => $q->where('id', '!=', $service->id))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function syncItems(Service $service, array $items): void
    {
        $service->items()->delete();
        $order = 0;
        foreach ($items as $item) {
            $title = trim($item['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $service->items()->create([
                'title' => $title,
                'description' => trim($item['description'] ?? '') ?: null,
                'sort_order' => $order++,
            ]);
        }
    }

    /** Only files uploaded through this panel (under services/) are removed. */
    private function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'services/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
