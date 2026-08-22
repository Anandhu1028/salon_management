@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')

    <div class="product-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel'     => 'Add Product',
            'addModal'     => '#productModal',
            'addOnclick'   => 'openAddProductModal()',
            'filterModule' => 'products',
            'filterRoute'  => route('products.index'),
            'filterData'   => [
                'categories'    => $filterCategories ?? [],
                'subcategories' => $filterSubcategories ?? [],
            ],
            'excelUrl' => route('products.export.excel', request()->query()),
            'pdfUrl'   => route('products.export.pdf',   request()->query()),
        ])

        

        {{-- Stats Row --}}
        @php
            $totalProducts = \App\Models\Product::count();
            $activeProducts = \App\Models\Product::where('status', 'active')->count();
            $inactiveProducts = \App\Models\Product::where('status', 'inactive')->count();
        @endphp
        <div class="mgmt-stats-grid">
            @include('partials.mgmt-stat-card', [
                'theme' => 'blue',
                'icon' => 'box-blue',
                'label' => 'Total Products',
                'value' => $totalProducts,
                'subtext' => 'In inventory',
                'sparkColor' => '#3B82F6',
                'trend' => '6.4',
                'trendUp' => true,
            ])
            @include('partials.mgmt-stat-card', [
                'theme' => 'green',
                'icon' => 'check-green',
                'label' => 'Active Products',
                'value' => $activeProducts,
                'subtext' => 'Available to sell',
                'sparkColor' => '#22C55E',
                'trend' => '4.1',
                'trendUp' => true,
            ])
            @include('partials.mgmt-stat-card', [
                'theme' => 'red',
                'icon' => 'pause-red',
                'label' => 'Inactive Products',
                'value' => $inactiveProducts,
                'subtext' => 'Not listed',
                'sparkColor' => '#EF4444',
                'trend' => '1.2',
                'trendUp' => false,
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

        {{-- Product Card --}}
        <div class="content-card">

            {{-- Card Header --}}
            <div class="content-card-header">
                <div>
                    <h2>Product List</h2>
                    <span>{{ $products->total() }} retail products in stock</span>
                </div>

                <div class="content-card-header-actions">
                    <form method="GET" action="{{ route('products.index') }}" class="product-search">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search products...">
                            @if(!empty($filter))
                                <input type="hidden" name="filter" value="{{ $filter }}">
                            @endif
                            @if($search)
                                <a href="{{ route('products.index', array_filter(['filter' => $filter ?? ''])) }}"
                                    title="Clear search">
                                    <i class="bi bi-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if($products->count())

                @php $listStart = ($products->currentPage() - 1) * $products->perPage(); @endphp

                <div
                    class="premium-list premium-list--catalog premium-list--feed premium-list--compact premium-list--mgmt premium-list--product">
                    <div class="premium-list-head">
                        <span class="pli-head-cell col-center">#</span>
                        <span class="pli-head-cell col-left">Name</span>
                        <span class="pli-head-cell col-left pli-head-category">Category</span>
                        <span class="pli-head-cell col-left pli-head-subcategory">Sub Category</span>
                        <span class="pli-head-cell col-center pli-head-status">Status</span>
                        <span class="pli-head-cell col-center product-actions-col">Actions</span>
                    </div>

                    @foreach($products as $product)
                        <article class="premium-list-item" id="product-row-{{ $product->id }}">
                            <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                            <div class="pli-col col-left">
                                <div class="pli-name-cell">
                                    <div class="pli-icon pli-icon--blue">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </div>
                                    <div class="pli-name-stack">
                                        <span class="pli-title product-name">{{ $product->product_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pli-col col-left pli-col-category">
                                <span class="pli-col-text">{{ $product->category ?: '—' }}</span>
                            </div>

                            <div class="pli-col col-left pli-col-subcategory">
                                <span class="pli-col-text">{{ $product->subcategory ?: '—' }}</span>
                            </div>

                            <div class="pli-col col-center status-cell">
                                <span id="status-badge-{{ $product->id }}"
                                    class="status-badge {{ $product->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    <span></span>
                                    <span class="status-text">{{ ucfirst($product->status) }}</span>
                                </span>
                            </div>

                            {{-- ========================================================= --}}
                            {{-- ACTIONS — always last, pinned to the far right edge       --}}
                            {{-- ========================================================= --}}
                            <div class="pli-col pli-col-actions actions-cell product-actions-col">
                                {{-- Mobile Dropdown --}}
                                <div class="dropdown product-action-dropdown d-md-none">
                                    <span id="mob-prod-status-badge-{{ $product->id }}"
                                        class="pli-mob-status-dot {{ $product->status === 'active' ? 'pli-mob-status-dot--active' : 'pli-mob-status-dot--inactive' }}"
                                        title="{{ ucfirst($product->status) }}"></span>

                                    <button class="pli-btn-dots" type="button" data-bs-toggle="dropdown"
                                        data-bs-boundary="viewport" aria-expanded="false" title="Actions">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end product-action-menu">
                                        {{-- Purchase actions --}}
                                        <li>
                                            <button type="button" class="dropdown-item product-action-item"
                                                onclick="openPurchaseModal({{ $product->id }}, @js($product->product_name), @js($product->category), @js($product->subcategory), {{ (float) $product->price }})">
                                                <span class="product-action-icon product-action-icon--purple"><i class="bi bi-cart-plus"></i></span>
                                                <span>Record Purchase</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item product-action-item"
                                                onclick="openPurchaseHistory({{ $product->id }}, @js($product->product_name), @js($product->category), @js($product->subcategory))">
                                                <span class="product-action-icon product-action-icon--blue"><i class="bi bi-clock-history"></i></span>
                                                <span>Purchase History</span>
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider my-1"></li>

                                        {{-- Product CRUD --}}
                                        <li>
                                            <button type="button" class="dropdown-item product-action-item"
                                                data-bs-toggle="modal" data-bs-target="#productModal"
                                                onclick='openEditProductModal(@json($product))'>
                                                <span class="product-action-icon product-action-icon--indigo"><i class="bi bi-pencil"></i></span>
                                                <span>Edit Product</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item product-action-item product-action-status-btn"
                                                id="prod-status-btn-{{ $product->id }}"
                                                data-status="{{ $product->status }}"
                                                onclick="triggerProductStatusToggle({{ $product->id }}, @js($product->product_name))">
                                                <span class="product-action-icon product-action-icon--green">
                                                    <i class="bi {{ $product->status === 'active' ? 'bi-toggle2-on' : 'bi-toggle2-off' }}"></i>
                                                </span>
                                                <span class="product-action-status-text">
                                                    {{ $product->status === 'active' ? 'Deactivate Product' : 'Activate Product' }}
                                                </span>
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider my-1"></li>

                                        <li>
                                            <button type="button" class="dropdown-item product-action-item product-action-item--danger"
                                                onclick="openDeleteProductModal({{ $product->id }}, @js($product->product_name))">
                                                <span class="product-action-icon product-action-icon--red"><i class="bi bi-trash3"></i></span>
                                                <span>Delete Product</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Desktop: 3-dot action popover (same pattern used on Services / Job Cards / Attendance) --}}
                                <div class="pli-action-menu-wrap pli-action-buttons-desktop">
                                    <button
                                        type="button"
                                        class="pli-action-dots"
                                        aria-label="Product actions"
                                        aria-expanded="false"
                                        onclick="togglePliActions(this)"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <div class="pli-action-popover">
                                        <button
                                            type="button"
                                            class="pli-popover-action"
                                            onclick="openPurchaseModal({{ $product->id }}, @js($product->product_name), @js($product->category), @js($product->subcategory), {{ (float) $product->price }}); closePliActions(this)"
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--view">
                                                <i class="bi bi-cart-plus"></i>
                                            </span>
                                            <span>Record Purchase</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="pli-popover-action"
                                            onclick="openPurchaseHistory({{ $product->id }}, @js($product->product_name), @js($product->category), @js($product->subcategory)); closePliActions(this)"
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--view">
                                                <i class="bi bi-clock-history"></i>
                                            </span>
                                            <span>Purchase History</span>
                                        </button>

                                        <div class="pli-popover-divider"></div>

                                        <button
                                            type="button"
                                            class="pli-popover-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#productModal"
                                            onclick='openEditProductModal(@json($product)); closePliActions(this)'
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--edit">
                                                <i class="bi bi-pencil"></i>
                                            </span>
                                            <span>Edit Product</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="pli-popover-action pli-popover-status-btn"
                                            id="desk-prod-status-btn-{{ $product->id }}"
                                            data-status="{{ $product->status }}"
                                            onclick="triggerProductStatusToggle({{ $product->id }}, @js($product->product_name)); closePliActions(this)"
                                        >
                                            <span class="pli-popover-status-left">
                                                <span class="pli-popover-icon pli-popover-icon--status">
                                                    <i class="bi {{ $product->status === 'active' ? 'bi-toggle2-on' : 'bi-toggle2-off' }}"></i>
                                                </span>
                                                <span class="pli-popover-status-text">
                                                    {{ $product->status === 'active' ? 'Deactivate Product' : 'Activate Product' }}
                                                </span>
                                            </span>
                                        </button>

                                        <div class="pli-popover-divider"></div>

                                        <button
                                            type="button"
                                            class="pli-popover-action pli-popover-action--danger"
                                            onclick="openDeleteProductModal({{ $product->id }}, @js($product->product_name)); closePliActions(this)"
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--delete">
                                                <i class="bi bi-trash3"></i>
                                            </span>
                                            <span>Delete Product</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @include('partials.pagination-bar', ['paginator' => $products])

            @else

                {{-- Empty State --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h3>No products found</h3>
                    <p>Start building your inventory list by adding your first retail product.</p>
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#productModal"
                        onclick="openAddProductModal()">
                        @include('partials.action-icons', ['type' => 'add'])
                        Add Product
                    </button>
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ADD / EDIT PRODUCT MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal premium-modal--md" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="productForm" method="POST" action="{{ route('products.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="productFormMethod" value="POST">

                    {{-- Header --}}
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-icon-box info">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="modal-header-content">
                                <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                                <p class="modal-subtitle" id="productModalSubtitle">Add a new salon retail product.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <div class="modal-form-grid">

                            {{-- Product Name — full width --}}
                            <div class="form-field form-field--full">
                                <label for="product_name" class="form-label">
                                    Product Name <span>*</span>
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-box-seam"></i></span>
                                    <input type="text" name="product_name" id="product_name" class="form-control"
                                        placeholder="e.g. Premium Hair Shampoo" required>
                                </div>
                            </div>

                            {{-- Category --}}
                            <div class="form-field">
                                <label for="product_category" class="form-label">
                                    Category <span>*</span>
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-grid"></i></span>
                                    <input type="text" name="category" id="product_category" class="form-control"
                                        placeholder="e.g. Hair Care" required>
                                </div>
                            </div>

                            {{-- Subcategory --}}
                            <div class="form-field">
                                <label for="product_subcategory" class="form-label">
                                    Subcategory
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-collection"></i></span>
                                    <input type="text" name="subcategory" id="product_subcategory" class="form-control"
                                        placeholder="e.g. Shampoo">
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="form-field">
                                <label for="product_status" class="form-label">
                                    Status <span>*</span>
                                </label>
                                <select name="status" id="product_status" class="form-select" data-icon="bi-shield-check"
                                    required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="productSubmitButton">
                            <i class="bi bi-box-seam"></i> Create Product
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- STATUS CONFIRMATION MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal" id="productStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="confirm-modal-body">
                    <div class="confirm-icon primary" id="productStatusIcon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h5 class="confirm-title" id="productStatusTitle">Change Status?</h5>
                    <p class="confirm-message" id="productStatusMessage">Are you sure?</p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmProductStatusButton">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- DELETE CONFIRMATION MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal" id="deleteProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="confirm-modal-body">
                    <div class="confirm-icon danger">
                        @include('partials.action-icons', ['type' => 'delete'])
                    </div>
                    <h5 class="confirm-title">Delete Product?</h5>
                    <p class="confirm-message" id="deleteProductMessage">This action cannot be undone.</p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteProductButton">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- RECORD PURCHASE MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal premium-modal--md" id="purchaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="purchaseForm">
                    {{-- Header --}}
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-icon-box info">
                                <i class="bi bi-cart-plus"></i>
                            </div>
                            <div class="modal-header-content">
                                <h5 class="modal-title">Record Purchase</h5>
                                <p class="modal-subtitle">Add a new purchase for this product.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">

                        {{-- Product preview card --}}
                        <div class="purchase-product-preview">
                            <div class="pli-icon pli-icon--blue">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>
                            <div class="pli-name-stack" style="flex:1;min-width:0;">
                                <span class="pli-title" id="purchaseProductName">—</span>
                                <span class="pli-subtext" id="purchaseProductMeta">—</span>
                            </div>
                            <div class="purchase-price-badge">
                                <span class="purchase-price-badge__label">Unit Price</span>
                                <span class="purchase-price-badge__value" id="purchaseUnitPrice">₹0.00</span>
                            </div>
                        </div>

                        {{-- Only Date + Quantity — price comes from the product record --}}
                        <div class="modal-form-grid mt-3">
                            <div class="form-field">
                                <label for="purchase_date" class="form-label">
                                    Purchase Date <span>*</span>
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="purchase_date" id="purchase_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="purchase_quantity" class="form-label">
                                    Quantity <span>*</span>
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-123"></i></span>
                                    <input type="number" name="quantity" id="purchase_quantity" class="form-control"
                                        min="1" step="1" placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        {{-- Live total (read-only) --}}
                        <div class="purchase-total-card">
                            <span class="purchase-total-card__label">Total Purchase Amount</span>
                            <div class="purchase-total-card__row">
                                <span class="purchase-total-card__breakdown" id="purchaseTotalBreakdown">0 × ₹0.00</span>
                                <span class="purchase-total-card__value" id="purchaseTotalValue">₹0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="purchaseSubmitButton">
                            <i class="bi bi-cart-check"></i> Record Purchase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- VIEW PURCHASES (HISTORY) MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal premium-modal--md" id="purchaseHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title">Purchase History</h5>
                            <p class="modal-subtitle" id="purchaseHistoryMeta">—</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                    <div id="purchaseHistoryLoading" class="purchase-history-loading text-center py-4">
                        <span class="spinner-border spinner-border-sm me-2"></span> Loading purchase history...
                    </div>

                    <div id="purchaseHistoryContent" style="display:none;">

                        <div class="purchase-history-summary">
                            <div class="purchase-history-summary__item">
                                <span class="purchase-history-summary__label">Total Purchases</span>
                                <span class="purchase-history-summary__value" id="purchaseSummaryCount">0</span>
                            </div>
                            <div class="purchase-history-summary__item">
                                <span class="purchase-history-summary__label">Total Quantity</span>
                                <span class="purchase-history-summary__value" id="purchaseSummaryQuantity">0</span>
                            </div>
                            <div class="purchase-history-summary__item">
                                <span class="purchase-history-summary__label">Total Amount</span>
                                <span class="purchase-history-summary__value" id="purchaseSummaryAmount">₹0.00</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table purchase-history-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Purchase Date</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price / Unit</th>
                                        <th class="text-end">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseHistoryTableBody"></tbody>
                            </table>
                        </div>

                        <div id="purchaseHistoryEmpty" class="empty-state py-4" style="display:none;">
                            <div class="empty-state-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <p class="mb-0">No purchase records found.</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- PURCHASE HISTORY — PRODUCT PICKER MODAL                    --}}
    {{-- (opened by the header "Purchase History" quick button,     --}}
    {{--  which has no product context of its own)                  --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal" id="productPickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon-box info">
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="modal-header-content">
                            <h5 class="modal-title">Purchase History</h5>
                            <p class="modal-subtitle">Select a product to view its purchase history.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="field-control-wrap mb-3">
                        <span class="form-field-icon"><i class="bi bi-search"></i></span>
                        <input type="text" id="productPickerSearch" class="form-control" placeholder="Search products...">
                    </div>

                    <div id="productPickerList" class="product-picker-list"></div>

                    <div id="productPickerEmpty" class="empty-state py-4" style="display:none;">
                        <p class="mb-0">No products match your search.</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>


@endsection


@push('styles')
    <style>
        /* ─── Purchases count badge ─── */
        .product-purchase-count {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 56px;
            padding: 4px 10px;
            border-radius: 20px;
            background: linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%);
            border: 1px solid #C4B5FD;
            line-height: 1.25;
            gap: 1px;
        }

        .product-purchase-count__number {
            font-weight: 800;
            font-size: 0.9rem;
            color: #4F46E5;
            letter-spacing: -0.01em;
        }

        .product-purchase-count__label {
            font-size: 0.64rem;
            font-weight: 600;
            color: #6D5FD8;
            text-transform: lowercase;
            white-space: nowrap;
        }

        /* ─── Action dropdown ─── */
        .product-action-dropdown {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .product-action-menu {
            min-width: 210px;
            padding: 5px;
            border: 1px solid #E8E8F0;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(17, 12, 46, 0.10), 0 2px 6px rgba(0,0,0,0.04);
            background: #fff;
        }

        .product-action-item {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            padding: 7px 8px;
            border-radius: 9px;
            font-size: 0.84rem;
            font-weight: 500;
            color: #374151;
            background: transparent;
            border: none;
            text-align: left;
            transition: background 0.14s ease, color 0.14s ease;
        }

        .product-action-item:hover {
            background: #F5F3FF;
            color: #4F46E5;
        }

        .product-action-item--danger:hover {
            background: #FEF2F2;
            color: #DC2626;
        }

        /* Action icon containers — distinct per type */
        .product-action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 27px;
            height: 27px;
            border-radius: 8px;
            font-size: 0.82rem;
            transition: transform 0.12s ease;
        }

        .product-action-icon--purple  { background: #EDE9FE; color: #7C3AED; }
        .product-action-icon--blue    { background: #EFF6FF; color: #2563EB; }
        .product-action-icon--indigo  { background: #EEF2FF; color: #4F46E5; }
        .product-action-icon--green   { background: #F0FDF4; color: #16A34A; }
        .product-action-icon--red     { background: #FEF2F2; color: #DC2626; }

        /* Default icon (fallback) */
        .product-action-icon:not([class*="product-action-icon--"]) {
            background: #F4F4F6;
            color: #6B7280;
        }

        .product-action-item:hover .product-action-icon--purple { background: #DDD6FE; }
        .product-action-item:hover .product-action-icon--blue   { background: #DBEAFE; }
        .product-action-item:hover .product-action-icon--indigo { background: #E0E7FF; }
        .product-action-item:hover .product-action-icon--green  { background: #DCFCE7; }
        .product-action-item--danger:hover .product-action-icon--red { background: #FEE2E2; }

        .dropdown-divider { border-color: #F1F5F9; margin: 4px 0; }

        /* ─── Purchase product preview (modal) ─── */
        .purchase-product-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, #F8F7FF 0%, #F0F9FF 100%);
            border: 1px solid #E0E7FF;
        }

        .purchase-price-badge {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            flex-shrink: 0;
            margin-left: auto;
            gap: 1px;
        }

        .purchase-price-badge__label {
            font-size: 0.63rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6D5FD8;
        }

        .purchase-price-badge__value {
            font-size: 1rem;
            font-weight: 800;
            color: #4F46E5;
            letter-spacing: -0.01em;
        }

        /* ─── Purchase total card ─── */
        .purchase-total-card {
            margin-top: 1rem;
            padding: 12px 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #F5F3FF 0%, #EEF2FF 100%);
            border: 1.5px solid #E0E7FF;
        }

        .purchase-total-card__label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6D5FD8;
            margin-bottom: 6px;
        }

        .purchase-total-card__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .purchase-total-card__breakdown {
            font-size: 0.85rem;
            color: #64748B;
        }

        .purchase-total-card__value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #4338CA;
            letter-spacing: -0.01em;
        }

        /* ─── History summary cards ─── */
        .purchase-history-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .purchase-history-summary__item {
            padding: 12px 14px;
            border-radius: 12px;
            background: #F8F9FB;
            border: 1px solid #EEF0F4;
            text-align: center;
        }

        .purchase-history-summary__label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .purchase-history-summary__value {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1E1B4B;
            letter-spacing: -0.01em;
        }

        /* ─── History table ─── */
        .purchase-history-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .purchase-history-table thead th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            background: #F8F9FB;
            border-bottom: 1.5px solid #EEF0F4;
            padding: 10px 12px;
            white-space: nowrap;
        }

        .purchase-history-table tbody td {
            font-size: 0.875rem;
            color: #374151;
            vertical-align: middle;
            padding: 10px 12px;
            border-bottom: 1px solid #F1F5F9;
        }

        .purchase-history-table tbody tr:last-child td {
            border-bottom: none;
        }

        .purchase-history-table tbody tr:hover td {
            background: #FAFBFF;
        }

        .purchase-qty-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%);
            color: #4F46E5;
            border: 1px solid #C4B5FD;
        }

        .purchase-history-loading {
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* ─── Header "Purchase History" quick button ─── */
        .product-page-extra-actions {
            display: inline-flex;
        }

        .mgmt-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border-radius: 12px;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.16s ease;
        }

        .mgmt-action-btn--secondary {
            background: #fff;
            color: #4F46E5;
            border: 1.5px solid #E0E7FF;
        }

        .mgmt-action-btn--secondary:hover {
            background: #F5F3FF;
            border-color: #C7D2FE;
        }

        .mgmt-action-btn__icon {
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
        }

        /* ─── Product picker (global Purchase History button) ─── */
        .product-picker-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 360px;
            overflow-y: auto;
        }

        .product-picker-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #EEF0F4;
            background: #fff;
            text-align: left;
            cursor: pointer;
            transition: background 0.14s ease, border-color 0.14s ease;
        }

        .product-picker-item:hover {
            background: #F5F3FF;
            border-color: #E0E7FF;
        }

        .product-picker-item .pli-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        .product-picker-item__text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
            flex: 1;
        }

        .product-picker-item__name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-picker-item__meta {
            font-size: 0.72rem;
            color: #94A3B8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-picker-item .bi-chevron-right {
            color: #C7D2FE;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        @media (max-width: 767.98px) {
            .purchase-history-summary {
                gap: 6px;
            }
            .purchase-history-summary__value {
                font-size: 0.9rem;
            }
        }

        /* ============================================================
           ROW ALIGNMENT FIX — tighter gaps, actions flush right,
           dropdown button vertically centered, never wraps, and
           every header label pinned to sit directly above its
           matching data column (Category / Sub Category / Price /
           Purchases / Status), left- or center-aligned consistently.
           ============================================================ */

        .premium-list--catalog.premium-list--feed.premium-list--product {
            --pli-grid: 40px minmax(190px, 1.3fr) 120px 120px 90px 100px 90px minmax(96px, auto) !important;
        }

        .premium-list--product .premium-list-head,
        .premium-list--product .premium-list-item {
            gap: 10px;
            min-width: 960px;
        }

        /* Base: every head cell and data cell becomes a flex box so
           justify-content reliably controls horizontal position,
           independent of text length. */
        .premium-list--product .pli-head-cell,
        .premium-list--product .pli-col,
        .premium-list--product .pli-rank {
            display: flex;
            align-items: center;
            white-space: nowrap;
        }

        /* Generic left / center helpers shared by head + data cells */
        .premium-list--product .col-left {
            justify-content: flex-start;
            text-align: left;
        }

        .premium-list--product .col-center {
            justify-content: center;
            text-align: center;
        }

        .premium-list--product .pli-name-cell {
            gap: 8px;
        }

        /* Category / Sub Category — header label and data cell both
           locked to flex-start so they line up on the left edge of
           their column, regardless of value length. */
        .premium-list--product .pli-head-category,
        .premium-list--product .pli-head-subcategory,
        .premium-list--product .pli-col-category,
        .premium-list--product .pli-col-subcategory {
            justify-content: flex-start;
            text-align: left;
            padding-left: 0;
        }

        /* Price / Purchases / Status — header label and data cell
           both centered within their column. */
        .premium-list--product .pli-head-price,
        .premium-list--product .pli-head-purchases,
        .premium-list--product .pli-head-status,
        .premium-list--product .pli-col-amount,
        .premium-list--product .pli-col-purchases,
        .premium-list--product .status-cell {
            justify-content: center;
            text-align: center;
        }

        /* Keep the pill/badge contents themselves centered inside
           their own box so they don't drift to one side. */
        .premium-list--product .pli-col-amount .pli-col-text,
        .premium-list--product .pli-col-purchases .product-purchase-count,
        .premium-list--product .status-cell .status-badge {
            margin: 0 auto;
        }

        .premium-list--product .pli-col-actions,
        .premium-list--product .product-actions-col {
            justify-content: flex-end;
            align-self: center;
            /* Comfortable, consistent breathing room between the Status
               column and the Actions column on every row. */
            margin-left: 14px;
            overflow: visible;
        }

        .premium-list--product .product-action-dropdown {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
        }

        /* Desktop 3-dot popover trigger sits in the same right-aligned
           actions column as the mobile dropdown above. */
        .premium-list--product .pli-action-menu-wrap {
            justify-content: flex-end;
        }

        .premium-list--product .pli-btn-dots {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #E2E8F0;
            background: #F8FAFC;
            color: #64748B;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .premium-list--product .pli-btn-dots:hover {
            background: #EEF2FF;
            color: #4F46E5;
            border-color: #C7D2FE;
        }
    </style>
@endpush


@push('scripts')
    <script src="{{ asset('js/pli-action-popover.js') }}"></script>
    <script>
        let currentProductId = null;
        let currentProductTargetStatus = null;
        let deleteProductId = null;
        let currentPurchaseProductId = null;

        // URL templates for the purchase endpoints. Falls back to the plain
        // "/products/{id}/purchases" path if the named routes are not yet
        // registered by the backend (Route::has guards against errors).
        const purchaseStoreUrlTemplate = @json(\Illuminate\Support\Facades\Route::has('products.purchases.store')
            ? route('products.purchases.store', ['product' => '__PRODUCT_ID__'])
            : '/products/__PRODUCT_ID__/purchases');

        const purchaseHistoryUrlTemplate = @json(\Illuminate\Support\Facades\Route::has('products.purchases.history')
            ? route('products.purchases.history', ['product' => '__PRODUCT_ID__'])
            : '/products/__PRODUCT_ID__/purchases');

        function openAddProductModal() {
            const form = document.getElementById('productForm');
            form.reset();
            form.action = "{{ route('products.store') }}";
            document.getElementById('productFormMethod').value = 'POST';
            document.getElementById('productModalTitle').textContent = 'Add Product';
            document.getElementById('productModalSubtitle').textContent = 'Add a new salon retail product.';
            document.getElementById('productSubmitButton').innerHTML = '<i class="bi bi-box-seam"></i> Create Product';
            document.getElementById('product_status').value = 'active';
        }

        function openEditProductModal(product) {
            const form = document.getElementById('productForm');
            form.action = `/products/${product.id}`;
            document.getElementById('productFormMethod').value = 'PUT';
            document.getElementById('productModalTitle').textContent = 'Edit Product';
            document.getElementById('productModalSubtitle').textContent = 'Update product information.';
            document.getElementById('productSubmitButton').innerHTML = '<i class="bi bi-check2-circle"></i> Update Product';

            document.getElementById('product_name').value = product.product_name ?? '';
            document.getElementById('product_category').value = product.category ?? '';
            document.getElementById('product_subcategory').value = product.subcategory ?? '';
            document.getElementById('product_status').value = product.status ?? 'active';
        }

        function triggerProductStatusToggle(productId, productName) {
            const btn = document.getElementById(`prod-status-btn-${productId}`);
            const currentStatus = btn ? (btn.dataset.status || 'active') : 'active';
            const targetStatus = currentStatus === 'active' ? 'inactive' : 'active';
            confirmProductStatus(productId, targetStatus, productName);
        }

        // Kept for backward compatibility with any existing toggle-switch based
        // markup elsewhere in the app; safe no-op if no such element exists.
        function onProductStatusToggle(productId, productName, input) {
            const targetStatus = input.checked ? 'active' : 'inactive';
            input.checked = !input.checked;
            confirmProductStatus(productId, targetStatus, productName);
        }

        function confirmProductStatus(productId, targetStatus, productName) {
            currentProductId = productId;
            currentProductTargetStatus = targetStatus;

            const isActivating = targetStatus === 'active';
            document.getElementById('productStatusTitle').textContent = isActivating ? 'Activate Product?' : 'Deactivate Product?';
            document.getElementById('productStatusMessage').textContent = isActivating
                ? `Are you sure you want to activate ${productName}?`
                : `Are you sure you want to deactivate ${productName}?`;

            const iconBox = document.getElementById('productStatusIcon');
            iconBox.className = isActivating ? 'confirm-icon success' : 'confirm-icon warning';

            const button = document.getElementById('confirmProductStatusButton');
            button.textContent = isActivating ? 'Activate' : 'Deactivate';
            button.className = isActivating ? 'btn btn-success' : 'btn btn-danger';

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('productStatusModal'));
            modal.show();
        }

        document.getElementById('confirmProductStatusButton').addEventListener('click', async function () {
            if (!currentProductId || !currentProductTargetStatus) return;

            const button = this;
            button.disabled = true;

            try {
                const response = await fetch(`/products/${currentProductId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to update product status.');
                }

                const modalElement = document.getElementById('productStatusModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();

                updateProductStatusUI(currentProductId, data.status);
                showToast(data.message, 'success');

                currentProductId = null;
                currentProductTargetStatus = null;

            } catch (error) {
                showToast(error.message, 'danger');
            } finally {
                button.disabled = false;
            }
        });

        function updateProductStatusUI(productId, status) {
            const badge = document.getElementById(`status-badge-${productId}`);
            const toggle = document.getElementById(`status-toggle-${productId}`);
            const actionBtn = document.getElementById(`prod-status-btn-${productId}`);
            const mobDot = document.getElementById(`mob-prod-status-badge-${productId}`);
            const isActive = status === 'active';

            // Legacy toggle-switch support (no-op if the element doesn't exist).
            if (toggle) {
                toggle.checked = isActive;
                const label = toggle.closest('.mgmt-status-toggle')?.querySelector('.mgmt-status-toggle__text');
                if (label) {
                    label.textContent = isActive ? label.dataset.activeText : label.dataset.inactiveText;
                }
            }

            if (badge) {
                if (isActive) {
                    badge.className = 'status-badge status-active';
                    badge.innerHTML = '<span></span><span class="status-text">Active</span>';
                } else {
                    badge.className = 'status-badge status-inactive';
                    badge.innerHTML = '<span></span><span class="status-text">Inactive</span>';
                }
            }

            if (actionBtn) {
                actionBtn.dataset.status = status;
                const icon = actionBtn.querySelector('.product-action-icon i');
                if (icon) {
                    icon.className = 'bi ' + (isActive ? 'bi-toggle2-on' : 'bi-toggle2-off');
                }
                const text = actionBtn.querySelector('.product-action-status-text');
                if (text) {
                    text.textContent = isActive ? 'Deactivate Product' : 'Activate Product';
                }
            }

            // Desktop popover status button (separate markup/id from the mobile one above).
            const deskActionBtn = document.getElementById(`desk-prod-status-btn-${productId}`);
            if (deskActionBtn) {
                deskActionBtn.dataset.status = status;
                const deskIcon = deskActionBtn.querySelector('.pli-popover-icon i');
                if (deskIcon) {
                    deskIcon.className = 'bi ' + (isActive ? 'bi-toggle2-on' : 'bi-toggle2-off');
                }
                const deskText = deskActionBtn.querySelector('.pli-popover-status-text');
                if (deskText) {
                    deskText.textContent = isActive ? 'Deactivate Product' : 'Activate Product';
                }
            }

            if (mobDot) {
                mobDot.className = 'pli-mob-status-dot d-md-none ' + (isActive ? 'pli-mob-status-dot--active' : 'pli-mob-status-dot--inactive');
                mobDot.title = isActive ? 'Active' : 'Inactive';
            }
        }

        function openDeleteProductModal(productId, productName) {
            deleteProductId = productId;
            document.getElementById('deleteProductMessage').textContent = `Are you sure you want to delete ${productName}?`;
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteProductModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteProductButton').addEventListener('click', async function () {
            if (!deleteProductId) return;

            const button = this;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');

            try {
                const response = await fetch(`/products/${deleteProductId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to delete product.');
                }

                const modalElement = document.getElementById('deleteProductModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();

                const row = document.getElementById(`product-row-${deleteProductId}`);
                if (row) {
                    row.style.transition = 'opacity .25s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        if (document.querySelector('.premium-list .premium-list-item') === null) {
                            window.location.reload();
                        }
                    }, 250);
                }

                showToast(data.message, 'success');

            } catch (error) {
                showToast(error.message, 'danger');
            } finally {
                deleteProductId = null;
                button.disabled = false;
                button.textContent = 'Delete';
            }
        });

        // ========================================================= //
        // RECORD PURCHASE
        // ========================================================= //

        let currentPurchaseProductPrice = 0;

        function openPurchaseModal(productId, productName, category, subcategory, productPrice) {
            currentPurchaseProductId    = productId;
            currentPurchaseProductPrice = parseFloat(productPrice) || 0;

            const form = document.getElementById('purchaseForm');
            if (form) form.reset();

            const nameEl  = document.getElementById('purchaseProductName');
            const metaEl  = document.getElementById('purchaseProductMeta');
            const priceEl = document.getElementById('purchaseUnitPrice');
            if (nameEl)  nameEl.textContent  = productName || '—';
            if (metaEl)  metaEl.textContent  = (category || 'General') + (subcategory ? ' • ' + subcategory : '');
            if (priceEl) priceEl.textContent = '₹' + currentPurchaseProductPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const dateInput = document.getElementById('purchase_date');
            if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

            updatePurchaseTotal();

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseModal'));
            modal.show();
        }

        function updatePurchaseTotal() {
            const qtyInput  = document.getElementById('purchase_quantity');
            const breakdown = document.getElementById('purchaseTotalBreakdown');
            const value     = document.getElementById('purchaseTotalValue');

            const qty   = parseFloat(qtyInput?.value) || 0;
            const price = currentPurchaseProductPrice;
            const total = qty * price;

            if (breakdown) {
                breakdown.textContent = `${qty} × ₹${price.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
            if (value) {
                value.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        document.getElementById('purchase_quantity')?.addEventListener('input', updatePurchaseTotal);

        document.getElementById('purchaseForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!currentPurchaseProductId) return;

            const button = document.getElementById('purchaseSubmitButton');
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Recording...';

            // Only send date + quantity — price is looked up server-side from products.price
            const payload = {
                purchase_date: document.getElementById('purchase_date').value,
                quantity:      parseInt(document.getElementById('purchase_quantity').value, 10),
            };

            const url = purchaseStoreUrlTemplate.replace('__PRODUCT_ID__', currentPurchaseProductId);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    const message = data.message
                        || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Unable to record purchase.');
                    throw new Error(message);
                }

                const modalElement = document.getElementById('purchaseModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();

                updateProductPurchaseCountUI(currentPurchaseProductId, data.purchases_count);
                showToast(data.message || 'Purchase recorded successfully.', 'success');

                // If the history modal for this exact product is currently open,
                // refresh it in place so the new purchase shows up immediately.
                const historyModalEl = document.getElementById('purchaseHistoryModal');
                if (historyModalEl.classList.contains('show') && historyModalEl.dataset.productId == currentPurchaseProductId) {
                    openPurchaseHistory(
                        currentPurchaseProductId,
                        document.getElementById('purchaseProductName')?.textContent,
                        null,
                        null
                    );
                }

                currentPurchaseProductId    = null;
                currentPurchaseProductPrice = 0;

            } catch (error) {
                showToast(error.message, 'danger');
            } finally {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        });

        function updateProductPurchaseCountUI(productId, count) {
            if (count === undefined || count === null) return;
            const row = document.getElementById(`product-row-${productId}`);
            if (!row) return;

            const numberEl = row.querySelector('.product-purchase-count__number');
            const labelEl = row.querySelector('.product-purchase-count__label');
            if (numberEl) numberEl.textContent = count;
            if (labelEl) labelEl.textContent = (parseInt(count, 10) === 1) ? 'purchase' : 'purchases';
        }

        // ========================================================= //
        // VIEW PURCHASES (HISTORY)
        // ========================================================= //

        async function openPurchaseHistory(productId, productName, category, subcategory) {
            const historyModalEl = document.getElementById('purchaseHistoryModal');
            historyModalEl.dataset.productId = productId;

            const metaEl = document.getElementById('purchaseHistoryMeta');
            if (metaEl) {
                metaEl.textContent = productName
                    ? `${productName}${category ? ' — ' + (category || 'General') : ''}${subcategory ? ' • ' + subcategory : ''}`
                    : '—';
            }

            const loading = document.getElementById('purchaseHistoryLoading');
            const content = document.getElementById('purchaseHistoryContent');
            const empty = document.getElementById('purchaseHistoryEmpty');
            const tableBody = document.getElementById('purchaseHistoryTableBody');
            const table = document.querySelector('#purchaseHistoryModal .purchase-history-table');

            loading.style.display = 'block';
            content.style.display = 'none';

            const modal = bootstrap.Modal.getOrCreateInstance(historyModalEl);
            modal.show();

            const url = purchaseHistoryUrlTemplate.replace('__PRODUCT_ID__', productId);

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!response.ok || data.success === false) {
                    throw new Error(data.message || 'Unable to load purchase history.');
                }

                // Prefer the server's own product info (always accurate,
                // even when this was opened from the global picker without
                // category/subcategory passed in).
                if (data.product && metaEl) {
                    const p = data.product;
                    metaEl.textContent = `${p.product_name} — ${p.category || 'General'}${p.subcategory ? ' • ' + p.subcategory : ''}`;
                }

                const purchases = data.purchases || data.data || [];

                loading.style.display = 'none';
                content.style.display = 'block';

                if (!purchases.length) {
                    if (table) table.style.display = 'none';
                    empty.style.display = 'block';
                    empty.querySelector('p').textContent = 'No purchase records found.';
                } else {
                    if (table) table.style.display = '';
                    empty.style.display = 'none';

                    tableBody.innerHTML = purchases.map(function (purchase, index) {
                        const qty   = parseInt(purchase.quantity, 10) || 0;
                        const price = purchase.price_per_unit || '0.00';
                        const total = purchase.total_amount   || '0.00';
                        return `<tr>
                            <td class="text-muted" style="font-size:0.78rem;font-weight:600;">${index + 1}</td>
                            <td><i class="bi bi-calendar3 me-1 text-muted" style="font-size:0.8rem;"></i>${purchase.purchase_date}</td>
                            <td class="text-center"><span class="purchase-qty-badge">${qty}</span></td>
                            <td class="text-end" style="font-weight:600;">&#8377;${price}</td>
                            <td class="text-end" style="font-weight:700;color:#4338CA;">&#8377;${total}</td>
                        </tr>`;
                    }).join('');
                }

                // Use server-calculated summary values for accuracy
                const summary = data.summary || {};
                document.getElementById('purchaseSummaryCount').textContent    = summary.total_purchases ?? purchases.length;
                document.getElementById('purchaseSummaryQuantity').textContent  = summary.total_quantity  ?? 0;
                document.getElementById('purchaseSummaryAmount').textContent    = '₹' + (summary.total_amount ?? '0.00');

            } catch (error) {
                loading.style.display = 'none';
                content.style.display = 'block';
                if (table) table.style.display = 'none';
                empty.style.display = 'block';
                empty.querySelector('p').textContent = error.message;
                showToast(error.message, 'danger');
            }
        }

        function formatPurchaseDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            if (isNaN(d)) return dateStr;
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        // ========================================================= //
        // GLOBAL "PURCHASE HISTORY" BUTTON — PRODUCT PICKER
        // (no product context of its own, so let the user search
        //  the products already rendered on this page and pick one)
        // ========================================================= //

        function openPurchaseHistoryPicker() {
            const searchInput = document.getElementById('productPickerSearch');
            if (searchInput) searchInput.value = '';
            renderProductPickerList('');

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('productPickerModal'));
            modal.show();
            setTimeout(() => searchInput?.focus(), 300);
        }

        function renderProductPickerList(query) {
            const list = document.getElementById('productPickerList');
            const empty = document.getElementById('productPickerEmpty');
            if (!list) return;

            const rows = Array.from(document.querySelectorAll('.premium-list-item[id^="product-row-"]'));
            const q = (query || '').trim().toLowerCase();

            const matches = rows.filter(row => {
                const name = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
                return name.includes(q);
            });

            if (!matches.length) {
                list.innerHTML = '';
                empty.style.display = 'block';
                return;
            }
            empty.style.display = 'none';

            list.innerHTML = matches.map(row => {
                const id = row.id.replace('product-row-', '');
                const name = row.querySelector('.product-name')?.textContent || '';
                const meta = row.querySelector('.pli-product-cat')?.textContent || '';
                return `<button type="button" class="product-picker-item"
                            data-product-id="${id}"
                            data-product-name="${escapeHtml(name)}"
                            data-product-meta="${escapeHtml(meta)}">
                        <span class="pli-icon pli-icon--blue"><i class="bi bi-box-seam-fill"></i></span>
                        <span class="product-picker-item__text">
                            <span class="product-picker-item__name">${escapeHtml(name)}</span>
                            <span class="product-picker-item__meta">${escapeHtml(meta)}</span>
                        </span>
                        <i class="bi bi-chevron-right"></i>
                    </button>`;
            }).join('');
        }

        document.getElementById('productPickerSearch')?.addEventListener('input', function () {
            renderProductPickerList(this.value);
        });

        document.getElementById('productPickerList')?.addEventListener('click', function (e) {
            const btn = e.target.closest('.product-picker-item');
            if (!btn) return;

            const productId = btn.dataset.productId;
            const name = btn.dataset.productName;
            const meta = btn.dataset.productMeta || '';
            const parts = meta.split('•').map(s => s.trim());
            const category = parts[0] || '';
            const subcategory = parts[1] || '';

            const pickerModalEl = document.getElementById('productPickerModal');
            const pickerModal = bootstrap.Modal.getInstance(pickerModalEl);
            if (pickerModal) pickerModal.hide();

            openPurchaseHistory(productId, name, category, subcategory);
        });

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }
    </script>
@endpush
