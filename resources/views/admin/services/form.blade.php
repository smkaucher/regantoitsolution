@extends('admin.layout')
@section('title', $service->exists ? 'Edit service' : 'Add service')
@section('content')
@php
    $items = old('items', $service->exists ? $service->items->map(fn ($i) => ['title' => $i->title, 'description' => $i->description])->all() : []);
    $img = \App\Support\Media::url($service->image);
@endphp
<form class="ad-form" method="POST" enctype="multipart/form-data"
      action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
    @csrf
    @if($service->exists) @method('PUT') @endif
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <h2>Details</h2>
        <div class="ad-grid">
            <label>Title *<input type="text" name="title" value="{{ old('title', $service->title) }}">@error('title')<em>{{ $message }}</em>@enderror</label>
            <label>URL slug (auto if empty)<input type="text" name="slug" value="{{ old('slug', $service->slug) }}" placeholder="ui-ux-design">@error('slug')<em>{{ $message }}</em>@enderror</label>
            <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $service->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active))> Visible on the website</label>
        </div>
        <div class="ad-grid one">
            <label>Short description<textarea name="short_description" rows="2">{{ old('short_description', $service->short_description) }}</textarea>@error('short_description')<em>{{ $message }}</em>@enderror</label>
            <label>Details (HTML allowed)<textarea name="details" rows="7">{{ old('details', $service->details) }}</textarea>@error('details')<em>{{ $message }}</em>@enderror</label>
            <div class="ad-file">
                <span>Image</span>
                @if($img)<img src="{{ $img }}" alt="" class="ad-thumb">@endif
                <input type="file" name="image" accept="image/*">@error('image')<em>{{ $message }}</em>@enderror
                @if($img)<label class="ad-check"><input type="checkbox" name="remove_image" value="1"> Remove image</label>@endif
            </div>
        </div>
    </section>

    <section class="ad-panel">
        <h2>Sub-items (shown on the home tabs and service page)</h2>
        <div id="itemRows">
            @foreach($items as $i => $it)
                <div class="ad-item">
                    <input type="text" name="items[{{ $i }}][title]" value="{{ $it['title'] ?? '' }}" placeholder="Title, e.g. Mobile App UI/UX">
                    <textarea name="items[{{ $i }}][description]" rows="2" placeholder="Short description">{{ $it['description'] ?? '' }}</textarea>
                    <button type="button" class="ad-link-danger" data-remove>Remove</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="ad-btn ghost" id="addItem">+ Add item</button>
        <p class="ad-hint">Items with an empty title are ignored. They keep the order you see here.</p>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $service->exists ? 'Save service' : 'Create service' }}</button>
        <a href="{{ route('admin.services.index') }}">Cancel</a>
    </div>
</form>
<template id="itemTpl">
    <div class="ad-item">
        <input type="text" name="items[__N__][title]" placeholder="Title, e.g. Mobile App UI/UX">
        <textarea name="items[__N__][description]" rows="2" placeholder="Short description"></textarea>
        <button type="button" class="ad-link-danger" data-remove>Remove</button>
    </div>
</template>
<script>
(function () {
    var rows = document.getElementById('itemRows');
    var n = Date.now();
    document.getElementById('addItem').addEventListener('click', function () {
        var html = document.getElementById('itemTpl').innerHTML.replace(/__N__/g, n++);
        rows.insertAdjacentHTML('beforeend', html);
        rows.lastElementChild.querySelector('input').focus();
    });
    rows.addEventListener('click', function (e) {
        if (e.target.hasAttribute('data-remove')) e.target.closest('.ad-item').remove();
    });
})();
</script>
@endsection
