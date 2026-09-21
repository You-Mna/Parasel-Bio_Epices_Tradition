@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Gestion des Commandes</h1>
        <p class="admin-page-subtitle">Suivez et gérez toutes les commandes de vos clients</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.orders.create') }}" class="btn-admin success">
            <i class="fa-solid fa-plus"></i> Commande hors ligne
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Tableau de bord
        </a>
    </div>
</div>

@php
    $hasOnline = isset($onlineOrders) && $onlineOrders->count() > 0;
    $hasOffline = isset($offlineOrders) && $offlineOrders->count() > 0;
@endphp

@if(!$hasOnline && !$hasOffline)
    <div class="card" style="text-align: center; padding: 40px;">
        <h3>Aucune commande</h3>
        <p>Aucune commande n'a été passée pour le moment.</p>
    </div>
@else

    @if($hasOnline)
        <h2 class="admin-section-title" style="margin-top:12px;margin-bottom:8px;font-size:18px;font-weight:600;color:#111827;">
            Commandes en ligne
        </h2>
        <div class="admin-table-wrapper">
        <table class="admin-table admin-table--orders">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($onlineOrders as $order)
                    @php
                        $badgeClass = match($order->status) {
                            'payee_en_ligne' => 'success',
                            'payee' => 'success',
                            'a_la_livraison' => 'warning',
                            'en_cours' => 'warning',
                            'livree_payee' => 'success',
                            'livree' => 'success',
                            'annulee' => 'danger',
                            default => 'secondary',
                        };
                        $statusLabel = match($order->status) {
                            'payee_en_ligne' => 'Payée en ligne',
                            'a_la_livraison' => 'Paiement à la livraison',
                            'en_cours' => 'En attente paiement',
                            'livree_payee' => 'Livrée payée',
                            'livree' => 'Livrée payée',
                            'annulee' => 'Annulée',
                            'payee' => 'Payée en ligne',
                            default => ucfirst(str_replace('_', ' ', $order->status)),
                        };
                    @endphp
                    <tr>
                        <td class="col-id" data-label="ID">
                            <div class="cell-main">#{{ $order->id }}</div>
                        </td>
                        <td class="col-client" data-label="Client">
                            <div class="cell-main">{{ $order->client_name ?? '—' }}</div>
                        </td>
                        <td class="col-phone" data-label="Téléphone">
                            <div class="cell-main">{{ $order->client_phone ?? '—' }}</div>
                        </td>
                        <td class="col-email" data-label="Email">
                            <div class="cell-main">{{ $order->client_email ?? '—' }}</div>
                        </td>
                        <td class="col-total" data-label="Total">
                            <div class="cell-main">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="col-status" data-label="Statut">
                            @if(in_array($order->status, ['payee_en_ligne', 'payee', 'a_la_livraison'], true))
                                <span class="admin-status-text">{{ $statusLabel }}</span>
                            @else
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            @endif
                        </td>
                        <td class="col-date" data-label="Date">
                            <div class="cell-main">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="col-actions" data-label="Actions">
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                                @csrf
                                @method('PATCH')
                                @php
                                    $isFinalStatus = in_array($order->status, ['livree_payee', 'livree', 'annulee'], true);
                                @endphp
                                <select name="status" onchange="this.form.submit()" class="admin-status-select">
                                    @unless($isFinalStatus)
                                        <option value="" selected disabled>Changer le statut…</option>
                                    @endunless
                                    <option value="livree_payee" {{ in_array($order->status, ['livree_payee', 'livree']) ? 'selected' : '' }}>Livrée payée</option>
                                    <option value="annulee" {{ $order->status === 'annulee' ? 'selected' : '' }}>Annulée</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="admin-table-pagination">
            {{ $onlineOrders->links('vendor.pagination.admin') }}
        </div>
    @endif

    @if($hasOffline)
        <h2 class="admin-section-title" style="margin-top:24px;margin-bottom:8px;font-size:18px;font-weight:600;color:#111827;">
            Commandes hors ligne
        </h2>
        <div class="admin-table-wrapper">
        <table class="admin-table admin-table--orders">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offlineOrders as $order)
                    @php
                        // Pour les commandes hors ligne : seulement deux états lisibles : Payée / Annulée
                        $badgeClass = $order->status === 'annulee' ? 'danger' : 'success';
                        $statusLabel = $order->status === 'annulee' ? 'Annulée' : 'Payée';
                    @endphp
                    <tr>
                        <td class="col-id" data-label="ID">
                            <div class="cell-main">#{{ $order->id }}</div>
                        </td>
                        <td class="col-client" data-label="Client">
                            <div class="cell-main">{{ $order->client_name ?? '—' }}</div>
                        </td>
                        <td class="col-phone" data-label="Téléphone">
                            <div class="cell-main">{{ $order->client_phone ?? '—' }}</div>
                        </td>
                        <td class="col-email" data-label="Email">
                            <div class="cell-main">{{ $order->client_email ?? '—' }}</div>
                        </td>
                        <td class="col-total" data-label="Total">
                            <div class="cell-main">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="col-status" data-label="Statut">
                            @if(in_array($order->status, ['payee_en_ligne', 'payee', 'a_la_livraison'], true))
                                <span class="admin-status-text">{{ $statusLabel }}</span>
                            @else
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            @endif
                        </td>
                        <td class="col-date" data-label="Date">
                            <div class="cell-main">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="col-actions" data-label="Actions">
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                                @csrf
                                @method('PATCH')
                                @php
                                    $isFinalStatus = in_array($order->status, ['livree_payee', 'livree', 'annulee'], true);
                                @endphp
                                <select name="status" onchange="this.form.submit()" class="admin-status-select">
                                    @unless($isFinalStatus)
                                        <option value="" selected disabled>Changer le statut…</option>
                                    @endunless
                                    <option value="livree_payee" {{ in_array($order->status, ['livree_payee', 'livree']) ? 'selected' : '' }}>Payée</option>
                                    <option value="annulee" {{ $order->status === 'annulee' ? 'selected' : '' }}>Annulée</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="admin-table-pagination">
            {{ $offlineOrders->links('vendor.pagination.admin') }}
        </div>
    @endif

@endif
@endsection