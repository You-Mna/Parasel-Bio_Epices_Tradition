<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre commande</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }
        .status-change {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #059669;
        }
        .order-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background-color: #059669;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Parasel-Bio</div>
            <p>Épices et Tradition</p>
        </div>

        <h2>Bonjour {{ $user->first_name }},</h2>
        
        <p>Nous vous informons que le statut de votre commande a été mis à jour.</p>

        <div class="status-change">
            <h3>Changement de statut</h3>
            <p><strong>Commande #{{ $order->id }}</strong></p>
            <p>Ancien statut : <span style="color: #666;">{{ $oldStatusLabel }}</span></p>
            <p>Nouveau statut : <span style="color: #059669; font-weight: bold;">{{ $newStatusLabel }}</span></p>
        </div>

        <div class="order-details">
            <h3>Détails de la commande</h3>
            <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Total :</strong> {{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
            <p><strong>Méthode de paiement :</strong> {{ $order->payment_method }}</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/mes-commandes') }}" class="btn">Voir mes commandes</a>
        </div>

        <p>Merci pour votre confiance et votre patience !</p>

        <div class="footer">
            <p>L'équipe Parasel-Bio</p>
            <p>Épices et Tradition - Assaisonnement aux 17 épices et légumes naturels bio</p>
        </div>
    </div>
</body>
</html>
