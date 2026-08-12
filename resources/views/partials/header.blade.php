<header class="app-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle btn btn-outline-secondary" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
    </div>

    <div class="header-actions">
        <button type="button" class="btn btn-icon" aria-label="Notifications">
            <i class="bi bi-bell"></i>
        </button>

        <div class="profile-chip">
            <span class="profile-name">Administrator</span>
            <i class="bi bi-person-circle"></i>
        </div>
    </div>
</header>
