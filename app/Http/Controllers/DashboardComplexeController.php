<?php

namespace App\Http\Controllers;

use App\Models\Complexe;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\User;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardComplexeController extends Controller
{
    /**
     * Display the complexe dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a le rôle approprié
        if ($user->role !== 'directeur_complexe') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer le complexe dirigé par cet utilisateur
        $complexe = $user->complexe;
        
        if (!$complexe) {
            abort(404, 'Aucun complexe associé à cet utilisateur');
        }

        // Statistiques générales du complexe
        $stats = $this->getComplexeStats($complexe);
        
        // Données pour les graphiques
        $chartsData = $this->getChartsData($complexe);

        return view('administrationcomplexe.dashboard', compact('complexe', 'stats', 'chartsData'));
    }

    /**
     * Get complexe statistics
     */
    private function getComplexeStats($complexe)
    {
        $etablissements = $complexe->etablissements;
        $stats = [
            'total_etablissements' => $etablissements->count(),
            'total_formations' => Formation::whereIn('etablissement_id', $etablissements->pluck('id'))->count(),
            'total_groupes' => Groupe::whereHas('formation', function($query) use ($etablissements) {
                $query->whereIn('etablissement_id', $etablissements->pluck('id'));
            })->count(),
            'total_etudiants' => Groupe::whereHas('formation', function($query) use ($etablissements) {
                $query->whereIn('etablissement_id', $etablissements->pluck('id'));
            })->sum('effectif'),
            'directeurs_etablissement' => User::where('role', 'directeur_etablissement')
                ->whereHas('etablissement', function($query) use ($complexe) {
                    $query->where('complexe_id', $complexe->id);
                })->count(),
            'etablissements_avec_alertes' => 0,
            'nombre_alertes_critiques' => 0,
            'nombre_alertes_avertissements' => 0
        ];

        // Check for issues in each establishment
        $etablissementsAvecAlertes = [];
        
        foreach ($etablissements as $etablissement) {
            $issues = $this->checkEtablissementIssues($etablissement);
            
            if (!empty($issues)) {
                $etablissementsAvecAlertes[] = [
                    'etablissement' => $etablissement,
                    'issues' => $issues,
                    'has_critical' => collect($issues)->contains('type', 'danger'),
                    'issues_count' => count($issues)
                ];
                
                $stats['etablissements_avec_alertes']++;
                $stats['nombre_alertes_critiques'] += collect($issues)->where('type', 'danger')->count();
                $stats['nombre_alertes_avertissements'] += collect($issues)->where('type', 'warning')->count();
            }
        }
        
        $stats['etablissements_alertes'] = $etablissementsAvecAlertes;
        
        return $stats;
    }
    
    /**
     * Check for issues in an establishment
     */
    private function checkEtablissementIssues($etablissement)
    {
        $issues = [];
        
        // Check for missing trainers
        $totalFormateurs = $etablissement->formateurs()->count();
        if ($totalFormateurs == 0) {
            $issues[] = [
                'type' => 'danger',
                'message' => 'Aucun formateur enregistré',
                'icon' => 'fa-user-tie'
            ];
        }
        
        // Check for missing pedagogical spaces
        $totalEspaces = $etablissement->espacesPedagogiques()->count();
        if ($totalEspaces == 0) {
            $issues[] = [
                'type' => 'danger',
                'message' => 'Aucun espace pédagogique enregistré',
                'icon' => 'fa-chalkboard'
            ];
        }
        
        // Check for insufficient teaching hours
        $masseHoraireDisponible = $etablissement->formateurs()->sum('masse_horaire_disponible');
        
        // CORRECTION: Utiliser 'formations' au lieu de 'formation'
        $masseHoraireModules = Module::whereHas('formations', function($query) use ($etablissement) {
            $query->where('etablissement_id', $etablissement->id);
        })->sum('masse_horaire');
        
        if ($masseHoraireDisponible < $masseHoraireModules) {
            $deficit = $masseHoraireModules - $masseHoraireDisponible;
            $issues[] = [
                'type' => 'warning',
                'message' => "Déficit de $deficit heures de formation",
                'icon' => 'fa-clock'
            ];
        }
        
        // Check formations for minimum hours (910h/year)
        $formations = $etablissement->formations()->with('modules')->get();
        foreach ($formations as $formation) {
            $totalHours = $formation->modules->sum('masse_horaire');
            if ($totalHours < 910) {
                $missingHours = 910 - $totalHours;
                $issues[] = [
                    'type' => 'warning',
                    'message' => "Formation '{$formation->titre}': {$missingHours}h manquantes (min 910h/an)",
                    'icon' => 'fa-graduation-cap'
                ];
            }
        }
        
        return $issues;
    }

    /**
     * Get data for dashboard charts
     */
    private function getChartsData($complexe)
    {
        $etablissements = $complexe->etablissements()->with(['formations.groupes'])->get();
        
        // Répartition des formations par établissement
        $formationsParEtablissement = [];
        foreach ($etablissements as $etablissement) {
            $formationsParEtablissement[] = [
                'name' => $etablissement->nom,
                'value' => $etablissement->formations->count()
            ];
        }

        // Répartition des étudiants par établissement
        $etudiantsParEtablissement = [];
        foreach ($etablissements as $etablissement) {
            $totalEtudiants = 0;
            foreach ($etablissement->formations as $formation) {
                $totalEtudiants += $formation->groupes->sum('effectif');
            }
            $etudiantsParEtablissement[] = [
                'name' => $etablissement->nom,
                'value' => $totalEtudiants
            ];
        }

        // Répartition par type de formation
        $formationsParType = Formation::whereIn('etablissement_id', $etablissements->pluck('id'))
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->type,
                    'value' => $item->count
                ];
            })->toArray();

        return [
            'formations_par_etablissement' => $formationsParEtablissement,
            'etudiants_par_etablissement' => $etudiantsParEtablissement,
            'formations_par_type' => $formationsParType
        ];
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities()
    {
        $user = Auth::user();
        $complexe = $user->complexe;
        
        if (!$complexe) {
            return response()->json(['activities' => []]);
        }

        // Exemple d'activités récentes - vous pouvez adapter selon vos besoins
        $activities = [];
        
        // Récupérer les dernières formations créées dans le complexe
        $recentFormations = Formation::whereIn('etablissement_id', $complexe->etablissements->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->with('etablissement')
            ->get();
            
        foreach ($recentFormations as $formation) {
            $activities[] = [
                'type' => 'formation_created',
                'message' => "Formation '{$formation->titre}' créée à {$formation->etablissement->nom}",
                'timestamp' => $formation->created_at,
                'etablissement' => $formation->etablissement->nom
            ];
        }

        return response()->json(['activities' => $activities]);
    }

    /**
     * Export complexe data
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $complexe = $user->complexe;
        
        if (!$complexe) {
            abort(404, 'Aucun complexe associé');
        }

        $format = $request->get('format', 'pdf');
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($complexe);
            case 'excel':
                return $this->exportToExcel($complexe);
            default:
                abort(400, 'Format non supporté');
        }
    }

    private function exportToPdf($complexe)
    {
        // Implémentation de l'export PDF
        // Vous devrez installer et configurer une librairie comme DomPDF ou TCPDF
    }

    private function exportToExcel($complexe)
    {
        // Implémentation de l'export Excel
        // Vous devrez installer et configurer Laravel Excel
    }
}