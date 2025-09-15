<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CartController as BaseCartController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        // Utiliser la même logique que le contrôleur de base pour les utilisateurs connectés
        if (\Illuminate\Support\Facades\Auth::check()) {
            // Pour les utilisateurs connectés, utiliser la base de données
            $cartItems = \App\Models\CartItem::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->with('product')
                ->get();
            
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item->variant_price * $item->quantity;
            }
        } else {
            // Pour les utilisateurs non connectés, utiliser la session
            $cart = $request->session()->get('cart', []);
            $cartItems = [];
            $total = 0;
            
            foreach ($cart as $cartKey => $cartData) {
                if (is_array($cartData) && isset($cartData['product_id'])) {
                    $product = \App\Models\Product::find($cartData['product_id']);
                    if ($product) {
                        $variantPrice = $cartData['variant_price'] ?? $product->price;
                        $variantSize = $cartData['variant_size'] ?? '';
                        
                        $cartItems[] = [
                            'product' => $product,
                            'quantity' => $cartData['quantity'],
                            'variant_price' => $variantPrice,
                            'variant_size' => $variantSize,
                            'cart_key' => $cartKey
                        ];
                        $total += $variantPrice * $cartData['quantity'];
                    }
                }
            }
        }
        
        return view('admin.cart', compact('cartItems', 'total'));
    }
}
