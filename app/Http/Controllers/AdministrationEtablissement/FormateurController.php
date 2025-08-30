<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\Etablissement;
use App\Models\Metier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormateurController extends Controller
{
    public function index(Request $request)
    {
        $etablissement_id = $this->getEtablissementId();
        
        // Construction de la requête de base
        $query = Formateur::with(['etablissement', 'metiers'])
                          ->where('etablissement_id', $etablissement_id);

        // Recherche par nom
        if ($request->filled('search_nom')) {
            $query->where('nom', 'like', '%' . $request->search_nom . '%');
        }

        // Recherche par email
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->search_email . '%');
        }

        // Filtrage par métier
        if ($request->filled('search_metier')) {
            $query->whereHas('metiers', function($q) use ($request) {
                $q->where('metier_id', $request->search_metier);
            });
        }

        // Filtrage par masse horaire
        if ($request->filled('search_masse_horaire_min')) {
            $query->where('masse_horaire_disponible', '>=', $request->search_masse_horaire_min);
        }

        if ($request->filled('search_masse_horaire_max')) {
            $query->where('masse_horaire_disponible', '<=', $request->search_masse_horaire_max);
        }

        // Tri
        $sort_by = $request->get('sort_by', 'nom');
        $sort_direction = $request->get('sort_direction', 'asc');
        
        $valid_sort_columns = ['nom', 'email', 'masse_horaire_disponible', 'created_at'];
        if (in_array($sort_by, $valid_sort_columns)) {
            $query->orderBy($sort_by, $sort_direction);
        }

        $formateurs = $query->paginate(10)->withQueryString();
        
        // Récupérer tous les métiers pour le filtre
        $metiers = Metier::orderBy('nom')->get();
        
        return view('administrationetablissement.formateurs.index', compact('formateurs', 'metiers'));
    }

    public function create()
    {
        $etablissement_id = $this->getEtablissementId();
        $metiers = Metier::orderBy('nom')->get();
        
        return view('administrationetablissement.formateurs.create', compact('metiers', 'etablissement_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:formateurs,email',
            'masse_horaire_disponible' => 'nullable|integer|min:0',
            'metiers' => 'required|array|min:1',
            'metiers.*' => 'exists:metiers,id'
        ]);

        $etablissement_id = $this->getEtablissementId();

        // Si masse_horaire_disponible n'est pas fournie, utiliser 910 par défaut
        $masse_horaire = $request->masse_horaire_disponible ?? 910;

        $formateur = Formateur::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'masse_horaire_disponible' => $masse_horaire,
            'etablissement_id' => $etablissement_id
        ]);

        // Attacher les métiers sélectionnés
        $formateur->metiers()->attach($request->metiers);

        return redirect()->route('administrationetablissement.formateurs.index')
                        ->with('success', 'Formateur créé avec succès.');
    }

    public function show(Formateur $formateur)
    {
        $this->checkEtablissementAccess($formateur);
        $formateur->load(['etablissement', 'metiers']);
        
        return view('administrationetablissement.formateurs.show', compact('formateur'));
    }

    public function edit(Formateur $formateur)
    {
        $this->checkEtablissementAccess($formateur);
        $metiers = Metier::orderBy('nom')->get();
        
        return view('administrationetablissement.formateurs.edit', compact('formateur', 'metiers'));
    }

    public function update(Request $request, Formateur $formateur)
    {
        $this->checkEtablissementAccess($formateur);

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:formateurs,email,' . $formateur->id,
            'masse_horaire_disponible' => 'required|integer|min:0',
            'metiers' => 'required|array|min:1',
            'metiers.*' => 'exists:metiers,id'
        ]);

        $formateur->update([
            'nom' => $request->nom,
            'email' => $request->email,
            'masse_horaire_disponible' => $request->masse_horaire_disponible
        ]);

        // Synchroniser les métiers
        $formateur->metiers()->sync($request->metiers);

        return redirect()->route('administrationetablissement.formateurs.index')
                        ->with('success', 'Formateur mis à jour avec succès.');
    }

    public function destroy(Formateur $formateur)
    {
        $this->checkEtablissementAccess($formateur);
        
        // Détacher tous les métiers avant de supprimer le formateur
        $formateur->metiers()->detach();
        $formateur->delete();

        return redirect()->route('administrationetablissement.formateurs.index')
                        ->with('success', 'Formateur supprimé avec succès.');
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
        
        \Log::info('Établissement trouvé:', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'etablissement_id' => $etablissement->id,
            'etablissement_nom' => $etablissement->nom
        ]);
        
        return $etablissement->id;
    }

    private function checkEtablissementAccess(Formateur $formateur)
    {
        if ($formateur->etablissement_id !== $this->getEtablissementId()) {
            abort(403, 'Accès non autorisé à ce formateur');
        }
    }
}