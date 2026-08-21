<header class="app-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="header-title-group">
            <span class="header-greeting-sub">SalonPro · Admin</span>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        </div>
    </div>

    <div class="header-search-wrap">
        <i class="bi bi-search search-icon"></i>
        <input type="text" class="header-search-input" placeholder="Search customers, staff, services…" aria-label="Search">
        <kbd class="search-kbd-hint"><span class="search-kbd-key">⌘</span> /</kbd>
    </div>

    <div class="header-actions">
        <button type="button" class="header-icon-btn header-icon-btn--theme" aria-label="Toggle Theme" title="Toggle Theme" id="themeToggleBtn">
            <i class="bi bi-sun" id="themeToggleIcon"></i>
        </button>

        <button type="button" class="header-icon-btn header-icon-btn--notify position-relative" aria-label="Notifications" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="header-notif-badge">3</span>
        </button>

        <button type="button" class="header-icon-btn header-icon-btn--calendar" aria-label="Calendar" title="Calendar">
            <i class="bi bi-calendar3"></i>
        </button>

        <div class="header-divider" aria-hidden="true"></div>

        <div class="dropdown">
            <a href="#" class="header-profile" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="header-avatar-wrap">
                    <div class="header-avatar">
                        <img src="{{ auth()->user()->avatar_url ?? asset('images/icons8-user-default-64.png') }}" alt="{{ auth()->user()->name ?? 'Administrator' }}">
                    </div>
                    <span class="header-avatar-status" aria-hidden="true"></span>
                </div>
                <div class="header-profile-text d-none d-md-block">
                    <div class="header-profile-name">{{ auth()->user()?->name ?? 'Administrator' }}</div>
                    <div class="header-profile-role">{{ ucfirst(auth()->user()?->role?->name ?? 'Staff') }}</div>
                </div>
                <i class="bi bi-chevron-down header-profile-chevron d-none d-md-block" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
                <li class="dropdown-header">
                    <div class="profile-dropdown-name">{{ auth()->user()?->name ?? 'Administrator' }}</div>
                    <div class="profile-dropdown-email">{{ auth()->user()?->email ?? 'admin@salonpro.com' }}</div>
                </li>
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> My Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="dropdown-item dropdown-item--danger">
                            <i class="bi bi-box-arrow-right"></i>
                            Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
