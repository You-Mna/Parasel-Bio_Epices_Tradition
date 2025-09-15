<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:50|regex:/^[0-9]+$/',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'client',
        ]);
        Auth::login($user);
        return redirect()->route('client.account');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            
            // Synchroniser le panier de session vers la base de données
            $this->syncCartFromSession($request);
            
            // Rediriger les administrateurs vers l'admin
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin');
            }
            
            return redirect()->intended('/');
        }
        return back()->withErrors(['email' => 'Identifiants invalides']);
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/');
        } catch (\Exception $e) {
            // En cas d'erreur, forcer la déconnexion
            Auth::logout();
            return redirect('/');
        }
    }
    
    private function syncCartFromSession(Request $request): void
    {
        $sessionCart = $request->session()->get('cart', []);
        
        if (empty($sessionCart)) {
            return;
        }
        
        foreach ($sessionCart as $cartKey => $cartData) {
            if (is_array($cartData) && isset($cartData['product_id'])) {
                // Vérifier si l'item existe déjà dans la base de données
                $existingItem = CartItem::where('user_id', Auth::id())
                    ->where('cart_key', $cartKey)
                    ->first();
                
                if (!$existingItem) {
                    // Créer un nouvel item dans la base de données
                    try {
                        CartItem::create([
                            'user_id' => Auth::id(),
                            'product_id' => $cartData['product_id'],
                            'quantity' => $cartData['quantity'] ?? 1,
                            'variant_price' => $cartData['variant_price'] ?? null,
                            'variant_size' => $cartData['variant_size'] ?? null,
                            'cart_key' => $cartKey
                        ]);
                    } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                        // Si l'item existe déjà, l'ignorer
                        \Log::info('Item déjà existant lors de la synchronisation', ['cart_key' => $cartKey]);
                    }
                }
            }
        }
        
        // Vider le panier de session après synchronisation
        $request->session()->forget('cart');
    }
}

