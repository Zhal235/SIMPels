<!-- resources/views/partials/header.blade.php -->
<header class="bg-blue-800 text-white py-3 px-4 sm:px-6 flex justify-end items-center shadow-md">
    <div class="flex items-center space-x-3">
        <div class="relative">
            <div class="w-10 h-10 bg-blue-600 border-2 border-blue-400 text-white font-semibold rounded-full flex items-center justify-center uppercase text-lg">
                {{ auth()->user()->name[0] ?? '?' }}
            </div>
            {{-- Optional: Green dot for online status --}}
            {{-- <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-500 border-2 border-white ring-2 ring-green-400"></span> --}}
        </div>
        <div class="text-sm text-right">
            <div class="font-semibold">{{ auth()->user()->name ?? 'User' }}</div>
            <div class="text-xs text-blue-200">{{ auth()->user()->role ?? 'User' }}</div>
        </div>
    </div>
</header>
