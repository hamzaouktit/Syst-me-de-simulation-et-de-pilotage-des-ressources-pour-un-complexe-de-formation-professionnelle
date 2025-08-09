<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'Établissement</title>
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
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-edit"></i> Modifier l'Établissement
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('administrationcomplexe.etablissements.update', $etablissement) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nom" class="form-label">
                                        <i class="fas fa-school"></i> Nom de l'établissement *
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" 
                                           value="{{ old('nom', $etablissement->nom) }}" 
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
                                              placeholder="Ex: 123 Avenue Mohammed V, Casablanca">{{ old('adresse', $etablissement->adresse) }}</textarea>
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
                                           id="longitude" name="longitude" 
                                           value="{{ old('longitude', $etablissement->longitude) }}" 
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
                                           id="altitude" name="altitude" 
                                           value="{{ old('altitude', $etablissement->altitude) }}" 
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
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Information :</strong> Cet établissement appartient au complexe 
                                <strong>{{ $etablissement->complexe->nom }}</strong>.
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('administrationcomplexe.etablissements.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left"></i> Retour à la liste
                                    </a>
                                    <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Voir l'établissement
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informations actuelles -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info"></i> Informations actuelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nom actuel :</strong> {{ $etablissement->nom }}<br>
                                <strong>Adresse actuelle :</strong> {{ $etablissement->adresse }}<br>
                                <strong>Directeur actuel :</strong> 
                                @if($etablissement->directeur)
                                    {{ $etablissement->directeur->nom }} ({{ $etablissement->directeur->email }})
                                @else
                                    <span class="text-muted">Aucun</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Longitude :</strong> {{ $etablissement->longitude }}<br>
                                <strong>Altitude :</strong> {{ $etablissement->altitude }} m<br>
                                <strong>Créé le :</strong> {{ optional($etablissement->created_at)->format('d/m/Y') ?? 'Non défini' }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>