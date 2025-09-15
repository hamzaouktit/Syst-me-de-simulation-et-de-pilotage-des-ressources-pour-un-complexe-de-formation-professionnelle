<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Formation;
use App\Models\Metier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
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

        // Construire la requête de base avec les relations
        // Filtrer seulement les modules qui sont associés aux formations de cet établissement
        $query = Module::with(['formations', 'metiers'])
            ->whereHas('formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            });

        // Filtrage par recherche (nom du module)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nom', 'LIKE', "%{$search}%");
        }

        // Filtrage par formation (seulement les formations de cet établissement)
        if ($request->filled('formation_id')) {
            $query->whereHas('formations', function($q) use ($request, $etablissement) {
                $q->where('formations.id', $request->get('formation_id'))
                  ->where('etablissement_id', $etablissement->id);
            });
        }

        // Filtrage par métier
        if ($request->filled('metier_id')) {
            $query->whereHas('metiers', function($q) use ($request) {
                $q->where('metiers.id', $request->get('metier_id'));
            });
        }

        // Filtrage par masse horaire
        if ($request->filled('masse_horaire_min')) {
            $query->where('masse_horaire', '>=', $request->get('masse_horaire_min'));
        }

        if ($request->filled('masse_horaire_max')) {
            $query->where('masse_horaire', '<=', $request->get('masse_horaire_max'));
        }

        // Tri par défaut par nom
        $query->orderBy('nom', 'asc');

        // Pagination
        $modules = $query->paginate(10);

        // Récupérer seulement les formations et métiers de cet établissement pour les filtres
        $formations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('titre')->get();
        
        // Pour les métiers, on récupère ceux associés aux modules de l'établissement
        $metiers = Metier::whereHas('modules.formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->orderBy('nom')->get();

        return view('administrationetablissement.modules.index', compact('modules', 'formations', 'metiers'));
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

        // Récupérer seulement les formations de cet établissement
        $formations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('titre')->get();
        
        // Récupérer seulement les métiers liés à cet établissement
        $metiers = Metier::whereHas('modules.formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->orWhereDoesntHave('modules') // Inclure les métiers qui n'ont pas encore de modules
            ->orderBy('nom')->get();
        
        return view('administrationetablissement.modules.create', compact('formations', 'metiers'));
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
            'nom' => 'required|string|max:255',
            'masse_horaire' => 'required|integer|min:1',
            'formations' => 'required|array|min:1',
            'formations.*' => 'exists:formations,id',
            'metiers' => 'nullable|array',
            'metiers.*' => 'exists:metiers,id',
        ]);

        // Vérifier que toutes les formations sélectionnées appartiennent à cet établissement
        $formationsEtablissement = Formation::where('etablissement_id', $etablissement->id)
            ->whereIn('id', $request->formations)
            ->pluck('id');

        if ($formationsEtablissement->count() !== count($request->formations)) {
            return back()->withErrors(['formations' => 'Certaines formations ne sont pas de votre établissement.']);
        }

        // Créer le module
        $module = Module::create([
            'nom' => $request->nom,
            'masse_horaire' => $request->masse_horaire,
        ]);

        // Associer les formations
        $module->formations()->attach($request->formations);

        // Associer les métiers s'ils sont sélectionnés
        if ($request->filled('metiers')) {
            $module->metiers()->attach($request->metiers);
        }

        return redirect()->route('administrationetablissement.modules.index')
                        ->with('success', 'Module créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le module appartient à une formation de cet établissement
        $moduleEtablissement = $module->formations()
            ->where('etablissement_id', $etablissement->id)
            ->exists();

        if (!$moduleEtablissement) {
            abort(403, 'Ce module ne fait pas partie de votre établissement.');
        }

        $module->load(['formations', 'metiers']);
        return view('administrationetablissement.modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le module appartient à une formation de cet établissement
        $moduleEtablissement = $module->formations()
            ->where('etablissement_id', $etablissement->id)
            ->exists();

        if (!$moduleEtablissement) {
            abort(403, 'Ce module ne fait pas partie de votre établissement.');
        }

        // Récupérer seulement les formations de cet établissement
        $formations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('titre')->get();
        
        // Récupérer seulement les métiers liés à cet établissement
        $metiers = Metier::whereHas('modules.formations', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id);
            })
            ->orWhereDoesntHave('modules') // Inclure les métiers qui n'ont pas encore de modules
            ->orderBy('nom')->get();
        $module->load(['formations', 'metiers']);
        
        return view('administrationetablissement.modules.edit', compact('module', 'formations', 'metiers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le module appartient à une formation de cet établissement
        $moduleEtablissement = $module->formations()
            ->where('etablissement_id', $etablissement->id)
            ->exists();

        if (!$moduleEtablissement) {
            abort(403, 'Ce module ne fait pas partie de votre établissement.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'masse_horaire' => 'required|integer|min:1',
            'formations' => 'required|array|min:1',
            'formations.*' => 'exists:formations,id',
            'metiers' => 'nullable|array',
            'metiers.*' => 'exists:metiers,id',
        ]);

        // Vérifier que toutes les formations sélectionnées appartiennent à cet établissement
        $formationsEtablissement = Formation::where('etablissement_id', $etablissement->id)
            ->whereIn('id', $request->formations)
            ->pluck('id');

        if ($formationsEtablissement->count() !== count($request->formations)) {
            return back()->withErrors(['formations' => 'Certaines formations ne sont pas de votre établissement.']);
        }

        // Mettre à jour le module
        $module->update([
            'nom' => $request->nom,
            'masse_horaire' => $request->masse_horaire,
        ]);

        // Synchroniser les formations
        $module->formations()->sync($request->formations);

        // Synchroniser les métiers
        $module->metiers()->sync($request->metiers ?? []);

        return redirect()->route('administrationetablissement.modules.index')
                        ->with('success', 'Module mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas associé à un établissement.');
        }

        // Vérifier que le module appartient à une formation de cet établissement
        $moduleEtablissement = $module->formations()
            ->where('etablissement_id', $etablissement->id)
            ->exists();

        if (!$moduleEtablissement) {
            abort(403, 'Ce module ne fait pas partie de votre établissement.');
        }

        // Les relations many-to-many seront automatiquement supprimées grâce à onDelete('cascade')
        $module->delete();

        return redirect()->route('administrationetablissement.modules.index')
                        ->with('success', 'Module supprimé avec succès.');
    }
}