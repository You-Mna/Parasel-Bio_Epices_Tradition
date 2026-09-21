@extends('layouts.app')

@section('content')
<h1>Mes commandes</h1>

@if($orders->isEmpty())
    <div class="empty-state">
        <p>Aucune commande effectuée.</p>
    </div>
@else
    <div class="orders-grid">
        @foreach($orders as $order)
            @php
                // Numéro de commande pour ce client : 1 = première commande, 2 = deuxième, etc. (tri : plus récente en premier)
                $orderNumber = $orders->total() - (($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration - 1);
            @endphp
            <div class="order-card">
                <div class="order-header">
                    <h3>Commande n° {{ $orderNumber }}</h3>
                    @php
                        $badgeClass = match($order->status) {
                            'payee_en_ligne', 'payee' => 'success',
                            'a_la_livraison', 'en_cours' => 'warning',
                            'livree_payee', 'livree' => 'success',
                            'annulee' => 'danger',
                            default => 'secondary',
                        };
                        $label = match($order->status) {
                            'payee_en_ligne', 'payee' => 'Payée en ligne',
                            'a_la_livraison' => 'Paiement à la livraison',
                            'en_cours' => 'En attente paiement',
                            'livree_payee', 'livree' => 'Livrée payée',
                            'annulee' => 'Annulée',
                            default => ucfirst(str_replace('_', ' ', $order->status)),
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                </div>
                <div class="order-date">
                    Passée le {{ $order->created_at->format('d/m/Y') }}
                </div>
                <div class="order-items">
                    @foreach($order->items as $item)
                        <div class="order-item">
                            <span class="product-name">{{ $item->product->name }}</span>
                            <span class="quantity">{{ $item->quantity }} unité{{ $item->quantity > 1 ? 's' : '' }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="order-total">
                    @php
                        // Calculer le total à partir des items si le total de la commande est à 0 ou null
                        $calculatedTotal = $order->total > 0 ? $order->total : $order->items->sum('line_total');
                    @endphp
                    <strong>Total : {{ number_format($calculatedTotal, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        @endforeach
    </div>
    <div class="pagination-container">
        {{ $orders->onEachSide(1)->links('components.client-pagination') }}
    </div>
@endif
@endsection

