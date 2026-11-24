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
        $data = $request->validate(self::FIELDS);

        foreach (array_keys(self::FIELDS) as $key) {
            Setting::set($key, $data[$key] ?? '');
        }

        return redirect()->route('admin.settings')->with('ok', 'Settings saved. The website is updated.');
    }
}
