@extends('layouts.app')

@section('title', 'Modifier le Formateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Formateur</h3>
                </div>

                <form action="{{ route('administrationetablissement.formateurs.update', $formateur) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nom">Nom <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom', $formateur->nom) }}" 
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $formateur->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="masse_horaire_disponible">Masse Horaire Disponible (heures) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('masse_horaire_disponible') is-invalid @enderror" 
                                   id="masse_horaire_disponible" 
                                   name="masse_horaire_disponible" 
                                   value="{{ old('masse_horaire_disponible', $formateur->masse_horaire_disponible) }}" 
                                   min="0" 
                                   required>
                            @error('masse_horaire_disponible')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="metiers">Métiers</label>
                            <select class="form-control select2 @error('metiers') is-invalid @enderror" 
                                    id="metiers" 
                                    name="metiers[]" 
                                    multiple="multiple">
                                @foreach($metiers as $metier)
                                    <option value="{{ $metier->id }}" 
                                            {{ in_array($metier->id, old('metiers', $formateur->metiers->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $metier->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('metiers')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Sélectionnez les métiers que peut enseigner ce formateur.
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('administrationetablissement.formateurs.show', $formateur) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('administrationetablissement.formateurs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Sélectionner les métiers...",
            allowClear: true
        });
    });
</script>
@endsection