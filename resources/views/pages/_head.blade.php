<section class="pg-head">
    <small class="pg-crumb"><a href="{{ url('/') }}">Home</a> / {{ $crumb ?? $title }}</small>
    <h1>{{ $title }}</h1>
    @isset($lead)<p>{{ $lead }}</p>@endisset
</section>
