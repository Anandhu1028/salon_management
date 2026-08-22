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

        <button type="button" class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn" aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>

        <button type="button" class="sidebar-pin-hint d-none d-lg-flex" aria-hidden="true" tabindex="-1">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>


    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- =====================================================
        MAIN
        ====================================================== --}}
        <div class="sidebar-section-label">
            Main
        </div>

        <ul class="sidebar-menu">

            {{-- Dashboard --}}
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <span class="sidebar-nav-icon nav-icon-dashboard">
                        <i class="bi bi-grid-1x2"></i>
                    </span>
                    <span class="sidebar-nav-text">
                        Dashboard
                    </span>
                </a>
            </li>

        </ul>


        {{-- =====================================================
        OPERATIONS
        ====================================================== --}}
        <div class="sidebar-section-label">
            Management
        </div>

        <ul class="sidebar-menu sidebar-menu-management">

            {{-- Job Cards --}}
            <li class="{{ request()->routeIs('job-cards.*') ? 'active' : '' }}">
                <a href="{{ route('job-cards.index') }}">
                    <span class="sidebar-nav-icon nav-icon-jobs">
                        <i class="bi bi-clipboard2-check"></i>
                    </span>
                    <span class="sidebar-nav-text">
                        Job Cards
                    </span>
                </a>
            </li>

            {{-- Reports --}}
            @if(auth()->user()?->hasAnyRole(['administrator', 'manager']))
            <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a href="{{ route('reports.index') }}">
                    <span class="sidebar-nav-icon nav-icon-reports">
                        <i class="bi bi-bar-chart-line"></i>
                    </span>
                    <span class="sidebar-nav-text">
                        Reports
                    </span>
                </a>
            </li>
            @endif

            {{-- Marketing --}}
            <li class="{{ request()->routeIs('marketing.*') ? 'active' : '' }}">
                <a href="{{ route('marketing.index') }}">
                    <span class="sidebar-nav-icon nav-icon-marketing">
                        <i class="bi bi-megaphone"></i>
                    </span>
                    <span class="sidebar-nav-text">
                        Marketing
                    </span>
                </a>
            </li>

            {{-- Complaints --}}
            @if(auth()->user()?->hasAnyRole(['administrator', 'manager']))
            <li class="{{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                <a href="{{ route('complaints.index') }}">
                    <span class="sidebar-nav-icon nav-icon-complaints">
                        <i class="bi bi-exclamation-circle"></i>
                    </span>
                    <span class="sidebar-nav-text">
                        Complaints
                    </span>
                </a>
            </li>
            @endif

            @if(auth()->user()?->hasAnyRole(['administrator', 'manager']))
            <li class="{{ request()->routeIs('product-purchases.*') ? 'active' : '' }}">
                <a href="{{ route('product-purchases.index') }}">
                    <span class="sidebar-nav-icon"><i class="bi bi-cart-check"></i></span>
                    <span class="sidebar-nav-text">Product Purchases</span>
                </a>
            </li>
            <!-- <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <a href="{{ route('expenses.index') }}">
                    <span class="sidebar-nav-icon"><i class="bi bi-wallet2"></i></span>
                    <span class="sidebar-nav-text">Expenses</span>
                </a>
            </li> -->
            @endif

        </ul>


        {{-- =====================================================
        MASTER
        ====================================================== --}}

        <ul class="sidebar-menu sidebar-menu-master">

            @php
                // Route-name based check
                $masterRouteNames = ['staff.*', 'attendance.*', 'customers.*', 'services.*', 'products.*'];

                // URL-path based fallback — catches cases where the route name
                // doesn't match the pattern above (e.g. attendance uses a
                // different route name than 'attendance.*').
                $masterUrlSegments = ['staff', 'attendance', 'customers', 'services', 'products'];

                $masterActive = request()->routeIs($masterRouteNames)
                    || collect($masterUrlSegments)->contains(fn($seg) => request()->is($seg) || request()->is($seg . '/*'));
            @endphp

            {{-- Master (collapsible submenu) --}}
            <li class="sidebar-has-submenu {{ $masterActive ? 'submenu-open' : '' }}">
                <button type="button" class="sidebar-submenu-toggle {{ $masterActive ? 'active' : '' }}"
                    aria-expanded="{{ $masterActive ? 'true' : 'false' }}" aria-controls="masterSubmenu"
                    data-sidebar-submenu-toggle>
                    <span class="sidebar-nav-icon nav-icon-master">
                        <i class="bi bi-boxes"></i>
                    </span>
                    <span class="sidebar-nav-text">Master</span>
                    <i class="bi bi-chevron-down sidebar-submenu-arrow"></i>
                </button>

                <ul class="sidebar-submenu {{ $masterActive ? 'show' : '' }}" id="masterSubmenu">

                    {{-- Staff --}}
                    @if(auth()->user()?->hasAnyRole(['administrator', 'manager']))
                    <li
                        class="{{ request()->routeIs('staff.*') || request()->is('staff') || request()->is('staff/*') ? 'active' : '' }}">
                        <a href="{{ route('staff.index') }}">
                            <span class="sidebar-nav-icon nav-icon-staff">
                                <i class="bi bi-people"></i>
                            </span>
                            <span class="sidebar-nav-text">
                                Staff
                            </span>
                        </a>
                    </li>

                    {{-- Attendance --}}
                    <li
                        class="{{ request()->routeIs('attendance.*') || request()->is('attendance') || request()->is('attendance/*') ? 'active' : '' }}">
                        <a href="{{ route('attendance.index') }}">
                            <span class="sidebar-nav-icon nav-icon-attendance">
                                <i class="bi bi-calendar2-check"></i>
                            </span>
                            <span class="sidebar-nav-text">
                                Attendance
                            </span>
                        </a>
                    </li>
                    @endif

                    {{-- Customers --}}
                    <li
                        class="{{ request()->routeIs('customers.*') || request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}">
                            <span class="sidebar-nav-icon nav-icon-customers">
                                <i class="bi bi-person-heart"></i>
                            </span>
                            <span class="sidebar-nav-text">
                                Customers
                            </span>
                        </a>
                    </li>

                    {{-- Services --}}
                    <li
                        class="{{ request()->routeIs('services.*') || request()->is('services') || request()->is('services/*') ? 'active' : '' }}">
                        <a href="{{ route('services.index') }}">
                            <span class="sidebar-nav-icon nav-icon-services">
                                <i class="bi bi-scissors"></i>
                            </span>
                            <span class="sidebar-nav-text">
                                Services
                            </span>
                        </a>
                    </li>

                    {{-- Products --}}
                    @if(auth()->user()?->hasAnyRole(['administrator', 'manager']))
                    <li
                        class="{{ request()->routeIs('products.*') || request()->is('products') || request()->is('products/*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}">
                            <span class="sidebar-nav-icon nav-icon-products">
                                <i class="bi bi-box-seam"></i>
                            </span>
                            <span class="sidebar-nav-text">
                                Products
                            </span>
                        </a>
                    </li>

                      <!-- Expense -->
                     
                    <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <a href="{{ route('expenses.index') }}">
                            <span class="sidebar-nav-icon"><i class="bi bi-wallet2"></i></span>
                            <span class="sidebar-nav-text">Expenses</span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>

        </ul>
    </nav>


    {{-- =====================================================
    PROFILE
    ====================================================== --}}
    <div class="sidebar-bottom">

        <div class="sidebar-profile-card">

            <div class="sidebar-profile">

                <div class="sidebar-profile-avatar-wrap">
                    <div class="sidebar-profile-avatar">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                    </div>
                    <span class="sidebar-profile-status"></span>
                </div>

                <div class="sidebar-profile-info">
                    <div class="sidebar-profile-name">
                        {{ auth()->user()?->name ?? 'User' }}
                    </div>
                    <div class="sidebar-profile-role">
                        {{ ucfirst(auth()->user()?->role?->name ?? 'Staff') }}
                    </div>
                </div>

            </div>

        </div>

    </div>

</aside>
