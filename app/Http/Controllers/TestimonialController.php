<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Support\ImageOptimizer;
use App\Support\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** "Write a review" on the home page. A review is saved hidden; an admin shows it from Admin > Testimonials. */
class TestimonialController extends Controller
{
    private const THANKS = 'Thank you! Your review will show on the site after our team checks it.';

    public function store(Request $request)
    {
        // Bots fill the hidden "website" field; real visitors never see it.
        if (filled($request->input('website'))) {
            return $this->reply($request, self::THANKS);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'quote' => ['required', 'string', 'min:10', 'max:600'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Please enter your name.',
            'rating.*' => 'Please pick a rating from 1 to 5 stars.',
            'quote.required' => 'Please write your review.',
            'quote.min' => 'Please write a little more (at least 10 letters).',
            'quote.max' => 'Please keep your review under 600 letters.',
            'photo.image' => 'The photo must be a picture (JPG, PNG or WebP).',
            'photo.max' => 'The photo must be smaller than 2 MB.',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors()->messages())->map(fn ($m) => $this->tr($m[0]));
            if ($request->expectsJson()) {
                return response()->json(['message' => $errors->first(), 'errors' => $errors], 422);
            }

            return redirect()->to(url('/') . '#reviews')->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        Testimonial::create([
            'name' => trim($data['name']),
            'role' => filled($data['role'] ?? null) ? trim($data['role']) : null,
            'rating' => (int) $data['rating'],
            'quote' => trim($data['quote']),
            'photo' => $request->hasFile('photo') ? ImageOptimizer::store($request->file('photo'), 'testimonials', 400) : null,
            'is_active' => false,
            'source' => 'visitor',
            'ip' => $request->ip(),
            'sort_order' => (int) Testimonial::max('sort_order') + 1,
        ]);

        return $this->reply($request, self::THANKS);
    }

    private function reply(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $this->tr($message)])
            : redirect()->to(url('/') . '#reviews')->with('review_sent', true);
    }

    /** JSON is not translated by the Localize middleware, so Bangla visitors get the text translated here. */
    private function tr(string $text): string
    {
        return app()->getLocale() === 'bn' ? Translator::text($text) : $text;
    }
}
