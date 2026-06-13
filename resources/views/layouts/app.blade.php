
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FreelanHub - Job Board & Freelance Marketplace">
    <title>@yield('title', 'FreelanHub - Job Board & Freelance Marketplace')</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/fav.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome/all.min.css') }}">

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/leaflet.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/output-tailwind.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/output-scss.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>[x-cloak]{display:none !important;}</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div id="app">
        @include('layouts.header')

        <main class="{{ request()->routeIs('admin.*') ? 'pt-16' : 'pt-20' }}">
            @if (trim($__env->yieldContent('suppressFlashModal')) !== '1')
                @include('components.flash-modal')
            @endif

            @yield('content')
        </main>

        @unless(request()->routeIs('admin.*'))
            @include('layouts.footer')
        @endunless
    </div>

    <!-- External JS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/phosphor-icons.js') }}"></script>
    <script src="{{ asset('assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/leaflet.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
