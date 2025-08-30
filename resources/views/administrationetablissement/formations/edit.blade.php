@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title">Modifier la Formation : {{ $formation->titre }}</h3>
                        <small class="text-muted">
                            Établissement: {{ $etablissement->nom }}
                        </small>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('administrationetablissement.formations.show', $formation) }}" 
                           class="btn btn-info">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('administrationetablissement.formations.index') }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('administrationetablissement.formations.update', $formation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Pas besoin de champ caché pour l'établissement car on ne le modifie pas -->
                        
                        <div class="row">
                            <!-- Titre -->
                            <div class="col-md-6 mb-3">
                                <label for="titre" class="form-label">Titre de la formation <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('titre') is-invalid @enderror" 
                                       id="titre" 
                                       name="titre" 
                                       value="{{ old('titre', $formation->titre) }}" 
                                       placeholder="Ex: Master en Informatique"
                                       required>
                                @error('titre')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Niveau -->
                            <div class="col-md-6 mb-3">
                                <label for="niveau" class="form-label">Niveau <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('niveau') is-invalid @enderror" 
                                       id="niveau" 
                                       name="niveau" 
                                       value="{{ old('niveau', $formation->niveau) }}" 
                                       placeholder="Ex: Master, Licence, BTS, DUT..."
                                       required>
                                @error('niveau')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type de formation <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Sélectionnez un type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ old('type', $formation->type) == $type ? 'selected' : '' }}>
                                            @switch($type)
                                                @case('initiale')
                                                    Formation Initiale
                                                    @break
                                                @case('continue')
                                                    Formation Continue
                                                    @break
                                                @case('alternance')
                                                    Formation en Alternance
                                                    @break
                                                @case('distance')
                                                    Formation à Distance
                                                    @break
                                                @default
                                                    {{ ucfirst($type) }}
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Établissement (en lecture seule) -->
                            <div class="col-md-6 mb-3">
                                <label for="etablissement_display" class="form-label">Établissement</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="etablissement_display" 
                                       value="{{ $etablissement->nom }}" 
                                       readonly
                                       style="background-color: #f8f9fa;">
                                <small class="form-text text-muted">
                                    <i class="fas fa-lock"></i> 
                                    L'établissement ne peut pas être modifié.
                                </small>
                            </div>
                        </div>

                        <!-- Informations sur la modification -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Attention :</strong> La modification de cette formation peut affecter les groupes, modules et années de formation associés.
                                </div>
                            </div>
                        </div>

                        <!-- Informations de modification -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Informations sur la formation</h6>
                                        <p class="card-text">
                                            <strong>ID :</strong> {{ $formation->id }}<br>
                                            <strong>Établissement :</strong> {{ $formation->etablissement->nom }}<br>
                                            <strong>Créée le :</strong> {{ $formation->created_at->format('d/m/Y à H:i') }}<br>
                                            <strong>Dernière modification :</strong> {{ $formation->updated_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Information :</strong> Les champs marqués d'un astérisque (*) sont obligatoires.
                                    L'établissement reste <strong>{{ $etablissement->nom }}</strong> et ne peut pas être modifié.
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('administrationetablissement.formations.index') }}" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Annuler
                                        </a>
                                        <a href="{{ route('administrationetablissement.formations.show', $formation) }}" 
                                           class="btn btn-info">
                                            <i class="fas fa-eye"></i> Voir la formation
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validation côté client et confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const requiredFields = form.querySelectorAll('[required]');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            // Confirmation de modification
            if (!confirm('Êtes-vous sûr de vouloir modifier cette formation ?')) {
                e.preventDefault();
            }
        });

        // Debug info
        console.log('Formation ID:', {{ $formation->id }});
        console.log('Établissement ID:', {{ $formation->etablissement_id }});
        console.log('Établissement nom:', '{{ $etablissement->nom }}');
    });
</script>
@endpush