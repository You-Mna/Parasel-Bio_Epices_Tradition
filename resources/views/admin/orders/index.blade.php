@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Gestion des Commandes</h1>
        <p class="admin-page-subtitle">Suivez et gérez toutes les commandes de vos clients</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Tableau de bord
        </a>
    </div>
</div>

@if($orders->count() > 0)
    <table style="table-layout: fixed; width: 100%;">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th style="width: 150px;">Client</th>
                <th style="width: 200px;">Email</th>
                <th style="width: 120px;">Total</th>
                <th style="width: 100px;">Statut</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td style="text-align: center;">#{{ $order->id }}</td>
                    <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $order->user ? $order->user->name : 'N/A' }}</td>
                    <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $order->user ? $order->user->email : 'N/A' }}</td>
                    <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                    <td>
                        <span class="badge {{ $order->status === 'en_attente' ? 'warning' : ($order->status === 'livree' ? 'success' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px;">
                                <option value="en_attente" {{ $order->status === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ $order->status === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="livree" {{ $order->status === 'livree' ? 'selected' : '' }}>Livrée</option>
                            </select>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 16px;">{{ $orders->links() }}</div>
@else
    <div class="card" style="text-align: center; padding: 40px;">
        <h3>Aucune commande</h3>
        <p>Aucune commande n'a été passée pour le moment.</p>
    </div>
@endif
@endsection