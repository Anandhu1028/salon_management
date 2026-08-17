{{--
    Mobile Bottom Navigation Bar
    Appears only on mobile screens (<= 768px)
--}}

@php
    $currentRoute = request()->route()?->getName() ?? '';
    
    // Determine the add action for the floating '+' button
    $addModalTarget = match(true) {
        request()->routeIs('job-cards.*') => '#jobCardModal',
        request()->routeIs('staff.*') => '#staffModal',
        request()->routeIs('customers.*') => '#customerModal',
        request()->routeIs('services.*') => '#serviceModal',
        request()->routeIs('products.*') => '#productModal',
        default => '#jobCardModal',
    };

    $addOnclickAction = match(true) {
        request()->routeIs('job-cards.*') => 'window.openAddJobCardModal ? window.openAddJobCardModal() : null',
        request()->routeIs('staff.*') => 'window.openAddStaffModal ? window.openAddStaffModal() : null',
        request()->routeIs('customers.*') => 'window.openAddCustomerModal ? window.openAddCustomerModal() : null',
        request()->routeIs('services.*') => 'window.openAddServiceModal ? window.openAddServiceModal() : null',
        request()->routeIs('products.*') => 'window.openAddProductModal ? window.openAddProductModal() : null',
        default => null,
    };
@endphp

<nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
    <div class="mobile-bottom-nav__inner">
        {{-- 1. Dashboard --}}
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
            <span class="mobile-nav-icon">
                <i class="bi {{ request()->routeIs('dashboard') ? 'bi-grid-1x2-fill' : 'bi-grid-1x2' }}"></i>
            </span>
            <span class="mobile-nav-label">Dashboard</span>
        </a>

        {{-- 2. Job Cards --}}
        <a href="{{ route('job-cards.index') }}" class="mobile-nav-item {{ request()->routeIs('job-cards.*') ? 'is-active' : '' }}">
            <span class="mobile-nav-icon">
                <i class="bi {{ request()->routeIs('job-cards.*') ? 'bi-clipboard2-check-fill' : 'bi-clipboard2-check' }}"></i>
            </span>
            <span class="mobile-nav-label">Job Cards</span>
        </a>

        {{-- 3. Floating '+' Action Button --}}
        <div class="mobile-nav-fab-wrap">
            @if($addOnclickAction)
                <button
                    type="button"
                    class="mobile-nav-fab"
                    data-bs-toggle="modal"
                    data-bs-target="{{ $addModalTarget }}"
                    onclick="{{ $addOnclickAction }}"
                    aria-label="Add new record"
                    title="Add"
                >
                    <i class="bi bi-plus-lg"></i>
                </button>
            @else
                <a
                    href="{{ route('job-cards.index') }}"
                    class="mobile-nav-fab"
                    aria-label="Create Job Card"
                    title="Create Job Card"
                >
                    <i class="bi bi-plus-lg"></i>
                </a>
            @endif
        </div>

        {{-- 4. Services / Calendar --}}
        <a href="{{ route('services.index') }}" class="mobile-nav-item {{ request()->routeIs('services.*') ? 'is-active' : '' }}">
            <span class="mobile-nav-icon">
                <i class="bi {{ request()->routeIs('services.*') ? 'bi-scissors' : 'bi-scissors' }}"></i>
            </span>
            <span class="mobile-nav-label">Services</span>
        </a>

        {{-- 5. More (Opens Mobile Drawer) --}}
        <button type="button" class="mobile-nav-item" id="mobileNavMoreBtn" aria-label="Open menu drawer">
            <span class="mobile-nav-icon">
                <i class="bi bi-three-dots"></i>
            </span>
            <span class="mobile-nav-label">More</span>
        </button>
    </div>
</nav>
