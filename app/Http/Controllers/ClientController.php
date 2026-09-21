<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Commandes affichées uniquement si au moins un mode de paiement a été validé :
     * - Paiement à la livraison : validé dès la création (choix client).
     * - Paiement en ligne : validé lorsque FedaPay confirme (statut différent de en_cours).
     */
    private function validatedOrdersQuery()
    {
        return Auth::user()->orders()->where(function ($q) {
            $q->where('payment_method', 'cash') // à la livraison = validé à la création
                ->orWhere(function ($q2) {
                    $q2->where('payment_method', 'fedapay')
                        ->where('status', '!=', 'en_cours'); // en ligne = affichée seulement après confirmation paiement
                });
        });
    }

    public function account(): View
    {
        $user = Auth::user();
        $ordersQuery = $this->validatedOrdersQuery();
        $lastOrder = $ordersQuery->with('items.product')->latest()->first();
        // Numéro de la dernière commande pour ce client (1 = 1ère, 2 = 2e, etc.)
        $lastOrderNumber = $lastOrder ? $ordersQuery->count() : 0;
        return view('client.account', compact('user', 'lastOrder', 'lastOrderNumber'));
    }

    public function orders(): View
    {
        $orders = $this->validatedOrdersQuery()
            ->with('items.product')
            ->latest()
            ->paginate(9);
        return view('client.orders', compact('orders'));
    }
}

