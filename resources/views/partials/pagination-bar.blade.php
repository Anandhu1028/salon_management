@if($paginator->total() > 0)
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $window = 2;
        $start = max(1, $current - $window);
        $end = min($last, $current + $window);

        if ($end - $start < $window * 2) {
            $start = max(1, $end - ($window * 2));
            $end = min($last, $start + ($window * 2));
        }
    @endphp

    <div class="pagination-wrapper">
        <p class="pagination-summary">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            to
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            results
        </p>

        <nav class="premium-pagination" aria-label="Page navigation">
            <ul class="pagination premium-pagination__list">
                {{-- Previous --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if($paginator->onFirstPage())
                        <span class="page-link premium-pagination__btn premium-pagination__btn--nav" aria-disabled="true" aria-label="Previous page">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </span>
                    @else
                        <a class="page-link premium-pagination__btn premium-pagination__btn--nav" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </a>
                    @endif
                </li>

                {{-- First page + ellipsis --}}
                @if($start > 1)
                    <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                        <a class="page-link premium-pagination__btn premium-pagination__btn--page" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled" aria-hidden="true">
                            <span class="page-link premium-pagination__btn premium-pagination__btn--ellipsis">&hellip;</span>
                        </li>
                    @endif
                @endif

                {{-- Page window --}}
                @for($page = $start; $page <= $end; $page++)
                    <li class="page-item {{ $page === $current ? 'active' : '' }}">
                        @if($page === $current)
                            <span class="page-link premium-pagination__btn premium-pagination__btn--page" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-link premium-pagination__btn premium-pagination__btn--page" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        @endif
                    </li>
                @endfor

                {{-- Last page + ellipsis --}}
                @if($end < $last)
                    @if($end < $last - 1)
                        <li class="page-item disabled" aria-hidden="true">
                            <span class="page-link premium-pagination__btn premium-pagination__btn--ellipsis">&hellip;</span>
                        </li>
                    @endif
                    <li class="page-item {{ $current === $last ? 'active' : '' }}">
                        <a class="page-link premium-pagination__btn premium-pagination__btn--page" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next --}}
                <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                    @if(!$paginator->hasMorePages())
                        <span class="page-link premium-pagination__btn premium-pagination__btn--nav" aria-disabled="true" aria-label="Next page">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </span>
                    @else
                        <a class="page-link premium-pagination__btn premium-pagination__btn--nav" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </a>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
@endif
