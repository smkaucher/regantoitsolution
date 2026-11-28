@extends('admin.layout')
@section('title', 'Profile')
@section('content')
<div class="pf">
    <form class="ad-panel pf-card" method="POST" action="{{ route('admin.profile.update') }}">
        @csrf @method('PUT')
        <h2>Your details</h2>
        <p class="pf-note">The email is what you sign in with.</p>
        @php $e = $errors->details; @endphp
        <label>Name<input type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="120" autocomplete="name" required>@if($e->has('name'))<em>{{ $e->first('name') }}</em>@endif</label>
        <label>Email<input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="190" autocomplete="username" required>@if($e->has('email'))<em>{{ $e->first('email') }}</em>@endif</label>
        <label>Current password<input type="password" name="current_password" autocomplete="current-password" required>@if($e->has('current_password'))<em>{{ $e->first('current_password') }}</em>@endif</label>
        <button class="ad-btn" type="submit">Save details</button>
    </form>

    <form class="ad-panel pf-card" method="POST" action="{{ route('admin.profile.password') }}">
        @csrf @method('PUT')
        <h2>Change password</h2>
        <p class="pf-note">At least 10 characters, with letters and numbers. Other browsers and devices are signed out.</p>
        @php $p = $errors->pass; @endphp
        <label>Current password<input type="password" name="current_password" autocomplete="current-password" required>@if($p->has('current_password'))<em>{{ $p->first('current_password') }}</em>@endif</label>
        <label>New password<span class="pf-pw"><input type="password" name="password" minlength="10" autocomplete="new-password" required data-pw><button type="button" class="pf-eye" data-pw-toggle aria-label="Show password">Show</button></span>@if($p->has('password'))<em>{{ $p->first('password') }}</em>@endif</label>
        <label>New password again<input type="password" name="password_confirmation" minlength="10" autocomplete="new-password" required data-pw></label>
        <button class="ad-btn" type="submit">Change password</button>
    </form>
</div>
<script>
document.querySelectorAll('[data-pw-toggle]').forEach(function (b) {
  b.addEventListener('click', function () {
    var show = b.textContent === 'Show';
    b.closest('form').querySelectorAll('[data-pw]').forEach(function (i) { i.type = show ? 'text' : 'password'; });
    b.textContent = show ? 'Hide' : 'Show';
    b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });
});
</script>
@endsection
