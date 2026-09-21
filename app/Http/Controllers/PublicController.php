<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Experience;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $experiences = Experience::where('is_published', true)->latest()->take(5)->get();
        $homeProducts = Product::active()
            ->orderedForCatalog()
            ->take(4)
            ->get();

        return view('public.home', compact('experiences', 'homeProducts'));
    }

    public function products(Request $request): View|RedirectResponse
    {
        // Après connexion : ajouter au panier le produit mémorisé puis rediriger vers la page produit (ancrage)
        if (Auth::check() && $request->has('add_product')) {
            $productId = (int) $request->get('add_product');
            $data = session('add_to_cart_after_login');
            if ($data && (int)($data['product_id'] ?? 0) === $productId) {
                $product = Product::where('status', 'active')->find($productId);
                if ($product) {
                    $qty = (int)($data['quantity'] ?? 1);
                    $variantPrice = $data['variant_price'] ?? $product->price;
                    $variantSize = $data['variant_size'] ?? '';
                    $cartKey = $product->id . '_' . $variantPrice . '_' . $variantSize;
                    $existingItem = CartItem::where('user_id', Auth::id())->where('cart_key', $cartKey)->first();
                    if ($existingItem) {
                        $existingItem->update(['quantity' => $existingItem->quantity + $qty]);
                    } else {
                        CartItem::create([
                            'user_id' => Auth::id(),
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'variant_price' => $variantPrice,
                            'variant_size' => $variantSize,
                            'cart_key' => $cartKey,
                        ]);
                    }
                    session()->forget('add_to_cart_after_login');
                    return redirect()->to(route('products.index') . '#product-' . $productId)
                        ->with('success', 'Produit ajouté au panier.');
                }
                session()->forget('add_to_cart_after_login');
            }
        }

        $products = Product::active()->orderedForCatalog()->paginate(12);
        return view('public.products', compact('products'));
    }



    public function pointsDistribution(): View
    {
        return view('public.points-distribution');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50|regex:/^[0-9]+$/',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Créer le message avec les nouveaux champs
        Message::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'content' => "Sujet: " . $data['subject'] . "\n\nMessage: " . $data['message'],
        ]);
        
        return redirect()
            ->route('contact')
            ->setStatusCode(303)
            ->with('success', 'Merci pour votre message ! Notre équipe vous répondra dans les plus brefs délais.');
    }
}

