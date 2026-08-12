<aside class="sidebar">
    <div class="sidebar-brand">
        <span>Salon Management</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>
    </nav>
</aside>
