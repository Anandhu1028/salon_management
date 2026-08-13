<header class="app-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <!-- <div class="header-title-group">
            <span class="header-greeting-sub">Welcome back,</span>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        </div> -->
    </div>

    {{-- Centered Search Bar --}}
    <div class="header-search-wrap">
        <i class="bi bi-search search-icon"></i>
        <input type="text" class="header-search-input" placeholder="Search anything...">
        <kbd class="search-kbd-hint">CTRL /</kbd>
    </div>

    <div class="header-actions">
        {{-- Theme Toggle Icon (currently showing Light Mode = Sun) --}}
        <button type="button" class="header-icon-btn" aria-label="Toggle Theme" title="Toggle Theme" id="themeToggleBtn">
            <i class="bi bi-sun" id="themeToggleIcon"></i>
        </button>

        {{-- Notification Bell --}}
        <button type="button" class="header-icon-btn position-relative" aria-label="Notifications" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="header-notif-badge">3</span>
        </button>

        {{-- Calendar Icon Button --}}
        <button type="button" class="header-icon-btn" aria-label="Calendar" title="Calendar">
            <i class="bi bi-calendar3"></i>
        </button>

        <div class="header-divider"></div>

        <div class="dropdown">
            <a href="#" class="header-profile" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="header-avatar-wrap">
                    <div class="header-avatar">
                        <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.jpg') }}" alt="{{ auth()->user()->name ?? 'Administrator' }}">
                    </div>
                </div>
                <div class="d-none d-md-block text-start">
                    <div class="header-profile-name">Administrator</div>
                    <div class="header-profile-role">Salon Manager</div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
                <li class="dropdown-header">
                    <div class="fw-bold">Administrator</div>
                    <div class="text-muted small">admin@salonpro.com</div>
                </li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> My Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</header>