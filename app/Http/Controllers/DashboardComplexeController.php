<?php

namespace App\Http\Controllers;

use App\Models\Complexe;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\User;
use App\Models\Module;
use App\Models\Formateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * Get complexe statistics with detailed analysis
     */
    private function getComplexeStats($complexe)
    {
        $etablissements = $complexe->etablissements()->with([
            'formations.modules',
            'formations.groupes',
            'formateurs.metiers.modules'
        ])->get();

        // Statistiques de base
        $totalFormations = 0;
        $totalGroupes = 0;
        $totalEtudiants = 0;
        $totalFormateurs = 0;
        $totalHeuresRequises = 0;
        $totalHeuresDisponibles = 0;

        foreach ($etablissements as $etablissement) {
            $totalFormations += $etablissement->formations->count();
            $totalFormateurs += $etablissement->formateurs->count();
            
            foreach ($etablissement->formations as $formation) {
                $totalGroupes += $formation->groupes->count();
                $totalEtudiants += $formation->groupes->sum('effectif');
                $totalHeuresRequises += $formation->modules->sum('masse_horaire');
            }
            
            $totalHeuresDisponibles += $etablissement->formateurs->sum('masse_horaire_disponible');
        }

        $stats = [
            'total_etablissements' => $etablissements->count(),
            'total_formations' => $totalFormations,
            'total_groupes' => $totalGroupes,
            'total_etudiants' => $totalEtudiants,
            'total_formateurs' => $totalFormateurs,
            'total_heures_requises' => $totalHeuresRequises,
            'total_heures_disponibles' => $totalHeuresDisponibles,
            'deficit_heures_global' => max(0, $totalHeuresRequises - $totalHeuresDisponibles),
            'directeurs_etablissement' => User::where('role', 'directeur_etablissement')
                ->whereHas('etablissement', function($query) use ($complexe) {
                    $query->where('complexe_id', $complexe->id);
                })->count(),
            'etablissements_avec_alertes' => 0,
            'nombre_alertes_critiques' => 0,
            'nombre_alertes_avertissements' => 0,
            'formations_incompletes' => 0,
            'etablissements_sans_formateurs' => 0
        ];

        // Analyse détaillée des problèmes par établissement
        $etablissementsAvecAlertes = [];
        
        foreach ($etablissements as $etablissement) {
            $issues = $this->analyzeEtablissementIssues($etablissement);
            
            if (!empty($issues)) {
                $criticalCount = collect($issues)->where('type', 'danger')->count();
                $warningCount = collect($issues)->where('type', 'warning')->count();
                
                $etablissementsAvecAlertes[] = [
                    'etablissement' => $etablissement,
                    'issues' => $issues,
                    'has_critical' => $criticalCount > 0,
                    'critical_count' => $criticalCount,
                    'warning_count' => $warningCount,
                    'total_issues' => count($issues),
                    'completion_percentage' => $this->calculateCompletionPercentage($etablissement, $issues)
                ];
                
                $stats['etablissements_avec_alertes']++;
                $stats['nombre_alertes_critiques'] += $criticalCount;
                $stats['nombre_alertes_avertissements'] += $warningCount;
            }
        }

        // Compter les formations incomplètes et établissements sans formateurs
        foreach ($etablissements as $etablissement) {
            if ($etablissement->formateurs->count() == 0) {
                $stats['etablissements_sans_formateurs']++;
            }
            
            foreach ($etablissement->formations as $formation) {
                $totalHeuresFormation = $formation->modules->sum('masse_horaire');
                if ($totalHeuresFormation < 910) {
                    $stats['formations_incompletes']++;
                }
            }
        }
        
        $stats['etablissements_alertes'] = $etablissementsAvecAlertes;
        $stats['taux_couverture_heures'] = $totalHeuresRequises > 0 
            ? round(($totalHeuresDisponibles / $totalHeuresRequises) * 100, 1) 
            : 100;
        
        return $stats;
    }
    
    /**
     * Analyze establishment issues with detailed calculations
     */
    private function analyzeEtablissementIssues($etablissement)
    {
        $issues = [];
        $formations = $etablissement->formations()->with('modules')->get();
        $formateurs = $etablissement->formateurs()->with('metiers.modules')->get();
        $totalFormateurs = $formateurs->count();
        $totalEspaces = $etablissement->espacesPedagogiques()->count();

        // 1. Vérification des formateurs
        if ($totalFormateurs == 0) {
            $issues[] = [
                'type' => 'danger',
                'category' => 'formateurs',
                'message' => 'Aucun formateur enregistré',
                'icon' => 'fa-user-tie',
                'priority' => 1,
                'details' => 'Établissement sans aucun formateur'
            ];
        }

        // 2. Vérification des espaces pédagogiques
        if ($totalEspaces == 0) {
            $issues[] = [
                'type' => 'danger',
                'category' => 'infrastructure',
                'message' => 'Aucun espace pédagogique enregistré',
                'icon' => 'fa-chalkboard',
                'priority' => 1,
                'details' => 'Infrastructure pédagogique manquante'
            ];
        }

        // 3. Analyse des heures de formation
        $masseHoraireDisponible = $formateurs->sum('masse_horaire_disponible');
        $masseHoraireRequise = 0;
        
        // Calcul correct des heures requises pour cet établissement
        foreach ($formations as $formation) {
            $masseHoraireRequise += $formation->modules->sum('masse_horaire');
        }
        
        if ($masseHoraireRequise > 0 && $masseHoraireDisponible < $masseHoraireRequise) {
            $deficit = $masseHoraireRequise - $masseHoraireDisponible;
            $pourcentageCouverture = round(($masseHoraireDisponible / $masseHoraireRequise) * 100, 1);
            
            $issues[] = [
                'type' => $deficit > ($masseHoraireRequise * 0.3) ? 'danger' : 'warning',
                'category' => 'heures',
                'message' => "Déficit de {$deficit}h de formation ({$pourcentageCouverture}% couvert)",
                'icon' => 'fa-clock',
                'priority' => $deficit > ($masseHoraireRequise * 0.3) ? 1 : 2,
                'details' => "Disponible: {$masseHoraireDisponible}h / Requis: {$masseHoraireRequise}h"
            ];
        }

        // 4. Analyse des formations incomplètes (minimum 910h/an)
        $formationsIncompletes = 0;
        foreach ($formations as $formation) {
            $totalHoursFormation = $formation->modules->sum('masse_horaire');
            if ($totalHoursFormation < 910) {
                $missingHours = 910 - $totalHoursFormation;
                $formationsIncompletes++;
                
                $issues[] = [
                    'type' => $missingHours > 100 ? 'danger' : 'warning',
                    'category' => 'formations',
                    'message' => "Formation '{$formation->titre}': {$missingHours}h manquantes (min 910h/an)",
                    'icon' => 'fa-graduation-cap',
                    'priority' => $missingHours > 100 ? 1 : 2,
                    'details' => "Actuel: {$totalHoursFormation}h / Minimum: 910h"
                ];
            }
        }

        // 5. Analyse de la répartition des compétences des formateurs
        if ($totalFormateurs > 0) {
            $modulesNonCouvertes = $this->findUncoveredModules($etablissement, $formateurs, $formations);
            
            if (!empty($modulesNonCouvertes)) {
                $issues[] = [
                    'type' => 'warning',
                    'category' => 'competences',
                    'message' => count($modulesNonCouvertes) . ' module(s) sans formateur qualifié',
                    'icon' => 'fa-exclamation-triangle',
                    'priority' => 2,
                    'details' => 'Modules: ' . implode(', ', array_slice($modulesNonCouvertes, 0, 3)) . (count($modulesNonCouvertes) > 3 ? '...' : '')
                ];
            }
        }

        // 6. Vérification du ratio formateurs/étudiants
        $totalEtudiants = 0;
        foreach ($formations as $formation) {
            $totalEtudiants += $formation->groupes->sum('effectif');
        }

        if ($totalEtudiants > 0 && $totalFormateurs > 0) {
            $ratioEtudiantsFormateurs = $totalEtudiants / $totalFormateurs;
            if ($ratioEtudiantsFormateurs > 25) { // Plus de 25 étudiants par formateur
                $issues[] = [
                    'type' => 'warning',
                    'category' => 'ratio',
                    'message' => "Ratio étudiants/formateurs élevé: " . round($ratioEtudiantsFormateurs, 1) . ":1",
                    'icon' => 'fa-balance-scale',
                    'priority' => 2,
                    'details' => "{$totalEtudiants} étudiants pour {$totalFormateurs} formateurs"
                ];
            }
        }

        // Trier les problèmes par priorité
        usort($issues, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $issues;
    }

    /**
     * Find modules that are not covered by available trainers
     */
    private function findUncoveredModules($etablissement, $formateurs, $formations)
    {
        $modulesRequis = collect();
        $modulesCouverts = collect();

        // Collecter tous les modules requis pour cet établissement
        foreach ($formations as $formation) {
            $modulesRequis = $modulesRequis->merge($formation->modules);
        }

        // Collecter tous les modules que les formateurs peuvent enseigner
        foreach ($formateurs as $formateur) {
            foreach ($formateur->metiers as $metier) {
                $modulesCouverts = $modulesCouverts->merge($metier->modules);
            }
        }

        // Trouver les modules non couverts
        $moduleRequireIds = $modulesRequis->pluck('id')->unique();
        $moduleCouvretIds = $modulesCouverts->pluck('id')->unique();
        
        $modulesNonCouverts = $moduleRequireIds->diff($moduleCouvretIds);
        
        // Récupérer les noms des modules non couverts
        return Module::whereIn('id', $modulesNonCouverts)->pluck('nom')->toArray();
    }

    /**
     * Calculate completion percentage for an establishment
     */
    private function calculateCompletionPercentage($etablissement, $issues)
    {
        $totalCriteria = 6; // Nombre total de critères évalués
        $problemCount = 0;

        // Compter les problèmes critiques et d'avertissement
        foreach ($issues as $issue) {
            if ($issue['type'] === 'danger') {
                $problemCount += 2; // Les problèmes critiques comptent double
            } elseif ($issue['type'] === 'warning') {
                $problemCount += 1;
            }
        }

        // Calculer le pourcentage de completion (inversé du pourcentage de problèmes)
        $maxProblems = $totalCriteria * 2; // Maximum si tous les critères sont critiques
        $completionPercentage = max(0, 100 - (($problemCount / $maxProblems) * 100));
        
        return round($completionPercentage, 1);
    }

    /**
     * Get data for dashboard charts
     */
    private function getChartsData($complexe)
    {
        $etablissements = $complexe->etablissements()->with(['formations.groupes', 'formateurs'])->get();
        
        // Répartition des formations par établissement
        $formationsParEtablissement = [];
        $heuresParEtablissement = [];
        $formateurParEtablissement = [];
        
        foreach ($etablissements as $etablissement) {
            $formationsParEtablissement[] = [
                'name' => $etablissement->nom,
                'value' => $etablissement->formations->count()
            ];
            
            // Calculer les heures par établissement
            $totalHeures = 0;
            foreach ($etablissement->formations as $formation) {
                $totalHeures += $formation->modules->sum('masse_horaire');
            }
            
            $heuresParEtablissement[] = [
                'name' => $etablissement->nom,
                'heures_requises' => $totalHeures,
                'heures_disponibles' => $etablissement->formateurs->sum('masse_horaire_disponible'),
                'deficit' => max(0, $totalHeures - $etablissement->formateurs->sum('masse_horaire_disponible'))
            ];

            $formateurParEtablissement[] = [
                'name' => $etablissement->nom,
                'value' => $etablissement->formateurs->count()
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
            'formations_par_type' => $formationsParType,
            'heures_par_etablissement' => $heuresParEtablissement,
            'formateurs_par_etablissement' => $formateurParEtablissement
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