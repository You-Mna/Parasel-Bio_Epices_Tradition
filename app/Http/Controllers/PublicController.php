<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Experience;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $experiences = Experience::where('is_published', true)->latest()->take(5)->get();
        return view('public.home', compact('experiences'));
    }

    public function products(): View
    {
        $products = Product::where('status', 'active')->orderByRaw("
            CASE 
                WHEN name = 'Parasel-Bio Marinade' THEN 1
                WHEN name = 'Xwladjê du Chef Paludier' THEN 2
                WHEN name = 'Arôme Parasel' THEN 3
                WHEN name = 'ParaStress' THEN 4
                ELSE 5
            END
        ")->paginate(12);
        return view('public.products', compact('products'));
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
        
        return back()->with('success', 'Merci pour votre message ! Notre équipe vous répondra dans les plus brefs délais.');
    }
}

