<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>@yield('title', 'Poyenn — Quality Electronics, Delivered.')</title>
    <meta name="description" content="@yield('description', 'Shop premium electronics from top brands. Fast delivery across Nigeria. Quality guaranteed.')">
    @hasSection('keywords')
        <meta name="keywords" content="@yield('keywords')">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', View::getSection('title') ?? 'Poyenn')">
    <meta property="og:description" content="@yield('og_description', View::getSection('description') ?? 'Quality Electronics, Delivered.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- Top Announcement Bar --}}
    <div class="bg-indigo-900 text-white text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <p>📱 Free delivery on orders above ₦50,000</p>
            <p class="hidden md:block">Need help? Call <a href="tel:08012345678" class="underline">08012345678</a></p>
        </div>
    </div>

    {{-- Header --}}
    @include('shop.partials.header')

    {{-- Mobile category drawer --}}
    @include('shop.partials.mobile-menu')

    {{-- Main content --}}
    <main class="flex-1">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 pt-4">
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 pt-4">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    @include('shop.partials.footer')

    @stack('scripts')
</body>
</html>