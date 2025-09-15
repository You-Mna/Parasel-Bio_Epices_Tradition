<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function account(): View
    {
        $user = Auth::user();
        $lastOrder = $user->orders()->with('items.product')->latest()->first();
        return view('client.account', compact('user', 'lastOrder'));
    }

    public function orders(): View
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return view('client.orders', compact('orders'));
    }
}

