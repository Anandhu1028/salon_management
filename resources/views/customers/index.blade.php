@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')

    <div class="customer-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel' => 'Add Customer',
            'addModal' => '#customerModal',
            'addOnclick' => 'openAddCustomerModal()',
            'filterModule' => 'customers',
            'filterRoute' => route('customers.index'),
            'excelUrl' => route('customers.export.excel', request()->query()),
            'pdfUrl' => route('customers.export.pdf', request()->query()),
        ])

        {{-- Stats Row --}}
        @php
            $totalCustomers = \App\Models\Customer::count();
            $newThisMonth = \App\Models\Customer::where('created_at', '>=', now()->startOfMonth())->count();
            $withWhatsapp = \App\Models\Customer::whereNotNull('whatsapp_number')->where('whatsapp_number', '!=', '')->count();
        @endphp
        <div class="mgmt-stats-grid">
            @include('partials.mgmt-stat-card', [
                'theme' => 'pink',
                'icon' => 'heart-pink',
                'label' => 'Total Customers',
                'value' => $totalCustomers,
                'subtext' => 'Registered clients',
                'sparkColor' => '#EC4899',
                'trend' => '11.3',
                'trendUp' => true,
            ])
            @include('partials.mgmt-stat-card', [
                'theme' => 'orange',
                'icon' => 'calendar-orange',
                'label' => 'New This Month',
                'value' => $newThisMonth,
                'subtext' => 'Recent sign-ups',
                'sparkColor' => '#F59E0B',
                'trend' => '7.5',
                'trendUp' => true,
            ])
            @include('partials.mgmt-stat-card', [
                'theme' => 'green',
                'icon' => 'shield-green',
                'label' => 'Registered WhatsApp',
                'value' => $withWhatsapp,
                'subtext' => 'With WhatsApp on file',
                'sparkColor' => '#22C55E',
                'trend' => '8.4',
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

        {{-- Customer Card --}}
        <div class="content-card">

            {{-- Card Header --}}
            <div class="content-card-header">
                <div>
                    <h2>Customer List</h2>
                    <span>{{ $customers->total() }} customers registered</span>
                </div>

                <div class="content-card-header-actions">
                    <form method="GET" action="{{ route('customers.index') }}" class="customer-search">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search customers...">
                            @if(!empty($filter))
                                <input type="hidden" name="filter" value="{{ $filter }}">
                            @endif
                            @if($search)
                                <a href="{{ route('customers.index', array_filter(['filter' => $filter ?? ''])) }}"
                                    title="Clear search">
                                    <i class="bi bi-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if($customers->count())

                @php $listStart = ($customers->currentPage() - 1) * $customers->perPage(); @endphp

                <div class="premium-list premium-list--customer premium-list--feed premium-list--compact premium-list--mgmt">
                    <div class="premium-list-head">
                        <span class="pli-head-cell col-center">#</span>
                        <span class="pli-head-cell col-left">Name</span>
                        <span class="pli-head-cell col-center">WhatsApp</span>
                        <span class="pli-head-cell col-center">Contact</span>
                        <span class="pli-head-cell col-center">Status</span>
                        <span class="pli-head-cell col-center">Actions</span>
                    </div>

                    @foreach($customers as $customer)
                        <article class="premium-list-item" id="customer-row-{{ $customer->id }}">
                            <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                            <div class="pli-col col-left">
                                <div class="pli-name-cell">
                                    <div class="pli-icon pli-icon--pink">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div class="pli-name-stack">
                                        <span class="pli-title customer-name">{{ $customer->name }}</span>
                                        @if($customer->whatsapp_number)
                                            <span class="pli-subtext pli-customer-whatsapp d-md-none"><i class="bi bi-whatsapp text-success"></i> {{ $customer->whatsapp_country_code ? $customer->whatsapp_country_code . ' ' : '' }}{{ $customer->whatsapp_number }}</span>
                                        @endif
                                        @if($customer->mobile_number)
                                            <span class="pli-subtext pli-customer-phone d-md-none"><i class="bi bi-telephone"></i> {{ $customer->mobile_country_code ? $customer->mobile_country_code . ' ' : '' }}{{ $customer->mobile_number }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="pli-col col-center pli-col-whatsapp">
                                @if($customer->whatsapp_number)
                                    <div class="pli-contact-cell">
                                        @include('partials.contact-icons', ['type' => 'whatsapp'])
                                        <span class="pli-col-text">{{ $customer->whatsapp_country_code ? $customer->whatsapp_country_code . ' ' : '' }}{{ $customer->whatsapp_number }}</span>
                                    </div>
                                @else
                                    <span class="pli-col-text text-muted">—</span>
                                @endif
                            </div>

                            <div class="pli-col col-center pli-col-contact">
                                @if($customer->mobile_number)
                                    <div class="pli-contact-cell">
                                        @include('partials.contact-icons', ['type' => 'phone'])
                                        <span class="pli-col-text">{{ $customer->mobile_country_code ? $customer->mobile_country_code . ' ' : '' }}{{ $customer->mobile_number }}</span>
                                    </div>
                                @else
                                    <span class="pli-col-text text-muted">—</span>
                                @endif
                            </div>

                            <div class="pli-col col-center status-cell">
                                <span id="status-badge-{{ $customer->id }}"
                                    class="status-badge {{ $customer->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    <span></span>
                                    <span class="status-text">{{ ucfirst($customer->status) }}</span>
                                </span>
                            </div>

                            <div class="pli-col pli-col-actions col-actions actions-cell">
                                <div class="dropdown pli-dots-dropdown d-md-none">
                                    <span id="mob-cust-status-badge-{{ $customer->id }}"
                                        class="pli-mob-status-dot {{ $customer->status === 'active' ? 'pli-mob-status-dot--active' : 'pli-mob-status-dot--inactive' }}"
                                        title="{{ ucfirst($customer->status) }}"></span>
                                    <button class="pli-btn-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end pli-action-menu">
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item"
                                                data-bs-toggle="modal" data-bs-target="#customerModal"
                                                onclick='openEditCustomerModal(@json($customer))'>
                                                <span class="pli-menu-icon pli-menu-icon--edit"><i class="bi bi-pencil"></i></span>
                                                <span>Edit Customer</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item pli-menu-status-btn"
                                                id="mob-cust-status-btn-{{ $customer->id }}"
                                                data-status="{{ $customer->status }}"
                                                onclick="triggerCustomerStatusToggle({{ $customer->id }}, @js($customer->name))">
                                                <span class="pli-menu-status-left">
                                                    <span class="pli-menu-icon pli-menu-icon--status {{ $customer->status === 'active' ? 'pli-status-active' : 'pli-status-inactive' }}">
                                                        <i class="bi {{ $customer->status === 'active' ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                    </span>
                                                    <span>Toggle Status</span>
                                                </span>
                                                <span class="pli-menu-status-state {{ $customer->status === 'active' ? 'active' : 'inactive' }}">
                                                    {{ ucfirst($customer->status) }}
                                                </span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="pli-action-buttons-desktop d-none d-md-inline-flex">
                                    @include('partials.status-toggle', [
                                        'id' => $customer->id,
                                        'status' => $customer->status,
                                        'onChange' => 'onCustomerStatusToggle(' . $customer->id . ', ' . json_encode($customer->name) . ', this)',
                                    ])
                                    <button type="button" class="pli-btn-icon pli-btn-icon--edit" title="Edit Customer"
                                        data-bs-toggle="modal" data-bs-target="#customerModal"
                                        onclick='openEditCustomerModal(@json($customer))'>
                                        @include('partials.action-icons', ['type' => 'edit', 'size' => 16])
                                    </button>
                                    <button type="button" class="pli-btn-icon pli-btn-icon--danger" title="Delete Customer"
                                        onclick="openDeleteCustomerModal({{ $customer->id }}, @js($customer->name))">
                                        @include('partials.action-icons', ['type' => 'delete', 'size' => 16])
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @include('partials.pagination-bar', ['paginator' => $customers])

            @else

                {{-- Empty State --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <h3>No customers found</h3>
                    <p>Start building your customer list by adding your first customer.</p>
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#customerModal"
                        onclick="openAddCustomerModal()">
                        @include('partials.action-icons', ['type' => 'add'])
                        Add Customer
                    </button>
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ADD / EDIT CUSTOMER MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal premium-modal--md" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="customerForm" method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="customerFormMethod" value="POST">

                    {{-- Header --}}
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-icon-box accent">
                                <i class="bi bi-person-heart"></i>
                            </div>
                            <div class="modal-header-content">
                                <h5 class="modal-title" id="customerModalTitle">Add Customer</h5>
                                <p class="modal-subtitle" id="customerModalSubtitle">Add a new customer to your salon.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <div class="modal-form-grid">

                            {{-- Name — full width --}}
                            <div class="form-field form-field--full">
                                <label for="customer_name" class="form-label">
                                    Customer Name <span>*</span>
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" id="customer_name" class="form-control"
                                        placeholder="Enter customer's full name" required>
                                </div>
                            </div>

                            {{-- WhatsApp --}}
                            <div class="form-field">
                                <label for="customer_whatsapp" class="form-label">
                                    WhatsApp Number <span>*</span>
                                </label>
                                <div class="phone-input-group">
                                    <div class="phone-prefix-box">
                                        <select name="whatsapp_country_code" id="customer_whatsapp_country_code" class="phone-prefix-select">
                                            @foreach($countryCodes ?? [] as $code)
                                                <option value="{{ $code->dial_code }}" {{ $code->is_default ? 'selected' : '' }} title="{{ $code->name }}">
                                                    {{ $code->dial_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="bi bi-chevron-down phone-prefix-arrow"></i>
                                    </div>
                                    <div class="phone-number-box">
                                        <span class="form-field-icon"><i class="bi bi-whatsapp"></i></span>
                                        <input type="tel" name="whatsapp_number" id="customer_whatsapp" class="form-control"
                                            placeholder="98765 43210" maxlength="20">
                                    </div>
                                </div>
                                <span class="form-field-hint">Enter 10-digit WhatsApp number</span>
                            </div>

                            {{-- Mobile --}}
                            <div class="form-field">
                                <label for="customer_mobile" class="form-label">
                                    Mobile Number <span>*</span>
                                </label>
                                <div class="phone-input-group">
                                    <div class="phone-prefix-box">
                                        <select name="mobile_country_code" id="customer_mobile_country_code" class="phone-prefix-select">
                                            @foreach($countryCodes ?? [] as $code)
                                                <option value="{{ $code->dial_code }}" {{ $code->is_default ? 'selected' : '' }} title="{{ $code->name }}">
                                                    {{ $code->dial_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="bi bi-chevron-down phone-prefix-arrow"></i>
                                    </div>
                                    <div class="phone-number-box">
                                        <span class="form-field-icon"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" name="mobile_number" id="customer_mobile" class="form-control"
                                            placeholder="98765 43210" maxlength="20">
                                    </div>
                                </div>
                                <span class="form-field-hint">Enter 10-digit mobile number</span>
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="customerSubmitButton">
                            <i class="bi bi-person-plus"></i> Create Customer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- STATUS CONFIRMATION MODAL --}}
    {{-- ========================================================= --}}
    <div class="modal fade premium-modal" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="confirm-modal-body">
                    <div class="confirm-icon warning" id="statusConfirmIcon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="confirm-title" id="statusConfirmTitle">Change Status?</h5>
                    <p class="confirm-message" id="statusConfirmMessage">
                        Are you sure you want to change this customer's status?
                    </p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="cancelStatusButton">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="confirmStatusButton">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- DELETE CONFIRMATION MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="confirm-modal-body">
                    <div class="confirm-icon danger">
                        @include('partials.action-icons', ['type' => 'delete'])
                    </div>
                    <h5 class="confirm-title">Delete Customer?</h5>
                    <p class="confirm-message" id="deleteCustomerMessage">This action cannot be undone.</p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteCustomerButton">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            let deleteCustomerId = null;
            let currentCustomerId = null;
            let currentTargetStatus = null;

            function triggerCustomerStatusToggle(customerId, customerName) {
                const mobBtn = document.getElementById(`mob-cust-status-btn-${customerId}`);
                const currentStatus = mobBtn ? mobBtn.dataset.status : 'active';
                const targetStatus = currentStatus === 'active' ? 'inactive' : 'active';

                currentCustomerId = customerId;
                currentTargetStatus = targetStatus;

                const message = `Are you sure you want to mark ${customerName} as ${targetStatus}?`;
                document.getElementById('statusConfirmMessage').textContent = message;

                const icon = document.getElementById('statusConfirmIcon');
                if (targetStatus === 'inactive') {
                    icon.className = 'confirm-icon danger';
                    icon.innerHTML = '<i class="bi bi-person-x"></i>';
                } else {
                    icon.className = 'confirm-icon warning';
                    icon.innerHTML = '<i class="bi bi-person-check"></i>';
                }

                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('statusConfirmModal'));
                modal.show();
            }

            function onCustomerStatusToggle(customerId, customerName, toggleElement) {
                const targetStatus = toggleElement.checked ? 'active' : 'inactive';
                toggleElement.checked = !toggleElement.checked; // Revert until confirmed

                currentCustomerId = customerId;
                currentTargetStatus = targetStatus;

                const message = `Are you sure you want to mark ${customerName} as ${targetStatus}?`;
                document.getElementById('statusConfirmMessage').textContent = message;

                const icon = document.getElementById('statusConfirmIcon');
                if (targetStatus === 'inactive') {
                    icon.className = 'confirm-icon danger';
                    icon.innerHTML = '<i class="bi bi-person-x"></i>';
                } else {
                    icon.className = 'confirm-icon warning';
                    icon.innerHTML = '<i class="bi bi-person-check"></i>';
                }

                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('statusConfirmModal'));
                modal.show();
            }

            document.getElementById('confirmStatusButton')?.addEventListener('click', async function () {
                if (!currentCustomerId) return;

                const button = this;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                try {
                    const response = await fetch(`/customers/${currentCustomerId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to update status.');
                    }

                    const modalElement = document.getElementById('statusConfirmModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    updateCustomerStatusUI(currentCustomerId, data.status);
                    showToast(data.message, 'success');

                    currentCustomerId = null;
                    currentTargetStatus = null;

                } catch (error) {
                    showToast(error.message, 'danger');
                } finally {
                    button.disabled = false;
                    button.textContent = 'Confirm';
                }
            });

            function updateCustomerStatusUI(customerId, status) {
                const badge = document.getElementById(`status-badge-${customerId}`);
                const toggle = document.getElementById(`status-toggle-${customerId}`);
                const mobBtn = document.getElementById(`mob-cust-status-btn-${customerId}`);
                const isActive = status === 'active';

                if (toggle) {
                    toggle.checked = isActive;
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

                if (mobBtn) {
                    mobBtn.dataset.status = status;
                    const icon = mobBtn.querySelector('.pli-menu-icon');
                    if (icon) {
                        icon.className = 'pli-menu-icon pli-menu-icon--status ' + (isActive ? 'pli-status-active' : 'pli-status-inactive');
                        icon.innerHTML = `<i class="bi ${isActive ? 'bi-toggle-on' : 'bi-toggle-off'}"></i>`;
                    }
                    const stateBadge = mobBtn.querySelector('.pli-menu-status-state');
                    if (stateBadge) {
                        stateBadge.className = 'pli-menu-status-state ' + (isActive ? 'active' : 'inactive');
                        stateBadge.textContent = isActive ? 'Active' : 'Inactive';
                    }
                }

                const mobDot = document.getElementById(`mob-cust-status-badge-${customerId}`);
                if (mobDot) {
                    mobDot.className = 'pli-mob-status-dot ' + (isActive ? 'pli-mob-status-dot--active' : 'pli-mob-status-dot--inactive');
                    mobDot.title = isActive ? 'Active' : 'Inactive';
                }
            }

            function openAddCustomerModal() {
                const form = document.getElementById('customerForm');
                form.reset();
                form.action = "{{ route('customers.store') }}";
                document.getElementById('customerFormMethod').value = 'POST';
                document.getElementById('customerModalTitle').textContent = 'Add Customer';
                document.getElementById('customerModalSubtitle').textContent = 'Add a new customer to your salon.';
                document.getElementById('customerSubmitButton').innerHTML = '<i class="bi bi-person-plus"></i> Create Customer';
                document.getElementById('customer_mobile_country_code').value = '+91';
                document.getElementById('customer_whatsapp_country_code').value = '+91';
            }

            function openEditCustomerModal(customer) {
                const form = document.getElementById('customerForm');
                form.action = `/customers/${customer.id}`;
                document.getElementById('customerFormMethod').value = 'PUT';
                document.getElementById('customerModalTitle').textContent = 'Edit Customer';
                document.getElementById('customerModalSubtitle').textContent = 'Update customer information.';
                document.getElementById('customerSubmitButton').innerHTML = '<i class="bi bi-check2-circle"></i> Update Customer';

                document.getElementById('customer_name').value = customer.name ?? '';
                document.getElementById('customer_mobile_country_code').value = customer.mobile_country_code || '+91';
                document.getElementById('customer_mobile').value = customer.mobile_number ?? '';
                document.getElementById('customer_whatsapp_country_code').value = customer.whatsapp_country_code || '+91';
                document.getElementById('customer_whatsapp').value = customer.whatsapp_number ?? '';
            }

            function openDeleteCustomerModal(customerId, customerName) {
                deleteCustomerId = customerId;
                document.getElementById('deleteCustomerMessage').textContent = `Are you sure you want to delete ${customerName}?`;
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteCustomerModal'));
                modal.show();
            }

            document.getElementById('confirmDeleteCustomerButton').addEventListener('click', async function () {
                if (!deleteCustomerId) return;

                const button = this;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    showToast('CSRF token missing.', 'danger');
                    button.disabled = false;
                    button.textContent = 'Delete';
                    return;
                }

                try {
                    const response = await fetch(`/customers/${deleteCustomerId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to delete customer.');
                    }

                    const modalElement = document.getElementById('deleteCustomerModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    const row = document.getElementById(`customer-row-${deleteCustomerId}`);
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
                    deleteCustomerId = null;
                    button.disabled = false;
                    button.textContent = 'Delete';
                }
            });
        </script>
    @endpush

@endsection