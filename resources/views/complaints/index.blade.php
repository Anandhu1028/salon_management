@extends('layouts.app')

@section('title', 'Complaints')
@section('page-title', 'Complaints')

@push('styles')
<style>

/* ==========================================================
   COMPLAINTS TABLE — 7-COLUMN GRID (OWN GRID, NOT --staff's)
   ========================================================== */

.premium-list--complaints {
    --complaint-grid:
        42px
        minmax(150px, 1.05fr)
        minmax(110px, 0.75fr)
        minmax(170px, 1.15fr)
        minmax(210px, 1.45fr)
        110px
        132px;
}

.premium-list--complaints .premium-list-head,
.premium-list--complaints .premium-list-item {
    display: grid !important;
    grid-template-columns: var(--complaint-grid) !important;
    align-items: center !important;
    column-gap: 10px !important;
    width: 100%;
    min-width: 1040px;
}

.premium-list--complaints .premium-list-head {
    grid-template-columns: var(--complaint-grid) !important;
}

.premium-list--complaints .premium-list-item {
    grid-template-columns: var(--complaint-grid) !important;
    grid-auto-flow: column !important;
    align-items: center !important;
    min-height: 64px;
    padding: 10px 12px;
}

.premium-list--complaints .premium-list-item > * {
    min-width: 0;
}

.premium-list--complaints .premium-list-head .pli-head-cell {
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
}

/* STAFF NAME */
.premium-list--complaints .pli-col-name,
.premium-list--complaints .pli-name-cell {
    min-width: 0;
    max-width: 100%;
}

.premium-list--complaints .pli-name-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.premium-list--complaints .pli-name-stack {
    min-width: 0;
    overflow: hidden;
}

.premium-list--complaints .pli-title,
.premium-list--complaints .staff-name {
    display: block;
    min-width: 0;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.premium-list--complaints .pli-icon {
    flex-shrink: 0;
}

/* TYPE */
.premium-list--complaints .pli-col-type {
    min-width: 0;
    overflow: hidden;
}

.premium-list--complaints .complaint-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* SUBJECT */
.premium-list--complaints .pli-col-subject {
    min-width: 0;
    overflow: hidden;
}

.premium-list--complaints .pli-col-subject .pli-col-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* DESCRIPTION */
.premium-list--complaints .pli-col-description {
    min-width: 0;
    overflow: hidden;
}

.premium-list--complaints .pli-col-description .pli-col-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* DATE */
.premium-list--complaints .pli-col-date {
    min-width: 0;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ACTION — must always stay one row */
.premium-list--complaints .pli-col-actions,
.premium-list--complaints .actions-cell {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    min-width: 0;
    width: 100%;
}

.premium-list--complaints .pli-col-actions > *,
.premium-list--complaints .actions-cell > *,
.premium-list--complaints .pli-btn-group {
    flex-shrink: 0 !important;
}

.premium-list--complaints .pli-btn-group {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 5px !important;
    flex-wrap: nowrap !important;
    margin: 0 !important;
    padding: 0 !important;
}

.premium-list--complaints .pli-btn-icon {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;
    flex: 0 0 32px !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Responsive — scroll, never wrap */
.premium-list--complaints {
    overflow-x: auto;
}

@media (max-width: 767px) {
    .mgmt-top-actions__right { gap: 8px; }
    .mgmt-action-btn__label { display: none; }
}

/* ═══════════════════════════════════════════════════════════════
   COMPLAINTS PAGE — PREMIUM STYLES
   ═══════════════════════════════════════════════════════════════ */

.complaint-type-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.complaint-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    white-space: nowrap;
}

.complaint-status-badge--pending {
    background: #FEF3C7;
    color: #D97706;
}

.complaint-status-badge--closed {
    background: #DCFCE7;
    color: #15803D;
}

/* ═══════════════════════════════════════════════════
   MODAL HEADER (shared style used across app modals)
   ═══════════════════════════════════════════════════ */
.att-modal-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px 26px 18px;
    border-bottom: 1px solid #F1F5F9;
}

.att-modal-icon {
    width: 48px;
    height: 48px;
    border-radius: 13px;
    background: #EDE9FE;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7C3AED;
    font-size: 21px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(124,58,237,.15);
}

.att-modal-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1E293B;
    margin: 0;
    letter-spacing: -0.01em;
}

.att-modal-subtitle {
    font-size: 0.82rem;
    color: #64748B;
    margin: 2px 0 0;
}

.att-modal-header .btn-close {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background-color: #F1F5F9;
    background-size: 12px;
    opacity: 1;
    transition: background-color .18s ease;
    flex-shrink: 0;
}

.att-modal-header .btn-close:hover {
    background-color: #E2E8F0;
}

/* Wider Add/Edit Complaint modal */
#complaintModal .modal-dialog {
    max-width: 760px;
}

@media (max-width: 800px) {
    #complaintModal .modal-dialog {
        max-width: calc(100% - 24px);
    }
}

/* Wider View Complaint modal (matches design mock) */
#viewComplaintModal .modal-dialog {
    max-width: 640px;
}

@media (max-width: 700px) {
    #viewComplaintModal .modal-dialog {
        max-width: calc(100% - 24px);
    }
}

/* ═══════════════════════════════════════════════════
   COMPLAINT MODAL — REDESIGNED FORM
   ═══════════════════════════════════════════════════ */

.cmp-section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.72rem;
    font-weight: 800;
    color: #1E293B;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 16px;
}

.cmp-section-label::before {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7C3AED, #5B21B6);
    flex-shrink: 0;
}

.cmp-grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
}

.cmp-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.cmp-field label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 7px;
}

.cmp-field label span { color: #EF4444; }

/* Icon-prefixed select/input wrap */
.cmp-select-wrap {
    position: relative;
}

.cmp-select-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 24px;
    height: 24px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    pointer-events: none;
    z-index: 2;
}

.cmp-select-wrap .form-select,
.cmp-select-wrap .form-control {
    height: 50px;
    padding-left: 46px !important;
    border-radius: 12px;
    border: 1.5px solid #E4EBFB;
    font-size: 0.86rem;
    font-weight: 500;
    color: #1E293B;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.cmp-select-wrap .form-select:focus,
.cmp-select-wrap .form-control:focus {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3.5px rgba(139,92,246,.12);
    outline: none;
}

.cmp-icon--violet { background: #EDE9FE; color: #7C3AED; }
.cmp-icon--blue   { background: #DBEAFE; color: #2563EB; }
.cmp-icon--amber  { background: #FEF3C7; color: #D97706; }
.cmp-icon--red    { background: #FEE2E2; color: #DC2626; }
.cmp-icon--slate  { background: #F1F5F9; color: #64748B; }
.cmp-icon--green  { background: #DCFCE7; color: #15803D; }

/* Plain fields (no icon) */
.cmp-field .form-control,
.cmp-field .form-select {
    height: 50px;
    border-radius: 12px;
    border: 1.5px solid #E4EBFB;
    font-size: 0.88rem;
    padding: 0 14px;
    color: #1E293B;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.cmp-field .form-control:focus,
.cmp-field .form-select:focus {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3.5px rgba(139,92,246,.12);
    outline: none;
}

.cmp-field textarea.form-control {
    height: auto;
    min-height: 110px;
    padding: 12px 14px;
    line-height: 1.55;
    resize: vertical;
}

.cmp-char-counter {
    display: block;
    text-align: right;
    font-size: 0.72rem;
    color: #94A3B8;
    font-weight: 600;
    margin-top: 6px;
}

/* Evidence drop zone */
.cmp-dropzone {
    border: 2px dashed #DDD6FE;
    border-radius: 14px;
    padding: 22px 16px;
    text-align: center;
    background: #FAFAFF;
    cursor: pointer;
    transition: background .18s ease, border-color .18s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.cmp-dropzone:hover {
    background: #F5F3FF;
    border-color: #C4B5FD;
}

.cmp-dropzone-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #EDE9FE;
    color: #7C3AED;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 10px;
}

.cmp-dropzone p { margin: 0; }
.cmp-dropzone .cmp-dz-title { color: #4B5563; font-size: 0.84rem; font-weight: 600; }
.cmp-dropzone .cmp-dz-sub { color: #9CA3AF; font-size: 0.72rem; margin-top: 3px; }

/* Note box */
.cmp-note-box {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #F5F3FF;
    border: 1.5px solid #E4D9FE;
    border-radius: 14px;
    padding: 14px 16px;
}

.cmp-note-icon {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: #EDE9FE;
    color: #7C3AED;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.cmp-note-box strong {
    display: block;
    color: #1E293B;
    font-size: 0.84rem;
    font-weight: 700;
    margin-bottom: 2px;
}

.cmp-note-box span {
    color: #6D28D9;
    font-size: 0.8rem;
    line-height: 1.5;
}

/* ═══════════════════════════════════════════════════
   VIEW COMPLAINT MODAL — CARD-STYLE DETAILS
   ═══════════════════════════════════════════════════ */

.cmp-view-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.cmp-view-card {
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1.5px solid #E4EBFB;
    border-radius: 14px;
    padding: 14px 16px;
    min-width: 0;
}

.cmp-view-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.cmp-view-label {
    font-size: 0.68rem;
    font-weight: 800;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}

.cmp-view-value {
    font-size: 0.92rem;
    font-weight: 700;
    color: #1E293B;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cmp-view-block {
    margin-top: 18px;
}

.cmp-view-block-label {
    font-size: 0.68rem;
    font-weight: 800;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}

.cmp-view-box {
    background: #F8FAFF;
    border: 1.5px solid #E4EBFB;
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 0.88rem;
    color: #1E293B;
    line-height: 1.65;
}

.cmp-view-evidence {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border: 1.5px solid #E4EBFB;
    border-radius: 12px;
    padding: 12px 16px;
}

.cmp-view-evidence-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.cmp-view-file-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    background: #FEE2E2;
    color: #DC2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
}

.cmp-view-file-icon.cmp-view-file-icon--image {
    background: #DBEAFE;
    color: #2563EB;
}

.cmp-view-file-name {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1E293B;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cmp-view-file-size {
    font-size: 0.72rem;
    color: #94A3B8;
    margin-top: 2px;
}

.cmp-view-file-link {
    font-size: 0.82rem;
    font-weight: 700;
    color: #4F46E5;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    flex-shrink: 0;
}

.cmp-view-file-link:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    .cmp-view-grid-2 {
        grid-template-columns: 1fr;
    }
}

</style>
@endpush

@section('content')

<div class="management-page">

{{-- Top Actions Bar --}}
@include('partials.mgmt-top-actions', [
    'addLabel' => 'Add Complaint',
    'addModal' => '#complaintModal',
    'addOnclick' => 'openAddComplaintModal()',
    'filterModule' => 'complaints',
    'filterRoute' => route('complaints.index'),
])

{{-- Stats Cards --}}
{{-- Complaint Statistics --}}
<div class="mgmt-stats-grid mgmt-stats-grid--4">

    {{-- Total --}}
    @include('partials.mgmt-stat-card', [
        'theme' => 'indigo',
        'icon' => 'people-purple',
        'label' => 'Total Complaints',
        'value' => $totalComplaints,
        'subtext' => 'All complaints',
        'sparkColor' => '#6366F1',
        'trend' => null,
    ])

    {{-- Pending --}}
    @include('partials.mgmt-stat-card', [
        'theme' => 'orange',
        'icon' => 'clock-orange',
        'label' => 'Pending',
        'value' => $pendingComplaints,
        'subtext' => 'Awaiting action',
        'sparkColor' => '#F59E0B',
        'trend' => null,
    ])

    {{-- Closed --}}
    @include('partials.mgmt-stat-card', [
        'theme' => 'green',
        'icon' => 'shield-green',
        'label' => 'Closed',
        'value' => $closedComplaints,
        'subtext' => 'Completed complaints',
        'sparkColor' => '#22C55E',
        'trend' => null,
    ])

    {{-- Today --}}
    @include('partials.mgmt-stat-card', [
        'theme' => 'blue',
        'icon' => 'calendar-blue',
        'label' => 'Today',
        'value' => $todayComplaints,
        'subtext' => 'Reported today',
        'sparkColor' => '#3B82F6',
        'trend' => null,
    ])

</div>

{{-- Session Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Main Content Card --}}
<div class="content-card">
    <div class="content-card-header">
        <div>
            <h2>Complaints</h2>
            <span>{{ $complaints->total() }} complaint(s) found</span>
        </div>
        <div class="content-card-header-actions">
            {{-- Search --}}
            <form method="GET" action="{{ route('complaints.index') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="type_filter" value="{{ $typeFilter }}">
                <input type="hidden" name="status_filter" value="">
                <div class="search-input-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="search" name="search" class="form-control search-input"
                           placeholder="Search complaints..." value="{{ $search }}" autocomplete="off">
                </div>
            </form>
        </div>
    </div>

    @if($complaints->count())

    {{-- Table Header --}}
    {{-- Complaints Table --}}
<div class="premium-list premium-list--complaints premium-list--staff premium-list--feed premium-list--compact premium-list--mgmt">

    {{-- Table Header --}}
    <div class="premium-list-head">
        <span class="pli-head-cell col-center">#</span>
        <span class="pli-head-cell col-left">STAFF NAME</span>
        <span class="pli-head-cell col-left">TYPE</span>
        <span class="pli-head-cell col-left">SUBJECT</span>
        <span class="pli-head-cell col-left">DESCRIPTION</span>
        <span class="pli-head-cell col-center">DATE</span>
        <span class="pli-head-cell col-center">ACTION</span>
    </div>

    @php
        $listStart = ($complaints->currentPage() - 1) * $complaints->perPage();
    @endphp

    @foreach($complaints as $complaint)

        <article
            class="premium-list-item"
            id="complaint-row-{{ $complaint->id }}"
        >

            {{-- # --}}
            <div class="pli-rank col-center">
                {{ $listStart + $loop->iteration }}
            </div>

            {{-- Staff Name --}}
            <div class="pli-col col-left pli-col-name">
                <div class="pli-name-cell">

                    <div
                        class="pli-icon"
                        style="
                            background:#EEF2FF;
                            color:#6366F1;
                            width:36px;
                            height:36px;
                            border-radius:10px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            flex-shrink:0;
                            font-weight:700;
                        "
                    >
                        {{ strtoupper(substr($complaint->complainantStaff->name ?? 'S', 0, 1)) }}
                    </div>

                    <div class="pli-name-stack">
                        <span class="pli-title staff-name">
                            {{ $complaint->complainantStaff->name ?? '—' }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- Type --}}
            <div class="pli-col col-left pli-col-type">
                <span
                    class="complaint-type-badge"
                    style="
                        padding:5px 10px;
                        border-radius:999px;
                        background:#F5F3FF;
                        color:#6366F1;
                        font-size:.68rem;
                        font-weight:700;
                    "
                >
                    <i class="bi {{ $complaint->complaintType->icon ?? 'bi-exclamation-circle' }}"></i>

                    {{ $complaint->complaintType->name ?? 'General' }}
                </span>
            </div>

            {{-- Subject --}}
            <div class="pli-col col-left pli-col-subject">
                <span
                    class="pli-col-text"
                    style="
                        font-size:.8125rem;
                        font-weight:600;
                        color:#334155;
                    "
                    title="{{ $complaint->subject }}"
                >
                    {{ $complaint->subject }}
                </span>
            </div>

            {{-- Description --}}
            <div class="pli-col col-left pli-col-description">
                <span
                    class="pli-col-text complaint-description"
                    title="{{ $complaint->description }}"
                >
                    {{ $complaint->description ?: '—' }}
                </span>
            </div>

            {{-- Date --}}
            <div class="pli-col col-center pli-col-date">
                <span
                    style="
                        font-size:.75rem;
                        font-weight:600;
                        color:#64748B;
                        white-space:nowrap;
                    "
                >
                    <i class="bi bi-calendar3 me-1" style="color:#94A3B8;"></i>
                    {{ $complaint->date_of_complaint->format('d M Y') }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="pli-col pli-col-actions actions-cell col-center">

                {{-- View --}}
                <button
                    type="button"
                    class="pli-btn-icon pli-btn-icon--view"
                    title="View"
                    onclick="openViewComplaintModal({{ $complaint->id }})"
                >
                    @include('partials.action-icons', [
                        'type' => 'view',
                        'size' => 15
                    ])
                </button>

                {{-- Edit --}}
                <button
                    type="button"
                    class="pli-btn-icon pli-btn-icon--edit"
                    title="Edit"
                    data-bs-toggle="modal"
                    data-bs-target="#complaintModal"
                    onclick='openEditComplaintModal(@json($complaint))'
                >
                    @include('partials.action-icons', [
                        'type' => 'edit',
                        'size' => 15
                    ])
                </button>

                {{-- Delete --}}
                <button
                    type="button"
                    class="pli-btn-icon pli-btn-icon--danger"
                    title="Delete"
                    onclick="openDeleteComplaintModal(
                        {{ $complaint->id }},
                        @js($complaint->subject)
                    )"
                >
                    @include('partials.action-icons', [
                        'type' => 'delete',
                        'size' => 15
                    ])
                </button>

            </div>

        </article>

    @endforeach

</div>

    {{-- Pagination --}}
    @if($complaints->hasPages())
        <div class="content-card-footer">
            @include('partials.pagination', ['paginator' => $complaints])
        </div>
    @endif

    @else
    <div class="empty-state">
        <div style="text-align:center; padding: 60px 20px;">
            <div style="width:72px;height:72px;border-radius:20px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:30px;color:#7C3AED;">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <h4 style="font-size:1.05rem;font-weight:700;color:#1E293B;margin-bottom:6px;">No Complaints Found</h4>
            <p style="color:#64748B;font-size:0.88rem;margin-bottom:20px;">No complaints have been filed yet. Click below to add a new complaint.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#complaintModal" onclick="openAddComplaintModal()">
                <i class="bi bi-plus-lg me-1"></i> Add Complaint
            </button>
        </div>
    </div>
    @endif
</div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- COMPLAINT MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="complaintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="complaint_id" id="complaintId" value="">

                {{-- Hidden fields kept for backend compatibility (fields removed from visible UI) --}}
                <input type="hidden" name="against_staff_id" id="comp_against_id" value="">
                <input type="hidden" name="priority" id="comp_priority" value="Medium">
                <input type="hidden" name="department" id="comp_department" value="">

                {{-- Modal Header --}}
                <div class="att-modal-header">
                    <div class="att-modal-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div>
                        <h5 class="att-modal-title" id="complaintModalTitle">Add Complaint</h5>
                        <p class="att-modal-subtitle" id="complaintModalSubtitle">Record and manage staff complaints</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body" style="padding: 26px;">

                    {{-- Complaint Information Section --}}
                    <div class="cmp-section-label">Complaint Information</div>

                    {{-- Row 1: Complainant + Type --}}
                    <div class="cmp-grid-2 mb-4">
                        <div class="cmp-field">
                            <label>Complainant (Staff) <span>*</span></label>
                            <div class="cmp-select-wrap">
                                <span class="cmp-select-icon cmp-icon--violet"><i class="bi bi-person"></i></span>
                                <select name="complainant_staff_id" id="comp_complainant_id" class="form-select" required>
                                    <option value="">Select staff member...</option>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="cmp-field">
                            <label>Complaint Type <span>*</span></label>
                            <div class="cmp-select-wrap">
                                <span class="cmp-select-icon cmp-icon--amber"><i class="bi bi-tags"></i></span>
                                <select name="complaint_type_id" id="comp_type_id" class="form-select" required>
                                    <option value="">Select type...</option>
                                    @foreach($complaintTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="cmp-field mb-4">
                        <label>Date of Complaint <span>*</span></label>
                        <div class="cmp-select-wrap">
                            <span class="cmp-select-icon cmp-icon--blue"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date_of_complaint" id="comp_date" class="form-control" required>
                        </div>
                    </div>
                    {{-- Subject & Description --}}
                    <div class="cmp-field mb-4">
                        <label>Subject <span>*</span></label>
                        <input type="text" name="subject" id="comp_subject" class="form-control" maxlength="255" required>
                    </div>

                    <div class="cmp-field mb-4">
                        <label>Description <span>*</span></label>
                        <textarea name="description" id="comp_description" class="form-control" rows="4" maxlength="1000" required oninput="updateCharCounter()"></textarea>
                        <small class="cmp-char-counter" id="comp_char_counter">0 / 1000</small>
                    </div>

                    <hr style="border-color:#F1F5F9; margin: 0 0 20px;">

                    {{-- Additional Information --}}
                    <div class="cmp-section-label">Additional Information</div>

                    <div class="cmp-field mb-4">
                        <label>Attach Evidence (Optional)</label>
                        <div class="cmp-dropzone" id="evidenceDropZone">
                            <div class="cmp-dropzone-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <p class="cmp-dz-title">Drag & drop files here or click to browse</p>
                            <p class="cmp-dz-sub">JPG, PNG, PDF up to 5MB</p>
                            <input type="file" name="evidence" id="comp_evidence" class="form-control" style="display: none;" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer" style="padding: 16px 26px; border-top: 1px solid #F1F5F9; background:#FAFBFF;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; padding:9px 20px; font-weight:600;">
                        <i class="bi bi-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius:10px; padding:9px 22px; font-weight:700; background:linear-gradient(135deg,#6366f1,#4f46e5); border:none; box-shadow:0 3px 10px rgba(99,102,241,.35);">
                        <i class="bi bi-check2 me-1"></i> Submit Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- VIEW COMPLAINT MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="viewComplaintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <div class="att-modal-header">
                <div class="att-modal-icon">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <div>
                    <h5 class="att-modal-title">Complaint Details</h5>
                    <p class="att-modal-subtitle">View complete complaint information</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px 26px;" id="viewComplaintContent">
                <!-- Loaded via AJAX -->
            </div>
            <div class="modal-footer" style="padding: 16px 26px; border-top: 1px solid #F1F5F9; background:#FAFBFF;" id="viewComplaintFooter">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; padding:9px 20px; font-weight:600;">
                    <i class="bi bi-x me-1"></i> Close
                </button>
                <button type="button" id="markAsClosedBtn" class="btn btn-success" style="border-radius:10px; padding:9px 22px; font-weight:700; display:none;">
                    <i class="bi bi-check2-circle me-1"></i> Mark as Closed
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- DELETE CONFIRM MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="deleteComplaintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <div class="att-modal-header">
                <div class="att-modal-icon" style="background: #FEE2E2; color: #DC2626;">
                    <i class="bi bi-trash3"></i>
                </div>
                <div>
                    <h5 class="att-modal-title">Delete Complaint</h5>
                    <p class="att-modal-subtitle">This action cannot be undone</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p style="color: #64748B; margin-bottom: 0;">
                    Are you sure you want to delete the complaint "<span id="deleteComplaintSubject"></span>"? This action cannot be reversed.
                </p>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #F1F5F9; background:#FAFBFF;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; padding:9px 20px; font-weight:600;">
                    Cancel
                </button>
                <form id="deleteComplaintForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="border-radius:10px; padding:9px 22px; font-weight:700;">
                        <i class="bi bi-trash3 me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    let selectedComplaintId = null;

    function updateCharCounter() {
        const desc = document.getElementById('comp_description');
        const counter = document.getElementById('comp_char_counter');
        if (desc && counter) {
            counter.textContent = `${desc.value.length} / 1000`;
        }
    }

    function openAddComplaintModal() {
        document.getElementById('complaintForm').reset();
        document.getElementById('complaintId').value = '';
        document.getElementById('complaintModalTitle').textContent = 'Add Complaint';
        document.getElementById('complaintModalSubtitle').textContent = 'Record and manage staff complaints';
        document.getElementById('comp_date').valueAsDate = new Date();

        // Reset hidden defaults
        document.getElementById('comp_against_id').value = '';
        document.getElementById('comp_priority').value = 'Medium';
        document.getElementById('comp_department').value = '';

        updateCharCounter();
    }

    function openEditComplaintModal(complaint) {
        document.getElementById('complaintId').value = complaint.id;
        document.getElementById('comp_complainant_id').value = complaint.complainant_staff_id;
        document.getElementById('comp_type_id').value = complaint.complaint_type_id;
        document.getElementById('comp_date').value = complaint.date_of_complaint;
        document.getElementById('comp_subject').value = complaint.subject;
        document.getElementById('comp_description').value = complaint.description;

        // Hidden fields — keep existing backend values on edit
        document.getElementById('comp_against_id').value = complaint.against_staff_id || '';
        document.getElementById('comp_priority').value = complaint.priority || 'Medium';
        document.getElementById('comp_department').value = complaint.department || '';

        document.getElementById('complaintModalTitle').textContent = 'Edit Complaint';
        document.getElementById('complaintModalSubtitle').textContent = 'Update complaint details';

        updateCharCounter();
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function renderStatusBadge(status) {
        const isClosed = (status || '').toLowerCase() === 'closed';
        const icon = isClosed ? 'bi-check-circle-fill' : 'bi-circle';
        const cls = isClosed ? 'complaint-status-badge--closed' : 'complaint-status-badge--pending';
        return `<span class="complaint-status-badge ${cls}"><i class="bi ${icon} me-1"></i>${status || 'Pending'}</span>`;
    }

    function openViewComplaintModal(complaintId) {
        selectedComplaintId = complaintId;
        fetch(`{{ route('complaints.show', ':id') }}`.replace(':id', complaintId))
            .then(r => r.json())
            .then(complaint => {

                const staffName = complaint.complainant_staff?.name || '—';
                const typeName = complaint.complaint_type?.name || '—';
                const typeIcon = complaint.complaint_type?.icon || 'bi-exclamation-circle';
                const status = complaint.status || 'Pending';
                const isClosed = status.toLowerCase() === 'closed';

                let evidenceHtml = '';
                if (complaint.evidence_url) {
                    const fileName = complaint.evidence_name || 'evidence file';
                    const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
                    const iconClass = isImage ? 'cmp-view-file-icon cmp-view-file-icon--image' : 'cmp-view-file-icon';
                    const iconLabel = isImage ? 'IMG' : 'PDF';

                    evidenceHtml = `
                        <div class="cmp-view-block">
                            <div class="cmp-view-block-label">Attached Evidence</div>
                            <div class="cmp-view-evidence">
                                <div class="cmp-view-evidence-left">
                                    <div class="${iconClass}">${iconLabel}</div>
                                    <div style="min-width:0;">
                                        <div class="cmp-view-file-name">${fileName}</div>
                                        <div class="cmp-view-file-size">${complaint.evidence_size || ''}</div>
                                    </div>
                                </div>
                                <a href="${complaint.evidence_url}" target="_blank" rel="noopener" class="cmp-view-file-link">
                                    View File <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }

                let content = `
                    <div class="cmp-view-grid-2">
                        <div class="cmp-view-card">
                            <div class="cmp-view-icon cmp-icon--violet"><i class="bi bi-person"></i></div>
                            <div style="min-width:0;">
                                <div class="cmp-view-label">Staff Name</div>
                                <div class="cmp-view-value">${staffName}</div>
                            </div>
                        </div>
                        <div class="cmp-view-card">
                            <div class="cmp-view-icon cmp-icon--blue"><i class="bi ${typeIcon}"></i></div>
                            <div style="min-width:0;">
                                <div class="cmp-view-label">Complaint Type</div>
                                <div class="cmp-view-value">${typeName}</div>
                            </div>
                        </div>
                    </div>

                    <div class="cmp-view-grid-2" style="margin-top:14px;">
                        <div class="cmp-view-card">
                            <div class="cmp-view-icon cmp-icon--green"><i class="bi bi-calendar3"></i></div>
                            <div style="min-width:0;">
                                <div class="cmp-view-label">Date of Complaint</div>
                                <div class="cmp-view-value">${formatDate(complaint.date_of_complaint)}</div>
                            </div>
                        </div>
                        <div class="cmp-view-card" id="statusCard">
                            <div class="cmp-view-icon ${isClosed ? 'cmp-icon--green' : 'cmp-icon--amber'}" id="statusCardIcon">
                                <i class="bi ${isClosed ? 'bi-check-circle' : 'bi-circle'}"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="cmp-view-label">Status</div>
                                <div class="cmp-view-value" id="statusCardValue">${renderStatusBadge(status)}</div>
                            </div>
                        </div>
                    </div>

                    <div class="cmp-view-block">
                        <div class="cmp-view-block-label">Subject</div>
                        <div class="cmp-view-box">${complaint.subject || '—'}</div>
                    </div>

                    <div class="cmp-view-block">
                        <div class="cmp-view-block-label">Description</div>
                        <div class="cmp-view-box">${complaint.description || '—'}</div>
                    </div>

                    ${evidenceHtml}
                `;

                document.getElementById('viewComplaintContent').innerHTML = content;

                const markBtn = document.getElementById('markAsClosedBtn');
                markBtn.style.display = isClosed ? 'none' : 'inline-flex';
                markBtn.onclick = () => markComplaintAsClosed(complaint.id);

                new bootstrap.Modal(document.getElementById('viewComplaintModal')).show();
            });
    }

    function markComplaintAsClosed(complaintId) {
        const markBtn = document.getElementById('markAsClosedBtn');
        markBtn.disabled = true;
        markBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Closing...';

        fetch(`{{ route('complaints.close', ':id') }}`.replace(':id', complaintId), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            // Update status card in the open modal
            const statusValue = document.getElementById('statusCardValue');
            const statusIcon = document.getElementById('statusCardIcon');
            if (statusValue) statusValue.innerHTML = renderStatusBadge(data.status);
            if (statusIcon) {
                statusIcon.className = 'cmp-view-icon cmp-icon--green';
                statusIcon.innerHTML = '<i class="bi bi-check-circle"></i>';
            }

            markBtn.style.display = 'none';

            // Update the row in the table without a full reload
            const row = document.getElementById(`complaint-row-${complaintId}`);
            if (row) {
                row.style.transition = 'background .3s ease';
                row.style.background = '#F0FDF4';
                setTimeout(() => { row.style.background = ''; }, 900);
            }

            // Refresh stats + row status source of truth
            setTimeout(() => window.location.reload(), 700);
        })
        .catch(() => {
            markBtn.disabled = false;
            markBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Mark as Closed';
            alert('Something went wrong while closing this complaint. Please try again.');
        });
    }

    function openDeleteComplaintModal(complaintId, subject) {
        selectedComplaintId = complaintId;
        document.getElementById('deleteComplaintSubject').textContent = subject;
        document.getElementById('deleteComplaintForm').action = `{{ route('complaints.destroy', ':id') }}`.replace(':id', complaintId);
        new bootstrap.Modal(document.getElementById('deleteComplaintModal')).show();
    }

    // File drop zone
    const evidenceDropZone = document.getElementById('evidenceDropZone');
    const evidenceInput = document.getElementById('comp_evidence');

    if (evidenceDropZone) {
        evidenceDropZone.addEventListener('click', () => evidenceInput.click());
        evidenceDropZone.addEventListener('dragover', e => {
            e.preventDefault();
            evidenceDropZone.style.background = '#EDE9FE';
        });
        evidenceDropZone.addEventListener('dragleave', () => {
            evidenceDropZone.style.background = '#FAFAFF';
        });
        evidenceDropZone.addEventListener('drop', e => {
            e.preventDefault();
            evidenceDropZone.style.background = '#FAFAFF';
            if (e.dataTransfer.files.length) {
                evidenceInput.files = e.dataTransfer.files;
            }
        });
    }
</script>
@endpush

@endsection