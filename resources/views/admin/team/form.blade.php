@extends('admin.layout')
@section('title', $member->exists ? 'Edit team member' : 'Add team member')
@section('content')
@php $photo = \App\Support\Media::url($member->photo); @endphp
<form class="ad-form" method="POST" enctype="multipart/form-data"
      action="{{ $member->exists ? route('admin.team.update', $member) : route('admin.team.store') }}">
    @csrf
    @if($member->exists) @method('PUT') @endif
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <h2>Member</h2>
        <div class="ad-grid">
            <label>Name *<input type="text" name="name" value="{{ old('name', $member->name) }}">@error('name')<em>{{ $message }}</em>@enderror</label>
            <label>Designation *<input type="text" name="designation" value="{{ old('designation', $member->designation) }}" placeholder="Senior Developer">@error('designation')<em>{{ $message }}</em>@enderror</label>
            <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $member->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active))> Visible on the website</label>
        </div>
        <div class="ad-grid one">
            <label>Short bio<textarea name="bio" rows="3">{{ old('bio', $member->bio) }}</textarea>@error('bio')<em>{{ $message }}</em>@enderror</label>
            <div class="ad-file">
                <span>Photo</span>
                @if($photo)<img src="{{ $photo }}" alt="" class="ad-thumb round">@endif
                <input type="file" name="photo" accept="image/*">@error('photo')<em>{{ $message }}</em>@enderror
                @if($photo)<label class="ad-check"><input type="checkbox" name="remove_photo" value="1"> Remove photo</label>@endif
            </div>
        </div>
    </section>

    <section class="ad-panel">
        <h2>Links</h2>
        <div class="ad-grid">
            @foreach([['portfolio_url', 'Portfolio'], ['linkedin_url', 'LinkedIn'], ['github_url', 'GitHub'], ['facebook_url', 'Facebook']] as [$k, $label])
                <label>{{ $label }}<input type="url" name="{{ $k }}" value="{{ old($k, $member->$k) }}" placeholder="https://">@error($k)<em>{{ $message }}</em>@enderror</label>
            @endforeach
        </div>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $member->exists ? 'Save member' : 'Add member' }}</button>
        <a href="{{ route('admin.team.index') }}">Cancel</a>
    </div>
</form>
@endsection
