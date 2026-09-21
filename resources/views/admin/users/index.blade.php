@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Carnet d'adresses clients</h1>
        <p class="admin-page-subtitle">Liste des comptes clients</p>
    </div>
</div>

@if($users->count() > 0)
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Nombre de commandes</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td data-label="ID">#{{ $user->id }}</td>
                        <td data-label="Nom">{{ $user->last_name ?? '-' }}</td>
                        <td data-label="Prénom">{{ $user->first_name ?? '-' }}</td>
                        <td data-label="Email">{{ $user->email }}</td>
                        <td data-label="Téléphone">{{ $user->phone ?? '-' }}</td>
                        <td data-label="Nombre de commandes">{{ $user->orders_count }}</td>
                        <td data-label="Créé le">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="admin-table-pagination">
        {{ $users->links('vendor.pagination.admin') }}
    </div>
@else
    <div class="card" style="text-align:center;padding:40px;">
        <h3>Aucun client</h3>
        <p>Aucun compte client n'a encore été créé.</p>
    </div>
@endif
@endsection
