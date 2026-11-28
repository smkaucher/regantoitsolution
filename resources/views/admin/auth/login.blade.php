<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin login</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body class="ad-login">
<form class="ad-card" method="POST" action="{{ route('admin.login.submit') }}">
    @csrf
    <h1>Reganto <small>Admin</small></h1>
    <p>Sign in to manage the website.</p>
    @if(session('expired') === 'signed_out')
        <div class="ad-err">You were signed out, so that save didn't go through. Sign in and what you typed will be put back in the form.</div>
    @elseif(session('expired') === 'login')
        <div class="ad-err">This page was open too long. Please sign in again.</div>
    @endif
    <label>Email
        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    </label>
    @error('email')<div class="ad-err">{{ $message }}</div>@enderror
    <label>Password
        <input type="password" name="password" required autocomplete="current-password">
    </label>
    <label class="ad-check"><input type="checkbox" name="remember" value="1"> Remember me</label>
    <button type="submit">Sign in</button>
    <a href="{{ url('/') }}">← Back to website</a>
</form>
</body>
</html>
