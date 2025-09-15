@extends('layouts.app')

@section('title', 'Créer un Module')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Créer un Module</h1>
                    <p class="text-muted">Ajouter un nouveau module de formation</p>
                </div>
                <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du Module</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administrationetablissement.modules.store') }}" method="POST">
                        @csrf

                        <!-- Nom du module -->
                        <div class="mb-4">
                            <label for="nom" class="form-label required">Nom du Module</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Masse horaire -->
                        <div class="mb-4">
                            <label for="masse_horaire" class="form-label required">Masse Horaire (heures)</label>
                            <input type="number" class="form-control @error('masse_horaire') is-invalid @enderror" 
                                   id="masse_horaire" name="masse_horaire" value="{{ old('masse_horaire') }}" 
                                   min="1" required>
                            @error('masse_horaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Formations associées -->
                        <div class="mb-4">
                            <label class="form-label required">Formations Associées</label>
                            <div class="border rounded p-3 @error('formations') is-invalid @enderror">
                                @if($formations->count() > 0)
                                    <div class="row">
                                        @foreach($formations as $formation)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="formations[]" value="{{ $formation->id }}" 
                                                           id="formation_{{ $formation->id }}"
                                                           {{ in_array($formation->id, old('formations', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="formation_{{ $formation->id }}">
                                                        <strong>{{ $formation->titre }}</strong><br>
                                                        <small class="text-muted">{{ $formation->niveau }} - {{ $formation->type }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <p class="text-muted mb-0">Aucune formation disponible</p>
                                        <small class="text-muted">Créez d'abord des formations.</small>
                                    </div>
                                @endif
                            </div>
                            @error('formations')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Sélectionnez au moins une formation.</small>
                        </div>

                        <!-- Métiers associés -->
                        <div class="mb-4">
                            <label class="form-label">Métiers Associés <span class="text-muted">(optionnel)</span></label>
                            <div class="border rounded p-3 @error('metiers') is-invalid @enderror">
                                @if($metiers->count() > 0)
                                    <div class="row">
                                        @foreach($metiers as $metier)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="metiers[]" value="{{ $metier->id }}" 
                                                           id="metier_{{ $metier->id }}"
                                                           {{ in_array($metier->id, old('metiers', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="metier_{{ $metier->id }}">
                                                        <strong>{{ $metier->nom }}</strong>
                                                        @if($metier->description)
                                                            <br><small class="text-muted">{{ Str::limit($metier->description, 50) }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <p class="text-muted mb-0">Aucun métier disponible</p>
                                        <small class="text-muted">Créez d'abord des métiers.</small>
                                    </div>
                                @endif
                            </div>
                            @error('metiers')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Sélectionnez les métiers concernés par ce module.</small>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer le Module
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: red;
}
</style>
@endsection