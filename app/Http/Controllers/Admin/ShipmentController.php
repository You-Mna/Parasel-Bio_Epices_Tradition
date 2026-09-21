<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointDeVente;
use App\Models\Product;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Shipment::with('pointDeVente', 'items.product')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shipments = $query->paginate(20)->withQueryString();
        return view('admin.shipments.index', compact('shipments'));
    }

    public function create(): View
    {
        $pointsDeVente = PointDeVente::orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('admin.shipments.create', compact('pointsDeVente', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'point_de_vente_id' => 'required|exists:points_de_vente,id',
            'shipped_at' => 'required|date',
            'delivery_method' => 'nullable|string|max:100',
            'tracking_reference' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_size' => 'nullable|string|max:50',
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && $this->isMarinadeWithVariants($product)) {
                if (empty($item['variant_size'])) {
                    return back()->withInput()->with('error', 'Pour « Parasel-Bio Marinade », veuillez choisir une variante (poids).');
                }
            }
        }
        // Toutes les expéditions démarrent en "en_cours" côté stock
        $status = Shipment::STATUS_EN_COURS;

        // On doit réserver le stock immédiatement
        if ($status === Shipment::STATUS_EN_COURS) {
            $err = $this->validateShipmentItemsStock($data['items']);
            if ($err) {
                return back()->withInput()->with('error', $err);
            }
        }

        $shipment = Shipment::create([
            'point_de_vente_id' => $data['point_de_vente_id'],
            'order_id' => null,
            'status' => $status,
            'shipped_at' => $data['shipped_at'] ?? null,
            'delivery_method' => $data['delivery_method'] ?? null,
            'tracking_reference' => $data['tracking_reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || (int) $item['quantity'] < 1) {
                continue;
            }
            $shipment->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'variant_size' => $item['variant_size'] ?? null,
            ]);
        }

        // Réservation de stock dès que le statut est "en cours"
        if ($shipment->status === 'en_cours') {
            $this->decrementStockForShipment($shipment->fresh(['items.product']));
        }

        return redirect()->route('admin.shipments.index')->with('success', 'Expédition créée avec succès.');
    }

    public function edit(Shipment $shipment): View
    {
        $shipment->load('pointDeVente', 'items.product');
        $pointsDeVente = PointDeVente::orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('admin.shipments.edit', compact('shipment', 'pointsDeVente', 'products'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'point_de_vente_id' => 'required|exists:points_de_vente,id',
            'status' => 'required|in:en_cours,expediee,annulee',
            'shipped_at' => 'required|date',
            'delivery_method' => 'nullable|string|max:100',
            'tracking_reference' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_size' => 'nullable|string|max:50',
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && $this->isMarinadeWithVariants($product)) {
                if (empty($item['variant_size'])) {
                    return back()->withInput()->with('error', 'Pour « Parasel-Bio Marinade », veuillez choisir une variante (poids).');
                }
            }
        }

        $previousStatus = $shipment->status;

        // On garde un snapshot des anciennes lignes pour ajuster le stock après mise à jour
        $originalItems = $shipment->items()->with('product')->get();

        // Validation du stock selon le changement de statut
        if ($previousStatus !== 'en_cours' && $data['status'] === 'en_cours') {
            // On passe à "en cours" : on doit vérifier tout le stock nécessaire
            $err = $this->validateShipmentItemsStock($data['items']);
            if ($err) {
                return back()->withInput()->with('error', $err);
            }
        } elseif ($previousStatus === 'en_cours' && $data['status'] === 'en_cours') {
            // On reste en "en cours" mais les quantités changent : vérifier uniquement les ajouts
            $diffToReserve = $this->computeItemsDiffToReserve($originalItems, $data['items']);
            $err = $this->validateShipmentItemsStock($diffToReserve);
            if ($err) {
                return back()->withInput()->with('error', $err);
            }
        }

        $shipment->update([
            'point_de_vente_id' => $data['point_de_vente_id'],
            'status' => $data['status'],
            'shipped_at' => $data['shipped_at'] ?? null,
            'delivery_method' => $data['delivery_method'] ?? null,
            'tracking_reference' => $data['tracking_reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // On remplace les lignes d'expédition par les nouvelles
        $shipment->items()->delete();
        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || (int) $item['quantity'] < 1) {
                continue;
            }
            $shipment->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'variant_size' => $item['variant_size'] ?? null,
            ]);
        }

        // Ajustement du stock en fonction de l'évolution du statut
        $updatedShipment = $shipment->fresh(['items.product']);

        if ($previousStatus !== 'en_cours' && $data['status'] === 'en_cours') {
            // Nouveau passage à "en cours" : réserver tout le stock
            $this->decrementStockForShipment($updatedShipment);
        } elseif ($previousStatus === 'en_cours' && $data['status'] === 'en_cours') {
            // Toujours "en cours" : appliquer seulement la différence
            $this->applyStockDiff($originalItems, $updatedShipment->items);
        } elseif ($previousStatus === 'en_cours' && $data['status'] === 'annulee') {
            // Annulation : restaurer tout le stock réservé
            $this->incrementStockForItems($originalItems);
        }
        // Cas "expediee" : aucun changement, la réservation a déjà été faite en "en_cours"

        return redirect()->route('admin.shipments.index')->with('success', 'Expédition mise à jour.');
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        $shipment->delete();
        return back()->with('success', 'Expédition supprimée.');
    }

    /**
     * Seul le produit "Parasel-Bio Marinade" a des variantes (poids) à choisir.
     */
    private function isMarinadeWithVariants(Product $product): bool
    {
        return $product->name === 'Parasel-Bio Marinade'
            && $product->variants
            && is_array($product->variants)
            && count($product->variants) > 0;
    }

    /**
     * Vérifie que le stock est suffisant pour chaque ligne (produit ou variante Marinade).
     */
    private function validateShipmentItemsStock(array $items): ?string
    {
        foreach ($items as $item) {
            if (empty($item['product_id']) || (int) ($item['quantity'] ?? 0) < 1) {
                continue;
            }
            $product = Product::find($item['product_id']);
            if (!$product) {
                continue;
            }
            $qty = (int) $item['quantity'];
            $variantSize = $item['variant_size'] ?? null;
            if ($this->isMarinadeWithVariants($product) && $variantSize) {
                foreach ($product->variants as $v) {
                    if (($v['size'] ?? '') === $variantSize) {
                        $stock = (int) ($v['stock'] ?? 0);
                        if ($stock < $qty) {
                            return 'Stock insuffisant pour « ' . $product->name . ' » (' . $variantSize . ') (disponible : ' . $stock . ').';
                        }
                        break;
                    }
                }
            } else {
                if ((int) $product->stock < $qty) {
                    return 'Stock insuffisant pour « ' . $product->name . ' » (disponible : ' . $product->stock . ').';
                }
            }
        }
        return null;
    }

    /**
     * Diminue le stock global (ou variante pour Marinade) pour chaque produit de l'expédition.
     */
    private function decrementStockForShipment(Shipment $shipment): void
    {
        $shipment->load('items.product');
        $this->adjustStockForItems($shipment->items, -1);
    }

    /**
     * Ré-augmente le stock pour une collection d'items (utilisé en cas d'annulation).
     */
    private function incrementStockForItems($items): void
    {
        $this->adjustStockForItems($items, 1);
    }

    /**
     * Applique une variation de stock (direction = -1 pour décrémenter, +1 pour incrémenter).
     */
    private function adjustStockForItems($items, int $direction): void
    {
        foreach ($items as $item) {
            if (!$item->product) {
                continue;
            }
            $product = $item->product;
            $qty = $item->quantity * $direction;

            if ($this->isMarinadeWithVariants($product) && $item->variant_size) {
                $variants = $product->variants;
                foreach ($variants as $index => $v) {
                    if (($v['size'] ?? '') === $item->variant_size) {
                        $current = (int) ($v['stock'] ?? 0);
                        $variants[$index]['stock'] = max(0, $current + $qty);
                        break;
                    }
                }
                $product->variants = $variants;
                $product->stock = array_sum(array_column($variants, 'stock'));
                $product->save();
            } else {
                // Pour l'incrément, on utilise increment ; pour le décrément, decrement
                if ($direction < 0) {
                    $product->decrement('stock', abs($qty));
                } elseif ($direction > 0) {
                    $product->increment('stock', $qty);
                }
            }
        }
    }

    /**
     * Calcule la différence de quantités à réserver en plus (par produit / variante)
     * lorsque l'on reste en statut "en_cours".
     *
     * Retourne un tableau d'"items" au même format que dans la requête, mais ne
     * contenant que les quantités supplémentaires (différence positive).
     */
    private function computeItemsDiffToReserve($originalItems, array $newItems): array
    {
        $oldMap = [];
        foreach ($originalItems as $item) {
            $key = $item->product_id . '|' . ($item->variant_size ?? '');
            $oldMap[$key] = ($oldMap[$key] ?? 0) + (int) $item->quantity;
        }

        $newMap = [];
        foreach ($newItems as $item) {
            if (empty($item['product_id']) || (int) ($item['quantity'] ?? 0) < 1) {
                continue;
            }
            $key = (int)$item['product_id'] . '|' . (($item['variant_size'] ?? '') ?: '');
            $newMap[$key] = ($newMap[$key] ?? 0) + (int) $item['quantity'];
        }

        $diffItems = [];
        foreach ($newMap as $key => $newQty) {
            $oldQty = $oldMap[$key] ?? 0;
            if ($newQty > $oldQty) {
                // Il faut réserver la différence supplémentaire
                [$productId, $variantSize] = explode('|', $key, 2);
                $diffItems[] = [
                    'product_id' => (int) $productId,
                    'quantity' => $newQty - $oldQty,
                    'variant_size' => $variantSize !== '' ? $variantSize : null,
                ];
            }
        }

        return $diffItems;
    }

    /**
     * Applique la différence de stock entre les anciennes lignes et les nouvelles
     * pour un statut "en_cours" (augmentation => décrément, diminution => incrément).
     */
    private function applyStockDiff($originalItems, $newItems): void
    {
        $oldMap = [];
        foreach ($originalItems as $item) {
            $key = $item->product_id . '|' . ($item->variant_size ?? '');
            $oldMap[$key] = ($oldMap[$key] ?? 0) + (int) $item->quantity;
        }

        $newMap = [];
        foreach ($newItems as $item) {
            $key = $item->product_id . '|' . ($item->variant_size ?? '');
            $newMap[$key] = ($newMap[$key] ?? 0) + (int) $item->quantity;
        }

        $allKeys = array_unique(array_merge(array_keys($oldMap), array_keys($newMap)));

        foreach ($allKeys as $key) {
            $oldQty = $oldMap[$key] ?? 0;
            $newQty = $newMap[$key] ?? 0;
            $diff = $newQty - $oldQty;

            if ($diff === 0) {
                continue;
            }

            [$productId, $variantSize] = explode('|', $key, 2);
            $product = Product::find((int) $productId);
            if (!$product) {
                continue;
            }

            // Construire un "fake item" pour réutiliser adjustStockForItems
            $fakeItem = new \stdClass();
            $fakeItem->product = $product;
            $fakeItem->quantity = abs($diff);
            $fakeItem->variant_size = $variantSize !== '' ? $variantSize : null;

            if ($diff > 0) {
                // On a besoin de plus de stock => décrémenter
                $this->adjustStockForItems([$fakeItem], -1);
            } else {
                // Moins de stock réservé => ré-augmenter
                $this->adjustStockForItems([$fakeItem], 1);
            }
        }
    }
}
