<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\Formation;
use App\Models\AnneeDeFormation;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Groupe::with(['formation', 'anneeDeFormation']);

        // Filtrage par formation
        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->formation_id);
        }

        // Filtrage par année
        if ($request->filled('annee_id')) {
            $query->where('annee_de_formation_id', $request->annee_id);
        }

        // Recherche par nom
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        $groupes = $query->orderBy('nom')->paginate(10);
        
        // Données pour les filtres
        $formations = Formation::orderBy('titre')->get();
        $annees = AnneeDeFormation::orderBy('annee', 'desc')->get();

        return view('administrationetablissement.groupes.index', compact('groupes', 'formations', 'annees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formations = Formation::orderBy('titre')->get();
        $annees = AnneeDeFormation::orderBy('annee', 'desc')->get();
        
        return view('administrationetablissement.groupes.create', compact('formations', 'annees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                // Règle d'unicité composite
                \Illuminate\Validation\Rule::unique('groupes')
                    ->where('formation_id', $request->formation_id)
                    ->where('annee_de_formation_id', $request->annee_de_formation_id)
            ],
            'effectif' => 'required|integer|min:0|max:100',
            'formation_id' => 'required|exists:formations,id',
            'annee_de_formation_id' => 'required|exists:annees_de_formation,id'
        ], [
            'nom.required' => 'Le nom du groupe est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'nom.unique' => 'Ce nom de groupe existe déjà pour cette formation et cette année.',
            'effectif.required' => 'L\'effectif est requis.',
            'effectif.integer' => 'L\'effectif doit être un nombre entier.',
            'effectif.min' => 'L\'effectif ne peut pas être négatif.',
            'effectif.max' => 'L\'effectif ne peut pas dépasser 100 étudiants.',
            'formation_id.required' => 'Veuillez sélectionner une formation.',
            'formation_id.exists' => 'La formation sélectionnée n\'existe pas.',
            'annee_de_formation_id.required' => 'Veuillez sélectionner une année.',
            'annee_de_formation_id.exists' => 'L\'année sélectionnée n\'existe pas.'
        ]);

        Groupe::create($request->all());

        return redirect()->route('administrationetablissement.groupes.index')
            ->with('success', 'Groupe créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Groupe $groupe)
    {
        $groupe->load(['formation', 'anneeDeFormation']);
        return view('administrationetablissement.groupes.show', compact('groupe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Groupe $groupe)
    {
        $formations = Formation::orderBy('titre')->get();
        $annees = AnneeDeFormation::orderBy('annee', 'desc')->get();
        
        return view('administrationetablissement.groupes.edit', compact('groupe', 'formations', 'annees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groupe $groupe)
    {
        $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                // Règle d'unicité composite (exclure le groupe actuel)
                \Illuminate\Validation\Rule::unique('groupes')
                    ->where('formation_id', $request->formation_id)
                    ->where('annee_de_formation_id', $request->annee_de_formation_id)
                    ->ignore($groupe->id)
            ],
            'effectif' => 'required|integer|min:0|max:100',
            'formation_id' => 'required|exists:formations,id',
            'annee_de_formation_id' => 'required|exists:annees_de_formation,id'
        ], [
            'nom.required' => 'Le nom du groupe est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'nom.unique' => 'Ce nom de groupe existe déjà pour cette formation et cette année.',
            'effectif.required' => 'L\'effectif est requis.',
            'effectif.integer' => 'L\'effectif doit être un nombre entier.',
            'effectif.min' => 'L\'effectif ne peut pas être négatif.',
            'effectif.max' => 'L\'effectif ne peut pas dépasser 100 étudiants.',
            'formation_id.required' => 'Veuillez sélectionner une formation.',
            'formation_id.exists' => 'La formation sélectionnée n\'existe pas.',
            'annee_de_formation_id.required' => 'Veuillez sélectionner une année.',
            'annee_de_formation_id.exists' => 'L\'année sélectionnée n\'existe pas.'
        ]);

        $groupe->update($request->all());

        return redirect()->route('administrationetablissement.groupes.index')
            ->with('success', 'Groupe modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groupe $groupe)
    {
        try {
            $groupe->delete();
            
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('success', 'Groupe supprimé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('error', 'Erreur lors de la suppression du groupe.');
        }
    }
}