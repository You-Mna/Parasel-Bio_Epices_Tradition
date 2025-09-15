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
            
            $dbCartItems = CartItem::with(['product', 'variant'])
                ->where('user_id', Auth::id())
                ->get();
            
            foreach ($dbCartItems as $item) {
                $cartItems[] = [
                    'product' => $item->product,
                    'variant' => $item->variant,
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
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);
        
        $qty = $validated['quantity'] ?? 1;
        $variantId = $validated['variant_id'] ?? null;
        
        // Si le produit a des variantes, une variante doit être sélectionnée
        if ($product->has_variants && !$variantId) {
            return back()->with('error', 'Veuillez sélectionner une taille pour ce produit.');
        }
        
        // Récupérer la variante ou utiliser le produit de base
        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if (!$variant || $variant->product_id !== $product->id) {
                return back()->with('error', 'Variante invalide.');
            }
            
            // Vérifier le stock de la variante
            if ($variant->stock <= 0) {
                return back()->with('error', 'Cette taille est actuellement en rupture de stock.');
            }
            
            $price = $variant->price;
            $size = $variant->size;
            $stock = $variant->stock;
        } else {
            // Produit sans variantes
            if ($product->stock <= 0) {
                return back()->with('error', 'Ce produit est actuellement en rupture de stock.');
            }
            
            $price = $product->price;
            $size = '';
            $stock = $product->stock;
        }
        
        // Créer une clé unique pour cette variante
        $cartKey = $product->id . '_' . ($variantId ?? 'base');
        
        // Vérifier si l'item existe déjà dans le panier
        $existingItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($existingItem) {
            // Vérifier si la quantité totale ne dépasse pas le stock
            if (($existingItem->quantity + $qty) > $stock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $stock . ' unités restantes).');
            }
            
            // Mettre à jour la quantité
            $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
        } else {
            // Vérifier si la quantité ne dépasse pas le stock
            if ($qty > $stock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $stock . ' unités restantes).');
            }
            
            // Créer un nouvel item dans le panier
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'quantity' => $qty,
                'variant_price' => $price,
                'variant_size' => $size,
                'cart_key' => $cartKey
            ]);
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
            $maxStock = $cartItem->variant ? $cartItem->variant->stock : $cartItem->product->stock;
            
            if ($validated['quantity'] > $maxStock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $maxStock . ' unités restantes).');
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
        
        $cartItems = CartItem::with(['product', 'variant'])
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
            'payment_method' => 'required|in:cash,mobile_money',
            'delivery_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $cartItems = CartItem::with(['product', 'variant'])
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
                'variant_id' => $cartItem->variant_id,
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


