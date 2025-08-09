<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DirecteurController extends Controller
{
    // Afficher la liste des directeurs
    public function index()
    {
        $directeurs = User::where('role', 'directeur_etablissement')->get();
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
        $directeur = User::where('role', 'directeur_etablissement')->findOrFail($id);
        return view('administrationcomplexe.directeurs.show', compact('directeur'));
    }

    // Formulaire modification
    public function edit($id)
    {
        $directeur = User::where('role', 'directeur_etablissement')->findOrFail($id);
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
        $directeur->delete();

        return redirect()->route('administrationcomplexe.directeurs.index')
                         ->with('success', 'Directeur supprimé avec succès.');
    }
}
