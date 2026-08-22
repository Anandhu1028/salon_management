@extends('layouts.app')

@section('title', 'Complaints')
@section('page-title', 'Complaints')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/module-lists.css') }}">
    <style>
        /* ============================================================
           COMPLAINT LIST TABLE — 9-column grid layout
           ============================================================ */
        .complaint-page .premium-list--complaints {
            --complaint-grid:
                44px
                minmax(180px, 1.3fr)
                minmax(140px, 1fr)
                minmax(150px, 1.1fr)
                minmax(180px, 1.4fr)
                minmax(160px, 1.2fr)
                110px
                110px
                70px;
        }

        .complaint-page .premium-list--complaints .premium-list-head,
        .complaint-page .premium-list--complaints .premium-list-item {
            grid-template-columns: var(--complaint-grid) !important;
            min-width: 1180px !important;
        }

        .complaint-page .premium-list--complaints .premium-list-head {
            gap: 12px !important;
        }

        .complaint-page .premium-list--complaints .premium-list-item {
            gap: 12px !important;
            min-height: 68px;
        }

        .complaint-page .complaint-cell-center {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }

        .complaint-page .complaint-jobcard-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .complaint-page .complaint-code {
            font-size: .6875rem;
            font-weight: 600;
            color: #6366F1;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .complaint-page .complaint-customer-sub {
            font-size: .75rem;
            font-weight: 600;
            color: #94A3B8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .complaint-page .complaint-text-box {
            display: block;
            max-width: 100%;
            font-size: .82rem;
            font-weight: 500;
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .complaint-page .complaint-action-box {
            display: block;
            max-width: 100%;
            font-size: .82rem;
            font-weight: 500;
            color: #16A34A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .complaint-page .complaint-compensation-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
        }

        .complaint-page .complaint-comp-amount {
            font-size: .84rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
        }

        .complaint-page .complaint-comp-badge {
            font-size: .62rem;
            font-weight: 700;
            color: #16A34A;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .complaint-page .complaint-date-text {
            font-size: .82rem;
            font-weight: 600;
            color: #64748B;
            white-space: nowrap;
        }

        .complaint-page .complaint-service-chip {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            max-width: 100%;
        }

        .complaint-page .complaint-service-name {
            font-size: .82rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .complaint-page .complaint-category-tag {
            font-size: .68rem;
            font-weight: 600;
            color: #6366F1;
            background: #EEF2FF;
            padding: 1px 8px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .complaint-page .complaint-staff-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .complaint-page .complaint-staff-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            color: #FFFFFF;
            font-size: .72rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .complaint-page .complaint-staff-name {
            font-size: .82rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .complaint-page .complaint-list-actions,
        .complaint-page .premium-list--complaints .complaint-list-actions,
        .complaint-page .premium-list--complaints .pli-col-actions {
            grid-column: 9 !important;
            grid-row: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: visible !important;
        }

        .complaint-page .premium-list--complaints .premium-list-head .pli-head-cell:last-child {
            grid-column: 9 !important;
            grid-row: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Ensure 3-dot action popover opens downward and stays on top */
        .complaint-page .content-card,
        .complaint-page .premium-list,
        .complaint-page .premium-list--complaints {
            overflow: visible !important;
        }

        .complaint-page .premium-list-item {
            position: relative;
        }

        .complaint-page .premium-list-item.action-menu-row-open {
            z-index: 100 !important;
        }

        .complaint-page .pli-action-menu-wrap {
            position: relative;
        }

        .complaint-page .pli-action-menu-wrap.is-open {
            z-index: 105 !important;
        }

        .complaint-page .pli-action-popover,
        .complaint-page .premium-list-item:last-child .pli-action-popover,
        .complaint-page .premium-list-item.pli-open-upward .pli-action-popover,
        .complaint-page .premium-list-item:last-child .pli-action-menu-wrap.is-open .pli-action-popover,
        .complaint-page .premium-list-item.pli-open-upward .pli-action-menu-wrap.is-open .pli-action-popover {
            top: calc(100% + 6px) !important;
            bottom: auto !important;
            right: 0 !important;
            left: auto !important;
            z-index: 120 !important;
            transform-origin: top right !important;
            transform: translateY(0) scale(1) !important;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.15) !important;
        }

        /* Detail Modal Styles */
        .complaint-detail-quote {
            background: #F8FAFC;
            border-left: 3.5px solid #6366F1;
            border-radius: 0 10px 10px 0;
            padding: 12px 16px;
            margin-top: 14px;
        }

        .complaint-detail-quote-label {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 4px;
        }

        .complaint-detail-quote-text {
            font-size: .88rem;
            font-weight: 500;
            color: #1E293B;
            line-height: 1.5;
            white-space: pre-wrap;
        }

        .complaint-detail-quote--success {
            background: #F0FDF4;
            border-left-color: #22C55E;
        }

        .complaint-detail-quote--success .complaint-detail-quote-label {
            color: #15803D;
        }

        .complaint-detail-quote--success .complaint-detail-quote-text {
            color: #14532D;
        }
    </style>
@endpush

@section('content')
<div class="job-card-page complaint-page management-page">
    @include('partials.mgmt-top-actions', [
        'addLabel' => 'Add Complaint',
        'addModal' => '#complaintModal',
        'addOnclick' => 'openComplaint()',
        'filterModule' => 'complaints',
        'filterRoute' => route('complaints.index'),
        'filterData' => ['staff' => $staff, 'services' => $services],
    ])

    {{-- Statistics Cards --}}
    <div class="mgmt-stats-grid mgmt-stats-grid--4">
        @include('partials.mgmt-stat-card', [
            'theme' => 'indigo',
            'icon' => 'people-purple',
            'label' => 'Total Complaints',
            'value' => $totalComplaintsCount,
            'subtext' => 'All complaint records',
            'sparkColor' => '#6366F1',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'calendar-orange',
            'label' => 'Pending',
            'value' => $pendingComplaintsCount,
            'subtext' => 'Need attention',
            'sparkColor' => '#F59E0B',
            'trend' => '0.0',
            'trendUp' => false,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'green',
            'icon' => 'check-green',
            'label' => 'Resolved',
            'value' => $resolvedComplaintsCount,
            'subtext' => 'Successfully handled',
            'sparkColor' => '#22C55E',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'rupee-green',
            'label' => 'Total Compensation',
            'value' => '₹' . number_format($totalCompensationSum, 2),
            'subtext' => 'All compensation paid',
            'sparkColor' => '#F59E0B',
            'trend' => '0.0',
            'trendUp' => true,
        ])
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h2>Complaint List</h2>
                <span>{{ $complaints->total() }} total complaints</span>
            </div>
            <div class="content-card-header-actions">
                <form method="GET" action="{{ route('complaints.index') }}" class="job-card-search">
                    <input type="hidden" name="date_from" value="{{ $dateFrom }}"><input type="hidden" name="date_to" value="{{ $dateTo }}"><input type="hidden" name="status" value="{{ $statusFilter }}"><input type="hidden" name="staff_id" value="{{ $staffId }}"><input type="hidden" name="service_id" value="{{ $serviceId }}">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search complaints...">
                        @if($search)
                            <a href="{{ route('complaints.index') }}" class="text-muted" title="Clear search"><i class="bi bi-x"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($complaints->count())
            @php
                $complaintListStart = ($complaints->currentPage() - 1) * $complaints->perPage();
            @endphp

            <div class="premium-list premium-list--complaints premium-list--feed premium-list--compact premium-list--mgmt">
                <div class="premium-list-head">
                    <span class="pli-head-cell col-center">#</span>
                    <span class="pli-head-cell col-left">Job Card</span>
                    <span class="pli-head-cell col-left">Staff</span>
                    <span class="pli-head-cell col-center">Service</span>
                    <span class="pli-head-cell col-center">Reason</span>
                    <span class="pli-head-cell col-center">Action Taken</span>
                    <span class="pli-head-cell col-center">Compensation</span>
                    <span class="pli-head-cell col-center">Date</span>
                    <span class="pli-head-cell col-center">Actions</span>
                </div>

                @foreach($complaints as $complaint)
                    @php
                        $jobCard = $complaint->jobCard;
                        $jobCardNum = $jobCard?->job_card_number ?? ($jobCard ? 'JC-' . str_pad($jobCard->id, 3, '0', STR_PAD_LEFT) : '—');
                        $jobCardName = $jobCard?->job_card_name ?? ($jobCard ? 'Job Card #' . $jobCard->id : 'Direct Complaint');
                        $customerName = $jobCard?->customer?->name ?? '—';
                        $staffName = $complaint->staff?->name ?? '—';
                        $staffInitial = $staffName !== '—' ? strtoupper(substr($staffName, 0, 1)) : '?';
                        $serviceName = $complaint->service?->service_name ?? '—';
                        $categoryName = $complaint->category ?: ($complaint->service?->category ?? '');
                        $compAmount = (float) ($complaint->compensation ?? 0);
                    @endphp

                    <article class="premium-list-item" id="complaint-row-{{ $complaint->id }}">
                        <div class="pli-rank col-center">{{ $complaintListStart + $loop->iteration }}</div>

                        {{-- Job Card cell --}}
                        <div class="pli-col col-left">
                            <div class="pli-name-cell">
                                <div class="pli-icon pli-icon--indigo"><i class="bi bi-clipboard2-x-fill"></i></div>
                                <div class="pli-name-stack">
                                    <span class="pli-title" title="{{ $jobCardName }}">{{ $jobCardName }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="complaint-code">{{ $jobCardNum }}</span>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Staff cell --}}
                        <div class="pli-col col-left">
                            <div class="complaint-staff-cell">
                                <span class="complaint-staff-name" title="{{ $staffName }}">{{ $staffName }}</span>
                            </div>
                        </div>

                        {{-- Service cell --}}
                        <div class="pli-col complaint-cell-center">
                            <div class="complaint-service-chip">
                                <span class="complaint-service-name" title="{{ $serviceName }}">{{ $serviceName }}</span>
                                @if($categoryName)
                                    <span class="complaint-category-tag">{{ $categoryName }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Reason cell --}}
                        <div class="pli-col complaint-cell-center">
                            <span class="complaint-text-box" title="{{ $complaint->reason }}">
                                {{ $complaint->reason ?: '—' }}
                            </span>
                        </div>

                        {{-- Action taken cell --}}
                        <div class="pli-col complaint-cell-center">
                            @if($complaint->action_taken)
                                <span class="complaint-action-box" title="{{ $complaint->action_taken }}">
                                    <i class="bi bi-check-circle me-1"></i>{{ $complaint->action_taken }}
                                </span>
                            @else
                                <span class="badge rounded-pill bg-light text-muted border" style="font-size:.7rem;font-weight:600;">Pending</span>
                            @endif
                        </div>

                        {{-- Compensation cell --}}
                        <div class="pli-col complaint-cell-center">
                            <div class="complaint-compensation-pill">
                                <span class="complaint-comp-amount">₹{{ number_format($compAmount, 2) }}</span>
                                @if($compAmount > 0)
                                    <span class="complaint-comp-badge">Paid</span>
                                @endif
                            </div>
                        </div>

                        {{-- Date cell --}}
                        <div class="pli-col complaint-cell-center">
                            <span class="complaint-date-text">{{ optional($complaint->complaint_date)->format('d/m/Y') }}</span>
                        </div>

                        {{-- Actions menu --}}
                        <div class="pli-col pli-col-actions complaint-list-actions">
                            <div class="pli-action-menu-wrap">
                                <button type="button" class="pli-action-dots" aria-label="Complaint actions" aria-expanded="false" onclick="toggleComplaintActions(this)">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="pli-action-popover">
                                    <button type="button" class="pli-popover-action" onclick='openComplaintDetailsModal(@json($complaint)); closeComplaintActions(this)'>
                                        <span class="pli-popover-icon pli-popover-icon--view"><i class="bi bi-eye"></i></span>
                                        <span>View Details</span>
                                    </button>
                                    <button type="button" class="pli-popover-action" onclick='openComplaint(@json($complaint)); closeComplaintActions(this)'>
                                        <span class="pli-popover-icon pli-popover-icon--edit"><i class="bi bi-pencil"></i></span>
                                        <span>Edit Complaint</span>
                                    </button>
                                    <div class="pli-popover-divider"></div>
                                    <form method="POST" action="{{ route('complaints.destroy', $complaint) }}" onsubmit="return confirm('Delete this complaint record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="pli-popover-action pli-popover-action--danger">
                                            <span class="pli-popover-icon pli-popover-icon--delete"><i class="bi bi-trash3"></i></span>
                                            <span>Delete Complaint</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @include('partials.pagination-bar', ['paginator' => $complaints])
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-exclamation-octagon"></i></div>
                <h3>No complaints found</h3>
                <p>{{ $search ? 'No complaints match your search query.' : 'Record and manage client service complaints.' }}</p>
                <button class="btn btn-primary mt-2" onclick="openComplaint()">
                    <i class="bi bi-plus-lg"></i> Add Complaint
                </button>
            </div>
        @endif
    </div>
</div>

{{-- ========================================================= --}}
{{-- ADD / EDIT COMPLAINT MODAL --}}
{{-- ========================================================= --}}
<div class="modal fade premium-modal job-card-builder-modal" id="complaintModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 780px;">
        <div class="modal-content">
            <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}" class="job-card-builder-form">
                @csrf
                <input type="hidden" id="complaintMethod" name="_method">

                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box job-card-modal-icon" style="background:linear-gradient(135deg,#FEE2E2,#FECACA);color:#DC2626;">
                            <i class="bi bi-exclamation-octagon"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title" id="complaintTitle">Add Complaint</h5>
                            <p class="modal-subtitle" id="complaintSubtitle">Record a customer feedback or service complaint.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- 1st: Job Card Selection --}}
                        <div class="col-12">
                            <label for="complaintJobCard" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Select Job Card <span class="text-danger">*</span>
                            </label>
                            <div class="field-control-wrap position-relative">
                                <select id="complaintJobCard" name="job_card_id" class="form-select no-nice-select" required onchange="onJobCardSelected(this.value)">
                                    <option value="">Select Job Card</option>
                                    @foreach($jobCards as $jc)
                                        @php
                                            $cName = $jc->customer?->name ?? ($jc->customers->first()?->name ?? 'Walk-in');
                                            $jcNum = $jc->job_card_number ?: ('JC-' . str_pad($jc->id, 3, '0', STR_PAD_LEFT));
                                        @endphp
                                        <option value="{{ $jc->id }}">
                                            {{ $jcNum }} — {{ $jc->job_card_name }} ({{ $cName }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Service Selection (Populated dynamically based on Job Card) --}}
                        <div class="col-md-6">
                            <label for="complaintService" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Service
                            </label>
                            <select id="complaintService" name="service_id" class="form-select no-nice-select" onchange="onServiceSelected(this.value)">
                                <option value="">Select service from Job Card</option>
                            </select>
                        </div>

                        {{-- Staff Selection (Populated dynamically based on Job Card & Service) --}}
                        <div class="col-md-6">
                            <label for="complaintStaff" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Staff <span class="text-danger">*</span>
                            </label>
                            <select id="complaintStaff" name="staff_id" class="form-select no-nice-select" required>
                                <option value="">Select staff</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Category (Auto-filled / Read-only) --}}
                        <div class="col-md-6">
                            <label for="complaintCategory" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Category
                            </label>
                            <input type="text" id="complaintCategory" name="category" class="form-control" readonly placeholder="Auto-filled from service" style="background:#F8FAFC;color:#64748B;">
                        </div>

                        {{-- Subcategory (Auto-filled / Read-only) --}}
                        <div class="col-md-6">
                            <label for="complaintSubcategory" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Subcategory
                            </label>
                            <input type="text" id="complaintSubcategory" name="subcategory" class="form-control" readonly placeholder="Auto-filled from service" style="background:#F8FAFC;color:#64748B;">
                        </div>

                        {{-- Reason of Complaint (Mandatory) --}}
                        <div class="col-12">
                            <label for="complaintReason" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Reason of Complaint <span class="text-danger">*</span>
                            </label>
                            <textarea id="complaintReason" name="reason" class="form-control" rows="3" placeholder="Enter the detailed reason of the complaint..." required></textarea>
                        </div>

                        {{-- Action Taken (Optional / Not Mandatory) --}}
                        <div class="col-12">
                            <label for="complaintAction" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Action Taken <small class="text-muted fw-normal">(optional)</small>
                            </label>
                            <textarea id="complaintAction" name="action_taken" class="form-control" rows="2" placeholder="Describe any corrective action or resolution taken..."></textarea>
                        </div>

                        {{-- Compensation (Optional / Not Mandatory) --}}
                        <div class="col-md-6">
                            <label for="complaintCompensation" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Compensation (₹) <small class="text-muted fw-normal">(optional)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">₹</span>
                                <input id="complaintCompensation" name="compensation" type="number" step="0.01" min="0" class="form-control border-start-0" placeholder="0.00">
                            </div>
                        </div>

                        {{-- Complaint Date (Mandatory) --}}
                        <div class="col-md-6">
                            <label for="complaintDate" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input id="complaintDate" name="complaint_date" type="date" class="form-control" required value="{{ now()->toDateString() }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="complaintSubmit">
                        <i class="bi bi-check2-circle"></i> Save Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- COMPLAINT DETAILS MODAL — matching Job Card shell --}}
{{-- ========================================================= --}}
<div class="modal fade premium-modal job-card-details-modal" id="complaintDetailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 680px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-box job-card-details-title-icon" style="background:linear-gradient(135deg,#FEE2E2,#FECACA);color:#DC2626;">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="modal-header-content">
                        <h5 class="modal-title">Complaint Details</h5>
                        <p class="modal-subtitle">Full complaint report and resolution breakdown</p>
                    </div>
                </div>
                <div class="job-card-details-header-actions">
                    <button type="button" class="job-card-detail-tool" onclick="window.print()" title="Print" aria-label="Print"><i class="bi bi-printer"></i></button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="jcd-details-grid" aria-label="Complaint summary">
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Job Card</span>
                            <strong class="jcd-detail-value" id="detailsJobCardName">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Staff Member</span>
                            <strong class="jcd-detail-value" id="detailsStaffName">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Service & Category</span>
                            <strong class="jcd-detail-value" id="detailsServiceName">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Complaint Date</span>
                            <strong class="jcd-detail-value" id="detailsDate">—</strong>
                        </div>
                    </div>
                </div>

                {{-- Reason Section --}}
                <div class="complaint-detail-quote">
                    <div class="complaint-detail-quote-label"><i class="bi bi-chat-square-text me-1"></i>Reason of Complaint</div>
                    <div class="complaint-detail-quote-text" id="detailsReason">—</div>
                </div>

                {{-- Action Taken Section --}}
                <div class="complaint-detail-quote complaint-detail-quote--success">
                    <div class="complaint-detail-quote-label"><i class="bi bi-check2-circle me-1"></i>Action Taken</div>
                    <div class="complaint-detail-quote-text" id="detailsAction">—</div>
                </div>

                {{-- Totals / Compensation Card --}}
                <div class="jcd-totals-card mt-4">
                    <div class="jcd-totals-row jcd-totals-row--final">
                        <span class="jcd-totals-label">Compensation Paid</span>
                        <strong id="detailsCompensation">₹ 0.00</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer job-card-details-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@php
    $jobCardsDataForJs = $jobCards->map(function ($jc) {
        $cust = $jc->customer?->name ?? ($jc->customers->first()?->name ?? 'Walk-in');
        $serviceList = [];
        if ($jc->serviceItems && $jc->serviceItems->count()) {
            foreach ($jc->serviceItems as $item) {
                $staffList = $item->staff ? $item->staff->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray() : [];
                $serviceList[] = [
                    'service_id' => $item->service_id,
                    'service_name' => $item->service?->service_name ?? '—',
                    'category' => $item->service?->category ?? '—',
                    'subcategory' => $item->subcategory ?: ($item->service?->subcategory ?? '—'),
                    'staff' => $staffList,
                ];
            }
        } elseif ($jc->service) {
            $staffList = $jc->staff ? $jc->staff->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray() : [];
            $serviceList[] = [
                'service_id' => $jc->service_id,
                'service_name' => $jc->service->service_name,
                'category' => $jc->service->category ?? '—',
                'subcategory' => $jc->subcategory ?: ($jc->service->subcategory ?? '—'),
                'staff' => $staffList,
            ];
        }

        $allStaff = [];
        foreach ($serviceList as $s) {
            foreach ($s['staff'] as $st) {
                $allStaff[$st['id']] = $st;
            }
        }
        if (empty($allStaff) && $jc->staff) {
            foreach ($jc->staff as $st) {
                $allStaff[$st->id] = ['id' => $st->id, 'name' => $st->name];
            }
        }

        return [
            'id' => $jc->id,
            'job_card_number' => $jc->job_card_number ?: ('JC-' . str_pad($jc->id, 3, '0', STR_PAD_LEFT)),
            'job_card_name' => $jc->job_card_name,
            'customer_name' => $cust,
            'services' => $serviceList,
            'staff' => array_values($allStaff),
        ];
    });

    $allActiveStaffForJs = $staff->map(fn($s) => ['id' => $s->id, 'name' => $s->name]);
@endphp

@push('scripts')
<script>
    const complaintModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('complaintModal'));
    const complaintDetailsModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('complaintDetailsModal'));
    const jobCardsData = @json($jobCardsDataForJs);
    const allActiveStaff = @json($allActiveStaffForJs);

    function onJobCardSelected(jobCardId, selectedServiceId = null, selectedStaffId = null) {
        const serviceSelect = document.getElementById('complaintService');
        const staffSelect = document.getElementById('complaintStaff');
        const categoryInput = document.getElementById('complaintCategory');
        const subcategoryInput = document.getElementById('complaintSubcategory');

        serviceSelect.innerHTML = '<option value="">Select service from Job Card</option>';
        categoryInput.value = '';
        subcategoryInput.value = '';

        if (!jobCardId) {
            populateStaffDropdown(allActiveStaff, selectedStaffId);
            return;
        }

        const jobCard = jobCardsData.find(jc => String(jc.id) === String(jobCardId));
        if (!jobCard) {
            populateStaffDropdown(allActiveStaff, selectedStaffId);
            return;
        }

        // Populate Services
        if (jobCard.services && jobCard.services.length > 0) {
            jobCard.services.forEach(srv => {
                const opt = document.createElement('option');
                opt.value = srv.service_id;
                opt.textContent = `${srv.service_name} (${srv.category})`;
                opt.dataset.category = srv.category || '';
                opt.dataset.subcategory = srv.subcategory || '';
                serviceSelect.appendChild(opt);
            });

            // If only 1 service or preselected, auto-select it
            if (selectedServiceId) {
                serviceSelect.value = selectedServiceId;
            } else if (jobCard.services.length === 1) {
                serviceSelect.value = jobCard.services[0].service_id;
            }

            // Update category/subcategory for selected service
            onServiceSelected(serviceSelect.value, jobCard);
        } else {
            categoryInput.value = '—';
            subcategoryInput.value = '—';
        }

        // Populate Staff assigned to this Job Card (or fallback to all staff)
        const staffList = (jobCard.staff && jobCard.staff.length > 0) ? jobCard.staff : allActiveStaff;
        populateStaffDropdown(staffList, selectedStaffId);
    }

    function onServiceSelected(serviceId, preloadedJobCard = null) {
        const jobCardId = document.getElementById('complaintJobCard').value;
        const jobCard = preloadedJobCard || jobCardsData.find(jc => String(jc.id) === String(jobCardId));
        const categoryInput = document.getElementById('complaintCategory');
        const subcategoryInput = document.getElementById('complaintSubcategory');

        if (!serviceId || !jobCard) {
            categoryInput.value = '';
            subcategoryInput.value = '';
            return;
        }

        const foundService = jobCard.services.find(s => String(s.service_id) === String(serviceId));
        if (foundService) {
            categoryInput.value = foundService.category || '—';
            subcategoryInput.value = foundService.subcategory || '—';

            // If service has specific assigned staff, refine staff dropdown
            if (foundService.staff && foundService.staff.length > 0) {
                const currentStaffId = document.getElementById('complaintStaff').value;
                populateStaffDropdown(foundService.staff, currentStaffId);
            }
        }
    }

    function populateStaffDropdown(staffList, selectedId = null) {
        const staffSelect = document.getElementById('complaintStaff');
        staffSelect.innerHTML = '<option value="">Select staff</option>';

        staffList.forEach(st => {
            const opt = document.createElement('option');
            opt.value = st.id;
            opt.textContent = st.name;
            if (selectedId && String(st.id) === String(selectedId)) {
                opt.selected = true;
            }
            staffSelect.appendChild(opt);
        });

        if (selectedId) {
            staffSelect.value = selectedId;
        }
    }

    function openComplaint(c = null) {
        closeAllComplaintActionMenus();

        const form = document.getElementById('complaintForm');
        form.reset();

        document.getElementById('complaintMethod').value = c ? 'PUT' : '';
        form.action = c ? `/complaints/${c.id}` : `{{ route('complaints.store') }}`;
        document.getElementById('complaintTitle').textContent = c ? 'Edit Complaint' : 'Add Complaint';
        document.getElementById('complaintSubtitle').textContent = c ? 'Update complaint details and resolution.' : 'Record a customer feedback or service complaint.';
        document.getElementById('complaintSubmit').innerHTML = c ? '<i class="bi bi-check2-circle"></i> Update Complaint' : '<i class="bi bi-check2-circle"></i> Save Complaint';

        document.getElementById('complaintDate').value = c?.complaint_date ? (c.complaint_date.split('T')[0] || c.complaint_date) : '{{ now()->toDateString() }}';
        document.getElementById('complaintReason').value = c?.reason ?? '';
        document.getElementById('complaintAction').value = c?.action_taken ?? '';
        document.getElementById('complaintCompensation').value = c?.compensation ? Number(c.compensation) : '';

        if (c && c.job_card_id) {
            document.getElementById('complaintJobCard').value = c.job_card_id;
            onJobCardSelected(c.job_card_id, c.service_id, c.staff_id);
            if (c.category) document.getElementById('complaintCategory').value = c.category;
            if (c.subcategory) document.getElementById('complaintSubcategory').value = c.subcategory;
        } else {
            document.getElementById('complaintJobCard').value = '';
            onJobCardSelected('');
        }

        complaintModal.show();
    }

    function openComplaintDetailsModal(c) {
        if (!c) return;

        const jobCard = c.job_card || c.jobCard;
        const jcNum = jobCard?.job_card_number || (jobCard ? ('JC-' + String(jobCard.id).padStart(3, '0')) : '—');
        const jcName = jobCard?.job_card_name || 'Job Card';
        const custName = jobCard?.customer?.name || '—';

        // document.getElementById('detailsJobCardName').textContent = `${jcNum} — ${jcName} (${custName})`;
        document.getElementById('detailsJobCardName').textContent = `${jcNum} — ${jcName} `;

        document.getElementById('detailsStaffName').textContent = c.staff?.name || '—';

        const srvName = c.service?.service_name || '—';
        const catName = c.category || c.service?.category || '';
        document.getElementById('detailsServiceName').textContent = catName ? `${srvName} (${catName})` : srvName;

        let formattedDate = '—';
        if (c.complaint_date) {
            const parts = c.complaint_date.split(/[-T ]/);
            if (parts.length >= 3) {
                formattedDate = `${parts[2].slice(0, 2)}/${parts[1]}/${parts[0]}`;
            }
        }
        document.getElementById('detailsDate').textContent = formattedDate;

        document.getElementById('detailsReason').textContent = c.reason || 'No reason provided.';
        document.getElementById('detailsAction').textContent = c.action_taken || 'No corrective action recorded yet (Pending).';

        const comp = Number(c.compensation || 0);
        document.getElementById('detailsCompensation').textContent = `₹ ${comp.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        complaintDetailsModal.show();
    }

    // ---------------------------------------------------------------
    // Three-dot action popover (desktop)
    // ---------------------------------------------------------------

    function closeAllComplaintActionMenus() {
        document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(wrapper => {
            wrapper.classList.remove('is-open');
            const button = wrapper.querySelector('.pli-action-dots');
            if (button) {
                button.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            }
            const row = wrapper.closest('.premium-list-item');
            if (row) row.classList.remove('action-menu-row-open');
        });
    }

    function toggleComplaintActions(button) {
        const wrapper = button.closest('.pli-action-menu-wrap');
        const currentRow = button.closest('.premium-list-item');
        if (!wrapper) return;

        document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(menu => {
            if (menu !== wrapper) {
                menu.classList.remove('is-open');
                const menuButton = menu.querySelector('.pli-action-dots');
                if (menuButton) {
                    menuButton.classList.remove('is-open');
                    menuButton.setAttribute('aria-expanded', 'false');
                }
                const row = menu.closest('.premium-list-item');
                if (row) row.classList.remove('action-menu-row-open');
            }
        });

        const isOpen = wrapper.classList.toggle('is-open');
        button.classList.toggle('is-open', isOpen);
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (currentRow) {
            currentRow.classList.toggle('action-menu-row-open', isOpen);
        }
    }

    function closeComplaintActions(element) {
        const wrapper = element.closest('.pli-action-menu-wrap');
        if (!wrapper) return;

        wrapper.classList.remove('is-open');
        const button = wrapper.querySelector('.pli-action-dots');
        if (button) {
            button.classList.remove('is-open');
            button.setAttribute('aria-expanded', 'false');
        }
        const row = wrapper.closest('.premium-list-item');
        if (row) row.classList.remove('action-menu-row-open');
    }

    document.addEventListener('click', event => {
        if (!event.target.closest('.pli-action-menu-wrap')) {
            closeAllComplaintActionMenus();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeAllComplaintActionMenus();
        }
    });
</script>
@endpush
@endsection
