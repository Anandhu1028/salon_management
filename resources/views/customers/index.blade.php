@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')

    <div class="customer-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel' => 'Add Customer',
            'addModal' => '#customerModal',
            'addOnclick' => 'openAddCustomerModal()',
            'filterRoute' => route('customers.index'),
            'filter' => $filter ?? '',
            'search' => $search ?? '',
            'filterOptions' => [
                '' => 'All Customers',
                'with_email' => 'With Email',
                'new_month' => 'New This Month',
            ],
            'excelUrl' => route('customers.export.excel', request()->query()),
            'pdfUrl' => route('customers.export.pdf', request()->query()),
        ])

        {{-- Stats Row --}}
        @php
            $totalCustomers = \App\Models\Customer::count();
            $newThisMonth = \App\Models\Customer::where('created_at', '>=', now()->startOfMonth())->count();
            $withEmail = \App\Models\Customer::whereNotNull('email')->where('email', '!=', '')->count();
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
                'theme' => 'blue',
                'icon' => 'people-blue',
                'label' => 'Registered Emails',
                'value' => $withEmail,
                'subtext' => 'With email on file',
                'sparkColor' => '#3B82F6',
                'trend' => '3.2',
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
                        <span class="pli-head-cell col-center">Email</span>
                        <span class="pli-head-cell col-center">Contact</span>
                        <span class="pli-head-cell col-center">Joined</span>
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
                                    <span class="pli-title customer-name">{{ $customer->name }}</span>
                                </div>
                            </div>

                            <div class="pli-col col-center">
                                @if($customer->email)
                                    <div class="pli-contact-cell">
                                        @include('partials.contact-icons', ['type' => 'mail'])
                                        <span class="pli-col-text">{{ $customer->email }}</span>
                                    </div>
                                @else
                                    <span class="pli-col-text text-muted">—</span>
                                @endif
                            </div>

                            <div class="pli-col col-center">
                                @if($customer->mobile_number)
                                    <div class="pli-contact-cell">
                                        @include('partials.contact-icons', ['type' => 'phone'])
                                        <span class="pli-col-text">{{ $customer->mobile_number }}</span>
                                    </div>
                                @else
                                    <span class="pli-col-text text-muted">—</span>
                                @endif
                            </div>

                            <div class="pli-col col-center">
                                <span
                                    class="pli-col-text">{{ $customer->created_at ? $customer->created_at->format('d M Y') : '—' }}</span>
                            </div>

                            <div class="pli-col col-center status-cell">
                                <span class="status-badge status-active">
                                    <span></span>
                                    <span class="status-text">Active</span>
                                </span>
                            </div>

                            <div class="pli-col pli-col-actions col-actions actions-cell">
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

                            {{-- Email --}}
                            <div class="form-field">
                                <label for="customer_email" class="form-label">
                                    Email Address
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="customer_email" class="form-control"
                                        placeholder="e.g. customer@example.com">
                                </div>
                            </div>

                            {{-- Mobile --}}
                            <div class="form-field">
                                <label for="customer_mobile" class="form-label">
                                    Mobile Number
                                </label>
                                <div class="field-control-wrap">
                                    <span class="form-field-icon"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="mobile_number" id="customer_mobile" class="form-control"
                                        placeholder="e.g. +91 98765 43210" maxlength="20">
                                </div>
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

            function openAddCustomerModal() {
                const form = document.getElementById('customerForm');
                form.reset();
                form.action = "{{ route('customers.store') }}";
                document.getElementById('customerFormMethod').value = 'POST';
                document.getElementById('customerModalTitle').textContent = 'Add Customer';
                document.getElementById('customerModalSubtitle').textContent = 'Add a new customer to your salon.';
                document.getElementById('customerSubmitButton').innerHTML = '<i class="bi bi-person-plus"></i> Create Customer';
            }

            function openEditCustomerModal(customer) {
                const form = document.getElementById('customerForm');
                form.action = `/customers/${customer.id}`;
                document.getElementById('customerFormMethod').value = 'PUT';
                document.getElementById('customerModalTitle').textContent = 'Edit Customer';
                document.getElementById('customerModalSubtitle').textContent = 'Update customer information.';
                document.getElementById('customerSubmitButton').innerHTML = '<i class="bi bi-check2-circle"></i> Update Customer';

                document.getElementById('customer_name').value = customer.name ?? '';
                document.getElementById('customer_email').value = customer.email ?? '';
                document.getElementById('customer_mobile').value = customer.mobile_number ?? '';
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