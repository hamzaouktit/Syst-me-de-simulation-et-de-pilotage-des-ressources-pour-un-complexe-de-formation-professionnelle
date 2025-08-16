<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DirecteurController extends Controller
{
    // Afficher la liste des directeurs avec recherche et pagination
    public function index(Request $request)
    {
        $query = User::where('role', 'directeur_etablissement')
                    ->with('etablissement'); // Charger la relation établissement
        
        // Recherche par nom ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Tri des résultats
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nom_asc':
                    $query->orderBy('nom', 'asc');
                    break;
                case 'nom_desc':
                    $query->orderBy('nom', 'desc');
                    break;
                case 'email_asc':
                    $query->orderBy('email', 'asc');
                    break;
                case 'email_desc':
                    $query->orderBy('email', 'desc');
                    break;
                case 'created_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            // Tri par défaut
            $query->orderBy('created_at', 'desc');
        }
        
        // Pagination avec 10 éléments par page
        $directeurs = $query->paginate(10);
        
        return view('administrationcomplexe.directeurs.index', compact('directeurs'));
    }

    // Formulaire création
    public function create()
    {
        return view('administrationcomplexe.directeurs.create');
    }

    // Enregistrer nouveau directeur
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'directeur_etablissement',
        ]);

        return redirect()->route('administrationcomplexe.directeurs.index')
                         ->with('success', 'Directeur créé avec succès.');
    }

    // Afficher un directeur
    public function show($id)
    {
        $directeur = User::where('role', 'directeur_etablissement')
                        ->with('etablissement')
                        ->findOrFail($id);
        return view('administrationcomplexe.directeurs.show', compact('directeur'));
    }

    // Formulaire modification
    public function edit($id)
    {
        $directeur = User::where('role', 'directeur_etablissement')
                        ->with('etablissement')
                        ->findOrFail($id);
        return view('administrationcomplexe.directeurs.edit', compact('directeur'));
    }

    // Mettre à jour directeur
    public function update(Request $request, $id)
    {
        $directeur = User::where('role', 'directeur_etablissement')->findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$directeur->id,
            'password' => 'nullable|min:6',
        ]);

        $directeur->nom = $request->nom;
        $directeur->email = $request->email;
        if ($request->filled('password')) {
            $directeur->password = Hash::make($request->password);
        }
        $directeur->save();

        return redirect()->route('administrationcomplexe.directeurs.index')
                         ->with('success', 'Directeur modifié avec succès.');
    }

    // Supprimer directeur
    public function destroy($id)
    {
        $directeur = User::where('role', 'directeur_etablissement')->findOrFail($id);
        
        // Vérifier si le directeur a un établissement associé
        if ($directeur->etablissement) {
            return redirect()->route('administrationcomplexe.directeurs.index')
                           ->with('error', 'Impossible de supprimer ce directeur car il est associé à un établissement.');
        }
        
        $directeur->delete();

        return redirect()->route('administrationcomplexe.directeurs.index')
                         ->with('success', 'Directeur supprimé avec succès.');
    }
}