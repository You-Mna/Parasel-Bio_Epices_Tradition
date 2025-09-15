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
            
            foreach ($dbCartItems as $cartItem) {
                if ($cartItem->product && $cartItem->product->status === 'active') {
                    $cartItems[] = [
                        'cart_key' => $cartItem->cart_key,
                        'product' => $cartItem->product,
                        'quantity' => $cartItem->quantity,
                        'variant_price' => $cartItem->variant_price ?? $cartItem->product->price,
                        'variant_size' => $cartItem->variant_size ?? ''
                    ];
                    
                    $total += ($cartItem->variant_price ?? $cartItem->product->price) * $cartItem->quantity;
                }
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
        
        // Vérifier si le produit est en stock
        if ($product->stock <= 0) {
            return back()->with('error', 'Ce produit est actuellement en rupture de stock.');
        }
        
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variant_price' => 'nullable|numeric|min:0',
            'variant_size' => 'nullable|string'
        ]);
        $qty = $validated['quantity'] ?? 1;
        $variantPrice = $validated['variant_price'] ?? $product->price;
        $variantSize = $validated['variant_size'] ?? '';
        
        // Debug: Log les données reçues
        \Log::info('Ajout au panier', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant_price' => $variantPrice,
            'variant_size' => $variantSize,
            'quantity' => $qty,
            'request_data' => $request->all()
        ]);
        
        // Créer une clé unique pour cette variante
        $cartKey = $product->id . '_' . $variantPrice . '_' . $variantSize;
        
        // Vérifier si l'item existe déjà dans le panier
        $existingItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($existingItem) {
            // Vérifier si la quantité totale ne dépasse pas le stock
            if (($existingItem->quantity + $qty) > $product->stock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $product->stock . ' unités restantes).');
            }
            
            // Mettre à jour la quantité
            $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
        } else {
            // Vérifier si la quantité ne dépasse pas le stock
            if ($qty > $product->stock) {
                return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $product->stock . ' unités restantes).');
            }
            
            // Créer un nouvel item
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $qty,
                'variant_price' => $variantPrice,
                'variant_size' => $variantSize,
                'cart_key' => $cartKey
            ]);
        }
        
        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function remove(Request $request, $cartKey): RedirectResponse
    {
        if (Auth::check()) {
            // Pour les utilisateurs connectés, supprimer de la base de données
            CartItem::where('user_id', Auth::id())
                ->where('cart_key', $cartKey)
                ->delete();
        } else {
            // Pour les utilisateurs non connectés, supprimer de la session
            $cart = $request->session()->get('cart', []);
            unset($cart[$cartKey]);
            $request->session()->put('cart', $cart);
        }
        
        return back()->with('success', 'Produit retiré du panier.');
    }

    public function updateQuantity(Request $request, $cartKey): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);
        
        if (Auth::check()) {
            // Pour les utilisateurs connectés, mettre à jour dans la base de données
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('cart_key', $cartKey)
                ->first();
            
            if ($cartItem) {
                $product = $cartItem->product;
                
                // Vérifier si le produit est toujours en stock
                if ($product && $product->stock <= 0) {
                    return back()->with('error', 'Ce produit est maintenant en rupture de stock.');
                }
                
                // Vérifier si la quantité demandée ne dépasse pas le stock disponible
                if ($product && $validated['quantity'] > $product->stock) {
                    return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $product->stock . ' unités restantes).');
                }
                
                $cartItem->update(['quantity' => $validated['quantity']]);
            }
        } else {
            // Pour les utilisateurs non connectés, mettre à jour dans la session
            $cart = $request->session()->get('cart', []);
            if (isset($cart[$cartKey])) {
                $product = Product::where('status', 'active')->find($cart[$cartKey]['product_id']);
                
                // Vérifier si le produit est toujours en stock
                if ($product && $product->stock <= 0) {
                    return back()->with('error', 'Ce produit est maintenant en rupture de stock.');
                }
                
                // Vérifier si la quantité demandée ne dépasse pas le stock disponible
                if ($product && $validated['quantity'] > $product->stock) {
                    return back()->with('error', 'La quantité demandée dépasse le stock disponible (' . $product->stock . ' unités restantes).');
                }
                
                $cart[$cartKey]['quantity'] = $validated['quantity'];
                $request->session()->put('cart', $cart);
            }
        }
        
        return back()->with('success', 'Quantité mise à jour.');
    }

    public function checkout(Request $request): View
    {
        $cart = $request->session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        
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
        
        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function processOrder(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:mtn_momo,moov_money,celtiis_money,cash',
            'phone_number' => 'nullable|required_if:payment_method,mtn_momo,moov_money,celtiis_money|regex:/^[0-9]+$/',
            'payment_reference' => 'nullable|required_if:payment_method,mtn_momo,moov_money,celtiis_money|string|max:255',
        ], [
            'phone_number.regex' => 'Le numéro de téléphone doit contenir uniquement des chiffres.',
        ]);
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Votre panier est vide.'], 400);
            }
            return back()->withErrors(['cart' => 'Votre panier est vide.']);
        }
        $total = 0;
        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'en_cours',
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'] ?? null,
            'total' => 0,
        ]);
        
        foreach ($cart as $cartKey => $cartData) {
            if (is_array($cartData) && isset($cartData['product_id'])) {
                $product = Product::where('status', 'active')->find($cartData['product_id']);
                if ($product) {
                    $line = $cartData['variant_price'] * $cartData['quantity'];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $cartData['quantity'],
                        'unit_price' => $cartData['variant_price'],
                        'line_total' => $line,
                    ]);
                    $total += $line;
                }
            }
        }
        $order->update(['total' => $total]);
        
        // Don't clear cart yet for API payments - will be cleared after successful payment
        if ($data['payment_method'] === 'cash') {
            $request->session()->forget('cart');
        }

        // Email notification
        try {
            Mail::raw('Votre commande #'.$order->id.' a été reçue. Total: '.$total.' FCFA', function ($m) use ($order) {
                $m->to($order->user->email)->subject('Confirmation de commande');
            });
        } catch (\Throwable $e) {
            // silently ignore in dev
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'message' => 'Commande créée avec succès'
            ]);
        }

        return redirect()->route('client.orders')->with('success', 'Commande passée avec succès.');
    }

}

