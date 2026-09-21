@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Rapports de ventes</h1>
        <p class="admin-page-subtitle">Chiffre d'affaires, commandes et produits les plus vendus</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Retour au tableau de bord
        </a>
        <a href="{{ route('admin.reports.compare') }}" class="btn-admin secondary">
            <i class="fa-solid fa-code-compare"></i> Comparer deux périodes
        </a>
        <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="btn-admin success">
            <i class="fa-solid fa-file-pdf"></i> Exporter en PDF
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.reports.index') }}" class="card admin-filters-form">
    <div class="report-filters">
        <div class="form-group">
            <label>Période</label>
            <select name="period" id="period" onchange="toggleCustomDates()">
                <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Cette semaine</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Période personnalisée</option>
            </select>
        </div>
        <div class="form-group custom-dates" id="custom-dates" style="{{ $period === 'custom' ? '' : 'display:none;' }}">
            <label>Du</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}">
        </div>
        <div class="form-group custom-dates" id="custom-dates-to" style="{{ $period === 'custom' ? '' : 'display:none;' }}">
            <label>Au</label>
            <input type="date" name="date_to" value="{{ $dateTo }}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-admin success"><i class="fa-solid fa-filter"></i> Appliquer</button>
        </div>
    </div>
</form>

<p class="report-period-info">Période : <strong>{{ $start->format('d/m/Y') }}</strong> → <strong>{{ $end->format('d/m/Y') }}</strong></p>

<div class="report-stats-grid">
    <div class="report-stat-card">
        <div class="report-stat-icon"><i class="fa-solid fa-shopping-cart"></i></div>
        <h3>Nombre total de commandes</h3>
        <div class="report-stat-value">{{ $totalOrders }}</div>
    </div>
    <div class="report-stat-card highlight">
        <div class="report-stat-icon"><i class="fa-solid fa-coins"></i></div>
        <h3>Chiffre d'affaires</h3>
        <div class="report-stat-value">{{ number_format($revenue, 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="report-stat-card">
        <div class="report-stat-icon success"><i class="fa-solid fa-check-circle"></i></div>
        <h3>Commandes livrées</h3>
        <div class="report-stat-value">{{ $delivered }}</div>
    </div>
    <div class="report-stat-card">
        <div class="report-stat-icon danger"><i class="fa-solid fa-times-circle"></i></div>
        <h3>Commandes annulées</h3>
        <div class="report-stat-value">{{ $cancelled }}</div>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <h2 class="report-section-title">Produits les plus vendus</h2>
    @if($topProducts->count() > 0)
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité vendue</th>
                        <th>Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $row)
                        <tr>
                            <td>{{ $row->product->name ?? 'Produit #' . $row->product_id }}</td>
                            <td>{{ $row->total_quantity }}</td>
                            <td>{{ number_format($row->total_amount ?? 0, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>Aucune vente sur cette période.</p>
    @endif
</div>

<script>
function toggleCustomDates() {
    var period = document.getElementById('period').value;
    var els = document.querySelectorAll('.custom-dates');
    els.forEach(function(el) {
        el.style.display = period === 'custom' ? '' : 'none';
    });
    var elTo = document.getElementById('custom-dates-to');
    if (elTo) elTo.style.display = period === 'custom' ? '' : 'none';
}
</script>
@endsection
