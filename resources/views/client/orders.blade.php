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
            <div class="order-card">
                <div class="order-header">
                    <h3>Commande n° {{ $order->id }}</h3>
                    @php
                        $badgeClass = $order->status === 'livree' ? 'success' : ($order->status === 'en_cours' ? 'warning' : 'danger');
                        $label = $order->status === 'livree' ? 'Livrée' : ($order->status === 'en_cours' ? 'En cours' : 'Annulée');
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
                    <strong>Total : {{ number_format($order->total, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        @endforeach
    </div>
    <div style="margin-top:16px;">{{ $orders->links() }}</div>
@endif
@endsection

