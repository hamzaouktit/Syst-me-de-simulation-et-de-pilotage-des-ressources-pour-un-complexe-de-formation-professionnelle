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

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Formation::with('etablissement');

        // Filtres
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('etablissement_id')) {
            $query->byEtablissement($request->etablissement_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('niveau', 'LIKE', '%' . $request->search . '%');
            });
        }

        $formations = $query->orderBy('titre')->paginate(15);
        $etablissements = Etablissement::orderBy('nom')->get();

        return view('administrationetablissement.formations.index', compact('formations', 'etablissements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $etablissements = Etablissement::orderBy('nom')->get();
        $types = ['initiale', 'continue', 'alternance', 'distance'];

        return view('administrationetablissement.formations.create', compact('etablissements', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'niveau' => 'required|string|max:255',
            'type' => 'required|in:initiale,continue,alternance,distance',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        Formation::create($validated);

        return redirect()->route('administrationetablissement.formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Formation $formation): View
    {
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
        $etablissements = Etablissement::orderBy('nom')->get();
        $types = ['initiale', 'continue', 'alternance', 'distance'];

        return view('administrationetablissement.formations.edit', compact('formation', 'etablissements', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formation $formation): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'niveau' => 'required|string|max:255',
            'type' => 'required|in:initiale,continue,alternance,distance',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $formation->update($validated);

        return redirect()->route('administrationetablissement.formations.index')
            ->with('success', 'Formation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation): RedirectResponse
    {
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
        $etablissementId = $request->etablissement_id;
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
        $stats = [
            'total' => Formation::count(),
            'by_type' => Formation::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_etablissement' => Formation::with('etablissement:id,nom')
                ->selectRaw('etablissement_id, COUNT(*) as count')
                ->groupBy('etablissement_id')
                ->get()
                ->pluck('count', 'etablissement.nom'),
        ];

        return response()->json($stats);
    }
}