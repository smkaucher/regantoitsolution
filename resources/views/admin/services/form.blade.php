@extends('admin.layout')
@section('title', $service->exists ? 'Edit service' : 'Add service')
@section('content')
@php
    $items = old('items', $service->exists ? $service->items->map(fn ($i) => ['id' => $i->id, 'title' => $i->title, 'description' => $i->description, 'points' => implode("\n", $i->points ?? [])])->all() : []);
    $img = \App\Support\Media::url($service->image);
    $itemImages = $service->exists ? $service->items->pluck('image', 'id') : collect(); // also after a failed save
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
                @if($img)<label class="ad-check"><input type="checkbox" name="remove_image" value="1" data-confirm-title="Remove this image?" data-confirm="The service's image is deleted when you click Save service." data-confirm-ok="Remove"> Remove image</label>@endif
            </div>
        </div>
    </section>

    <section class="ad-panel">
        <h2>Services in this category (each one has its own page)</h2>
        <div id="itemRows">
            @foreach($items as $i => $it)
                @php $pic = \App\Support\Media::url($itemImages[$it['id'] ?? 0] ?? null); @endphp
                <div class="ad-item">
                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $it['id'] ?? '' }}">
                    <div class="ad-item-txt">
                        <input type="text" name="items[{{ $i }}][title]" value="{{ $it['title'] ?? '' }}" placeholder="Title, e.g. Mobile App UI/UX">
                        <textarea name="items[{{ $i }}][description]" rows="2" placeholder="Short description">{{ $it['description'] ?? '' }}</textarea>
                    </div>
                    <textarea name="items[{{ $i }}][points]" rows="4" placeholder="What is included, one point per line">{{ $it['points'] ?? '' }}</textarea>
                    <div class="ad-item-pic">
                        <span class="ad-item-prev{{ $pic ? '' : ' empty' }}">@if($pic)<img src="{{ $pic }}" alt="">@else No picture @endif</span>
                        <label class="ad-item-up">{{ $pic ? 'Change picture' : 'Add picture' }}<input type="file" name="items[{{ $i }}][image]" accept="image/jpeg,image/png,image/webp"></label>
                        @if($pic)<label class="ad-check"><input type="checkbox" name="items[{{ $i }}][remove_image]" value="1" data-confirm-title="Remove this picture?" data-confirm="The pop-up will use the service's image instead. The picture is deleted when you click Save service." data-confirm-ok="Remove"> Remove</label>@endif
                        @error("items.$i.image")<em>{{ $message }}</em>@enderror
                    </div>
                    <button type="button" class="ad-link-danger" data-remove data-confirm-title="Remove this item?" data-confirm="“{{ $it['title'] ?? 'This item' }}” is taken off the list. It's deleted from the website, with its picture, when you click Save service." data-confirm-ok="Remove">Remove</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="ad-btn ghost" id="addItem">+ Add item</button>
        <p class="ad-hint">Items with an empty title are ignored. They keep the order you see here. An item's picture is the cover of its pop-up on the website; without one, the pop-up uses this service's image above.</p>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $service->exists ? 'Save service' : 'Create service' }}</button>
        <a href="{{ route('admin.services.index') }}">Cancel</a>
    </div>
</form>
<template id="itemTpl">
    <div class="ad-item">
        <div class="ad-item-txt">
            <input type="text" name="items[__N__][title]" placeholder="Title, e.g. Mobile App UI/UX">
            <textarea name="items[__N__][description]" rows="2" placeholder="Short description"></textarea>
        </div>
        <textarea name="items[__N__][points]" rows="4" placeholder="What is included, one point per line"></textarea>
        <div class="ad-item-pic">
            <span class="ad-item-prev empty">No picture</span>
            <label class="ad-item-up">Add picture<input type="file" name="items[__N__][image]" accept="image/jpeg,image/png,image/webp"></label>
        </div>
        <button type="button" class="ad-link-danger" data-remove data-confirm-title="Remove this item?" data-confirm="This new item is taken off the list." data-confirm-ok="Remove">Remove</button>
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
    // Show the chosen picture straight away, before saving.
    rows.addEventListener('change', function (e) {
        if (e.target.type !== 'file' || !e.target.files[0]) return;
        var prev = e.target.closest('.ad-item-pic').querySelector('.ad-item-prev');
        prev.classList.remove('empty');
        prev.innerHTML = '';
        var img = document.createElement('img');
        img.src = URL.createObjectURL(e.target.files[0]);
        img.alt = '';
        prev.appendChild(img);
        e.target.parentNode.firstChild.textContent = 'Change picture';
    });
})();
</script>
@endsection
