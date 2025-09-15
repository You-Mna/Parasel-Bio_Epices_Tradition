@extends('layouts.admin')

@section('content')
<!-- Header du Dashboard -->
<div class="dashboard-header">
    <div>
        <h1 class="dashboard-title">Tableau de bord</h1>
        <p class="dashboard-subtitle">Gérez votre boutique Parasel-Bio en toute simplicité</p>
    </div>
    <div class="dashboard-actions">
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
            <i class="fa-solid fa-cog"></i> Gérer les produits
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
            <i class="fa-solid fa-eye"></i> Consulter
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
            <i class="fa-solid fa-cog"></i> Gérer les témoignages
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
        <p class="stat-description">Commandes en cours de traitement</p>
        <span class="badge warning">{{ $stats['orders_en_cours'] }} en attente</span>
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
@endsection

