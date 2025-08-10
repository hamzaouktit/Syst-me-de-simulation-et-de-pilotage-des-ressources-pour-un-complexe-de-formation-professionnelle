@extends('layouts.app')

@section('title', 'Modifier le Module')

@section('content')
<div class="container">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-outline-secondary" title="Retour à la liste">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 fw-bold mb-0">Modifier le Module</h1>
    </div>

    <form action="{{ route('administrationetablissement.modules.update', $module) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nom" class="form-label">Nom du Module <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control @error('nom') is-invalid @enderror" 
                   id="nom" 
                   name="nom" 
                   value="{{ old('nom', $module->nom) }}" 
                   placeholder="Ex: Programmation Web, Mathématiques..." 
                   required>
            @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="masse_horaire" class="form-label">Masse Horaire (en heures) <span class="text-danger">*</span></label>
            <input type="number" 
                   class="form-control @error('masse_horaire') is-invalid @enderror" 
                   id="masse_horaire" 
                   name="masse_horaire" 
                   value="{{ old('masse_horaire', $module->masse_horaire) }}" 
                   min="1" 
                   placeholder="Ex: 30" 
                   required>
            @error('masse_horaire')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="formation_id" class="form-label">Formation <span class="text-danger">*</span></label>
            <select id="formation_id" 
                    name="formation_id" 
                    class="form-select @error('formation_id') is-invalid @enderror" 
                    required>
                <option value="" disabled {{ old('formation_id', $module->formation_id) ? '' : 'selected' }}>Sélectionnez une formation</option>
                @foreach($formations as $formation)
                    <option value="{{ $formation->id }}" 
                            {{ old('formation_id', $module->formation_id) == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }} ({{ $formation->niveau }})
                    </option>
                @endforeach
            </select>
            @error('formation_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-outline-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-warning text-white">
                <i class="fas fa-save me-2"></i> Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
