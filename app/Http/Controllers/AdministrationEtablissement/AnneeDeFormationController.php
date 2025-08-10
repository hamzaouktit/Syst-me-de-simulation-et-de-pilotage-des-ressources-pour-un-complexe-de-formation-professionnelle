<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\AnneeDeFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnneeDeFormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $anneesDeFormation = AnneeDeFormation::orderBy('annee', 'desc')->paginate(10);
        return view('administrationetablissement.anneesdeformations.index', compact('anneesDeFormation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrationetablissement.anneesdeformations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'annee' => 'required|integer|min:2020|max:2050|unique:annees_de_formation,annee'
        ], [
            'annee.required' => 'L\'année est requise.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année doit être supérieure ou égale à 2020.',
            'annee.max' => 'L\'année doit être inférieure ou égale à 2050.',
            'annee.unique' => 'Cette année existe déjà.'
        ]);

        AnneeDeFormation::create($request->all());

        return redirect()->route('administrationetablissement.anneesdeformations.index')
            ->with('success', 'Année de formation créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AnneeDeFormation $anneesdeformation)
    {
        $anneesdeformation->load('groupes');
        return view('administrationetablissement.anneesdeformations.show', compact('anneesdeformation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AnneeDeFormation $anneesdeformation)
    {
        return view('administrationetablissement.anneesdeformations.edit', compact('anneesdeformation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnneeDeFormation $anneesdeformation)
    {
        $request->validate([
            'annee' => 'required|integer|min:2020|max:2050|unique:annees_de_formation,annee,' . $anneesdeformation->id
        ], [
            'annee.required' => 'L\'année est requise.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année doit être supérieure ou égale à 2020.',
            'annee.max' => 'L\'année doit être inférieure ou égale à 2050.',
            'annee.unique' => 'Cette année existe déjà.'
        ]);

        $anneesdeformation->update($request->all());

        return redirect()->route('administrationetablissement.anneesdeformations.index')
            ->with('success', 'Année de formation modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnneeDeFormation $anneesdeformation)
    {
        try {
            // Vérifier s'il y a des groupes associés
            if ($anneesdeformation->groupes()->count() > 0) {
                return redirect()->route('administrationetablissement.anneesdeformations.index')
                    ->with('error', 'Impossible de supprimer cette année car elle contient des groupes.');
            }

            $anneesdeformation->delete();
            
            return redirect()->route('administrationetablissement.anneesdeformations.index')
                ->with('success', 'Année de formation supprimée avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('administrationetablissement.anneesdeformations.index')
                ->with('error', 'Erreur lors de la suppression de l\'année de formation.');
        }
    }
}