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
use App\Models\Metier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardEtablissementController extends Controller
{
    const HEURES_FORMATEUR_PAR_AN = 910; // Heures standard par formateur par an
    const HEURES_ESPACE_PAR_SEMAINE = 60; // Heures max par espace par semaine  
    const SEMAINES_PAR_AN = 10; // 10 mois = ~40 semaines
    const HEURES_ESPACE_PAR_AN = self::HEURES_ESPACE_PAR_SEMAINE * self::SEMAINES_PAR_AN; // 600h par an
    const HEURES_FORMATION_MIN = 400; // Minimum d'heures par formation

    /**
     * Display the etablissement dashboard
     */
    public function index(Etablissement $etablissement = null)
    {
        $user = Auth::user();
        
        if (!$etablissement) {
            if ($user->role !== 'directeur_etablissement') {
                abort(403, 'Accès non autorisé');
            }

            $etablissement = $user->etablissement;
            
            if (!$etablissement) {
                abort(404, 'Aucun établissement associé à cet utilisateur');
            }
        } else {
            if ($user->role === 'directeur_etablissement' && $etablissement->id !== $user->etablissement_id) {
                abort(403, 'Accès non autorisé à ce tableau de bord');
            } elseif ($user->role === 'directeur_complexe' && $etablissement->complexe_id !== $user->complexe_id) {
                abort(403, 'Accès non autorisé à ce tableau de bord');
            }
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
     * Get etablissement statistics with enhanced warnings
     */
    private function getEtablissementStats($etablissement)
    {
        $totalFormateurs = Formateur::where('etablissement_id', $etablissement->id)->count();
        $totalEspaces = EspacePedagogique::where('etablissement_id', $etablissement->id)->count();
        $masseHoraireDisponible = Formateur::where('etablissement_id', $etablissement->id)
            ->sum('masse_horaire_disponible');
        
        // Calculs avancés des besoins
        $besoinsCalcules = $this->calculateDetailedNeeds($etablissement);
        
        // Avertissements améliorés
        $issues = $this->getEnhancedIssues($etablissement, $besoinsCalcules);
        
        return [
            'total_formations' => $etablissement->formations()->count(),
            'total_groupes' => Groupe::whereHas('formation', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->count(),
            'total_modules' => Module::whereHas('formations', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->count(),
            'total_etudiants' => Groupe::whereHas('formation', function($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->sum('effectif'),
            'total_formateurs' => $totalFormateurs,
            'total_espaces' => $totalEspaces,
            'capacite_totale_espaces' => EspacePedagogique::where('etablissement_id', $etablissement->id)->sum('capacite'),
            'masse_horaire_disponible' => $masseHoraireDisponible,
            'besoins_calcules' => $besoinsCalcules,
            'issues' => $issues
        ];
    }

    /**
     * Calculate detailed needs for the establishment - VERSION AMÉLIORÉE
     */
    private function calculateDetailedNeeds($etablissement)
    {
        $besoins = [
            'heures_totales_necessaires' => 0,
            'heures_disponibles' => 0,
            'deficit_heures' => 0,
            'formations_details' => [],
            'formateurs_details' => [],
            'espaces_requis_heures' => 0,
            'espaces_disponibles_heures' => 0,
            'deficit_espaces_heures' => 0,
            'formateurs_requis' => 0,
            'formateurs_disponibles' => 0,
            'deficit_formateurs' => 0,
            'modules_orphelins' => 0,
            'formations_sans_modules' => 0,
            'formations_sous_minimum' => 0,
            'formateurs_surcharges' => [],
            'formateurs_sous_utilises' => []
        ];

        // === 1. CALCUL DES BESOINS PAR FORMATION ===
        $formations = $etablissement->formations()->with(['modules', 'groupes'])->get();
        
        foreach ($formations as $formation) {
            $nombreGroupes = $formation->groupes->count();
            $masseHoraireFormation = $formation->modules->sum('masse_horaire');
            $heuresNecessairesFormation = $masseHoraireFormation * $nombreGroupes;
            
            $formationDetail = [
                'formation_id' => $formation->id,
                'formation_nom' => $formation->titre,
                'formation_type' => $formation->type,
                'nombre_groupes' => $nombreGroupes,
                'masse_horaire_formation' => $masseHoraireFormation,
                'heures_necessaires' => $heuresNecessairesFormation,
                'modules_count' => $formation->modules->count(),
                'etudiants_total' => $formation->groupes->sum('effectif'),
                'deficit_horaire' => max(0, self::HEURES_FORMATION_MIN - $masseHoraireFormation),
                'pourcentage_completude' => $masseHoraireFormation > 0 ? 
                    min(100, ($masseHoraireFormation / self::HEURES_FORMATION_MIN) * 100) : 0,
                'status' => $this->getFormationStatus($formation, $masseHoraireFormation, $nombreGroupes)
            ];
            
            $besoins['formations_details'][] = $formationDetail;
            $besoins['heures_totales_necessaires'] += $heuresNecessairesFormation;
            
            // Compter formations problématiques
            if ($formation->modules->count() == 0) {
                $besoins['formations_sans_modules']++;
            }
            if ($masseHoraireFormation < self::HEURES_FORMATION_MIN) {
                $besoins['formations_sous_minimum']++;
            }
        }

        // === 2. CALCUL DES RESSOURCES FORMATEURS ===
        $formateurs = Formateur::where('etablissement_id', $etablissement->id)
            ->with('metiers.modules.formations')
            ->get();

        foreach ($formateurs as $formateur) {
            $heuresUtilisees = $this->calculateFormateurHeuresUtilisees($formateur, $etablissement);
            $heuresDisponibles = $formateur->masse_horaire_disponible ?: self::HEURES_FORMATEUR_FOR_AN;
            $tauxUtilisation = $heuresDisponibles > 0 ? ($heuresUtilisees / $heuresDisponibles) * 100 : 0;
            
            $formateurDetail = [
                'formateur_id' => $formateur->id,
                'formateur_nom' => $formateur->nom,
                'heures_disponibles' => $heuresDisponibles,
                'heures_utilisees' => $heuresUtilisees,
                'heures_libres' => max(0, $heuresDisponibles - $heuresUtilisees),
                'taux_utilisation' => $tauxUtilisation,
                'metiers_count' => $formateur->metiers->count(),
                'status' => $this->getFormateurStatus($tauxUtilisation),
                'surcharge' => $heuresUtilisees > $heuresDisponibles,
                'deficit' => max(0, $heuresUtilisees - $heuresDisponibles)
            ];
            
            $besoins['formateurs_details'][] = $formateurDetail;
            $besoins['heures_disponibles'] += $heuresDisponibles;
            
            // Identifier formateurs problématiques
            if ($tauxUtilisation > 100) {
                $besoins['formateurs_surcharges'][] = $formateurDetail;
            } elseif ($tauxUtilisation < 50 && $heuresUtilisees > 0) {
                $besoins['formateurs_sous_utilises'][] = $formateurDetail;
            }
        }

        // === 3. CALCUL DU DÉFICIT GLOBAL ===
        $besoins['deficit_heures'] = max(0, $besoins['heures_totales_necessaires'] - $besoins['heures_disponibles']);
        $besoins['formateurs_requis'] = ceil($besoins['heures_totales_necessaires'] / self::HEURES_FORMATEUR_PAR_AN);
        $besoins['formateurs_disponibles'] = $formateurs->count();
        $besoins['deficit_formateurs'] = max(0, $besoins['formateurs_requis'] - $besoins['formateurs_disponibles']);

        // === 4. CALCUL DES BESOINS EN ESPACES ===
        $besoins['espaces_requis_heures'] = $besoins['heures_totales_necessaires'];
        $besoins['espaces_disponibles_heures'] = EspacePedagogique::where('etablissement_id', $etablissement->id)
            ->sum(DB::raw('COALESCE(couvertureHoraireMax, ' . self::HEURES_ESPACE_PAR_AN . ')'));
        
        $besoins['deficit_espaces_heures'] = max(0, $besoins['espaces_requis_heures'] - $besoins['espaces_disponibles_heures']);

        // === 5. MODULES ORPHELINS ===
        $besoins['modules_orphelins'] = Module::whereDoesntHave('formations', function($query) use ($etablissement) {
            $query->where('etablissement_id', $etablissement->id);
        })->count();

        return $besoins;
    }

    /**
     * Calculer les heures utilisées par un formateur
     */
    private function calculateFormateurHeuresUtilisees($formateur, $etablissement)
    {
        $heuresUtilisees = 0;
        
        foreach ($formateur->metiers as $metier) {
            foreach ($metier->modules as $module) {
                // Calculer heures pour ce module dans cet établissement
                $formations = $module->formations()->where('etablissement_id', $etablissement->id)->get();
                foreach ($formations as $formation) {
                    $nombreGroupes = $formation->groupes->count();
                    $heuresUtilisees += $module->masse_horaire * $nombreGroupes;
                }
            }
        }
        
        return $heuresUtilisees;
    }

    /**
     * Déterminer le statut d'une formation
     */
    private function getFormationStatus($formation, $masseHoraire, $nombreGroupes)
    {
        if ($formation->modules->count() == 0) {
            return 'vide';
        }
        if ($nombreGroupes == 0) {
            return 'inactive';
        }
        if ($masseHoraire < self::HEURES_FORMATION_MIN) {
            return 'incomplete';
        }
        return 'complete';
    }

    /**
     * Déterminer le statut d'un formateur
     */
    private function getFormateurStatus($tauxUtilisation)
    {
        if ($tauxUtilisation > 100) {
            return 'surcharge';
        } elseif ($tauxUtilisation > 80) {
            return 'optimal';
        } elseif ($tauxUtilisation > 50) {
            return 'normal';
        } elseif ($tauxUtilisation > 0) {
            return 'sous_utilise';
        }
        return 'inactif';
    }

    /**
     * Get enhanced issues with detailed warnings and actionable links
     */
    private function getEnhancedIssues($etablissement, $besoins)
    {
        $issues = [];
        $urgencyLevel = 1;
        
        // === 1. ALERTES CRITIQUES (Blocantes) ===
        
        // Aucun formateur
        if ($besoins['formateurs_disponibles'] == 0) {
            $issues[] = [
                'id' => 'no_formateurs',
                'urgency' => $urgencyLevel++,
                'type' => 'danger',
                'category' => 'formateurs',
                'title' => '🚨 Aucun formateur enregistré',
                'message' => 'Impossible de dispenser des formations sans formateurs.',
                'impact' => 'CRITIQUE - Établissement non opérationnel',
                'action_text' => 'Ajouter un formateur',
                'action_route' => 'administrationetablissement.formateurs.create',
                'priority_class' => 'critical-alert',
                'icon' => 'fas fa-user-times'
            ];
        }

        // Aucun espace pédagogique
        if (EspacePedagogique::where('etablissement_id', $etablissement->id)->count() == 0) {
            $issues[] = [
                'id' => 'no_espaces',
                'urgency' => $urgencyLevel++,
                'type' => 'danger',
                'category' => 'espaces',
                'title' => '🚨 Aucun espace pédagogique',
                'message' => 'Impossible de dispenser des cours sans espaces.',
                'impact' => 'CRITIQUE - Établissement non opérationnel',
                'action_text' => 'Ajouter un espace',
                'action_route' => 'administrationetablissement.espaces.create',
                'priority_class' => 'critical-alert',
                'icon' => 'fas fa-door-closed'
            ];
        }

        // === 2. ALERTES URGENTES (Déficits importants) ===
        
        // Déficit horaire majeur (>50%)
        if ($besoins['deficit_heures'] > 0) {
            $pourcentageDeficit = $besoins['heures_totales_necessaires'] > 0 
                ? ($besoins['deficit_heures'] / $besoins['heures_totales_necessaires']) * 100 
                : 0;
            
            $formateursDDeficit = ceil($besoins['deficit_heures'] / self::HEURES_FORMATEUR_PAR_AN);
            
            if ($pourcentageDeficit > 50) {
                $issues[] = [
                    'id' => 'deficit_heures_critique',
                    'urgency' => $urgencyLevel++,
                    'type' => 'danger',
                    'category' => 'planning',
                    'title' => '⚠️ Déficit horaire critique',
                    'message' => "Manque {$besoins['deficit_heures']}h ({$pourcentageDeficit}% des besoins)",
                    'impact' => "Nécessite {$formateursDDeficit} formateur(s) supplémentaire(s)",
                    'action_text' => 'Recruter des formateurs',
                    'action_route' => 'administrationetablissement.formateurs.create',
                    'priority_class' => 'urgent-alert',
                    'icon' => 'fas fa-exclamation-triangle',
                    'details' => [
                        'heures_manquantes' => $besoins['deficit_heures'],
                        'pourcentage_deficit' => round($pourcentageDeficit, 1),
                        'formateurs_requis' => $formateursDDeficit
                    ]
                ];
            } elseif ($pourcentageDeficit > 25) {
                $issues[] = [
                    'id' => 'deficit_heures_modere',
                    'urgency' => $urgencyLevel++,
                    'type' => 'warning',
                    'category' => 'planning',
                    'title' => '📊 Déficit horaire modéré',
                    'message' => "Manque {$besoins['deficit_heures']}h ({$pourcentageDeficit}% des besoins)",
                    'impact' => "Optimisation nécessaire ou recrutement d'appoint",
                    'action_text' => 'Optimiser les ressources',
                    'action_route' => 'administrationetablissement.formateurs.index',
                    'priority_class' => 'warning-alert',
                    'icon' => 'fas fa-chart-line'
                ];
            }
        }

        // Formateurs surchargés
        foreach ($besoins['formateurs_surcharges'] as $formateur) {
            $issues[] = [
                'id' => 'formateur_surcharge_' . $formateur['formateur_id'],
                'urgency' => $urgencyLevel++,
                'type' => 'danger',
                'category' => 'formateurs',
                'title' => '🔴 Formateur surchargé',
                'message' => "{$formateur['formateur_nom']}: {$formateur['heures_utilisees']}h/{$formateur['heures_disponibles']}h ({$formateur['taux_utilisation']}%)",
                'impact' => "Surcharge de {$formateur['deficit']}h - Risque de burnout",
                'action_text' => 'Réajuster la charge',
                'action_route' => 'administrationetablissement.formateurs.edit',
                'action_params' => ['formateur' => $formateur['formateur_id']],
                'priority_class' => 'urgent-alert',
                'icon' => 'fas fa-user-clock'
            ];
        }

        // === 3. ALERTES DE FORMATIONS ===
        
        // Formations sans modules
        $formationsSansModules = collect($besoins['formations_details'])
            ->where('status', 'vide')
            ->take(3);
            
        foreach ($formationsSansModules as $formation) {
            $issues[] = [
                'id' => 'formation_vide_' . $formation['formation_id'],
                'urgency' => $urgencyLevel++,
                'type' => 'warning',
                'category' => 'formations',
                'title' => '📚 Formation sans contenu',
                'message' => "'{$formation['formation_nom']}' n'a aucun module",
                'impact' => 'Formation non dispensable',
                'action_text' => 'Ajouter des modules',
                'action_route' => 'administrationetablissement.formations.show',
                'action_params' => ['formation' => $formation['formation_id']],
                'priority_class' => 'warning-alert',
                'icon' => 'fas fa-puzzle-piece'
            ];
        }

        // Formations sous le minimum horaire
        $formationsSousMinimum = collect($besoins['formations_details'])
            ->where('pourcentage_completude', '<', 80)
            ->where('pourcentage_completude', '>', 0)
            ->take(3);
            
        foreach ($formationsSousMinimum as $formation) {
            $issues[] = [
                'id' => 'formation_incomplete_' . $formation['formation_id'],
                'urgency' => $urgencyLevel++,
                'type' => 'info',
                'category' => 'formations',
                'title' => '⏱️ Formation incomplète',
                'message' => "'{$formation['formation_nom']}': {$formation['masse_horaire_formation']}h/{self::HEURES_FORMATION_MIN}h",
                'impact' => "Manque {$formation['deficit_horaire']}h pour atteindre le standard",
                'action_text' => 'Compléter la formation',
                'action_route' => 'administrationetablissement.formations.show',
                'action_params' => ['formation' => $formation['formation_id']],
                'priority_class' => 'info-alert',
                'icon' => 'fas fa-hourglass-half'
            ];
        }

        // === 4. ALERTES D'ESPACES ===
        
        if ($besoins['deficit_espaces_heures'] > 0) {
            $pourcentageCouverture = $besoins['espaces_requis_heures'] > 0 
                ? ($besoins['espaces_disponibles_heures'] / $besoins['espaces_requis_heures']) * 100 
                : 100;
            
            $espacesManquants = ceil($besoins['deficit_espaces_heures'] / self::HEURES_ESPACE_PAR_AN);
            
            $issues[] = [
                'id' => 'deficit_espaces',
                'urgency' => $urgencyLevel++,
                'type' => $pourcentageCouverture < 60 ? 'danger' : 'warning',
                'category' => 'espaces',
                'title' => '🏢 Capacité d\'espaces insuffisante',
                'message' => "Couverture: {$pourcentageCouverture}% des besoins horaires",
                'impact' => "Nécessite {$espacesManquants} espace(s) supplémentaire(s)",
                'action_text' => 'Optimiser les espaces',
                'action_route' => 'administrationetablissement.espaces.index',
                'priority_class' => $pourcentageCouverture < 60 ? 'urgent-alert' : 'warning-alert',
                'icon' => 'fas fa-building'
            ];
        }

        // === 5. ALERTES D'OPTIMISATION ===
        
        // Modules orphelins
        if ($besoins['modules_orphelins'] > 0) {
            $issues[] = [
                'id' => 'modules_orphelins',
                'urgency' => 999, // Basse priorité
                'type' => 'info',
                'category' => 'modules',
                'title' => '🔗 Modules non utilisés',
                'message' => "{$besoins['modules_orphelins']} modules ne sont assignés à aucune formation",
                'impact' => 'Ressources pédagogiques gaspillées',
                'action_text' => 'Réviser les modules',
                'action_route' => 'administrationetablissement.modules.index',
                'priority_class' => 'info-alert',
                'icon' => 'fas fa-unlink'
            ];
        }

        // Formateurs sous-utilisés
        if (count($besoins['formateurs_sous_utilises']) > 0) {
            $formateur = $besoins['formateurs_sous_utilises'][0]; // Premier formateur sous-utilisé
            $issues[] = [
                'id' => 'formateurs_sous_utilises',
                'urgency' => 998, // Basse priorité
                'type' => 'success',
                'category' => 'formateurs',
                'title' => '💡 Opportunité d\'optimisation',
                'message' => "{$formateur['formateur_nom']} n'utilise que {$formateur['taux_utilisation']}% de sa capacité",
                'impact' => "Capacité disponible: {$formateur['heures_libres']}h",
                'action_text' => 'Optimiser l\'attribution',
                'action_route' => 'administrationetablissement.formateurs.index',
                'priority_class' => 'success-alert',
                'icon' => 'fas fa-lightbulb'
            ];
        }

        // Trier par urgence
        usort($issues, function($a, $b) {
            return $a['urgency'] <=> $b['urgency'];
        });

        return $issues;
    }

    // Méthodes existantes inchangées...
    private function getChartsData($etablissement)
    {
        // Étudiants par formation
        $etudiantsParFormation = Formation::where('etablissement_id', $etablissement->id)
            ->with('groupes')
            ->get()
            ->map(function($formation) {
                return [
                    'name' => $formation->titre,
                    'value' => $formation->groupes->sum('effectif')
                ];
            })->toArray();

        // Formations par type
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

        // Étudiants par année
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

        // Utilisation des espaces
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

        // Évolution des inscriptions
        $evolutionInscriptions = $this->getEvolutionInscriptions($etablissement);

        return [
            'etudiants_par_formation' => $etudiantsParFormation,
            'formations_par_type' => $formationsParType,
            'etudiants_par_annee' => $etudiantsParAnnee,
            'utilisation_espaces' => $utilisationEspaces,
            'evolution_inscriptions' => $evolutionInscriptions
        ];
    }

    private function getEvolutionInscriptions($etablissement)
    {
        $data = DB::table('groupes')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->where('formations.etablissement_id', $etablissement->id)
            ->selectRaw('DATE_FORMAT(groupes.created_at, "%b") as month, SUM(groupes.effectif) as inscriptions')
            ->where('groupes.created_at', '>=', now()->subMonths(6))
            ->groupByRaw('DATE_FORMAT(groupes.created_at, "%b"), YEAR(groupes.created_at), MONTH(groupes.created_at)')
            ->orderByRaw('YEAR(groupes.created_at), MONTH(groupes.created_at)')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'inscriptions' => (int) $item->inscriptions
                ];
            })->toArray();

        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('M');
        })->reverse()->values()->unique()->toArray();

        $result = [];
        foreach ($months as $month) {
            $found = collect($data)->firstWhere('month', $month) ?? ['month' => $month, 'inscriptions' => 0];
            $result[] = $found;
        }

        return $result;
    }

    private function getRecentActivities($etablissement)
    {
        $activities = [];
        
        // Formations récentes
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

        // Groupes récents
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

        // Formateurs récents
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

        return collect($activities)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->toArray();
    }

    public function getDetailedStats(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return response()->json(['error' => 'Aucun établissement associé'], 404);
        }

        $besoins = $this->calculateDetailedNeeds($etablissement);
        
        return response()->json([
            'besoins_detailles' => $besoins,
            'constantes' => [
                'heures_formateur_par_an' => self::HEURES_FORMATEUR_PAR_AN,
                'heures_espace_par_an' => self::HEURES_ESPACE_PAR_AN,
                'heures_formation_min' => self::HEURES_FORMATION_MIN
            ]
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            abort(404, 'Aucun établissement associé');
        }

        $format = $request->get('format', 'pdf');
        $type = $request->get('type', 'summary');
        
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
        return response()->json(['message' => 'Export PDF en cours de développement']);
    }

    private function exportToExcel($etablissement, $type)
    {
        // Implémentation de l'export Excel
        return response()->json(['message' => 'Export Excel en cours de développement']);
    }
}