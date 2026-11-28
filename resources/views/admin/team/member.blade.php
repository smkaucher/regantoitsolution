@extends('admin.layout')
@section('title', $member->exists ? 'Edit ' . $member->name : 'Add a person')
@section('content')
@php $photo = \App\Support\Media::url($member->photo); @endphp
@if($groups->isEmpty())
    <section class="ad-panel"><p class="ad-empty">Add a team section first, then the people in it. <a href="{{ route('admin.team.groups.create') }}">Add section</a></p></section>
@else
<form class="ad-form" method="POST" enctype="multipart/form-data"
      action="{{ $member->exists ? route('admin.team.members.update', $member) : route('admin.team.members.store') }}">
    @csrf
    @if($member->exists) @method('PUT') @endif
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <h2>Person</h2>
        <div class="ad-grid">
            <label>Name *<input type="text" name="name" value="{{ old('name', $member->name) }}" maxlength="120" required>@error('name')<em>{{ $message }}</em>@enderror</label>
            <label>Section *<select name="team_group_id">
                    @foreach($groups as $g)<option value="{{ $g->id }}" @selected((int) old('team_group_id', $member->team_group_id) === $g->id)>{{ $g->name }}</option>@endforeach
                </select>@error('team_group_id')<em>{{ $message }}</em>@enderror</label>
            <label>Designation<input type="text" name="designation" value="{{ old('designation', $member->designation) }}" maxlength="150" placeholder="e.g. Full Stack Software Engineer">@error('designation')<em>{{ $message }}</em>@enderror</label>
            <label>Experience<input type="text" name="experience" value="{{ old('experience', $member->experience) }}" maxlength="60" placeholder="e.g. 5+ years">@error('experience')<em>{{ $message }}</em>@enderror</label>
        </div>
        <div class="ad-grid one">
            <label>Skills <small class="ad-sub">Separate with commas. Each one shows as a tag on the card.</small><input type="text" name="skills" value="{{ old('skills', $member->skills) }}" maxlength="400" placeholder="PHP, Laravel, React">@error('skills')<em>{{ $message }}</em>@enderror</label>
            <label>Short bio <small class="ad-sub">Shown when a visitor hovers or taps the card.</small><textarea name="bio" rows="3" maxlength="600">{{ old('bio', $member->bio) }}</textarea>@error('bio')<em>{{ $message }}</em>@enderror</label>
        </div>
        <div class="ad-file" style="margin-top:1rem">
            <span>Photo <small class="ad-sub">A square photo works best. Without one, the first letter of the name is shown.</small></span>
            @if($photo)<img src="{{ $photo }}" alt="" class="ad-thumb round">@endif
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">@error('photo')<em>{{ $message }}</em>@enderror
            @if($photo)<label class="ad-check"><input type="checkbox" name="remove_photo" value="1" data-confirm-title="Remove the photo?" data-confirm="The card will show the first letter of the name instead, after you save." data-confirm-ok="Remove"> Remove photo</label>@endif
        </div>
    </section>

    <section class="ad-panel">
        <h2>Links <small class="ad-sub">Optional. Shown as buttons on the card.</small></h2>
        <div class="ad-grid">
            @foreach(['portfolio_url' => 'Portfolio', 'linkedin_url' => 'LinkedIn', 'github_url' => 'GitHub', 'facebook_url' => 'Facebook'] as $k => $label)
                <label>{{ $label }}<input type="url" name="{{ $k }}" value="{{ old($k, $member->{$k}) }}" maxlength="255" placeholder="https://">@error($k)<em>{{ $message }}</em>@enderror</label>
            @endforeach
        </div>
        <div class="ad-grid" style="margin-top:1rem">
            <label>Display order in the section<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $member->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active))> Visible on the website</label>
        </div>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $member->exists ? 'Save' : 'Add person' }}</button>
        <a href="{{ route('admin.team.index') }}">Cancel</a>
    </div>
</form>
@endif
@endsection
