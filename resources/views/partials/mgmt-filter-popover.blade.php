@php
    $module = $filterModule ?? 'default';
    $route = $filterRoute ?? url()->current();
    $search = request('search', '');
    $filterData = $filterData ?? [];

    $moduleTitle = match($module) {
        'job-cards' => 'Job Cards',
        'staff' => 'Staff',
        'customers' => 'Customers',
        'services' => 'Services',
        'products' => 'Products',
        default => 'Items',
    };

    // Calculate active filters count
    $activeCount = 0;
    $filteredParams = [];

    if ($module === 'job-cards') {
        $filteredParams = [
            'job_card' => request('job_card', ''),
            'customer_id' => request('customer_id', ''),
            'service_id' => request('service_id', ''),
            'subcategory' => request('subcategory', ''),
            'amount_range' => request('amount_range', ''),
        ];
    } elseif ($module === 'staff' || $module === 'customers') {
        $filteredParams = [
            'name' => request('name', ''),
            'email' => request('email', ''),
            'contact' => request('contact', ''),
            'status' => request('status', request('filter', '')),
        ];
    } elseif ($module === 'services' || $module === 'products') {
        $filteredParams = [
            'name' => request('name', ''),
            'category' => request('category', ''),
            'subcategory' => request('subcategory', ''),
            'price_range' => request('price_range', ''),
            'status' => request('status', request('filter', '')),
        ];
    }

    foreach ($filteredParams as $k => $v) {
        if ($v !== '' && $v !== null && $v !== 'all') {
            $activeCount++;
        }
    }

    $isActive = $activeCount > 0;
    $priceRanges = [
        '' => 'All ' . ($module === 'job-cards' ? 'Amounts' : 'Prices'),
        'under_500' => 'Under ₹500',
        '500_1000' => '₹500 – ₹1,000',
        '1001_2500' => '₹1,001 – ₹2,500',
        '2501_5000' => '₹2,501 – ₹5,000',
        'above_5000' => 'Above ₹5,000',
    ];
@endphp

<div class="mgmt-filter-wrapper" id="mgmtFilterWrapper">
    {{-- Trigger Button --}}
    <button
        type="button"
        class="mgmt-action-btn mgmt-action-btn--filter {{ $isActive ? 'is-active' : '' }}"
        id="mgmtFilterTrigger"
        aria-expanded="false"
        aria-controls="mgmtFilterPopover"
        title="Filter list"
    >
        <span class="mgmt-action-btn__icon" aria-hidden="true">
            <i class="bi bi-funnel-fill"></i>
        </span>
        <span class="mgmt-action-btn__label">Filter</span>
        @if($isActive)
            <span class="mgmt-filter-badge">{{ $activeCount }}</span>
        @endif
    </button>

    {{-- Popover Overlay (Mobile Bottom Sheet Dim) --}}
    <div class="mgmt-filter-overlay" id="mgmtFilterOverlay"></div>

    {{-- Floating Popover / Mobile Bottom Sheet Panel --}}
    <div class="mgmt-filter-popover" id="mgmtFilterPopover" role="dialog" aria-label="Filter options">
        {{-- Mobile Pull / Drag Indicator Handle --}}
        <div class="mgmt-filter-drag-handle"></div>

        <form method="GET" action="{{ $route }}" id="mgmtFilterForm">
            {{-- Keep search parameter if present --}}
            @if(!empty($search))
                <input type="hidden" name="search" value="{{ $search }}">
            @endif

            {{-- Popover Header --}}
            <div class="mgmt-filter-popover__header">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mgmt-filter-title">Filter {{ $moduleTitle }}</h4>
                    @if($isActive)
                        <span class="mgmt-filter-active-chip">{{ $activeCount }} active</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ $route . ($search ? '?search=' . urlencode($search) : '') }}" class="mgmt-filter-clear-link" title="Clear all filters">
                        Clear All
                    </a>
                    <button type="button" class="mgmt-filter-close-btn" id="mgmtFilterCloseBtn" aria-label="Close filters">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Popover Info Banner --}}
            <div class="mgmt-filter-info-banner">
                <div class="mgmt-filter-info-icon">
                    <i class="bi bi-funnel"></i>
                </div>
                <div class="mgmt-filter-info-text">
                    Refine your search to find the exact {{ strtolower($moduleTitle) }} you need.
                </div>
            </div>

            {{-- Popover Body --}}
            <div class="mgmt-filter-popover__body">

                {{-- ======================================================== --}}
                {{-- 1. JOB CARDS FILTERS --}}
                {{-- ======================================================== --}}
                @if($module === 'job-cards')
                    {{-- Job Card (Name or ID) --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_job_card">
                            <i class="bi bi-fonts"></i> Job Card
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="job_card"
                                id="filter_job_card"
                                class="filter-input"
                                placeholder="Search by name or #JC ID..."
                                value="{{ request('job_card', '') }}"
                            >
                            @if(request('job_card'))
                                <button type="button" class="filter-clear-input" onclick="document.getElementById('filter_job_card').value='';"><i class="bi bi-x"></i></button>
                            @endif
                        </div>
                    </div>

                    {{-- Customer --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_customer_id">
                            <i class="bi bi-person"></i> Customer
                        </label>
                        <div class="filter-input-wrap">
                            <select name="customer_id" id="filter_customer_id" class="filter-select">
                                <option value="">All Customers</option>
                                @foreach($filterData['customers'] ?? [] as $cust)
                                    <option value="{{ $cust->id }}" {{ (string) request('customer_id') === (string) $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }}
                                        @if(!empty($cust->mobile_number)) ({{ $cust->mobile_number }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Service --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_service_id">
                            <i class="bi bi-scissors"></i> Service
                        </label>
                        <div class="filter-input-wrap">
                            <select name="service_id" id="filter_service_id" class="filter-select">
                                <option value="">All Services</option>
                                @foreach($filterData['services'] ?? [] as $svc)
                                    <option value="{{ $svc->id }}" {{ (string) request('service_id') === (string) $svc->id ? 'selected' : '' }}>
                                        {{ $svc->service_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Sub Category --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_subcategory">
                            <i class="bi bi-grid"></i> Sub Category
                        </label>
                        <div class="filter-input-wrap">
                            <select name="subcategory" id="filter_subcategory" class="filter-select">
                                <option value="">All Sub Categories</option>
                                @foreach($filterData['subcategories'] ?? [] as $subcat)
                                    <option value="{{ $subcat }}" {{ (string) request('subcategory') === (string) $subcat ? 'selected' : '' }}>
                                        {{ $subcat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Amount Range --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_amount_range">
                            <i class="bi bi-currency-rupee"></i> Amount
                        </label>
                        <div class="filter-input-wrap">
                            <select name="amount_range" id="filter_amount_range" class="filter-select">
                                @foreach($priceRanges as $val => $label)
                                    <option value="{{ $val }}" {{ request('amount_range') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                {{-- ======================================================== --}}
                {{-- 2. STAFF FILTERS --}}
                {{-- ======================================================== --}}
                @if($module === 'staff')
                    {{-- Name --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_staff_name">
                            <i class="bi bi-person"></i> Name
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="name"
                                id="filter_staff_name"
                                class="filter-input"
                                placeholder="Search staff name..."
                                value="{{ request('name', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_staff_email">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="email"
                                id="filter_staff_email"
                                class="filter-input"
                                placeholder="Search email address..."
                                value="{{ request('email', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_staff_contact">
                            <i class="bi bi-telephone"></i> Contact
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="contact"
                                id="filter_staff_contact"
                                class="filter-input"
                                placeholder="Search mobile number..."
                                value="{{ request('contact', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="bi bi-toggle-on"></i> Status
                        </label>
                        <div class="filter-segmented-group">
                            @php $currStatus = request('status', request('filter', '')); @endphp
                            <label class="filter-segmented-btn {{ $currStatus === '' || $currStatus === 'all' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="" {{ $currStatus === '' || $currStatus === 'all' ? 'checked' : '' }}>
                                <span>All Staff</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'active' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="active" {{ $currStatus === 'active' ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'inactive' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="inactive" {{ $currStatus === 'inactive' ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- ======================================================== --}}
                {{-- 3. CUSTOMERS FILTERS --}}
                {{-- ======================================================== --}}
                @if($module === 'customers')
                    {{-- Name --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_cust_name">
                            <i class="bi bi-person"></i> Name
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="name"
                                id="filter_cust_name"
                                class="filter-input"
                                placeholder="Search customer name..."
                                value="{{ request('name', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_cust_email">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="email"
                                id="filter_cust_email"
                                class="filter-input"
                                placeholder="Search email address..."
                                value="{{ request('email', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_cust_contact">
                            <i class="bi bi-telephone"></i> Contact
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="contact"
                                id="filter_cust_contact"
                                class="filter-input"
                                placeholder="Search mobile number..."
                                value="{{ request('contact', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="bi bi-toggle-on"></i> Status
                        </label>
                        <div class="filter-segmented-group">
                            @php $currStatus = request('status', request('filter', '')); @endphp
                            <label class="filter-segmented-btn {{ $currStatus === '' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="" {{ $currStatus === '' ? 'checked' : '' }}>
                                <span>All</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'active' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="active" {{ $currStatus === 'active' ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'inactive' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="inactive" {{ $currStatus === 'inactive' ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- ======================================================== --}}
                {{-- 4. SERVICES FILTERS --}}
                {{-- ======================================================== --}}
                @if($module === 'services')
                    {{-- Name --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_svc_name">
                            <i class="bi bi-scissors"></i> Name
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="name"
                                id="filter_svc_name"
                                class="filter-input"
                                placeholder="Search service name..."
                                value="{{ request('name', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_svc_category">
                            <i class="bi bi-folder2"></i> Category
                        </label>
                        <div class="filter-input-wrap">
                            <select name="category" id="filter_svc_category" class="filter-select">
                                <option value="">All Categories</option>
                                @foreach($filterData['categories'] ?? [] as $cat)
                                    <option value="{{ $cat }}" {{ (string) request('category') === (string) $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Sub Category --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_svc_subcategory">
                            <i class="bi bi-grid"></i> Sub Category
                        </label>
                        <div class="filter-input-wrap">
                            <select name="subcategory" id="filter_svc_subcategory" class="filter-select">
                                <option value="">All Sub Categories</option>
                                @foreach($filterData['subcategories'] ?? [] as $subcat)
                                    <option value="{{ $subcat }}" {{ (string) request('subcategory') === (string) $subcat ? 'selected' : '' }}>
                                        {{ $subcat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_svc_price">
                            <i class="bi bi-currency-rupee"></i> Price
                        </label>
                        <div class="filter-input-wrap">
                            <select name="price_range" id="filter_svc_price" class="filter-select">
                                @foreach($priceRanges as $val => $label)
                                    <option value="{{ $val }}" {{ request('price_range') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="bi bi-toggle-on"></i> Status
                        </label>
                        <div class="filter-segmented-group">
                            @php $currStatus = request('status', request('filter', '')); @endphp
                            <label class="filter-segmented-btn {{ $currStatus === '' || $currStatus === 'all' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="" {{ $currStatus === '' || $currStatus === 'all' ? 'checked' : '' }}>
                                <span>All Services</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'active' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="active" {{ $currStatus === 'active' ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'inactive' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="inactive" {{ $currStatus === 'inactive' ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- ======================================================== --}}
                {{-- 5. PRODUCTS FILTERS --}}
                {{-- ======================================================== --}}
                @if($module === 'products')
                    {{-- Name --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_prod_name">
                            <i class="bi bi-box-seam"></i> Name
                        </label>
                        <div class="filter-input-wrap">
                            <input
                                type="text"
                                name="name"
                                id="filter_prod_name"
                                class="filter-input"
                                placeholder="Search product name..."
                                value="{{ request('name', '') }}"
                            >
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_prod_category">
                            <i class="bi bi-folder2"></i> Category
                        </label>
                        <div class="filter-input-wrap">
                            <select name="category" id="filter_prod_category" class="filter-select">
                                <option value="">All Categories</option>
                                @foreach($filterData['categories'] ?? [] as $cat)
                                    <option value="{{ $cat }}" {{ (string) request('category') === (string) $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Sub Category --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_prod_subcategory">
                            <i class="bi bi-grid"></i> Sub Category
                        </label>
                        <div class="filter-input-wrap">
                            <select name="subcategory" id="filter_prod_subcategory" class="filter-select">
                                <option value="">All Sub Categories</option>
                                @foreach($filterData['subcategories'] ?? [] as $subcat)
                                    <option value="{{ $subcat }}" {{ (string) request('subcategory') === (string) $subcat ? 'selected' : '' }}>
                                        {{ $subcat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_prod_price">
                            <i class="bi bi-currency-rupee"></i> Price
                        </label>
                        <div class="filter-input-wrap">
                            <select name="price_range" id="filter_prod_price" class="filter-select">
                                @foreach($priceRanges as $val => $label)
                                    <option value="{{ $val }}" {{ request('price_range') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="bi bi-toggle-on"></i> Status
                        </label>
                        <div class="filter-segmented-group">
                            @php $currStatus = request('status', request('filter', '')); @endphp
                            <label class="filter-segmented-btn {{ $currStatus === '' || $currStatus === 'all' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="" {{ $currStatus === '' || $currStatus === 'all' ? 'checked' : '' }}>
                                <span>All Products</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'active' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="active" {{ $currStatus === 'active' ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <label class="filter-segmented-btn {{ $currStatus === 'inactive' ? 'is-active' : '' }}">
                                <input type="radio" name="status" value="inactive" {{ $currStatus === 'inactive' ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Popover Footer --}}
            <div class="mgmt-filter-popover__footer">
                <a href="{{ $route . ($search ? '?search=' . urlencode($search) : '') }}" class="btn-filter-secondary">
                    Reset
                </a>
                <button type="submit" class="btn-filter-primary">
                    <i class="bi bi-funnel-fill"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>
