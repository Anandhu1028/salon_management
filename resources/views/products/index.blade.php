@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')

    <div class="product-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel' => 'Add Product',
            'addModal' => '#productModal',
            'addOnclick' => 'openAddProductModal()',
            'filterRoute' => route('products.index'),
            'filter' => $filter ?? '',
            'search' => $search ?? '',
            'filterOptions' => [
                '' => 'All Products',
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
            'excelUrl' => route('products.export.excel', request()->query()),
            'pdfUrl' => route('products.export.pdf', request()->query()),
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
                        <span class="pli-head-cell col-center">Category</span>
                        <span class="pli-head-cell col-center">Sub Category</span>
                        <span class="pli-head-cell col-center">Price</span>
                        <span class="pli-head-cell col-center">Status</span>
                        <span class="pli-head-cell col-center">Actions</span>
                    </div>

                    @foreach($products as $product)
                        <article class="premium-list-item" id="product-row-{{ $product->id }}">
                            <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                            <div class="pli-col col-left">
                                <div class="pli-name-cell">
                                    <div class="pli-icon pli-icon--blue">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </div>
                                    <span class="pli-title">{{ $product->product_name }}</span>
                                </div>
                            </div>

                            <div class="pli-col col-center">
                                <span class="pli-col-text">{{ $product->category ?: '—' }}</span>
                            </div>

                            <div class="pli-col col-center">
                                <span class="pli-col-text">{{ $product->subcategory ?: '—' }}</span>
                            </div>

                            <div class="pli-col col-center price-cell">
                                <span class="pli-col-text pli-col-price">₹{{ number_format($product->price, 2) }}</span>
                            </div>

                            <div class="pli-col col-center status-cell">
                                <span id="status-badge-{{ $product->id }}"
                                    class="status-badge {{ $product->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    <span></span>
                                    <span class="status-text">{{ ucfirst($product->status) }}</span>
                                </span>
                            </div>

                            <div class="pli-col pli-col-actions col-actions actions-cell">
                                @include('partials.status-toggle', [
                                    'id' => $product->id,
                                    'status' => $product->status,
                                    'onChange' => 'onProductStatusToggle(' . $product->id . ', ' . json_encode($product->product_name) . ', this)',
                                ])
                                <button type="button" class="pli-btn-icon pli-btn-icon--edit" title="Edit Product"
                                    data-bs-toggle="modal" data-bs-target="#productModal"
                                    onclick='openEditProductModal(@json($product))'>
                                    @include('partials.action-icons', ['type' => 'edit', 'size' => 16])
                                </button>
                                <button type="button" class="pli-btn-icon pli-btn-icon--danger" title="Delete Product"
                                    onclick="openDeleteProductModal({{ $product->id }}, @js($product->product_name))">
                                    @include('partials.action-icons', ['type' => 'delete', 'size' => 16])
                                </button>
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

                            {{-- Price --}}
                            <div class="form-field">
                                <label for="product_price" class="form-label">
                                    Price (₹) <span>*</span>
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-currency-rupee"></i></span>
                                    <input type="number" name="price" id="product_price" class="form-control"
                                        placeholder="0.00" min="0" step="0.01" required>
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


@endsection


@push('scripts')
    <script>
        let currentProductId = null;
        let currentProductTargetStatus = null;
        let deleteProductId = null;

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
            document.getElementById('product_price').value = product.price ?? '';
            document.getElementById('product_status').value = product.status ?? 'active';
        }

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

            if (!badge || !toggle) return;

            const isActive = status === 'active';
            toggle.checked = isActive;

            const label = toggle.closest('.mgmt-status-toggle')?.querySelector('.mgmt-status-toggle__text');
            if (label) {
                label.textContent = isActive ? label.dataset.activeText : label.dataset.inactiveText;
            }

            if (isActive) {
                badge.className = 'status-badge status-active';
                badge.innerHTML = '<span></span><span class="status-text">Active</span>';
            } else {
                badge.className = 'status-badge status-inactive';
                badge.innerHTML = '<span></span><span class="status-text">Inactive</span>';
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
    </script>
@endpush