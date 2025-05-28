<!-- resources/views/partials/sidebar.blade.php -->
<aside id="sidebar" class="w-64 bg-[#212936] text-white flex flex-col h-screen fixed top-0 left-0 z-40 transition-all duration-300 ease-in-out">
    <!-- Header biru -->
    <div class="bg-blue-800 text-white py-4 text-2xl font-bold tracking-wide flex items-center justify-between px-4 border-b border-blue-700">
        <span id="logo-text">SIMPelS</span>
        <button id="sidebar-toggle" class="inline-flex items-center px-2 py-1 rounded text-white hover:bg-blue-700">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
    <!-- Menu Sidebar -->
    <nav class="flex-1 px-2 py-6 space-y-2 overflow-y-auto">
        <a href="/dashboard"
           class="flex items-center space-x-2 px-3 py-2 rounded-md transition {{ request()->is('dashboard') ? 'bg-blue-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18" />
            </svg>
            <span class="menu-label">Dashboard</span>
        </a>
        <!-- Kesantrian Menu -->
        <div x-data="{ open: true }" class="space-y-1" x-cloak>
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-semibold uppercase tracking-wider hover:bg-gray-800 rounded-md text-white">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.779.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="menu-label">Kesantrian</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform duration-200"
                    :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open" class="pl-8 space-y-1">
                <a href="/santris" class="flex items-center space-x-2 px-3 py-1 text-sm rounded-md transition {{ request()->is('santris*') ? 'bg-blue-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
                    <span class="material-icons text-base">chevron_right</span>
                    <span class="menu-label">Data Santri</span>
                </a>
                <a href="/kelas" class="flex items-center space-x-2 px-3 py-1 text-sm rounded-md transition {{ request()->is('kelas*') ? 'bg-blue-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
                    <span class="material-icons text-base">chevron_right</span>
                    <span class="menu-label">Kelas</span>
                </a>
                <a href="/asrama" class="flex items-center space-x-2 px-3 py-1 text-sm rounded-md transition {{ request()->is('asrama*') ? 'bg-blue-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
                    <span class="material-icons text-base">chevron_right</span>
                    <span class="menu-label">Asrama</span>
                </a>
               <a href="{{ route('rfid-tags.index') }}"
   class="flex items-center space-x-2 px-3 py-1 text-sm rounded-md transition
          {{ request()->is('rfid-tags*') ? 'bg-blue-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
  <span class="material-icons text-base">qr_code_2</span>
  <span class="menu-label">RFID Tags</span>
</a>

                <a href="{{ route('mutasi_santri.index') }}"
                   class="flex items-center space-x-2 px-3 py-1 text-sm rounded-md transition {{ request()->is('mutasi-santri*') ? 'bg-blue-800 text-white' : 'hover:bg-gray-800 text-gray-300' }}">
                    <span class="material-icons text-base">swap_horiz</span>
                    <span class="menu-label">Mutasi Santri</span>
                </a>
            </div>
        </div>
    </nav>
    <!-- Logout fixed bottom -->
    <div class="mt-auto p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center space-x-2 px-3 py-2 hover:bg-red-100 text-red-200 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H4a3 3 0 01-3-3V7a3 3 0 013-3h6a3 3 0 013 3v1" />
                </svg>
                <span class="menu-label">Logout</span>
            </button>
        </form>
    </div>
</aside>
<!-- Alpine.js jika belum dimuat global -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<!-- Material Icons jika butuh -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
