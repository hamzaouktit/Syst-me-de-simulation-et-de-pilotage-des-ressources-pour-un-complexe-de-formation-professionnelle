<?php

use App\Http\Controllers\AuthController;
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