<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        // Ne pas afficher les commandes "en attente paiement" (paiement en ligne non confirmé) :
        // si FedaPay n'est pas confirmé, on considère qu'il n'y a pas de commande.
        $baseQuery = Order::with('user')
            ->where('status', '!=', 'en_cours');

        // Commandes en ligne : tout sauf "sur_place"
        $onlineOrders = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereNull('payment_method')
                  ->orWhere('payment_method', '!=', 'sur_place');
            })
            ->latest()
            ->paginate(10, ['*'], 'online_page');

        // Commandes hors ligne : paiement sur place
        $offlineOrders = (clone $baseQuery)
            ->where('payment_method', 'sur_place')
            ->latest()
            ->paginate(10, ['*'], 'offline_page');

        return view('admin.orders.index', compact('onlineOrders', 'offlineOrders'));
    }

    /**
     * Formulaire d'ajout d'une commande hors ligne (ex. vente à l'usine).
     */
    public function create(): View
    {
        $clients = User::where('role', 'client')
            ->where('email', 'not like', '%@example.%')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('admin.orders.create', compact('clients', 'products'));
    }

    /**
     * Enregistrer une commande hors ligne. Le stock est diminué à la création.
     * Ici, on ne gère que des clients hors ligne (nom + téléphone), sans compte.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'client_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_size' => 'nullable|string|max:50',
        ]);

        $clientName = trim($data['client_name']);
        $clientPhone = trim($data['client_phone']);
        $clientEmail = $data['client_email'] ?? null;

        // Créer / réutiliser un "vrai" client pour la page Clients (Relation clients)
        // Règle : on identifie un client par (téléphone + email) quand l'email est fourni.
        $userId = null;
        $userQuery = User::where('role', 'client')
            ->where('phone', $clientPhone);

        if ($clientEmail) {
            $userQuery->where('email', $clientEmail);
        }

        $existing = $userQuery->first();

        if ($existing) {
            $userId = $existing->id;
        } else {
            $user = User::create([
                'name' => $clientName,
                'first_name' => $clientName,
                'last_name' => $clientName,
                'email' => $clientEmail,
                'phone' => $clientPhone,
                'role' => 'client',
                'password' => bcrypt(Str::random(32)),
            ]);
            $userId = $user->id;
        }

        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && $this->isMarinadeWithVariants($product) && empty($item['variant_size'] ?? null)) {
                return back()->withInput()->with('error', 'Pour « Parasel-Bio Marinade », veuillez choisir une variante (poids).');
            }
        }

        $total = 0;
        $itemsToCreate = [];
        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || (int) $item['quantity'] < 1) {
                continue;
            }
            $qty = (int) $item['quantity'];
            $variantSize = $item['variant_size'] ?? null;
            if ($this->isMarinadeWithVariants($product) && $variantSize) {
                $price = 0;
                foreach ($product->variants as $v) {
                    if (($v['size'] ?? '') === $variantSize) {
                        $price = (float) ($v['price'] ?? 0);
                        break;
                    }
                }
                if ($price <= 0) {
                    return back()->withInput()->with('error', 'Variante « ' . $variantSize . ' » introuvable pour Marinade.');
                }
            } else {
                $price = (float) $product->price;
            }
            $lineTotal = $price * $qty;
            $total += $lineTotal;
            $itemsToCreate[] = [
                'product' => $product,
                'quantity' => $qty,
                'variant_size' => $this->isMarinadeWithVariants($product) ? $variantSize : null,
                'unit_price' => $price,
                'line_total' => $lineTotal,
            ];
        }

        if ($total <= 0) {
            return back()->withInput()->with('error', 'Ajoutez au moins un produit avec une quantité valide.');
        }

        foreach ($itemsToCreate as $item) {
            $product = $item['product'];
            $qty = $item['quantity'];
            if ($this->isMarinadeWithVariants($product) && !empty($item['variant_size'])) {
                foreach ($product->variants as $v) {
                    if (($v['size'] ?? '') === $item['variant_size']) {
                        $stock = (int) ($v['stock'] ?? 0);
                        if ($stock < $qty) {
                            return back()->withInput()->with('error', 'Stock insuffisant pour « Parasel-Bio Marinade » (' . $item['variant_size'] . ') (disponible : ' . $stock . ').');
                        }
                        break;
                    }
                }
            } else {
                $stock = (int) $product->stock;
                if ($stock < $qty) {
                    return back()->withInput()->with('error', 'Stock insuffisant pour « ' . $product->name . ' » (disponible : ' . $stock . ').');
                }
            }
        }

        $order = Order::create([
            'user_id' => $userId,
            'client_name' => $clientName,
            'client_phone' => $clientPhone,
            'client_email' => $clientEmail,
            'status' => 'livree_payee',
            'total' => $total,
            'payment_method' => 'sur_place',
            'payment_reference' => null,
        ]);

        foreach ($itemsToCreate as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'variant_size' => $item['variant_size'] ?? null,
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ]);
            $this->decrementStockForOrderItem($item['product'], $item['quantity'], $item['variant_size'] ?? null);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Commande hors ligne enregistrée. Le stock a été mis à jour.');
    }

    private function isMarinadeWithVariants(Product $product): bool
    {
        return $product->name === 'Parasel-Bio Marinade'
            && $product->variants
            && is_array($product->variants)
            && count($product->variants) > 0;
    }

    private function decrementStockForOrderItem(Product $product, int $qty, ?string $variantSize): void
    {
        if ($this->isMarinadeWithVariants($product) && $variantSize) {
            $variants = $product->variants;
            foreach ($variants as $index => $v) {
                if (($v['size'] ?? '') === $variantSize) {
                    $variants[$index]['stock'] = max(0, (int) ($v['stock'] ?? 0) - $qty);
                    break;
                }
            }
            $product->variants = $variants;
            $product->stock = array_sum(array_column($variants, 'stock'));
            $product->save();
        } else {
            $product->decrement('stock', $qty);
        }
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            // L'admin ne peut que marquer : Livrée payée ou Annulée (après livraison ou annulation)
            'status' => 'required|in:livree_payee,annulee',
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

