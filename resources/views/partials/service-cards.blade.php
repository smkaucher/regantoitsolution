{{-- The service cards of one category, added to the Services tabs when that tab is opened. Opens in the pop-up. --}}
@foreach($service->items as $i)
    <a href="{{ url('/services/' . $service->slug . '/' . $i->slug) }}" data-svc-pop class="sv2-card" data-tag="{{ $service->slug }}" hidden>
        <span class="sv2-ico"><x-icon :name="$service->icon ?: 'rocket'" size="20" /></span>
        <span class="sv2-cap">
            <small>{{ $service->title }}</small>
            <b>{{ $i->title }}</b>
        </span>
        <span class="wk2-go"><x-icon name="arrow-right" size="16" /></span>
    </a>
@endforeach
