@extends('layouts.app')

@section('title', 'Modifier l\'Établissement')

@section('page-title', 'Modifier l\'Établissement')

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
                <i class="fas fa-edit"></i> Modifier
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Formulaire de modification -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="fas fa-edit me-2"></i>
                <h4 class="mb-0">Modifier l'Établissement</h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('administrationcomplexe.etablissements.update', $etablissement) }}" method="POST" id="editEtablissementForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nom de l'établissement -->
                    <div class="mb-3">
                        <label for="nom" class="form-label fw-bold">
                            <i class="fas fa-school text-primary me-2"></i>Nom de l'établissement <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom', $etablissement->nom) }}" 
                               placeholder="Ex: École Primaire Al-Manar"
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div class="mb-3">
                        <label for="adresse" class="form-label fw-bold">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>Adresse <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                  id="adresse" 
                                  name="adresse" 
                                  rows="2" 
                                  placeholder="Ex: 123 Avenue Mohammed V, Casablanca"
                                  required>{{ old('adresse', $etablissement->adresse) }}</textarea>
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Coordonnées géographiques -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label fw-bold">
                                <i class="fas fa-globe text-info me-2"></i>Longitude <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   step="any" 
                                   class="form-control @error('longitude') is-invalid @enderror" 
                                   id="longitude" 
                                   name="longitude" 
                                   value="{{ old('longitude', $etablissement->longitude) }}" 
                                   placeholder="Ex: -7.589843"
                                   min="-180" 
                                   max="180"
                                   required>
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Coordonnée Est/Ouest (-180 à 180)
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="altitude" class="form-label fw-bold">
                                <i class="fas fa-mountain text-success me-2"></i>Altitude <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   step="any" 
                                   class="form-control @error('altitude') is-invalid @enderror" 
                                   id="altitude" 
                                   name="altitude" 
                                   value="{{ old('altitude', $etablissement->altitude) }}" 
                                   placeholder="Ex: 50"
                                   required>
                            @error('altitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Altitude en mètres
                            </small>
                        </div>
                    </div>

                    <!-- Directeur -->
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-bold">
                            <i class="fas fa-user-tie text-warning me-2"></i>Directeur d'établissement <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('user_id') is-invalid @enderror" 
                                id="user_id" 
                                name="user_id"
                                required>
                            <option value="">-- Sélectionner un directeur --</option>
                            @foreach($directeursDisponibles as $directeur)
                                <option value="{{ $directeur->id }}" 
                                        {{ old('user_id', $etablissement->user_id) == $directeur->id ? 'selected' : '' }}>
                                    {{ $directeur->nom }} ({{ $directeur->email }})
                                    @if($directeur->id == $etablissement->user_id)
                                        - Directeur actuel
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Information sur le complexe -->
                    <div class="alert alert-info border-l-4 border-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-info me-3"></i>
                            <div>
                                <strong>Information :</strong> Cet établissement appartient au complexe 
                                <strong class="text-primary">{{ $etablissement->complexe->nom }}</strong>.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Boutons d'action -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('administrationcomplexe.etablissements.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                            </a>
                            <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement) }}" 
                               class="btn btn-outline-info">
                                <i class="fas fa-eye me-1"></i> Voir l'établissement
                            </a>
                        </div>
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations actuelles -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    Informations actuelles
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-group mb-3">
                            <strong class="text-muted d-block">Nom actuel :</strong>
                            <span class="fs-6">{{ $etablissement->nom }}</span>
                        </div>
                        <div class="info-group mb-3">
                            <strong class="text-muted d-block">Adresse actuelle :</strong>
                            <span class="fs-6">{{ $etablissement->adresse }}</span>
                        </div>
                        <div class="info-group">
                            <strong class="text-muted d-block">Directeur actuel :</strong>
                            @if($etablissement->directeur)
                                <span class="fs-6">
                                    <i class="fas fa-user-circle text-primary me-1"></i>
                                    {{ $etablissement->directeur->nom }} 
                                    <small class="text-muted">({{ $etablissement->directeur->email }})</small>
                                </span>
                            @else
                                <span class="text-muted fst-italic">
                                    <i class="fas fa-user-slash me-1"></i>Aucun directeur assigné
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group mb-3">
                            <strong class="text-muted d-block">Longitude :</strong>
                            <span class="fs-6">
                                <i class="fas fa-globe text-info me-1"></i>{{ $etablissement->longitude }}°
                            </span>
                        </div>
                        <div class="info-group mb-3">
                            <strong class="text-muted d-block">Altitude :</strong>
                            <span class="fs-6">
                                <i class="fas fa-mountain text-success me-1"></i>{{ $etablissement->altitude }} m
                            </span>
                        </div>
                        <div class="info-group">
                            <strong class="text-muted d-block">Créé le :</strong>
                            <span class="fs-6">
                                <i class="fas fa-calendar-alt text-secondary me-1"></i>
                                {{ optional($etablissement->created_at)->format('d/m/Y à H:i') ?? 'Non défini' }}
                            </span>
                        </div>
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
    const form = document.getElementById('editEtablissementForm');
    
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