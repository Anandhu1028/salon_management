@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}?v={{ time() }}">
    <style>

        /* ============================================================
   PAYMENT TYPE COLUMN (list view — one pill per job card)
   ============================================================ */

.job-card-page .premium-list--jobs .pli-col-payment-type {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
}

.payment-type-list {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}

.payment-type-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    min-height: 30px;
    padding: 5px 10px;

    border-radius: 9px;
    border: 1px solid transparent;

    font-size: 0.70rem;
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

.payment-type-pill:hover {
    transform: translateY(-1px);
    box-shadow:
        0 4px 10px rgba(15, 23, 42, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.7);
}

.payment-type-pill i {
    font-size: 0.78rem;
    line-height: 1;
}

/* UPI */
.payment-type-upi {
    color: #6366F1;
    background: linear-gradient(
        135deg,
        #F5F3FF 0%,
        #EDE9FE 100%
    );
    border-color: #DDD6FE;
}

/* Cash */
.payment-type-cash {
    color: #16A34A;
    background: linear-gradient(
        135deg,
        #F0FDF4 0%,
        #DCFCE7 100%
    );
    border-color: #BBF7D0;
}

/* Card */
.payment-type-card {
    color: #7C3AED;
    background: linear-gradient(
        135deg,
        #FAF5FF 0%,
        #F3E8FF 100%
    );
    border-color: #E9D5FF;
}

/* Bank */
.payment-type-bank {
    color: #2563EB;
    background: linear-gradient(
        135deg,
        #EFF6FF 0%,
        #DBEAFE 100%
    );
    border-color: #BFDBFE;
}

/* Net Banking */
.payment-type-net-banking {
    color: #0891B2;
    background: linear-gradient(
        135deg,
        #ECFEFF 0%,
        #CFFAFE 100%
    );
    border-color: #A5F3FC;
}

/* Fallback */
.payment-type-default {
    color: #64748B;
    background: #F8FAFC;
    border-color: #E2E8F0;
}
        /* Compact global Payment Method field (single, applies to whole job card) */
        .job-card-payment-field {
            max-width: 280px;
        }

        .job-card-payment-select-wrap {
            height: 48px;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            background: #FFFFFF;
            padding: 0 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        .job-card-payment-select-wrap:hover {
            border-color: #CBD5E1;
        }

        .job-card-payment-select-wrap:focus-within {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 3.5px rgba(139, 92, 246, 0.12);
        }

        .job-card-payment-select-wrap .form-field-icon {
            color: #6366F1;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        #payment_type_id.no-nice-select {
            border: 0 !important;
            outline: none !important;
            background: transparent !important;
            box-shadow: none !important;
            height: 100% !important;
            padding: 0 20px 0 2px !important;
            color: #1E293B !important;
            font-size: 0.88rem !important;
            font-weight: 500 !important;
            width: 100% !important;
            cursor: pointer;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }

        .job-card-payment-select-arrow {
            pointer-events: none;
            font-size: 11px;
            color: #94A3B8;
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
        }

        @media (max-width: 576px) {
            .job-card-payment-field {
                max-width: 100%;
            }
        }
        /* Payment cards retain a single selected method, matching the job-card transaction model. */
        .job-card-payment-cards { display: flex; flex-wrap: wrap; gap: 10px; }
        .job-card-payment-card { position: relative; display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 8px 12px; border: 1.5px solid #E2E8F0; border-radius: 12px; background: #fff; color: #475569; font-size: .78rem; font-weight: 700; cursor: pointer; transition: .16s ease; }
        .job-card-payment-card:hover { border-color: #C4B5FD; background: #FAF8FF; }
        .job-card-payment-card input { position: absolute; opacity: 0; pointer-events: none; }
        .job-card-payment-card .payment-card-check { width: 17px; height: 17px; border: 1.5px solid #CBD5E1; border-radius: 5px; display: inline-flex; align-items: center; justify-content: center; color: transparent; transition: .16s ease; }
        .job-card-payment-card .payment-card-icon { width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; background: #F1F5F9; color: #64748B; }
        .job-card-payment-card.is-selected { border-color: #8B5CF6; background: #F5F3FF; color: #5B21B6; box-shadow: 0 0 0 3px rgba(139, 92, 246, .10); }
        .job-card-payment-card.is-selected .payment-card-check { border-color: #7C3AED; background: #7C3AED; color: #fff; }
        .job-card-payment-card.is-selected .payment-card-icon { background: #EDE9FE; color: #6D28D9; }

        /* Custom customer selector keeps the native field for form semantics
           while presenting the same controlled option menu as purchases. */
        .job-card-custom-select {
            position: relative;
            width: 100%;
        }

        .job-card-native-select {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        .job-card-select-trigger {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 12px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #334155;
            font-size: .88rem;
            font-weight: 600;
            text-align: left;
        }

        .job-card-select-value {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .job-card-select-trigger:focus-visible,
        .job-card-custom-select.is-open .job-card-select-trigger {
            outline: 2px solid rgba(124, 58, 237, .28);
            outline-offset: -2px;
        }

        .job-card-select-trigger.is-placeholder { color: #94A3B8; }

        .job-card-select-trigger i {
            flex: 0 0 auto;
            color: #94A3B8;
            transition: transform .18s ease;
        }

        .job-card-custom-select.is-open .job-card-select-trigger i {
            transform: rotate(180deg);
            color: #7C3AED;
        }

        .job-card-select-menu {
            position: fixed;
            z-index: 2000;
            top: 0;
            left: 0;
            max-height: 250px;
            overflow-y: auto;
            padding: 6px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #FFFFFF;
            box-shadow: 0 14px 32px rgba(15, 23, 42, .16);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-5px);
            transition: opacity .16s ease, transform .16s ease, visibility .16s ease;
        }

        .job-card-custom-select.is-open .job-card-select-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .job-card-select-option {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 38px;
            padding: 8px 10px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #334155;
            font-size: .84rem;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
        }

        .job-card-select-option:hover,
        .job-card-select-option:focus-visible {
            background: #F5F3FF;
            color: #6D28D9;
            outline: none;
        }

        .job-card-select-option.is-selected {
            background: #EDE9FE;
            color: #5B21B6;
        }

        .job-card-select-option i { margin-left: auto; color: #7C3AED; }

        .field-control-wrap:has(.job-card-custom-select) {
            overflow: visible !important;
            z-index: 20;
        }

        @media (max-width: 576px) {
            .job-card-select-menu { max-height: 210px; }
        }
    </style>
@endpush

@section('title', 'Job Cards')
@section('page-title', 'Job Cards')

@section('content')

    <div class="job-card-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel' => 'Create Job Card',
            'addModal' => '#jobCardModal',
            'addOnclick' => 'openAddJobCardModal()',
            'filterModule' => 'job-cards',
            'filterRoute' => route('job-cards.index'),
            'filterData' => [
                'customers' => $filterCustomers ?? $customers ?? [],
                'services' => $filterServices ?? $services ?? [],
                'subcategories' => $filterSubcategories ?? [],
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
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search job cards...">
                            @if(!empty($filter))
                                <input type="hidden" name="filter" value="{{ $filter }}">
                            @endif
                            @if($search)
                                <a href="{{ route('job-cards.index', array_filter(['filter' => $filter ?? ''])) }}"
                                    title="Clear search">
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
                @endphp

                <div class="premium-list premium-list--jobs premium-list--feed premium-list--compact premium-list--mgmt">
                    <div class="premium-list-head">
                        <span class="pli-head-cell col-center">#</span>
                        <span class="pli-head-cell col-left">Job Card</span>
                        <span class="pli-head-cell col-center">Customer</span>
                        <span class="pli-head-cell col-center">Service</span>
                        <span class="pli-head-cell col-center">Sub Category</span>
                        <span class="pli-head-cell col-center">Payment Method</span>
                        <span class="pli-head-cell col-center">Amount</span>
                        <span class="pli-head-cell col-center">Actions</span>
                    </div>

                    @foreach($jobCards as $jobCard)
                        <article class="premium-list-item" id="job-card-row-{{ $jobCard->id }}">
                            <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                            <div class="pli-col col-left">
                                <div class="pli-name-cell">
                                    <div class="pli-icon pli-icon--cyan">
                                        <i class="bi bi-clipboard2-check-fill"></i>
                                    </div>
                                    <div class="pli-name-stack">
                                        <span class="pli-title job-card-name">{{ $jobCard->job_card_name }}</span>
                                        <small class="text-muted d-block">{{ $jobCard->job_card_number ?? ('JC-' . str_pad($jobCard->id, 3, '0', STR_PAD_LEFT)) }}</small>
                                        <!-- <span class="pli-subtext job-card-number">#JC-{{ str_pad($jobCard->id, 5, '0', STR_PAD_LEFT) }}</span> -->
                                    </div>
                                </div>
                            </div>

                            <div class="pli-col pli-col-customer col-center">
                                @if($jobCard->customers->isNotEmpty())
                                    @php
                                        $firstCustomer = $jobCard->customers->first();
                                        $extraCount = $jobCard->customers->count() - 1;
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                        <span class="pli-col-text">{{ $firstCustomer->name }}</span>
                                        @if($extraCount > 0)
                                            <span class="badge rounded-pill bg-light text-primary border" title="{{ $jobCard->customers->pluck('name')->join(', ') }}" style="font-size: 0.72rem; font-weight: 600; cursor: help;">
                                                +{{ $extraCount }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="pli-col-text">{{ $jobCard->customer->name ?? '—' }}</span>
                                @endif
                            </div>

                            <div class="pli-col pli-col-service col-center">
                                @php
                                    $servicesList = $jobCard->serviceItems->isNotEmpty()
                                        ? $jobCard->serviceItems->map(fn($item) => $item->service?->service_name)->filter()->values()
                                        : collect([$jobCard->service?->service_name])->filter()->values();
                                    $firstService = $servicesList->first() ?? '—';
                                    $extraServices = $servicesList->count() - 1;
                                @endphp
                                @if($servicesList->isNotEmpty())
                                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                        <span class="pli-col-text">{{ $firstService }}</span>
                                        @if($extraServices > 0)
                                            <span class="badge rounded-pill bg-light text-primary border" title="{{ $servicesList->join(', ') }}" style="font-size: 0.72rem; font-weight: 600; cursor: help;">
                                                +{{ $extraServices }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="pli-col-text">—</span>
                                @endif
                            </div>

                            <div class="pli-col pli-col-subcategory col-center">
                                @php
                                    $subcategoriesList = $jobCard->serviceItems->isNotEmpty()
                                        ? $jobCard->serviceItems->pluck('subcategory')->filter()->values()
                                        : collect([$jobCard->subcategory])->filter()->values();
                                    $firstSubcategory = $subcategoriesList->first() ?? '—';
                                    $extraSubcategories = $subcategoriesList->count() - 1;
                                @endphp
                                @if($subcategoriesList->isNotEmpty())
                                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                        <span class="pli-col-text">{{ $firstSubcategory }}</span>
                                        @if($extraSubcategories > 0)
                                            <span class="badge rounded-pill bg-light text-secondary border" title="{{ $subcategoriesList->join(', ') }}" style="font-size: 0.72rem; font-weight: 600; cursor: help;">
                                                +{{ $extraSubcategories }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="pli-col-text">—</span>
                                @endif
                            </div>

                            {{-- Payment Type — one payment method per job card (shared by every service item) --}}
                            <div class="pli-col pli-col-payment-type col-center">
                                @php
                                    $jobCardPaymentType = $jobCard->serviceItems->isNotEmpty()
                                        ? $jobCard->serviceItems->map(fn($item) => $item->paymentType?->name)->filter()->first()
                                        : null;
                                    $paymentTypeKey = strtolower(trim($jobCardPaymentType ?? ''));

                                    $paymentIcon = match (true) {
                                        str_contains($paymentTypeKey, 'upi') => 'bi-phone',
                                        str_contains($paymentTypeKey, 'cash') => 'bi-cash',
                                        str_contains($paymentTypeKey, 'card') => 'bi-credit-card',
                                        str_contains($paymentTypeKey, 'bank') => 'bi-bank',
                                        str_contains($paymentTypeKey, 'net') => 'bi-globe2',
                                        default => 'bi-wallet2',
                                    };
                                @endphp

                                @if($jobCardPaymentType)
                                    <span class="payment-type-pill payment-type-{{ str_replace(' ', '-', $paymentTypeKey) }}">
                                        <i class="bi {{ $paymentIcon }}"></i>
                                        <span>{{ $jobCardPaymentType }}</span>
                                    </span>
                                @else
                                    <span class="pli-col-text">—</span>
                                @endif
                            </div>

                            <div class="pli-col pli-col-amount col-center">
                                @php
                                    $subtotalAmount = $jobCard->serviceItems->isNotEmpty()
                                        ? $jobCard->serviceItems->sum('amount')
                                        : 0;
                                    $discountAmount = (float) ($jobCard->discount_amount ?? 0);
                                    $finalAmount = max(0, $subtotalAmount - $discountAmount);
                                @endphp
                                <span class="pli-col-text font-bold" style="font-weight: 700; color: #1E293B;">₹{{ number_format($finalAmount, 0) }}</span>
                                @if($discountAmount > 0)
                                    <div class="pli-amount-discount-tag" title="Subtotal ₹{{ number_format($subtotalAmount, 2) }} minus ₹{{ number_format($discountAmount, 2) }} discount">
                                        -₹{{ number_format($discountAmount, 0) }} off
                                    </div>
                                @endif
                            </div>

                            <div class="pli-col pli-col-actions col-actions actions-cell col-center">
                                <div class="dropdown pli-dots-dropdown d-md-none">
                                    <button class="pli-btn-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end pli-action-menu">
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item"
                                                onclick='openJobCardDetailsModal(@json($jobCard))'>
                                                <span class="pli-menu-icon pli-menu-icon--view"><i class="bi bi-eye"></i></span>
                                                <span>View Details</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item"
                                                data-bs-toggle="modal" data-bs-target="#jobCardModal"
                                                onclick='openEditJobCardModal(@json($jobCard))'>
                                                <span class="pli-menu-icon pli-menu-icon--edit"><i class="bi bi-pencil"></i></span>
                                                <span>Edit Job Card</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item pli-menu-item--danger"
                                                onclick="openDeleteJobCardModal({{ $jobCard->id }}, @js($jobCard->job_card_name))">
                                                <span class="pli-menu-icon pli-menu-icon--delete"><i class="bi bi-trash3"></i></span>
                                                <span>Delete Job Card</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="pli-action-menu-wrap">
                                    <button
                                        type="button"
                                        class="pli-action-dots"
                                        aria-label="Job card actions"
                                        aria-expanded="false"
                                        onclick="toggleJobCardActions(this)"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <div class="pli-action-popover">

                                        <button
                                            type="button"
                                            class="pli-popover-action"
                                            onclick='openJobCardDetailsModal(@json($jobCard)); closeJobCardActions(this)'
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--view">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                            <span>View Details</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="pli-popover-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#jobCardModal"
                                            onclick='openEditJobCardModal(@json($jobCard)); closeJobCardActions(this)'
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--edit">
                                                <i class="bi bi-pencil"></i>
                                            </span>
                                            <span>Edit Job Card</span>
                                        </button>

                                        <div class="pli-popover-divider"></div>

                                        <button
                                            type="button"
                                            class="pli-popover-action pli-popover-action--danger"
                                            onclick="openDeleteJobCardModal({{ $jobCard->id }}, @js($jobCard->job_card_name)); closeJobCardActions(this)"
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--delete">
                                                <i class="bi bi-trash3"></i>
                                            </span>
                                            <span>Delete Job Card</span>
                                        </button>

                                    </div>
                                </div>
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
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#jobCardModal"
                        onclick="openAddJobCardModal()">
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

    <div class="modal fade premium-modal job-card-builder-modal" id="jobCardModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 960px;">
            <div class="modal-content">
                <form id="jobCardForm" method="POST" action="{{ route('job-cards.store') }}" class="job-card-builder-form">
                    @csrf
                    <input type="hidden" name="_method" id="jobCardFormMethod" value="POST">
                    <input type="hidden" name="discount_amount" id="jobCardDiscountHidden" value="0">

                    {{-- Header --}}
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-icon-box" style="background: #EDE9FE; color: #6366F1; border-radius: 12px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
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
                    <div class="modal-body job-card-builder-body">

                        {{-- SCROLLABLE AREA: only the name/customer fields and the
                             service items list scroll. Payment method, total
                             summary, and the footer stay fixed/visible. --}}
                        <div class="job-card-builder-scroll-area">

                            {{-- Top Section: Job Card Name and Customer (2-column) --}}
                            <div class="job-card-builder-section">
                                <div class="job-card-builder-top-row">
                                    <div class="form-field">
                                        <label for="job_card_name" class="form-label">
                                            JOB CARD NAME <span>*</span>
                                        </label>
                                        <div class="field-control-wrap">
                                            <span class="form-field-icon"><i class="bi bi-fonts"></i></span>
                                            <input type="text" name="job_card_name" id="job_card_name" class="form-control"
                                                placeholder="e.g. Bridal Package" required>
                                        </div>
                                    </div>

                                    <div class="form-field">
                                        <label for="customer_ids" class="form-label">
                                            CUSTOMER <span>*</span>
                                        </label>
                                        <div class="field-control-wrap">
                                            <span class="form-field-icon"><i class="bi bi-person"></i></span>
                                            <select name="customer_ids[]" id="customer_ids" class="no-nice-select job-card-native-select" required>
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
                                                <div class="job-card-custom-select" data-select-id="customer_ids"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Service Items Section --}}
                            <div class="job-card-builder-section">
                                <div class="job-card-builder-section-header">
                                    <div>
                                        <h6 class="job-card-builder-section-title">SERVICE ITEMS</h6>
                                    </div>
                                    <button type="button" class="btn-add-service-pill" id="addServiceItemBtn">
                                        <i class="bi bi-plus-lg"></i> Add Service
                                    </button>
                                </div>

                                <div id="serviceItemsContainer" class="job-card-service-items-card">
                                    {{-- Service items will be dynamically rendered here --}}
                                </div>
                            </div>

                        </div>
                        {{-- /.job-card-builder-scroll-area --}}

                        {{-- FIXED BOTTOM AREA: Payment Method + Total Summary.
                             This section always stays visible above the footer,
                             regardless of how many service items are scrolled
                             above it. --}}
                        <div class="job-card-builder-fixed-bottom">

                            {{-- Payment Method — ONE selection that applies to the whole job card
                                 (every service item shares this single payment method). --}}
                            <div class="job-card-builder-section">
                                <div class="form-field">
                                    <label for="payment_type_id" class="form-label">
                                        PAYMENT METHOD <span>*</span>
                                    </label>
                                    <input type="hidden" name="payment_type_id" id="payment_type_id" value="">
                                    <div class="job-card-payment-cards" id="jobCardPaymentCards">
                                        @foreach($paymentTypes as $paymentType)
                                            @php
                                                $paymentName = strtolower($paymentType->name);
                                                $paymentCardIcon = str_contains($paymentName, 'upi') ? 'bi-phone' : (str_contains($paymentName, 'cash') ? 'bi-cash' : (str_contains($paymentName, 'card') ? 'bi-credit-card' : (str_contains($paymentName, 'bank') ? 'bi-bank' : 'bi-wallet2')));
                                            @endphp
                                            <label class="job-card-payment-card" data-payment-id="{{ $paymentType->id }}">
                                                <input type="radio" name="payment_type_choice" value="{{ $paymentType->id }}" aria-label="{{ $paymentType->name }}">
                                                <span class="payment-card-check"><i class="bi bi-check"></i></span>
                                                <span class="payment-card-icon"><i class="bi {{ $paymentCardIcon }}"></i></span>
                                                <span>{{ $paymentType->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Total Amount Summary Card --}}
                            <div class="job-card-summary-card">
                                {{-- LEFT: calc icon + total amount + services badge --}}
                                <div class="job-card-summary-left">
                                    <div class="job-card-summary-calc-icon">
                                        <i class="bi bi-calculator"></i>
                                    </div>
                                    <div class="job-card-summary-total-info">
                                        <span class="job-card-summary-total-label">TOTAL AMOUNT</span>
                                        <span class="job-card-summary-total-val" id="jobCardTotalAmount">₹ 0</span>
                                    </div>
                                    <div class="job-card-services-badge">
                                        <i class="bi bi-people"></i>
                                        <span id="jobCardServiceCount">0</span> Services
                                    </div>
                                </div>

                                {{-- MIDDLE: Subtotal / Discount / Total breakdown --}}
                                <div class="job-card-summary-middle">
                                    <div class="job-card-summary-row">
                                        <span class="job-card-summary-row-label">Subtotal</span>
                                        <span class="job-card-summary-row-val" id="jobCardSubtotal">₹ 0.00</span>
                                    </div>

                                    {{-- Discount row: click the amount to edit it inline --}}
                                    <div class="job-card-summary-row job-card-discount-row">
                                        <span class="job-card-summary-row-label">Discount</span>

                                        <button type="button" class="job-card-discount-display" id="jobCardDiscountDisplay" title="Click to edit discount">
                                            <span class="job-card-summary-row-val" style="color:#EF4444;" id="jobCardDiscount">- ₹ 0.00</span>
                                        </button>

                                        <div class="job-card-discount-edit-wrap" id="jobCardDiscountEditWrap">
                                            <span class="job-card-discount-input-prefix">₹</span>
                                            <input type="number" id="jobCardDiscountInput" class="job-card-discount-input"
                                                min="0" step="0.01" placeholder="0.00" inputmode="decimal">
                                            <button type="button" class="job-card-discount-confirm-btn" id="jobCardDiscountConfirmBtn" title="Save discount">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="job-card-summary-divider"></div>
                                    <div class="job-card-summary-row job-card-summary-row-final">
                                        <span class="job-card-summary-row-label">Total</span>
                                        <span class="job-card-summary-row-val" id="jobCardFinalTotal">₹ 0.00</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- /.job-card-builder-fixed-bottom --}}

                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="jobCardSubmitButton">
                            <i class="bi bi-clipboard2-plus"></i> Create Job Card
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- JOB CARD DETAILS MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal job-card-details-modal" id="jobCardDetailsModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box job-card-details-title-icon"><i class="bi bi-card-checklist"></i></div>
                        <div class="modal-header-content">
                            <h5 class="modal-title">Job Card</h5>
                            <p class="modal-subtitle">Job Card details</p>
                        </div>
                    </div>
                    <div class="job-card-details-header-actions">
                        <button type="button" class="job-card-detail-tool" title="Download" aria-label="Download"><i
                                class="bi bi-download"></i></button>
                        <button type="button" class="job-card-detail-tool" title="Print" aria-label="Print"><i
                                class="bi bi-printer"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="jcd-details-grid" aria-label="Job card summary">
                        <div class="jcd-detail-item">
                            <div class=""></div>
                            <div class="jcd-detail-text">
                                <span class="jcd-detail-label">Job Card Name</span>
                                <strong class="jcd-detail-value" id="jobCardDetailsName">—</strong>
                            </div>
                        </div>
                        <div class="jcd-detail-item">
                            <div class=""></div>
                            <div class="jcd-detail-text">
                                <span class="jcd-detail-label">Date</span>
                                <strong class="jcd-detail-value" id="jobCardDetailsCreated">—</strong>
                            </div>
                        </div>
                        <div class="jcd-detail-item">
                            <div class=""></div>
                            <div class="jcd-detail-text">
                                <span class="jcd-detail-label">Customer(s)</span>
                                <strong class="jcd-detail-value" id="jobCardDetailsCustomer">—</strong>
                            </div>
                        </div>
                        <div class="jcd-detail-item">
                            <div class=""></div>
                            <div class="jcd-detail-text">
                                <span class="jcd-detail-label">Payment Method</span>
                                <strong class="jcd-detail-value" id="jobCardDetailsPaymentType">—</strong>
                            </div>
                        </div>
                        
                    </div>
                    <div class="job-card-details-invoice">
                        <div class="job-card-details-invoice-head">
                          <span>#</span><span>Service</span><span>Staff</span><span>Amount (₹)</span></div>
                        <div id="jobCardDetailsInvoiceItems">
                            {{-- Service items will be populated dynamically --}}
                        </div>
                    </div>
                    <div class="jcd-totals-card">
                        <div class="jcd-totals-row">
                            <span>Subtotal</span><strong id="jobCardDetailsSubtotal">₹0</strong>
                        </div>
                        <div class="jcd-totals-row jcd-totals-row--discount" id="jobCardDetailsDiscountRow">
                            <span>Discount</span><strong id="jobCardDetailsDiscountVal">-₹0</strong>
                        </div>
                        <div class="jcd-totals-divider"></div>
                        <div class="jcd-totals-row jcd-totals-row--final">
                            <span class="jcd-totals-label">Total Amount</span><strong id="jobCardDetailsTotal">₹0</strong>
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

            // ---------------------------------------------------------------
            // FIX: close any open row action-menu (3-dot popover) before any
            // modal is opened. Previously an open menu row kept its elevated
            // z-index (.action-menu-row-open) even while a modal was shown,
            // which made that row visually float on top of the modal.
            // ---------------------------------------------------------------
            function closeAllJobCardActionMenus() {
                document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(wrapper => {
                    wrapper.classList.remove('is-open');

                    const button = wrapper.querySelector('.pli-action-dots');
                    if (button) {
                        button.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');
                    }

                    const row = wrapper.closest('.premium-list-item');
                    if (row) {
                        row.classList.remove('action-menu-row-open');
                    }
                });
            }

            function closeJobCardCustomerDropdown(except = null) {
                document.querySelectorAll('.job-card-custom-select.is-open').forEach(dropdown => {
                    if (dropdown !== except) {
                        dropdown.classList.remove('is-open');
                        dropdown.querySelector('.job-card-select-trigger')?.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            function positionJobCardStaffDropdown(panel, trigger) {
                if (!panel || !trigger) return;

                const triggerRect = trigger.getBoundingClientRect();
                const panelHeight = Math.min(panel.scrollHeight, 220);
                const gap = 6;
                const opensAbove = triggerRect.bottom + gap + panelHeight > window.innerHeight
                    && triggerRect.top - gap - panelHeight > 8;

                panel.style.left = `${Math.max(8, triggerRect.left)}px`;
                panel.style.width = `${Math.min(Math.max(triggerRect.width, 260), window.innerWidth - 16)}px`;
                panel.style.top = opensAbove
                    ? `${Math.max(8, triggerRect.top - panelHeight - gap)}px`
                    : `${triggerRect.bottom + gap}px`;
            }

            function positionJobCardCustomerDropdown(customSelect) {
                const trigger = customSelect.querySelector('.job-card-select-trigger');
                const menu = customSelect.querySelector('.job-card-select-menu');
                if (!trigger || !menu) return;

                const triggerRect = trigger.getBoundingClientRect();
                const menuHeight = Math.min(menu.scrollHeight, window.innerWidth <= 576 ? 210 : 250);
                const gap = 7;
                const opensAbove = triggerRect.bottom + gap + menuHeight > window.innerHeight
                    && triggerRect.top - gap - menuHeight > 8;

                menu.style.left = `${Math.max(8, triggerRect.left)}px`;
                const minimumWidth = customSelect.closest('.job-card-item-field-col') ? 260 : 320;
                menu.style.width = `${Math.min(Math.max(triggerRect.width, minimumWidth), window.innerWidth - 16)}px`;
                menu.style.top = opensAbove
                    ? `${Math.max(8, triggerRect.top - menuHeight - gap)}px`
                    : `${triggerRect.bottom + gap}px`;
            }

            function initializeJobCardCustomerDropdown() {
                const select = document.getElementById('customer_ids');
                const customSelect = document.querySelector('.job-card-custom-select[data-select-id="customer_ids"]');
                if (!select || !customSelect || customSelect.dataset.initialized === 'true') return;

                customSelect.dataset.initialized = 'true';
                customSelect.innerHTML = `
                    <button type="button" class="job-card-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="job-card-select-value"></span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="job-card-select-menu" role="listbox"></div>
                `;

                const trigger = customSelect.querySelector('.job-card-select-trigger');
                const value = customSelect.querySelector('.job-card-select-value');
                const menu = customSelect.querySelector('.job-card-select-menu');

                function syncDropdown() {
                    const selectedOption = select.options[select.selectedIndex];
                    const selected = selectedOption && selectedOption.value !== '';
                    value.textContent = selected ? selectedOption.textContent.trim() : 'Select customer';
                    trigger.classList.toggle('is-placeholder', !selected);
                    menu.querySelectorAll('.job-card-select-option').forEach(option => {
                        const isSelected = option.dataset.value === select.value;
                        option.classList.toggle('is-selected', isSelected);
                        option.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                        option.querySelector('i')?.classList.toggle('d-none', !isSelected);
                    });
                }

                Array.from(select.options).forEach(option => {
                    const optionButton = document.createElement('button');
                    optionButton.type = 'button';
                    optionButton.className = 'job-card-select-option';
                    optionButton.dataset.value = option.value;
                    optionButton.setAttribute('role', 'option');
                    optionButton.innerHTML = `<span>${option.textContent.trim()}</span><i class="bi bi-check-lg d-none"></i>`;
                    optionButton.addEventListener('click', () => {
                        select.value = option.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        customSelect.classList.remove('is-open');
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                    menu.appendChild(optionButton);
                });

                trigger.addEventListener('click', () => {
                    const isOpen = !customSelect.classList.contains('is-open');
                    closeJobCardCustomerDropdown(customSelect);
                    customSelect.classList.toggle('is-open', isOpen);
                    trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    if (isOpen) positionJobCardCustomerDropdown(customSelect);
                });

                select.addEventListener('change', syncDropdown);
                syncDropdown();
            }

            function initializeJobCardServiceDropdown(select, customSelect) {
                if (!select || !customSelect || customSelect.dataset.initialized === 'true') return;

                customSelect.dataset.initialized = 'true';
                customSelect.innerHTML = `
                    <button type="button" class="job-card-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="job-card-select-value"></span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="job-card-select-menu" role="listbox"></div>
                `;

                const trigger = customSelect.querySelector('.job-card-select-trigger');
                const value = customSelect.querySelector('.job-card-select-value');
                const menu = customSelect.querySelector('.job-card-select-menu');

                function syncDropdown() {
                    const selectedOption = select.options[select.selectedIndex];
                    const selected = selectedOption && selectedOption.value !== '';
                    value.textContent = selected ? selectedOption.textContent.trim() : 'Select service';
                    trigger.classList.toggle('is-placeholder', !selected);
                    menu.querySelectorAll('.job-card-select-option').forEach(option => {
                        const isSelected = option.dataset.value === select.value;
                        option.classList.toggle('is-selected', isSelected);
                        option.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                        option.querySelector('i')?.classList.toggle('d-none', !isSelected);
                    });
                }

                Array.from(select.options).forEach(option => {
                    const optionButton = document.createElement('button');
                    optionButton.type = 'button';
                    optionButton.className = 'job-card-select-option';
                    optionButton.dataset.value = option.value;
                    optionButton.setAttribute('role', 'option');
                    optionButton.innerHTML = `<span>${option.textContent.trim()}</span><i class="bi bi-check-lg d-none"></i>`;
                    optionButton.addEventListener('click', () => {
                        select.value = option.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        customSelect.classList.remove('is-open');
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                    menu.appendChild(optionButton);
                });

                trigger.addEventListener('click', () => {
                    const isOpen = !customSelect.classList.contains('is-open');
                    closeJobCardCustomerDropdown(customSelect);
                    customSelect.classList.toggle('is-open', isOpen);
                    trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    if (isOpen) positionJobCardCustomerDropdown(customSelect);
                });

                select.addEventListener('change', syncDropdown);
                syncDropdown();
            }

            function openAddJobCardModal() {
                closeAllJobCardActionMenus();

                const form = document.getElementById('jobCardForm');
                form.reset();
                form.action = "{{ route('job-cards.store') }}";
                document.getElementById('jobCardFormMethod').value = 'POST';
                document.getElementById('jobCardModalTitle').textContent = 'Create Job Card';
                document.getElementById('jobCardModalSubtitle').textContent = 'Create a new customer service job card.';
                document.getElementById('jobCardSubmitButton').innerHTML = '<i class="bi bi-clipboard2-plus"></i> Create Job Card';

                const customerSelect = document.getElementById('customer_ids');
                customerSelect.value = '';
                customerSelect.dispatchEvent(new Event('change', { bubbles: true }));

                setJobCardPaymentMethod('');

                // Reset discount
                setDiscountValue(0);
                closeDiscountEditor();

                // Initialize with one empty service item
                initializeServiceItemBuilder([]);
            }

            function openEditJobCardModal(jobCard) {
                closeAllJobCardActionMenus();

                const form = document.getElementById('jobCardForm');
                form.action = `/job-cards/${jobCard.id}`;
                document.getElementById('jobCardFormMethod').value = 'PUT';
                document.getElementById('jobCardModalTitle').textContent = 'Edit Job Card';
                document.getElementById('jobCardModalSubtitle').textContent = 'Update customer service job card.';
                document.getElementById('jobCardSubmitButton').innerHTML = '<i class="bi bi-check2-circle"></i> Update Job Card';

                document.getElementById('job_card_name').value = jobCard.job_card_name ?? '';

                const customerIds = (jobCard.customers && jobCard.customers.length)
                    ? jobCard.customers.map(c => String(c.id))
                    : (jobCard.customer_id ? [String(jobCard.customer_id)] : []);

                const customerSelect = document.getElementById('customer_ids');
                customerSelect.value = customerIds[0] || '';
                customerSelect.dispatchEvent(new Event('change', { bubbles: true }));

                // Populate the single job-card-level payment method from the
                // first service item's payment type (all items share one now).
                const existingServiceItems = jobCard.service_items || [];
                const existingPaymentTypeId = (existingServiceItems.length && existingServiceItems[0].payment_type_id)
                    ? String(existingServiceItems[0].payment_type_id)
                    : '';
                setJobCardPaymentMethod(existingPaymentTypeId);

                // Populate discount
                setDiscountValue(parseFloat(jobCard.discount_amount) || 0);
                closeDiscountEditor();

                // Populate service items
                initializeServiceItemBuilder(existingServiceItems);
            }

            function initializeServiceItemBuilder(serviceItems) {
                const container = document.getElementById('serviceItemsContainer');
                container.innerHTML = '';

                // Add existing service items or create an empty one
                if (serviceItems && serviceItems.length > 0) {
                    serviceItems.forEach((item, index) => {
                        addServiceItemRow(container, item);
                    });
                } else {
                    addServiceItemRow(container);
                }

                updateTotalAmount();
                attachServiceItemHandlers();
            }

            function addServiceItemRow(container, itemData = null) {
                const itemIndex = container.querySelectorAll('.job-card-service-item').length;
                const itemId = 'service-item-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                const itemNumber = String(itemIndex + 1).padStart(2, '0');

                const itemHTML = `
                    <div class="job-card-service-item" data-item-id="${itemId}">
                        {{-- Number badge 
                        <div class="job-card-item-num-col">
                            <div class="job-card-item-num-badge">${itemNumber}</div>
                        </div>--}}

                        {{-- Service Select --}}
                        <div class="job-card-item-field-col">
                            <label class="job-card-item-label">SERVICE</label>
                            <div class="job-card-input-box">
                                <select name="service_items[${itemIndex}][service_id]" class="service-select job-card-native-select" data-item-id="${itemId}" required>
                                    <option value="">Select service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-subcategory="{{ $service->subcategory }}" data-category="{{ $service->category }}">
                                            {{ $service->service_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="job-card-custom-select"></div>
                            </div>
                        </div>

                        {{-- Subcategory Select --}}
                        <div class="job-card-item-field-col">
                            <label class="job-card-item-label">SUBCATEGORY</label>
                            <div class="job-card-input-box">
                                <select name="service_items[${itemIndex}][subcategory]" class="subcategory-select" data-item-id="${itemId}" required disabled>
                                    <option value="">Select subcategory</option>
                                </select>
                                <i class="bi bi-chevron-down job-card-select-arrow"></i>
                            </div>
                        </div>

                        {{-- Staff Multi-Select Picker --}}
                        <div class="job-card-item-field-col job-card-item-field-col--staff">
                            <label class="job-card-item-label">STAFF</label>
                            <div class="job-card-staff-picker-wrap" data-item-id="${itemId}">
                                <div class="job-card-input-box job-card-staff-trigger" data-item-id="${itemId}">
                                    <div class="job-card-staff-avatar-stack" id="staff-avatars-${itemId}">
                                        <span class="job-card-staff-placeholder">Select staff</span>
                                    </div>
                                    <i class="bi bi-chevron-down job-card-select-arrow"></i>
                                </div>
                                <div class="job-card-staff-subtext" id="staff-subtext-${itemId}"></div>

                                {{-- Staff Dropdown Checklist --}}
                                <div class="job-card-staff-dropdown-panel" id="staff-panel-${itemId}">
                                    <div class="job-card-staff-dropdown-header">Select Staff Members</div>
                                    <div class="job-card-staff-dropdown-list">
                                        @foreach($staff as $member)
                                            <label class="job-card-staff-option">
                                                <input type="checkbox" name="service_items[${itemIndex}][staff_ids][]" value="{{ $member->id }}" data-name="{{ $member->name }}" class="staff-checkbox" data-item-id="${itemId}">
                                                <span class="staff-option-name">{{ $member->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Amount --}}
                            <div class="job-card-item-field-col job-card-item-field-col--amount">
                                <label class="job-card-item-label">AMOUNT (₹)</label>

                                <div class="job-card-input-box job-card-amount-box">
                                    <span class="job-card-currency-symbol">₹</span>

                                    <input
                                        type="number"
                                        name="service_items[${itemIndex}][amount]"
                                        class="amount-input"
                                        data-item-id="${itemId}"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                        required
                                    >
                                </div>
                            </div>

                            {{-- Delete --}}
                            <button
                                type="button"
                                class="job-card-item-delete-btn"
                                data-item-id="${itemId}"
                                title="Remove service item"
                                aria-label="Remove service item"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                `;

                const wrapper = document.createElement('div');
                wrapper.innerHTML = itemHTML.trim();
                const itemElement = wrapper.firstElementChild;
                container.appendChild(itemElement);

                initializeJobCardServiceDropdown(
                    itemElement.querySelector('.service-select'),
                    itemElement.querySelector('.job-card-custom-select')
                );

                // Populate with existing data if editing
                if (itemData) {
                    const serviceSelect = itemElement.querySelector('.service-select');
                    const subcategorySelect = itemElement.querySelector('.subcategory-select');
                    const amountInput = itemElement.querySelector('.amount-input');

                    serviceSelect.value = itemData.service_id || '';
                    serviceSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    amountInput.value = itemData.amount || '';

                    // Load and select subcategory
                    const selectedOption = serviceSelect.querySelector(`option[value="${itemData.service_id}"]`);
                    if (selectedOption && selectedOption.dataset.subcategory) {
                        subcategorySelect.disabled = false;
                        subcategorySelect.innerHTML = `<option value="${selectedOption.dataset.subcategory}">${selectedOption.dataset.subcategory}</option>`;
                        subcategorySelect.value = itemData.subcategory || selectedOption.dataset.subcategory;
                    }

                    // Select staff members
                    if (itemData.staff && itemData.staff.length) {
                        const staffIds = itemData.staff.map(s => String(s.id));
                        const checkboxes = itemElement.querySelectorAll('.staff-checkbox');
                        checkboxes.forEach(cb => {
                            if (staffIds.includes(String(cb.value))) {
                                cb.checked = true;
                            }
                        });
                        updateStaffDisplay(itemId);
                    }
                }
            }

            function setupServiceItemEvents() {
                const container = document.getElementById('serviceItemsContainer');
                if (!container || container._eventsInitialized) return;
                container._eventsInitialized = true;

                // 1. Service select change & Amount change
                container.addEventListener('change', function(e) {
                    // Service select changed
                    if (e.target.classList.contains('service-select')) {
                        const item = e.target.closest('.job-card-service-item');
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        const subcategorySelect = item.querySelector('.subcategory-select');

                        if (selectedOption && selectedOption.dataset.subcategory) {
                            subcategorySelect.disabled = false;
                            subcategorySelect.innerHTML = `<option value="${selectedOption.dataset.subcategory}">${selectedOption.dataset.subcategory}</option>`;
                            subcategorySelect.value = selectedOption.dataset.subcategory;
                        } else {
                            subcategorySelect.disabled = true;
                            subcategorySelect.innerHTML = '<option value="">Select subcategory</option>';
                        }
                    }

                    // Staff checkbox changed
                    if (e.target.classList.contains('staff-checkbox')) {
                        const item = e.target.closest('.job-card-service-item');
                        if (item) {
                            updateStaffDisplay(item.dataset.itemId);
                        }
                    }

                    // Amount input changed
                    if (e.target.classList.contains('amount-input')) {
                        updateTotalAmount();
                    }
                });

                // 2. Amount live input
                container.addEventListener('input', function(e) {
                    if (e.target.classList.contains('amount-input')) {
                        updateTotalAmount();
                    }
                });

                // 3. Click handler for Staff trigger & Delete button
                container.addEventListener('click', function(e) {
                    // Staff Picker Trigger
                    const staffTrigger = e.target.closest('.job-card-staff-trigger');
                    if (staffTrigger) {
                        e.stopPropagation();
                        const item = staffTrigger.closest('.job-card-service-item');
                        const itemId = item.dataset.itemId;
                        const panel = document.getElementById(`staff-panel-${itemId}`);

                        // Close any other open panels
                        document.querySelectorAll('.job-card-staff-dropdown-panel').forEach(p => {
                            if (p !== panel) p.classList.remove('is-open');
                        });

                        if (panel) {
                            const isOpen = !panel.classList.contains('is-open');
                            panel.classList.toggle('is-open', isOpen);
                            if (isOpen) positionJobCardStaffDropdown(panel, staffTrigger);
                        }
                        return;
                    }

                    // Delete Item Button
                    const deleteBtn = e.target.closest('.job-card-item-delete-btn');
                    if (deleteBtn) {
                        e.preventDefault();
                        const item = deleteBtn.closest('.job-card-service-item');
                        if (!item) return;

                        const allItems = container.querySelectorAll('.job-card-service-item');
                        if (allItems.length > 1) {
                            item.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                item.remove();
                                renumberServiceItems();
                                updateTotalAmount();
                            }, 180);
                        } else {
                            // Reset the single item fields
                            const serv = item.querySelector('.service-select');
                            if (serv) {
                                serv.value = '';
                                serv.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            const subcat = item.querySelector('.subcategory-select');
                            if (subcat) {
                                subcat.disabled = true;
                                subcat.innerHTML = '<option value="">Select subcategory</option>';
                            }
                            item.querySelectorAll('.staff-checkbox').forEach(c => c.checked = false);
                            const amt = item.querySelector('.amount-input');
                            if (amt) amt.value = '';
                            updateStaffDisplay(item.dataset.itemId);
                            updateTotalAmount();
                        }
                        return;
                    }
                });

                // Close staff dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.job-card-staff-picker-wrap')) {
                        document.querySelectorAll('.job-card-staff-dropdown-panel').forEach(p => p.classList.remove('is-open'));
                    }
                });

                window.addEventListener('resize', function () {
                    document.querySelectorAll('.job-card-staff-dropdown-panel.is-open').forEach(panel => {
                        const trigger = panel.closest('.job-card-staff-picker-wrap')?.querySelector('.job-card-staff-trigger');
                        positionJobCardStaffDropdown(panel, trigger);
                    });
                });

                document.querySelector('.job-card-builder-scroll-area')?.addEventListener('scroll', function () {
                    document.querySelectorAll('.job-card-staff-dropdown-panel.is-open').forEach(panel => {
                        const trigger = panel.closest('.job-card-staff-picker-wrap')?.querySelector('.job-card-staff-trigger');
                        positionJobCardStaffDropdown(panel, trigger);
                    });
                });

                // Add Service button click
                const addBtn = document.getElementById('addServiceItemBtn');
                if (addBtn) {
                    addBtn.onclick = function(e) {
                        e.preventDefault();
                        addServiceItemRow(container);
                        renumberServiceItems();
                        updateTotalAmount();
                    };
                }
            }

            function attachServiceItemHandlers() {
                setupServiceItemEvents();
                setupDiscountEditorEvents();
                setupPaymentMethodEvents();
            }

            function updateStaffDisplay(itemId) {
                const item = document.querySelector(`.job-card-service-item[data-item-id="${itemId}"]`);
                if (!item) return;

                const avatarStack = document.getElementById(`staff-avatars-${itemId}`);
                const subtext = document.getElementById(`staff-subtext-${itemId}`);
                const checkedBoxes = Array.from(item.querySelectorAll('.staff-checkbox:checked'));

                if (checkedBoxes.length === 0) {
                    avatarStack.innerHTML = '<span class="job-card-staff-placeholder">Select staff</span>';
                    subtext.textContent = '';
                    return;
                }

                const names = checkedBoxes.map(cb => cb.dataset.name || cb.parentElement.textContent.trim());
                const maxVisible = 2;
                const visibleNames = names.slice(0, maxVisible);
                const extraCount = names.length - maxVisible;

                let avatarsHtml = '';
                visibleNames.forEach(name => {
                    const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    avatarsHtml += `<div class="job-card-staff-avatar-bubble" title="${name}">${initials}</div>`;
                });

                if (extraCount > 0) {
                    avatarsHtml += `<div class="job-card-staff-avatar-more">+${extraCount}</div>`;
                }

                avatarStack.innerHTML = avatarsHtml;

                if (names.length === 1) {
                    subtext.textContent = names[0];
                } else if (extraCount > 0) {
                    subtext.textContent = `${visibleNames.join(', ')} +${extraCount}`;
                } else {
                    subtext.textContent = visibleNames.join(', ');
                }
            }

            function renumberServiceItems() {
                const container = document.getElementById('serviceItemsContainer');
                const items = container.querySelectorAll('.job-card-service-item');

                items.forEach((item, index) => {
                    const itemNumber = String(index + 1).padStart(2, '0');
                    const numberDisplay = item.querySelector('.job-card-item-num-badge');
                    if (numberDisplay) {
                        numberDisplay.textContent = itemNumber;
                    }

                    // Update field names to maintain 0, 1, 2 array indexing
                    item.querySelectorAll('input, select').forEach(field => {
                        const nameAttr = field.getAttribute('name');
                        if (nameAttr) {
                            field.setAttribute('name', nameAttr.replace(/\[\d+\]/, `[${index}]`));
                        }
                    });
                });
            }

            // ---------------------------------------------------------------
            // DISCOUNT — click-to-edit amount, saved into a hidden field
            // that is submitted with the form and persisted to the DB.
            // ---------------------------------------------------------------

            function formatCurrency(value) {
                return `₹ ${Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }

            function getSubtotal() {
                const container = document.getElementById('serviceItemsContainer');
                if (!container) return 0;
                let total = 0;
                container.querySelectorAll('.amount-input').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                return total;
            }

            function getDiscountValue() {
                const hidden = document.getElementById('jobCardDiscountHidden');
                return hidden ? (parseFloat(hidden.value) || 0) : 0;
            }

            function setDiscountValue(value) {
                const subtotal = getSubtotal();
                let clamped = Math.max(0, parseFloat(value) || 0);
                if (clamped > subtotal) clamped = subtotal;

                const hidden = document.getElementById('jobCardDiscountHidden');
                const display = document.getElementById('jobCardDiscount');
                const input = document.getElementById('jobCardDiscountInput');

                if (hidden) hidden.value = clamped.toFixed(2);
                if (display) display.textContent = clamped > 0
                    ? `- ${formatCurrency(clamped)}`
                    : `- ₹ 0.00`;
                if (input) input.value = clamped > 0 ? clamped.toFixed(2) : '';

                return clamped;
            }

            function openDiscountEditor() {
                const displayBtn = document.getElementById('jobCardDiscountDisplay');
                const editWrap = document.getElementById('jobCardDiscountEditWrap');
                const input = document.getElementById('jobCardDiscountInput');
                if (!displayBtn || !editWrap || !input) return;

                displayBtn.style.display = 'none';
                editWrap.classList.add('is-open');
                input.value = getDiscountValue() > 0 ? getDiscountValue().toFixed(2) : '';
                input.focus();
                input.select();
            }

            function closeDiscountEditor() {
                const displayBtn = document.getElementById('jobCardDiscountDisplay');
                const editWrap = document.getElementById('jobCardDiscountEditWrap');
                if (!displayBtn || !editWrap) return;

                displayBtn.style.display = '';
                editWrap.classList.remove('is-open');
            }

            function commitDiscountEditor() {
                const input = document.getElementById('jobCardDiscountInput');
                const raw = input ? input.value : 0;
                setDiscountValue(raw);
                closeDiscountEditor();
                updateTotalAmount();
            }

            function setupDiscountEditorEvents() {
                const displayBtn = document.getElementById('jobCardDiscountDisplay');
                const input = document.getElementById('jobCardDiscountInput');
                const confirmBtn = document.getElementById('jobCardDiscountConfirmBtn');
                const editWrap = document.getElementById('jobCardDiscountEditWrap');

                if (displayBtn && !displayBtn._bound) {
                    displayBtn._bound = true;
                    displayBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        openDiscountEditor();
                    });
                }

                if (confirmBtn && !confirmBtn._bound) {
                    confirmBtn._bound = true;
                    confirmBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        commitDiscountEditor();
                    });
                }

                if (input && !input._bound) {
                    input._bound = true;
                    input.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            commitDiscountEditor();
                        } else if (e.key === 'Escape') {
                            e.preventDefault();
                            closeDiscountEditor();
                        }
                    });
                    input.addEventListener('blur', function () {
                        // Small delay so a click on the confirm button still registers
                        setTimeout(() => {
                            if (editWrap && editWrap.classList.contains('is-open')) {
                                commitDiscountEditor();
                            }
                        }, 120);
                    });
                }
            }

            function updateTotalAmount() {
                const subtotal = getSubtotal();
                let discount = getDiscountValue();

                // Keep discount from exceeding the current subtotal (e.g. if a service was removed)
                if (discount > subtotal) {
                    discount = setDiscountValue(subtotal);
                }

                const finalTotal = Math.max(0, subtotal - discount);

                document.getElementById('jobCardTotalAmount').textContent = formatCurrency(finalTotal);
                document.getElementById('jobCardSubtotal').textContent = formatCurrency(subtotal);
                document.getElementById('jobCardDiscount').textContent = discount > 0
                    ? `- ${formatCurrency(discount)}`
                    : `- ₹ 0.00`;
                document.getElementById('jobCardFinalTotal').textContent = formatCurrency(finalTotal);

                // Update count badge
                const serviceCount = document.querySelectorAll('#serviceItemsContainer .amount-input').length;
                document.getElementById('jobCardServiceCount').textContent = serviceCount;
            }


            function openJobCardDetailsModal(jobCard) {
                closeAllJobCardActionMenus();

                const customers = (jobCard.customers && jobCard.customers.length)
                    ? jobCard.customers
                    : (jobCard.customer ? [jobCard.customer] : []);

                const customerText = customers.map(c => {
                    return c.mobile_number ? `${c.name} (${c.mobile_number})` : c.name;
                }).join(', ') || '—';

                // Extract staff assigned across service items.
                const serviceItems = jobCard.service_items || [];
                const allStaff = serviceItems.flatMap(item => item.staff?.map(s => s.name) || []);
                const uniqueStaff = [...new Set(allStaff)].join(', ') || '—';

                // Single job-card-level payment method (shared across every service item)
                const firstItemWithPayment = serviceItems.find(item => item.payment_type?.name || item.paymentType?.name);
                const jobCardPaymentTypeName = firstItemWithPayment
                    ? (firstItemWithPayment.payment_type?.name || firstItemWithPayment.paymentType?.name)
                    : '—';

                // Calculate totals
                const subtotal = serviceItems.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
                const discount = Math.min(parseFloat(jobCard.discount_amount) || 0, subtotal);
                const total = Math.max(0, subtotal - discount);

                const formattedSubtotal = subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const formattedDiscount = discount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const formattedTotal = total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                // Generate invoice items (Payment column removed — it's shown once above as a single field)
                let invoiceItemsHTML = '';
                serviceItems.forEach((item, index) => {
                    const serviceName = item.service?.service_name || '—';
                    const staffNames = item.staff?.map(s => s.name).join(', ') || 'No staff assigned';
                    const amount = parseFloat(item.amount) || 0;
                    const formattedAmount = amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    invoiceItemsHTML += `
                        <div class="job-card-details-invoice-line">
                            <span><span class="jcd-invoice-num-badge">${index + 1}</span></span>
                            <span><strong>${serviceName}</strong></span>
                            <span>${staffNames}</span>
                            <span>₹${formattedAmount}</span>
                        </div>
                    `;
                });

                document.getElementById('jobCardDetailsName').textContent = jobCard.job_card_name || '—';
                document.getElementById('jobCardDetailsCreated').textContent = jobCard.created_at
                    ? new Date(jobCard.created_at).toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })
                    : '—';
                document.getElementById('jobCardDetailsCustomer').textContent = customerText;
                document.getElementById('jobCardDetailsPaymentType').textContent = jobCardPaymentTypeName;
                document.getElementById('jobCardDetailsInvoiceItems').innerHTML = invoiceItemsHTML;
                document.getElementById('jobCardDetailsSubtotal').textContent = `₹${formattedSubtotal}`;
                document.getElementById('jobCardDetailsDiscountVal').textContent = `-₹${formattedDiscount}`;
                document.getElementById('jobCardDetailsTotal').textContent = `₹${formattedTotal}`;

                bootstrap.Modal.getOrCreateInstance(document.getElementById('jobCardDetailsModal')).show();
            }

            function openDeleteJobCardModal(jobCardId, jobCardName) {
                closeAllJobCardActionMenus();

                deleteJobCardId = jobCardId;
                const msg = document.getElementById('deleteJobCardMessage');
                if (msg) msg.textContent = `Are you sure you want to delete ${jobCardName}?`;
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteJobCardModal'));
                modal.show();
            }

            document.getElementById('confirmDeleteJobCardButton').addEventListener('click', async function () {
                if (!deleteJobCardId) return;

                const button = this;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

                try {
                    const response = await fetch(`/job-cards/${deleteJobCardId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to delete job card.');
                    }

                    const modalElement = document.getElementById('deleteJobCardModal');
                    const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
                    if (modal) modal.hide();

                    const row = document.getElementById(`job-card-row-${deleteJobCardId}`);
                    if (row) {
                        row.style.transition = 'opacity .25s ease, transform .25s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            row.remove();
                            if (document.querySelectorAll('.premium-list .premium-list-item').length === 0) {
                                window.location.reload();
                            }
                        }, 250);
                    }

                    if (window.showToast) {
                        window.showToast(data.message || 'Job card deleted successfully.', 'success');
                    }

                } catch (error) {
                    if (window.showToast) {
                        window.showToast(error.message || 'Error deleting job card', 'danger');
                    } else {
                        alert(error.message || 'Error deleting job card');
                    }
                } finally {
                    deleteJobCardId = null;
                    button.disabled = false;
                    button.textContent = 'Delete';
                }
            });

            initializeJobCardCustomerDropdown();

            document.addEventListener('click', function (event) {
                if (!event.target.closest('.job-card-custom-select')) {
                    closeJobCardCustomerDropdown();
                }
            });

            window.addEventListener('resize', function () {
                document.querySelectorAll('.job-card-custom-select.is-open').forEach(positionJobCardCustomerDropdown);
            });

            document.querySelector('.job-card-builder-scroll-area')?.addEventListener('scroll', function () {
                document.querySelectorAll('.job-card-custom-select.is-open').forEach(positionJobCardCustomerDropdown);
            });

            // Form submission handler
            document.getElementById('jobCardForm').addEventListener('submit', function(e) {
                // Validate that at least one service item exists
                const serviceItems = document.querySelectorAll('.job-card-service-item');
                if (serviceItems.length === 0) {
                    e.preventDefault();
                    if (window.showToast) window.showToast('Please add at least one service item', 'danger');
                    return false;
                }

                // Make sure any open discount edit is committed before submit
                const editWrap = document.getElementById('jobCardDiscountEditWrap');
                if (editWrap && editWrap.classList.contains('is-open')) {
                    commitDiscountEditor();
                }

                let isValid = true;

                // Validate the single job-card-level payment method
                const paymentSelect = document.getElementById('payment_type_id');
                if (!paymentSelect || !paymentSelect.value) {
                    if (window.showToast) window.showToast('Please select a payment method', 'danger');
                    isValid = false;
                }

                // Validate all service items
                serviceItems.forEach((item, index) => {
                    const serviceSelect = item.querySelector('.service-select');
                    const subcategorySelect = item.querySelector('.subcategory-select');
                    const checkedStaff = item.querySelectorAll('.staff-checkbox:checked');
                    const amountInput = item.querySelector('.amount-input');

                    if (!serviceSelect || !serviceSelect.value) {
                        if (window.showToast) window.showToast(`Service item ${index + 1}: Service is required`, 'danger');
                        isValid = false;
                    }
                    if (!subcategorySelect || !subcategorySelect.value) {
                        if (window.showToast) window.showToast(`Service item ${index + 1}: Subcategory is required`, 'danger');
                        isValid = false;
                    }
                    if (!checkedStaff || checkedStaff.length === 0) {
                        if (window.showToast) window.showToast(`Service item ${index + 1}: At least one staff member is required`, 'danger');
                        isValid = false;
                    }
                    if (!amountInput || !amountInput.value || parseFloat(amountInput.value) < 0) {
                        if (window.showToast) window.showToast(`Service item ${index + 1}: Valid amount is required`, 'danger');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }

                // Renumber items before submission to ensure correct indexing
                renumberServiceItems();
            });




           function toggleJobCardActions(button) {
    const wrapper = button.closest('.pli-action-menu-wrap');
    const currentRow = button.closest('.premium-list-item');

    // Close all other open menus
    document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(menu => {
        if (menu !== wrapper) {
            menu.classList.remove('is-open');

            const menuButton = menu.querySelector('.pli-action-dots');

            if (menuButton) {
                menuButton.classList.remove('is-open');
                menuButton.setAttribute('aria-expanded', 'false');
            }

            const row = menu.closest('.premium-list-item');

            if (row) {
                row.classList.remove('action-menu-row-open');
            }
        }
    });

    const isOpen = wrapper.classList.toggle('is-open');

    button.classList.toggle('is-open', isOpen);
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    // Bring the current row above all other rows
    if (currentRow) {
        currentRow.classList.toggle('action-menu-row-open', isOpen);
    }
}


function closeJobCardActions(element) {
    const wrapper = element.closest('.pli-action-menu-wrap');

    if (!wrapper) {
        return;
    }

    wrapper.classList.remove('is-open');

    const button = wrapper.querySelector('.pli-action-dots');

    if (button) {
        button.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');
    }

    const row = wrapper.closest('.premium-list-item');

    if (row) {
        row.classList.remove('action-menu-row-open');
    }
}


// Close when clicking outside
document.addEventListener('click', function (event) {

    if (!event.target.closest('.pli-action-menu-wrap')) {

        document
            .querySelectorAll('.pli-action-menu-wrap.is-open')
            .forEach(wrapper => {

                wrapper.classList.remove('is-open');

                const button =
                    wrapper.querySelector('.pli-action-dots');

                if (button) {
                    button.classList.remove('is-open');
                    button.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }

                const row =
                    wrapper.closest('.premium-list-item');

                if (row) {
                    row.classList.remove(
                        'action-menu-row-open'
                    );
                }
            });
    }
});


// Escape key
document.addEventListener('keydown', function (event) {

    if (event.key !== 'Escape') {
        return;
    }

    document
        .querySelectorAll('.pli-action-menu-wrap.is-open')
        .forEach(wrapper => {

            wrapper.classList.remove('is-open');

            const button =
                wrapper.querySelector('.pli-action-dots');

            if (button) {
                button.classList.remove('is-open');
                button.setAttribute(
                    'aria-expanded',
                    'false'
                );
            }

            const row =
                wrapper.closest('.premium-list-item');

            if (row) {
                row.classList.remove(
                    'action-menu-row-open'
                );
            }
        });
});



// ============================================================
// JOB CARD PAYMENT METHOD
// Single payment method for the whole job card
// ============================================================
function setupPaymentMethodEvents() {
    const paymentContainer = document.getElementById('jobCardPaymentCards');
    const paymentHidden = document.getElementById('payment_type_id');

    if (!paymentContainer || !paymentHidden) {
        return;
    }

    // Prevent duplicate event binding
    if (paymentContainer._paymentEventsInitialized) {
        return;
    }

    paymentContainer._paymentEventsInitialized = true;

    // Handle payment method selection
    paymentContainer.addEventListener('change', function (e) {
        if (!e.target.matches('input[name="payment_type_choice"]')) {
            return;
        }

        const selectedId = String(e.target.value);

        // Store selected payment type in hidden field
        paymentHidden.value = selectedId;

        // Update selected UI
        paymentContainer
            .querySelectorAll('.job-card-payment-card')
            .forEach(card => {
                card.classList.remove('is-selected');
            });

        const selectedCard = e.target.closest('.job-card-payment-card');

        if (selectedCard) {
            selectedCard.classList.add('is-selected');
        }
    });
}


// ============================================================
// SET PAYMENT METHOD PROGRAMMATICALLY
// Used for Add / Edit modal
// ============================================================
function setJobCardPaymentMethod(paymentTypeId) {
    const paymentContainer = document.getElementById('jobCardPaymentCards');
    const paymentHidden = document.getElementById('payment_type_id');

    if (!paymentContainer || !paymentHidden) {
        return;
    }

    const value = paymentTypeId ? String(paymentTypeId) : '';

    // Update hidden field
    paymentHidden.value = value;

    // Reset all cards
    paymentContainer
        .querySelectorAll('.job-card-payment-card')
        .forEach(card => {
            card.classList.remove('is-selected');

            const radio = card.querySelector('input[name="payment_type_choice"]');

            if (radio) {
                radio.checked = false;
            }
        });

    // Select matching card
    if (value) {
        const selectedRadio = paymentContainer.querySelector(
            `input[name="payment_type_choice"][value="${value}"]`
        );

        if (selectedRadio) {
            selectedRadio.checked = true;

            const selectedCard = selectedRadio.closest('.job-card-payment-card');

            if (selectedCard) {
                selectedCard.classList.add('is-selected');
            }
        }
    }
}
        </script>
    @endpush

@endsection
