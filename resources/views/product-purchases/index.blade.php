@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/module-lists.css') }}">
    <style>
        /* Product purchase list — exact 10-column alignment */
        .product-purchase-page .premium-list--purchases {
            --purchase-grid:
                44px
                minmax(155px, 1.05fr)
                110px
                minmax(190px, 1.55fr)
                minmax(115px, .9fr)
                minmax(115px, .9fr)
                100px
                135px
                115px
                70px;
        }

        .product-purchase-page .premium-list--purchases .premium-list-head,
        .product-purchase-page .premium-list--purchases .premium-list-item {
            grid-template-columns: var(--purchase-grid) !important;
            min-width: 1260px !important;
        }

        .product-purchase-page .premium-list--purchases .premium-list-head {
            gap: 12px !important;
        }

        .product-purchase-page .premium-list--purchases .premium-list-item {
            gap: 12px !important;
            min-height: 68px;
        }

        .product-purchase-page .purchase-date-cell,
        .product-purchase-page .purchase-qty-cell,
        .product-purchase-page .purchase-amount-cell,
        .product-purchase-page .purchase-payment-cell {
            display:flex;
            align-items:center;
            justify-content:center;
            min-width:0;
        }

        .product-purchase-page .purchase-date-main {
            font-size:.78rem;
            font-weight:700;
            color:#0F172A;
            white-space:nowrap;
        }

        .product-purchase-page .purchase-product {
            display:block;
            width:100%;
            min-width:0;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:.82rem;
            font-weight:700;
            color:#0F172A;
        }

        .product-purchase-page .purchase-category {
            display:block;
            width:100%;
            text-align:center;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:.78rem;
            font-weight:600;
            color:#475569;
        }

        .product-purchase-page .purchase-subcategory {
            display:block;
            width:100%;
            text-align:center;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:.76rem;
            font-weight:500;
            color:#94A3B8;
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
            grid-column: 10 !important;
            grid-row: 1 !important;
        }
        .product-purchase-page .premium-list--purchases .premium-list-head .pli-head-cell:last-child {
            grid-column: 10 !important;
            grid-row: 1 !important;
            display:flex !important;
            align-items:center !important;
            justify-content:center !important;
        }


        /* Purchase modal follows the Job Card builder shell */
        .product-purchase-page .purchase-builder-modal .modal-dialog {
            max-width: 980px;
            width: calc(100% - 32px);
        }

        .product-purchase-page .purchase-builder-modal .modal-content {
            max-height: calc(100vh - 30px);
        }

        .product-purchase-page .purchase-builder-modal .job-card-builder-scroll-area {
            gap: 22px;
        }

        .product-purchase-page .purchase-builder-modal .job-card-builder-fixed-bottom {
            gap: 14px;
        }

        .product-purchase-page .purchase-payment-cards {
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .product-purchase-page .purchase-payment-card {
            position:relative;
            display:inline-flex;
            align-items:center;
            gap:8px;
            min-height:44px;
            padding:8px 12px;
            border:1.5px solid #E2E8F0;
            border-radius:12px;
            background:#fff;
            color:#475569;
            font-size:.78rem;
            font-weight:700;
            cursor:pointer;
            transition:.16s ease;
        }

        .product-purchase-page .purchase-payment-card:hover {
            border-color:#C4B5FD;
            background:#FAF8FF;
        }

        .product-purchase-page .purchase-payment-card input {
            position:absolute;
            opacity:0;
            pointer-events:none;
        }

        .product-purchase-page .purchase-payment-check {
            width:17px;
            height:17px;
            border:1.5px solid #CBD5E1;
            border-radius:5px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:transparent;
        }

        .product-purchase-page .purchase-payment-icon {
            width:25px;
            height:25px;
            border-radius:7px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background:#F1F5F9;
            color:#64748B;
        }

        .product-purchase-page .purchase-payment-card.is-selected {
            border-color:#8B5CF6;
            background:#F5F3FF;
            color:#5B21B6;
            box-shadow:0 0 0 3px rgba(139,92,246,.10);
        }

        .product-purchase-page .purchase-payment-card.is-selected .purchase-payment-check {
            border-color:#7C3AED;
            background:#7C3AED;
            color:#fff;
        }

        .product-purchase-page .purchase-payment-card.is-selected .purchase-payment-icon {
            background:#EDE9FE;
            color:#6D28D9;
        }

        @media (max-width: 768px) {
            .product-purchase-page .purchase-builder-modal .modal-dialog {
                width: calc(100% - 16px);
            }
        }
    </style>
@endpush

@section('title', 'Product Purchases')
@section('page-title', 'Product Purchases')

@section('content')
<div class="job-card-page product-purchase-page management-page">
    @include('partials.mgmt-top-actions', ['addLabel' => 'Add Purchase', 'addModal' => '#purchaseModal', 'addOnclick' => 'openPurchaseModal()', 'filterRoute' => route('product-purchases.index'), 'filter' => '', 'search' => '', 'filterOptions' => []])

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
            <div class="content-card-header-actions"><form method="GET" action="{{ route('product-purchases.index') }}" class="job-card-search"><div class="search-box"><i class="bi bi-search"></i><input type="text" name="search" value="{{ $search }}" placeholder="Search purchases..."><button class="border-0 bg-transparent" aria-label="Search"><i class="bi bi-search"></i></button></div></form></div>
        </div>

        @if($purchases->count())
        @php $listStart = ($purchases->currentPage() - 1) * $purchases->perPage(); @endphp
        <div class="premium-list premium-list--jobs premium-list--purchases premium-list--feed premium-list--compact premium-list--mgmt">
            <div class="premium-list-head">
                <span class="pli-head-cell col-center">#</span><span class="pli-head-cell col-left">Purchase</span><span class="pli-head-cell col-center">Date</span><span class="pli-head-cell col-center">Product</span><span class="pli-head-cell col-center">Category</span><span class="pli-head-cell col-center">Subcategory</span><span class="pli-head-cell col-center">Quantity</span><span class="pli-head-cell col-center">Payment Method</span><span class="pli-head-cell col-center">Amount</span><span class="pli-head-cell col-center">Actions</span>
            </div>
            @foreach($purchases as $purchase)
            @php
                $paymentKey = strtolower(str_replace(' ', '-', $purchase->payment_method ?? ''));
                $paymentIcon = match ($purchase->payment_method) { 'Cash' => 'bi-cash', 'UPI' => 'bi-phone', 'Card' => 'bi-credit-card', 'Bank Transfer' => 'bi-bank', default => 'bi-wallet2' };
            @endphp
            <article class="premium-list-item" id="purchase-row-{{ $purchase->id }}">
                <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>
                <div class="pli-col col-left"><div class="pli-name-cell"><div class="pli-icon pli-icon--cyan"><i class="bi bi-cart-check-fill"></i></div><div class="pli-name-stack"><span class="pli-title">{{ $purchase->purchase_number }}</span><span class="pli-subtext">Product purchase</span></div></div></div>
                <div class="pli-col purchase-date-cell"><span class="purchase-date-main"><i class="bi bi-calendar3 me-1" style="color:#94A3B8;font-size:.72rem"></i>{{ $purchase->purchase_date->format('d/m/Y') }}</span></div>
                <div class="pli-col pli-col-product col-center"><span class="purchase-product" title="{{ $purchase->product?->product_name }}">{{ $purchase->product?->product_name ?? '—' }}</span></div>
                <div class="pli-col col-center"><span class="purchase-category" title="{{ $purchase->product?->category }}">{{ $purchase->product?->category ?? '—' }}</span></div>
                <div class="pli-col pli-col-subcategory col-center"><span class="purchase-subcategory" title="{{ $purchase->product?->subcategory }}">{{ $purchase->product?->subcategory ?? '—' }}</span></div>
                <div class="pli-col purchase-qty-cell"><span class="purchase-quantity-pill">{{ $purchase->quantity }} units</span></div>
                <div class="pli-col purchase-payment-cell"><span class="payment-type-pill payment-type-{{ $paymentKey ?: 'default' }}"><i class="bi {{ $paymentIcon }}"></i>{{ $purchase->payment_method ?? '—' }}</span></div>
                <div class="pli-col purchase-amount-cell"><span class="module-amount">₹{{ number_format($purchase->total_amount, 2) }}</span></div>
                <div class="pli-col pli-col-actions purchase-list-actions"><div class="pli-action-menu-wrap"><button type="button" class="pli-action-dots" onclick="togglePurchaseActions(this)"><i class="bi bi-three-dots-vertical"></i></button><div class="pli-action-popover"><button type="button" class="pli-popover-action" onclick='openPurchaseModal(@json($purchase)); closePurchaseActions(this)'><span class="pli-popover-icon pli-popover-icon--edit"><i class="bi bi-pencil"></i></span><span>Edit Purchase</span></button><div class="pli-popover-divider"></div><form method="POST" action="{{ route('product-purchases.destroy', $purchase) }}" onsubmit="return confirm('Delete this purchase?')">@csrf @method('DELETE')<button class="pli-popover-action pli-popover-action--danger"><span class="pli-popover-icon pli-popover-icon--delete"><i class="bi bi-trash3"></i></span><span>Delete Purchase</span></button></form></div></div></div>
            </article>
            @endforeach
        </div>
        @include('partials.pagination-bar', ['paginator' => $purchases])
        @else
        <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-cart-check"></i></div><h3>No purchases found</h3><p>Record your first product purchase transaction.</p><button class="btn btn-primary mt-2" onclick="openPurchaseModal()"><i class="bi bi-plus-lg"></i> Add Purchase</button></div>
        @endif
    </div>
</div>

<div class="modal fade premium-modal job-card-builder-modal purchase-builder-modal" id="purchaseModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
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

                        <div class="job-card-builder-section">
                            <div class="job-card-builder-top-row">
                                <div class="form-field">
                                    <label for="purchaseProduct" class="form-label">PRODUCT <span>*</span></label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-box-seam"></i></span>
                                        <select id="purchaseProduct" name="product_id" class="no-nice-select" required>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-category="{{ $product->category }}"
                                                    data-subcategory="{{ $product->subcategory }}">
                                                    {{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="bi bi-chevron-down job-card-select-arrow"></i>
                                    </div>
                                    <small id="productMeta" class="job-card-builder-section-subtitle d-block mt-1"></small>
                                </div>

                                <div class="form-field">
                                    <label for="purchaseDate" class="form-label">PURCHASE DATE <span>*</span></label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-calendar3"></i></span>
                                        <input id="purchaseDate" type="date" class="form-control" name="purchase_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="job-card-builder-section">
                            <div class="job-card-builder-section-header">
                                <div>
                                    <h6 class="job-card-builder-section-title">PURCHASE DETAILS</h6>
                                    <p class="job-card-builder-section-subtitle">Enter the quantity and unit price for this purchase.</p>
                                </div>
                            </div>

                            <div class="job-card-builder-top-row">
                                <div class="form-field">
                                    <label for="purchaseQuantity" class="form-label">QUANTITY <span>*</span></label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-123"></i></span>
                                        <input id="purchaseQuantity" type="number" min="1" class="form-control" name="quantity" placeholder="0" required>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label for="purchaseUnitPrice" class="form-label">UNIT PRICE <span>*</span></label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-currency-rupee"></i></span>
                                        <input id="purchaseUnitPrice" type="number" step="0.01" min="0" class="form-control" name="unit_price" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="job-card-builder-section">
                            <div class="form-field">
                                <label for="purchaseNotes" class="form-label">NOTES</label>
                                <div class="field-control-wrap purchase-notes-wrap" style="height:auto;min-height:92px;align-items:flex-start;padding-top:10px;padding-bottom:10px;">
                                    <span class="form-field-icon mt-1"><i class="bi bi-sticky"></i></span>
                                    <textarea id="purchaseNotes" class="form-control" name="notes" rows="3" placeholder="Optional purchase notes"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="job-card-builder-fixed-bottom">
                        <div class="job-card-builder-section">
                            <div class="form-field">
                                <label class="form-label">PAYMENT METHOD <span>*</span></label>
                                <select id="purchasePayment" name="payment_method" required class="d-none">
                                    @foreach(['Cash','UPI','Card','Bank Transfer','Other'] as $method)
                                        <option value="{{ $method }}">{{ $method }}</option>
                                    @endforeach
                                </select>

                                <div class="purchase-payment-cards" id="purchasePaymentCards">
                                    @foreach(['Cash','UPI','Card','Bank Transfer','Other'] as $method)
                                        @php
                                            $paymentName = strtolower($method);
                                            $paymentIcon = str_contains($paymentName, 'upi') ? 'bi-phone' : (str_contains($paymentName, 'cash') ? 'bi-cash' : (str_contains($paymentName, 'card') ? 'bi-credit-card' : (str_contains($paymentName, 'bank') ? 'bi-bank' : 'bi-wallet2')));
                                        @endphp
                                        <label class="purchase-payment-card" data-payment="{{ $method }}">
                                            <input type="radio" name="purchase_payment_choice" value="{{ $method }}" aria-label="{{ $method }}">
                                            <span class="purchase-payment-check"><i class="bi bi-check"></i></span>
                                            <span class="purchase-payment-icon"><i class="bi {{ $paymentIcon }}"></i></span>
                                            <span>{{ $method }}</span>
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
                                    <span class="job-card-summary-total-val" id="purchaseTotal">₹ 0.00</span>
                                </div>
                                <div class="job-card-services-badge">
                                    <i class="bi bi-box-seam"></i>
                                    <span id="purchaseQuantityBadge">0</span> Units
                                </div>
                            </div>

                            <div class="job-card-summary-middle">
                                <div class="job-card-summary-row">
                                    <span class="job-card-summary-row-label">Quantity</span>
                                    <span class="job-card-summary-row-val" id="purchaseQuantitySummary">0</span>
                                </div>
                                <div class="job-card-summary-row">
                                    <span class="job-card-summary-row-label">Unit Price</span>
                                    <span class="job-card-summary-row-val" id="purchaseUnitPriceSummary">₹ 0.00</span>
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

@push('scripts')
<script>
const purchaseModalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseModal'));

function updatePurchaseSummary() {
    const quantity = Number(document.getElementById('purchaseQuantity').value) || 0;
    const price = Number(document.getElementById('purchaseUnitPrice').value) || 0;
    const total = quantity * price;

    document.getElementById('purchaseTotal').textContent = `₹ ${total.toFixed(2)}`;
    document.getElementById('purchaseFinalTotal').textContent = `₹ ${total.toFixed(2)}`;
    document.getElementById('purchaseQuantityBadge').textContent = quantity;
    document.getElementById('purchaseQuantitySummary').textContent = quantity;
    document.getElementById('purchaseUnitPriceSummary').textContent = `₹ ${price.toFixed(2)}`;
}

function updateProductMeta() {
    const select = document.getElementById('purchaseProduct');
    const product = select?.selectedOptions?.[0];
    if (!product) return;

    document.getElementById('productMeta').textContent =
        `Category: ${product.dataset.category || '—'} · Subcategory: ${product.dataset.subcategory || '—'}`;
}

function setPurchasePaymentMethod(method) {
    const hiddenSelect = document.getElementById('purchasePayment');
    const container = document.getElementById('purchasePaymentCards');

    if (!hiddenSelect || !container) return;

    const value = method || 'Cash';
    hiddenSelect.value = value;

    container.querySelectorAll('.purchase-payment-card').forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        const selected = String(radio?.value || '') === String(value);

        card.classList.toggle('is-selected', selected);
        if (radio) radio.checked = selected;
    });
}

function setupPurchasePaymentEvents() {
    const container = document.getElementById('purchasePaymentCards');
    if (!container || container.dataset.bound === '1') return;

    container.dataset.bound = '1';

    container.addEventListener('change', function (event) {
        const radio = event.target.closest('input[name="purchase_payment_choice"]');
        if (!radio) return;

        setPurchasePaymentMethod(radio.value);
    });

    container.addEventListener('click', function (event) {
        const card = event.target.closest('.purchase-payment-card');
        if (!card) return;

        const radio = card.querySelector('input[type="radio"]');
        if (radio) {
            setPurchasePaymentMethod(radio.value);
        }
    });
}

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

    document.getElementById('purchaseDate').value =
        purchase ? purchase.purchase_date : '{{ now()->toDateString() }}';

    if (purchase) {
        document.getElementById('purchaseProduct').value = purchase.product_id;
        document.getElementById('purchaseQuantity').value = purchase.quantity;
        document.getElementById('purchaseUnitPrice').value = purchase.unit_price;
        document.getElementById('purchasePayment').value = purchase.payment_method;
        document.getElementById('purchaseNotes').value = purchase.notes || '';
        setPurchasePaymentMethod(purchase.payment_method);
    } else {
        setPurchasePaymentMethod('Cash');
    }

    updateProductMeta();
    updatePurchaseSummary();
    purchaseModalInstance.show();
}

function togglePurchaseActions(button) {
    const menu = button.nextElementSibling;
    document.querySelectorAll('.pli-action-popover.is-open').forEach(item => {
        if (item !== menu) item.classList.remove('is-open');
    });
    menu.classList.toggle('is-open');
}

function closePurchaseActions(element) {
    element.closest('.pli-action-popover')?.classList.remove('is-open');
}

document.getElementById('purchaseProduct').addEventListener('change', updateProductMeta);
document.getElementById('purchaseQuantity').addEventListener('input', updatePurchaseSummary);
document.getElementById('purchaseUnitPrice').addEventListener('input', updatePurchaseSummary);

setupPurchasePaymentEvents();
setPurchasePaymentMethod('Cash');

document.addEventListener('click', event => {
    if (!event.target.closest('.pli-action-menu-wrap')) {
        document.querySelectorAll('.pli-action-popover.is-open')
            .forEach(item => item.classList.remove('is-open'));
    }
});
</script>
@endpush
@endsection
