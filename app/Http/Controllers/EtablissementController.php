<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\User;
use App\Models\Complexe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtablissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Si l'utilisateur est directeur de complexe, afficher tous les établissements de son complexe
        if ($user->role === 'directeur_complexe') {
            $complexe = $user->complexe;
            $etablissements = $complexe ? $complexe->etablissements()->with(['directeur', 'complexe'])->get() : collect();
        } else {
            // Si directeur d'établissement, afficher seulement son établissement
            $etablissements = $user->etablissement ? collect([$user->etablissement->load(['directeur', 'complexe'])]) : collect();
        }
        
        return view('administrationcomplexe.etablissements.index', compact('etablissements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Seul le directeur de complexe peut créer des établissements
        if (Auth::user()->role !== 'directeur_complexe') {
            abort(403, 'Accès non autorisé.');
        }

        $user = Auth::user();
        $complexe = $user->complexe;
        
        if (!$complexe) {
            return redirect()->route('administrationcomplexe.etablissements.index')
                           ->with('error', 'Vous devez d\'abord créer un complexe.');
        }

        // Récupérer tous les directeurs d'établissement qui ne dirigent pas encore un établissement
        $directeursDisponibles = User::where('role', 'directeur_etablissement')
                                   ->whereDoesntHave('etablissement')
                                   ->get();

        return view('administrationcomplexe.etablissements.create', compact('directeursDisponibles', 'complexe'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'directeur_complexe') {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $complexe = $user->complexe;

        if (!$complexe) {
            return redirect()->route('administrationcomplexe.etablissements.index')
                           ->with('error', 'Vous devez d\'abord créer un complexe.');
        }

        // Vérifier que le directeur sélectionné n'a pas déjà un établissement
        $directeurSelectionne = User::find($request->user_id);
        if ($directeurSelectionne->etablissement) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Ce directeur dirige déjà un établissement.');
        }

        Etablissement::create([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'longitude' => $request->longitude,
            'altitude' => $request->altitude,
            'complexe_id' => $complexe->id,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('administrationcomplexe.etablissements.index')
                        ->with('success', 'Établissement créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Etablissement $etablissement)
    {
        // Vérifier l'autorisation
        $user = Auth::user();
        
        if ($user->role === 'directeur_complexe') {
            // Le directeur de complexe peut voir tous les établissements de son complexe
            if ($etablissement->complexe_id !== $user->complexe->id) {
                abort(403, 'Accès non autorisé.');
            }
        } else {
            // Le directeur d'établissement ne peut voir que son établissement
            if ($etablissement->user_id !== $user->id) {
                abort(403, 'Accès non autorisé.');
            }
        }

        $etablissement->load(['directeur', 'complexe']);
        
        return view('administrationcomplexe.etablissements.show', compact('etablissement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etablissement $etablissement)
    {
        // Seul le directeur de complexe peut modifier
        if (Auth::user()->role !== 'directeur_complexe') {
            abort(403, 'Accès non autorisé.');
        }

        $user = Auth::user();
        
        // Vérifier que l'établissement appartient au complexe du directeur connecté
        if ($etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Récupérer tous les directeurs d'établissement disponibles + le directeur actuel
        $directeursDisponibles = User::where('role', 'directeur_etablissement')
                                   ->where(function($query) use ($etablissement) {
                                       $query->whereDoesntHave('etablissement')
                                             ->orWhere('id', $etablissement->user_id);
                                   })
                                   ->get();

        return view('administrationcomplexe.etablissements.edit', compact('etablissement', 'directeursDisponibles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etablissement $etablissement)
    {
        if (Auth::user()->role !== 'directeur_complexe') {
            abort(403, 'Accès non autorisé.');
        }

        $user = Auth::user();
        
        if ($etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        // Vérifier que le nouveau directeur n'a pas déjà un établissement (sauf s'il s'agit du directeur actuel)
        if ($request->user_id != $etablissement->user_id) {
            $directeurSelectionne = User::find($request->user_id);
            if ($directeurSelectionne->etablissement) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Ce directeur dirige déjà un établissement.');
            }
        }

        $etablissement->update([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'longitude' => $request->longitude,
            'altitude' => $request->altitude,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('administrationcomplexe.etablissements.index')
                        ->with('success', 'Établissement modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etablissement $etablissement)
    {
        if (Auth::user()->role !== 'directeur_complexe') {
            abort(403, 'Accès non autorisé.');
        }

        $user = Auth::user();
        
        if ($etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé.');
        }

        $etablissement->delete();

        return redirect()->route('administrationcomplexe.etablissements.index')
                        ->with('success', 'Établissement supprimé avec succès.');
    }
}