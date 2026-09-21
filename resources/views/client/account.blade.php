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
                    <h3>Commande n° {{ $lastOrderNumber }}</h3>
                    @php
                        $badgeClass = match($lastOrder->status) {
                            'payee_en_ligne', 'payee' => 'success',
                            'a_la_livraison', 'en_cours' => 'warning',
                            'livree_payee', 'livree' => 'success',
                            'annulee' => 'danger',
                            default => 'secondary',
                        };
                        $label = match($lastOrder->status) {
                            'payee_en_ligne', 'payee' => 'Payée en ligne',
                            'a_la_livraison' => 'Paiement à la livraison',
                            'en_cours' => 'En attente paiement',
                            'livree_payee', 'livree' => 'Livrée payée',
                            'annulee' => 'Annulée',
                            default => ucfirst(str_replace('_', ' ', $lastOrder->status)),
                        };
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
                    @php
                        // Calculer le total à partir des items si le total de la commande est à 0 ou null
                        $calculatedTotal = $lastOrder->total > 0 ? $lastOrder->total : $lastOrder->items->sum('line_total');
                    @endphp
                    <strong>Total : {{ number_format($calculatedTotal, 0, ',', ' ') }} FCFA</strong>
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

