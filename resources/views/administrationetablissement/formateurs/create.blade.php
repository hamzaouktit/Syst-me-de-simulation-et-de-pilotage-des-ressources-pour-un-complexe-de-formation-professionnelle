@extends('layouts.app')

@section('title', 'Créer un Formateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Créer un Nouveau Formateur</h3>
                </div>

                <form action="{{ route('administrationetablissement.formateurs.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nom">Nom <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}" 
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
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="masse_horaire_disponible">Masse Horaire Disponible (heures)</label>
                            <input type="number" 
                                   class="form-control @error('masse_horaire_disponible') is-invalid @enderror" 
                                   id="masse_horaire_disponible" 
                                   name="masse_horaire_disponible" 
                                   value="{{ old('masse_horaire_disponible', 910) }}" 
                                   min="0" 
                                   placeholder="910">
                            @error('masse_horaire_disponible')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Par défaut : 910 heures par année. Laissez vide pour utiliser la valeur par défaut.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="metiers">Métiers <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('metiers') is-invalid @enderror" 
                                    id="metiers" 
                                    name="metiers[]" 
                                    multiple="multiple"
                                    required>
                                @foreach($metiers as $metier)
                                    <option value="{{ $metier->id }}" 
                                            {{ in_array($metier->id, old('metiers', [])) ? 'selected' : '' }}>
                                        {{ $metier->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('metiers')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('metiers.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <strong>Obligatoire :</strong> Sélectionnez au moins un métier que peut enseigner ce formateur. Vous pouvez en sélectionner plusieurs.
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer le Formateur
                        </button>
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
        // Configuration Select2 pour la sélection multiple des métiers
        $('#metiers').select2({
            placeholder: "Sélectionner les métiers (obligatoire)...",
            allowClear: false, // Empêche de tout désélectionner
            closeOnSelect: false, // Garde le dropdown ouvert pour sélection multiple
            width: '100%'
        });

        // Validation côté client pour s'assurer qu'au moins un métier est sélectionné
        $('form').on('submit', function(e) {
            var selectedMetiers = $('#metiers').val();
            if (!selectedMetiers || selectedMetiers.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un métier pour ce formateur.');
                $('#metiers').focus();
                return false;
            }
        });

        // Mise à jour du placeholder de masse horaire
        $('#masse_horaire_disponible').attr('placeholder', '910 (valeur par défaut)');
    });
</script>

<style>
    /* Amélioration visuelle pour Select2 */
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border: 1px solid #007bff;
        color: white;
        padding: 2px 8px;
        margin: 2px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcccc;
    }
</style>
@endsection