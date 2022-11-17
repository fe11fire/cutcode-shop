@if ($paginator->hasPages())
    <nav>
        <ul class="flex flex-wrap items-center justify-center gap-3">
            @if (!$paginator->onFirstPage())
            <li><a href="{{ $paginator->previousPageUrl() }}" class="block p-3 text-white hover:text-pink text-sm font-black leading-none">{!! __('pagination.previous') !!}</a></li>
                
            @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="text-body/50 text-sm font-black leading-none" aria-disabled="true">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                <li class="active" aria-current="page"><span href="#" class="block p-3 pointer-events-none text-pink text-sm font-black leading-none">{{ $page }}</span></li>
                                @else
                                <li><a href="{{ $url }}" class="block p-3 text-white hover:text-pink text-sm font-black leading-none">{{ $page }}</a></li>
                                    
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                    <li><a href="{{ $paginator->nextPageUrl() }}" class="block p-3 text-white hover:text-pink text-sm font-black leading-none" aria-label="{{ __('pagination.next') }}"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg></a></li>
                    @endif
        </ul>
    </nav>
@endif
