<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\EspacePedagogique;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EspacePedagogiqueController extends Controller
{
    public function index()
    {
        // Récupérer l'établissement du directeur connecté
        $etablissement = Etablissement::where('user_id', Auth::id())->firstOrFail();

        $espaces = EspacePedagogique::where('etablissement_id', $etablissement->id)->get();

        return view('administrationetablissement.espaces.index', compact('espaces'));
    }

    public function create()
    {
        return view('administrationetablissement.espaces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'couvertureHoraireMax' => 'required|integer|min:1|max:60',
        ],
        [
            'nom.required' => 'Le nom de l\'espace est obligatoire.',
            'type.required' => 'Le type d\'espace est obligatoire.',
            'capacite.required' => 'La capacité de l\'espace est obligatoire.',
            'couvertureHoraireMax.required' => 'La couverture horaire maximale est obligatoire.',
            'couvertureHoraireMax.max' => 'La couverture horaire maximale ne peut pas dépasser 60 heures.',
            'capacite.min' => 'La capacité doit être au moins de 1.',
        ]);

        $etablissement = Etablissement::where('user_id', Auth::id())->firstOrFail();

        EspacePedagogique::create([
            'nom' => $request->nom,
            'type' => $request->type,
            'capacite' => $request->capacite,
            'couvertureHoraireMax' => $request->couvertureHoraireMax,
            'etablissement_id' => $etablissement->id,
        ]);

        return redirect()->route('espaces.index')->with('success', 'Espace pédagogique créé avec succès.');
    }

    public function show(EspacePedagogique $espace)
    {
        return view('administrationetablissement.espaces.show', compact('espace'));
    }

    public function edit(EspacePedagogique $espace)
    {
        return view('administrationetablissement.espaces.edit', compact('espace'));
    }

    public function update(Request $request, EspacePedagogique $espace)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'couvertureHoraireMax' => 'required|integer|min:1|max:60',
        ],
        [
            'nom.required' => 'Le nom de l\'espace est obligatoire.',
            'type.required' => 'Le type d\'espace est obligatoire.',
            'capacite.required' => 'La capacité de l\'espace est obligatoire.',
            'couvertureHoraireMax.required' => 'La couverture horaire maximale est obligatoire.',
            'couvertureHoraireMax.max' => 'La couverture horaire maximale ne peut pas dépasser 60 heures.',
            'capacite.min' => 'La capacité doit être au moins de 1.',
        ]);

        $espace->update($request->all());

        return redirect()->route('espaces.index')->with('success', 'Espace pédagogique modifié avec succès.');
    }

    public function destroy(EspacePedagogique $espace)
    {
        $espace->delete();

        return redirect()->route('espaces.index')->with('success', 'Espace pédagogique supprimé avec succès.');
    }
}