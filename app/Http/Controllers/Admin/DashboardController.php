<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Message;
use App\Models\Experience;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'orders_en_cours' => Order::where('status', 'en_cours')->count(),
            'orders_livree' => Order::where('status', 'livree')->count(),
            'orders_annulee' => Order::where('status', 'annulee')->count(),
            'messages' => Message::count(),
            'experiences' => Experience::count(),
            'experiences_published' => Experience::where('is_published', true)->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}

