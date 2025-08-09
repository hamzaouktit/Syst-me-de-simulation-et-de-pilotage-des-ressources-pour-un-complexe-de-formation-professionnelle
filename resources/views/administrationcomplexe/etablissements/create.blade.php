<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Établissement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard.complexe') }}">
                <i class="fas fa-building"></i> Administration Complexe
            </a>
            <div class="navbar-nav ms-auto">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle"></i> Créer un Nouvel Établissement
                        </h4>
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
                                    <label for="nom" class="form-label">
                                        <i class="fas fa-school"></i> Nom de l'établissement *
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
                                    <label for="adresse" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> Adresse *
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
                                    <label for="longitude" class="form-label">
                                        <i class="fas fa-globe"></i> Longitude *
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
                                    <label for="altitude" class="form-label">
                                        <i class="fas fa-mountain"></i> Altitude *
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
                                    <label for="user_id" class="form-label">
                                        <i class="fas fa-user-tie"></i> Directeur d'établissement *
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

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Information :</strong> Cet établissement sera rattaché au complexe 
                                <strong>{{ $complexe->nom }}</strong>.
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('administrationcomplexe.etablissements.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-success" 
                                        {{ $directeursDisponibles->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Créer l'établissement
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>