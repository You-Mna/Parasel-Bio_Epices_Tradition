@extends('layouts.admin')

@section('content')
<!-- Header du Dashboard -->
<div class="dashboard-header">
    <div>
        <h1 class="dashboard-title">Tableau de bord</h1>
        <p class="dashboard-subtitle">Gérez votre boutique Parasel-Bio en toute simplicité</p>
    </div>
    <div class="dashboard-actions">
        @php
            $lowStockList = isset($lowStockProducts) ? $lowStockProducts : collect();
            $lowStockCount = $lowStockList->count();
        @endphp
        <div class="alert-dropdown" id="lowStockDropdown">
            <button type="button" class="btn-dashboard alert-toggle" id="lowStockToggle" onclick="toggleLowStockMenu(event)">
                <i class="fa-solid fa-bell"></i>
                <span>Alerte</span>
                <span class="alert-count">{{ $lowStockCount }}</span>
            </button>
            <div class="alert-menu" id="lowStockMenu">
                <div class="alert-menu-header">
                    <span class="alert-menu-title">Stock faible</span>
                    <span class="alert-menu-subtitle">
                        @if($lowStockCount > 0)
                            Produits sous 20&nbsp;% du stock initial
                        @else
                            Aucune alerte de stock pour le moment
                        @endif
                    </span>
                </div>
                @if($lowStockCount > 0)
                    <ul class="alert-menu-list">
                        @foreach($lowStockList as $product)
                            @php
                                $percent = $product->initial_stock > 0
                                    ? round(($product->stock / $product->initial_stock) * 100)
                                    : 0;
                            @endphp
                            <li class="alert-menu-item">
                                <span class="alert-product-name">{{ $product->name }}</span>
                                <span class="alert-product-meta">{{ $product->stock }} restants ({{ $percent }}&nbsp;%)</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <a href="/" class="btn-dashboard">
            <i class="fa-solid fa-home"></i> Boutique principale
        </a>
    </div>
</div>

<!-- Statistiques principales -->
<div class="dashboard-stats">
    <!-- Produits -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon products">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>
        <h3 class="stat-title">Produits</h3>
        <div class="stat-value">{{ $stats['products'] }}</div>
        <p class="stat-description">Produits disponibles dans votre catalogue</p>
        <a href="{{ route('admin.products.index') }}" class="stat-action">
            <i class="fa-solid fa-eye"></i> Voir les produits
        </a>
    </div>

    <!-- Commandes -->
    <div class="stat-card orders">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fa-solid fa-shopping-bag"></i>
            </div>
        </div>
        <h3 class="stat-title">Commandes</h3>
        <div class="stat-value">{{ $stats['orders'] }}</div>
        <p class="stat-description">Total des commandes reçues</p>
        <a href="{{ route('admin.orders.index') }}" class="stat-action">
            <i class="fa-solid fa-eye"></i> Voir les commandes
        </a>
    </div>

    <!-- Messages -->
    <div class="stat-card messages">
        <div class="stat-header">
            <div class="stat-icon messages">
                <i class="fa-solid fa-envelope"></i>
            </div>
        </div>
        <h3 class="stat-title">Messages</h3>
        <div class="stat-value">{{ $stats['messages'] }}</div>
        <p class="stat-description">Messages clients en attente</p>
        <a href="{{ route('admin.messages.index') }}" class="stat-action">
            <i class="fa-solid fa-eye"></i> Voir les messages
        </a>
    </div>

    <!-- Témoignages -->
    <div class="stat-card experiences">
        <div class="stat-header">
            <div class="stat-icon experiences">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
        <h3 class="stat-title">Témoignages</h3>
        <div class="stat-value">{{ $stats['experiences'] }}</div>
        <p class="stat-description">Témoignages clients ({{ $stats['experiences_published'] }} publiés)</p>
        <a href="{{ route('admin.experiences.index') }}" class="stat-action">
            <i class="fa-solid fa-eye"></i> Voir les témoignages
        </a>
    </div>
</div>

<!-- Statistiques secondaires -->
<div class="dashboard-stats">
    <!-- Commandes en cours -->
    <div class="stat-card pending">
        <div class="stat-header">
            <div class="stat-icon pending">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
        <h3 class="stat-title">En cours</h3>
        <div class="stat-value">{{ $stats['orders_en_cours'] }}</div>
        <p class="stat-description">Commandes prêtes à être livrées</p>
        <span class="badge warning">{{ $stats['orders_en_cours'] }} en cours</span>
    </div>

    <!-- Commandes livrées -->
    <div class="stat-card delivered">
        <div class="stat-header">
            <div class="stat-icon delivered">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>
        <h3 class="stat-title">Livrées</h3>
        <div class="stat-value">{{ $stats['orders_livree'] }}</div>
        <p class="stat-description">Commandes livrées avec succès</p>
        <span class="badge success">{{ $stats['orders_livree'] }} terminées</span>
    </div>

    <!-- Commandes annulées -->
    <div class="stat-card cancelled">
        <div class="stat-header">
            <div class="stat-icon cancelled">
                <i class="fa-solid fa-times-circle"></i>
            </div>
        </div>
        <h3 class="stat-title">Annulées</h3>
        <div class="stat-value">{{ $stats['orders_annulee'] }}</div>
        <p class="stat-description">Commandes annulées</p>
        <span class="badge danger">{{ $stats['orders_annulee'] }} annulées</span>
    </div>
</div>

<script>
function toggleLowStockMenu(event) {
    event.stopPropagation();
    var menu = document.getElementById('lowStockMenu');
    if (!menu) return;
    menu.classList.toggle('is-open');
}

document.addEventListener('click', function (e) {
    var menu = document.getElementById('lowStockMenu');
    var dropdown = document.getElementById('lowStockDropdown');
    if (!menu || !dropdown) return;
    if (!dropdown.contains(e.target)) {
        menu.classList.remove('is-open');
    }
});
</script>
@endsection
