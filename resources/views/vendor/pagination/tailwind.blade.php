@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-end space-x-2 select-none">
        {{-- Info jumlah data --}}
        <span class="text-sm text-gray-500 mr-2">
            Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
        </span>

        <ul class="inline-flex items-center rounded-lg bg-white/80 border border-gray-200 shadow px-2 py-1 space-x-0.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-2 py-1 text-gray-300 bg-gray-100 rounded-md cursor-default">&lsaquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-2 py-1 text-gray-500 hover:bg-blue-50 rounded-md transition"
                       rel="prev">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="px-2 py-1 text-gray-400">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="px-3 py-1 rounded-md bg-blue-100 text-blue-700 font-bold shadow-sm">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="px-3 py-1 rounded-md text-gray-600 hover:bg-blue-50 transition"
                                   aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="px-2 py-1 text-gray-500 hover:bg-blue-50 rounded-md transition"
                       rel="next">&rsaquo;</a>
                </li>
            @else
                <li>
                    <span class="px-2 py-1 text-gray-300 bg-gray-100 rounded-md cursor-default">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
