@extends('admin.layout')
@section('title', $project->exists ? 'Edit project' : 'Add project')
@section('content')
@php $cover = \App\Support\Media::url($project->cover_image); @endphp
<form class="ad-form" method="POST" enctype="multipart/form-data"
      action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}">
    @csrf
    @if($project->exists) @method('PUT') @endif
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <h2>Details</h2>
        <div class="ad-grid">
            <label>Title *<input type="text" name="title" value="{{ old('title', $project->title) }}">@error('title')<em>{{ $message }}</em>@enderror</label>
            <label>URL slug (auto if empty)<input type="text" name="slug" value="{{ old('slug', $project->slug) }}">@error('slug')<em>{{ $message }}</em>@enderror</label>
            @php
                $cat = old('category', $project->category);
                $cats = array_values(array_unique(array_filter([...\App\Models\Project::CATEGORIES, $project->category])));
            @endphp
            <label>Category<select name="category">
                    <option value="">No category (shows under All only)</option>
                    @foreach($cats as $c)<option value="{{ $c }}" @selected($cat === $c)>{{ $c }}</option>@endforeach
                </select>@error('category')<em>{{ $message }}</em>@enderror</label>
            <label>Client<input type="text" name="client" value="{{ old('client', $project->client) }}">@error('client')<em>{{ $message }}</em>@enderror</label>
            <label>Live URL<input type="url" name="url" value="{{ old('url', $project->url) }}" placeholder="https://">@error('url')<em>{{ $message }}</em>@enderror</label>
            <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $project->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $project->is_active))> Visible on the website</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project->is_featured))> Featured: shown first in "Recent work" on the home page</label>
        </div>
        <div class="ad-grid one">
            <label>Short description<textarea name="short_description" rows="2">{{ old('short_description', $project->short_description) }}</textarea>@error('short_description')<em>{{ $message }}</em>@enderror</label>
            <label>Full description <small class="ad-sub">Basic HTML works: &lt;p&gt;, &lt;b&gt;, &lt;ul&gt;/&lt;li&gt;, &lt;a href&gt;, &lt;img src&gt;. Scripts and styles are removed on the website.</small><textarea name="description" rows="7">{{ old('description', $project->description) }}</textarea>@error('description')<em>{{ $message }}</em>@enderror</label>
        </div>
    </section>

    <section class="ad-panel">
        <h2>Photos</h2>
        <div class="ad-file">
            <span>Cover photo <small class="ad-sub">Needed to show on the home page and the service pages; without one, the project is only on the Projects page.</small></span>
            @if($cover)<img src="{{ $cover }}" alt="" class="ad-thumb big">@endif
            <input type="file" name="cover_image" accept="image/*">@error('cover_image')<em>{{ $message }}</em>@enderror
            @if($cover)<label class="ad-check"><input type="checkbox" name="remove_cover" value="1" data-confirm-title="Remove the cover?" data-confirm="The cover photo is deleted when you save the project." data-confirm-ok="Remove"> Remove cover</label>@endif
        </div>
        <div class="ad-file" style="margin-top:1.2rem">
            <span>Gallery (select several, up to 4 MB each)</span>
            @if($project->exists && $project->images->count())
                <div class="ad-gal">
                    @foreach($project->images as $img)
                        <label class="ad-gal-item"><img src="{{ \App\Support\Media::url($img->path) }}" alt=""><span class="ad-check"><input type="checkbox" name="remove_images[]" value="{{ $img->id }}" data-confirm-title="Remove this photo?" data-confirm="This gallery photo is deleted when you save the project." data-confirm-ok="Remove"> Remove</span></label>
                    @endforeach
                </div>
            @endif
            <input type="file" name="gallery[]" accept="image/*" multiple>@error('gallery')<em>{{ $message }}</em>@enderror @error('gallery.*')<em>{{ $message }}</em>@enderror
        </div>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $project->exists ? 'Save project' : 'Create project' }}</button>
        <a href="{{ route('admin.projects.index') }}">Cancel</a>
    </div>
</form>
@endsection
