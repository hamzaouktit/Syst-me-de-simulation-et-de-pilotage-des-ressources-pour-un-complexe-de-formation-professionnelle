@extends('layouts.app')

@section('title', 'Modifier le Groupe')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-edit"></i> Modifier le Groupe
                </h1>
                <div>
                    <a href="{{ route('administrationetablissement.groupes.show', $groupe) }}" 
                       class="btn btn-info btn-sm shadow-sm mr-2">
                        <i class="fas fa-eye fa-sm text-white-50"></i> Voir
                    </a>
                    <a href="{{ route('administrationetablissement.groupes.index') }}" 
                       class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-users-cog"></i> Modifier les Informations
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('administrationetablissement.groupes.update', $groupe) }}" 
                                  method="POST">
                                @csrf
                                @method('PUT')

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
                                                   value="{{ old('nom', $groupe->nom) }}" 
                                                   placeholder="Ex: Groupe A, TDI101, etc."
                                                   required>
                                            
                                            @error('nom')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
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
                                                   value="{{ old('effectif', $groupe->effectif) }}" 
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
                                                            {{ old('formation_id', $groupe->formation_id) == $formation->id ? 'selected' : '' }}>
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
                                                            {{ old('annee_de_formation_id', $groupe->annee_de_formation_id) == $annee->id ? 'selected' : '' }}>
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

                                <!-- Informations complémentaires -->
                                <div class="alert alert-light border-left-primary">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <strong>Créé le :</strong><br>
                                                {{ $groupe->created_at->format('d/m/Y à H:i') }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-edit"></i> <strong>Modifié le :</strong><br>
                                                {{ $groupe->updated_at->format('d/m/Y à H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                    <a href="{{ route('administrationetablissement.groupes.show', $groupe) }}" 
                                       class="btn btn-info mr-2">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
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

            <!-- Informations actuelles -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-left-warning shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Informations Actuelles
                                    </div>
                                    <div class="text-sm text-gray-800">
                                        <strong>Groupe :</strong> {{ $groupe->nom }} | 
                                        <strong>Formation :</strong> {{ $groupe->formation->titre }} | 
                                        <strong>Année :</strong> {{ $groupe->anneeDeFormation->annee }} | 
                                        <strong>Effectif :</strong> {{ $groupe->effectif }} étudiant(s)
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-info-circle fa-2x text-warning"></i>
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
// Auto-focus sur le champ nom et sélection du texte
document.addEventListener('DOMContentLoaded', function() {
    const nomInput = document.getElementById('nom');
    nomInput.focus();
    nomInput.select();
});
</script>
@endsection