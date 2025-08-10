<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::with('formation')->get();
        return view('administrationetablissement.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formations = Formation::all();
        return view('administrationetablissement.modules.create', compact('formations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'masse_horaire' => 'required|integer|min:1',
            'formation_id' => 'required|exists:formations,id',
        ]);

        Module::create([
            'nom' => $request->nom,
            'masse_horaire' => $request->masse_horaire,
            'formation_id' => $request->formation_id,
        ]);

        return redirect()->route('administrationetablissement.modules.index')
                        ->with('success', 'Module créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        $module->load('formation');
        return view('administrationetablissement.modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        $formations = Formation::all();
        return view('administrationetablissement.modules.edit', compact('module', 'formations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'masse_horaire' => 'required|integer|min:1',
            'formation_id' => 'required|exists:formations,id',
        ]);

        $module->update([
            'nom' => $request->nom,
            'masse_horaire' => $request->masse_horaire,
            'formation_id' => $request->formation_id,
        ]);

        return redirect()->route('administrationetablissement.modules.index')
                        ->with('success', 'Module mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('administrationetablissement.modules.index')
                        ->with('success', 'Module supprimé avec succès.');
    }
}