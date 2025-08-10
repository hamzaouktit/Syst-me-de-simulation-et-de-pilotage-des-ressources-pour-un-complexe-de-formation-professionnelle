@extends('layouts.app')

@section('title', 'Créer une Année de Formation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus-circle"></i> Créer une Année de Formation
                </h1>
                <a href="{{ route('administrationetablissement.anneesdeformations.index') }}" 
                   class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
                </a>
            </div>

            <!-- Formulaire de création -->
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-plus"></i> Informations de l'Année
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('administrationetablissement.anneesdeformations.store') }}" 
                                  method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="annee" class="font-weight-bold">
                                        <i class="fas fa-calendar-alt text-primary"></i> Année <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('annee') is-invalid @enderror" 
                                           id="annee" 
                                           name="annee" 
                                           value="{{ old('annee', date('Y')) }}" 
                                           min="2020" 
                                           max="2050"
                                           required>
                                    
                                    @error('annee')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        L'année doit être comprise entre 2020 et 2050.
                                    </small>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                    <a href="{{ route('administrationetablissement.anneesdeformations.index') }}" 
                                       class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte d'information -->
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-left-info shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Information
                                    </div>
                                    <div class="text-sm text-gray-800">
                                        <i class="fas fa-lightbulb text-warning"></i>
                                        L'année de formation permet de classer et organiser les groupes d'étudiants 
                                        par année académique. Cette information est essentielle pour la gestion 
                                        des formations et des plannings.
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-info-circle fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus sur le champ année
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('annee').focus();
});
</script>
@endsection