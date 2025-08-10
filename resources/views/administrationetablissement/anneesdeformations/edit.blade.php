@extends('layouts.app')

@section('title', 'Modifier l\'Année de Formation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit"></i> Modifier l'Année de Formation
                </h1>
                <div>
                    <a href="{{ route('administrationetablissement.anneesdeformations.show', $anneesdeformation) }}" 
                       class="btn btn-info btn-sm shadow-sm mr-2">
                        <i class="fas fa-eye fa-sm text-white-50"></i> Voir
                    </a>
                    <a href="{{ route('administrationetablissement.anneesdeformations.index') }}" 
                       class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-edit"></i> Modifier les Informations
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('administrationetablissement.anneesdeformations.update', $anneesdeformation) }}" 
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="annee" class="font-weight-bold">
                                        <i class="fas fa-calendar-alt text-primary"></i> Année <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('annee') is-invalid @enderror" 
                                           id="annee" 
                                           name="annee" 
                                           value="{{ old('annee', $anneesdeformation->annee) }}" 
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

                                <!-- Informations complémentaires -->
                                <div class="alert alert-light border-left-primary">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <strong>Créé le :</strong><br>
                                                {{ $anneesdeformation->created_at->format('d/m/Y à H:i') }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-edit"></i> <strong>Modifié le :</strong><br>
                                                {{ $anneesdeformation->updated_at->format('d/m/Y à H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                    <a href="{{ route('administrationetablissement.anneesdeformations.show', $anneesdeformation) }}" 
                                       class="btn btn-info mr-2">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
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

            <!-- Informations sur les groupes associés -->
            @if($anneesdeformation->groupes()->count() > 0)
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-left-warning shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Attention
                                    </div>
                                    <div class="text-sm text-gray-800">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        Cette année contient <strong>{{ $anneesdeformation->groupes()->count() }} groupe(s)</strong>. 
                                        La modification de l'année peut affecter ces groupes.
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-focus sur le champ année et sélection du texte
document.addEventListener('DOMContentLoaded', function() {
    const anneeInput = document.getElementById('annee');
    anneeInput.focus();
    anneeInput.select();
});
</script>
@endsection