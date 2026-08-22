@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/module-lists.css') }}">
    <style>
        /* Product purchase list — 8-column grid (multi-product aware) */
        .product-purchase-page .premium-list--purchases {
            --purchase-grid:
                44px
                minmax(190px, 1.35fr)
                minmax(140px, 1fr)
                minmax(110px, .85fr)
                100px
                135px
                115px
                70px;
        }

        .product-purchase-page .premium-list--purchases .premium-list-head,
        .product-purchase-page .premium-list--purchases .premium-list-item {
            grid-template-columns: var(--purchase-grid) !important;
            min-width: 1180px !important;
        }

        .product-purchase-page .premium-list--purchases .premium-list-head {
            gap: 12px !important;
        }

        .product-purchase-page .premium-list--purchases .premium-list-item {
            gap: 12px !important;
            min-height: 68px;
        }

        .product-purchase-page .purchase-customer-cell,
        .product-purchase-page .purchase-date-cell,
        .product-purchase-page .purchase-qty-cell,
        .product-purchase-page .purchase-amount-cell,
        .product-purchase-page .purchase-payment-cell {
            display:flex;
            align-items:center;
            justify-content:center;
            min-width:0;
        }

        .product-purchase-page .purchase-customer-cell {
            flex-direction: column;
            gap: 2px;
        }

        .product-purchase-page .purchase-number-code {
            font-size: .6875rem;
            font-weight: 600;
            color: #0EA5E9;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .product-purchase-page .purchase-date-text {
            font-size: .82rem;
            font-weight: 600;
            color: #64748B;
            white-space: nowrap;
        }

        .product-purchase-page .purchase-customer-name {
            font-size:.82rem;
            font-weight:700;
            color:#0F172A;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            max-width:100%;
        }

        .product-purchase-page .purchase-product {
            display:block;
            max-width:100%;
            min-width:0;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:.82rem;
            font-weight:700;
            color:#0F172A;
        }

        .product-purchase-page .purchase-quantity-pill {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:28px;
            padding:5px 10px;
            border-radius:999px;
            background:#EFF6FF;
            border:1px solid #DBEAFE;
            color:#2563EB;
            font-size:.68rem;
            font-weight:800;
            white-space:nowrap;
        }

        .product-purchase-page .purchase-list-actions {
            display:flex !important;
            align-items:center !important;
            justify-content:center !important;
            overflow:visible !important;
        }
        .product-purchase-page .premium-list--purchases .purchase-list-actions {
            grid-column: 8 !important;
            grid-row: 1 !important;
        }
        .product-purchase-page .premium-list--purchases .premium-list-head .pli-head-cell:last-child {
            grid-column: 8 !important;
            grid-row: 1 !important;
            display:flex !important;
            align-items:center !important;
            justify-content:center !important;
        }

        /* Purchase modal follows the Job Card builder shell exactly
           (same modal-xl / 960px sizing as Add Job Card) */
        .product-purchase-page .purchase-builder-modal .modal-content {
            max-height: calc(100vh - 30px);
        }

        .product-purchase-page .purchase-builder-modal .job-card-builder-scroll-area {
            gap: 22px;
        }

        .product-purchase-page .purchase-builder-modal .job-card-builder-fixed-bottom {
            gap: 14px;
        }

        /* Read-only category/subcategory chips inside a product item row */
        .product-purchase-page .job-card-input-box.is-readonly {
            background: #F8FAFC;
        }

        .product-purchase-page .job-card-input-box .purchase-readonly-text {
            font-size: .88rem;
            font-weight: 500;
            color: #64748B;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-purchase-page .job-card-item-field-col--qty,
        .product-purchase-page .job-card-item-field-col--price {
            flex: 0.75;
        }

        .product-purchase-page .job-card-item-field-col--category,
        .product-purchase-page .job-card-item-field-col--subcategory {
            flex: 0.9;
        }

        /* ------------------------------------------------------------
           PAYMENT METHOD PILLS (Table List View — same as Job Card)
           ------------------------------------------------------------ */
        .product-purchase-page .payment-type-pill {
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

        .product-purchase-page .payment-type-pill:hover {
            transform: translateY(-1px);
            box-shadow:
                0 4px 10px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .product-purchase-page .payment-type-pill i {
            font-size: 0.88rem;
            line-height: 1;
        }

        /* UPI */
        .product-purchase-page .payment-type-upi {
            color: #6366F1;
            background: linear-gradient(135deg, #F5F3FF 0%, #EDE9FE 100%);
            border-color: #DDD6FE;
        }

        /* Cash */
        .product-purchase-page .payment-type-cash {
            color: #16A34A;
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            border-color: #BBF7D0;
        }

        /* Card */
        .product-purchase-page .payment-type-card {
            color: #7C3AED;
            background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%);
            border-color: #E9D5FF;
        }

        /* EC / Wallet */
        .product-purchase-page .payment-type-ec,
        .product-purchase-page .payment-type-wallet {
            color: #0F172A;
            background: #FFFFFF;
            border-color: #E2E8F0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Bank */
        .product-purchase-page .payment-type-bank {
            color: #2563EB;
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            border-color: #BFDBFE;
        }

        /* Net Banking */
        .product-purchase-page .payment-type-net-banking {
            color: #0891B2;
            background: linear-gradient(135deg, #ECFEFF 0%, #CFFAFE 100%);
            border-color: #A5F3FC;
        }

        /* Fallback / Default */
        .product-purchase-page .payment-type-default {
            color: #64748B;
            background: #F8FAFC;
            border-color: #E2E8F0;
        }

        /* ------------------------------------------------------------
           PAYMENT METHOD CARDS — matching 2nd image UI reference
           ------------------------------------------------------------ */
        .product-purchase-page .job-card-payment-cards {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 10px !important;
            margin-top: 4px !important;
        }

        .product-purchase-page .job-card-payment-card {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 10px !important;
            min-height: 48px !important;
            padding: 9px 16px 9px 12px !important;
            border: 1.5px solid #E2E8F0 !important;
            border-radius: 12px !important;
            background: #FFFFFF !important;
            color: #1E293B !important;
            font-size: 0.88rem !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            user-select: none !important;
            transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }

        .product-purchase-page .job-card-payment-card:hover {
            border-color: #C4B5FD !important;
            background: #FAFAFA !important;
        }

        /* Fully hide the native radio button — no trace */
        .product-purchase-page .job-card-payment-card input[type="radio"] {
            position: absolute !important;
            opacity: 0 !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            pointer-events: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }

        /* Custom square checkbox — default (unchecked) */
        .product-purchase-page .job-card-payment-card .payment-card-check {
            flex-shrink: 0 !important;
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            border-radius: 6px !important;
            border: 1.5px solid #CBD5E1 !important;
            background: #F8FAFC !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.18s ease !important;
            overflow: hidden !important;
        }

        /* Hide the check icon by default */
        .product-purchase-page .job-card-payment-card .payment-card-check .bi-check-lg {
            display: none !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            color: #FFFFFF !important;
        }

        /* Icon badge */
        .product-purchase-page .job-card-payment-card .payment-card-icon {
            flex-shrink: 0 !important;
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            border-radius: 7px !important;
            background: #F1F5F9 !important;
            color: #64748B !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.9rem !important;
            transition: background 0.18s ease, color 0.18s ease !important;
        }

        /* Label text */
        .product-purchase-page .job-card-payment-card .payment-card-text {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            color: #334155 !important;
            letter-spacing: -0.01em !important;
            white-space: nowrap !important;
        }

        /* ── SELECTED STATE ── */
        .product-purchase-page .job-card-payment-card.is-selected {
            border-color: #8B5CF6 !important;
            background: linear-gradient(135deg, #F5F3FF 0%, #EDE9FE 100%) !important;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.14) !important;
        }

        .product-purchase-page .job-card-payment-card.is-selected .payment-card-check {
            border-color: #7C3AED !important;
            background: #7C3AED !important;
        }

        /* Show check icon when selected */
        .product-purchase-page .job-card-payment-card.is-selected .payment-card-check .bi-check-lg {
            display: inline-block !important;
        }

        .product-purchase-page .job-card-payment-card.is-selected .payment-card-text {
            color: #5B21B6 !important;
        }

        .product-purchase-page .job-card-payment-card.is-selected .payment-card-icon {
            background: #DDD6FE !important;
            color: #6D28D9 !important;
        }
    </style>
@endpush

@section('title', 'Product Purchases')
@section('page-title', 'Product Purchases')

@section('content')
<div class="job-card-page product-purchase-page management-page">
    @include('partials.mgmt-top-actions', ['addLabel' => 'Add Purchase', 'addModal' => '#purchaseModal', 'addOnclick' => 'openPurchaseModal()', 'filterModule' => 'purchases', 'filterRoute' => route('product-purchases.index'), 'filterData' => ['customers' => $customers, 'products' => $products, 'paymentTypes' => $paymentTypes]])

    <div class="mgmt-stats-grid mgmt-stats-grid--4">
        @include('partials.mgmt-stat-card', ['theme' => 'cyan', 'icon' => 'clipboard-cyan', 'label' => 'Total Purchases', 'value' => $totalPurchases, 'subtext' => 'All records', 'sparkColor' => '#0EA5E9', 'trend' => '0.0', 'trendUp' => true])
        @include('partials.mgmt-stat-card', ['theme' => 'orange', 'icon' => 'rupee-green', 'label' => 'Total Spent', 'value' => '₹' . number_format($totalSpent, 2), 'subtext' => 'All purchases', 'sparkColor' => '#F59E0B', 'trend' => '0.0', 'trendUp' => true])
        @include('partials.mgmt-stat-card', ['theme' => 'blue', 'icon' => 'box-blue', 'label' => 'Total Products', 'value' => $totalProducts, 'subtext' => 'Products purchased', 'sparkColor' => '#3B82F6', 'trend' => '0.0', 'trendUp' => true])
        @include('partials.mgmt-stat-card', ['theme' => 'green', 'icon' => 'coins-green', 'label' => 'This Month', 'value' => '₹' . number_format($monthSpent, 2), 'subtext' => now()->format('F Y'), 'sparkColor' => '#22C55E', 'trend' => '0.0', 'trendUp' => true])
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="content-card">
        <div class="content-card-header">
            <div><h2>Product Purchase List</h2><span>{{ $purchases->total() }} total purchases</span></div>
            <div class="content-card-header-actions"><form method="GET" action="{{ route('product-purchases.index') }}" class="job-card-search"><input type="hidden" name="date_from" value="{{ $dateFrom }}"><input type="hidden" name="date_to" value="{{ $dateTo }}"><input type="hidden" name="customer_id" value="{{ $customerId }}"><input type="hidden" name="product_id" value="{{ $productId }}"><input type="hidden" name="payment_type_id" value="{{ $paymentTypeId }}"><div class="search-box"><i class="bi bi-search"></i><input type="text" name="search" value="{{ $search }}" placeholder="Search purchases..."><button class="border-0 bg-transparent" aria-label="Search"><i class="bi bi-search"></i></button></div></form></div>
        </div>

        @if($purchases->count())
        @php $listStart = ($purchases->currentPage() - 1) * $purchases->perPage(); @endphp
        <div class="premium-list premium-list--jobs premium-list--purchases premium-list--feed premium-list--compact premium-list--mgmt">
            <div class="premium-list-head">
                <span class="pli-head-cell col-center">#</span><span class="pli-head-cell col-left">Purchase</span><span class="pli-head-cell col-center">Customer</span><span class="pli-head-cell col-center">Date</span><span class="pli-head-cell col-center">Quantity</span><span class="pli-head-cell col-center">Payment Method</span><span class="pli-head-cell col-center">Amount</span><span class="pli-head-cell col-center">Actions</span>
            </div>
            @foreach($purchases as $purchase)
            @php
                $paymentTypeName = $purchase->paymentType?->name;
                $paymentTypeKeyLower = strtolower(trim($paymentTypeName ?? ''));
                $paymentKey = match (true) {
                    str_contains($paymentTypeKeyLower, 'upi') => 'upi',
                    str_contains($paymentTypeKeyLower, 'cash') => 'cash',
                    str_contains($paymentTypeKeyLower, 'card') => 'card',
                    str_contains($paymentTypeKeyLower, 'bank') => 'bank',
                    str_contains($paymentTypeKeyLower, 'net') => 'net-banking',
                    str_contains($paymentTypeKeyLower, 'ec') => 'ec',
                    str_contains($paymentTypeKeyLower, 'wallet') => 'ec',
                    default => strtolower(str_replace(' ', '-', $paymentTypeName ?? 'default')),
                };
                $paymentIcon = match (true) {
                    str_contains($paymentTypeKeyLower, 'upi') => 'bi-phone',
                    str_contains($paymentTypeKeyLower, 'cash') => 'bi-cash',
                    str_contains($paymentTypeKeyLower, 'card') => 'bi-credit-card',
                    str_contains($paymentTypeKeyLower, 'bank') => 'bi-bank',
                    str_contains($paymentTypeKeyLower, 'net') => 'bi-globe2',
                    default => 'bi-wallet2',
                };
                $items = $purchase->items;
                $firstProductName = $items->first()?->product?->product_name ?? '—';
                $extraProducts = $items->count() - 1;
                $totalQty = $items->sum('quantity');
            @endphp
            <article class="premium-list-item" id="purchase-row-{{ $purchase->id }}">
                <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>
                <div class="pli-col col-left">
                    <div class="pli-name-cell">
                        <div class="pli-icon pli-icon--cyan"><i class="bi bi-cart-check-fill"></i></div>
                        <div class="pli-name-stack">
                            <div class="d-flex align-items-center gap-1">
                                <span class="pli-title" title="{{ $items->pluck('product.product_name')->filter()->join(', ') }}">{{ $firstProductName }}</span>
                                @if($extraProducts > 0)
                                    <span class="badge rounded-pill bg-light text-primary border" title="{{ $items->pluck('product.product_name')->filter()->join(', ') }}" style="font-size:.68rem;font-weight:600;cursor:help;">+{{ $extraProducts }}</span>
                                @endif
                            </div>
                            <span class="purchase-number-code">{{ $purchase->purchase_number }}</span>
                        </div>
                    </div>
                </div>
                <div class="pli-col purchase-customer-cell"><span class="purchase-customer-name">{{ $purchase->customer?->name ?? '—' }}</span></div>
                <div class="pli-col purchase-date-cell"><span class="purchase-date-text">{{ optional($purchase->purchase_date)->format('d/m/Y') }}</span></div>
                <div class="pli-col purchase-qty-cell"><span class="purchase-quantity-pill">{{ $totalQty }} units</span></div>
                <div class="pli-col purchase-payment-cell"><span class="payment-type-pill payment-type-{{ $paymentKey ?: 'default' }}"><i class="bi {{ $paymentIcon }}"></i>{{ $paymentTypeName ?? '—' }}</span></div>
                <div class="pli-col purchase-amount-cell"><span class="module-amount">₹{{ number_format($purchase->total_amount, 2) }}</span></div>
                <div class="pli-col pli-col-actions purchase-list-actions">
                    <div class="pli-action-menu-wrap">
                        <button type="button" class="pli-action-dots" aria-label="Purchase actions" aria-expanded="false" onclick="togglePurchaseActions(this)">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <div class="pli-action-popover">
                            <button type="button" class="pli-popover-action" onclick='openPurchaseDetailsModal(@json($purchase)); closePurchaseActions(this)'>
                                <span class="pli-popover-icon pli-popover-icon--view"><i class="bi bi-eye"></i></span>
                                <span>View Details</span>
                            </button>
                            <button type="button" class="pli-popover-action" onclick='openPurchaseModal(@json($purchase)); closePurchaseActions(this)'>
                                <span class="pli-popover-icon pli-popover-icon--edit"><i class="bi bi-pencil"></i></span>
                                <span>Edit Purchase</span>
                            </button>
                            <div class="pli-popover-divider"></div>
                            <form method="POST" action="{{ route('product-purchases.destroy', $purchase) }}" onsubmit="return confirm('Delete this purchase?')">
                                @csrf
                                @method('DELETE')
                                <button class="pli-popover-action pli-popover-action--danger">
                                    <span class="pli-popover-icon pli-popover-icon--delete"><i class="bi bi-trash3"></i></span>
                                    <span>Delete Purchase</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @include('partials.pagination-bar', ['paginator' => $purchases])
        @else
        <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-cart-check"></i></div><h3>No purchases found</h3><p>Record your first product purchase transaction.</p><button class="btn btn-primary mt-2" onclick="openPurchaseModal()"><i class="bi bi-plus-lg"></i> Add Purchase</button></div>
        @endif
    </div>
</div>

{{-- ========================================================= --}}
{{-- ADD / EDIT PRODUCT PURCHASE MODAL — Job Card shell reused --}}
{{-- ========================================================= --}}
<div class="modal fade premium-modal job-card-builder-modal purchase-builder-modal product-purchase-page" id="purchaseModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 960px;">
        <div class="modal-content">
            <form id="purchaseForm" method="POST" action="{{ route('product-purchases.store') }}" class="job-card-builder-form">
                @csrf
                <input type="hidden" id="purchaseMethod" name="_method">

                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box job-card-modal-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title" id="purchaseTitle">Add Product Purchase</h5>
                            <p class="modal-subtitle" id="purchaseSubtitle">Record a new product purchase.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body job-card-builder-body">
                    <div class="job-card-builder-scroll-area">

                        {{-- Top row: Customer only (Product moved into the item builder below) --}}
                        <div class="job-card-builder-section">
                            <div class="job-card-builder-top-row">
                                <div class="form-field">
                                    <label for="purchaseCustomer" class="form-label">CUSTOMER <span>*</span></label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-person"></i></span>
                                        <select id="purchaseCustomer" name="customer_id" class="no-nice-select" required>
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
                                        <i class="bi bi-chevron-down job-card-select-arrow"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Product Items builder --}}
                        <div class="job-card-builder-section">
                            <div class="job-card-builder-section-header">
                                <div>
                                    <h6 class="job-card-builder-section-title">PRODUCT ITEMS</h6>
                                </div>
                                <button type="button" class="btn-add-service-pill" id="addProductItemBtn">
                                    <i class="bi bi-plus-lg"></i> Add Product
                                </button>
                            </div>

                            <div id="productItemsContainer" class="job-card-service-items-card">
                                {{-- Product item rows rendered here by JS --}}
                            </div>
                        </div>

                    </div>
                    {{-- /.job-card-builder-scroll-area --}}

                    <div class="job-card-builder-fixed-bottom">
                        <div class="job-card-builder-section">
                            <div class="form-field">
                                <label class="form-label">PAYMENT METHOD <span>*</span></label>
                                <input type="hidden" id="purchasePaymentTypeId" name="payment_type_id" value="">

                                <div class="job-card-payment-cards" id="purchasePaymentCards">
                                    @foreach($paymentTypes as $paymentType)
                                        @php
                                            $paymentTypeNameLower = strtolower($paymentType->name);
                                            $paymentCardIcon = match (true) {
                                                str_contains($paymentTypeNameLower, 'upi') => 'bi-phone',
                                                str_contains($paymentTypeNameLower, 'cash') => 'bi-cash',
                                                str_contains($paymentTypeNameLower, 'card') => 'bi-credit-card',
                                                str_contains($paymentTypeNameLower, 'bank') => 'bi-bank',
                                                str_contains($paymentTypeNameLower, 'net') => 'bi-globe2',
                                                default => 'bi-wallet2',
                                            };
                                        @endphp
                                        <label class="job-card-payment-card" data-payment-id="{{ $paymentType->id }}">
                                            <input type="radio" name="purchase_payment_type_choice" value="{{ $paymentType->id }}" aria-label="{{ $paymentType->name }}">
                                            <span class="payment-card-check"><i class="bi bi-check-lg"></i></span>
                                            <span class="payment-card-icon"><i class="bi {{ $paymentCardIcon }}"></i></span>
                                            <span class="payment-card-text">{{ $paymentType->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="job-card-summary-card">
                            <div class="job-card-summary-left">
                                <div class="job-card-summary-calc-icon"><i class="bi bi-calculator"></i></div>
                                <div class="job-card-summary-total-info">
                                    <span class="job-card-summary-total-label">TOTAL AMOUNT</span>
                                    <span class="job-card-summary-total-val" id="purchaseTotalAmount">₹ 0</span>
                                </div>
                                <div class="job-card-services-badge">
                                    <i class="bi bi-box-seam"></i>
                                    <span id="purchaseItemCount">0</span> Items
                                </div>
                            </div>

                            <div class="job-card-summary-middle">
                                <div class="job-card-summary-row">
                                    <span class="job-card-summary-row-label">Subtotal</span>
                                    <span class="job-card-summary-row-val" id="purchaseSubtotal">₹ 0.00</span>
                                </div>
                                <div class="job-card-summary-divider"></div>
                                <div class="job-card-summary-row job-card-summary-row-final">
                                    <span class="job-card-summary-row-label">Total</span>
                                    <span class="job-card-summary-row-val" id="purchaseFinalTotal">₹ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="purchaseSubmit">
                        <i class="bi bi-cart-check"></i> Save Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- PRODUCT PURCHASE DETAILS MODAL — same shell as Job Card --}}
{{-- ========================================================= --}}
<div class="modal fade premium-modal job-card-details-modal" id="purchaseDetailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-box job-card-details-title-icon"><i class="bi bi-cart-check"></i></div>
                    <div class="modal-header-content">
                        <h5 class="modal-title">Product Purchase</h5>
                        <p class="modal-subtitle">Purchase details & item breakdown</p>
                    </div>
                </div>
                <div class="job-card-details-header-actions">
                    <button type="button" class="job-card-detail-tool" onclick="window.print()" title="Print" aria-label="Print"><i class="bi bi-printer"></i></button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="jcd-details-grid" aria-label="Product purchase summary">
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Purchase Number</span>
                            <strong class="jcd-detail-value" id="purchaseDetailsNumber">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Date</span>
                            <strong class="jcd-detail-value" id="purchaseDetailsDate">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Customer</span>
                            <strong class="jcd-detail-value" id="purchaseDetailsCustomer">—</strong>
                        </div>
                    </div>
                    <div class="jcd-detail-item">
                        <div class=""></div>
                        <div class="jcd-detail-text">
                            <span class="jcd-detail-label">Payment Method</span>
                            <strong class="jcd-detail-value" id="purchaseDetailsPaymentType">—</strong>
                        </div>
                    </div>
                </div>
                <div class="job-card-details-invoice">
                    <div class="job-card-details-invoice-head">
                        <span>#</span><span>Product</span><span>Qty × Price</span><span>Amount (₹)</span>
                    </div>
                    <div id="purchaseDetailsInvoiceItems">
                        {{-- Product item lines populated dynamically --}}
                    </div>
                </div>
                <div class="jcd-totals-card">
                    <div class="jcd-totals-row">
                        <span>Subtotal</span><strong id="purchaseDetailsSubtotal">₹ 0.00</strong>
                    </div>
                    <div class="jcd-totals-divider"></div>
                    <div class="jcd-totals-row jcd-totals-row--final">
                        <span class="jcd-totals-label">Total Amount</span><strong id="purchaseDetailsTotal">₹ 0.00</strong>
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
    $purchaseProductsForJs = [];
    foreach ($products as $product) {
        $purchaseProductsForJs[] = [
            'id' => $product->id,
            'product_name' => $product->product_name,
            'category' => $product->category,
            'subcategory' => $product->subcategory,
        ];
    }
@endphp

@push('scripts')
<script>
const purchaseModalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseModal'));
const purchaseProductsData = @json($purchaseProductsForJs);

function formatPurchaseCurrency(value) {
    return `₹ ${Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// ---------------------------------------------------------------
// PRODUCT ITEM ROWS
// ---------------------------------------------------------------

function productItemOptionsHtml(selectedId) {
    let html = '<option value="">Select product</option>';
    purchaseProductsData.forEach(p => {
        const selected = String(p.id) === String(selectedId) ? 'selected' : '';
        html += `<option value="${p.id}" data-category="${p.category ?? ''}" data-subcategory="${p.subcategory ?? ''}" ${selected}>${p.product_name}</option>`;
    });
    return html;
}

function addProductItemRow(container, itemData = null) {
    const itemIndex = container.querySelectorAll('.job-card-service-item').length;
    const itemId = 'product-item-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

    const productId = itemData?.product_id ?? '';
    const quantity = itemData?.quantity ?? '';
    const unitPrice = itemData?.unit_price ?? '';
    const category = itemData?.product?.category ?? '';
    const subcategory = itemData?.product?.subcategory ?? '';

    const itemHTML = `
        <div class="job-card-service-item" data-item-id="${itemId}">
            <div class="job-card-item-field-col">
                <label class="job-card-item-label">PRODUCT</label>
                <div class="job-card-input-box">
                    <select class="product-select" data-item-id="${itemId}" name="products[${itemIndex}][product_id]" required>
                        ${productItemOptionsHtml(productId)}
                    </select>
                    <i class="bi bi-chevron-down job-card-select-arrow"></i>
                </div>
            </div>

            <div class="job-card-item-field-col job-card-item-field-col--category">
                <label class="job-card-item-label">CATEGORY</label>
                <div class="job-card-input-box is-readonly">
                    <span class="purchase-readonly-text product-category-display" data-item-id="${itemId}">${category || '—'}</span>
                </div>
            </div>

            <div class="job-card-item-field-col job-card-item-field-col--subcategory">
                <label class="job-card-item-label">SUBCATEGORY</label>
                <div class="job-card-input-box is-readonly">
                    <span class="purchase-readonly-text product-subcategory-display" data-item-id="${itemId}">${subcategory || '—'}</span>
                </div>
            </div>

            <div class="job-card-item-field-col job-card-item-field-col--qty">
                <label class="job-card-item-label">QUANTITY</label>
                <div class="job-card-input-box">
                    <input type="number" class="quantity-input" data-item-id="${itemId}" name="products[${itemIndex}][quantity]" min="1" step="1" placeholder="0" value="${quantity}" required>
                </div>
            </div>

            <div class="job-card-item-field-col job-card-item-field-col--price">
                <label class="job-card-item-label">UNIT PRICE (₹)</label>
                <div class="job-card-input-box job-card-amount-box">
                    <span class="job-card-currency-symbol">₹</span>
                    <input type="number" class="unit-price-input" data-item-id="${itemId}" name="products[${itemIndex}][unit_price]" min="0" step="0.01" placeholder="0.00" value="${unitPrice}" required>
                </div>
            </div>

            <button type="button" class="job-card-item-delete-btn" data-item-id="${itemId}" title="Remove product" aria-label="Remove product">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </div>
    `;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = itemHTML.trim();
    container.appendChild(wrapper.firstElementChild);
}

function renumberProductItems() {
    const container = document.getElementById('productItemsContainer');
    const items = container.querySelectorAll('.job-card-service-item');

    items.forEach((item, index) => {
        item.querySelectorAll('input, select').forEach(field => {
            const nameAttr = field.getAttribute('name');
            if (nameAttr) {
                field.setAttribute('name', nameAttr.replace(/\[\d+\]/, `[${index}]`));
            }
        });
    });
}

function updatePurchaseTotals() {
    const container = document.getElementById('productItemsContainer');
    let subtotal = 0;
    let itemCount = 0;

    container.querySelectorAll('.job-card-service-item').forEach(item => {
        const quantity = Number(item.querySelector('.quantity-input')?.value) || 0;
        const price = Number(item.querySelector('.unit-price-input')?.value) || 0;
        subtotal += quantity * price;
        itemCount += 1;
    });

    document.getElementById('purchaseTotalAmount').textContent = formatPurchaseCurrency(subtotal);
    document.getElementById('purchaseSubtotal').textContent = formatPurchaseCurrency(subtotal);
    document.getElementById('purchaseFinalTotal').textContent = formatPurchaseCurrency(subtotal);
    document.getElementById('purchaseItemCount').textContent = itemCount;
}

function setupProductItemEvents() {
    const container = document.getElementById('productItemsContainer');
    if (!container || container._eventsInitialized) return;
    container._eventsInitialized = true;

    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const item = e.target.closest('.job-card-service-item');
            const selectedOption = e.target.options[e.target.selectedIndex];
            const categoryDisplay = item.querySelector('.product-category-display');
            const subcategoryDisplay = item.querySelector('.product-subcategory-display');

            categoryDisplay.textContent = selectedOption?.dataset.category || '—';
            subcategoryDisplay.textContent = selectedOption?.dataset.subcategory || '—';
        }

        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
            updatePurchaseTotals();
        }
    });

    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
            updatePurchaseTotals();
        }
    });

    container.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.job-card-item-delete-btn');
        if (!deleteBtn) return;

        e.preventDefault();
        const item = deleteBtn.closest('.job-card-service-item');
        const allItems = container.querySelectorAll('.job-card-service-item');

        if (allItems.length > 1) {
            item.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
            item.style.opacity = '0';
            item.style.transform = 'scale(0.95)';
            setTimeout(() => {
                item.remove();
                renumberProductItems();
                updatePurchaseTotals();
            }, 180);
        } else {
            const select = item.querySelector('.product-select');
            if (select) select.value = '';
            item.querySelector('.product-category-display').textContent = '—';
            item.querySelector('.product-subcategory-display').textContent = '—';
            item.querySelector('.quantity-input').value = '';
            item.querySelector('.unit-price-input').value = '';
            updatePurchaseTotals();
        }
    });

    const addBtn = document.getElementById('addProductItemBtn');
    if (addBtn) {
        addBtn.onclick = function (e) {
            e.preventDefault();
            addProductItemRow(container);
            renumberProductItems();
            updatePurchaseTotals();
        };
    }
}

function initializeProductItemBuilder(items) {
    const container = document.getElementById('productItemsContainer');
    container.innerHTML = '';

    if (items && items.length > 0) {
        items.forEach(item => addProductItemRow(container, item));
    } else {
        addProductItemRow(container);
    }

    renumberProductItems();
    updatePurchaseTotals();
    setupProductItemEvents();
}

// ---------------------------------------------------------------
// PAYMENT METHOD (payment_types table — same source as Job Card)
// ---------------------------------------------------------------

function setPurchasePaymentMethod(paymentTypeId) {
    const hidden = document.getElementById('purchasePaymentTypeId');
    const container = document.getElementById('purchasePaymentCards');
    if (!hidden || !container) return;

    const value = paymentTypeId ? String(paymentTypeId) : '';
    hidden.value = value;

    container.querySelectorAll('.job-card-payment-card').forEach(card => {
        card.classList.remove('is-selected');
        const radio = card.querySelector('input[name="purchase_payment_type_choice"]');
        if (radio) radio.checked = false;
    });

    if (value) {
        const selectedRadio = container.querySelector(
            `input[name="purchase_payment_type_choice"][value="${value}"]`
        );

        if (selectedRadio) {
            selectedRadio.checked = true;
            const selectedCard = selectedRadio.closest('.job-card-payment-card');
            if (selectedCard) selectedCard.classList.add('is-selected');
        }
    }
}

function setupPurchasePaymentEvents() {
    const container = document.getElementById('purchasePaymentCards');
    const hidden = document.getElementById('purchasePaymentTypeId');
    if (!container || !hidden) return;
    if (container._paymentEventsInitialized) return;
    container._paymentEventsInitialized = true;

    container.addEventListener('change', function (event) {
        if (!event.target.matches('input[name="purchase_payment_type_choice"]')) return;

        const selectedId = String(event.target.value);
        hidden.value = selectedId;

        container.querySelectorAll('.job-card-payment-card').forEach(card => {
            card.classList.remove('is-selected');
        });

        const selectedCard = event.target.closest('.job-card-payment-card');
        if (selectedCard) selectedCard.classList.add('is-selected');
    });
}

// ---------------------------------------------------------------
// OPEN MODAL (ADD / EDIT)
// ---------------------------------------------------------------

function openPurchaseModal(purchase = null) {
    const form = document.getElementById('purchaseForm');
    form.reset();

    document.getElementById('purchaseMethod').value = purchase ? 'PUT' : '';
    form.action = purchase
        ? `/product-purchases/${purchase.id}`
        : `{{ route('product-purchases.store') }}`;

    document.getElementById('purchaseTitle').textContent =
        purchase ? 'Edit Product Purchase' : 'Add Product Purchase';

    document.getElementById('purchaseSubtitle').textContent =
        purchase ? 'Update product purchase details.' : 'Record a new product purchase.';

    document.getElementById('purchaseSubmit').innerHTML = purchase
        ? '<i class="bi bi-check2-circle"></i> Update Purchase'
        : '<i class="bi bi-cart-check"></i> Save Purchase';

    document.getElementById('purchaseCustomer').value = purchase?.customer_id ?? '';

    setPurchasePaymentMethod(purchase?.payment_type_id ?? '');

    initializeProductItemBuilder(purchase?.items ?? []);

    purchaseModalInstance.show();
}

// ---------------------------------------------------------------
// OPEN DETAILS MODAL (VIEW DETAILS)
// ---------------------------------------------------------------

function openPurchaseDetailsModal(purchase) {
    if (!purchase) return;

    const customer = purchase.customer;
    const customerText = customer
        ? (customer.mobile_number ? `${customer.name} (${customer.mobile_number})` : customer.name)
        : '—';

    const paymentTypeName = purchase.payment_type?.name || purchase.paymentType?.name || '—';

    let formattedDate = '—';
    if (purchase.purchase_date) {
        const parts = purchase.purchase_date.split(/[-T ]/);
        if (parts.length >= 3) {
            formattedDate = `${parts[2].slice(0, 2)}/${parts[1]}/${parts[0]}`;
        }
    } else if (purchase.created_at) {
        formattedDate = new Date(purchase.created_at).toLocaleDateString('en-GB');
    }

    const items = purchase.items || [];
    let subtotal = 0;
    let invoiceItemsHTML = '';

    items.forEach((item, index) => {
        const productName = item.product?.product_name || '—';
        const category = item.product?.category;
        const subcategory = item.product?.subcategory;
        const catSub = [category, subcategory].filter(Boolean).join(' • ');

        const qty = Number(item.quantity) || 0;
        const unitPrice = Number(item.unit_price) || 0;
        const lineTotal = Number(item.total_price) || (qty * unitPrice);
        subtotal += lineTotal;

        const formattedUnitPrice = unitPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const formattedLineTotal = lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        invoiceItemsHTML += `
            <div class="job-card-details-invoice-line">
                <span><span class="jcd-invoice-num-badge">${index + 1}</span></span>
                <span>
                    <strong style="color:#0F172A;font-weight:700;">${productName}</strong>
                    ${catSub ? `<div style="font-size:0.75rem;color:#64748B;font-weight:500;margin-top:2px;">${catSub}</div>` : ''}
                </span>
                <span style="color:#475569;font-weight:600;">${qty} × ₹${formattedUnitPrice}</span>
                <span style="color:#0F172A;font-weight:700;">₹${formattedLineTotal}</span>
            </div>
        `;
    });

    if (items.length === 0) {
        invoiceItemsHTML = `<div class="p-3 text-center text-muted" style="font-size:0.85rem;">No product items found.</div>`;
    }

    const totalAmount = Number(purchase.total_amount) || subtotal;
    const formattedTotal = totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('purchaseDetailsNumber').textContent = purchase.purchase_number || '—';
    document.getElementById('purchaseDetailsDate').textContent = formattedDate;
    document.getElementById('purchaseDetailsCustomer').textContent = customerText;
    document.getElementById('purchaseDetailsPaymentType').textContent = paymentTypeName;
    document.getElementById('purchaseDetailsInvoiceItems').innerHTML = invoiceItemsHTML;
    document.getElementById('purchaseDetailsSubtotal').textContent = `₹ ${formattedTotal}`;
    document.getElementById('purchaseDetailsTotal').textContent = `₹ ${formattedTotal}`;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseDetailsModal')).show();
}

// ---------------------------------------------------------------
// FORM VALIDATION
// ---------------------------------------------------------------

document.getElementById('purchaseForm').addEventListener('submit', function (e) {
    let isValid = true;

    const customer = document.getElementById('purchaseCustomer');
    if (!customer.value) {
        if (window.showToast) window.showToast('Please select a customer', 'danger');
        isValid = false;
    }

    const payment = document.getElementById('purchasePaymentTypeId');
    if (!payment.value) {
        if (window.showToast) window.showToast('Please select a payment method', 'danger');
        isValid = false;
    }

    const items = document.querySelectorAll('#productItemsContainer .job-card-service-item');
    if (items.length === 0) {
        if (window.showToast) window.showToast('Please add at least one product', 'danger');
        isValid = false;
    }

    items.forEach((item, index) => {
        const product = item.querySelector('.product-select');
        const quantity = item.querySelector('.quantity-input');
        const unitPrice = item.querySelector('.unit-price-input');

        if (!product || !product.value) {
            if (window.showToast) window.showToast(`Product item ${index + 1}: Product is required`, 'danger');
            isValid = false;
        }
        if (!quantity || !quantity.value || parseInt(quantity.value, 10) < 1) {
            if (window.showToast) window.showToast(`Product item ${index + 1}: Valid quantity is required`, 'danger');
            isValid = false;
        }
        if (!unitPrice || unitPrice.value === '' || parseFloat(unitPrice.value) < 0) {
            if (window.showToast) window.showToast(`Product item ${index + 1}: Valid unit price is required`, 'danger');
            isValid = false;
        }
    });

    if (!isValid) {
        e.preventDefault();
        return false;
    }

    renumberProductItems();
});

setupPurchasePaymentEvents();

// ---------------------------------------------------------------
// ROW ACTION MENU (list view 3-dot popover)
// ---------------------------------------------------------------

function togglePurchaseActions(button) {
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

function closePurchaseActions(element) {
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
        document.querySelectorAll('.pli-action-popover.is-open')
            .forEach(item => item.classList.remove('is-open'));
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
});
</script>
@endpush
@endsection
