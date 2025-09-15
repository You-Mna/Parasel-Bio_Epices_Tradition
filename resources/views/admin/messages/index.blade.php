@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Formulaire de Contact</h1>
        <p class="admin-page-subtitle">Consultez les messages de vos clients</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Tableau de bord
        </a>
    </div>
</div>
<table>
    <thead><tr><th>Client</th><th>Email</th><th>Téléphone</th><th>Message</th><th>Date</th></tr></thead>
    <tbody>
    @foreach($messages as $m)
        <tr>
            <td>{{ $m->name }}</td>
            <td>{{ $m->email }}</td>
            <td>{{ $m->phone }}</td>
            <td>{{ $m->content }}</td>
            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div style="margin-top:16px;">{{ $messages->links() }}</div>
@endsection

