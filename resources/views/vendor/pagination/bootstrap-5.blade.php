@if ($paginator->hasPages())
    <nav aria-label="Pagination" class="pag-nav">
        <ul class="pag-list">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="pag-item disabled">
                    <span class="pag-btn"><i class="bi bi-chevron-left"></i> Prev</span>
                </li>
            @else
                <li class="pag-item">
                    <a class="pag-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i> Prev
                    </a>
                </li>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pag-item disabled"><span class="pag-btn pag-dots">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pag-item active"><span class="pag-btn">{{ $page }}</span></li>
                        @else
                            <li class="pag-item"><a class="pag-btn" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="pag-item">
                    <a class="pag-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pag-item disabled">
                    <span class="pag-btn">Next <i class="bi bi-chevron-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
