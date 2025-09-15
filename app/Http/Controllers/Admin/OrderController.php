<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:en_cours,livree,annulee',
        ]);
        
        $oldStatus = $order->status;
        $newStatus = $data['status'];
        
        // Mettre à jour le statut
        $order->update(['status' => $newStatus]);
        
        // Envoyer la notification au client
        if ($order->user && $oldStatus !== $newStatus) {
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $newStatus));
        }
        
        return back()->with('success', 'Statut mis à jour et notification envoyée au client');
    }
}

