<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SIMPelS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const logoText = document.getElementById('logo-text');
            const menuLabels = document.querySelectorAll('.menu-label');

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('w-20');
                logoText.classList.toggle('hidden');
                menuLabels.forEach(label => label.classList.toggle('hidden'));
            });
        });
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gray-800 text-white flex flex-col h-full transition-all duration-300 ease-in-out">
            <div class="bg-blue-800 text-white py-4 text-2xl font-bold tracking-wide flex items-center justify-between px-4">
                <span id="logo-text">SIMPelS</span>
                <button id="sidebar-toggle" class="inline-flex items-center px-2 py-1 rounded text-white hover:bg-blue-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-2 py-6 space-y-2">
                <a href="/dashboard" class="flex items-center space-x-2 px-3 py-2 rounded-md transition {{ request()->is('dashboard') ? 'bg-blue-800' : 'hover:bg-gray-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                    </svg>
                    <span class="menu-label">Dashboard</span>
                </a>

                <!-- Menu Utama Data Santri -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-2 px-3 py-2 text-sm font-semibold text-white uppercase tracking-wider">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.779.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="menu-label">Kesantrian</span>
                    </div>
                    <a href="/santris" class="ml-8 flex items-center space-x-2 px-3 py-1 text-sm rounded-md transition {{ request()->is('santris*') ? 'bg-blue-800' : 'hover:bg-gray-700' }}">
                        <span class="menu-label">Data Santri</span>
                    </a>
                    <!-- Submenu lainnya bisa ditambahkan di sini -->
                </div>

                <!-- Tambahkan menu lainnya di sini -->
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="p-4">
                @csrf
                <button class="w-full flex items-center space-x-2 px-3 py-2 hover:bg-red-100 text-red-200 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H4a3 3 0 01-3-3V7a3 3 0 013-3h6a3 3 0 013 3v1" />
                    </svg>
                    <span class="menu-label">Logout</span>
                </button>
            </form>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header Topbar -->
            <header class="bg-blue-800 text-white p-4 flex justify-end items-center shadow md:px-6">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-white text-blue-800 font-bold rounded-full flex items-center justify-center uppercase">
                        {{ auth()->user()->name[0] ?? '?' }}
                    </div>
                    <div class="text-sm">
                        <div class="font-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-xs text-blue-100">Admin</div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>
</body>
</html>
