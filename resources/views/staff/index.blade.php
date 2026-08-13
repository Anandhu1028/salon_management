@extends('layouts.app')

@section('title', 'Staff')
@section('page-title', 'Staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/staff.css') }}">
@endpush

@section('content')

<div class="staff-page management-page">

    @include('partials.mgmt-top-actions', [
        'addLabel' => 'Add Staff',
        'addModal' => '#staffModal',
        'addOnclick' => 'openAddStaffModal()',
        'filterRoute' => route('staff.index'),
        'filter' => $filter ?? '',
        'search' => $search ?? '',
        'filterOptions' => [
            '' => 'All Staff',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
        'excelUrl' => route('staff.export.excel', request()->query()),
        'pdfUrl' => route('staff.export.pdf', request()->query()),
    ])

    {{-- Stats Row --}}
    @php
        $totalStaffCount = \App\Models\Staff::count();
        $activeStaffCount = \App\Models\Staff::where('status', 'active')->count();
        $inactiveStaffCount = \App\Models\Staff::where('status', 'inactive')->count();
        $onLeaveCount = 0;
    @endphp
    <div class="mgmt-stats-grid mgmt-stats-grid--4">
        @include('partials.mgmt-stat-card', [
            'theme' => 'indigo',
            'icon' => 'people-purple',
            'label' => 'Total Staff',
            'value' => $totalStaffCount,
            'subtext' => 'All team members',
            'sparkColor' => '#6366F1',
            'trend' => '12.5',
            'trendUp' => true,
            'sparkBars' => [42, 48, 45, 55, 52, 60, 58, 68, 72, 78],
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'green',
            'icon' => 'shield-green',
            'label' => 'Active Staff',
            'value' => $activeStaffCount,
            'subtext' => 'Currently active',
            'sparkColor' => '#22C55E',
            'trend' => '8.2',
            'trendUp' => true,
            'sparkBars' => [38, 44, 50, 48, 55, 58, 62, 65, 70, 74],
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'clock-orange',
            'label' => 'On Leave',
            'value' => $onLeaveCount,
            'subtext' => 'Staff on leave',
            'sparkColor' => '#F59E0B',
            'trend' => '2.3',
            'trendUp' => false,
            'sparkBars' => [55, 50, 48, 52, 45, 42, 46, 40, 38, 35],
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'red',
            'icon' => 'user-x-red',
            'label' => 'Inactive Staff',
            'value' => $inactiveStaffCount,
            'subtext' => 'Not active',
            'sparkColor' => '#EF4444',
            'trend' => '1.6',
            'trendUp' => false,
            'sparkBars' => [48, 46, 44, 42, 40, 38, 36, 34, 32, 30],
        ])
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation Errors Alert --}}
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
                <h2>Staff List</h2>
                <span>{{ $staff->total() }} staff members found</span>
            </div>

            <div class="content-card-header-actions">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('staff.index') }}" class="staff-search">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search staff..."
                        >
                        @if(!empty($filter))
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        @if($search)
                            <a href="{{ route('staff.index', array_filter(['filter' => $filter ?? ''])) }}" title="Clear search">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($staff->count())

            @php $listStart = ($staff->currentPage() - 1) * $staff->perPage(); @endphp

            <div class="premium-list premium-list--staff premium-list--feed premium-list--compact premium-list--mgmt">
                <div class="premium-list-head">
                    <span class="pli-head-cell col-center">#</span>
                    <span class="pli-head-cell col-left">Name</span>
                    <span class="pli-head-cell col-center">Email</span>
                    <span class="pli-head-cell col-center">Contact</span>
                    <span class="pli-head-cell col-center">Status</span>
                    <span class="pli-head-cell col-center">Actions</span>
                </div>

                @foreach($staff as $member)
                    <article class="premium-list-item" id="staff-row-{{ $member->id }}">
                        <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                        <div class="pli-col col-left">
                            <div class="pli-name-cell">
                                <div class="pli-icon pli-icon--indigo">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <span class="pli-title staff-name">{{ $member->name }}</span>
                            </div>
                        </div>

                        <div class="pli-col col-center">
                            @if($member->email)
                                <div class="pli-contact-cell">
                                    @include('partials.contact-icons', ['type' => 'mail'])
                                    <span class="pli-col-text">{{ $member->email }}</span>
                                </div>
                            @else
                                <span class="pli-col-text text-muted">—</span>
                            @endif
                        </div>

                        <div class="pli-col col-center">
                            @if($member->mobile_number)
                                <div class="pli-contact-cell">
                                    @include('partials.contact-icons', ['type' => 'phone'])
                                    <span class="pli-col-text">{{ $member->mobile_number }}</span>
                                </div>
                            @else
                                <span class="pli-col-text text-muted">—</span>
                            @endif
                        </div>

                        <div class="pli-col col-center status-cell">
                            <span
                                id="status-badge-{{ $member->id }}"
                                class="status-badge {{ $member->status === 'active' ? 'status-active' : 'status-inactive' }}"
                            >
                                <span></span>
                                <span class="status-text">{{ ucfirst($member->status) }}</span>
                            </span>
                        </div>

                        <div class="pli-col pli-col-actions col-actions actions-cell">
                            @include('partials.status-toggle', [
                                'id' => $member->id,
                                'status' => $member->status,
                                'onChange' => 'onStaffStatusToggle(' . $member->id . ', ' . json_encode($member->name) . ', this)',
                            ])
                            <button
                                type="button"
                                class="pli-btn-icon pli-btn-icon--edit"
                                title="Edit Staff"
                                data-bs-toggle="modal"
                                data-bs-target="#staffModal"
                                onclick='openEditStaffModal(@json($member))'
                            >
                                @include('partials.action-icons', ['type' => 'edit', 'size' => 16])
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @include('partials.pagination-bar', ['paginator' => $staff])

        @else

            {{-- Empty State --}}
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3>No staff members found</h3>
                <p>Start building your team list by adding your first staff member.</p>
                <button
                    type="button"
                    class="btn btn-primary mt-2"
                    data-bs-toggle="modal"
                    data-bs-target="#staffModal"
                    onclick="openAddStaffModal()"
                >
                    @include('partials.action-icons', ['type' => 'add'])
                    Add Staff
                </button>
            </div>

        @endif

    </div>

</div>


{{-- ========================================================= --}}
{{-- ADD / EDIT STAFF MODAL --}}
{{-- ========================================================= --}}

<div class="modal fade premium-modal" id="staffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="staffForm" method="POST" action="{{ route('staff.store') }}">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title" id="staffModalTitle">Add Staff</h5>
                            <p class="modal-subtitle" id="staffModalSubtitle">Add a new staff member to your salon.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body">

                    {{-- Name --}}
                    <div class="form-field">
                        <label for="staff_name" class="form-label">
                            Full Name <span>*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="staff_name"
                            class="form-control"
                            placeholder="Enter staff member's full name"
                            required
                        >
                    </div>

                    {{-- Email --}}
                    <div class="form-field">
                        <label for="staff_email" class="form-label">
                            Email Address
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="staff_email"
                            class="form-control"
                            placeholder="e.g. staff@salon.com"
                        >
                    </div>

                    {{-- Mobile Number --}}
                    <div class="form-field">
                        <label for="staff_mobile" class="form-label">
                            Mobile Number
                        </label>
                        <input
                            type="text"
                            name="mobile_number"
                            id="staff_mobile"
                            class="form-control"
                            placeholder="e.g. +91 98765 43210"
                            maxlength="20"
                        >
                    </div>

                    {{-- Status --}}
                    <div class="form-field">
                        <label for="staff_status" class="form-label">
                            Status <span>*</span>
                        </label>
                        <select name="status" id="staff_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="staffSubmitButton">Save Staff</button>
                </div>

            </form>
        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- STATUS CONFIRMATION MODAL --}}
{{-- ========================================================= --}}

<div class="modal fade premium-modal" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="confirm-modal-body">
                <div class="confirm-icon primary" id="confirmationIcon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h5 class="confirm-title" id="statusConfirmTitle">Change Status?</h5>
                <p class="confirm-message" id="statusConfirmMessage">Are you sure you want to change staff status?</p>
                <div class="confirm-actions">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
let currentStaffId = null;
let currentTargetStatus = null;

function openAddStaffModal() {
    const form = document.getElementById('staffForm');
    form.reset();
    form.action = "{{ route('staff.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('staffModalTitle').textContent = 'Add Staff';
    document.getElementById('staffModalSubtitle').textContent = 'Add a new staff member to your salon.';
    document.getElementById('staffSubmitButton').textContent = 'Save Staff';
    document.getElementById('staff_status').value = 'active';
}

function openEditStaffModal(staff) {
    const form = document.getElementById('staffForm');
    form.action = `/staff/${staff.id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('staffModalTitle').textContent = 'Edit Staff';
    document.getElementById('staffModalSubtitle').textContent = 'Update staff member information.';
    document.getElementById('staffSubmitButton').textContent = 'Update Staff';

    document.getElementById('staff_name').value = staff.name ?? '';
    document.getElementById('staff_email').value = staff.email ?? '';
    document.getElementById('staff_mobile').value = staff.mobile_number ?? '';
    document.getElementById('staff_status').value = staff.status ?? 'active';
}

function onStaffStatusToggle(staffId, staffName, input) {
    const targetStatus = input.checked ? 'active' : 'inactive';
    input.checked = !input.checked;
    confirmStatusChange(staffId, targetStatus, staffName);
}

function confirmStatusChange(staffId, targetStatus, staffName) {
    currentStaffId = staffId;
    currentTargetStatus = targetStatus;

    const isActivating = targetStatus === 'active';

    document.getElementById('statusConfirmTitle').textContent = isActivating ? 'Activate Staff?' : 'Deactivate Staff?';
    document.getElementById('statusConfirmMessage').textContent = isActivating
        ? `Are you sure you want to activate ${staffName}?`
        : `Are you sure you want to deactivate ${staffName}?`;

    const iconBox = document.getElementById('confirmationIcon');
    iconBox.className = isActivating ? 'confirm-icon success' : 'confirm-icon warning';

    const button = document.getElementById('confirmStatusButton');
    button.textContent = isActivating ? 'Activate' : 'Deactivate';
    button.className = isActivating ? 'btn btn-success' : 'btn btn-danger';

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('statusConfirmModal'));
    modal.show();
}

document.getElementById('confirmStatusButton').addEventListener('click', async function () {
    if (!currentStaffId || !currentTargetStatus) return;

    const button = this;
    button.disabled = true;

    try {
        const response = await fetch(`/staff/${currentStaffId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Unable to update staff status.');
        }

        const modalElement = document.getElementById('statusConfirmModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();

        updateStaffStatusUI(currentStaffId, data.status);
        showToast(data.message, 'success');

        currentStaffId = null;
        currentTargetStatus = null;

    } catch (error) {
        showToast(error.message, 'danger');
    } finally {
        button.disabled = false;
    }
});

function updateStaffStatusUI(staffId, status) {
    const badge = document.getElementById(`status-badge-${staffId}`);
    const toggle = document.getElementById(`status-toggle-${staffId}`);

    if (!badge || !toggle) return;

    const isActive = status === 'active';
    toggle.checked = isActive;

    const label = toggle.closest('.mgmt-status-toggle')?.querySelector('.mgmt-status-toggle__text');
    if (label) {
        label.textContent = isActive ? label.dataset.activeText : label.dataset.inactiveText;
    }

    if (isActive) {
        badge.className = 'status-badge status-active';
        badge.innerHTML = '<span></span><span class="status-text">Active</span>';
    } else {
        badge.className = 'status-badge status-inactive';
        badge.innerHTML = '<span></span><span class="status-text">Inactive</span>';
    }
}
</script>
@endpush