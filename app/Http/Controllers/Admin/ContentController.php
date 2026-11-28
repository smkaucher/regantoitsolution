<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ContentTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Generic CRUD for the home page content blocks described in ContentTypes. */
class ContentController extends Controller
{
    public function index(string $type)
    {
        $cfg = ContentTypes::get($type);
        $q = $cfg['model']::query();
        if ($type === 'testimonials') {
            // Reviews sent from the website that nobody has checked yet come first.
            $q->orderByRaw("case when source = 'visitor' and reviewed_at is null then 0 else 1 end");
        }
        $q->orderBy('sort_order')->orderBy('id');

        return view('admin.content.index', ['type' => $type, 'cfg' => $cfg, 'items' => $q->get()]);
    }

    public function create(string $type)
    {
        $cfg = ContentTypes::get($type);
        $item = new $cfg['model']($cfg['defaults'] ?? []);
        $item->is_active = true;
        $item->sort_order = (int) $cfg['model']::max('sort_order') + 1;

        return view('admin.content.form', ['type' => $type, 'cfg' => $cfg, 'item' => $item]);
    }

    public function store(Request $request, string $type)
    {
        $cfg = ContentTypes::get($type);
        $item = $cfg['model']::create($this->attributes($request, $cfg, $type));

        return redirect()->route('admin.content.edit', [$type, $item->id])->with('ok', ucfirst($cfg['singular']) . ' added.');
    }

    public function edit(string $type, int $id)
    {
        $cfg = ContentTypes::get($type);

        return view('admin.content.form', ['type' => $type, 'cfg' => $cfg, 'item' => $cfg['model']::findOrFail($id)]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $cfg = ContentTypes::get($type);
        $item = $cfg['model']::findOrFail($id);
        $item->update($this->attributes($request, $cfg, $type, $item) + $this->reviewed($type, $item));

        return redirect()->route('admin.content.edit', [$type, $item->id])->with('ok', ucfirst($cfg['singular']) . ' saved.');
    }

    public function toggle(string $type, int $id)
    {
        $item = ContentTypes::get($type)['model']::findOrFail($id);
        $item->update(['is_active' => !$item->is_active] + $this->reviewed($type, $item));

        return back()->with('ok', $item->is_active ? 'Now visible on the website.' : 'Now hidden from the website.');
    }

    /** Showing, hiding or editing a visitor's review marks it as checked, so it leaves the "Waiting" list. */
    private function reviewed(string $type, Model $item): array
    {
        return $type === 'testimonials' && $item->reviewed_at === null ? ['reviewed_at' => now()] : [];
    }

    public function destroy(string $type, int $id)
    {
        $cfg = ContentTypes::get($type);
        $item = $cfg['model']::findOrFail($id);
        foreach ($cfg['fields'] as $name => $def) {
            if ($def[1] === 'image') {
                $this->deleteFile($item->{$name}, $type);
            }
        }
        $item->delete();

        return redirect()->route('admin.content.index', $type)->with('ok', ucfirst($cfg['singular']) . ' deleted.');
    }

    private function attributes(Request $request, array $cfg, string $type, ?Model $item = null): array
    {
        $rules = [
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
        ];
        foreach ($cfg['fields'] as $name => $def) {
            $rules[$name] = $def[2];
            if ($def[1] === 'image') {
                $rules['remove_' . $name] = 'nullable|boolean';
            }
        }
        $data = $request->validate($rules);

        $attrs = ['is_active' => $request->boolean('is_active'), 'sort_order' => $data['sort_order'] ?? 0];
        foreach ($cfg['fields'] as $name => $def) {
            if ($def[1] === 'image') {
                if ($request->hasFile($name)) {
                    $this->deleteFile($item?->{$name}, $type);
                    $attrs[$name] = \App\Support\ImageOptimizer::store($request->file($name), $type);
                } elseif ($request->boolean('remove_' . $name)) {
                    $this->deleteFile($item?->{$name}, $type);
                    $attrs[$name] = null;
                }
            } else {
                $attrs[$name] = ($data[$name] ?? null) === '' ? null : ($data[$name] ?? null);
            }
        }
        return $attrs;
    }

    /** Only files uploaded through this panel (under <type>/) are removed. */
    private function deleteFile(?string $path, string $type): void
    {
        if ($path && str_starts_with($path, $type . '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
