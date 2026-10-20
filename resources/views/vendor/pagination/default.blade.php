@if ($paginator->hasPages())
    <nav aria-label="Страницы каталога">
        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true"><span aria-hidden="true">‹</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Предыдущая страница">‹</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Следующая страница">›</a></li>
            @else
                <li class="disabled" aria-disabled="true"><span aria-hidden="true">›</span></li>
            @endif
        </ul>
    </nav>
@endif
