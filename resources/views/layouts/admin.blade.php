<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIMPelS') }}</title>

    <!-- Assets (Tailwind CSS & JS via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js cloak style -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <!-- Alpine.js for small interactions -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Sidebar toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn  = document.getElementById('sidebar-toggle');
            const sidebar    = document.getElementById('sidebar');
            const logoText   = document.getElementById('logo-text');
            const menuLabels = document.querySelectorAll('.menu-label');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('w-64');
                    sidebar.classList.toggle('w-20');
                    if (logoText) logoText.classList.toggle('hidden');
                    menuLabels.forEach(label => label.classList.toggle('hidden'));
                });
            }
        });
    </script>

    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col">
            @include('partials.header')

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
</body>
</html>
