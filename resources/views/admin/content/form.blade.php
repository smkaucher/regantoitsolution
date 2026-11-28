@extends('admin.layout')
@section('title', ($item->exists ? 'Edit ' : 'Add ') . $cfg['singular'])
@section('content')
<form class="ad-form" method="POST" enctype="multipart/form-data"
      action="{{ $item->exists ? route('admin.content.update', [$type, $item->id]) : route('admin.content.store', $type) }}">
    @csrf
    @if($item->exists) @method('PUT') @endif
    @if($errors->any())<div class="ad-err">Please fix the highlighted fields below.</div>@endif

    <section class="ad-panel">
        <div class="ad-grid one">
            @foreach($cfg['fields'] as $name => $def)
                @php [$label, $kind, $rules] = $def; $hint = $def[3] ?? null; $req = str_contains($rules, 'required'); @endphp
                @if($kind === 'image')
                    @php $url = \App\Support\Media::url($item->{$name}); @endphp
                    <div class="ad-file">
                        <span>{{ $label }}</span>
                        @if($url)<img src="{{ $url }}" alt="" class="ad-thumb big">@endif
                        <input type="file" name="{{ $name }}" accept="image/*">@error($name)<em>{{ $message }}</em>@enderror
                        @if($url)<label class="ad-check"><input type="checkbox" name="remove_{{ $name }}" value="1" data-confirm-title="Remove this image?" data-confirm="The image is deleted when you save." data-confirm-ok="Remove"> Remove image</label>@endif
                    </div>
                @elseif($kind === 'textarea')
                    <label>{{ $label }}{{ $req ? ' *' : '' }}<textarea name="{{ $name }}" rows="4">{{ old($name, $item->{$name}) }}</textarea>@error($name)<em>{{ $message }}</em>@enderror</label>
                @else
                    <label>{{ $label }}{{ $req ? ' *' : '' }}<input type="{{ $kind }}" name="{{ $name }}" value="{{ old($name, $item->{$name}) }}" @if($kind === 'number') step="any" @endif>@if($hint)<small class="ad-sub">{{ $hint }}</small>@endif @error($name)<em>{{ $message }}</em>@enderror</label>
                @endif
            @endforeach
        </div>
        <div class="ad-grid" style="margin-top:1rem">
            <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order', $item->sort_order) }}">@error('sort_order')<em>{{ $message }}</em>@enderror</label>
            <label class="ad-check ad-pad"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))> Visible on the website</label>
        </div>
    </section>

    <div class="ad-bar">
        <button class="ad-btn" type="submit">{{ $item->exists ? 'Save' : 'Add ' . $cfg['singular'] }}</button>
        <a href="{{ route('admin.content.index', $type) }}">Cancel</a>
    </div>
</form>
@endsection
