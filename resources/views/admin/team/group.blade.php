@extends('admin.layout')
@section('title', $group->exists ? 'Edit section' : 'Add team section')
@section('content')
@php $cover = \App\Support\Media::url($group->cover); @endphp
<form class="ad-form" method="POST" enctype="multipart/form-data"
      action="{{ $group->exists ? route('admin.team.groups.update', $group) : route('admin.team.groups.store') }}">
    @csrf
    @if($group->exists) @method('PUT') @endif
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <div class="ad-grid">
            <label>Section name *<input type="text" name="name" value="{{ old('name', $group->name) }}" maxlength="120" placeholder="e.g. Software Engineer" required>@error('name')<em>{{ $message }}</em>@enderror</label>
            <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $group->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
        </div>
        <div class="ad-file" style="margin-top:1rem">
            <span>Cover picture <small class="ad-sub">Shown at the top of every card in this section. A wide picture works best.</small></span>
            @if($cover)<img src="{{ $cover }}" alt="" class="ad-thumb big tmg-cover-big">@endif
            <input type="file" name="cover" accept="image/jpeg,image/png,image/webp">@error('cover')<em>{{ $message }}</em>@enderror
            @if($cover)<label class="ad-check"><input type="checkbox" name="remove_cover" value="1" data-confirm-title="Remove the cover?" data-confirm="The cards in this section will show without a cover picture after you save." data-confirm-ok="Remove"> Remove cover</label>@endif
        </div>
        <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $group->is_active))> Visible on the Team page</label>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $group->exists ? 'Save section' : 'Add section' }}</button>
        <a href="{{ route('admin.team.index') }}">Cancel</a>
    </div>
</form>
@endsection
