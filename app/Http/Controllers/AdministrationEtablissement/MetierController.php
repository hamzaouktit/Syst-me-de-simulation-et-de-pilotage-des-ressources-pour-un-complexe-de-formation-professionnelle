<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Metier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Construction de la requête de base
        // Filtrer seulement les métiers associés aux modules des formations de cet établissement
        $query = Metier::whereHas('modules.formations', function($q) use ($etablissement) {
            $q->where('etablissement_id', $etablissement->id);
        });

        // Recherche par nom
        if ($request->filled('search_nom')) {
            $query->where('nom', 'like', '%' . $request->search_nom . '%');
        }

        // Recherche par description
        if ($request->filled('search_description')) {
            $query->where('description', 'like', '%' . $request->search_description . '%');
        }

        // Tri
        $sort_by = $request->get('sort_by', 'nom');
        $sort_direction = $request->get('sort_direction', 'asc');
        
        $valid_sort_columns = ['nom', 'description', 'created_at'];
        if (in_array($sort_by, $valid_sort_columns)) {
            $query->orderBy($sort_by, $sort_direction);
        } else {
            $query->orderBy('nom', 'asc');
        }

        $metiers = $query->paginate(10)->withQueryString();
        
        return view('administrationetablissement.metiers.index', compact('metiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        return view('administrationetablissement.metiers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

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
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le métier est associé à au moins un module de formation de cet établissement
        $metierEtablissement = $metier->modules()
            ->whereHas('formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->exists();

        if (!$metierEtablissement) {
            abort(403, 'Ce métier ne fait pas partie de votre établissement.');
        }

        // Charger seulement les formateurs et modules liés à cet établissement
        $metier->load([
            'formateurs' => function($query) use ($etablissement) {
                $query->whereHas('etablissement', function($q) use ($etablissement) {
                    $q->where('id', $etablissement->id);
                });
            },
            'modules' => function($query) use ($etablissement) {
                $query->whereHas('formations', function($q) use ($etablissement) {
                    $q->where('etablissement_id', $etablissement->id);
                });
            }
        ]);

        return view('administrationetablissement.metiers.show', compact('metier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Metier $metier)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le métier est associé à au moins un module de formation de cet établissement
        $metierEtablissement = $metier->modules()
            ->whereHas('formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->exists();

        if (!$metierEtablissement) {
            abort(403, 'Ce métier ne fait pas partie de votre établissement.');
        }

        return view('administrationetablissement.metiers.edit', compact('metier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Metier $metier)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le métier est associé à au moins un module de formation de cet établissement
        $metierEtablissement = $metier->modules()
            ->whereHas('formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->exists();

        if (!$metierEtablissement) {
            abort(403, 'Ce métier ne fait pas partie de votre établissement.');
        }

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
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le métier est associé à au moins un module de formation de cet établissement
        $metierEtablissement = $metier->modules()
            ->whereHas('formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->exists();

        if (!$metierEtablissement) {
            abort(403, 'Ce métier ne fait pas partie de votre établissement.');
        }

        try {
            // Attention: Cette suppression pourrait affecter d'autres établissements
            // Il serait peut-être mieux de dissocier le métier des modules de cet établissement seulement
            $metier->delete();
            return redirect()->route('administrationetablissement.metiers.index')
                            ->with('success', 'Métier supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('administrationetablissement.metiers.index')
                            ->with('error', 'Impossible de supprimer ce métier car il est utilisé.');
        }
    }

    /**
     * Méthode alternative pour dissocier un métier des modules de l'établissement au lieu de le supprimer
     */
    public function detachFromEtablissement(Metier $metier)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Récupérer tous les modules de cet établissement associés au métier
        $modules = $metier->modules()
            ->whereHas('formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->get();

        // Dissocier le métier de ces modules
        foreach ($modules as $module) {
            $module->metiers()->detach($metier->id);
        }

        return redirect()->route('administrationetablissement.metiers.index')
                        ->with('success', 'Métier dissocié des modules de votre établissement.');
    }
}