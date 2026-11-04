<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /** Plain text settings and how each is validated. */
    private const FIELDS = [
        'site_name' => 'required|string|max:120',
        'tagline' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:190',
        'phone' => 'nullable|string|max:60',
        'whatsapp' => 'nullable|url|max:255',
        'address' => 'nullable|string|max:255',
        'book_call_url' => 'nullable|url|max:255',
        'facebook' => 'nullable|url|max:255',
        'instagram' => 'nullable|url|max:255',
        'linkedin' => 'nullable|url|max:255',
        'youtube' => 'nullable|url|max:255',
        'tiktok' => 'nullable|url|max:255',
        'hero_subtitle' => 'nullable|string|max:400',
        'footer_text' => 'nullable|string|max:255',
    ];

    public function edit()
    {
        return view('admin.settings', ['s' => Setting::allSettings(), 'fields' => array_keys(self::FIELDS)]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(self::FIELDS + [
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'remove_logo' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
        ]);

        foreach (array_keys(self::FIELDS) as $key) {
            Setting::set($key, $data[$key] ?? '');
        }

        foreach (['logo', 'favicon'] as $key) {
            if ($request->hasFile($key)) {
                $this->deleteUploaded(Setting::get($key));
                Setting::set($key, \App\Support\ImageOptimizer::store($request->file($key), 'settings', $key === 'logo' ? 800 : 256));
            } elseif ($request->boolean('remove_' . $key)) {
                $this->deleteUploaded(Setting::get($key));
                Setting::set($key, '');
            }
        }

        return redirect()->route('admin.settings')->with('ok', 'Settings saved. The website is updated.');
    }

    /** Only files this panel uploaded (under settings/) are ever deleted. */
    private function deleteUploaded(?string $path): void
    {
        if ($path && str_starts_with($path, 'settings/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
