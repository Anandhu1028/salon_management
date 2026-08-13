@extends('layouts.app')

@section('title', 'Job Cards')
@section('page-title', 'Job Cards')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
@endpush

@section('content')

<div class="job-card-page management-page">

    @include('partials.mgmt-top-actions', [
        'addLabel' => 'Create Job Card',
        'addModal' => '#jobCardModal',
        'addOnclick' => 'openAddJobCardModal()',
        'filterRoute' => route('job-cards.index'),
        'filter' => $filter ?? '',
        'search' => $search ?? '',
        'filterOptions' => [
            '' => 'All Job Cards',
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
        'excelUrl' => route('job-cards.export.excel', request()->query()),
        'pdfUrl' => route('job-cards.export.pdf', request()->query()),
    ])

    {{-- Stats Row --}}
    @php
        $totalJobCards = \App\Models\JobCard::count();
        $pendingJobCards = \App\Models\JobCard::where('status', 'pending')->count();
        $inProgressJobCards = \App\Models\JobCard::where('status', 'in_progress')->count();
        $completedJobCards = \App\Models\JobCard::where('status', 'completed')->count();
    @endphp
    <div class="mgmt-stats-grid mgmt-stats-grid--4">
        @include('partials.mgmt-stat-card', [
            'theme' => 'cyan',
            'icon' => 'clipboard-cyan',
            'label' => 'Total Job Cards',
            'value' => $totalJobCards,
            'subtext' => 'All records',
            'sparkColor' => '#0EA5E9',
            'trend' => '14.2',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'calendar-orange',
            'label' => 'Pending',
            'value' => $pendingJobCards,
            'subtext' => 'Awaiting start',
            'sparkColor' => '#F59E0B',
            'trend' => '3.1',
            'trendUp' => false,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'blue',
            'icon' => 'people-blue',
            'label' => 'In Progress',
            'value' => $inProgressJobCards,
            'subtext' => 'Being worked on',
            'sparkColor' => '#3B82F6',
            'trend' => '6.7',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'green',
            'icon' => 'shield-green',
            'label' => 'Completed',
            'value' => $completedJobCards,
            'subtext' => 'Finished jobs',
            'sparkColor' => '#22C55E',
            'trend' => '10.4',
            'trendUp' => true,
        ])
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <div class="fw-semibold mb-1">Please fix the following errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Content Card --}}
    <div class="content-card">

        {{-- Card Header --}}
        <div class="content-card-header">
            <div>
                <h2>Job Card List</h2>
                <span>{{ $jobCards->total() }} total job cards</span>
            </div>

            <div class="content-card-header-actions">
                <form method="GET" action="{{ route('job-cards.index') }}" class="job-card-search">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search job cards..."
                        >
                        @if(!empty($filter))
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        @if($search)
                            <a href="{{ route('job-cards.index', array_filter(['filter' => $filter ?? ''])) }}" title="Clear search">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($jobCards->count())

            @php
                $listStart = ($jobCards->currentPage() - 1) * $jobCards->perPage();
                $statusLabels = [
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ];
            @endphp

            <div class="premium-list premium-list--jobs premium-list--feed premium-list--compact premium-list--mgmt">
                <div class="premium-list-head">
                    <span class="pli-head-cell pli-head-rank">#</span>
                    <span class="pli-head-cell pli-head-icon"></span>
                    <span class="pli-head-cell pli-head-name">Job Card</span>
                    <span class="pli-head-cell pli-head-customer">Customer</span>
                    <span class="pli-head-cell pli-head-service">Service</span>
                    <span class="pli-head-cell pli-head-subcategory">Sub Category</span>
                    <span class="pli-head-cell pli-head-status">Status</span>
                    <span class="pli-head-cell pli-head-joined">Created</span>
                    <span class="pli-head-cell pli-head-actions">Actions</span>
                </div>

                @foreach($jobCards as $jobCard)
                    <article class="premium-list-item" id="job-card-row-{{ $jobCard->id }}">
                        <div class="pli-rank">{{ $listStart + $loop->iteration }}</div>

                        <div class="pli-col pli-col-icon">
                            <div class="pli-icon pli-icon--cyan">
                                <i class="bi bi-clipboard2-check-fill"></i>
                            </div>
                        </div>

                        <div class="pli-col pli-col-name">
                            <div class="pli-name-stack">
                                <span class="pli-title job-card-name">{{ $jobCard->job_card_name }}</span>
                                <span class="pli-subtext">#JC-{{ str_pad($jobCard->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <div class="pli-col pli-col-customer">
                            <span class="pli-col-text">{{ $jobCard->customer->name ?? '—' }}</span>
                        </div>

                        <div class="pli-col pli-col-service">
                            <span class="pli-col-text">{{ $jobCard->service->service_name ?? '—' }}</span>
                        </div>

                        <div class="pli-col pli-col-subcategory">
                            <span class="pli-col-text">{{ $jobCard->subcategory ?: '—' }}</span>
                        </div>

                        <div class="pli-col pli-col-status-col">
                            <span class="job-status status-{{ $jobCard->status }}">
                                <span class="status-dot"></span>
                                {{ $statusLabels[$jobCard->status] ?? ucfirst($jobCard->status) }}
                            </span>
                        </div>

                        <div class="pli-col pli-col-joined">
                            <span class="pli-col-text">{{ $jobCard->created_at ? $jobCard->created_at->format('d M Y') : '—' }}</span>
                        </div>

                        <div class="pli-col pli-col-actions col-actions actions-cell">
                            <button
                                type="button"
                                class="pli-btn-icon pli-btn-icon--edit"
                                title="Edit Job Card"
                                data-bs-toggle="modal"
                                data-bs-target="#jobCardModal"
                                onclick='openEditJobCardModal(@json($jobCard))'
                            >
                                @include('partials.action-icons', ['type' => 'edit', 'size' => 16])
                            </button>
                            <button
                                type="button"
                                class="pli-btn-icon pli-btn-icon--danger"
                                title="Delete Job Card"
                                onclick="openDeleteJobCardModal({{ $jobCard->id }}, @js($jobCard->job_card_name))"
                            >
                                @include('partials.action-icons', ['type' => 'delete', 'size' => 16])
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @include('partials.pagination-bar', ['paginator' => $jobCards])

        @else

            {{-- Empty State --}}
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-clipboard2-check"></i>
                </div>
                <h3>No job cards found</h3>
                <p>Track customer service operations by creating your first job card.</p>
                <button
                    type="button"
                    class="btn btn-primary mt-2"
                    data-bs-toggle="modal"
                    data-bs-target="#jobCardModal"
                    onclick="openAddJobCardModal()"
                >
                    @include('partials.action-icons', ['type' => 'add'])
                    Create Job Card
                </button>
            </div>

        @endif

    </div>

</div>


{{-- ========================================================= --}}
{{-- ADD / EDIT JOB CARD MODAL --}}
{{-- ========================================================= --}}

<div class="modal fade premium-modal" id="jobCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="jobCardForm" method="POST" action="{{ route('job-cards.store') }}">
                @csrf
                <input type="hidden" name="_method" id="jobCardFormMethod" value="POST">

                {{-- Header --}}
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box warning">
                            <i class="bi bi-clipboard2-check"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title" id="jobCardModalTitle">Create Job Card</h5>
                            <p class="modal-subtitle" id="jobCardModalSubtitle">Create a new customer service job card.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                    {{-- Job Card Name --}}
                    <div class="form-field">
                        <label for="job_card_name" class="form-label">
                            Job Card Name <span>*</span>
                        </label>
                        <input
                            type="text"
                            name="job_card_name"
                            id="job_card_name"
                            class="form-control"
                            placeholder="e.g. Bridal Hair Service & Styling"
                            required
                        >
                    </div>

                    {{-- Customer --}}
                    <div class="form-field">
                        <label for="customer_id" class="form-label">
                            Customer <span>*</span>
                        </label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <option value="">Select customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }}
                                    @if($customer->mobile_number)
                                        — {{ $customer->mobile_number }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Service --}}
                    <div class="form-field">
                        <label for="service_id" class="form-label">
                            Service <span>*</span>
                        </label>
                        <select name="service_id" id="service_id" class="form-select" required>
                            <option value="">Select service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" data-subcategory="{{ $service->subcategory }}">
                                    {{ $service->service_name }}
                                    @if($service->category)
                                        — {{ $service->category }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Subcategory --}}
                    <div class="form-field">
                        <label for="subcategory" class="form-label">
                            Subcategory <span>*</span>
                        </label>
                        <select name="subcategory" id="subcategory" class="form-select" required disabled>
                            <option value="">Select a service first</option>
                        </select>
                        <div class="field-help">Subcategory is automatically loaded from selected service.</div>
                    </div>

                    {{-- Status --}}
                    <div class="form-field">
                        <label for="job_card_status" class="form-label">
                            Status <span>*</span>
                        </label>
                        <select name="status" id="job_card_status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="jobCardSubmitButton">Create Job Card</button>
                </div>

            </form>
        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- DELETE CONFIRMATION MODAL --}}
{{-- ========================================================= --}}

<div class="modal fade premium-modal" id="deleteJobCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="confirm-modal-body">
                <div class="confirm-icon danger">
                    @include('partials.action-icons', ['type' => 'delete'])
                </div>
                <h5 class="confirm-title">Delete Job Card?</h5>
                <p class="confirm-message" id="deleteJobCardMessage">This action cannot be undone.</p>
                <div class="confirm-actions">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteJobCardButton">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
let deleteJobCardId = null;

const serviceSelect = document.getElementById('service_id');
const subcategorySelect = document.getElementById('subcategory');

function loadSubcategory(serviceId, selectedSubcategory = null) {
    subcategorySelect.innerHTML = '';

    if (!serviceId) {
        subcategorySelect.disabled = true;
        subcategorySelect.innerHTML = '<option value="">Select a service first</option>';
        return;
    }

    const selectedOption = serviceSelect.querySelector(`option[value="${serviceId}"]`);
    if (!selectedOption) {
        subcategorySelect.disabled = true;
        return;
    }

    const subcategory = selectedOption.dataset.subcategory;
    if (!subcategory) {
        subcategorySelect.disabled = true;
        subcategorySelect.innerHTML = '<option value="">No subcategory available</option>';
        return;
    }

    subcategorySelect.disabled = false;
    subcategorySelect.innerHTML = `<option value="${escapeHtml(subcategory)}">${escapeHtml(subcategory)}</option>`;

    if (selectedSubcategory) {
        subcategorySelect.value = selectedSubcategory;
    }
}

serviceSelect.addEventListener('change', function () {
    loadSubcategory(this.value);
});

function openAddJobCardModal() {
    const form = document.getElementById('jobCardForm');
    form.reset();
    form.action = "{{ route('job-cards.store') }}";
    document.getElementById('jobCardFormMethod').value = 'POST';
    document.getElementById('jobCardModalTitle').textContent = 'Create Job Card';
    document.getElementById('jobCardModalSubtitle').textContent = 'Create a new customer service job card.';
    document.getElementById('jobCardSubmitButton').textContent = 'Create Job Card';
    document.getElementById('job_card_status').value = 'pending';
    loadSubcategory(null);
}

function openEditJobCardModal(jobCard) {
    const form = document.getElementById('jobCardForm');
    form.action = `/job-cards/${jobCard.id}`;
    document.getElementById('jobCardFormMethod').value = 'PUT';
    document.getElementById('jobCardModalTitle').textContent = 'Edit Job Card';
    document.getElementById('jobCardModalSubtitle').textContent = 'Update job card information.';
    document.getElementById('jobCardSubmitButton').textContent = 'Update Job Card';

    document.getElementById('job_card_name').value = jobCard.job_card_name ?? '';
    document.getElementById('customer_id').value = jobCard.customer_id ?? '';
    document.getElementById('service_id').value = jobCard.service_id ?? '';
    document.getElementById('job_card_status').value = jobCard.status ?? 'pending';

    loadSubcategory(jobCard.service_id, jobCard.subcategory);
}

function openDeleteJobCardModal(jobCardId, jobCardName) {
    deleteJobCardId = jobCardId;
    document.getElementById('deleteJobCardMessage').textContent = `Are you sure you want to delete ${jobCardName}?`;
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteJobCardModal'));
    modal.show();
}

document.getElementById('confirmDeleteJobCardButton').addEventListener('click', async function () {
    if (!deleteJobCardId) return;

    const button = this;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    try {
        const response = await fetch(`/job-cards/${deleteJobCardId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
            }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Unable to delete job card.');
        }

        const modalElement = document.getElementById('deleteJobCardModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();

        const row = document.getElementById(`job-card-row-${deleteJobCardId}`);
        if (row) {
            row.style.transition = 'opacity .25s ease';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                if (document.querySelector('.premium-list .premium-list-item') === null) {
                    window.location.reload();
                }
            }, 250);
        }

        showToast(data.message, 'success');

    } catch (error) {
        showToast(error.message, 'danger');
    } finally {
        deleteJobCardId = null;
        button.disabled = false;
        button.textContent = 'Delete';
    }
});
</script>
@endpush

@endsection