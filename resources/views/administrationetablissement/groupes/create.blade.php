@extends('layouts.app')

@section('title', 'Créer un Groupe')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-plus"></i> Créer un Groupe
                </h1>
                <a href="{{ route('administrationetablissement.groupes.index') }}" 
                   class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
                </a>
            </div>

            <!-- Formulaire de création -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-users-cog"></i> Informations du Groupe
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('administrationetablissement.groupes.store') }}" 
                                  method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom" class="font-weight-bold">
                                                <i class="fas fa-tag text-primary"></i> Nom du Groupe <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('nom') is-invalid @enderror" 
                                                   id="nom" 
                                                   name="nom" 
                                                   value="{{ old('nom') }}" 
                                                   placeholder="Ex: Groupe A, TDI101, etc."
                                                   required>
                                            
                                            @error('nom')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                    <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Le nom doit être unique pour cette formation et cette année.
                                            </small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="effectif" class="font-weight-bold">
                                                <i class="fas fa-users text-primary"></i> Effectif <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('effectif') is-invalid @enderror" 
                                                   id="effectif" 
                                                   name="effectif" 
                                                   value="{{ old('effectif', 0) }}" 
                                                   min="0" 
                                                   max="100"
                                                   required>
                                            
                                            @error('effectif')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                            
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Nombre d'étudiants (maximum 100).
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="formation_id" class="font-weight-bold">
                                                <i class="fas fa-graduation-cap text-primary"></i> Formation <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('formation_id') is-invalid @enderror" 
                                                    id="formation_id" 
                                                    name="formation_id" 
                                                    required>
                                                <option value="">Sélectionner une formation</option>
                                                @foreach($formations as $formation)
                                                    <option value="{{ $formation->id }}" 
                                                            {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                                        {{ $formation->titre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            @error('formation_id')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="annee_de_formation_id" class="font-weight-bold">
                                                <i class="fas fa-calendar-alt text-primary"></i> Année de Formation <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('annee_de_formation_id') is-invalid @enderror" 
                                                    id="annee_de_formation_id" 
                                                    name="annee_de_formation_id" 
                                                    required>
                                                <option value="">Sélectionner une année</option>
                                                @foreach($annees as $annee)
                                                    <option value="{{ $annee->id }}" 
                                                            {{ old('annee_de_formation_id') == $annee->id ? 'selected' : '' }}>
                                                        {{ $annee->annee }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            @error('annee_de_formation_id')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                    <a href="{{ route('administrationetablissement.groupes.index') }}" 
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
                <div class="col-lg-8">
                    <div class="card border-left-info shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Information
                                    </div>
                                    <div class="text-sm text-gray-800">
                                        <i class="fas fa-lightbulb text-warning"></i>
                                        Un groupe représente un ensemble d'étudiants qui suivent une formation 
                                        spécifique durant une année académique donnée. Cette organisation 
                                        facilite la gestion des cours et des plannings.
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
// Auto-focus sur le champ nom
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nom').focus();
});
</script>
@endsection