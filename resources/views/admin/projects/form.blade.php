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
            <label>Category<input type="text" name="category" value="{{ old('category', $project->category) }}" placeholder="Website, Development…">@error('category')<em>{{ $message }}</em>@enderror</label>
            <label>Client<input type="text" name="client" value="{{ old('client', $project->client) }}">@error('client')<em>{{ $message }}</em>@enderror</label>
            <label>Live URL<input type="url" name="url" value="{{ old('url', $project->url) }}" placeholder="https://">@error('url')<em>{{ $message }}</em>@enderror</label>
            <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $project->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $project->is_active))> Visible on the website</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project->is_featured))> Featured on the home page</label>
        </div>
        <div class="ad-grid one">
            <label>Short description<textarea name="short_description" rows="2">{{ old('short_description', $project->short_description) }}</textarea>@error('short_description')<em>{{ $message }}</em>@enderror</label>
            <label>Full description (HTML allowed)<textarea name="description" rows="7">{{ old('description', $project->description) }}</textarea>@error('description')<em>{{ $message }}</em>@enderror</label>
        </div>
    </section>

    <section class="ad-panel">
        <h2>Photos</h2>
        <div class="ad-file">
            <span>Cover photo</span>
            @if($cover)<img src="{{ $cover }}" alt="" class="ad-thumb big">@endif
            <input type="file" name="cover_image" accept="image/*">@error('cover_image')<em>{{ $message }}</em>@enderror
            @if($cover)<label class="ad-check"><input type="checkbox" name="remove_cover" value="1"> Remove cover</label>@endif
        </div>
        <div class="ad-file" style="margin-top:1.2rem">
            <span>Gallery (select several, up to 4 MB each)</span>
            @if($project->exists && $project->images->count())
                <div class="ad-gal">
                    @foreach($project->images as $img)
                        <label class="ad-gal-item"><img src="{{ \App\Support\Media::url($img->path) }}" alt=""><span class="ad-check"><input type="checkbox" name="remove_images[]" value="{{ $img->id }}"> Remove</span></label>
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
