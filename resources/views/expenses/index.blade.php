@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Expenses')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/module-lists.css') }}">
    <style>
        /* ============================================================
           EXPENSE LIST TABLE — 8-column grid layout
           ============================================================ */
        .expense-page .premium-list--expenses {
            --expense-grid:
                44px
                130px
                minmax(120px, 0.95fr)
                minmax(140px, 1fr)
                minmax(170px, 1.3fr)
                115px
                145px
                70px;
        }

        .expense-page .premium-list--expenses .premium-list-head,
        .expense-page .premium-list--expenses .premium-list-item {
            grid-template-columns: var(--expense-grid) !important;
            min-width: 1040px !important;
        }

        .expense-page .premium-list--expenses .premium-list-head {
            gap: 12px !important;
        }

        .expense-page .premium-list--expenses .premium-list-item {
            gap: 12px !important;
            min-height: 66px;
        }

        .expense-page .expense-date-cell,
        .expense-page .expense-category-cell,
        .expense-page .expense-amount-cell,
        .expense-page .expense-payment-cell,
        .expense-page .expense-cell-center {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }

        .expense-page .expense-date-text {
            font-size: .82rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
        }

        .expense-page .expense-date-sub {
            font-size: .7rem;
            font-weight: 500;
            color: #94A3B8;
        }

        .expense-page .expense-staff-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .expense-page .expense-staff-avatar {
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

        .expense-page .expense-staff-name {
            font-size: .82rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .expense-page .expense-desc-text {
            display: block;
            max-width: 100%;
            font-size: .82rem;
            font-weight: 600;
            color: #1E293B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .expense-page .expense-desc-sub {
            font-size: .72rem;
            font-weight: 500;
            color: #94A3B8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .expense-page .expense-amount-val {
            font-size: .92rem;
            font-weight: 800;
            color: #0F172A;
            white-space: nowrap;
        }

        .expense-page .expense-category-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ------------------------------------------------------------
           PAYMENT METHOD PILLS (Table List View — same as Job Card / Purchase)
           ------------------------------------------------------------ */
        .expense-page .payment-type-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 32px;
            padding: 5px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-size: 0.76rem;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.65);
            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease;
        }

        .expense-page .payment-type-pill:hover {
            transform: translateY(-1px);
            box-shadow:
                0 4px 10px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .expense-page .payment-type-pill i {
            font-size: 0.88rem;
            line-height: 1;
        }

        /* UPI */
        .expense-page .payment-type-upi {
            color: #6366F1;
            background: linear-gradient(135deg, #F5F3FF 0%, #EDE9FE 100%);
            border-color: #DDD6FE;
        }

        /* Cash */
        .expense-page .payment-type-cash {
            color: #16A34A;
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            border-color: #BBF7D0;
        }

        /* Card */
        .expense-page .payment-type-card {
            color: #7C3AED;
            background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%);
            border-color: #E9D5FF;
        }

        /* EC / Wallet */
        .expense-page .payment-type-ec,
        .expense-page .payment-type-wallet {
            color: #0F172A;
            background: #FFFFFF;
            border-color: #E2E8F0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Bank */
        .expense-page .payment-type-bank,
        .expense-page .payment-type-bank-transfer {
            color: #2563EB;
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            border-color: #BFDBFE;
        }

        /* Net Banking */
        .expense-page .payment-type-net-banking {
            color: #0284C7;
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border-color: #BAE6FD;
        }

        /* Fallback / Other / Default */
        .expense-page .payment-type-default,
        .expense-page .payment-type-other {
            color: #475569;
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            border-color: #E2E8F0;
        }

        .expense-page .expense-list-actions,
        .expense-page .premium-list--expenses .expense-list-actions,
        .expense-page .premium-list--expenses .pli-col-actions {
            grid-column: 8 !important;
            grid-row: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: visible !important;
        }

        .expense-page .premium-list--expenses .premium-list-head .pli-head-cell:last-child {
            grid-column: 8 !important;
            grid-row: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Ensure 3-dot action popover opens downward and stays on top */
        .expense-page .content-card,
        .expense-page .premium-list,
        .expense-page .premium-list--expenses {
            overflow: visible !important;
        }

        .expense-page .premium-list-item {
            position: relative;
        }

        .expense-page .premium-list-item.action-menu-row-open {
            z-index: 100 !important;
        }

        .expense-page .pli-action-menu-wrap {
            position: relative;
        }

        .expense-page .pli-action-menu-wrap.is-open {
            z-index: 105 !important;
        }

        .expense-page .pli-action-popover,
        .expense-page .premium-list-item:last-child .pli-action-popover,
        .expense-page .premium-list-item.pli-open-upward .pli-action-popover,
        .expense-page .premium-list-item:last-child .pli-action-menu-wrap.is-open .pli-action-popover,
        .expense-page .premium-list-item.pli-open-upward .pli-action-menu-wrap.is-open .pli-action-popover {
            top: calc(100% + 6px) !important;
            bottom: auto !important;
            right: 0 !important;
            left: auto !important;
            z-index: 120 !important;
            transform-origin: top right !important;
            transform: translateY(0) scale(1) !important;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.15) !important;
        }

        /* Custom Category Dropdown in Modal */
        .custom-cat-selector {
            position: relative;
        }

        .custom-cat-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 9px 14px;
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #0F172A;
            transition: all 0.16s ease;
            text-align: left;
        }

        .custom-cat-btn:hover,
        .custom-cat-btn:focus,
        .custom-cat-btn.is-open {
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
            outline: none;
        }

        .custom-cat-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
            padding: 6px;
            z-index: 1060;
            display: none;
            max-height: 250px;
            overflow-y: auto;
        }

        .custom-cat-menu.show {
            display: block;
        }

        .custom-cat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            background: transparent;
            font-size: 0.84rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.14s ease;
            text-align: left;
        }

        .custom-cat-item:hover {
            background: #F5F3FF;
            color: #7C3AED;
        }

        .custom-cat-item.active {
            background: #EDE9FE;
            color: #7C3AED;
            font-weight: 700;
        }

        .custom-cat-add-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            background: #F8FAFC;
            font-size: 0.82rem;
            font-weight: 700;
            color: #7C3AED;
            transition: all 0.14s ease;
            margin-top: 4px;
        }

        .custom-cat-add-btn:hover {
            background: #EDE9FE;
        }

        .custom-cat-add-box {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px;
            background: #F8FAFC;
            border-radius: 8px;
            margin-top: 4px;
            border: 1px solid #E2E8F0;
        }

        .custom-cat-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 0.82rem;
            font-weight: 600;
            color: #0F172A;
            padding: 4px 8px;
            outline: none;
        }

        .custom-cat-save-btn {
            border: none;
            background: #7C3AED;
            color: #fff;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: all 0.14s ease;
        }

        .custom-cat-save-btn:hover {
            background: #6D28D9;
        }

        .custom-cat-cancel-btn {
            border: none;
            background: #E2E8F0;
            color: #64748B;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        /* Detail Modal */
        .expense-detail-quote {
            background: #F8FAFC;
            border-left: 3.5px solid #6366F1;
            border-radius: 0 10px 10px 0;
            padding: 12px 16px;
            margin-top: 14px;
        }

        .expense-detail-quote-label {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 4px;
        }

        .expense-detail-quote-text {
            font-size: .88rem;
            font-weight: 500;
            color: #1E293B;
            line-height: 1.5;
            white-space: pre-wrap;
        }
    </style>
@endpush

@section('content')
<div class="job-card-page expense-page management-page">
    @include('partials.mgmt-top-actions', [
        'addLabel' => 'Add Expense',
        'addModal' => '#expenseModal',
        'addOnclick' => 'openExpense()',
        'filterModule' => 'expenses',
        'filterRoute' => route('expenses.index'),
        'filterData' => ['categories' => $categories, 'staff' => $staff, 'paymentTypes' => $paymentTypes]
    ])

    {{-- Statistics Cards --}}
    <div class="mgmt-stats-grid mgmt-stats-grid--4">
        @include('partials.mgmt-stat-card', [
            'theme' => 'indigo',
            'icon' => 'rupee-green',
            'label' => 'This Month',
            'value' => '₹' . number_format($monthTotal, 2),
            'subtext' => 'Expenses this month',
            'sparkColor' => '#6366F1',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'blue',
            'icon' => 'clipboard-cyan',
            'label' => 'Total Expenses',
            'value' => '₹' . number_format($total, 2),
            'subtext' => 'All time expenses',
            'sparkColor' => '#3B82F6',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'green',
            'icon' => 'box-blue',
            'label' => 'Categories',
            'value' => $categoriesCount,
            'subtext' => 'Active categories',
            'sparkColor' => '#22C55E',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'calendar-orange',
            'label' => 'This Month Count',
            'value' => $monthCount,
            'subtext' => 'Total transactions',
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
                <h2>Expense List</h2>
                <span>{{ $expenses->total() }} total expenses</span>
            </div>
            <div class="content-card-header-actions">
                <form method="GET" action="{{ route('expenses.index') }}" class="job-card-search">
                    <input type="hidden" name="from" value="{{ request('from', '') }}"><input type="hidden" name="to" value="{{ request('to', '') }}"><input type="hidden" name="expense_category_id" value="{{ request('expense_category_id', '') }}"><input type="hidden" name="staff_id" value="{{ request('staff_id', '') }}"><input type="hidden" name="payment_method" value="{{ request('payment_method', '') }}">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search expenses...">
                        @if(request('search'))
                            <a href="{{ route('expenses.index') }}" class="text-muted" title="Clear search"><i class="bi bi-x"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($expenses->count())
            @php
                $listStart = ($expenses->currentPage() - 1) * $expenses->perPage();
                $badgePalette = [
                    ['bg' => '#F5F3FF', 'color' => '#6D28D9', 'border' => '#DDD6FE'],
                    ['bg' => '#FFF7ED', 'color' => '#C2410C', 'border' => '#FFEDD5'],
                    ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#DBEAFE'],
                    ['bg' => '#FDF2F8', 'color' => '#BE185D', 'border' => '#FCE7F3'],
                    ['bg' => '#F0FDF4', 'color' => '#15803D', 'border' => '#DCFCE7'],
                ];
                $payIcons = [
                    'Cash' => 'bi-cash',
                    'UPI' => 'bi-phone',
                    'Card' => 'bi-credit-card',
                    'Bank Transfer' => 'bi-bank',
                    'Other' => 'bi-wallet2',
                ];
            @endphp

            <div class="premium-list premium-list--expenses premium-list--feed premium-list--compact premium-list--mgmt">
                <div class="premium-list-head">
                    <span class="pli-head-cell col-center">#</span>
                    <span class="pli-head-cell col-center">Date</span>
                    <span class="pli-head-cell col-center">Category</span>
                    <span class="pli-head-cell col-left">Staff</span>
                    <span class="pli-head-cell col-left">Description</span>
                    <span class="pli-head-cell col-center">Amount</span>
                    <span class="pli-head-cell col-center">Payment</span>
                    <span class="pli-head-cell col-center">Actions</span>
                </div>

                @foreach($expenses as $expense)
                    @php
                        $catIdx = $expense->expense_category_id % count($badgePalette);
                        $catStyle = $badgePalette[$catIdx];
                        $staffName = $expense->staff?->name;
                        $staffInitial = $staffName ? strtoupper(substr($staffName, 0, 1)) : '';
                        $payMethod = $expense->payment_method ?? 'Cash';
                        $payIcon = $payIcons[$payMethod] ?? 'bi-wallet2';
                        $paymentKey = strtolower(str_replace(' ', '-', $payMethod));
                    @endphp

                    <article class="premium-list-item" id="expense-row-{{ $expense->id }}">
                        <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                        {{-- Date cell --}}
                        <div class="pli-col expense-cell-center">
                            <div class="text-center">
                                <span class="expense-date-text">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>{{ $expense->expense_date->format('d/m/Y') }}
                                </span>
                               
                            </div>
                        </div>

                        {{-- Category cell --}}
                        <div class="pli-col expense-cell-center">
                            <span class="expense-category-pill" style="background:{{ $catStyle['bg'] }};color:{{ $catStyle['color'] }};border:1px solid {{ $catStyle['border'] }};" title="{{ $expense->category?->name }}">
                                <i class="bi bi-tag-fill" style="font-size:0.7rem;"></i>{{ $expense->category?->name ?? '—' }}
                            </span>
                        </div>

                        {{-- Staff cell --}}
                        <div class="pli-col col-left">
                            @if($expense->staff)
                                <div class="expense-staff-cell">
                                    <span class="expense-staff-name" title="{{ $staffName }}">{{ $staffName }}</span>
                                </div>
                            @else
                                <span class="badge rounded-pill bg-light text-muted border" style="font-size:0.7rem;font-weight:600;">Not assigned</span>
                            @endif
                        </div>

                        {{-- Description cell --}}
                        <div class="pli-col col-left">
                            <div style="min-width:0;max-width:100%;">
                                <span class="expense-desc-text" title="{{ $expense->description }}">{{ $expense->description ?: '—' }}</span>
                                @if($expense->notes)
                                    <div class="expense-desc-sub" title="{{ $expense->notes }}">{{ $expense->notes }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Amount cell --}}
                        <div class="pli-col expense-cell-center">
                            <span class="expense-amount-val">₹{{ number_format($expense->amount, 2) }}</span>
                        </div>

                        {{-- Payment cell --}}
                        <div class="pli-col expense-cell-center">
                            <span class="payment-type-pill payment-type-{{ $paymentKey }}">
                                <i class="bi {{ $payIcon }}"></i>{{ $payMethod }}
                            </span>
                        </div>

                        {{-- Actions menu --}}
                        <div class="pli-col pli-col-actions expense-list-actions">
                            <div class="pli-action-menu-wrap">
                                <button type="button" class="pli-action-dots" aria-label="Expense actions" aria-expanded="false" onclick="toggleExpenseActions(this)">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="pli-action-popover">
                                    <button type="button" class="pli-popover-action" onclick='openExpenseDetailsModal(@json($expense)); closeExpenseActions(this)'>
                                        <span class="pli-popover-icon pli-popover-icon--view"><i class="bi bi-eye"></i></span>
                                        <span>View Details</span>
                                    </button>
                                    <button type="button" class="pli-popover-action" onclick='openExpense(@json($expense)); closeExpenseActions(this)'>
                                        <span class="pli-popover-icon pli-popover-icon--edit"><i class="bi bi-pencil"></i></span>
                                        <span>Edit Expense</span>
                                    </button>
                                    <div class="pli-popover-divider"></div>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="pli-popover-action pli-popover-action--danger">
                                            <span class="pli-popover-icon pli-popover-icon--delete"><i class="bi bi-trash3"></i></span>
                                            <span>Delete Expense</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @include('partials.pagination-bar', ['paginator' => $expenses])
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-wallet2"></i></div>
                <h3>No expenses found</h3>
                <p>{{ request('search') ? 'No expenses match your search query.' : 'Record and manage salon operational expenses.' }}</p>
                <button class="btn btn-primary mt-2" onclick="openExpense()">
                    <i class="bi bi-plus-lg"></i> Add Expense
                </button>
            </div>
        @endif
    </div>
</div>

{{-- ========================================================= --}}
{{-- ADD / EDIT EXPENSE MODAL --}}
{{-- ========================================================= --}}
<div class="modal fade premium-modal job-card-builder-modal" id="expenseModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 720px;">
        <div class="modal-content">
            <form id="expenseForm" method="POST" action="{{ route('expenses.store') }}" class="job-card-builder-form">
                @csrf
                <input type="hidden" id="expenseMethod" name="_method">

                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box job-card-modal-icon" style="background:linear-gradient(135deg,#E0E7FF,#EEF2FF);color:#4F46E5;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title" id="expenseTitle">Add Expense</h5>
                            <p class="modal-subtitle" id="expenseSubtitle">Record a salon operational expense or staff payout.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Date --}}
                        <div class="col-md-6">
                            <label for="expenseDate" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input id="expenseDate" name="expense_date" type="date" class="form-control" required value="{{ now()->toDateString() }}">
                        </div>

                        {{-- Category (with Custom Dropdown & Add New Category) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Category <span class="text-danger">*</span>
                            </label>
                            <input type="hidden" id="expenseCategory" name="expense_category_id" required>

                            <div class="custom-cat-selector">
                                <button type="button" class="custom-cat-btn" id="customCatBtn" onclick="toggleCategoryDropdown()">
                                    <span id="selectedCatText">Select Category</span>
                                    <i class="bi bi-chevron-down" id="customCatArrow"></i>
                                </button>

                                <div class="custom-cat-menu" id="customCatMenu">
                                    <div id="customCatList">
                                        @foreach($categories as $category)
                                            <button type="button"
                                                    class="custom-cat-item"
                                                    data-id="{{ $category->id }}"
                                                    data-name="{{ $category->name }}"
                                                    onclick="selectCategory('{{ $category->id }}', '{{ addslashes($category->name) }}')">
                                                <span>{{ $category->name }}</span>
                                                <i class="bi bi-check-lg custom-cat-check d-none"></i>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Add New Category trigger button --}}
                                    <button type="button" class="custom-cat-add-btn" id="addCatTriggerBtn" onclick="showNewCategoryInput()">
                                        <i class="bi bi-plus-circle-fill"></i> + Add New Category
                                    </button>

                                    {{-- Inline Input for New Category --}}
                                    <div class="custom-cat-add-box d-none" id="addCatInputBox">
                                        <input type="text" id="newCategoryName" class="custom-cat-input" placeholder="Category name..." maxlength="100">
                                        <button type="button" class="custom-cat-save-btn" onclick="saveNewCategory()" title="Save Category">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="custom-cat-cancel-btn" onclick="hideNewCategoryInput()" title="Cancel">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Staff Selection (ONLY shown when Category is Staff Salary, Staff Incentive, OT Staff) --}}
                        <div class="col-12" id="expenseStaffWrap" style="display:none;">
                            <label for="expenseStaff" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Staff Member <span class="text-danger">*</span>
                            </label>
                            <select id="expenseStaff" name="staff_id" class="form-select no-nice-select">
                                <option value="">Select staff member</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6">
                            <label for="expenseAmount" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Amount (₹) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">₹</span>
                                <input id="expenseAmount" name="amount" type="number" step="0.01" min="0.01" class="form-control border-start-0" placeholder="0.00" required>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-6">
                            <label for="expensePayment" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select id="expensePayment" name="payment_method" class="form-select no-nice-select" required>
                                @foreach($paymentTypes as $pt)
                                    <option value="{{ $pt->name }}">{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="expenseDescription" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Description
                            </label>
                            <input id="expenseDescription" name="description" class="form-control" placeholder="Expense description or purpose...">
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label for="expenseNotes" class="form-label fw-bold text-uppercase" style="font-size:0.75rem;color:#475569;letter-spacing:0.04em;">
                                Notes <small class="text-muted fw-normal">(optional)</small>
                            </label>
                            <textarea id="expenseNotes" name="notes" class="form-control" rows="2" placeholder="Additional notes or remarks..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="expenseSubmit">
                        <i class="bi bi-check2-circle"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- EXPENSE DETAILS MODAL --}}
{{-- ========================================================= --}}
<div class="modal fade premium-modal job-card-details-modal" id="expenseDetailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 650px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-box job-card-details-title-icon" style="background:linear-gradient(135deg,#E0E7FF,#EEF2FF);color:#4F46E5;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="modal-header-content">
                        <h5 class="modal-title">Expense Details</h5>
                        <p class="modal-subtitle">Expense transaction breakdown & details</p>
                    </div>
                </div>
                <div class="job-card-details-header-actions">
                    <button type="button" class="job-card-detail-tool" onclick="window.print()" title="Print" aria-label="Print"><i class="bi bi-printer"></i></button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="jcd-details-grid" aria-label="Expense summary">
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Category</span>
                            <strong class="jcd-detail-value" id="detailsExpenseCategory">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Date</span>
                            <strong class="jcd-detail-value" id="detailsExpenseDate">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Staff Member</span>
                            <strong class="jcd-detail-value" id="detailsExpenseStaff">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Payment Method</span>
                            <strong class="jcd-detail-value" id="detailsExpensePayment">—</strong>
                        </div>
                    </div>
                </div>

                {{-- Description Section --}}
                <div class="expense-detail-quote" id="detailsDescWrap">
                    <div class="expense-detail-quote-label"><i class="bi bi-card-text me-1"></i>Description</div>
                    <div class="expense-detail-quote-text" id="detailsExpenseDesc">—</div>
                </div>

                {{-- Notes Section --}}
                <div class="expense-detail-quote mt-2 d-none" id="detailsNotesWrap">
                    <div class="expense-detail-quote-label"><i class="bi bi-stickies me-1"></i>Notes</div>
                    <div class="expense-detail-quote-text" id="detailsExpenseNotes">—</div>
                </div>

                {{-- Totals Card --}}
                <div class="jcd-totals-card mt-4">
                    <div class="jcd-totals-row jcd-totals-row--final">
                        <span class="jcd-totals-label">Amount Paid</span>
                        <strong id="detailsExpenseAmount">₹ 0.00</strong>
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

@push('scripts')
<script>
    const expenseModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('expenseModal'));
    const expenseDetailsModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('expenseDetailsModal'));

    // Category selection & staff field visibility logic
    function isStaffCategory(categoryName) {
        if (!categoryName) return false;
        const name = categoryName.toLowerCase().trim();
        return name.includes('salary') || name.includes('incentive') || name.includes('ot staff') || name === 'staff';
    }

    function toggleCategoryDropdown() {
        const menu = document.getElementById('customCatMenu');
        const btn = document.getElementById('customCatBtn');
        menu.classList.toggle('show');
        btn.classList.toggle('is-open');
    }

    function selectCategory(id, name) {
        document.getElementById('expenseCategory').value = id;
        document.getElementById('selectedCatText').textContent = name;

        // Highlight selected item
        document.querySelectorAll('.custom-cat-item').forEach(item => {
            if (String(item.dataset.id) === String(id)) {
                item.classList.add('active');
                item.querySelector('.custom-cat-check')?.classList.remove('d-none');
            } else {
                item.classList.remove('active');
                item.querySelector('.custom-cat-check')?.classList.add('d-none');
            }
        });

        // Close dropdown menu
        document.getElementById('customCatMenu').classList.remove('show');
        document.getElementById('customCatBtn').classList.remove('is-open');

        // Sync Staff field visibility
        syncStaffField(name);
    }

    function syncStaffField(categoryName) {
        const staffWrap = document.getElementById('expenseStaffWrap');
        const staffSelect = document.getElementById('expenseStaff');

        if (isStaffCategory(categoryName)) {
            staffWrap.style.display = 'block';
            staffSelect.setAttribute('required', 'required');
        } else {
            staffWrap.style.display = 'none';
            staffSelect.removeAttribute('required');
            staffSelect.value = '';
        }
    }

    function showNewCategoryInput() {
        document.getElementById('addCatTriggerBtn').classList.add('d-none');
        document.getElementById('addCatInputBox').classList.remove('d-none');
        const input = document.getElementById('newCategoryName');
        input.value = '';
        input.focus();
    }

    function hideNewCategoryInput() {
        document.getElementById('addCatInputBox').classList.add('d-none');
        document.getElementById('addCatTriggerBtn').classList.remove('d-none');
    }

    async function saveNewCategory() {
        const input = document.getElementById('newCategoryName');
        const name = input.value.trim();
        if (!name) {
            input.focus();
            return;
        }

        try {
            const response = await fetch("{{ route('expense-categories.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name,
                    status: 'active'
                })
            });

            const data = await response.json();

            if (response.ok && data.category) {
                const cat = data.category;

                // Add newly created category to list
                const list = document.getElementById('customCatList');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'custom-cat-item';
                btn.dataset.id = cat.id;
                btn.dataset.name = cat.name;
                btn.onclick = () => selectCategory(cat.id, cat.name);
                btn.innerHTML = `<span>${cat.name}</span><i class="bi bi-check-lg custom-cat-check d-none"></i>`;
                list.appendChild(btn);

                // Select this new category
                selectCategory(cat.id, cat.name);
                hideNewCategoryInput();
            } else {
                alert(data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Failed to add category.'));
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred while saving the category.');
        }
    }

    // Close Category dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.custom-cat-selector')) {
            document.getElementById('customCatMenu')?.classList.remove('show');
            document.getElementById('customCatBtn')?.classList.remove('is-open');
        }
    });

    function openExpense(e = null) {
        closeAllExpenseActionMenus();

        const form = document.getElementById('expenseForm');
        form.reset();

        document.getElementById('expenseMethod').value = e ? 'PUT' : '';
        form.action = e ? `/expenses/${e.id}` : `{{ route('expenses.store') }}`;
        document.getElementById('expenseTitle').textContent = e ? 'Edit Expense' : 'Add Expense';
        document.getElementById('expenseSubtitle').textContent = e ? 'Update expense details and payment.' : 'Record a salon operational expense or staff payout.';
        document.getElementById('expenseSubmit').innerHTML = e ? '<i class="bi bi-check2-circle"></i> Update Expense' : '<i class="bi bi-check2-circle"></i> Save Expense';

        document.getElementById('expenseDate').value = e?.expense_date ? (e.expense_date.split('T')[0] || e.expense_date) : '{{ now()->toDateString() }}';
        document.getElementById('expenseAmount').value = e?.amount ? Number(e.amount) : '';
        document.getElementById('expensePayment').value = e?.payment_method || 'Cash';
        document.getElementById('expenseDescription').value = e?.description || '';
        document.getElementById('expenseNotes').value = e?.notes || '';

        hideNewCategoryInput();

        if (e && e.expense_category_id) {
            const catName = e.category?.name || '';
            selectCategory(e.expense_category_id, catName);
            if (e.staff_id) {
                document.getElementById('expenseStaff').value = e.staff_id;
            }
        } else {
            document.getElementById('expenseCategory').value = '';
            document.getElementById('selectedCatText').textContent = 'Select Category';
            document.querySelectorAll('.custom-cat-item').forEach(i => i.classList.remove('active'));
            syncStaffField('');
        }

        expenseModal.show();
    }

    function openExpenseDetailsModal(e) {
        if (!e) return;

        document.getElementById('detailsExpenseCategory').textContent = e.category?.name || '—';

        let formattedDate = '—';
        if (e.expense_date) {
            const parts = e.expense_date.split(/[-T ]/);
            if (parts.length >= 3) {
                formattedDate = `${parts[2].slice(0, 2)}/${parts[1]}/${parts[0]}`;
            }
        }
        document.getElementById('detailsExpenseDate').textContent = formattedDate;

        document.getElementById('detailsExpenseStaff').textContent = e.staff?.name || 'Not assigned';
        document.getElementById('detailsExpensePayment').textContent = e.payment_method || '—';
        document.getElementById('detailsExpenseDesc').textContent = e.description || 'No description provided.';

        const notesWrap = document.getElementById('detailsNotesWrap');
        if (e.notes) {
            notesWrap.classList.remove('d-none');
            document.getElementById('detailsExpenseNotes').textContent = e.notes;
        } else {
            notesWrap.classList.add('d-none');
        }

        const amt = Number(e.amount || 0);
        document.getElementById('detailsExpenseAmount').textContent = `₹ ${amt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        expenseDetailsModal.show();
    }

    // ---------------------------------------------------------------
    // Three-dot action popover (desktop)
    // ---------------------------------------------------------------

    function closeAllExpenseActionMenus() {
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

    function toggleExpenseActions(button) {
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

    function closeExpenseActions(element) {
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
            closeAllExpenseActionMenus();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeAllExpenseActionMenus();
        }
    });
</script>
@endpush
@endsection
