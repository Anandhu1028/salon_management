@extends('layouts.app')

@section('title', 'Job Cards')
@section('page-title', 'Job Cards')

@section('content')

    <div class="job-card-page management-page">

        @include('partials.mgmt-top-actions', [
            'addLabel' => 'Create Job Card',
            'addModal' => '#jobCardModal',
            'addOnclick' => 'openAddJobCardModal()',
            'filterRoute' => route('job-cards.index'),
            'filter' => $filter ?? '',
            'search' => $search ?? '',
            'filterOptions' => [
                '' => 'All Job Cards',
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
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
                        <span class="pli-head-cell col-center pli-head-icon"></span>
                        <span class="pli-head-cell col-left">Job Card</span>
                        <span class="pli-head-cell col-center">Customer</span>
                        <span class="pli-head-cell col-center">Service</span>
                        <span class="pli-head-cell col-center">Sub Category</span>
                        <span class="pli-head-cell col-center">Amount</span>
                        <span class="pli-head-cell col-center">Created</span>
                        <span class="pli-head-cell col-center">Actions</span>
                    </div>

                    @foreach($jobCards as $jobCard)
                        <article class="premium-list-item" id="job-card-row-{{ $jobCard->id }}">
                            <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                            <div class="pli-col pli-col-icon col-center">
                                <div class="pli-icon pli-icon--cyan">
                                    <i class="bi bi-clipboard2-check-fill"></i>
                                </div>
                            </div>

                            <div class="pli-col pli-col-name col-left">
                                <div class="pli-name-stack">
                                    <span class="pli-title job-card-name">{{ $jobCard->job_card_name }}</span>
                                    <span
                                        class="pli-subtext job-card-number">#JC-{{ str_pad($jobCard->id, 5, '0', STR_PAD_LEFT) }}</span>
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
                                <span class="pli-col-text">{{ $jobCard->service->service_name ?? '—' }}</span>
                            </div>

                            <div class="pli-col pli-col-subcategory col-center">
                                <span class="pli-col-text">{{ $jobCard->subcategory ?: '—' }}</span>
                            </div>

                            <div class="pli-col pli-col-amount col-center">
                                <span class="pli-col-text">₹{{ number_format($jobCard->service?->price ?? 0, 0) }}</span>
                            </div>

                            <div class="pli-col pli-col-joined col-center">
                                <span
                                    class="pli-col-text">{{ $jobCard->created_at ? $jobCard->created_at->format('d M Y') : '—' }}</span>
                            </div>

                            <div class="pli-col pli-col-actions col-actions actions-cell col-center">
                                <button type="button" class="pli-btn-icon pli-btn-icon--view" title="View Job Card"
                                    onclick='openJobCardDetailsModal(@json($jobCard))'>
                                    @include('partials.action-icons', ['type' => 'view', 'size' => 16])
                                </button>
                                <button type="button" class="pli-btn-icon pli-btn-icon--edit" title="Edit Job Card"
                                    data-bs-toggle="modal" data-bs-target="#jobCardModal"
                                    onclick='openEditJobCardModal(@json($jobCard))'>
                                    @include('partials.action-icons', ['type' => 'edit', 'size' => 16])
                                </button>
                                <button type="button" class="pli-btn-icon pli-btn-icon--danger" title="Delete Job Card"
                                    onclick="openDeleteJobCardModal({{ $jobCard->id }}, @js($jobCard->job_card_name))">
                                    @include('partials.action-icons', ['type' => 'delete', 'size' => 16])
                                </button>
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

    <div class="modal fade premium-modal premium-modal--lg" id="jobCardModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="jobCardForm" method="POST" action="{{ route('job-cards.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="jobCardFormMethod" value="POST">

                    {{-- Header --}}
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-icon-box warning">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                            <div class="modal-header-content">
                                <h5 class="modal-title" id="jobCardModalTitle">Create Job Card</h5>
                                <p class="modal-subtitle" id="jobCardModalSubtitle">Create a new customer service job card.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body job-card-form-grid">

                        {{-- Job Card Name --}}
                        <div class="form-field job-card-form-grid__full">
                            <label for="job_card_name" class="form-label">
                                Job Card Name <span>*</span>
                            </label>
                            <div class="field-control-wrap">
                                <span class="form-field-icon"><i class="bi bi-fonts"></i></span>
                                <input type="text" name="job_card_name" id="job_card_name" class="form-control"
                                    placeholder="e.g. Bridal Hair Styling" required>
                            </div>
                        </div>

                        {{-- Customers (Multi-select) --}}
                        <div class="form-field">
                            <label for="customer_ids" class="form-label">
                                Customer(s) <span>*</span>
                            </label>
                            <select name="customer_ids[]" id="customer_ids" class="form-select" multiple data-placeholder="Select customer(s)" data-icon="bi-people" required>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}
                                        @if($customer->mobile_number)
                                            — {{ $customer->mobile_number }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Staff Member(s) (Multi-select) --}}
                        <div class="form-field">
                            <label for="staff_ids" class="form-label">Staff Member(s)</label>
                            <select name="staff_ids[]" id="staff_ids" class="form-select" multiple data-placeholder="Select staff member(s)" data-icon="bi-person-badge">
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Service --}}
                        <div class="form-field">
                            <label for="service_id" class="form-label">
                                Service <span>*</span>
                            </label>
                            <select name="service_id" id="service_id" class="form-select" data-icon="bi-scissors" required>
                                <option value="">Select service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-subcategory="{{ $service->subcategory }}"
                                        data-category="{{ $service->category }}" data-icon="{{ $service->icon }}">
                                        {{ $service->service_name }}
                                        @if($service->category)
                                            — {{ $service->category }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subcategory / Service Category --}}
                        <div class="form-field">
                            <label for="subcategory" class="form-label">
                                Service Category
                            </label>
                            <select name="subcategory" id="subcategory" class="form-select" data-icon="bi-grid" required
                                disabled>
                                <option value="">Select service category (optional)</option>
                            </select>
                            <div class="field-help">Subcategory is automatically loaded from selected service.</div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancel
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
                            <h5 class="modal-title">Job Card Details</h5>
                            <p class="modal-subtitle" id="jobCardDetailsNumber">—</p>
                        </div>
                    </div>
                    <div class="job-card-details-header-actions">
                        <button type="button" class="job-card-detail-tool" title="Export PDF" aria-label="Export PDF"><i
                                class="bi bi-file-earmark-pdf"></i></button>
                        <button type="button" class="job-card-detail-tool" title="Export Excel" aria-label="Export Excel"><i
                                class="bi bi-file-earmark-spreadsheet"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="job-card-details-list">
                        <div class="job-card-details-row"><span class="job-card-details-row-icon"><i
                                    class="bi bi-person"></i></span><span
                                class="job-card-details-row-label">Name</span><strong id="jobCardDetailsName">—</strong>
                        </div>
                        <div class="job-card-details-row"><span class="job-card-details-row-icon"><i
                                    class="bi bi-people"></i></span><span
                                class="job-card-details-row-label">Customer(s)</span><strong id="jobCardDetailsCustomer">—</strong>
                        </div>
                        <div class="job-card-details-row"><span class="job-card-details-row-icon"><i
                                    class="bi bi-person-badge"></i></span><span
                                class="job-card-details-row-label">Staff Assigned</span><strong id="jobCardDetailsStaff">—</strong>
                        </div>
                        <div class="job-card-details-row"><span class="job-card-details-row-icon"><i
                                    class="bi bi-scissors"></i></span><span
                                class="job-card-details-row-label">Service</span><strong
                                id="jobCardDetailsService">—</strong></div>
                        <div class="job-card-details-row"><span class="job-card-details-row-icon"><i
                                    class="bi bi-layers"></i></span><span class="job-card-details-row-label">Sub
                                Category</span><strong id="jobCardDetailsSubcategory">—</strong></div>
                        <div class="job-card-details-row"><span class="job-card-details-row-icon"><i
                                    class="bi bi-calendar3"></i></span><span class="job-card-details-row-label">Created
                                On</span><strong id="jobCardDetailsCreated">—</strong></div>
                    </div>
                    <div class="job-card-details-invoice">
                        <div class="job-card-details-invoice-head">
                            <span>#</span><span>Description</span><span>Qty</span><span>Rate (₹)</span><span>Amount
                                (₹)</span></div>
                        <div class="job-card-details-invoice-line"><span>1</span><span><strong
                                    id="jobCardDetailsInvoiceService">—</strong><small
                                    id="jobCardDetailsCategory"></small></span><span>1</span><span
                                    id="jobCardDetailsRate">₹0</span><span id="jobCardDetailsAmount">₹0</span></div>
                        <div class="job-card-details-totals"><span>Subtotal</span><strong
                                id="jobCardDetailsSubtotal">₹0</strong><span>Discount</span><strong>₹0.00</strong><span>Tax
                                (0%)</span><strong>₹0.00</strong><span
                                class="job-card-details-total-label">Total</span><strong class="job-card-details-total"
                                id="jobCardDetailsTotal">₹0</strong></div>
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

            const serviceSelect = document.getElementById('service_id');
            const subcategorySelect = document.getElementById('subcategory');

            function loadSubcategory(serviceId, selectedSubcategory = null) {
                subcategorySelect.innerHTML = '';

                if (!serviceId) {
                    subcategorySelect.disabled = true;
                    subcategorySelect.innerHTML = '<option value="">Select a service first</option>';
                    return;
                }

                const selectedOption = serviceSelect.querySelector(`option[value="${serviceId}"]`);
                if (!selectedOption) {
                    subcategorySelect.disabled = true;
                    return;
                }

                const subcategory = selectedOption.dataset.subcategory;
                if (!subcategory) {
                    subcategorySelect.disabled = true;
                    subcategorySelect.innerHTML = '<option value="">No subcategory available</option>';
                    return;
                }

                subcategorySelect.disabled = false;
                const subcategoryOption = new Option(subcategory, subcategory, true, true);
                subcategorySelect.replaceChildren(subcategoryOption);

                if (selectedSubcategory) {
                    subcategorySelect.value = selectedSubcategory;
                }

                window.refreshNiceSelect?.(subcategorySelect);
            }

            serviceSelect.addEventListener('change', function () {
                loadSubcategory(this.value);
            });

            function initialiseServicePicker(select) {
                const iconMap = {
                    haircut: 'bi-scissors', scissors: 'bi-scissors', 'hair-color': 'bi-palette-fill', keratin: 'bi-stars',
                    spa: 'bi-flower1', facial: 'bi-stars', sparkle: 'bi-stars', sparkles: 'bi-stars', makeup: 'bi-brush',
                    brush: 'bi-brush', nails: 'bi-hand-index-thumb', nail: 'bi-hand-index-thumb', beard: 'bi-person',
                    user: 'bi-person', massage: 'bi-flower1', waxing: 'bi-droplet', droplet: 'bi-droplet', threading: 'bi-bezier2',
                    default: 'bi-scissors',
                };
                const services = [...select.options].filter(option => option.value).map(option => ({
                    id: option.value,
                    name: option.textContent.trim().split(' — ')[0],
                    category: option.dataset.category || '',
                    subcategory: option.dataset.subcategory || '',
                    icon: option.dataset.icon || 'default',
                }));
                const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
                const icon = (key) => `<i class="bi ${iconMap[key] || iconMap.default}" aria-hidden="true"></i>`;
                const details = (service) => [service.category, service.subcategory].filter(Boolean).map(escapeHtml).join(' <span>·</span> ');

                const picker = document.createElement('div');
                picker.className = 'job-service-picker';
                picker.innerHTML = `
                <button type="button" class="job-service-picker__trigger" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="jobServiceOptions">
                    <span class="job-service-picker__trigger-content"></span><i class="bi bi-chevron-down" aria-hidden="true"></i>
                </button>
                <div class="job-service-picker__panel" id="jobServicePanel">
                    <div class="job-service-picker__search"><i class="bi bi-search" aria-hidden="true"></i><input type="search" placeholder="Search service..." aria-label="Search service"></div>
                    <div class="job-service-picker__filter-wrap"><div class="job-service-picker__filters" aria-label="Filter services by category"></div><button type="button" class="job-service-picker__filter-next" aria-label="Show more categories"><i class="bi bi-chevron-right" aria-hidden="true"></i></button></div>
                    <div class="job-service-picker__options" id="jobServiceOptions" role="listbox"></div>
                </div>`;
                select.classList.add('job-service-picker__native');
                select.after(picker);

                const trigger = picker.querySelector('.job-service-picker__trigger');
                const triggerContent = picker.querySelector('.job-service-picker__trigger-content');
                const panel = picker.querySelector('.job-service-picker__panel');
                const search = picker.querySelector('input');
                const filters = picker.querySelector('.job-service-picker__filters');
                const filterNext = picker.querySelector('.job-service-picker__filter-next');
                const list = picker.querySelector('.job-service-picker__options');
                let activeCategory = '';
                let activeIndex = -1;

                const updateFilterArrow = () => {
                    filterNext.hidden = filters.scrollWidth <= filters.clientWidth + 2;
                };
                filterNext.addEventListener('click', () => filters.scrollBy({ left: 140, behavior: 'smooth' }));
                filters.addEventListener('scroll', updateFilterArrow);

                const close = () => { picker.classList.remove('is-open', 'opens-up'); trigger.setAttribute('aria-expanded', 'false'); activeIndex = -1; };
                const visibleServices = () => {
                    const query = search.value.toLowerCase().trim();
                    return services.filter(service => !activeCategory || service.category === activeCategory).filter(service =>
                        [service.name, service.category, service.subcategory].join(' ').toLowerCase().includes(query)
                    );
                };
                const render = () => {
                    const selected = services.find(service => service.id === select.value);
                    const iconHtml = `<span class="form-field-icon"><i class="bi bi-scissors" aria-hidden="true"></i></span>`;
                    triggerContent.innerHTML = selected
                        ? `${iconHtml}<span><strong>${escapeHtml(selected.name)}</strong><small>${details(selected)}</small></span>`
                        : `${iconHtml}<span><strong>Select service</strong></span>`;

                    const categories = [...new Set(services.map(service => service.category).filter(Boolean))];
                    filters.innerHTML = `<button type="button" class="${!activeCategory ? 'is-active' : ''}" data-category="">All</button>` + categories.map(category =>
                        `<button type="button" class="${activeCategory === category ? 'is-active' : ''}" data-category="${escapeHtml(category)}">${escapeHtml(category)}</button>`
                    ).join('');
                    filters.querySelectorAll('button').forEach(button => button.addEventListener('click', () => { activeCategory = button.dataset.category; render(); }));
                    requestAnimationFrame(updateFilterArrow);

                    const matches = visibleServices();
                    list.innerHTML = matches.length ? matches.map((service, index) => {
                        const selectedState = service.id === select.value;
                        const palette = escapeHtml(service.category.toLowerCase().replace(/\s+/g, '-'));
                        return `<button type="button" class="job-service-picker__option ${selectedState ? 'is-selected' : ''}" data-value="${escapeHtml(service.id)}" role="option" aria-selected="${selectedState}" tabindex="-1">
                        <span class="job-service-picker__icon category-${palette}">${icon(service.icon)}</span>
                        <span class="job-service-picker__copy"><strong>${escapeHtml(service.name)}</strong><small>${details(service)}</small></span>
                        ${selectedState ? '<i class="bi bi-check-circle-fill job-service-picker__check" aria-hidden="true"></i>' : ''}
                    </button>`;
                    }).join('') : '<div class="job-service-picker__empty"><i class="bi bi-scissors" aria-hidden="true"></i><strong>No services found</strong><span>Try another search term</span></div>';
                    list.querySelectorAll('.job-service-picker__option').forEach(option => option.addEventListener('click', () => choose(option.dataset.value)));
                };
                const choose = (id) => {
                    select.value = id;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    render();
                    close();
                    trigger.focus();
                };
                const open = () => {
                    picker.classList.add('is-open');
                    const rect = trigger.getBoundingClientRect();
                    picker.classList.toggle('opens-up', window.innerHeight - rect.bottom < 375 && rect.top > 375);
                    trigger.setAttribute('aria-expanded', 'true');
                    render();
                    search.focus();
                };
                trigger.addEventListener('click', () => picker.classList.contains('is-open') ? close() : open());
                search.addEventListener('input', render);
                picker.addEventListener('keydown', event => {
                    const options = [...list.querySelectorAll('.job-service-picker__option')];
                    if (event.key === 'Escape') { event.preventDefault(); close(); trigger.focus(); }
                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault(); activeIndex = Math.max(0, Math.min(options.length - 1, activeIndex + (event.key === 'ArrowDown' ? 1 : -1))); options[activeIndex]?.focus();
                    }
                    if (event.key === 'Enter' && document.activeElement.classList.contains('job-service-picker__option')) { event.preventDefault(); choose(document.activeElement.dataset.value); }
                });
                document.addEventListener('click', event => { if (!picker.contains(event.target)) close(); });
                select.addEventListener('change', render);
                window.refreshJobServicePicker = render;
                render();
            }

            initialiseServicePicker(serviceSelect);

            function openAddJobCardModal() {
                const form = document.getElementById('jobCardForm');
                form.reset();
                form.action = "{{ route('job-cards.store') }}";
                document.getElementById('jobCardFormMethod').value = 'POST';
                document.getElementById('jobCardModalTitle').textContent = 'Create Job Card';
                document.getElementById('jobCardModalSubtitle').textContent = 'Create a new customer service job card.';
                document.getElementById('jobCardSubmitButton').innerHTML = '<i class="bi bi-clipboard2-plus"></i> Create Job Card';
                
                const statusSelect = document.getElementById('job_card_status');
                if (statusSelect) {
                    statusSelect.value = 'pending';
                    window.refreshNiceSelect?.(statusSelect);
                }

                loadSubcategory(null);
                window.setMultiSelectValues?.(document.getElementById('customer_ids'), []);
                window.setMultiSelectValues?.(document.getElementById('staff_ids'), []);
                window.refreshNiceSelect?.(document.getElementById('customer_ids'));
                window.refreshNiceSelect?.(document.getElementById('staff_ids'));
                window.refreshJobServicePicker?.();
            }

            function openEditJobCardModal(jobCard) {
                const form = document.getElementById('jobCardForm');
                form.action = `/job-cards/${jobCard.id}`;
                document.getElementById('jobCardFormMethod').value = 'PUT';
                document.getElementById('jobCardModalTitle').textContent = 'Edit Job Card';
                document.getElementById('jobCardModalSubtitle').textContent = 'Update job card information.';
                document.getElementById('jobCardSubmitButton').innerHTML = '<i class="bi bi-check2-circle"></i> Update Job Card';

                document.getElementById('job_card_name').value = jobCard.job_card_name ?? '';
                document.getElementById('service_id').value = jobCard.service_id ?? '';
                
                const statusSelect = document.getElementById('job_card_status');
                if (statusSelect) {
                    statusSelect.value = jobCard.status ?? 'pending';
                    window.refreshNiceSelect?.(statusSelect);
                }

                const customerIds = (jobCard.customers && jobCard.customers.length)
                    ? jobCard.customers.map(c => c.id)
                    : (jobCard.customer_id ? [jobCard.customer_id] : []);

                const staffIds = (jobCard.staff && jobCard.staff.length)
                    ? jobCard.staff.map(s => s.id)
                    : (jobCard.staff_id ? [jobCard.staff_id] : []);

                window.setMultiSelectValues?.(document.getElementById('customer_ids'), customerIds);
                window.setMultiSelectValues?.(document.getElementById('staff_ids'), staffIds);
                window.refreshNiceSelect?.(document.getElementById('customer_ids'));
                window.refreshNiceSelect?.(document.getElementById('staff_ids'));

                loadSubcategory(jobCard.service_id, jobCard.subcategory);
                window.refreshJobServicePicker?.();
            }

            function openJobCardDetailsModal(jobCard) {
                const service = jobCard.service || {};
                const customers = (jobCard.customers && jobCard.customers.length)
                    ? jobCard.customers
                    : (jobCard.customer ? [jobCard.customer] : []);
                const staffList = (jobCard.staff && jobCard.staff.length)
                    ? jobCard.staff
                    : (jobCard.primary_staff ? [jobCard.primary_staff] : []);

                const customerText = customers.map(c => {
                    return c.mobile_number ? `${c.name} (${c.mobile_number})` : c.name;
                }).join(', ') || '—';

                const staffText = staffList.map(s => s.name).join(', ') || '—';

                const amount = Number(service.price || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
                const created = jobCard.created_at ? new Date(jobCard.created_at).toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';

                document.getElementById('jobCardDetailsNumber').textContent = `#JC-${String(jobCard.id).padStart(5, '0')}`;
                document.getElementById('jobCardDetailsAmount').textContent = `₹${amount}.00`;
                document.getElementById('jobCardDetailsName').textContent = jobCard.job_card_name || '—';
                document.getElementById('jobCardDetailsCreated').textContent = created;
                document.getElementById('jobCardDetailsCustomer').textContent = customerText;
                document.getElementById('jobCardDetailsStaff').textContent = staffText;
                document.getElementById('jobCardDetailsService').textContent = service.service_name || '—';
                document.getElementById('jobCardDetailsInvoiceService').textContent = service.service_name || '—';
                document.getElementById('jobCardDetailsCategory').textContent = [service.category, service.subcategory].filter(Boolean).join(' · ');
                document.getElementById('jobCardDetailsSubcategory').textContent = jobCard.subcategory || '—';
                document.getElementById('jobCardDetailsRate').textContent = `₹${amount}.00`;
                document.getElementById('jobCardDetailsSubtotal').textContent = `₹${amount}.00`;
                document.getElementById('jobCardDetailsTotal').textContent = `₹${amount}.00`;

                bootstrap.Modal.getOrCreateInstance(document.getElementById('jobCardDetailsModal')).show();
            }

            function openDeleteJobCardModal(jobCardId, jobCardName) {
                deleteJobCardId = jobCardId;
                document.getElementById('deleteJobCardMessage').textContent = `Are you sure you want to delete ${jobCardName}?`;
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteJobCardModal'));
                modal.show();
            }

            document.getElementById('confirmDeleteJobCardButton').addEventListener('click', async function () {
                if (!deleteJobCardId) return;

                const button = this;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                try {
                    const response = await fetch(`/job-cards/${deleteJobCardId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to delete job card.');
                    }

                    const modalElement = document.getElementById('deleteJobCardModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    const row = document.getElementById(`job-card-row-${deleteJobCardId}`);
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
                    deleteJobCardId = null;
                    button.disabled = false;
                    button.textContent = 'Delete';
                }
            });
        </script>
    @endpush

@endsection