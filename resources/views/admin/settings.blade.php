@extends('admin.layout')
@section('title', 'Site settings')
@section('content')
@php
    $text = fn ($key, $label, $type = 'text') => compact('key', 'label', 'type');
    $img = fn ($key) => \App\Support\Media::url($s[$key] ?? null);
@endphp
<form class="ad-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <h2>Brand</h2>
        <div class="ad-grid">
            <label>Site name *<input type="text" name="site_name" value="{{ old('site_name', $s['site_name'] ?? '') }}">@error('site_name')<em>{{ $message }}</em>@enderror</label>
            <label>Tagline<input type="text" name="tagline" value="{{ old('tagline', $s['tagline'] ?? '') }}">@error('tagline')<em>{{ $message }}</em>@enderror</label>
            <div class="ad-file">
                <span>Logo</span>
                @if($img('logo'))<img src="{{ $img('logo') }}" alt="Current logo" class="ad-thumb">@endif
                <input type="file" name="logo" accept="image/*">@error('logo')<em>{{ $message }}</em>@enderror
                @if($img('logo'))<label class="ad-check"><input type="checkbox" name="remove_logo" value="1"> Remove logo</label>@endif
            </div>
            <div class="ad-file">
                <span>Favicon</span>
                @if($img('favicon'))<img src="{{ $img('favicon') }}" alt="Current favicon" class="ad-thumb sm">@endif
                <input type="file" name="favicon" accept="image/*">@error('favicon')<em>{{ $message }}</em>@enderror
                @if($img('favicon'))<label class="ad-check"><input type="checkbox" name="remove_favicon" value="1"> Remove favicon</label>@endif
            </div>
        </div>
    </section>

    <section class="ad-panel">
        <h2>Contact</h2>
        <div class="ad-grid">
            @foreach([['email', 'Email', 'email'], ['phone', 'Phone', 'text'], ['whatsapp', 'WhatsApp link', 'url'], ['address', 'Address', 'text'], ['book_call_url', 'Book-a-call link', 'url']] as [$k, $label, $type])
                <label>{{ $label }}<input type="{{ $type }}" name="{{ $k }}" value="{{ old($k, $s[$k] ?? '') }}">@error($k)<em>{{ $message }}</em>@enderror</label>
            @endforeach
        </div>
    </section>

    <section class="ad-panel">
        <h2>Social links</h2>
        <div class="ad-grid">
            @foreach([['facebook', 'Facebook'], ['instagram', 'Instagram'], ['linkedin', 'LinkedIn'], ['youtube', 'YouTube'], ['tiktok', 'TikTok']] as [$k, $label])
                <label>{{ $label }}<input type="url" name="{{ $k }}" value="{{ old($k, $s[$k] ?? '') }}" placeholder="https://">@error($k)<em>{{ $message }}</em>@enderror</label>
            @endforeach
        </div>
        <p class="ad-hint">Leave a link empty to hide its icon in the footer.</p>
    </section>

    <section class="ad-panel">
        <h2>Page texts</h2>
        <div class="ad-grid one">
            <label>Home hero text<textarea name="hero_subtitle" rows="3">{{ old('hero_subtitle', $s['hero_subtitle'] ?? '') }}</textarea>@error('hero_subtitle')<em>{{ $message }}</em>@enderror</label>
            <label>Footer text (after © year)<input type="text" name="footer_text" value="{{ old('footer_text', $s['footer_text'] ?? '') }}">@error('footer_text')<em>{{ $message }}</em>@enderror</label>
        </div>
    </section>

    <button class="ad-btn" type="submit">Save settings</button>
</form>
@endsection
