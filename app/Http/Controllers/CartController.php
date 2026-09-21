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
        // Accepte invités (redirection login) et utilisateurs connectés (DB)
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variant_price' => 'nullable|numeric|min:0',
            'variant_size' => 'nullable|string'
        ]);
        
        $qty = $validated['quantity'] ?? 1;
        $variantPrice = $validated['variant_price'] ?? $product->price;
        $variantSize = $validated['variant_size'] ?? '';
        
        if (!Auth::check()) {
            // Invité : mémoriser l'intention et rediriger vers la connexion
            session([
                'url.intended' => route('products.index', ['add_product' => $product->id]),
                'add_to_cart_after_login' => [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'variant_price' => $variantPrice,
                    'variant_size' => $variantSize,
                ],
            ]);
            return redirect()->route('login.show')->with('info', 'Connectez-vous pour ajouter ce produit au panier.');
        }
        
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
        
        // Utilisateurs connectés: persister en base
        $existingItem = CartItem::where('user_id', Auth::id())
                ->where('cart_key', $cartKey)
                ->first();
            
            if ($existingItem) {
                if (($existingItem->quantity + $qty) > $availableStock) {
                    return redirect()->back(303)->with('error', 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).');
                }
                $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
            } else {
                if ($qty > $availableStock) {
                    return redirect()->back(303)->with('error', 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).');
                }
                
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
                    $existingItem = CartItem::where('user_id', Auth::id())
                        ->where('cart_key', $cartKey)
                        ->first();
                    if ($existingItem) {
                        if (($existingItem->quantity + $qty) > $availableStock) {
                            return redirect()->back(303)->with('error', 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).');
                        }
                        $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
                    }
                }
            }
        
        return redirect()->back(303)->with('success', 'Produit ajouté au panier avec succès!');
    }

    public function remove(Request $request, string $cartKey): mixed
    {
        if (!Auth::check()) {
            // Invités: retirer de la session
            $cart = $request->session()->get('cart', []);
            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
                $request->session()->put('cart', $cart);
                if ($request->wantsJson()) {
                    return response()->json(['success' => true]);
                }
                return back()->with('success', 'Produit retiré du panier.');
            }
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Produit non trouvé dans le panier.'], 404);
            }
            return back()->with('error', 'Produit non trouvé dans le panier.');
        }
        
        // Authentifiés: retirer de la base
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($cartItem) {
            $cartItem->delete();
            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Produit retiré du panier.');
        }
        
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'error' => 'Produit non trouvé dans le panier.'], 404);
        }
        return back()->with('error', 'Produit non trouvé dans le panier.');
    }

    public function updateQuantity(Request $request, string $cartKey): mixed
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        if (!Auth::check()) {
            // Invités: mettre à jour en session
            $cart = $request->session()->get('cart', []);
            if (!isset($cart[$cartKey])) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Produit non trouvé dans le panier.'], 404);
                }
                return back()->with('error', 'Produit non trouvé dans le panier.');
            }
            $productId = $cart[$cartKey]['product_id'] ?? null;
            $product = $productId ? Product::find($productId) : null;
            if (!$product) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Produit non trouvé.'], 404);
                }
                return back()->with('error', 'Produit non trouvé.');
            }
            $targetQty = (int)$validated['quantity'];
            $availableStock = $product->stock;
            $variantSize = $cart[$cartKey]['variant_size'] ?? '';
            if ($product->variants && is_array($product->variants) && $variantSize) {
                foreach ($product->variants as $variant) {
                    if ($variant['size'] === $variantSize) {
                        $availableStock = $variant['stock'];
                        break;
                    }
                }
            }
            if ($targetQty > $availableStock) {
                $message = 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => $message], 422);
                }
                return back()->with('error', $message);
            }
            $cart[$cartKey]['quantity'] = $targetQty;
            $request->session()->put('cart', $cart);
            if ($request->wantsJson()) {
                $itemTotal = ((float) ($cart[$cartKey]['variant_price'] ?? 0)) * $targetQty;
                $grandTotal = 0.0;
                foreach ($cart as $row) {
                    $grandTotal += ((float) ($row['variant_price'] ?? 0)) * ((int) ($row['quantity'] ?? 0));
                }
                return response()->json([
                    'success' => true,
                    'quantity' => $targetQty,
                    'item_total' => $itemTotal,
                    'grand_total' => $grandTotal,
                ]);
            }
            return back()->with('success', 'Quantité mise à jour.');
        }
        
        // Authentifiés: mettre à jour en base
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('cart_key', $cartKey)
            ->first();
        
        if ($cartItem) {
            $product = $cartItem->product;
            $availableStock = (int) $product->stock;
            if ($product->variants && is_array($product->variants) && $cartItem->variant_size) {
                foreach ($product->variants as $variant) {
                    if (($variant['size'] ?? null) === $cartItem->variant_size) {
                        $availableStock = (int) ($variant['stock'] ?? 0);
                        break;
                    }
                }
            }
            if ($validated['quantity'] > $availableStock) {
                $message = 'La quantité demandée dépasse le stock disponible (' . $availableStock . ' unités restantes).';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => $message], 422);
                }
                return back()->with('error', $message);
            }
            $cartItem->update(['quantity' => $validated['quantity']]);
            if ($request->wantsJson()) {
                $qty = (int) $validated['quantity'];
                $itemTotal = ((float) $cartItem->variant_price) * $qty;
                $grandTotal = (float) CartItem::where('user_id', Auth::id())->sum(\DB::raw('variant_price * quantity'));
                return response()->json([
                    'success' => true,
                    'quantity' => $qty,
                    'item_total' => $itemTotal,
                    'grand_total' => $grandTotal,
                ]);
            }
            return back()->with('success', 'Quantité mise à jour.');
        }
        
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'error' => 'Produit non trouvé dans le panier.'], 404);
        }
        return back()->with('error', 'Produit non trouvé dans le panier.');
    }

    public function checkout(): mixed
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
            // Deux modes de paiement : paiement en ligne (FedaPay) ou paiement à la livraison
            'payment_method' => 'required|in:cash,fedapay',
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
        
        // Statut automatique selon le mode de paiement
        $initialStatus = $validated['payment_method'] === 'cash'
            ? 'a_la_livraison'   // paiement à la livraison : en attente livraison (admin mettra "livrée payée" ou "annulée")
            : 'en_cours';        // paiement en ligne : en attente FedaPay (webhook mettra "payée en ligne")

        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => $initialStatus,
            'payment_method' => $validated['payment_method'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Créer les articles de commande et mettre à jour les stocks
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->variant_price,
                'line_total' => $cartItem->variant_price * $cartItem->quantity,
            ]);

            $product = $cartItem->product;
            if ($product) {
                // Gestion du stock pour les produits avec variantes
                if ($product->variants && is_array($product->variants) && count($product->variants) > 0 && $cartItem->variant_size) {
                    $variants = $product->variants;
                    foreach ($variants as $index => $variant) {
                        if (($variant['size'] ?? null) === $cartItem->variant_size) {
                            $currentStock = (int) ($variant['stock'] ?? 0);
                            $newStock = max(0, $currentStock - $cartItem->quantity);
                            $variants[$index]['stock'] = $newStock;
                            break;
                        }
                    }
                    $product->variants = $variants;
                    $product->stock = array_sum(array_column($variants, 'stock'));
                } else {
                    $currentStock = (int) ($product->stock ?? 0);
                    $product->stock = max(0, $currentStock - $cartItem->quantity);
                }

                $product->save();
            }
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

        // Soumission formulaire classique : paiement en ligne → page de paiement FedaPay
        if ($validated['payment_method'] === 'fedapay') {
            return redirect()->route('checkout.pay', $order);
        }

        return redirect()->route('client.orders')->with('success', 'Commande créée avec succès!');
    }

    /**
     * Page de paiement en ligne (FedaPay) pour une commande déjà créée.
     * Utilisée après redirection depuis le formulaire checkout (sans fetch, compatible mobile).
     */
    public function paymentPage(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (in_array($order->status, ['payee', 'payee_en_ligne', 'livree', 'livree_payee'], true)) {
            return redirect()->route('client.orders')->with('success', 'Cette commande est déjà payée.');
        }

        $total = (int) $order->total;
        $phoneForFedaPay = $this->normalizePhoneForFedaPay(Auth::user()->phone ?? '');
        return view('checkout.pay', compact('order', 'total', 'phoneForFedaPay'));
    }

    /**
     * Normalise n'importe quel format de numéro (Bénin) pour FedaPay :
     * sortie toujours 10 chiffres commençant par 01 (ex. 0151805450).
     *
     * Exemples acceptés en entrée :
     * - 0151805450, 0151 80 54 50, +229 01 51 80 54 50
     * - 151805450 (9 chiffres) → 0151805450
     * - 51805450 (8 chiffres)  → 0151805450
     * - 2290151805450          → 0151805450
     */
    private function normalizePhoneForFedaPay(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '229')) {
            $digits = substr($digits, 3);
        }

        $len = strlen($digits);

        if ($len >= 10) {
            return substr($digits, -10);
        }
        if ($len === 9) {
            return '0' . $digits;
        }
        if ($len === 8) {
            return '01' . $digits;
        }

        return '';
    }
}