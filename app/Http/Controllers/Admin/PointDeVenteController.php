<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointDeVente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointDeVenteController extends Controller
{
    public function index(): View
    {
        $points = PointDeVente::orderBy('name')->paginate(20);
        return view('admin.points-de-vente.index', compact('points'));
    }

    public function create(): View
    {
        return view('admin.points-de-vente.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:points_de_vente,code',
            'address' => 'required|string',
            'phone' => 'required|string|max:50|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
        ]);
        PointDeVente::create($data);
        return redirect()->route('admin.points-de-vente.index')->with('success', 'Point de vente créé.');
    }

    public function edit(PointDeVente $pointDeVente): View
    {
        return view('admin.points-de-vente.edit', ['point' => $pointDeVente]);
    }

    public function update(Request $request, PointDeVente $pointDeVente): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:points_de_vente,code,' . $pointDeVente->id,
            'address' => 'required|string',
            'phone' => 'required|string|max:50|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
        ]);
        $pointDeVente->update($data);
        return redirect()->route('admin.points-de-vente.index')->with('success', 'Point de vente mis à jour.');
    }

    public function destroy(PointDeVente $pointDeVente): RedirectResponse
    {
        $pointDeVente->delete();
        return back()->with('success', 'Point de vente supprimé.');
    }
}
