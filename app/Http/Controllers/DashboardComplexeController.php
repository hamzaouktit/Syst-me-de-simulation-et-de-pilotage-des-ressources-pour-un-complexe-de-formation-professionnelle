<?php

namespace App\Http\Controllers;

use App\Models\Complexe;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\User;
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
        
        return [
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
                })->count()
        ];
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

        // Ici vous pouvez implémenter la logique pour récupérer les activités récentes
        // Par exemple, les dernières formations créées, groupes ajoutés, etc.
        $activities = [
            // Exemple de structure
            [
                'type' => 'formation_created',
                'message' => 'Nouvelle formation créée',
                'timestamp' => now()->subHours(2),
                'etablissement' => 'Nom de l\'établissement'
            ]
        ];

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
        
        // Logique d'export selon le format demandé
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