<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de ventes - Parasel-Bio</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .report-header { margin-bottom: 22px; padding-bottom: 14px; border-bottom: 1px solid #ddd; text-align: center; }
        .report-logo-wrap { display: block; margin-bottom: 6px; }
        .report-logo { max-height: 62px; width: auto; display: block; margin: 0 auto; vertical-align: bottom; }
        .report-tagline { font-size: 14px; font-weight: bold; color: #374151; margin: 0; padding-top: 0; text-align: center; letter-spacing: 0.03em; line-height: 1.4; }
        h1 { font-size: 16px; font-weight: bold; margin: 0 0 6px 0; }
        .meta { color: #666; margin-bottom: 18px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .stat { margin: 12px 0; padding: 8px; background: #f9f9f9; }
        .stat-label { font-weight: bold; }
        .section { margin-top: 24px; }
    </style>
</head>
<body>
    <div class="report-header">
        @php
            $logoFile = null;
            foreach (['Parasel-BIO-adapted.svg', 'Parasel-BIO-adapted.png'] as $name) {
                $p = public_path('images/' . $name);
                if (file_exists($p)) { $logoFile = $p; break; }
            }
        @endphp
        <div class="report-logo-wrap">
        @if($logoFile)
            <img src="{{ $logoFile }}" alt="Parasel-BIO" class="report-logo" />
        @else
            <div style="font-size: 22px; font-weight: bold; text-align: center;">
                <span style="color: #1a1a1a;">Para</span><span style="color: #ea580c;">s</span><span style="color: #1a1a1a;">el-</span><span style="color: #15803d;">BIO</span>
            </div>
        @endif
        </div>
        <p class="report-tagline">Épices Tradition</p>
    </div>
    <h1>Rapport de ventes</h1>
    <p class="meta">Période : {{ $start->format('d/m/Y') }} → {{ $end->format('d/m/Y') }} ({{ $period === 'day' ? 'Jour' : ($period === 'week' ? 'Semaine' : 'Mois') }})</p>

    <div class="stat"><span class="stat-label">Nombre total de commandes :</span> {{ $totalOrders }}</div>
    <div class="stat"><span class="stat-label">Chiffre d'affaires :</span> {{ number_format($revenue, 0, ',', ' ') }} FCFA</div>
    <div class="stat"><span class="stat-label">Commandes livrées :</span> {{ $delivered }}</div>
    <div class="stat"><span class="stat-label">Commandes annulées :</span> {{ $cancelled }}</div>

    <div class="section">
        <h2>Produits les plus vendus</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts ?? [] as $row)
                    <tr>
                        <td>{{ $row->product_name ?? '—' }}</td>
                        <td>{{ $row->total_quantity ?? 0 }}</td>
                        <td>{{ number_format((float) ($row->total_amount ?? 0), 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p style="margin-top: 24px; font-size: 10px; color: #999;">Généré le {{ now()->format('d/m/Y H:i') }} — Parasel Bio Épices Tradition</p>
</body>
</html>
