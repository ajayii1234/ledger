<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Tailwind via CDN (no Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: customize tailwind config inline (example) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    // add custom colors, spacing etc. here if needed
                }
            }
        }
    </script>

    <!-- Place to add page-specific styles if needed -->
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        @includeIf('layouts.navigation')

        {{-- If layout is used as a component (x-app-layout), $slot will be set.
            Otherwise (classic @extends), render the content section. --}}
        @isset($slot)
            <!-- Component-style usage -->
            {{ $slot }}
        @else
            <!-- Classic extends-style usage -->
            @hasSection('header')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main>
                @yield('content')
            </main>
        @endisset
    </div>

    <!-- Place to add page-specific scripts -->
    @stack('scripts')
</body>
</html>
