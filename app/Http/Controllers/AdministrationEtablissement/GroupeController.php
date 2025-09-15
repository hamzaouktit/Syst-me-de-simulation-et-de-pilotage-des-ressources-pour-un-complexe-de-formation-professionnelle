<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\Formation;
use App\Models\AnneeDeFormation;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        $query = Groupe::with(['formation', 'anneeDeFormation'])
            ->whereHas('formation', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            });

        // Filtrage par formation (seulement les formations de l'établissement)
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
        
        // Données pour les filtres - seulement les formations de l'établissement du directeur
        $formations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('titre')
            ->get();
        $annees = AnneeDeFormation::orderBy('annee', 'desc')->get();

        return view('administrationetablissement.groupes.index', compact('groupes', 'formations', 'annees', 'etablissement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Seulement les formations de l'établissement du directeur
        $formations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('titre')
            ->get();
        $annees = AnneeDeFormation::orderBy('annee', 'desc')->get();
        
        return view('administrationetablissement.groupes.create', compact('formations', 'annees', 'etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Validation avec vérification que la formation appartient à l'établissement
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
            'formation_id' => [
                'required',
                'exists:formations,id',
                function ($attribute, $value, $fail) use ($etablissement) {
                    $formation = Formation::find($value);
                    if ($formation && $formation->etablissement_id != $etablissement->id) {
                        $fail('Cette formation n\'appartient pas à votre établissement.');
                    }
                }
            ],
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
        // Vérifier que le groupe appartient à l'établissement du directeur
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement || $groupe->formation->etablissement_id != $etablissement->id) {
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('error', 'Vous n\'avez pas accès à ce groupe.');
        }

        $groupe->load(['formation', 'anneeDeFormation']);
        return view('administrationetablissement.groupes.show', compact('groupe', 'etablissement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Groupe $groupe)
    {
        // Vérifier que le groupe appartient à l'établissement du directeur
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement || $groupe->formation->etablissement_id != $etablissement->id) {
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('error', 'Vous n\'avez pas accès à ce groupe.');
        }

        // Seulement les formations de l'établissement du directeur
        $formations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('titre')
            ->get();
        $annees = AnneeDeFormation::orderBy('annee', 'desc')->get();
        
        return view('administrationetablissement.groupes.edit', compact('groupe', 'formations', 'annees', 'etablissement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groupe $groupe)
    {
        // Vérifier que le groupe appartient à l'établissement du directeur
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement || $groupe->formation->etablissement_id != $etablissement->id) {
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('error', 'Vous n\'avez pas accès à ce groupe.');
        }

        // Validation avec vérification que la formation appartient à l'établissement
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
            'formation_id' => [
                'required',
                'exists:formations,id',
                function ($attribute, $value, $fail) use ($etablissement) {
                    $formation = Formation::find($value);
                    if ($formation && $formation->etablissement_id != $etablissement->id) {
                        $fail('Cette formation n\'appartient pas à votre établissement.');
                    }
                }
            ],
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
        // Vérifier que le groupe appartient à l'établissement du directeur
        $etablissement = $this->getEtablissementDirecteur();
        
        if (!$etablissement || $groupe->formation->etablissement_id != $etablissement->id) {
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('error', 'Vous n\'avez pas accès à ce groupe.');
        }

        try {
            $groupe->delete();
            
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('success', 'Groupe supprimé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('administrationetablissement.groupes.index')
                ->with('error', 'Erreur lors de la suppression du groupe.');
        }
    }

    /**
     * Récupère l'établissement du directeur connecté
     */
    private function getEtablissementDirecteur()
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        // Si le user a une relation directe avec un établissement
        if (method_exists($user, 'etablissement')) {
            return $user->etablissement;
        }

        // Sinon, chercher l'établissement via une table pivot ou une autre relation
        // Vous devrez adapter cette partie selon votre structure de base de données
        // Par exemple, si vous avez une table user_etablissement :
        /*
        return Etablissement::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();
        */

        // Ou si vous avez un champ etablissement_id dans la table users :
        /*
        if ($user->etablissement_id) {
            return Etablissement::find($user->etablissement_id);
        }
        */

        // Pour l'instant, je retourne le premier établissement (à adapter)
        // IMPORTANT: Vous devez modifier cette méthode selon votre structure de données
        return Etablissement::first();
    }
}