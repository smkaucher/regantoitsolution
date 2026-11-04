@if($paginator->hasPages())
<nav class="ad-pg" role="navigation" aria-label="Pagination">
    @if($paginator->onFirstPage())<span class="off">‹ Prev</span>@else<a href="{{ $paginator->previousPageUrl() }}">‹ Prev</a>@endif
    @foreach($elements as $element)
        @if(is_string($element))<span class="off">{{ $element }}</span>@endif
        @if(is_array($element))
            @foreach($element as $page => $url)
                @if($page == $paginator->currentPage())<span class="on">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
            @endforeach
        @endif
    @endforeach
    @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}">Next ›</a>@else<span class="off">Next ›</span>@endif
</nav>
@endif
