@extends('layouts.app')

@section('content')
<div class="account-welcome-simple">
    <h1>Bienvenue {{ $user->first_name }}, gérez vos informations et suivez vos commandes</h1>
</div>

<div class="account-grid">
    <div class="account-section">
        <h2>Informations personnelles</h2>
        <div class="card">
            <p><strong>Nom:</strong> {{ $user->last_name }}</p>
            <p><strong>Prénom:</strong> {{ $user->first_name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Téléphone:</strong> {{ $user->phone }}</p>
        </div>
    </div>

    <div class="account-section">
        <h2>Dernière commande</h2>
        @if($lastOrder)
            <div class="order-card">
                <div class="order-header">
                    <h3>Commande n° {{ $lastOrder->id }}</h3>
                    @php
                        $badgeClass = $lastOrder->status === 'livree' ? 'success' : ($lastOrder->status === 'en_cours' ? 'warning' : 'danger');
                        $label = $lastOrder->status === 'livree' ? 'Livrée' : ($lastOrder->status === 'en_cours' ? 'En cours' : 'Annulée');
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                </div>
                <div class="order-date">
                    Passée le {{ $lastOrder->created_at->format('d/m/Y') }}
                </div>
                <div class="order-items">
                    @foreach($lastOrder->items as $item)
                        <div class="order-item">
                            <span class="product-name">{{ $item->product->name }}</span>
                            <span class="quantity">{{ $item->quantity }} unité{{ $item->quantity > 1 ? 's' : '' }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="order-total">
                    <strong>Total : {{ number_format($lastOrder->total, 0, ',', ' ') }} FCFA</strong>
                </div>
                <div class="order-actions">
                    <a class="btn" href="{{ route('client.orders') }}">Voir toutes mes commandes</a>
                </div>
            </div>
        @else
            <div class="empty-state">
                <p>Aucune commande effectuée.</p>
            </div>
        @endif
    </div>
</div>

@endsection

