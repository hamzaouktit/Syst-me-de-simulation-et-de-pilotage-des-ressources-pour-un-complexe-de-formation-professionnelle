<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Etablissement;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\AnneeFormation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $etablissement_id = $this->getEtablissementId();
        
        // Construction de la requête de base - FILTRER PAR ÉTABLISSEMENT DU DIRECTEUR
        $query = Formation::with('etablissement')
                          ->where('etablissement_id', $etablissement_id);

        // Filtres additionnels
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Note: Retirer le filtre par etablissement_id car on filtre déjà
        // if ($request->filled('etablissement_id')) {
        //     $query->byEtablissement($request->etablissement_id);
        // }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('niveau', 'LIKE', '%' . $request->search . '%');
            });
        }

        $formations = $query->orderBy('titre')->paginate(15);
        
        // Récupérer seulement l'établissement du directeur connecté
        $etablissement = $this->getEtablissement();

        return view('administrationetablissement.formations.index', compact('formations', 'etablissement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $etablissement_id = $this->getEtablissementId();
        $etablissement = $this->getEtablissement();
        $types = ['initiale', 'continue', 'alternance', 'distance'];

        return view('administrationetablissement.formations.create', compact('etablissement', 'etablissement_id', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $etablissement_id = $this->getEtablissementId();

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'niveau' => 'required|string|max:255',
            'type' => 'required|in:initiale,continue,alternance,distance',
        ]);

        // Forcer l'établissement du directeur connecté
        $validated['etablissement_id'] = $etablissement_id;

        Formation::create($validated);

        return redirect()->route('administrationetablissement.formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Formation $formation): View
    {
        $this->checkEtablissementAccess($formation);

        // Charger les relations avec des sous-relations pour optimiser les requêtes
        $formation->load([
            'etablissement',
            'groupes' => function ($query) {
                $query->with('anneeDeFormation');
            },
            'modules',
            'anneesFormation'
        ]);

        return view('administrationetablissement.formations.show', compact('formation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formation $formation): View
    {
        $this->checkEtablissementAccess($formation);
        
        $etablissement = $this->getEtablissement();
        $types = ['initiale', 'continue', 'alternance', 'distance'];

        return view('administrationetablissement.formations.edit', compact('formation', 'etablissement', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formation $formation): RedirectResponse
    {
        $this->checkEtablissementAccess($formation);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'niveau' => 'required|string|max:255',
            'type' => 'required|in:initiale,continue,alternance,distance',
        ]);

        // Pas besoin de permettre de changer l'établissement
        // $validated['etablissement_id'] reste celui du directeur

        $formation->update($validated);

        return redirect()->route('administrationetablissement.formations.index')
            ->with('success', 'Formation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation): RedirectResponse
    {
        $this->checkEtablissementAccess($formation);

        try {
            $formation->delete();
            return redirect()->route('administrationetablissement.formations.index')
                ->with('success', 'Formation supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('administrationetablissement.formations.index')
                ->with('error', 'Impossible de supprimer cette formation. Elle est peut-être liée à d\'autres données.');
        }
    }

    /**
     * API method to get formations by etablissement
     */
    public function getByEtablissement(Request $request)
    {
        $etablissementId = $this->getEtablissementId();
        
        // Retourner seulement les formations de l'établissement du directeur connecté
        $formations = Formation::where('etablissement_id', $etablissementId)
            ->orderBy('titre')
            ->get(['id', 'titre', 'niveau', 'type']);

        return response()->json($formations);
    }

    /**
     * API method to get formation statistics
     */
    public function statistics()
    {
        $etablissementId = $this->getEtablissementId();
        
        // Statistiques limitées à l'établissement du directeur
        $stats = [
            'total' => Formation::where('etablissement_id', $etablissementId)->count(),
            'by_type' => Formation::where('etablissement_id', $etablissementId)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
        ];

        return response()->json($stats);
    }

    private function getEtablissementId()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Utilisateur non authentifié');
        }
        
        // Récupérer l'établissement dirigé par cet utilisateur
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas directeur d\'un établissement');
        }
        
        return $etablissement->id;
    }

    private function getEtablissement()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Utilisateur non authentifié');
        }
        
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            abort(403, 'Vous n\'êtes pas directeur d\'un établissement');
        }
        
        return $etablissement;
    }

    private function checkEtablissementAccess(Formation $formation)
    {
        if ($formation->etablissement_id !== $this->getEtablissementId()) {
            abort(403, 'Accès non autorisé à cette formation');
        }
    }
}