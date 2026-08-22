@extends('layouts.app')

@section('title', 'Complaints')
@section('page-title', 'Complaints')

@push('styles')
    <style>

        /* ============================================================
           STAT CARDS (top row)
           ============================================================ */

        .complaint-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .complaint-stat-card {
            position: relative;
            background: #FFFFFF;
            border: 1px solid #E8EDF3;
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .complaint-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .complaint-stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #fff;
        }

        .complaint-stat-icon--indigo { background: linear-gradient(135deg, #818CF8, #6366F1); }
        .complaint-stat-icon--orange { background: linear-gradient(135deg, #FBBF24, #F59E0B); }
        .complaint-stat-icon--green  { background: linear-gradient(135deg, #4ADE80, #22C55E); }
        .complaint-stat-icon--amber  { background: linear-gradient(135deg, #FB923C, #F97316); }

        .complaint-stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .complaint-stat-dot--indigo { background: #6366F1; }
        .complaint-stat-dot--orange { background: #F59E0B; }
        .complaint-stat-dot--green  { background: #22C55E; }
        .complaint-stat-dot--amber  { background: #F97316; }

        .complaint-stat-label {
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #94A3B8;
            margin-bottom: 6px;
        }

        .complaint-stat-value {
            font-size: 1.7rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1;
            margin-bottom: 6px;
        }

        .complaint-stat-sub {
            font-size: 0.78rem;
            color: #94A3B8;
            font-weight: 500;
        }

        .complaint-stat-spark {
            position: absolute;
            right: 14px;
            bottom: 14px;
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 26px;
            opacity: 0.55;
        }

        .complaint-stat-spark span {
            display: inline-block;
            width: 4px;
            border-radius: 2px;
        }

        .complaint-stat-spark--indigo span { background: #A5B4FC; }
        .complaint-stat-spark--orange span { background: #FCD34D; }
        .complaint-stat-spark--green span  { background: #86EFAC; }
        .complaint-stat-spark--amber span  { background: #FDBA74; }

        @media (max-width: 992px) {
            .complaint-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .complaint-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ============================================================
           COMPLAINT PAGE — premium list styling (mirrors Job Card UI)
           ============================================================ */

        .complaint-page .content-card {
            background: #FFFFFF;
            border: 1px solid #E8EDF3;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .complaint-page .content-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid #E8EDF3;
        }

        .complaint-page .content-card-header h2 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
        }

        .complaint-page .content-card-header span {
            font-size: 0.8rem;
            color: #94A3B8;
            font-weight: 600;
        }

        .complaint-page .mgmt-top-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .complaint-page .mgmt-top-actions .mgmt-title h4 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 2px 0;
        }

        .complaint-page .mgmt-top-actions .mgmt-title small {
            color: #94A3B8;
            font-weight: 500;
        }

        .complaint-page .btn-add-complaint {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 11px;
            border: none;
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.25);
            transition: transform .16s ease, box-shadow .16s ease;
        }

        .complaint-page .btn-add-complaint:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(99, 102, 241, 0.32);
            color: #fff;
        }

        /* ---- Search box (header) ---- */

        .complaint-search {
            margin: 0;
        }

        .complaint-search .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 240px;
            height: 40px;
            padding: 0 14px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            background: #F8FAFC;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        .complaint-search .search-box:focus-within {
            border-color: #8B5CF6;
            background: #fff;
            box-shadow: 0 0 0 3.5px rgba(139, 92, 246, 0.12);
        }

        .complaint-search .search-box i.bi-search {
            color: #94A3B8;
            font-size: 0.9rem;
        }

        .complaint-search .search-box input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.85rem;
            color: #1E293B;
            font-weight: 500;
        }

        .complaint-search .search-box a {
            color: #94A3B8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ---- Grid list ---- */

        .complaint-list {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .complaint-list-head,
        .complaint-list-item {
            display: grid;
            grid-template-columns:
                44px
                minmax(170px, 1.1fr)
                minmax(140px, 0.95fr)
                minmax(180px, 1.2fr)
                minmax(180px, 1.2fr)
                120px
                130px
                90px;
            align-items: center;
            column-gap: 12px;
            padding: 0 20px;
        }

        .complaint-list-head {
            background: linear-gradient(135deg, #F5F3FF 0%, #EEF2FF 100%);
            border-bottom: 1px solid #E8EDF3;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .complaint-head-cell {
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748B;
        }

        .complaint-head-cell.col-center { text-align: center; }
        .complaint-head-cell.col-left { text-align: left; }

        .complaint-list-item {
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #F1F5F9;
            transition: transform .16s ease, box-shadow .16s ease, background-color .16s ease;
            position: relative;
        }

        .complaint-list-item:last-child {
            border-bottom: none;
        }

        .complaint-list-item:hover {
            transform: translateY(-1px);
            background-color: #FAFAFF;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.08);
            z-index: 1;
        }

        .complaint-list-item.action-menu-row-open {
            z-index: 40;
        }

        .complaint-col-num {
            font-size: 0.75rem;
            font-weight: 700;
            color: #94A3B8;
            text-align: center;
        }

        /* Staff cell */
        .complaint-staff-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .complaint-staff-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #818CF8 0%, #6366F1 60%, #7C3AED 100%);
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
        }

        .complaint-staff-info {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }

        .complaint-staff-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .complaint-staff-role {
            font-size: 0.72rem;
            font-weight: 500;
            color: #94A3B8;
        }

        /* Complaint type pill — dot marker + text, matching the reference design */
        .complaint-col-type {
            text-align: center;
        }

        .complaint-type-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid transparent;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
        }

        .complaint-type-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        /* Customer Behavior */
        .complaint-type-customer {
            color: #4F46E5;
            background: #EEF2FF;
        }

        /* Service Issue */
        .complaint-type-service {
            color: #C2410C;
            background: #FFF7ED;
        }

        /* Staff Behavior */
        .complaint-type-staff {
            color: #7C3AED;
            background: #FAF5FF;
        }

        /* Product Issue */
        .complaint-type-product {
            color: #BE185D;
            background: #FDF2F8;
        }

        /* Appointment Issue */
        .complaint-type-appointment {
            color: #0891B2;
            background: #ECFEFF;
        }

        /* Payment Issue */
        .complaint-type-payment {
            color: #6D28D9;
            background: #F5F3FF;
        }

        /* Fallback */
        .complaint-type-default {
            color: #64748B;
            background: #F8FAFC;
        }

        /* Reason / Action taken */
        .complaint-col-reason,
        .complaint-col-action {
            text-align: center;
            min-width: 0;
        }

        .complaint-text-ellipsis {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        /* Compensation (optional field) */
        .complaint-col-compensation {
            text-align: center;
        }

        .complaint-compensation-val {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #1E293B;
        }

        .complaint-compensation-tag {
            font-size: 0.68rem;
            font-weight: 600;
            color: #16A34A;
            margin-top: 1px;
        }

        /* Date */
        .complaint-col-date {
            text-align: center;
        }

        .complaint-date-val {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
        }

        .complaint-date-val i {
            font-size: 0.8rem;
            color: #94A3B8;
        }

        /* Actions */
        .complaint-col-actions {
            text-align: center;
            position: relative;
        }

        .complaint-action-menu-wrap {
            position: relative;
            display: inline-flex;
        }

        .complaint-action-dots {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            border: 1px solid #E2E8F0;
            background: #fff;
            color: #64748B;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .16s ease;
        }

        .complaint-action-dots:hover,
        .complaint-action-dots.is-open {
            background: #F5F3FF;
            border-color: #C4B5FD;
            color: #6D28D9;
        }

        .complaint-action-popover {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 190px;
            background: #fff;
            border: 1px solid #E8EDF3;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
            padding: 6px;
            display: none;
            flex-direction: column;
            gap: 2px;
            z-index: 50;
        }

        .complaint-action-menu-wrap.is-open .complaint-action-popover {
            display: flex;
        }

        .complaint-popover-action {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            border: none;
            background: transparent;
            padding: 8px 10px;
            border-radius: 9px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            text-align: left;
            transition: background-color .14s ease;
        }

        .complaint-popover-action:hover {
            background: #F8FAFC;
        }

        .complaint-popover-icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .complaint-popover-icon--edit {
            background: #EDE9FE;
            color: #6D28D9;
        }

        .complaint-popover-icon--delete {
            background: #FEE2E2;
            color: #DC2626;
        }

        .complaint-popover-action--danger {
            color: #DC2626;
        }

        .complaint-popover-divider {
            height: 1px;
            background: #F1F5F9;
            margin: 4px 2px;
        }

        .complaint-delete-form {
            margin: 0;
        }

        /* Empty state (mirrors Job Card empty state) */
        .complaint-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
        }

        .complaint-empty-state-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: #F5F3FF;
            color: #8B5CF6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 16px;
        }

        .complaint-empty-state h3 {
            font-size: 1rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 4px;
        }

        .complaint-empty-state p {
            font-size: 0.85rem;
            color: #94A3B8;
            margin-bottom: 16px;
        }

        /* Mobile / responsive */
        @media (max-width: 992px) {
            .complaint-list-head,
            .complaint-list-item {
                grid-template-columns:
                    36px
                    minmax(150px, 1.1fr)
                    minmax(130px, 0.95fr)
                    minmax(160px, 1.2fr)
                    minmax(160px, 1.2fr)
                    110px
                    120px
                    60px;
            }
        }

        @media (max-width: 768px) {
            .complaint-list-head {
                display: none;
            }

            .complaint-list-item {
                grid-template-columns: 1fr;
                row-gap: 8px;
                padding: 14px 16px;
            }

            .complaint-col-num { display: none; }

            .complaint-col-reason,
            .complaint-col-action,
            .complaint-col-compensation,
            .complaint-col-date,
            .complaint-col-type {
                text-align: left;
            }

            .complaint-text-ellipsis {
                white-space: normal;
            }

            .complaint-col-actions {
                text-align: right;
                position: absolute;
                top: 12px;
                right: 12px;
            }

            .complaint-action-dots-mobile {
                width: 30px;
                height: 30px;
                border-radius: 9px;
                border: 1px solid #E2E8F0;
                background: #fff;
                color: #64748B;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .complaint-action-menu-wrap.d-desktop-only {
                display: none !important;
            }
        }

        @media (min-width: 769px) {
            .complaint-dropdown-mobile {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')

    <div class="complaint-page management-page">
        <div class="mgmt-top-actions">
            
            <button type="button" class="btn-add-complaint" onclick="openComplaint()">
                <i class="bi bi-plus-lg"></i> Add Complaint
            </button>
        </div>

        {{-- Stat cards --}}
        <div class="complaint-stats-grid">

            <div class="complaint-stat-card">
                <div class="complaint-stat-top">
                    <div class="complaint-stat-icon complaint-stat-icon--indigo"><i class="bi bi-chat-square-text-fill"></i></div>
                    <span class="complaint-stat-dot complaint-stat-dot--indigo"></span>
                </div>
                <div class="complaint-stat-label">Total Complaints</div>
                <div class="complaint-stat-value">{{ $totalComplaintsCount }}</div>
                <div class="complaint-stat-sub">All complaint records</div>
                <div class="complaint-stat-spark complaint-stat-spark--indigo">
                    <span style="height:35%"></span><span style="height:55%"></span><span style="height:40%"></span>
                    <span style="height:70%"></span><span style="height:50%"></span><span style="height:85%"></span>
                </div>
            </div>

            <div class="complaint-stat-card">
                <div class="complaint-stat-top">
                    <div class="complaint-stat-icon complaint-stat-icon--orange"><i class="bi bi-hourglass-split"></i></div>
                    <span class="complaint-stat-dot complaint-stat-dot--orange"></span>
                </div>
                <div class="complaint-stat-label">Pending</div>
                <div class="complaint-stat-value">{{ $pendingComplaintsCount }}</div>
                <div class="complaint-stat-sub">Need attention</div>
                <div class="complaint-stat-spark complaint-stat-spark--orange">
                    <span style="height:60%"></span><span style="height:30%"></span><span style="height:50%"></span>
                    <span style="height:25%"></span><span style="height:45%"></span><span style="height:35%"></span>
                </div>
            </div>

            <div class="complaint-stat-card">
                <div class="complaint-stat-top">
                    <div class="complaint-stat-icon complaint-stat-icon--green"><i class="bi bi-check-circle-fill"></i></div>
                    <span class="complaint-stat-dot complaint-stat-dot--green"></span>
                </div>
                <div class="complaint-stat-label">Resolved</div>
                <div class="complaint-stat-value">{{ $resolvedComplaintsCount }}</div>
                <div class="complaint-stat-sub">Successfully handled</div>
                <div class="complaint-stat-spark complaint-stat-spark--green">
                    <span style="height:40%"></span><span style="height:65%"></span><span style="height:55%"></span>
                    <span style="height:80%"></span><span style="height:60%"></span><span style="height:90%"></span>
                </div>
            </div>

            <div class="complaint-stat-card">
                <div class="complaint-stat-top">
                    <div class="complaint-stat-icon complaint-stat-icon--amber"><i class="bi bi-wallet2"></i></div>
                    <span class="complaint-stat-dot complaint-stat-dot--amber"></span>
                </div>
                <div class="complaint-stat-label">Total Compensation</div>
                <div class="complaint-stat-value">₹{{ number_format($totalCompensationSum, 2) }}</div>
                <div class="complaint-stat-sub">Total compensation</div>
                <div class="complaint-stat-spark complaint-stat-spark--amber">
                    <span style="height:50%"></span><span style="height:35%"></span><span style="height:65%"></span>
                    <span style="height:45%"></span><span style="height:75%"></span><span style="height:55%"></span>
                </div>
            </div>

        </div>

        

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="content-card">

            <div class="content-card-header">
                <div>
                    <h2>Complaint List</h2>
                    <span>{{ $complaints->total() }} total complaints</span>
                </div>

                <form method="GET" action="{{ route('complaints.index') }}" class="complaint-search">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search complaints...">
                        @if($search)
                            <a href="{{ route('complaints.index') }}" title="Clear search">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($complaints->count())

                @php
                    $complaintListStart = ($complaints->currentPage() - 1) * $complaints->perPage();
                @endphp

                <div class="complaint-list">
                    <div class="complaint-list-head">
                        <span class="complaint-head-cell col-center">#</span>
                        <span class="complaint-head-cell col-left">Staff</span>
                        <span class="complaint-head-cell col-center">Complaint Type</span>
                        <span class="complaint-head-cell col-center">Reason</span>
                        <span class="complaint-head-cell col-center">Action Taken</span>
                        <span class="complaint-head-cell col-center">Compensation</span>
                        <span class="complaint-head-cell col-center">Date</span>
                        <span class="complaint-head-cell col-center">Actions</span>
                    </div>

                    @foreach($complaints as $complaint)
                        @php
                            $complaintTypeKey = strtolower(trim($complaint->complaint_type_text ?? ''));

                            $complaintTypeClass = match (true) {
                                str_contains($complaintTypeKey, 'customer') => 'complaint-type-customer',
                                str_contains($complaintTypeKey, 'service') => 'complaint-type-service',
                                str_contains($complaintTypeKey, 'staff') => 'complaint-type-staff',
                                str_contains($complaintTypeKey, 'product') => 'complaint-type-product',
                                str_contains($complaintTypeKey, 'appointment') => 'complaint-type-appointment',
                                str_contains($complaintTypeKey, 'payment') => 'complaint-type-payment',
                                default => 'complaint-type-default',
                            };

                            $complaintStaffName = $complaint->staff?->name ?? '—';
                            $complaintStaffInitial = $complaintStaffName !== '—' ? strtoupper(substr($complaintStaffName, 0, 1)) : '?';
                            $complaintStaffRole = $complaint->staff?->role ?? null;

                            $complaintCompensation = (float) ($complaint->compensation ?? 0);
                        @endphp

                        <article class="complaint-list-item" id="complaint-row-{{ $complaint->id }}">

                            <div class="complaint-col-num">{{ $complaintListStart + $loop->iteration }}</div>

                            <div class="complaint-col col-left">
                                <div class="complaint-staff-cell">
                                    <div class="complaint-staff-avatar">{{ $complaintStaffInitial }}</div>
                                    <div class="complaint-staff-info">
                                        <span class="complaint-staff-name">{{ $complaintStaffName }}</span>
                                        @if($complaintStaffRole)
                                            <span class="complaint-staff-role">{{ $complaintStaffRole }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="complaint-col complaint-col-type">
                                @if($complaint->complaint_type_text)
                                    <span class="complaint-type-pill {{ $complaintTypeClass }}">
                                        <span class="complaint-type-dot"></span>
                                        <span>{{ $complaint->complaint_type_text }}</span>
                                    </span>
                                @else
                                    <span class="complaint-text-ellipsis">—</span>
                                @endif
                            </div>

                            <div class="complaint-col complaint-col-reason">
                                <span class="complaint-text-ellipsis" title="{{ $complaint->reason }}">
                                    {{ $complaint->reason ?: '—' }}
                                </span>
                            </div>

                            <div class="complaint-col complaint-col-action">
                                <span class="complaint-text-ellipsis" title="{{ $complaint->action_taken }}">
                                    {{ $complaint->action_taken ?: '—' }}
                                </span>
                            </div>

                            {{-- Compensation is optional — falls back to ₹0.00 with no "Compensation" tag when empty --}}
                            <div class="complaint-col complaint-col-compensation">
                                <span class="complaint-compensation-val">₹{{ number_format($complaintCompensation, 2) }}</span>
                                @if($complaintCompensation > 0)
                                    <div class="complaint-compensation-tag">Compensation</div>
                                @endif
                            </div>

                            <div class="complaint-col complaint-col-date">
                                <span class="complaint-date-val">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $complaint->complaint_date?->format('d/m/Y') ?? '—' }}
                                </span>
                            </div>

                            <div class="complaint-col complaint-col-actions">

                                {{-- Mobile dropdown --}}
                                <div class="dropdown complaint-dropdown-mobile">
                                    <button class="complaint-action-dots-mobile" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button type="button" class="dropdown-item" onclick='openComplaint(@json($complaint))'>
                                                <i class="bi bi-pencil me-2"></i> Edit Complaint
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form method="POST" action="{{ route('complaints.destroy', $complaint) }}" class="complaint-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete complaint?')">
                                                    <i class="bi bi-trash3 me-2"></i> Delete Complaint
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Desktop popover --}}
                                <div class="complaint-action-menu-wrap d-desktop-only">
                                    <button
                                        type="button"
                                        class="complaint-action-dots"
                                        aria-label="Complaint actions"
                                        aria-expanded="false"
                                        onclick="toggleComplaintActions(this)"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <div class="complaint-action-popover">

                                        <button
                                            type="button"
                                            class="complaint-popover-action"
                                            onclick='openComplaint(@json($complaint)); closeComplaintActions(this)'
                                        >
                                            <span class="complaint-popover-icon complaint-popover-icon--edit">
                                                <i class="bi bi-pencil"></i>
                                            </span>
                                            <span>Edit Complaint</span>
                                        </button>

                                        <div class="complaint-popover-divider"></div>

                                        <form method="POST" action="{{ route('complaints.destroy', $complaint) }}" class="complaint-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="complaint-popover-action complaint-popover-action--danger"
                                                onclick="return confirm('Delete complaint?')"
                                            >
                                                <span class="complaint-popover-icon complaint-popover-icon--delete">
                                                    <i class="bi bi-trash3"></i>
                                                </span>
                                                <span>Delete Complaint</span>
                                            </button>
                                        </form>

                                    </div>
                                </div>

                            </div>

                        </article>
                    @endforeach
                </div>

                <div class="p-3">
                    {{ $complaints->links() }}
                </div>

            @else

                <div class="complaint-empty-state">
                    <div class="complaint-empty-state-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <h3>No complaints found</h3>
                    <p>{{ $search ? 'No complaints match your search.' : 'Staff complaint records will appear here.' }}</p>
                    <button type="button" class="btn-add-complaint" onclick="openComplaint()">
                        <i class="bi bi-plus-lg"></i> Add Complaint
                    </button>
                </div>

            @endif

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- ADD / EDIT COMPLAINT MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade" id="complaintModal">
        <div class="modal-dialog">
            <form id="complaintForm" class="modal-content" action="{{ route('complaints.store') }}" method="POST">
                @csrf
                <input type="hidden" id="complaintMethod" name="_method">
                <div class="modal-header">
                    <h5 id="complaintTitle">Add Complaint</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label>Staff</label>
                        <select id="complaintStaff" class="form-select" name="staff_id" required>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Complaint Type</label>
                        <input id="complaintType" class="form-control" name="complaint_type_text" required>
                    </div>
                    <div class="col-12">
                        <label>Reason of Complaint</label>
                        <textarea id="complaintReason" class="form-control" name="reason" required></textarea>
                    </div>
                    <div class="col-12">
                        <label>Action Taken</label>
                        <textarea id="complaintAction" class="form-control" name="action_taken" required></textarea>
                    </div>
                    <div class="col-6">
                        {{-- Compensation is optional: no "required" attribute, no asterisk --}}
                        <label>Compensation <small class="text-muted">(optional)</small></label>
                        <input id="complaintCompensation" class="form-control" type="number" step="0.01" min="0" name="compensation" placeholder="0.00">
                    </div>
                    <div class="col-6">
                        <label>Date</label>
                        <input id="complaintDate" class="form-control" type="date" name="complaint_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save Complaint</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const complaintModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('complaintModal'));

            function openComplaint(c = null) {
                closeAllComplaintActionMenus();

                complaintForm.reset();
                complaintMethod.value = c ? 'PUT' : '';
                complaintForm.action = c ? `/complaints/${c.id}` : `{{ route('complaints.store') }}`;
                complaintTitle.textContent = c ? 'Edit Complaint' : 'Add Complaint';
                complaintDate.value = c ? c.complaint_date : '{{ now()->toDateString() }}';

                if (c) {
                    complaintStaff.value = c.staff_id;
                    complaintType.value = c.complaint_type_text;
                    complaintReason.value = c.reason;
                    complaintAction.value = c.action_taken;
                    complaintCompensation.value = c.compensation || '';
                }

                complaintModal.show();
            }

            // ---------------------------------------------------------------
            // Three-dot action popover (desktop) — mirrors Job Card behavior
            // ---------------------------------------------------------------

            function closeAllComplaintActionMenus() {
                document.querySelectorAll('.complaint-action-menu-wrap.is-open').forEach(wrapper => {
                    wrapper.classList.remove('is-open');

                    const button = wrapper.querySelector('.complaint-action-dots');
                    if (button) {
                        button.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');
                    }

                    const row = wrapper.closest('.complaint-list-item');
                    if (row) {
                        row.classList.remove('action-menu-row-open');
                    }
                });
            }

            function toggleComplaintActions(button) {
                const wrapper = button.closest('.complaint-action-menu-wrap');
                const currentRow = button.closest('.complaint-list-item');

                document.querySelectorAll('.complaint-action-menu-wrap.is-open').forEach(menu => {
                    if (menu !== wrapper) {
                        menu.classList.remove('is-open');

                        const menuButton = menu.querySelector('.complaint-action-dots');
                        if (menuButton) {
                            menuButton.classList.remove('is-open');
                            menuButton.setAttribute('aria-expanded', 'false');
                        }

                        const row = menu.closest('.complaint-list-item');
                        if (row) {
                            row.classList.remove('action-menu-row-open');
                        }
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
                const wrapper = element.closest('.complaint-action-menu-wrap');
                if (!wrapper) return;

                wrapper.classList.remove('is-open');

                const button = wrapper.querySelector('.complaint-action-dots');
                if (button) {
                    button.classList.remove('is-open');
                    button.setAttribute('aria-expanded', 'false');
                }

                const row = wrapper.closest('.complaint-list-item');
                if (row) {
                    row.classList.remove('action-menu-row-open');
                }
            }

            // Close popover when clicking outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('.complaint-action-menu-wrap')) {
                    closeAllComplaintActionMenus();
                }
            });

            // Close popover on Escape
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAllComplaintActionMenus();
                }
            });
        </script>
    @endpush

@endsection