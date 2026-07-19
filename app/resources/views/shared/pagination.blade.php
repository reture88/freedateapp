@if (isset($paginator) && $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages())
    <nav class="pagination" aria-label="Lapozas">
        @if ($paginator->onFirstPage())
            <span class="pagination__item pagination__item--disabled">Elozo</span>
        @else
            <a class="pagination__item" href="{{ $paginator->previousPageUrl() }}">Elozo</a>
        @endif

        @if ($paginator->hasMorePages())
            <a class="pagination__item" href="{{ $paginator->nextPageUrl() }}">Kovetkezo</a>
        @else
            <span class="pagination__item pagination__item--disabled">Kovetkezo</span>
        @endif
    </nav>
@endif
