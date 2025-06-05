<!-- resources/views/partials/sidebar.blade.php -->
<aside id="sidebar" class="w-64 bg-slate-900 text-slate-200 flex flex-col h-screen fixed top-0 left-0 z-40 transition-transform duration-300 ease-in-out shadow-lg">
    <!-- Sidebar Header -->
    <div class="bg-blue-700 text-white py-4 text-2xl font-bold tracking-tight flex items-center justify-between px-4 border-b border-blue-600 shadow-sm">
        <span id="logo-text" class="transition-opacity duration-300">SIMPelS</span>
        <button id="sidebar-toggle" class="p-1.5 rounded-md text-blue-200 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
        </button>
    </div>

    <!-- Menu Sidebar -->
    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-slate-800">
        <a href="/dashboard"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 ease-in-out group {{ request()->is('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <span class="material-icons-outlined text-xl opacity-80 group-hover:opacity-100 {{ request()->is('dashboard') ? 'text-white' : 'text-blue-400' }}">dashboard</span>
            <span class="menu-label font-medium">Dashboard</span>
        </a>

        <!-- Kesantrian Menu -->
        <div x-data="{ open: {{ request()->is('santris*') || request()->is('kelas*') || request()->is('asrama*') || request()->is('rfid-tags*') || request()->is('mutasi-santri*') ? 'true' : 'false' }} }" class="space-y-1" x-cloak>
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-semibold uppercase tracking-wider hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition-all duration-200 ease-in-out group">
                <div class="flex items-center space-x-3">
                    <span class="material-icons-outlined text-xl opacity-80 group-hover:opacity-100 text-blue-400">groups</span>
                    <span class="menu-label">Kesantrian</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200 text-slate-500 group-hover:text-white"
                    :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
           </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-4 pl-3 border-l border-slate-700 space-y-1">
                <a href="/santris" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('santris*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('santris*') ? 'text-white' : 'text-blue-400' }}">badge</span>
                    <span class="menu-label">Data Santri</span>
                </a>
                <a href="/kelas" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('kelas*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('kelas*') ? 'text-white' : 'text-blue-400' }}">school</span>
                    <span class="menu-label">Kelas</span>
                </a>
                <a href="/asrama" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('asrama*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('asrama*') ? 'text-white' : 'text-blue-400' }}">cottage</span>
                    <span class="menu-label">Asrama</span>
                </a>
                <a href="{{ route('rfid-tags.index') }}"
                   class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('rfid-tags*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('rfid-tags*') ? 'text-white' : 'text-blue-400' }}">qr_code_2</span>
                    <span class="menu-label">RFID Tags</span>
                </a>
                <a href="{{ route('mutasi_santri.index') }}"
                   class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('mutasi-santri*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('mutasi-santri*') ? 'text-white' : 'text-blue-400' }}">transfer_within_a_station</span>
                    <span class="menu-label">Mutasi Santri</span>
                </a>
            </div>
        </div>

        <!-- Keuangan Menu -->
        <div x-data="{ open: {{ request()->is('keuangan*') ? 'true' : 'false' }} }" class="space-y-1" x-cloak>
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-semibold uppercase tracking-wider hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition-all duration-200 ease-in-out group">
                <div class="flex items-center space-x-3">
                    <span class="material-icons-outlined text-xl opacity-80 group-hover:opacity-100 text-blue-400">account_balance_wallet</span>
                    <span class="menu-label">Keuangan</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200 text-slate-500 group-hover:text-white"
                    :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
           </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-4 pl-3 border-l border-slate-700 space-y-1">
                <a href="{{ route('keuangan.pembayaran-santri.index') }}" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('keuangan/pembayaran-santri*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('keuangan/pembayaran-santri*') ? 'text-white' : 'text-blue-400' }}">payments</span>
                    <span class="menu-label">Pembayaran Santri</span>
                </a>
                <a href="{{ route('keuangan.jenis-tagihan.index') }}" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('keuangan/jenis-tagihan*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('keuangan/jenis-tagihan*') ? 'text-white' : 'text-blue-400' }}">category</span>
                    <span class="menu-label">Jenis Tagihan</span>
                </a>
                <a href="{{ route('keuangan.tagihan-santri.index') }}" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('keuangan/tagihan-santri*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('keuangan/tagihan-santri*') ? 'text-white' : 'text-blue-400' }}">receipt_long</span>
                    <span class="menu-label">Tagihan Santri</span>
                </a>
            </div>
        </div>

        <!-- AKADEMIK -->
        <div x-data="{ open: {{ request()->is('tahun-ajaran*') ? 'true' : 'false' }} }" class="space-y-1" x-cloak>
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-semibold uppercase tracking-wider hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition-all duration-200 ease-in-out group">
                <div class="flex items-center space-x-3">
                    <span class="material-icons-outlined text-xl opacity-80 group-hover:opacity-100 text-blue-400">menu_book</span>
                    <span class="menu-label">Akademik</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200 text-slate-500 group-hover:text-white"
                    :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
           </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-4 pl-3 border-l border-slate-700 space-y-1">
                <a href="{{ route('akademik.tahun-ajaran.index') }}" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('akademik/tahun-ajaran*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('akademik/tahun-ajaran*') ? 'text-white' : 'text-blue-400' }}">date_range</span>
                    <span class="menu-label">Tahun Ajaran</span>
                </a>
            </div>
        </div>

        <!-- User Management -->
        @if(Auth::check() && Auth::user()->hasRole('admin')) {{-- Assuming Spatie/laravel-permission --}}
        <div x-data="{ open: {{ request()->is('users*') || request()->is('roles*') ? 'true' : 'false' }} }" class="space-y-1">
             <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-semibold uppercase tracking-wider hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition-all duration-200 ease-in-out group">
                <div class="flex items-center space-x-3">
                    <span class="material-icons-outlined text-xl opacity-80 group-hover:opacity-100 text-blue-400">admin_panel_settings</span>
                    <span class="menu-label">Admin Area</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200 text-slate-500 group-hover:text-white"
                    :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
           </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-4 pl-3 border-l border-slate-700 space-y-1">
                <a href="{{ route('users.index') }}"
                   class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('users*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('users*') ? 'text-white' : 'text-blue-400' }}">manage_accounts</span>
                    <span class="menu-label">User Management</span>
                </a>
                <a href="{{ route('roles.index') }}"
                   class="flex items-center space-x-3 px-3 py-2 text-sm rounded-md transition-all duration-200 ease-in-out group {{ request()->is('roles*') ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <span class="material-icons-outlined text-lg opacity-80 group-hover:opacity-100 {{ request()->is('roles*') ? 'text-white' : 'text-blue-400' }}">verified_user</span>
                    <span class="menu-label">Role Management</span>
                </a>
            </div>
        </div>
        @endif
    </nav>

    <!-- Logout fixed bottom -->
    <div class="mt-auto p-3 border-t border-slate-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-all duration-200 ease-in-out group">
                <span class="material-icons-outlined text-xl opacity-80 group-hover:opacity-100">logout</span>
                <span class="menu-label font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Alpine.js jika belum dimuat global -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<!-- Material Icons Outlined (recommended for modern look) -->
<link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">

<style>
    /* Custom scrollbar for WebKit browsers */
    #sidebar nav::-webkit-scrollbar {
        width: 6px;
    }
    #sidebar nav::-webkit-scrollbar-track {
        background: #1e293b; /* slate-800 */
    }
    #sidebar nav::-webkit-scrollbar-thumb {
        background: #334155; /* slate-700 */
        border-radius: 3px;
    }
    #sidebar nav::-webkit-scrollbar-thumb:hover {
        background: #475569; /* slate-600 */
    }

    /* Sidebar toggle animation */
    .sidebar-collapsed #logo-text {
        opacity: 0;
    }
    .sidebar-collapsed .menu-label {
        display: none;
    }
    .sidebar-collapsed #sidebar {
        width: 4.5rem; /* Adjust to fit icons only */
    }
    .sidebar-collapsed .menu-label{
        opacity:0;
        width:0;
        transition: opacity 0.1s ease-out, width 0.1s ease-out;
    }
    #sidebar:not(.sidebar-collapsed) .menu-label{
        opacity:1;
        transition: opacity 0.2s ease-in 0.1s;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const logoText = document.getElementById('logo-text');
        const menuLabels = document.querySelectorAll('.menu-label');
        const mainContent = document.querySelector('main'); // Assuming your main content area has a <main> tag

        // Function to toggle sidebar state
        function toggleSidebar() {
            sidebar.classList.toggle('sidebar-collapsed');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebarState', 'collapsed');
                if(mainContent) mainContent.style.marginLeft = '4.5rem'; // Adjust based on collapsed width
            } else {
                localStorage.setItem('sidebarState', 'expanded');
                if(mainContent) mainContent.style.marginLeft = '16rem'; // Adjust based on expanded width
            }
        }

        // Event listener for the toggle button
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        // Check local storage for saved sidebar state
        const savedSidebarState = localStorage.getItem('sidebarState');
        if (savedSidebarState === 'collapsed') {
            sidebar.classList.add('sidebar-collapsed');
            if(mainContent) mainContent.style.marginLeft = '4.5rem';
        } else {
            // Default to expanded if no state or 'expanded'
            sidebar.classList.remove('sidebar-collapsed');
            if(mainContent) mainContent.style.marginLeft = '16rem';
        }

        // Adjust main content margin on load based on initial sidebar state
        // This is important if the page loads with the sidebar already in a certain state
        if (sidebar.classList.contains('sidebar-collapsed')) {
            if(mainContent) mainContent.style.marginLeft = '4.5rem';
        } else {
            if(mainContent) mainContent.style.marginLeft = '16rem';
        }
    });
</script>
