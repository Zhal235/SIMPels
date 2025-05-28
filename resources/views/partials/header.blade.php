<!-- resources/views/partials/header.blade.php -->
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
