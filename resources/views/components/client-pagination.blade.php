@if ($paginator->hasPages())
    <nav class="pagination-client" role="navigation" aria-label="Pagination des commandes">
        <ul class="pagination-client-list">
            {{-- Lien Précédent --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-client-item disabled">
                    <span class="pagination-client-link">Précédent</span>
                </li>
            @else
                <li class="pagination-client-item">
                    <a class="pagination-client-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        Précédent
                    </a>
                </li>
            @endif

            {{-- Liens de pages (compact, avec ellipses) --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $window = 1; // nombre de pages autour de la page courante
            @endphp

            {{-- Première page --}}
            @if ($current > 1 + $window)
                <li class="pagination-client-item">
                    <a class="pagination-client-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if ($current > 2 + $window)
                    <li class="pagination-client-item disabled">
                        <span class="pagination-client-link">…</span>
                    </li>
                @endif
            @endif

            {{-- Pages autour de la page courante --}}
            @for ($page = max(1, $current - $window); $page <= min($last, $current + $window); $page++)
                @if ($page == $current)
                    <li class="pagination-client-item active">
                        <span class="pagination-client-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="pagination-client-item">
                        <a class="pagination-client-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Dernière page --}}
            @if ($current < $last - $window)
                @if ($current < $last - ($window + 1))
                    <li class="pagination-client-item disabled">
                        <span class="pagination-client-link">…</span>
                    </li>
                @endif
                <li class="pagination-client-item">
                    <a class="pagination-client-link" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                </li>
            @endif

            {{-- Lien Suivant --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-client-item">
                    <a class="pagination-client-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Suivant
                    </a>
                </li>
            @else
                <li class="pagination-client-item disabled">
                    <span class="pagination-client-link">Suivant</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

