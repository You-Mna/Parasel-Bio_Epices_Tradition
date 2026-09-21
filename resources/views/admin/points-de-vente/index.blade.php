@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Points de vente</h1>
        <p class="admin-page-subtitle">Gérez les points de vente pour les expéditions</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.shipments.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-truck"></i> Expéditions
        </a>
        <a href="{{ route('admin.points-de-vente.create') }}" class="btn-admin success">
            <i class="fa-solid fa-plus"></i> Nouveau point de vente
        </a>
    </div>
</div>

@if($points->count() > 0)
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
                    <th>Adresse</th>
                    <th>Email</th>
                    <th>Code</th>
                </tr>
            </thead>
            <tbody>
                @foreach($points as $point)
                    <tr>
                        <td>{{ $point->name }}</td>
                        <td>{{ $point->phone ?? '—' }}</td>
                        <td>{{ $point->city ?? '—' }}</td>
                        <td>{{ $point->address ?? '—' }}</td>
                        <td>{{ $point->email ?? '—' }}</td>
                        <td>{{ $point->code ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 16px;">{{ $points->links() }}</div>
@else
    <div class="card" style="text-align: center; padding: 40px;">
        <h3>Aucun point de vente</h3>
        <p>Créez un point de vente pour pouvoir créer des expéditions.</p>
        <a href="{{ route('admin.points-de-vente.create') }}" class="btn-admin success" style="margin-top: 12px;">
            <i class="fa-solid fa-plus"></i> Nouveau point de vente
        </a>
    </div>
@endif
@endsection
