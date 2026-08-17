{{--
    Top management action bar (above stat cards)
--}}

<div class="mgmt-top-actions">
    <div class="mgmt-top-actions__right">
        <a href="{{ $excelUrl }}" class="mgmt-action-btn mgmt-action-btn--excel" title="Export to Excel">
            <span class="mgmt-action-btn__icon" aria-hidden="true">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </span>
            <span class="mgmt-action-btn__label">Excel</span>
        </a>

        <a href="{{ $pdfUrl }}" class="mgmt-action-btn mgmt-action-btn--pdf" title="Export to PDF">
            <span class="mgmt-action-btn__icon" aria-hidden="true">
                <i class="bi bi-file-earmark-pdf-fill"></i>
            </span>
            <span class="mgmt-action-btn__label">PDF</span>
        </a>

        <button
            type="button"
            class="mgmt-action-btn mgmt-action-btn--primary"
            data-bs-toggle="modal"
            data-bs-target="{{ $addModal }}"
            @if(!empty($addOnclick)) onclick="{{ $addOnclick }}" @endif
        >
            <span class="mgmt-action-btn__icon" aria-hidden="true">
                @include('partials.action-icons', ['type' => 'add', 'size' => 16])
            </span>
            <span class="mgmt-action-btn__label">{{ $addLabel }}</span>
        </button>

        @if(!empty($filterModule))
            @include('partials.mgmt-filter-popover', [
                'filterModule' => $filterModule,
                'filterRoute' => $filterRoute,
                'filterData' => $filterData ?? [],
            ])
        @else
            @include('partials.mgmt-filter-btn', [
                'route' => $filterRoute,
                'filter' => $filter ?? '',
                'search' => $search ?? '',
                'options' => $filterOptions ?? [],
            ])
        @endif
    </div>
</div>
