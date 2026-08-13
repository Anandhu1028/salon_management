<aside class="sidebar" id="sidebar" aria-label="Main navigation">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-scissors"></i>
        </div>
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">SalonPro</div>
            <div class="sidebar-brand-sub">Management System</div>
        </div>
        <button type="button" class="sidebar-pin-hint" aria-hidden="true" tabindex="-1">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="sidebar-section-label">Main</div>
        <ul class="sidebar-menu">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <span class="sidebar-nav-icon nav-icon-dashboard"><i class="bi bi-grid-1x2"></i></span>
                    <span class="sidebar-nav-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Management</div>
        <ul class="sidebar-menu sidebar-menu-management">

            <li class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
                <a href="{{ route('staff.index') }}">
                    <span class="sidebar-nav-icon nav-icon-staff"><i class="bi bi-people"></i></span>
                    <span class="sidebar-nav-text">Staff</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}">
                    <span class="sidebar-nav-icon nav-icon-customers"><i class="bi bi-person-heart"></i></span>
                    <span class="sidebar-nav-text">Customers</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('services.*') ? 'active' : '' }}">
                <a href="{{ route('services.index') }}">
                    <span class="sidebar-nav-icon nav-icon-services"><i class="bi bi-scissors"></i></span>
                    <span class="sidebar-nav-text">Services</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <a href="{{ route('products.index') }}">
                    <span class="sidebar-nav-icon nav-icon-products"><i class="bi bi-box-seam"></i></span>
                    <span class="sidebar-nav-text">Products</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('job-cards.*') ? 'active' : '' }}">
                <a href="{{ route('job-cards.index') }}">
                    <span class="sidebar-nav-icon nav-icon-jobs"><i class="bi bi-clipboard2-check"></i></span>
                    <span class="sidebar-nav-text">Job Cards</span>
                </a>
            </li>

        </ul>

    </nav>
    {{-- Profile + theme --}}
    <div class="sidebar-bottom">
        <div class="sidebar-profile-card">
            <div class="sidebar-profile">
                <div class="sidebar-profile-avatar-wrap">
                    <div class="sidebar-profile-avatar">AD</div>
                    <span class="sidebar-profile-status"></span>
                </div>
                <div class="sidebar-profile-info">
                    <div class="sidebar-profile-name">Administrator</div>
                </div>
            </div>
        </div>
    </div>

</aside>