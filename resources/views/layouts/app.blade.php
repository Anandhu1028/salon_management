<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="SalonPro — Premium Salon Management System. Manage staff, customers, services, products, and job cards from one place.">

    <title>@yield('title', 'SalonPro — Salon Management System')</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Global CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/management.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/mgmt-stats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/premium-list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/management/pagination.css') }}">

    {{-- Page-specific CSS --}}
    @stack('styles')

</head>

<body class="app-shell">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="app-main">

        {{-- Header --}}
        @include('partials.header')

        {{-- Main Content --}}
        <main class="app-content">
            @yield('content')
        </main>

        

    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toastContainer"></div>

    {{-- Bootstrap JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Global JavaScript --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Page-specific JavaScript --}}
    @stack('scripts')

</body>

</html>