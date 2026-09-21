@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Gestion des expéditions</h1>
        <p class="admin-page-subtitle">Expéditions vers les points de vente</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Retour au tableau de bord
        </a>
        <a href="{{ route('admin.points-de-vente.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-store"></i> Points de vente
        </a>
        <a href="{{ route('admin.shipments.create') }}" class="btn-admin success">
            <i class="fa-solid fa-plus"></i> Nouvelle expédition
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.shipments.index') }}" class="admin-filters admin-filters--shipments">
    <div class="filter-group">
        <select name="status" class="filter-select" onchange="this.form.submit()" aria-label="Statuts">
            <option value="">{{ __('Statuts : Tous') }}</option>
            <option value="expediee" {{ request('status') === 'expediee' ? 'selected' : '' }}>Expédiée</option>
            <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
            <option value="annulee" {{ request('status') === 'annulee' ? 'selected' : '' }}>Annulée</option>
        </select>
    </div>
</form>

@if($shipments->count() > 0)
    <div class="admin-table-wrapper admin-table-wrapper--shipments">
        <table class="admin-table admin-table--shipments">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-point">Point de vente</th>
                    <th class="col-statut">Statut</th>
                    <th class="col-produits">Produits / Qté</th>
                    <th class="col-total">Total</th>
                    <th class="col-dates">Date d'expédition</th>
                    <th class="col-suivi">Référence</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $s)
                    <tr>
                        <td class="col-id">#{{ $s->id }}</td>
                        <td class="col-point">
                            @if($s->pointDeVente)
                                {{ $s->pointDeVente->name }}{{ $s->pointDeVente->code ? ' (' . $s->pointDeVente->code . ')' : '' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="col-statut">
                            <span class="badge badge-shipment-{{ $s->status }}">{{ $s->status_label }}</span>
                        </td>
                        @php
                            $produitsLine = $s->items->count() > 0
                                ? collect($s->items)->map(function ($item) {
                                    return ($item->product->name ?? 'N/A') . ($item->variant_size ? ' (' . $item->variant_size . ')' : '') . ' × ' . $item->quantity;
                                  })->join(', ')
                                : '—';
                        @endphp
                        <td class="col-produits">
                            {{ $produitsLine }}
                        </td>
                        <td class="col-total">
                            @php $total = $s->estimated_total; @endphp
                            {{ $total > 0 ? number_format($total, 0, ',', ' ') . ' FCFA' : '—' }}
                        </td>
                        <td class="col-dates">
                            {{ $s->shipped_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="col-suivi">{{ $s->tracking_reference ? Str::limit($s->tracking_reference, 12) : '—' }}</td>
                        <td class="col-actions admin-table-actions">
                            @if(in_array($s->status, [\App\Models\Shipment::STATUS_EXPEDIEE, \App\Models\Shipment::STATUS_ANNULEE], true))
                                <button class="btn-admin small" style="opacity:0.5;cursor:not-allowed;" disabled>
                                    <i class="fa-solid fa-edit"></i> Modifier
                                </button>
                            @else
                                <a href="{{ route('admin.shipments.edit', $s) }}" class="btn-admin small">
                                    <i class="fa-solid fa-edit"></i> Modifier
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 16px;">{{ $shipments->links() }}</div>
@else
    <div class="card" style="text-align: center; padding: 40px;">
        <h3>Aucune expédition</h3>
        <p>Créez une expédition en sélectionnant un point de vente et les produits/quantités à envoyer.</p>
        <a href="{{ route('admin.points-de-vente.index') }}" class="btn-admin secondary" style="margin-top: 8px;">Gérer les points de vente</a>
        <a href="{{ route('admin.shipments.create') }}" class="btn-admin success" style="margin-top: 12px;">
            <i class="fa-solid fa-plus"></i> Nouvelle expédition
        </a>
    </div>
@endif
@endsection
