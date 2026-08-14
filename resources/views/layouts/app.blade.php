<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @yield('title', 'Salon Management System')
    </title>

        {{-- Bootstrap --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

        {{-- Bootstrap Icons --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        {{-- Application assets are compiled and versioned by Vite. --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="app-shell">
    {{-- Sidebar --}}
    @include('partials.sidebar')
        <div class="app-main">
            {{-- Header --}}
            @include('partials.header')
            {{-- Page Content --}}
            <main class="app-content">
                @yield('content')
            </main>
        </div>

    {{-- Mobile Sidebar Overlay --}}
    <div class="sidebar-overlay"></div>
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
