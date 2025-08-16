<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\EspacePedagogique;
use App\Models\AnneeDeFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardEtablissementController extends Controller
{
    /**
     * Display the etablissement dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a le rôle approprié
        if ($user->role !== 'directeur_etablissement') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer l'établissement dirigé par cet utilisateur
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            abort(404, 'Aucun établissement associé à cet utilisateur');
        }

        // Statistiques générales de l'établissement
        $stats = $this->getEtablissementStats($etablissement);
        
        // Données pour les graphiques
        $chartsData = $this->getChartsData($etablissement);

        // Activités récentes
        $recentActivities = $this->getRecentActivities($etablissement);

        return view('administrationetablissement.dashboard', compact(
            'etablissement', 
            'stats', 
            'chartsData', 
            'recentActivities'
        ));
    }

    /**
     * Get etablissement statistics
     */
    private function getEtablissementStats($etablissement)
    {
        return [
            'total_formations' => $etablissement->formations()->count(),
            'total_groupes' => Groupe::whereHas('formation', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->count(),
            'total_modules' => Module::whereHas('formation', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->count(),
            'total_etudiants' => Groupe::whereHas('formation', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->sum('effectif'),
            'total_formateurs' => Formateur::where('etablissement_id', $etablissement->id)->count(),
            'total_espaces' => EspacePedagogique::where('etablissement_id', $etablissement->id)->count(),
            'capacite_totale_espaces' => EspacePedagogique::where('etablissement_id', $etablissement->id)->sum('capacite'),
            'masse_horaire_disponible' => Formateur::where('etablissement_id', $etablissement->id)
                ->sum('masse_horaire_disponible'),
            'masse_horaire_modules' => Module::whereHas('formation', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->sum('masse_horaire')
        ];
    }

    /**
     * Get data for dashboard charts
     */
    private function getChartsData($etablissement)
    {
        // Répartition des étudiants par formation
        $etudiantsParFormation = Formation::where('etablissement_id', $etablissement->id)
            ->with('groupes')
            ->get()
            ->map(function($formation) {
                return [
                    'name' => $formation->titre,
                    'value' => $formation->groupes->sum('effectif')
                ];
            })->toArray();

        // Répartition par type de formation
        $formationsParType = Formation::where('etablissement_id', $etablissement->id)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->type,
                    'value' => $item->count
                ];
            })->toArray();

        // Répartition des étudiants par année de formation
        $etudiantsParAnnee = AnneeDeFormation::whereHas('groupes.formation', function($query) use ($etablissement) {
            $query->where('etablissement_id', $etablissement->id);
        })
        ->with(['groupes' => function($query) use ($etablissement) {
            $query->whereHas('formation', function($subQuery) use ($etablissement) {
                $subQuery->where('etablissement_id', $etablissement->id);
            });
        }])
        ->get()
        ->map(function($annee) {
            return [
                'name' => 'Année ' . $annee->annee,
                'value' => $annee->groupes->sum('effectif')
            ];
        })->toArray();

        // Utilisation des espaces pédagogiques
        $utilisationEspaces = EspacePedagogique::where('etablissement_id', $etablissement->id)
            ->selectRaw('type, COUNT(*) as count, SUM(capacite) as capacite_totale')
            ->groupBy('type')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->type,
                    'count' => $item->count,
                    'capacite' => $item->capacite_totale
                ];
            })->toArray();

        // Évolution mensuelle des inscriptions (exemple avec données fictives)
        $evolutionInscriptions = $this->getEvolutionInscriptions($etablissement);

        return [
            'etudiants_par_formation' => $etudiantsParFormation,
            'formations_par_type' => $formationsParType,
            'etudiants_par_annee' => $etudiantsParAnnee,
            'utilisation_espaces' => $utilisationEspaces,
            'evolution_inscriptions' => $evolutionInscriptions
        ];
    }

    /**
     * Get enrollment evolution (placeholder - you may need to track this data)
     */
    /**
 * Get enrollment evolution for the establishment
 */
private function getEvolutionInscriptions($etablissement)
{
    // Get the last 6 months of enrollment data
    $data = DB::table('groupes')
        ->join('formations', 'groupes.formation_id', '=', 'formations.id')
        ->where('formations.etablissement_id', $etablissement->id)
        ->selectRaw('DATE_FORMAT(groupes.created_at, "%b") as month, SUM(groupes.effectif) as inscriptions')
        ->where('groupes.created_at', '>=', now()->subMonths(6)) // Last 6 months
        ->groupByRaw('DATE_FORMAT(groupes.created_at, "%b"), YEAR(groupes.created_at), MONTH(groupes.created_at)')
        ->orderByRaw('YEAR(groupes.created_at), MONTH(groupes.created_at)')
        ->get()
        ->map(function ($item) {
            return [
                'month' => $item->month, // Short month name (e.g., Jan, Feb)
                'inscriptions' => (int) $item->inscriptions // Total students enrolled
            ];
        })->toArray();

    // Ensure all months in the last 6 months are included, even if no data
    $months = collect(range(0, 5))->map(function ($i) {
        return now()->subMonths($i)->format('M'); // Short month name
    })->reverse()->values()->unique()->toArray();

    $result = [];
    foreach ($months as $month) {
        $found = collect($data)->firstWhere('month', $month) ?? ['month' => $month, 'inscriptions' => 0];
        $result[] = $found;
    }

    return $result;
}

    /**
     * Get recent activities for the etablissement
     */
    private function getRecentActivities($etablissement)
    {
        $activities = [];
        
        // Dernières formations créées
        $recentFormations = Formation::where('etablissement_id', $etablissement->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentFormations as $formation) {
            $activities[] = [
                'type' => 'formation_created',
                'message' => "Formation '{$formation->titre}' créée",
                'timestamp' => $formation->created_at,
                'icon' => 'book'
            ];
        }

        // Derniers groupes créés
        $recentGroupes = Groupe::whereHas('formation', function($query) use ($etablissement) {
            $query->where('etablissement_id', $etablissement->id);
        })
        ->with('formation')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
        
        foreach ($recentGroupes as $groupe) {
            $activities[] = [
                'type' => 'groupe_created',
                'message' => "Groupe '{$groupe->nom}' créé pour {$groupe->formation->titre}",
                'timestamp' => $groupe->created_at,
                'icon' => 'users'
            ];
        }

        // Derniers formateurs ajoutés
        $recentFormateurs = Formateur::where('etablissement_id', $etablissement->id)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
            
        foreach ($recentFormateurs as $formateur) {
            $activities[] = [
                'type' => 'formateur_added',
                'message' => "Formateur '{$formateur->nom}' ajouté",
                'timestamp' => $formateur->created_at,
                'icon' => 'user-plus'
            ];
        }

        // Trier par timestamp décroissant et limiter à 10
        return collect($activities)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Get detailed statistics for API calls
     */
    public function getDetailedStats(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return response()->json(['error' => 'Aucun établissement associé'], 404);
        }

        $period = $request->get('period', 'month'); // week, month, year
        
        $stats = [
            'formations_stats' => $this->getFormationsStats($etablissement, $period),
            'formateurs_stats' => $this->getFormateursStats($etablissement, $period),
            'espaces_stats' => $this->getEspacesStats($etablissement, $period)
        ];

        return response()->json($stats);
    }

    private function getFormationsStats($etablissement, $period)
    {
        return Formation::where('etablissement_id', $etablissement->id)
            ->selectRaw('
                COUNT(*) as total,
                AVG(
                    (SELECT SUM(effectif) FROM groupes WHERE groupes.formation_id = formations.id)
                ) as moyenne_etudiants
            ')
            ->first();
    }

    private function getFormateursStats($etablissement, $period)
    {
        return Formateur::where('etablissement_id', $etablissement->id)
            ->selectRaw('
                COUNT(*) as total,
                AVG(masse_horaire_disponible) as moyenne_masse_horaire,
                SUM(masse_horaire_disponible) as total_masse_horaire
            ')
            ->first();
    }

    private function getEspacesStats($etablissement, $period)
    {
        return EspacePedagogique::where('etablissement_id', $etablissement->id)
            ->selectRaw('
                COUNT(*) as total,
                AVG(capacite) as capacite_moyenne,
                SUM(capacite) as capacite_totale
            ')
            ->first();
    }

    /**
     * Export etablissement data
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            abort(404, 'Aucun établissement associé');
        }

        $format = $request->get('format', 'pdf');
        $type = $request->get('type', 'summary'); // summary, detailed, formations, etc.
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($etablissement, $type);
            case 'excel':
                return $this->exportToExcel($etablissement, $type);
            default:
                abort(400, 'Format non supporté');
        }
    }

    private function exportToPdf($etablissement, $type)
    {
        // Implémentation de l'export PDF
        // Vous devrez installer et configurer une librairie comme DomPDF
    }

    private function exportToExcel($etablissement, $type)
    {
        // Implémentation de l'export Excel
        // Vous devrez installer et configurer Laravel Excel
    }
}