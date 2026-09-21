<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Message;
use App\Models\Experience;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'orders_payee' => Order::whereIn('status', ['payee_en_ligne', 'payee'])->count(),
            // En cours = commandes payées en ligne ou paiement à la livraison, en attente de traitement admin
            'orders_en_cours' => Order::whereIn('status', ['payee_en_ligne', 'a_la_livraison'])->count(),
            'orders_livree' => Order::whereIn('status', ['livree_payee', 'livree'])->count(),
            'orders_annulee' => Order::where('status', 'annulee')->count(),
            'messages' => Message::count(),
            'experiences' => Experience::count(),
            'experiences_published' => Experience::where('is_published', true)->count(),
        ];
        
        // Produits en stock bas (≤ 20 % du stock initial)
        $lowStockProducts = Product::whereNotNull('initial_stock')
            ->where('initial_stock', '>', 0)
            ->whereColumn('stock', '<=', DB::raw('initial_stock * 0.2'))
            ->orderBy('stock')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'lowStockProducts'));
    }
}

