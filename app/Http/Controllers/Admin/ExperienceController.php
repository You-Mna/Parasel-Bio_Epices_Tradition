<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExperienceController extends Controller
{
    public function index(): View
    {
        $experiences = Experience::latest()->paginate(20);
        return view('admin.experiences.index', compact('experiences'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'author_name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);
        $data['is_published'] = (bool)($data['is_published'] ?? false);
        Experience::create($data);
        return back()->with('success', 'Expérience ajoutée');
    }

    public function edit(Experience $experience): View
    {
        return view('admin.experiences.edit', compact('experience'));
    }

    public function update(Request $request, Experience $experience): RedirectResponse
    {
        $data = $request->validate([
            'author_name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);
        $data['is_published'] = (bool)($data['is_published'] ?? false);
        $experience->update($data);
        return redirect()->route('admin.experiences.index')->with('success', "Expérience mise à jour");
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();
        return back()->with('success', 'Expérience supprimée');
    }
}

