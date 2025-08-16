<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DirecteurController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\DashboardComplexeController;
use App\Http\Controllers\AdministrationEtablissement\DashboardEtablissementController;
use App\Http\Controllers\AdministrationEtablissement\FormationController;
use App\Http\Controllers\AdministrationEtablissement\EspacePedagogiqueController;
use App\Http\Controllers\AdministrationEtablissement\AnneeDeFormationController;
use App\Http\Controllers\AdministrationEtablissement\GroupeController;
use App\Http\Controllers\AdministrationEtablissement\ModuleController;
use App\Http\Controllers\AdministrationEtablissement\FormateurController;
use App\Http\Controllers\AdministrationEtablissement\MetierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect fallback to login
Route::fallback(function () {
    return redirect()->route('login'); 
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forgot.password.form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('forgot.password.send');
Route::get('/verify-code', [ForgotPasswordController::class, 'showVerifyCodeForm'])->name('forgot.password.code.form');
Route::post('/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('forgot.password.verify');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/
// Dashboard Complexe
Route::get('/administrationcomplexe/dashboard', [DashboardComplexeController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.complexe');

// Dashboard Établissement
Route::get('/administrationetablissement/dashboard', [DashboardEtablissementController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.etablissement');

/*
|--------------------------------------------------------------------------
| Administration Complexe Routes
|--------------------------------------------------------------------------
*/
Route::prefix('administrationcomplexe')
    ->middleware('auth')
    ->name('administrationcomplexe.')
    ->group(function () {
        // Resource routes
        Route::resource('directeurs', DirecteurController::class);
        Route::resource('etablissements', EtablissementController::class);
        
        // Dashboard API routes
        Route::get('dashboard/activities', [DashboardComplexeController::class, 'getRecentActivities'])
            ->name('dashboard.activities');
        Route::get('dashboard/export', [DashboardComplexeController::class, 'export'])
            ->name('dashboard.export');
    });

/*
|--------------------------------------------------------------------------
| Administration Établissement Routes
|--------------------------------------------------------------------------
*/
Route::prefix('administrationetablissement')
    ->name('administrationetablissement.')
    ->middleware('auth')
    ->group(function () {
        
        // Resource routes
        Route::resource('formations', FormationController::class);
        Route::resource('espaces', EspacePedagogiqueController::class);
        Route::resource('anneesdeformations', AnneeDeFormationController::class);
        Route::resource('groupes', GroupeController::class);
        Route::resource('modules', ModuleController::class);
        Route::resource('formateurs', FormateurController::class);
        Route::resource('metiers', MetierController::class);
        
        // Dashboard API routes
        Route::get('dashboard/stats', [DashboardEtablissementController::class, 'getDetailedStats'])
            ->name('dashboard.stats');
        Route::get('dashboard/export', [DashboardEtablissementController::class, 'export'])
            ->name('dashboard.export');
            
        // Additional specific routes for établissement management
        Route::prefix('formations/{formation}')->name('formations.')->group(function () {
            Route::get('modules', [FormationController::class, 'modules'])->name('modules');
            Route::get('groupes', [FormationController::class, 'groupes'])->name('groupes');
        });
        
        Route::prefix('groupes')->name('groupes.')->group(function () {
            Route::get('by-formation/{formation}', [GroupeController::class, 'byFormation'])->name('by-formation');
            Route::get('by-annee/{annee}', [GroupeController::class, 'byAnnee'])->name('by-annee');
        });
        
        Route::prefix('formateurs/{formateur}')->name('formateurs.')->group(function () {
            Route::get('metiers', [FormateurController::class, 'metiers'])->name('metiers');
            Route::post('assign-metier', [FormateurController::class, 'assignMetier'])->name('assign-metier');
            Route::delete('remove-metier/{metier}', [FormateurController::class, 'removeMetier'])->name('remove-metier');
        });
        
        Route::prefix('metiers/{metier}')->name('metiers.')->group(function () {
            Route::get('modules', [MetierController::class, 'modules'])->name('modules');
            Route::get('formateurs', [MetierController::class, 'formateurs'])->name('formateurs');
            Route::post('assign-module', [MetierController::class, 'assignModule'])->name('assign-module');
            Route::delete('remove-module/{module}', [MetierController::class, 'removeModule'])->name('remove-module');
        });
    });

/*
|--------------------------------------------------------------------------
| API Routes (for AJAX requests)
|--------------------------------------------------------------------------
*/
Route::prefix('api')
    ->middleware('auth')
    ->name('api.')
    ->group(function () {
        
        // Complexe API routes
        Route::prefix('complexe')->name('complexe.')->group(function () {
            Route::get('stats', [DashboardComplexeController::class, 'getRecentActivities'])->name('stats');
            Route::get('etablissements', function() {
                $user = auth()->user();
                if ($user->role !== 'directeur_complexe') {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                return response()->json($user->complexe->etablissements ?? []);
            })->name('etablissements');
        });
        
        // Établissement API routes
        Route::prefix('etablissement')->name('etablissement.')->group(function () {
            Route::get('stats/{period?}', [DashboardEtablissementController::class, 'getDetailedStats'])->name('stats');
            Route::get('formations', function() {
                $user = auth()->user();
                if ($user->role !== 'directeur_etablissement') {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                return response()->json($user->etablissement->formations ?? []);
            })->name('formations');
            Route::get('groupes', function() {
                $user = auth()->user();
                if ($user->role !== 'directeur_etablissement') {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                $groupes = \App\Models\Groupe::whereHas('formation', function($query) use ($user) {
                    $query->where('etablissement_id', $user->etablissement->id);
                })->with(['formation', 'anneeDeFormation'])->get();
                return response()->json($groupes);
            })->name('groupes');
        });
        
        // Shared utility routes
        Route::get('annees-formation', function() {
            return response()->json(\App\Models\AnneeDeFormation::orderBy('annee')->get());
        })->name('annees-formation');
        
        Route::get('metiers', function() {
            return response()->json(\App\Models\Metier::orderBy('nom')->get());
        })->name('metiers');
    });

/*
|--------------------------------------------------------------------------
| Export Routes
|--------------------------------------------------------------------------
*/
Route::prefix('export')
    ->middleware('auth')
    ->name('export.')
    ->group(function () {
        
        Route::get('complexe/{format?}', [DashboardComplexeController::class, 'export'])
            ->name('complexe');
        
        Route::get('etablissement/{format?}', [DashboardEtablissementController::class, 'export'])
            ->name('etablissement');
            
        // Specific exports
        Route::get('formations/{format?}', [FormationController::class, 'export'])
            ->name('formations');
        
        Route::get('groupes/{format?}', [GroupeController::class, 'export'])
            ->name('groupes');
        
        Route::get('formateurs/{format?}', [FormateurController::class, 'export'])
            ->name('formateurs');
    });

/*
|--------------------------------------------------------------------------
| Additional Utility Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Profile routes
    Route::get('/profile', function() {
        return view('profile.show');
    })->name('profile.show');
    
    Route::put('/profile', function(\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $user->update($request->only(['nom', 'email']));
        return redirect()->back()->with('success', 'Profil mis à jour avec succès');
    })->name('profile.update');
    
    // Settings routes
    Route::get('/settings', function() {
        return view('settings.index');
    })->name('settings.index');
    
    // Help routes
    Route::get('/help', function() {
        return view('help.index');
    })->name('help.index');
});

/*
|--------------------------------------------------------------------------
| Role-based Middleware Groups
|--------------------------------------------------------------------------
*/
// Routes pour directeur de complexe uniquement
Route::middleware(['auth', 'role:directeur_complexe'])->group(function () {
    Route::prefix('admin/complexe')->name('admin.complexe.')->group(function () {
        Route::get('settings', function() {
            return view('administrationcomplexe.settings');
        })->name('settings');
        
        Route::get('reports', function() {
            return view('administrationcomplexe.reports');
        })->name('reports');
    });
});

// Routes pour directeur d'établissement uniquement
Route::middleware(['auth', 'role:directeur_etablissement'])->group(function () {
    Route::prefix('admin/etablissement')->name('admin.etablissement.')->group(function () {
        Route::get('settings', function() {
            return view('administrationetablissement.settings');
        })->name('settings');
        
        Route::get('reports', function() {
            return view('administrationetablissement.reports');
        })->name('reports');
        
        Route::get('planning', function() {
            return view('administrationetablissement.planning');
        })->name('planning');
    });
});

/*
|--------------------------------------------------------------------------
| Development/Debug Routes (à supprimer en production)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::get('/debug/user', function() {
        return response()->json([
            'user' => auth()->user(),
            'complexe' => auth()->user()?->complexe,
            'etablissement' => auth()->user()?->etablissement,
        ]);
    });
    
    Route::get('/debug/routes', function() {
        $routes = [];
        foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'middleware' => $route->middleware(),
            ];
        }
        return response()->json($routes);
    });
}