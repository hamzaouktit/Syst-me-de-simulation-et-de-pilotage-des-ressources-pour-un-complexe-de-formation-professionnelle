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
    public function index()
    {
        $etablissement_id = $this->getEtablissementId();
        $formateurs = Formateur::with(['etablissement', 'metiers'])
                                ->where('etablissement_id', $etablissement_id)
                                ->paginate(10);
        
        return view('administrationetablissement.formateurs.index', compact('formateurs'));
    }

    public function create()
    {
        $etablissement_id = $this->getEtablissementId();
        $metiers = Metier::all();
        
        return view('administrationetablissement.formateurs.create', compact('metiers', 'etablissement_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:formateurs,email',
            'masse_horaire_disponible' => 'required|integer|min:0',
            'metiers' => 'array',
            'metiers.*' => 'exists:metiers,id'
        ]);

        $etablissement_id = $this->getEtablissementId();

        $formateur = Formateur::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'masse_horaire_disponible' => $request->masse_horaire_disponible,
            'etablissement_id' => $etablissement_id
        ]);

        if ($request->has('metiers')) {
            $formateur->metiers()->attach($request->metiers);
        }

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
        $metiers = Metier::all();
        
        return view('administrationetablissement.formateurs.edit', compact('formateur', 'metiers'));
    }

    public function update(Request $request, Formateur $formateur)
    {
        $this->checkEtablissementAccess($formateur);

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:formateurs,email,' . $formateur->id,
            'masse_horaire_disponible' => 'required|integer|min:0',
            'metiers' => 'array',
            'metiers.*' => 'exists:metiers,id'
        ]);

        $formateur->update([
            'nom' => $request->nom,
            'email' => $request->email,
            'masse_horaire_disponible' => $request->masse_horaire_disponible
        ]);

        $formateur->metiers()->sync($request->metiers ?? []);

        return redirect()->route('administrationetablissement.formateurs.index')
                        ->with('success', 'Formateur mis à jour avec succès.');
    }

    public function destroy(Formateur $formateur)
    {
        $this->checkEtablissementAccess($formateur);
        
        $formateur->metiers()->detach();
        $formateur->delete();

        return redirect()->route('administrationetablissement.formateurs.index')
                        ->with('success', 'Formateur supprimé avec succès.');
    }

    private function getEtablissementId()
    {
        // Assumant que l'utilisateur connecté a un établissement_id
        // Vous devrez adapter selon votre logique d'authentification
        return Auth::user()->etablissement_id ?? 1;
    }

    private function checkEtablissementAccess(Formateur $formateur)
    {
        if ($formateur->etablissement_id !== $this->getEtablissementId()) {
            abort(403, 'Accès non autorisé');
        }
    }
}