@if ($paginator->hasPages())
<ul class="pagination">
    {{-- Previous Page Link --}}
    @if (!$paginator->onFirstPage())
        <li class="pagination-Item no-circle">
            <a class="pagination-Item-Link arrow-right left" href="{{ $paginator->previousPageUrl() }}"></a>
        </li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="pagination-Item">
                        <a class="pagination-Item-Link isActive" href="{{ $url }}"><span>{{ $page }}</span></a>
                    </li>
                @else
                    <li class="pagination-Item">
                        <a class="pagination-Item-Link" href="{{ $url }}"><span>{{ $page }}</span></a>
                    </li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li class="pagination-Item no-circle">
            <a class="pagination-Item-Link arrow-right" href="{{ $paginator->nextPageUrl() }}"></a>
        </li>
    @endif
</ul>
<p class="page">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}ページ</p>
@endif
