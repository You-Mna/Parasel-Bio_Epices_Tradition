@if ($paginator->hasPages())
    <nav class="admin-pagination-nav" role="navigation" aria-label="Pagination">
        <p class="admin-pagination-info">
            Affichage de <strong>{{ $paginator->firstItem() ?? 0 }}</strong> à <strong>{{ $paginator->lastItem() ?? 0 }}</strong> sur <strong>{{ $paginator->total() }}</strong> résultats
        </p>
        <ul class="admin-pagination-list">
            {{-- Précédent --}}
            @if ($paginator->onFirstPage())
                <li class="admin-pagination-item disabled">
                    <span class="admin-pagination-link">Précédent</span>
                </li>
            @else
                <li class="admin-pagination-item">
                    <a class="admin-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Précédent</a>
                </li>
            @endif

            {{-- Numéros de page --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="admin-pagination-item admin-pagination-ellipsis"><span>{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="admin-pagination-item active"><span class="admin-pagination-link">{{ $page }}</span></li>
                        @else
                            <li class="admin-pagination-item"><a class="admin-pagination-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($paginator->hasMorePages())
                <li class="admin-pagination-item">
                    <a class="admin-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant</a>
                </li>
            @else
                <li class="admin-pagination-item disabled">
                    <span class="admin-pagination-link">Suivant</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
