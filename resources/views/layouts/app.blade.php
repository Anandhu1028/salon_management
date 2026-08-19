<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Salon Management System')
    </title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    {{-- Bootstrap --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    >

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    >

    {{-- Application CSS + JS --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{--
        Keep the core shell available when a local Vite server is stopped or
        its generated assets are stale.  This is the stable stylesheet for
        the authenticated application layout (sidebar, header, and content).
    --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>

<body id="app-body" class="app-shell">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main --}}
    <div class="app-main">

        {{-- Header --}}
        @include('partials.header')

        {{-- Content --}}
        <main class="app-content">
            @yield('content')
        </main>

    </div>

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay"></div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>
