@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   ATTENDANCE PAGE — PREMIUM STYLES
   ═══════════════════════════════════════════════════ */

/* Month Selector Bar */
.attendance-month-bar {
    display: flex;
    align-items: center;
    gap: 0;
    flex-wrap: wrap;
    margin-bottom: 0;
}

.attendance-month-nav {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #fff;
    border: 1.5px solid #E4EBFB;
    border-radius: 10px;
    padding: 5px 8px;
    box-shadow: 0 1px 3px rgba(99,102,241,.04);
}

.attendance-month-nav select {
    border: none;
    outline: none;
    font-size: 0.78rem;
    font-weight: 600;
    color: #1E293B;
    background: transparent;
    cursor: pointer;
    padding: 0 4px;
}

.att-nav-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: #64748B;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s ease;
    font-size: 12px;
}

.att-nav-btn:hover {
    background: #EDE9FE;
    color: #7C3AED;
}

/* ── Filter Modal Options ── */
.att-filter-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.att-filter-option {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1.5px solid #E4EBFB;
    background: #fff;
    font-size: 0.86rem;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all .16s ease;
    text-align: left;
}

.att-filter-option:hover {
    border-color: #C7D2FE;
    background: #F8FAFF;
}

.att-filter-option.active {
    border-color: #A5B4FC;
    background: #EEF2FF;
    color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(99,102,241,.10);
}

.att-filter-option-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
}

.att-filter-option-check {
    margin-left: auto;
    font-size: 15px;
    color: #4F46E5;
    opacity: 0;
    transition: opacity .16s ease;
}

.att-filter-option.active .att-filter-option-check {
    opacity: 1;
}

/* ── Attendance Table Alignment ──
   Grid-based columns so header cells and row cells always line up:
   # | Staff Name (flexible) | Working Days | Present | Absent | Actions
*/
.premium-list--attendance .premium-list-head,
.premium-list--attendance .premium-list-item {
    display: grid;
    grid-template-columns: 40px 2fr 1fr 1fr 1fr 1fr;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
}

.premium-list--attendance .pli-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94A3B8;
    font-weight: 600;
    font-size: 0.8rem;
}

.premium-list--attendance .col-center {
    text-align: center;
    justify-self: center;
}

.premium-list--attendance .col-left {
    text-align: left;
    justify-self: start;
    min-width: 0;
    width: 100%;
}

.premium-list--attendance .pli-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.premium-list--attendance .pli-name-stack {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.premium-list--attendance .pli-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.premium-list--attendance .pli-col-actions {
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 767px) {
    .premium-list--attendance .premium-list-head,
    .premium-list--attendance .premium-list-item {
        grid-template-columns: 28px 1.6fr 1fr 1fr 1fr 0.8fr;
        gap: 6px;
        padding: 10px 12px;
    }
}

/* Attendance Table Rows */
.att-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}

.att-badge--present { background: #DCFCE7; color: #15803D; }
.att-badge--absent   { background: #FEE2E2; color: #DC2626; }
.att-badge--total   { background: #EDE9FE; color: #5B21B6; }

/* Modal Styles */
.att-modal-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px 24px 16px;
    border-bottom: 1px solid #F1F5F9;
}

.att-modal-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #EDE9FE;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7C3AED;
    font-size: 20px;
    flex-shrink: 0;
}

.att-modal-title { font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0; }
.att-modal-subtitle { font-size: 0.79rem; color: #64748B; margin: 2px 0 0; }

.att-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.att-form-grid--3 {
    grid-template-columns: 1fr 1fr 1fr;
}

.att-form-field label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 6px;
}

.att-form-field {
    min-width: 0;
}

.att-form-field .form-control,
.att-form-field .form-select {
    border-radius: 10px;
    border: 1.5px solid #E4EBFB;
    font-size: 0.88rem;
    padding: 9px 12px;
    color: #1E293B;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.att-form-field .form-control:focus,
.att-form-field .form-select:focus {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12);
    outline: none;
}

.att-stat-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #F8FAFC;
    border-radius: 10px;
    border: 1.5px solid #E4EBFB;
}

.att-stat-inline-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}

/* ── Redesigned Modal Close Button ── */
.att-modal-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1.5px solid #E4EBFB;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748B;
    cursor: pointer;
    transition: all .18s ease;
    flex-shrink: 0;
    font-size: 15px;
}
.att-modal-close:hover {
    background: #F8FAFC;
    color: #334155;
    border-color: #CBD5E1;
}

/* ── Icon Select Fields (Staff Member / Month / Year) ── */
.att-icon-field {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1.5px solid #E4EBFB;
    border-radius: 12px;
    padding: 7px 12px 7px 8px;
    background: #fff;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.att-icon-field:focus-within {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12);
}
.att-icon-field-icon {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: #EDE9FE;
    color: #7C3AED;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.att-icon-field select {
    flex: 1;
    min-width: 0;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.88rem;
    font-weight: 600;
    color: #1E293B;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    padding: 4px 2px;
}
.att-icon-field select:disabled {
    color: #94A3B8;
    cursor: not-allowed;
}
.att-icon-field-chevron {
    color: #94A3B8;
    font-size: 12px;
    flex-shrink: 0;
}

/* ── Working Days Field (readonly, auto-calculated) ── */
.att-days-field {
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1.5px solid #DDD6FE;
    border-radius: 14px;
    padding: 11px 16px;
    background: #F5F3FF;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.att-days-field:focus-within {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 4px rgba(139,92,246,.14);
}
.att-days-field-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #E9E1FE;
    color: #7C3AED;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.att-days-field input {
    flex: 1;
    min-width: 0;
    border: none;
    background: transparent;
    outline: none;
    font-size: 1rem;
    font-weight: 700;
    color: #1E293B;
}
.att-days-field input::placeholder {
    color: #A78BFA;
    font-weight: 600;
}
.att-days-field-suffix {
    font-size: 0.82rem;
    font-weight: 600;
    color: #8B5CF6;
    flex-shrink: 0;
}

/* Present (green) / Absent (red) variants of the days field, used for
   the actual attendance number inputs. */
.att-days-field--present {
    border-color: #BBF7D0;
    background: #F0FDF4;
}
.att-days-field--present .att-days-field-icon {
    background: #DCFCE7;
    color: #16A34A;
}
.att-days-field--present:focus-within {
    border-color: #22C55E;
    box-shadow: 0 0 0 4px rgba(34,197,94,.14);
}
.att-days-field--present .att-days-field-suffix { color: #16A34A; }

.att-days-field--absent {
    border-color: #FECACA;
    background: #FEF2F2;
}
.att-days-field--absent .att-days-field-icon {
    background: #FEE2E2;
    color: #DC2626;
}
.att-days-field--absent:focus-within {
    border-color: #EF4444;
    box-shadow: 0 0 0 4px rgba(239,68,68,.14);
}
.att-days-field--absent .att-days-field-suffix { color: #DC2626; }

.att-field-help {
    font-size: 0.78rem;
    color: #94A3B8;
    margin: 8px 2px 0;
}

/* ── Live Attendance Summary Card ── */
.att-summary-card {
    border-radius: 14px;
    border: 1.5px solid #E4EBFB;
    background: #F8FAFC;
    padding: 14px 16px;
}
.att-summary-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}
.att-summary-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 10px 6px;
    border-radius: 10px;
    background: #fff;
    border: 1.5px solid #E4EBFB;
}
.att-summary-label {
    font-size: 0.66rem;
    font-weight: 700;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: .04em;
    text-align: center;
}
.att-summary-value {
    font-size: 1.15rem;
    font-weight: 800;
    color: #1E293B;
}
.att-summary-item--present { border-color: #BBF7D0; background: #F0FDF4; }
.att-summary-item--present .att-summary-value { color: #15803D; }
.att-summary-item--absent { border-color: #FECACA; background: #FEF2F2; }
.att-summary-item--absent .att-summary-value { color: #DC2626; }
.att-summary-item--remaining { border-color: #C7D2FE; background: #EEF2FF; }
.att-summary-item--remaining .att-summary-value { color: #4F46E5; }

.att-summary-error {
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border-radius: 10px;
    background: #FEF2F2;
    border: 1.5px solid #FECACA;
    color: #DC2626;
    font-size: 0.82rem;
    font-weight: 600;
}

@media (max-width: 767px) {
    .att-summary-row { grid-template-columns: repeat(2, 1fr); }
}

/* ── Footer Buttons ── */
.att-btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 12px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: 0.88rem;
    color: #334155;
    background: #fff;
    border: 1.5px solid #E4EBFB;
    transition: all .18s ease;
}
.att-btn-cancel:hover {
    background: #F8FAFC;
    border-color: #CBD5E1;
    color: #1E293B;
}
.att-btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border-radius: 12px;
    padding: 10px 24px;
    font-weight: 700;
    font-size: 0.9rem;
    color: #fff;
    background: linear-gradient(135deg, #6366F1, #4F46E5);
    border: none;
    box-shadow: 0 6px 16px rgba(79,70,229,.35);
    transition: all .18s ease;
}
.att-btn-save:hover {
    box-shadow: 0 8px 20px rgba(79,70,229,.45);
    transform: translateY(-1px);
    color: #fff;
}
.att-btn-save:disabled {
    opacity: .55;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* Sidebar submenu styles */
.sidebar-has-submenu { position: relative; }
.sidebar-submenu-toggle {
    display: flex; align-items: center; gap: 10px;
    width: 100%; padding: 10px 16px;
    background: none; border: none; cursor: pointer;
    color: #64748B; font-size: 0.875rem; font-weight: 500;
    border-radius: 10px; transition: all .18s ease;
    text-align: left;
}
.sidebar-submenu-toggle:hover, .sidebar-submenu-toggle.active {
    background: linear-gradient(135deg, rgba(124,58,237,.08), rgba(139,92,246,.05));
    color: #5B21B6;
}
.sidebar-submenu-arrow { margin-left: auto; font-size: 11px; transition: transform .2s ease; }
.sidebar-has-submenu.submenu-open .sidebar-submenu-arrow { transform: rotate(180deg); }
.sidebar-submenu {
    display: none; list-style: none; padding: 4px 0 4px 38px; margin: 0;
}
.sidebar-submenu.show { display: block; }
.sidebar-submenu li a {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px; border-radius: 8px;
    color: #64748B; font-size: 0.82rem; font-weight: 500;
    text-decoration: none; transition: all .15s ease;
}
.sidebar-submenu li.active a, .sidebar-submenu li a:hover {
    background: rgba(124,58,237,.08); color: #5B21B6;
}
.sidebar-submenu li .sidebar-nav-icon { font-size: 13px; width: 18px; text-align: center; }

@media (max-width: 767px) {
    .attendance-month-nav { padding: 4px 6px; }
    .attendance-month-nav select { font-size: 0.7rem; padding: 0 2px; }
    .att-nav-btn { width: 20px; height: 20px; font-size: 10px; }
    .mgmt-top-actions__right { gap: 8px; }
    .mgmt-action-btn__label { display: none; }
    .mgmt-top-actions__right { flex-wrap: wrap; }
    .att-form-grid, .att-form-grid--3 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div class="management-page">

{{-- Top Actions Bar --}}
<div class="mgmt-top-actions">
    <div class="mgmt-top-actions__right">
        {{-- Month/Year Selector (Small) --}}
        <form method="GET" action="{{ route('attendance.index') }}" id="monthFilterForm" class="attendance-month-bar">
            <input type="hidden" name="search" value="{{ $search }}">
            <div class="attendance-month-nav">
                <button type="button" class="att-nav-btn" id="prevMonthBtn" title="Previous month">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <select name="month" id="monthSelect" onchange="document.getElementById('monthFilterForm').submit()">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('M') }}
                        </option>
                    @endforeach
                </select>
                <select name="year" id="yearSelect" onchange="document.getElementById('monthFilterForm').submit()">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $currentYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
                <button type="button" class="att-nav-btn" id="nextMonthBtn" title="Next month">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </form>

        {{-- Add Attendance Button --}}
        <button type="button" class="mgmt-action-btn mgmt-action-btn--primary"
            data-bs-toggle="modal" data-bs-target="#attendanceModal"
            onclick="openAddAttendanceModal()">
            <span class="mgmt-action-btn__icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </span>
            <span class="mgmt-action-btn__label">Add Attendance</span>
        </button>
    </div>
</div>

 {{-- Stats Cards --}}
@php
    $monthName = DateTime::createFromFormat(
        '!m',
        $currentMonth
    )->format('F');
@endphp

<div class="mgmt-stats-grid mgmt-stats-grid--4">

    @include('partials.mgmt-stat-card', [
        'theme'      => 'indigo',
        'icon'       => 'people-purple',
        'label'      => 'Total Staff',
        'value'      => $totalStaff,
        'subtext'    => 'Active staff members',
        'sparkColor' => '#6366F1',
        'trend'      => null,
    ])

    @include('partials.mgmt-stat-card', [
        'theme'      => 'green',
        'icon'       => 'shield-green',
        'label'      => 'Attendance Records',
        'value'      => $attendanceRecords,
        'subtext'    => $monthName . ' ' . $currentYear,
        'sparkColor' => '#22C55E',
        'trend'      => null,
    ])

    @include('partials.mgmt-stat-card', [
        'theme'      => 'orange',
        'icon'       => 'clock-orange',
        'label'      => 'Working Days',
        'value'      => $totalWorkingDays,
        'subtext'    => 'Total configured days',
        'sparkColor' => '#F59E0B',
        'trend'      => null,
    ])

    @include('partials.mgmt-stat-card', [
        'theme'      => 'blue',
        'icon'       => 'calendar-blue',
        'label'      => 'Selected Month',
        'value'      => $monthName,
        'subtext'    => (string) $currentYear,
        'sparkColor' => '#3B82F6',
        'trend'      => null,
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
                <h2>Attendance — {{ $monthName }} {{ $currentYear }}</h2>
                <span>{{ $attendances->total() }} record(s) found</span>
            </div>
            <div class="content-card-header-actions">
                {{-- Search --}}
                <form method="GET" action="{{ route('attendance.index') }}" class="staff-search">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="search" name="search"
                               placeholder="Search staff name..." value="{{ $search }}" autocomplete="off">
                    </div>
                </form>
            </div>
        </div>

        @if($attendances->count())

        {{-- Table Header --}}
        <div class="premium-list premium-list--attendance premium-list--feed premium-list--compact premium-list--mgmt">
            <div class="premium-list-head">
                <span class="pli-head-cell col-center">#</span>
                <span class="pli-head-cell col-left">Staff Name</span>
                <span class="pli-head-cell col-center">Working Days</span>
                <span class="pli-head-cell col-center">Present</span>
                <span class="pli-head-cell col-center">Absent</span>
                <span class="pli-head-cell col-center">Actions</span>
            </div>

            @php $listStart = ($attendances->currentPage() - 1) * $attendances->perPage(); @endphp

            @foreach($attendances as $record)

                <article class="premium-list-item" id="att-row-{{ $record->id }}">
                    <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                    <div class="pli-col col-left">
                        <div class="pli-name-cell">
                            <div class="pli-icon pli-icon--indigo">
                                <span style="font-size:0.85rem; font-weight:700;">{{ strtoupper(substr($record->staff->name ?? '?', 0, 2)) }}</span>
                            </div>
                            <div class="pli-name-stack">
                                <span class="pli-title">{{ $record->staff->name ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Working Days --}}
                    <div class="pli-col col-center">
                        <span class="att-badge att-badge--total">
                            {{ $record->total_working_days }}
                        </span>
                    </div>

                    {{-- Present Days --}}
                    <div class="pli-col col-center">
                        <span class="att-badge att-badge--present">
                            {{ $record->present_days }}
                        </span>
                    </div>

                    {{-- Absent Days --}}
                    <div class="pli-col col-center">
                        <span class="att-badge att-badge--absent">
                            {{ $record->absent_days }}
                        </span>
                    </div>

                    <div class="pli-col pli-col-actions col-actions actions-cell col-center">
                        {{-- Mobile Dropdown --}}
                        <div class="dropdown pli-dots-dropdown d-md-none">
                            <button class="pli-btn-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end pli-action-menu">
                                <li>
                                    <button type="button" class="dropdown-item pli-menu-item"
                                        onclick="openViewAttendanceModal({{ $record->id }})">
                                        <span class="pli-menu-icon pli-menu-icon--view"><i class="bi bi-eye"></i></span>
                                        <span>View</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item pli-menu-item"
                                        data-bs-toggle="modal" data-bs-target="#attendanceModal"
                                        onclick='openEditAttendanceModal(@json($record))'>
                                        <span class="pli-menu-icon pli-menu-icon--edit"><i class="bi bi-pencil"></i></span>
                                        <span>Edit</span>
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <button type="button" class="dropdown-item pli-menu-item pli-menu-item--danger"
                                        onclick="openDeleteAttendanceModal({{ $record->id }}, '{{ $record->staff->name ?? '' }}')">
                                        <span class="pli-menu-icon pli-menu-icon--delete"><i class="bi bi-trash3"></i></span>
                                        <span>Delete</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        {{-- Desktop: 3-dot action popover (same pattern as Job Cards) --}}
                        <div class="pli-action-menu-wrap pli-action-buttons-desktop">
                            <button
                                type="button"
                                class="pli-action-dots"
                                aria-label="Attendance actions"
                                aria-expanded="false"
                                onclick="togglePliActions(this)"
                            >
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>

                            <div class="pli-action-popover">
                                <button
                                    type="button"
                                    class="pli-popover-action"
                                    onclick="openViewAttendanceModal({{ $record->id }}); closePliActions(this)"
                                >
                                    <span class="pli-popover-icon pli-popover-icon--view">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                    <span>View Attendance</span>
                                </button>

                                <button
                                    type="button"
                                    class="pli-popover-action"
                                    data-bs-toggle="modal"
                                    data-bs-target="#attendanceModal"
                                    onclick='openEditAttendanceModal(@json($record)); closePliActions(this)'
                                >
                                    <span class="pli-popover-icon pli-popover-icon--edit">
                                        <i class="bi bi-pencil"></i>
                                    </span>
                                    <span>Edit Attendance</span>
                                </button>

                                <div class="pli-popover-divider"></div>

                                <button
                                    type="button"
                                    class="pli-popover-action pli-popover-action--danger"
                                    onclick="openDeleteAttendanceModal({{ $record->id }}, '{{ $record->staff->name ?? '' }}'); closePliActions(this)"
                                >
                                    <span class="pli-popover-icon pli-popover-icon--delete">
                                        <i class="bi bi-trash3"></i>
                                    </span>
                                    <span>Delete Attendance</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($attendances->hasPages())
            <div class="content-card-footer">
                @include('partials.pagination-bar', ['paginator' => $attendances])
            </div>
        @endif

        @else
        <div class="empty-state">
            <div style="text-align:center; padding: 60px 20px;">
                <div style="width:72px;height:72px;border-radius:20px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:30px;color:#7C3AED;">
                    <i class="bi bi-calendar2-check"></i>
                </div>
                <h4 style="font-size:1.05rem;font-weight:700;color:#1E293B;margin-bottom:6px;">No Attendance Records</h4>
                <p style="color:#64748B;font-size:0.88rem;margin-bottom:20px;">No records found for {{ $monthName }} {{ $currentYear }}. Click below to add attendance.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#attendanceModal" onclick="openAddAttendanceModal()">
                    <i class="bi bi-plus-lg me-1"></i> Add Attendance
                </button>
            </div>
        </div>
        @endif
    </div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- FILTER MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <div class="att-modal-header">
                <div class="att-modal-icon" style="background:#EEF2FF; color:#4F46E5;">
                    <i class="bi bi-funnel"></i>
                </div>
                <div>
                    <h5 class="att-modal-title">Filter Attendance</h5>
                    <p class="att-modal-subtitle">Filter records by status</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding: 24px;">
                <label style="display:block; font-size:0.72rem; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:.06em; margin-bottom:10px;">
                    Status
                </label>
                <div class="att-filter-options" id="filterStatusOptions">
                    <button type="button" class="att-filter-option active" data-value="">
                        <span class="att-filter-option-dot" style="background:#94A3B8;"></span>
                        <span>All Records</span>
                        <i class="bi bi-check-lg att-filter-option-check"></i>
                    </button>
                    <button type="button" class="att-filter-option" data-value="present">
                        <span class="att-filter-option-dot" style="background:#22C55E;"></span>
                        <span>Present</span>
                        <i class="bi bi-check-lg att-filter-option-check"></i>
                    </button>
                    <button type="button" class="att-filter-option" data-value="absent">
                        <span class="att-filter-option-dot" style="background:#EF4444;"></span>
                        <span>Absent</span>
                        <i class="bi bi-check-lg att-filter-option-check"></i>
                    </button>
                </div>
            </div>

            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #F1F5F9; background:#FAFBFF;">
                <button type="button" class="btn btn-light" id="filterResetBtn" style="border-radius:10px; padding:9px 20px; font-weight:600;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
                <button type="button" class="btn btn-primary" id="filterApplyBtn"
                        style="border-radius:10px; padding:9px 22px; font-weight:700; background:linear-gradient(135deg,#6366f1,#4f46e5); border:none; box-shadow:0 3px 10px rgba(99,102,241,.35);">
                    <i class="bi bi-check2 me-1"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- ADD / EDIT ATTENDANCE MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="attendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <form id="attendanceForm" method="POST" action="{{ route('attendance.store') }}">
                @csrf
                <input type="hidden" name="_method" id="attendanceFormMethod" value="POST">
                <input type="hidden" name="attendance_id" id="attendanceId" value="">

                {{-- Modal Header --}}
                <div class="att-modal-header">
                    <div class="att-modal-icon">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div>
                        <h5 class="att-modal-title" id="attendanceModalTitle">Add Attendance</h5>
                        <p class="att-modal-subtitle" id="attendanceModalSubtitle">Record staff attendance details for a selected month</p>
                    </div>
                    <button type="button" class="att-modal-close ms-auto" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body" style="padding: 24px;">

                    {{-- Row 1: Staff Member --}}
                    <div class="att-form-field mb-4">
                        <label>Staff Member <span style="color:#EF4444;">*</span></label>
                        <div class="att-icon-field">
                            <span class="att-icon-field-icon"><i class="bi bi-person"></i></span>
                            <select name="staff_id" id="att_staff_id" required>
                                <option value="">Select staff member</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down att-icon-field-chevron"></i>
                        </div>
                    </div>

                    {{-- Row 2: Month + Year --}}
                    <div class="att-form-grid mb-4" style="grid-template-columns: 1fr 1fr;">
                        <div class="att-form-field">
                            <label>Month <span style="color:#EF4444;">*</span></label>
                            <div class="att-icon-field">
                                <span class="att-icon-field-icon"><i class="bi bi-calendar3"></i></span>
                                <select name="month" id="att_month" required onchange="handleMonthYearChange()">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down att-icon-field-chevron"></i>
                            </div>
                        </div>
                        <div class="att-form-field">
                            <label>Year <span style="color:#EF4444;">*</span></label>
                            <div class="att-icon-field">
                                <span class="att-icon-field-icon"><i class="bi bi-calendar3"></i></span>
                                <select name="year" id="att_year" required onchange="handleMonthYearChange()">
                                    @foreach($availableYears as $yr)
                                        <option value="{{ $yr }}" {{ $currentYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down att-icon-field-chevron"></i>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#F1F5F9; margin: 0 0 20px;">

                    <!-- {{-- Working Days (read-only, auto-calculated — never submitted) --}}
                    <div class="att-form-field mb-4">

                        <label>
                            Total Working Days
                        </label>

                        <div class="att-days-field">

                            <span class="att-days-field-icon">
                                <i class="bi bi-calendar3"></i>
                            </span>

                            <input
                                type="text"
                                id="att_total_days"
                                value="{{ $totalDaysInMonth ?? 0 }}"
                                readonly
                            >

                            <span class="att-days-field-suffix">
                                days
                            </span>

                        </div>

                        <p class="att-field-help">
                            Automatically calculated based on the selected month and year.
                        </p>

                    </div> -->

                    {{-- Present / Absent Days --}}
                    <div class="att-form-grid mb-4" style="grid-template-columns: 1fr 1fr;">
                        <div class="att-form-field">
                            <label>Present Days <span style="color:#EF4444;">*</span></label>
                            <div class="att-days-field att-days-field--present">
                                <span class="att-days-field-icon"><i class="bi bi-person-check"></i></span>
                                <input
                                    type="number"
                                    name="present_days"
                                    id="att_present_days"
                                    min="0"
                                    max="{{ $totalDaysInMonth ?? 31 }}"
                                    value="0"
                                    required
                                    oninput="updateAttendanceSummary()"
                                >
                                <span class="att-days-field-suffix">days</span>
                            </div>
                        </div>
                        <div class="att-form-field">
                            <label>Absent Days <span style="color:#EF4444;">*</span></label>
                            <div class="att-days-field att-days-field--absent">
                                <span class="att-days-field-icon"><i class="bi bi-person-x"></i></span>
                                <input
                                    type="number"
                                    name="absent_days"
                                    id="att_absent_days"
                                    min="0"
                                    max="{{ $totalDaysInMonth ?? 31 }}"
                                    value="0"
                                    required
                                    oninput="updateAttendanceSummary()"
                                >
                                <span class="att-days-field-suffix">days</span>
                            </div>
                        </div>
                    </div>

                   

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #F1F5F9; background:#FAFBFF;">
                    <button type="button" class="att-btn-cancel" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                    <button type="submit" class="att-btn-save" id="attendanceSubmitBtn">
                        <i class="bi bi-save2"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- VIEW ATTENDANCE MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="viewAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <div class="att-modal-header">
                <div class="att-modal-icon" style="background:#DCFCE7; color:#15803D;">
                    <i class="bi bi-calendar2-check"></i>
                </div>
                <div>
                    <h5 class="att-modal-title" id="viewAttStaffName">Attendance Details</h5>
                    <p class="att-modal-subtitle" id="viewAttMonth">—</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="att-stat-inline">
                            <div class="att-stat-inline-icon" style="background:#EDE9FE; color:#7C3AED;"><i class="bi bi-calendar3"></i></div>
                            <div>
                                <div style="font-size:0.7rem; color:#64748B; font-weight:600;">Working Days</div>
                                <div style="font-size:1.2rem; font-weight:800; color:#1E293B;" id="viewTotalDays">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="att-stat-inline">
                            <div class="att-stat-inline-icon" style="background:#DCFCE7; color:#15803D;"><i class="bi bi-person-check"></i></div>
                            <div>
                                <div style="font-size:0.7rem; color:#64748B; font-weight:600;">Present</div>
                                <div style="font-size:1.2rem; font-weight:800; color:#15803D;" id="viewPresentDays">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="att-stat-inline">
                            <div class="att-stat-inline-icon" style="background:#FEE2E2; color:#DC2626;"><i class="bi bi-person-x"></i></div>
                            <div>
                                <div style="font-size:0.7rem; color:#64748B; font-weight:600;">Absent</div>
                                <div style="font-size:1.2rem; font-weight:800; color:#DC2626;" id="viewAbsentDays">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="att-stat-inline">
                            <div class="att-stat-inline-icon" style="background:#FEF3C7; color:#D97706;"><i class="bi bi-calendar-x"></i></div>
                            <div>
                                <div style="font-size:0.7rem; color:#64748B; font-weight:600;">Remaining</div>
                                <div style="font-size:1.2rem; font-weight:800; color:#D97706;" id="viewRemainingDays">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" id="viewNotesWrap" style="display:none;">
                        <div class="p-3 rounded-3" style="background:#F8FAFC; border:1.5px solid #E4EBFB;">
                            <div style="font-size:0.72rem; color:#64748B; font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px;">Notes</div>
                            <div style="font-size:0.88rem; color:#1E293B;" id="viewNotes"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════ --}}
{{-- DELETE CONFIRM MODAL --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="modal fade premium-modal" id="deleteAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="confirm-modal-body">
                <div class="confirm-icon danger">
                    @include('partials.action-icons', ['type' => 'delete'])
                </div>
                <h5 class="confirm-title">Delete Attendance?</h5>
                <p class="confirm-message" id="deleteAttMessage">This action cannot be undone.</p>
                <div class="confirm-actions">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAttBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('js/pli-action-popover.js') }}"></script>
<script>
    let deleteAttId = null;
    let selectedStatusFilter = '';

    /* ─── Helpers ─── */
    const months = ['', 'January','February','March','April','May','June',
                    'July','August','September','October','November','December'];

    /* Number of calendar days in a given month/year (handles leap years). */
    function daysInMonth(month, year) {
        month = parseInt(month, 10);
        year = parseInt(year, 10);
        if (!month || !year) return 30;
        return new Date(year, month, 0).getDate();
    }

    /* Recalculates the read-only "Total Working Days" field from the
       currently selected Month + Year, and keeps the Present/Absent
       inputs' max attributes in sync with it. */
    function calculateTotalDays() {
        const monthEl = document.getElementById('att_month');
        const yearEl = document.getElementById('att_year');
        const totalEl = document.getElementById('att_total_days');
        if (!monthEl || !yearEl || !totalEl) return 0;

        const total = daysInMonth(monthEl.value, yearEl.value);
        totalEl.value = total;

        const presentEl = document.getElementById('att_present_days');
        const absentEl = document.getElementById('att_absent_days');
        if (presentEl) presentEl.setAttribute('max', total);
        if (absentEl) absentEl.setAttribute('max', total);

        return total;
    }

    /* Recomputes the live summary card (Total / Present / Absent / Remaining),
       flags an error state when present + absent exceeds total, and
       disables the Save button while invalid. Returns true when valid. */
    function updateAttendanceSummary() {
        const totalEl = document.getElementById('att_total_days');
        const presentEl = document.getElementById('att_present_days');
        const absentEl = document.getElementById('att_absent_days');
        if (!totalEl || !presentEl || !absentEl) return true;

        const total = parseInt(totalEl.value, 10) || 0;
        let present = parseInt(presentEl.value, 10);
        let absent = parseInt(absentEl.value, 10);
        present = isNaN(present) ? 0 : Math.max(0, present);
        absent = isNaN(absent) ? 0 : Math.max(0, absent);

        const remaining = total - present - absent;
        const isInvalid = (present + absent) > total;

        const sumTotal = document.getElementById('att_summary_total');
        const sumPresent = document.getElementById('att_summary_present');
        const sumAbsent = document.getElementById('att_summary_absent');
        const sumRemaining = document.getElementById('att_summary_remaining');
        if (sumTotal) sumTotal.textContent = total;
        if (sumPresent) sumPresent.textContent = present;
        if (sumAbsent) sumAbsent.textContent = absent;
        if (sumRemaining) {
            sumRemaining.textContent = remaining;
            sumRemaining.style.color = isInvalid ? '#DC2626' : '';
        }

        const errorBox = document.getElementById('att_summary_error');
        if (errorBox) errorBox.style.display = isInvalid ? '' : 'none';

        const submitBtn = document.getElementById('attendanceSubmitBtn');
        if (submitBtn) submitBtn.disabled = isInvalid;

        return !isInvalid;
    }

    /* Fired when Month or Year changes inside the modal. */
    function handleMonthYearChange() {
        calculateTotalDays();
        updateAttendanceSummary();
    }

    /* ─── Open Add Modal ─── */
    function openAddAttendanceModal() {
        const form = document.getElementById('attendanceForm');
        form.reset();
        form.action = "{{ route('attendance.store') }}";
        document.getElementById('attendanceFormMethod').value = 'POST';
        document.getElementById('attendanceId').value = '';
        document.getElementById('attendanceModalTitle').textContent = 'Add Attendance';
        document.getElementById('attendanceModalSubtitle').textContent = 'Record staff attendance details for a selected month';
        document.getElementById('attendanceSubmitBtn').innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Attendance';

        // Pre-select the page's currently selected month/year
        document.getElementById('att_month').value = '{{ $currentMonth }}';
        document.getElementById('att_year').value = '{{ $currentYear }}';

        // Staff field must be enabled for adding a new record
        document.getElementById('att_staff_id').disabled = false;

        document.getElementById('att_present_days').value = 0;
        document.getElementById('att_absent_days').value = 0;

        calculateTotalDays();
        updateAttendanceSummary();
    }

    /* ─── Open Edit Modal ─── */
    function openEditAttendanceModal(record) {
        document.getElementById('attendanceForm').action = `/attendance/${record.id}`;
        document.getElementById('attendanceFormMethod').value = 'PUT';
        document.getElementById('attendanceId').value = record.id;
        document.getElementById('attendanceModalTitle').textContent = 'Edit Attendance';
        document.getElementById('attendanceModalSubtitle').textContent = `Editing record for ${months[record.month]} ${record.year}`;
        document.getElementById('attendanceSubmitBtn').innerHTML = '<i class="bi bi-check2-circle me-1"></i> Update Attendance';

        document.getElementById('att_staff_id').value = record.staff_id;
        document.getElementById('att_month').value = record.month;
        document.getElementById('att_year').value = record.year;
        document.getElementById('att_present_days').value = record.present_days ?? 0;
        document.getElementById('att_absent_days').value = record.absent_days ?? 0;

        // Lock staff when editing (still submitted — see submit handler below)
        document.getElementById('att_staff_id').disabled = true;

        // total_working_days is never loaded into an editable field — it is
        // always recalculated from month/year, matching the controller.
        calculateTotalDays();
        updateAttendanceSummary();
    }

    // Ensure disabled staff field still submits, and block submit while invalid
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        document.getElementById('att_staff_id').disabled = false;
        if (!updateAttendanceSummary()) {
            e.preventDefault();
        }
    });

    /* ─── Open View Modal ─── */
    async function openViewAttendanceModal(id) {
        const response = await fetch(`/attendance/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) return;
        const r = await response.json();

        const total = r.total_working_days || 0;
        const present = r.present_days || 0;
        const absent = r.absent_days || 0;
        const remaining = total - present - absent;

        document.getElementById('viewAttStaffName').textContent = r.staff?.name || '—';
        document.getElementById('viewAttMonth').textContent = `${months[r.month]} ${r.year}`;
        document.getElementById('viewTotalDays').textContent = total;
        document.getElementById('viewPresentDays').textContent = present;
        document.getElementById('viewAbsentDays').textContent = absent;
        document.getElementById('viewRemainingDays').textContent = remaining;

        const notesWrap = document.getElementById('viewNotesWrap');
        if (r.notes) {
            document.getElementById('viewNotes').textContent = r.notes;
            notesWrap.style.display = '';
        } else {
            notesWrap.style.display = 'none';
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('viewAttendanceModal')).show();
    }

    /* ─── Open Delete Modal ─── */
    function openDeleteAttendanceModal(id, name) {
        deleteAttId = id;
        document.getElementById('deleteAttMessage').textContent = `Are you sure you want to delete ${name}'s attendance record?`;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteAttendanceModal')).show();
    }

    /* ─── Confirm Delete ─── */
    document.getElementById('confirmDeleteAttBtn').addEventListener('click', async function() {
        if (!deleteAttId) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

        try {
            const response = await fetch(`/attendance/${deleteAttId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Failed to delete.');

            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAttendanceModal'));
            if (modal) modal.hide();

            const row = document.getElementById(`att-row-${deleteAttId}`);
            if (row) {
                row.style.transition = 'opacity .2s ease, transform .2s ease';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => { row.remove(); }, 200);
            }

            if (window.showToast) window.showToast(data.message || 'Record deleted.', 'success');
        } catch (e) {
            if (window.showToast) window.showToast(e.message, 'danger');
        } finally {
            deleteAttId = null;
            btn.disabled = false;
            btn.textContent = 'Delete';
        }
    });

    /* ─── Filter Modal Logic ─── */
    document.querySelectorAll('.att-filter-option').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.att-filter-option').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedStatusFilter = this.dataset.value;
        });
    });

    document.getElementById('filterResetBtn').addEventListener('click', function() {
        selectedStatusFilter = '';
        document.querySelectorAll('.att-filter-option').forEach(b => b.classList.remove('active'));
        const defaultOption = document.querySelector('.att-filter-option[data-value=""]');
        if (defaultOption) defaultOption.classList.add('active');
    });

    document.getElementById('filterApplyBtn').addEventListener('click', function() {
        const params = new URLSearchParams(window.location.search);
        if (selectedStatusFilter) {
            params.set('status_filter', selectedStatusFilter);
        } else {
            params.delete('status_filter');
        }
        params.set('page', 1);
        window.location.href = "{{ route('attendance.index') }}?" + params.toString();
    });

    /* ─── Sidebar Submenu Toggle ─── */
    document.querySelectorAll('.sidebar-submenu-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const li = this.closest('.sidebar-has-submenu');
            const submenu = li.querySelector('.sidebar-submenu');
            const isOpen = li.classList.contains('submenu-open');

            // Close others
            document.querySelectorAll('.sidebar-has-submenu.submenu-open').forEach(el => {
                if (el !== li) {
                    el.classList.remove('submenu-open');
                    el.querySelector('.sidebar-submenu').classList.remove('show');
                    el.querySelector('.sidebar-submenu-toggle').setAttribute('aria-expanded', 'false');
                }
            });

            li.classList.toggle('submenu-open', !isOpen);
            submenu.classList.toggle('show', !isOpen);
            this.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    /* ─── Month nav buttons ─── */
    document.getElementById('prevMonthBtn').addEventListener('click', function() {
        let m = parseInt(document.getElementById('monthSelect').value);
        let y = parseInt(document.getElementById('yearSelect').value);
        if (m === 1) { m = 12; y--; } else { m--; }
        document.getElementById('monthSelect').value = m;
        // Try to set year
        const opt = [...document.getElementById('yearSelect').options].find(o => parseInt(o.value) === y);
        if (opt) document.getElementById('yearSelect').value = y;
        document.getElementById('monthFilterForm').submit();
    });

    document.getElementById('nextMonthBtn').addEventListener('click', function() {
        let m = parseInt(document.getElementById('monthSelect').value);
        let y = parseInt(document.getElementById('yearSelect').value);
        if (m === 12) { m = 1; y++; } else { m++; }
        document.getElementById('monthSelect').value = m;
        const opt = [...document.getElementById('yearSelect').options].find(o => parseInt(o.value) === y);
        if (opt) document.getElementById('yearSelect').value = y;
        document.getElementById('monthFilterForm').submit();
    });

    // Reset staff disabled state when modal hides
    document.getElementById('attendanceModal').addEventListener('hide.bs.modal', function() {
        document.getElementById('att_staff_id').disabled = false;
    });
</script>
@endpush

@endsection