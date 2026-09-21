@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Comparaison par période</h1>
        <p class="admin-page-subtitle">Évolution du CA et du nombre de commandes entre deux périodes</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Retour au tableau de bord
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Rapports de ventes
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.reports.compare') }}" class="card admin-filters-form">
    <h3 style="margin-bottom: 16px;">Période 1</h3>
    <div class="report-filters form-row">
        <div class="form-group">
            <label>Du</label>
            <input type="date" name="period1_from" value="{{ $period1Start->format('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label>Au</label>
            <input type="date" name="period1_to" value="{{ $period1End->format('Y-m-d') }}">
        </div>
    </div>
    <h3 style="margin: 20px 0 16px;">Période 2</h3>
    <div class="report-filters form-row">
        <div class="form-group">
            <label>Du</label>
            <input type="date" name="period2_from" value="{{ $period2Start->format('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label>Au</label>
            <input type="date" name="period2_to" value="{{ $period2End->format('Y-m-d') }}">
        </div>
    </div>
    <div class="form-actions" style="margin-top: 16px;">
        <button type="submit" class="btn-admin success"><i class="fa-solid fa-code-compare"></i> Comparer</button>
    </div>
</form>

<div class="compare-grid">
    <div class="compare-card">
        <h3>Chiffre d'affaires</h3>
        <div class="compare-row">
            <span>Période 1 ({{ $period1Start->format('d/m/Y') }} - {{ $period1End->format('d/m/Y') }})</span>
            <strong>{{ number_format($revenue1, 0, ',', ' ') }} FCFA</strong>
        </div>
        <div class="compare-row">
            <span>Période 2 ({{ $period2Start->format('d/m/Y') }} - {{ $period2End->format('d/m/Y') }})</span>
            <strong>{{ number_format($revenue2, 0, ',', ' ') }} FCFA</strong>
        </div>
        <div class="compare-variation {{ $revenueVariation >= 0 ? 'positive' : 'negative' }}">
            Variation : {{ $revenueVariation >= 0 ? '+' : '' }}{{ number_format($revenueVariation, 0, ',', ' ') }} FCFA
            ({{ $revenueGrowthRate >= 0 ? '+' : '' }}{{ number_format($revenueGrowthRate, 1, ',', ' ') }} %)
        </div>
    </div>
    <div class="compare-card">
        <h3>Nombre de commandes</h3>
        <div class="compare-row">
            <span>Période 1</span>
            <strong>{{ $count1 }}</strong>
        </div>
        <div class="compare-row">
            <span>Période 2</span>
            <strong>{{ $count2 }}</strong>
        </div>
        <div class="compare-variation {{ $orderVariation >= 0 ? 'positive' : 'negative' }}">
            Variation : {{ $orderVariation >= 0 ? '+' : '' }}{{ $orderVariation }} commandes
            ({{ $orderGrowthRate >= 0 ? '+' : '' }}{{ number_format($orderGrowthRate, 1, ',', ' ') }} %)
        </div>
    </div>
</div>
@endsection
