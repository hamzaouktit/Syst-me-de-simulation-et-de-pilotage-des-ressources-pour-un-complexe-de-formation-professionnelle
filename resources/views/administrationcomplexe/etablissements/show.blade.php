<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $etablissement->nom }} - Détails</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info-card {
            transition: transform 0.2s;
        }
        .info-card:hover {
            transform: translateY(-2px);
        }
    </style>
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
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-school text-primary"></i> {{ $etablissement->nom }}</h2>
                        <p class="text-muted mb-0">
                            <i class="fas fa-building"></i> Complexe : {{ $etablissement->complexe->nom }}
                        </p>
                    </div>
                    @if(auth()->user()->role === 'directeur_complexe')
                        <div class="btn-group">
                            <a href="{{ route('administrationcomplexe.etablissements.edit', $etablissement) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('administrationcomplexe.etablissements.destroy', $etablissement) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet établissement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informations générales -->
            <div class="col-md-6 mb-4">
                <div class="card info-card shadow h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="fw-bold text-muted">Nom de l'établissement :</label>
                                <p class="mb-0 fs-5">{{ $etablissement->nom }}</p>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="fw-bold text-muted">Adresse :</label>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    {{ $etablissement->adresse }}
                                </p>
                            </div>

                            <div class="col-6 mb-3">
                                <label class="fw-bold text-muted">Longitude :</label>
                                <p class="mb-0">
                                    <code>{{ $etablissement->longitude }}°</code>
                                </p>
                            </div>

                            <div class="col-6 mb-3">
                                <label class="fw-bold text-muted">Altitude :</label>
                                <p class="mb-0">
                                    <i class="fas fa-mountain text-success"></i>
                                    {{ $etablissement->altitude }} m
                                </p>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold text-muted">Complexe :</label>
                                <p class="mb-0">
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-building"></i>
                                        {{ $etablissement->complexe->nom }}
                                    </span>
                                </p>
                            </div>

                            <div class="col-12">
                                <label class="fw-bold text-muted">Date de création :</label>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-alt text-info"></i>
                                    {{ optional($etablissement->created_at)->format('d/m/Y') ?? 'Non défini' }}

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du directeur -->
            <div class="col-md-6 mb-4">
                <div class="card info-card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-tie"></i> Directeur d'Établissement</h5>
                    </div>
                    <div class="card-body">
                        @if($etablissement->directeur)
                            <div class="text-center mb-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x text-primary"></i>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="fw-bold text-muted">Nom complet :</label>
                                    <p class="mb-0 fs-5">{{ $etablissement->directeur->nom }}</p>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="fw-bold text-muted">Email :</label>
                                    <p class="mb-0">
                                        <a href="mailto:{{ $etablissement->directeur->email }}" class="text-decoration-none">
                                            <i class="fas fa-envelope text-primary"></i>
                                            {{ $etablissement->directeur->email }}
                                        </a>
                                    </p>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="fw-bold text-muted">Rôle :</label>
                                    <p class="mb-0">
                                        <span class="badge bg-success">
                                            <i class="fas fa-user-graduate"></i>
                                            {{ ucwords(str_replace('_', ' ', $etablissement->directeur->role)) }}
                                        </span>
                                    </p>
                                </div>

                                <div class="col-12">
                                    <label class="fw-bold text-muted">Membre depuis :</label>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar-plus text-success"></i>
                                        {{ $etablissement->directeur->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun directeur assigné</h5>
                                <p class="text-muted">Cet établissement n'a pas encore de directeur assigné.</p>
                                @if(auth()->user()->role === 'directeur_complexe')
                                    <a href="{{ route('administrationcomplexe.etablissements.edit', $etablissement) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Assigner un directeur
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte de localisation -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt"></i> Localisation de l'Établissement
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded text-center">
                                    <i class="fas fa-compass fa-2x text-primary mb-2"></i>
                                    <h6>Coordonnées</h6>
                                    <p class="mb-0">
                                        <small>
                                            Lat: {{ $etablissement->longitude }}°<br>
                                            Alt: {{ $etablissement->altitude }} m
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="bg-light p-3 rounded">
                                    <i class="fas fa-map-marker-alt fa-lg text-danger me-2"></i>
                                    <strong>Adresse :</strong> {{ $etablissement->adresse }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Carte -->
                        <div id="map"></div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Cliquez et faites glisser pour naviguer sur la carte. Utilisez la molette pour zoomer.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('administrationcomplexe.etablissements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                    
                    <div>
                        @if(auth()->user()->role === 'directeur_complexe')
                            <a href="{{ route('administrationcomplexe.etablissements.edit', $etablissement) }}" 
                               class="btn btn-warning me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        @endif
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js"></script>
    
    <script>
        // Initialiser la carte
        var map = L.map('map').setView([{{ $etablissement->longitude }}, {{ $etablissement->longitude }}], 15);

        // Ajouter la couche de tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Créer une icône personnalisée
        var customIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="background-color: #007bff; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fas fa-school"></i></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        // Ajouter un marqueur pour l'établissement
        var marker = L.marker([{{ $etablissement->longitude }}, {{ $etablissement->longitude }}], {icon: customIcon})
            .addTo(map)
            .bindPopup(`
                <div style="min-width: 200px;">
                    <h6><i class="fas fa-school"></i> {{ $etablissement->nom }}</h6>
                    <p class="mb-1"><strong>Adresse :</strong><br>{{ $etablissement->adresse }}</p>
                    <p class="mb-1"><strong>Altitude :</strong> {{ $etablissement->altitude }} m</p>
                    <p class="mb-0"><strong>Complexe :</strong> {{ $etablissement->complexe->nom }}</p>
                    @if($etablissement->directeur)
                    <p class="mb-0"><strong>Directeur :</strong> {{ $etablissement->directeur->nom }}</p>
                    @endif
                </div>
            `)
            .openPopup();

        // Ajouter un cercle pour montrer la zone approximative
        var circle = L.circle([{{ $etablissement->longitude }}, {{ $etablissement->longitude }}], {
            color: '#007bff',
            fillColor: '#007bff',
            fillOpacity: 0.1,
            radius: 200
        }).addTo(map);

        // Ajuster la vue pour inclure le marqueur et le cercle
        var group = new L.featureGroup([marker, circle]);
        map.fitBounds(group.getBounds().pad(0.1));
    </script>
</body>
</html>