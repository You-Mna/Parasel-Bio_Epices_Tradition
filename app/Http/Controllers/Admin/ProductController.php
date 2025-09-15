<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
        ]);
        
        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }
        
        $data['is_featured'] = (bool)($data['is_featured'] ?? false);
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Produit ajouté');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
            'variants' => 'nullable|array',
            'variants.*.size' => 'required_with:variants|string',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
        ]);
        
        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }
        
        // Gérer les variantes
        if ($request->has('variants') && !empty($request->variants)) {
            $variants = [];
            foreach ($request->variants as $index => $variant) {
                if (!empty($variant['size']) && !empty($variant['price']) && isset($variant['stock'])) {
                    $variantData = [
                        'size' => $variant['size'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'] ?? 0
                    ];
                    
                    // Gérer l'upload de l'image de la variante
                    if ($request->hasFile("variants.{$index}.image")) {
                        $image = $request->file("variants.{$index}.image");
                        $imageName = time() . '_' . $variant['size'] . '_' . $image->getClientOriginalName();
                        $image->move(public_path('images'), $imageName);
                        $variantData['image'] = $imageName;
                    } elseif (isset($variant['image']) && $variant['image']) {
                        // Garder l'image existante si pas de nouveau upload
                        $variantData['image'] = $variant['image'];
                    }
                    
                    $variants[] = $variantData;
                }
            }
            $data['variants'] = $variants;
        }
        
        $data['is_featured'] = (bool)($data['is_featured'] ?? false);
        
        // Si le prix du produit principal est modifié et que le produit a des variantes,
        // mettre à jour aussi le prix de toutes les variantes
        if (isset($data['price']) && $product->variants && is_array($product->variants)) {
            $variants = $product->variants;
            foreach ($variants as $index => $variant) {
                $variants[$index]['price'] = $data['price'];
            }
            $data['variants'] = $variants;
        }
        
        $product->update($data);
        
        // Nettoyer le cache pour forcer la synchronisation
        \Cache::flush();
        
        // Retourner une réponse JSON pour les requêtes AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit mis à jour avec succès',
                'product' => $product->fresh()
            ]);
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);
        
        // Nettoyer le cache pour forcer la synchronisation
        \Cache::flush();
        
        $message = $newStatus === 'active' ? 'Produit activé' : 'Produit désactivé';
        return back()->with('success', $message);
    }

    public function toggleStock(Product $product)
    {
        try {
            // Toggle le statut du stock
            if ($product->stock > 0) {
                // Mettre en rupture
                $product->update(['stock' => 0]);
                
                // Si le produit a des variantes, mettre aussi toutes les variantes en rupture
                if ($product->variants && is_array($product->variants)) {
                    $variants = $product->variants;
                    foreach ($variants as $index => $variant) {
                        $variants[$index]['stock'] = 0;
                    }
                    $product->update(['variants' => $variants]);
                }
                
                $message = 'Produit mis en rupture de stock';
            } else {
                // Remettre en stock (on peut définir une quantité par défaut)
                $product->update(['stock' => 10]); // Quantité par défaut
                
                // Si le produit a des variantes, remettre aussi toutes les variantes en stock
                if ($product->variants && is_array($product->variants)) {
                    $variants = $product->variants;
                    foreach ($variants as $index => $variant) {
                        $variants[$index]['stock'] = 10; // Quantité par défaut
                    }
                    $product->update(['variants' => $variants]);
                }
                
                $message = 'Produit remis en stock';
            }

            // Nettoyer le cache pour forcer la synchronisation
            \Cache::flush();

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour du statut');
        }
    }
}