<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Metier;
use Illuminate\Http\Request;

class MetierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $metiers = Metier::orderBy('nom')->paginate(10);
        return view('administrationetablissement.metiers.index', compact('metiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrationetablissement.metiers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:metiers,nom',
            'description' => 'nullable|string'
        ], [
            'nom.required' => 'Le nom du métier est obligatoire.',
            'nom.unique' => 'Ce nom de métier existe déjà.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.'
        ]);

        Metier::create($request->all());

        return redirect()->route('administrationetablissement.metiers.index')
                        ->with('success', 'Métier créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Metier $metier)
    {
        $metier->load(['formateurs', 'modules']);
        return view('administrationetablissement.metiers.show', compact('metier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Metier $metier)
    {
        return view('administrationetablissement.metiers.edit', compact('metier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Metier $metier)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:metiers,nom,' . $metier->id,
            'description' => 'nullable|string'
        ], [
            'nom.required' => 'Le nom du métier est obligatoire.',
            'nom.unique' => 'Ce nom de métier existe déjà.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.'
        ]);

        $metier->update($request->all());

        return redirect()->route('administrationetablissement.metiers.index')
                        ->with('success', 'Métier modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Metier $metier)
    {
        try {
            $metier->delete();
            return redirect()->route('administrationetablissement.metiers.index')
                            ->with('success', 'Métier supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('administrationetablissement.metiers.index')
                            ->with('error', 'Impossible de supprimer ce métier car il est utilisé.');
        }
    }
}