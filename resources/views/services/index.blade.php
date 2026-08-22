@extends('layouts.app')

@section('title', 'Services')
@section('page-title', 'Services')

@push('styles')
<style>
    #serviceNameIcon { transition: color .18s ease, transform .18s ease; }
    #serviceNameIcon.is-changing { transform: scale(1.14); color: #7C3AED; }
    #serviceModalHeaderIcon .bi { color: #7C3AED; font-size: 1.35rem; }
</style>
@endpush

@section('content')

    <div class="service-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel' => 'Add Service',
            'addModal' => '#serviceModal',
            'addOnclick' => 'openAddServiceModal()',
            'filterModule' => 'services',
            'filterRoute' => route('services.index'),
            'filterData' => [
                'categories' => $filterCategories ?? [],
                'subcategories' => $filterSubcategories ?? [],
            ],
            'excelUrl' => route('services.export.excel', request()->query()),
            'pdfUrl' => route('services.export.pdf', request()->query()),
        ])

        {{-- Stats Row --}}
        @php
            $totalServices = \App\Models\Service::count();
            $activeServices = \App\Models\Service::where('status', 'active')->count();
            $inactiveServices = \App\Models\Service::where('status', 'inactive')->count();
        @endphp
        <div class="mgmt-stats-grid">
            @include('partials.mgmt-stat-card', [
                'theme' => 'violet',
                'icon' => 'scissors-violet',
                'label' => 'Total Services',
                'value' => $totalServices,
                'subtext' => 'All offerings',
                'sparkColor' => '#8B5CF6',
                'trend' => '9.8',
                'trendUp' => true,
            ])
            @include('partials.mgmt-stat-card', [
                'theme' => 'green',
                'icon' => 'shield-green',
                'label' => 'Active Services',
                'value' => $activeServices,
                'subtext' => 'Bookable now',
                'sparkColor' => '#22C55E',
                'trend' => '5.6',
                'trendUp' => true,
            ])
            @include('partials.mgmt-stat-card', [
                'theme' => 'red',
                'icon' => 'user-x-red',
                'label' => 'Inactive Services',
                'value' => $inactiveServices,
                'subtext' => 'Not available',
                'sparkColor' => '#EF4444',
                'trend' => '0.8',
                'trendUp' => false,
            ])
        </div>

        {{-- Success --}}
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

        {{-- Services Card --}}
        <div class="content-card">

            {{-- Header --}}
            <div class="content-card-header">
                <div>
                    <h2>Service List</h2>
                    <span>{{ $services->total() }} services offered</span>
                </div>

                <div class="content-card-header-actions">
                    <form method="GET" action="{{ route('services.index') }}" class="service-search">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search services...">
                            @if(!empty($filter))
                                <input type="hidden" name="filter" value="{{ $filter }}">
                            @endif
                            @if($search)
                                <a href="{{ route('services.index', array_filter(['filter' => $filter ?? ''])) }}"
                                    title="Clear search">
                                    <i class="bi bi-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if($services->count())

                @php $listStart = ($services->currentPage() - 1) * $services->perPage(); @endphp

                <div
                    class="premium-list premium-list--catalog premium-list--feed premium-list--compact premium-list--mgmt premium-list--service">
                    <div class="premium-list-head">
                        <span class="pli-head-cell col-center">#</span>
                        <span class="pli-head-cell col-left">Name</span>
                        <span class="pli-head-cell col-center">Category</span>
                        <span class="pli-head-cell col-center">Sub Category</span>
                        <span class="pli-head-cell col-center">Status</span>
                        <span class="pli-head-cell col-center">Actions</span>
                    </div>

                    @foreach($services as $service)
                        <article class="premium-list-item" id="service-row-{{ $service->id }}">
                            <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                            <div class="pli-col col-left">
                                <div class="pli-name-cell">
                                    @include('partials.service-icons.icon', [
                                        'key' => $service->icon,
                                        'size' => 'sm',
                                    ])
                                    <div class="pli-name-stack">
                                        <span class="pli-title service-name">{{ $service->service_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pli-col col-center pli-col-category">
                                <span class="pli-col-text">{{ $service->category ?: '—' }}</span>
                            </div>

                            <div class="pli-col col-center pli-col-subcategory">
                                <span class="pli-col-text">{{ $service->subcategory ?: '—' }}</span>
                            </div>

                            

                            <div class="pli-col col-center status-cell">
                                <span id="status-badge-{{ $service->id }}"
                                    class="status-badge {{ $service->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    <span></span>
                                    <span class="status-text">{{ ucfirst($service->status) }}</span>
                                </span>
                            </div>

                            <div class="pli-col pli-col-actions col-actions actions-cell">
                                <div class="dropdown pli-dots-dropdown d-md-none">
                                    <span id="mob-svc-status-badge-{{ $service->id }}"
                                        class="pli-mob-status-dot {{ $service->status === 'active' ? 'pli-mob-status-dot--active' : 'pli-mob-status-dot--inactive' }}"
                                        title="{{ ucfirst($service->status) }}"></span>
                                    <button class="pli-btn-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end pli-action-menu">
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item"
                                                data-bs-toggle="modal" data-bs-target="#serviceModal"
                                                onclick='openEditServiceModal(@json($service))'>
                                                <span class="pli-menu-icon pli-menu-icon--edit"><i class="bi bi-pencil"></i></span>
                                                <span>Edit Service</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item pli-menu-item pli-menu-status-btn"
                                                id="mob-status-btn-{{ $service->id }}"
                                                data-status="{{ $service->status }}"
                                                onclick="triggerServiceStatusToggle({{ $service->id }}, @js($service->service_name))">
                                                <span class="pli-menu-status-left">
                                                    <span class="pli-menu-icon pli-menu-icon--status {{ $service->status === 'active' ? 'pli-status-active' : 'pli-status-inactive' }}">
                                                        <i class="bi {{ $service->status === 'active' ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                    </span>
                                                    <span>Toggle Status</span>
                                                </span>
                                                <span class="pli-menu-status-state {{ $service->status === 'active' ? 'active' : 'inactive' }}">
                                                    {{ ucfirst($service->status) }}
                                                </span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="pli-action-menu-wrap pli-action-buttons-desktop">
                                    <button
                                        type="button"
                                        class="pli-action-dots"
                                        aria-label="Service actions"
                                        aria-expanded="false"
                                        onclick="togglePliActions(this)"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <div class="pli-action-popover">
                                        <button
                                            type="button"
                                            class="pli-popover-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#serviceModal"
                                            onclick='openEditServiceModal(@json($service)); closePliActions(this)'
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--edit">
                                                <i class="bi bi-pencil"></i>
                                            </span>
                                            <span>Edit Service</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="pli-popover-action pli-popover-status-btn"
                                            id="desk-status-btn-{{ $service->id }}"
                                            data-status="{{ $service->status }}"
                                            onclick="triggerServiceStatusToggle({{ $service->id }}, @js($service->service_name)); closePliActions(this)"
                                        >
                                            <span class="pli-popover-status-left">
                                                <span class="pli-popover-icon pli-popover-icon--status">
                                                    <i class="bi {{ $service->status === 'active' ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                </span>
                                                <span>Toggle Status</span>
                                            </span>
                                        </button>

                                        <div class="pli-popover-divider"></div>

                                        <button
                                            type="button"
                                            class="pli-popover-action pli-popover-action--danger"
                                            onclick="openDeleteServiceModal({{ $service->id }}, @js($service->service_name)); closePliActions(this)"
                                        >
                                            <span class="pli-popover-icon pli-popover-icon--delete">
                                                <i class="bi bi-trash3"></i>
                                            </span>
                                            <span>Delete Service</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @include('partials.pagination-bar', ['paginator' => $services])

            @else

                {{-- Empty State --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-scissors"></i>
                    </div>
                    <h3>No services found</h3>
                    <p>Start building your service list by adding your first salon service.</p>
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#serviceModal"
                        onclick="openAddServiceModal()">
                        @include('partials.action-icons', ['type' => 'add'])
                        Add Service
                    </button>
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ADD / EDIT SERVICE MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal premium-modal--wide" id="serviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="serviceForm" method="POST" action="{{ route('services.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="serviceFormMethod" value="POST">

                    {{-- Header --}}
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div id="serviceModalHeaderIcon"><i class="bi bi-stars"></i></div>
                            <div class="modal-header-content">
                                <h5 class="modal-title" id="serviceModalTitle">Add Service</h5>
                                <p class="modal-subtitle" id="serviceModalSubtitle">Add a new salon service.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">

                        {{-- Service Name --}}
                        <div class="form-field">
                            <label for="service_name" class="form-label">
                                Service Name <span>*</span>
                            </label>
                            <div class="field-control-wrap">
                                <span class="form-field-icon"><i id="serviceNameIcon" class="bi bi-stars"></i></span>
                                <input type="text" name="service_name" id="service_name" class="form-control"
                                    placeholder="e.g. Classic Haircut, Bridal Makeup" required autocomplete="off">
                            </div>
                        </div>

                        <div class="row g-3 service-form-grid">
                            {{-- Category --}}
                            <div class="col-md-6">
                                <div class="form-field mb-0">
                                    <label for="service_category" class="form-label">
                                        Category <span>*</span>
                                    </label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-grid"></i></span>
                                        <input type="text" name="category" id="service_category" class="form-control"
                                            placeholder="e.g. Hair, Treatment, Grooming" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Subcategory --}}
                            <div class="col-md-6">
                                <div class="form-field mb-0">
                                    <label for="service_subcategory" class="form-label">
                                        Subcategory
                                    </label>
                                    <div class="field-control-wrap">
                                        <span class="form-field-icon"><i class="bi bi-collection"></i></span>
                                        <input type="text" name="subcategory" id="service_subcategory" class="form-control"
                                            placeholder="e.g. Men's Hair Cut">
                                    </div>
                                </div>
                            </div>


                            {{-- Status --}}
                            <div class="col-md-6">
                                <div class="form-field mb-0">
                                    <label for="service_status" class="form-label">
                                        Status <span>*</span>
                                    </label>
                                    <select name="status" id="service_status" class="form-select"
                                        data-icon="bi-shield-check" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="serviceSubmitButton">
                            <i class="bi bi-scissors"></i> Create Service
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- STATUS CONFIRMATION MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal" id="serviceStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="confirm-modal-body">
                    <div class="confirm-icon primary" id="serviceStatusIcon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h5 class="confirm-title" id="serviceStatusTitle">Change Status?</h5>
                    <p class="confirm-message" id="serviceStatusMessage">Are you sure?</p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmServiceStatusButton">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- DELETE CONFIRMATION MODAL --}}
    {{-- ========================================================= --}}

    <div class="modal fade premium-modal" id="deleteServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="confirm-modal-body">
                    <div class="confirm-icon danger">
                        @include('partials.action-icons', ['type' => 'delete'])
                    </div>
                    <h5 class="confirm-title">Delete Service?</h5>
                    <p class="confirm-message" id="deleteServiceMessage">This action cannot be undone.</p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteServiceButton">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="{{ asset('js/pli-action-popover.js') }}"></script>
        <script>
            let currentServiceId = null;
            let currentServiceTargetStatus = null;
            let deleteServiceId = null;

            function openAddServiceModal() {
                const form = document.getElementById('serviceForm');
                form.reset();
                form.action = "{{ route('services.store') }}";
                document.getElementById('serviceFormMethod').value = 'POST';
                document.getElementById('serviceModalTitle').textContent = 'Add Service';
               document.getElementById('serviceModalSubtitle').textContent = 'Add a new salon service.';
                document.getElementById('serviceSubmitButton').innerHTML = '<i class="bi bi-scissors"></i> Create Service';
                document.getElementById('service_status').value = 'active';

                updateServiceNameIcon();
            }

            function openEditServiceModal(service) {
                const form = document.getElementById('serviceForm');

                form.action = `/services/${service.id}`;

                document.getElementById('serviceFormMethod').value = 'PUT';

                document.getElementById('serviceModalTitle').textContent =
                    'Edit Service';

                document.getElementById('serviceModalSubtitle').textContent =
                    'Update service information.';

                document.getElementById('serviceSubmitButton').innerHTML =
                    '<i class="bi bi-check2-circle"></i> Update Service';

                document.getElementById('service_name').value =
                    service.service_name ?? '';

                document.getElementById('service_category').value =
                    service.category ?? '';

                document.getElementById('service_subcategory').value =
                    service.subcategory ?? '';

                document.getElementById('service_status').value =
                    service.status ?? 'active';

                updateServiceNameIcon();
            }

            function detectedServiceIcon(name) {
                const value = (name || '').toLowerCase();
                if (/hair|haircut|hairstyle|hair spa|colour|color|smoothing|smoothening|keratin/.test(value)) return 'bi-scissors';
                if (/nail|manicure|pedicure|nail art/.test(value)) return 'bi-hand-index-thumb';
                if (/skin|facial|cleanup|clean up|skin care|treatment/.test(value)) return 'bi-person-hearts';
                if (/makeup|make up|bridal makeup|party makeup/.test(value)) return 'bi-palette';
                return 'bi-stars';
            }

            function updateServiceNameIcon() {
                const icon = detectedServiceIcon(document.getElementById('service_name').value);
                const inputIcon = document.getElementById('serviceNameIcon');
                const headerIcon = document.getElementById('serviceModalHeaderIcon');
                inputIcon.classList.add('is-changing');
                inputIcon.className = `bi ${icon} is-changing`;
                headerIcon.innerHTML = `<i class="bi ${icon}"></i>`;
                setTimeout(() => inputIcon.classList.remove('is-changing'), 180);
            }

            document.getElementById('service_name').addEventListener('input', updateServiceNameIcon);

            function triggerServiceStatusToggle(serviceId, serviceName) {
                const mobBtn = document.getElementById(`mob-status-btn-${serviceId}`);
                const currentStatus = mobBtn ? (mobBtn.dataset.status || 'active') : 'active';
                const targetStatus = currentStatus === 'active' ? 'inactive' : 'active';
                confirmServiceStatus(serviceId, targetStatus, serviceName);
            }

            function onServiceStatusToggle(serviceId, serviceName, input) {
                const targetStatus = input.checked ? 'active' : 'inactive';
                input.checked = !input.checked;
                confirmServiceStatus(serviceId, targetStatus, serviceName);
            }

            function confirmServiceStatus(serviceId, targetStatus, serviceName) {
                currentServiceId = serviceId;
                currentServiceTargetStatus = targetStatus;

                const isActivating = targetStatus === 'active';
                document.getElementById('serviceStatusTitle').textContent = isActivating ? 'Activate Service?' : 'Deactivate Service?';
                document.getElementById('serviceStatusMessage').textContent = isActivating
                    ? `Are you sure you want to activate ${serviceName}?`
                    : `Are you sure you want to deactivate ${serviceName}?`;

                const iconBox = document.getElementById('serviceStatusIcon');
                iconBox.className = isActivating ? 'confirm-icon success' : 'confirm-icon warning';

                const button = document.getElementById('confirmServiceStatusButton');
                button.textContent = isActivating ? 'Activate' : 'Deactivate';
                button.className = isActivating ? 'btn btn-success' : 'btn btn-danger';

                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('serviceStatusModal'));
                modal.show();
            }

            document.getElementById('confirmServiceStatusButton').addEventListener('click', async function () {
                if (!currentServiceId || !currentServiceTargetStatus) return;

                const button = this;
                button.disabled = true;

                try {
                    const response = await fetch(`/services/${currentServiceId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to update service status.');
                    }

                    const modalElement = document.getElementById('serviceStatusModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    updateServiceStatusUI(currentServiceId, data.status);
                    showToast(data.message, 'success');

                    currentServiceId = null;
                    currentServiceTargetStatus = null;

                } catch (error) {
                    showToast(error.message, 'danger');
                } finally {
                    button.disabled = false;
                }
            });

            function updateServiceStatusUI(serviceId, status) {
                const badge = document.getElementById(`status-badge-${serviceId}`);
                const toggle = document.getElementById(`status-toggle-${serviceId}`);
                const mobBtn = document.getElementById(`mob-status-btn-${serviceId}`);
                const isActive = status === 'active';

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

                const deskBtn = document.getElementById(`desk-status-btn-${serviceId}`);
                if (deskBtn) {
                    deskBtn.dataset.status = status;
                    const icon = deskBtn.querySelector('.pli-popover-icon i');
                    if (icon) {
                        icon.className = `bi ${isActive ? 'bi-toggle-on' : 'bi-toggle-off'}`;
                    }
                    const stateBadge = deskBtn.querySelector('.pli-popover-status-state');
                    if (stateBadge) {
                        stateBadge.className = 'pli-popover-status-state ' + (isActive ? 'active' : 'inactive');
                        stateBadge.textContent = isActive ? 'Active' : 'Inactive';
                    }
                }

                // Update the mobile dot indicator near 3-dots button
                const mobDot = document.getElementById(`mob-svc-status-badge-${serviceId}`);
                if (mobDot) {
                    mobDot.className = 'pli-mob-status-dot ' + (isActive ? 'pli-mob-status-dot--active' : 'pli-mob-status-dot--inactive');
                    mobDot.title = isActive ? 'Active' : 'Inactive';
                }
            }

            function openDeleteServiceModal(serviceId, serviceName) {
                deleteServiceId = serviceId;
                document.getElementById('deleteServiceMessage').textContent = `Are you sure you want to delete ${serviceName}?`;
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteServiceModal'));
                modal.show();
            }

            document.getElementById('confirmDeleteServiceButton').addEventListener('click', async function () {
                if (!deleteServiceId) return;

                const button = this;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                try {
                    const response = await fetch(`/services/${deleteServiceId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to delete service.');
                    }

                    const modalElement = document.getElementById('deleteServiceModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    const row = document.getElementById(`service-row-${deleteServiceId}`);
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
                    deleteServiceId = null;
                    button.disabled = false;
                    button.textContent = 'Delete';
                }
            });
        </script>
    @endpush

@endsection
