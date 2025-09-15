<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        if (!Auth::check()) {
            // Pour les utilisateurs non connectés, utiliser la session
        $cart = $request->session()->get('cart', []);
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $cartKey => $cartData) {
            if (is_array($cartData) && isset($cartData['product_id'])) {
                $product = Product::where('status', 'active')->find($cartData['product_id']);
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
        } else {
            // Pour les utilisateurs connectés, utiliser la base de données
            $cartItems = [];
            $total = 0;
            
            $dbCartItems = CartItem::with('product')
                ->where('user_id', Auth::id())
                ->get();
            
            foreach ($dbCartItems as $item) {
                $cartItems[] = [
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'variant_price' => $item->variant_price,
                    'variant_size' => $item->variant_size,
                    'cart_key' => $item->cart_key
                ];
                $total += $item->variant_price * $item->quantity;
            }
        }
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login.show')->with('message', 'Veuillez vous connecter pour ajouter des produits au panier.');
        }
        
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variant_price' => 'nullable|numeric|min:0',
            'variant_size' => 'nullable|string'
        ]);
        
        $qty = $validated['quantity'] ?? 1;
        $variantPrice = $validated['variant_price'] ?? $product->price;
        $variantSize = $validated['variant_size'] ?? '';
        
        
        
        
        // Déterminer le stock disponible (variante ou produit principal)
        $availableStock = $product->stock;
        if ($product->variants && is_array($product->variants) && $variantSize) {
            foreach ($product->variants as $variant) {
                if ($variant['size'] === $variantSize) {
                    $availableStock = $variant['stock'];
                    break;
                }
            }
        }
        
        
        
        // Créer une clé unique pour cette variante
        $cartKey = $product->id . '_' . $variantPrice . '_' . $variantSize;
        
        // Utiliser updateOrCreate pour éviter les doublons et gérer les conditions de course
        $existingItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($existingItem) {
            // Vérifier si la quantité totale ne dépasse pas le stock
            if (($existingItem->quantity + $qty) > $availableStock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).');
            }
            
            // Mettre à jour la quantité
            $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
        } else {
            // Vérifier si la quantité ne dépasse pas le stock
            if ($qty > $availableStock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).');
            }
            
            // Créer un nouvel item dans le panier
            try {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'variant_price' => $variantPrice,
                    'variant_size' => $variantSize,
                    'cart_key' => $cartKey
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Si l'item existe déjà (condition de course), le récupérer et mettre à jour
                $existingItem = CartItem::where('user_id', Auth::id())
                    ->where('cart_key', $cartKey)
                    ->first();
                
                if ($existingItem) {
                    if (($existingItem->quantity + $qty) > $availableStock) {
                        return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).');
                    }
                    $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
                }
            }
        }
        
        
        return back()->with('success', 'Produit ajouté au panier avec succès!');
    }

    public function remove(Request $request, string $cartKey): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }
        
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($cartItem) {
            $cartItem->delete();
        return back()->with('success', 'Produit retiré du panier.');
    }

        return back()->with('error', 'Produit non trouvé dans le panier.');
    }

    public function updateQuantity(Request $request, string $cartKey): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }
        
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($cartItem) {
            // Vérifier le stock disponible
            if ($validated['quantity'] > $cartItem->product->stock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $cartItem->product->stock . ' unités restantes).');
            }
            
            $cartItem->update(['quantity' => $validated['quantity']]);
            return back()->with('success', 'Quantité mise à jour.');
        }
        
        return back()->with('error', 'Produit non trouvé dans le panier.');
    }

    public function checkout(): View
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }
        
        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        
        $total = $cartItems->sum(function ($item) {
            return $item->variant_price * $item->quantity;
        });
        
        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function processOrder(Request $request): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,mtn_momo,moov_money,celtiis_money',
            'delivery_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        
        $total = $cartItems->sum(function ($item) {
            return $item->variant_price * $item->quantity;
        });
        
        // Créer la commande
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $total,
            'status' => 'en_attente',
            'payment_method' => $validated['payment_method'],
            'delivery_address' => $validated['delivery_address'],
            'notes' => $validated['notes']
        ]);
        
        // Créer les articles de commande
        foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->variant_price,
                'line_total' => $cartItem->variant_price * $cartItem->quantity
            ]);
        }
        
        // Vider le panier seulement pour les paiements en espèces
        if ($validated['payment_method'] === 'cash') {
            CartItem::where('user_id', Auth::id())->delete();
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'message' => 'Commande créée avec succès!'
            ]);
        }
        
        return redirect()->route('orders.show', $order)->with('success', 'Commande créée avec succès!');
    }
}