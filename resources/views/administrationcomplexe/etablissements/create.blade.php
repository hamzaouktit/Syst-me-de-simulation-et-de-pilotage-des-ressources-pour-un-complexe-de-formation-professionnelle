@extends('layouts.app')

@section('title', 'Créer un Établissement')

@section('page-title', 'Créer un Établissement')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.complexe') }}">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administrationcomplexe.etablissements.index') }}">
                    <i class="fas fa-building"></i> Établissements
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-plus-circle"></i> Créer
            </li>
        </ol>
    </nav>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Formulaire de création -->
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="fas fa-plus-circle me-2"></i>
                <h4 class="mb-0">Créer un Nouvel Établissement</h4>
            </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('administrationcomplexe.etablissements.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nom" class="form-label fw-bold">
                                        <i class="fas fa-school text-primary me-2"></i>Nom de l'établissement <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom') }}" 
                                           placeholder="Ex: École Primaire Al-Manar">
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="adresse" class="form-label fw-bold">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>Adresse <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                              id="adresse" name="adresse" rows="2" 
                                              placeholder="Ex: 123 Avenue Mohammed V, Casablanca">{{ old('adresse') }}</textarea>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label fw-bold">
                                        <i class="fas fa-globe text-info me-2"></i>Longitude <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude') }}" 
                                           placeholder="Ex: -7.589843">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Coordonnée Est/Ouest (-180 à 180)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="altitude" class="form-label fw-bold">
                                        <i class="fas fa-mountain text-success me-2"></i>Altitude <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="any" class="form-control @error('altitude') is-invalid @enderror" 
                                           id="altitude" name="altitude" value="{{ old('altitude') }}" 
                                           placeholder="Ex: 50">
                                    @error('altitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Altitude en mètres</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="user_id" class="form-label fw-bold">
                                        <i class="fas fa-user-tie text-warning me-2"></i>Directeur d'établissement <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                        <option value="">-- Sélectionner un directeur --</option>
                                        @foreach($directeursDisponibles as $directeur)
                                            <option value="{{ $directeur->id }}" 
                                                    {{ old('user_id') == $directeur->id ? 'selected' : '' }}>
                                                {{ $directeur->nom }} ({{ $directeur->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($directeursDisponibles->isEmpty())
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Aucun directeur d'établissement disponible. Vous devez d'abord créer des directeurs d'établissement.
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="alert alert-info border-l-4 border-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle text-info me-3"></i>
                                    <div>
                                        <strong>Information :</strong> Cet établissement sera rattaché au complexe 
                                        <strong class="text-primary">{{ $complexe->nom }}</strong>.
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('administrationcomplexe.etablissements.index') }}" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-success px-4" 
                                        {{ $directeursDisponibles->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save me-2"></i>Créer l'établissement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($directeursDisponibles->isEmpty())
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-warning mb-3"></i>
                            <h5>Aucun directeur disponible</h5>
                            <p class="text-muted mb-3">
                                Pour créer un établissement, vous devez d'abord avoir des directeurs d'établissement disponibles.
                            </p>
                            <a href="{{ route('administrationcomplexe.directeurs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer un directeur d'établissement
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-l-4 {
        border-left: 4px solid !important;
    }
    .info-group strong {
        font-size: 0.9em;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-header {
        border-bottom: 3px solid rgba(0,0,0,0.1);
    }
    .form-label {
        margin-bottom: 0.75rem;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation côté client
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const longitude = parseFloat(document.getElementById('longitude').value);
        
        if (longitude < -180 || longitude > 180) {
            e.preventDefault();
            alert('La longitude doit être comprise entre -180 et 180 degrés.');
            return false;
        }
    });

    // Animation pour les champs requis
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>
@endpush