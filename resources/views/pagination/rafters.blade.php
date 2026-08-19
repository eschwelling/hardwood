{{--
    Pagination markup for this app.

    Laravel's default paginator view is the Tailwind one, which wraps its
    links in <nav> elements and sizes its chevron SVGs with Tailwind
    utility classes. This app ships no compiled stylesheet — every style
    is inline in the layout — so those utility classes don't exist in the
    browser, and the site's own `nav { position: fixed; top: 0 }` rule
    was landing on the paginator's <nav> and yanking it to the top of the
    viewport on top of the header.

    This view emits plain <a>/<span> inside the .pagination container the
    layout already styles: no <nav>, no SVGs, no utility classes.
--}}
@if ($paginator->hasPages())
    <div class="pagination">
        @if ($paginator->onFirstPage())
            <span class="disabled" aria-disabled="true">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">‹</a>
        @endif

        {{-- simplePaginate() renders through this same view but passes no
             $elements, so page numbers are simply skipped in that case. --}}
        @foreach ($elements ?? [] as $element)
            @if (is_string($element))
                <span class="disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">›</a>
        @else
            <span class="disabled" aria-disabled="true">›</span>
        @endif
    </div>
@endif
