`<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DirecteurController;
use App\Http\Controllers\EtablissementController;
use Illuminate\Support\Facades\Route;
Route::fallback(function () {
    return redirect()->route('login'); 
});
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboards
Route::get('/administrationcomplexe/dashboard', function () {
    return view('administrationcomplexe.dashboard');
})->middleware('auth')->name('dashboard.complexe');

Route::get('/administrationetablissement/dashboard', function () {
    return view('administrationetablissement.dashboard');
})->middleware('auth')->name('dashboard.etablissement');
Route::prefix('administrationcomplexe')->middleware('auth')->name('administrationcomplexe.')->group(function () {
    Route::resource('directeurs', DirecteurController::class);
    Route::resource('etablissements', EtablissementController::class);
});



Route::prefix('administrationetablissement')->name('administrationetablissement.')->middleware('auth')->group(function () {
    Route::resource('formations', App\Http\Controllers\AdministrationEtablissement\FormationController::class);
    Route::resource('espaces', \App\Http\Controllers\AdministrationEtablissement\EspacePedagogiqueController::class);
    Route::resource('anneesdeformations', \App\Http\Controllers\AdministrationEtablissement\AnneeDeFormationController::class);
    Route::resource('groupes', \App\Http\Controllers\AdministrationEtablissement\GroupeController::class);
    Route::resource('modules', \App\Http\Controllers\AdministrationEtablissement\ModuleController::class);
});