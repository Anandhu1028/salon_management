@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
    <style>
        .job-card-page .premium-list--purchases .premium-list-head,
        .job-card-page .premium-list--purchases .premium-list-item {
            grid-template-columns: 44px minmax(128px, 1.15fr) 92px minmax(150px, 1.45fr) minmax(96px, .9fr) minmax(96px, .9fr) 90px 128px 106px 62px;
            min-width: 1220px;
        }
        .job-card-page .premium-list--purchases .pli-col-product { min-width: 0; }
        .job-card-page .premium-list--purchases .pli-col-product .pli-col-text { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:600; }
        .job-card-page .premium-list--purchases .pli-col-subcategory { color:#94A3B8; }
    </style>
@endpush

@section('title', 'Product Purchases')
@section('page-title', 'Product Purchases')

@section('content')
<div class="job-card-page management-page">
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
                <div class="pli-col col-center"><span class="pli-col-text">{{ $purchase->purchase_date->format('d/m/Y') }}</span></div>
                <div class="pli-col pli-col-product col-center"><span class="pli-col-text" title="{{ $purchase->product?->product_name }}">{{ $purchase->product?->product_name ?? '—' }}</span></div>
                <div class="pli-col col-center"><span class="pli-col-text">{{ $purchase->product?->category ?? '—' }}</span></div>
                <div class="pli-col pli-col-subcategory col-center"><span class="pli-col-text">{{ $purchase->product?->subcategory ?? '—' }}</span></div>
                <div class="pli-col col-center"><span class="badge rounded-pill bg-light text-primary border">{{ $purchase->quantity }} units</span></div>
                <div class="pli-col col-center"><span class="payment-type-pill payment-type-{{ $paymentKey ?: 'default' }}"><i class="bi {{ $paymentIcon }}"></i>{{ $purchase->payment_method ?? '—' }}</span></div>
                <div class="pli-col col-center"><span class="pli-col-text" style="font-weight:700;color:#1E293B">₹{{ number_format($purchase->total_amount, 2) }}</span></div>
                <div class="pli-col col-actions actions-cell col-center"><div class="pli-action-menu-wrap"><button type="button" class="pli-action-dots" onclick="togglePurchaseActions(this)"><i class="bi bi-three-dots-vertical"></i></button><div class="pli-action-popover"><button type="button" class="pli-popover-action" onclick='openPurchaseModal(@json($purchase)); closePurchaseActions(this)'><span class="pli-popover-icon pli-popover-icon--edit"><i class="bi bi-pencil"></i></span><span>Edit Purchase</span></button><div class="pli-popover-divider"></div><form method="POST" action="{{ route('product-purchases.destroy', $purchase) }}" onsubmit="return confirm('Delete this purchase?')">@csrf @method('DELETE')<button class="pli-popover-action pli-popover-action--danger"><span class="pli-popover-icon pli-popover-icon--delete"><i class="bi bi-trash3"></i></span><span>Delete Purchase</span></button></form></div></div></div>
            </article>
            @endforeach
        </div>
        @include('partials.pagination-bar', ['paginator' => $purchases])
        @else
        <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-cart-check"></i></div><h3>No purchases found</h3><p>Record your first product purchase transaction.</p><button class="btn btn-primary mt-2" onclick="openPurchaseModal()"><i class="bi bi-plus-lg"></i> Add Purchase</button></div>
        @endif
    </div>
</div>

<div class="modal fade premium-modal job-card-builder-modal" id="purchaseModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
 <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><form id="purchaseForm" method="POST" action="{{ route('product-purchases.store') }}" class="job-card-builder-form">@csrf <input type="hidden" id="purchaseMethod" name="_method">
  <div class="modal-header"><div class="d-flex align-items-center gap-3"><div class="modal-icon-box" style="background:#EDE9FE;color:#6366F1;border-radius:12px;width:44px;height:44px;display:flex;align-items:center;justify-content:center;font-size:1.25rem"><i class="bi bi-cart-check"></i></div><div class="modal-header-content"><h5 class="modal-title" id="purchaseTitle">Add Product Purchase</h5><p class="modal-subtitle" id="purchaseSubtitle">Record a new product purchase.</p></div></div><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body job-card-builder-body"><div class="job-card-builder-section"><div class="job-card-builder-top-row"><div class="form-field"><label class="form-label" for="purchaseProduct">PRODUCT <span>*</span></label><div class="field-control-wrap"><span class="form-field-icon"><i class="bi bi-box-seam"></i></span><select id="purchaseProduct" name="product_id" class="no-nice-select" required>@foreach($products as $product)<option value="{{ $product->id }}" data-category="{{ $product->category }}" data-subcategory="{{ $product->subcategory }}">{{ $product->product_name }}</option>@endforeach</select></div><small id="productMeta" class="text-muted ms-1"></small></div><div class="form-field"><label class="form-label" for="purchaseDate">PURCHASE DATE <span>*</span></label><div class="field-control-wrap"><span class="form-field-icon"><i class="bi bi-calendar3"></i></span><input id="purchaseDate" type="date" class="form-control" name="purchase_date" required></div></div></div></div><div class="job-card-builder-section"><div class="job-card-builder-section-header"><div><h6 class="job-card-builder-section-title">PURCHASE DETAILS</h6></div></div><div class="job-card-builder-top-row"><div class="form-field"><label class="form-label" for="purchaseQuantity">QUANTITY <span>*</span></label><div class="field-control-wrap"><span class="form-field-icon"><i class="bi bi-123"></i></span><input id="purchaseQuantity" type="number" min="1" class="form-control" name="quantity" required></div></div><div class="form-field"><label class="form-label" for="purchaseUnitPrice">UNIT PRICE <span>*</span></label><div class="field-control-wrap"><span class="form-field-icon"><i class="bi bi-currency-rupee"></i></span><input id="purchaseUnitPrice" type="number" step="0.01" min="0" class="form-control" name="unit_price" required></div></div></div></div>
  <div class="job-card-builder-fixed-bottom"><div class="job-card-builder-section"><div class="form-field job-card-payment-field"><label class="form-label">PAYMENT METHOD <span>*</span></label><div class="job-card-payment-select-wrap"><span class="form-field-icon"><i class="bi bi-wallet2"></i></span><select id="purchasePayment" name="payment_method" class="no-nice-select" required>@foreach(['Cash','UPI','Card','Bank Transfer','Other'] as $method)<option value="{{ $method }}">{{ $method }}</option>@endforeach</select><i class="bi bi-chevron-down job-card-payment-select-arrow"></i></div></div></div><div class="job-card-summary-card"><div class="job-card-summary-left"><div class="job-card-summary-calc-icon"><i class="bi bi-calculator"></i></div><div class="job-card-summary-total-info"><span class="job-card-summary-total-label">TOTAL AMOUNT</span><span class="job-card-summary-total-val" id="purchaseTotal">₹ 0.00</span></div><div class="job-card-services-badge"><i class="bi bi-box-seam"></i><span id="purchaseQuantityBadge">0</span> Units</div></div><div class="job-card-summary-middle"><div class="job-card-summary-row"><span class="job-card-summary-row-label">Quantity</span><span class="job-card-summary-row-val" id="purchaseQuantitySummary">0</span></div><div class="job-card-summary-row"><span class="job-card-summary-row-label">Unit Price</span><span class="job-card-summary-row-val" id="purchaseUnitPriceSummary">₹ 0.00</span></div><div class="job-card-summary-divider"></div><div class="job-card-summary-row job-card-summary-row-final"><span class="job-card-summary-row-label">Total</span><span class="job-card-summary-row-val" id="purchaseFinalTotal">₹ 0.00</span></div></div></div><div class="job-card-builder-section"><div class="form-field mb-0"><label class="form-label" for="purchaseNotes">NOTES</label><textarea id="purchaseNotes" class="form-control" name="notes" rows="2" placeholder="Optional purchase notes"></textarea></div></div></div></div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancel</button><button class="btn btn-primary" id="purchaseSubmit"><i class="bi bi-cart-check"></i> Save Purchase</button></div>
 </form></div></div>
</div>
@endsection

@push('scripts')
<script>
const purchaseModalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseModal'));
function updatePurchaseSummary() { const quantity = Number(document.getElementById('purchaseQuantity').value) || 0; const price = Number(document.getElementById('purchaseUnitPrice').value) || 0; const total = quantity * price; document.getElementById('purchaseTotal').textContent = `₹ ${total.toFixed(2)}`; document.getElementById('purchaseFinalTotal').textContent = `₹ ${total.toFixed(2)}`; document.getElementById('purchaseQuantityBadge').textContent = quantity; document.getElementById('purchaseQuantitySummary').textContent = quantity; document.getElementById('purchaseUnitPriceSummary').textContent = `₹ ${price.toFixed(2)}`; }
function updateProductMeta() { const product = document.getElementById('purchaseProduct').selectedOptions[0]; document.getElementById('productMeta').textContent = `Category: ${product.dataset.category || '—'} · Subcategory: ${product.dataset.subcategory || '—'}`; }
function openPurchaseModal(purchase = null) { const form = document.getElementById('purchaseForm'); form.reset(); document.getElementById('purchaseMethod').value = purchase ? 'PUT' : ''; form.action = purchase ? `/product-purchases/${purchase.id}` : '{{ route('product-purchases.store') }}'; document.getElementById('purchaseTitle').textContent = purchase ? 'Edit Product Purchase' : 'Add Product Purchase'; document.getElementById('purchaseSubtitle').textContent = purchase ? 'Update product purchase details.' : 'Record a new product purchase.'; document.getElementById('purchaseSubmit').innerHTML = purchase ? '<i class="bi bi-check2-circle"></i> Update Purchase' : '<i class="bi bi-cart-check"></i> Save Purchase'; document.getElementById('purchaseDate').value = purchase ? purchase.purchase_date : '{{ now()->toDateString() }}'; if (purchase) { document.getElementById('purchaseProduct').value = purchase.product_id; document.getElementById('purchaseQuantity').value = purchase.quantity; document.getElementById('purchaseUnitPrice').value = purchase.unit_price; document.getElementById('purchasePayment').value = purchase.payment_method; document.getElementById('purchaseNotes').value = purchase.notes || ''; } updateProductMeta(); updatePurchaseSummary(); purchaseModalInstance.show(); }
function togglePurchaseActions(button) { const menu = button.nextElementSibling; document.querySelectorAll('.pli-action-popover.is-open').forEach(item => { if (item !== menu) item.classList.remove('is-open'); }); menu.classList.toggle('is-open'); }
function closePurchaseActions(element) { element.closest('.pli-action-popover')?.classList.remove('is-open'); }
document.getElementById('purchaseProduct').addEventListener('change', updateProductMeta); document.getElementById('purchaseQuantity').addEventListener('input', updatePurchaseSummary); document.getElementById('purchaseUnitPrice').addEventListener('input', updatePurchaseSummary); document.addEventListener('click', event => { if (!event.target.closest('.pli-action-menu-wrap')) document.querySelectorAll('.pli-action-popover.is-open').forEach(item => item.classList.remove('is-open')); });
</script>
@endpush
