@extends('layouts.app')

@section('title', $etablissement->nom . ' - Détails')

@section('content')
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
                                <p class="mb-0 fs-5">{{ $etablissement->directeur->name }}</p>
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

    <!-- Carte de localisation avec sélecteur -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt"></i> Localisation de l'Établissement
                        </h5>
                        <!-- Sélecteur de type de carte -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light btn-sm active" 
                                    onclick="changeMapType('recommended')" id="btn-recommended">
                                <i class="fas fa-thumbs-up"></i> Recommandée
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" 
                                    onclick="changeMapType('modern')" id="btn-modern">
                                <i class="fas fa-satellite"></i> Moderne
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" 
                                    onclick="changeMapType('detailed')" id="btn-detailed">
                                <i class="fas fa-mountain"></i> Détaillée
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center">
                                <i class="fas fa-compass fa-2x text-primary mb-2"></i>
                                <h6>Coordonnées</h6>
                                <p class="mb-0">
                                    <small>
                                        Long: {{ $etablissement->longitude }}°<br>
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
                    <div id="map" style="height: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></div>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            <span id="map-info">Carte classique - Parfaite pour la lisibilité et la navigation</span>
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

<style>
.info-card {
    transition: transform 0.2s;
}
.info-card:hover {
    transform: translateY(-2px);
}
</style>

<!-- Scripts spécifiques à cette page -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css" />

<script>
let map;
let marker;
let circle;
let currentMapType = 'recommended';

// Coordonnées (utiliser l'altitude comme latitude si nécessaire)
const longitude = {{ $etablissement->longitude }};
const latitude = {{ $etablissement->altitude }}; // Si altitude = latitude dans votre DB
// Si vous avez un vrai champ latitude, utilisez: {{ $etablissement->latitude ?? $etablissement->altitude }}

function initMap() {
    // Créer la carte
    map = L.map('map').setView([latitude, longitude], 15);
    
    // Charger la carte recommandée par défaut
    loadMapType('recommended');
}

function loadMapType(type) {
    // Supprimer les couches existantes
    map.eachLayer(function(layer) {
        if (layer instanceof L.TileLayer) {
            map.removeLayer(layer);
        }
    });
    
    let tileLayer;
    let iconHtml;
    let mapInfo;
    
    switch(type) {
        case 'recommended':
            // Carte classique OpenStreetMap
            tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            });
            iconHtml = '<div style="background: linear-gradient(45deg, #007bff, #0056b3); color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,123,255,0.4); animation: pulse 2s infinite;"><i class="fas fa-graduation-cap"></i></div>';
            mapInfo = 'Carte classique - Parfaite pour la lisibilité et la navigation';
            break;
            
        case 'modern':
            // Vue satellite
            tileLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri',
                maxZoom: 19
            });
            iconHtml = '<div style="background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 4px solid white; box-shadow: 0 4px 12px rgba(255,107,107,0.4); animation: pulse 2s infinite;"><i class="fas fa-satellite"></i></div>';
            mapInfo = 'Vue satellite - Idéale pour visualiser l\'environnement réel';
            break;
            
        case 'detailed':
            // Carte topographique
            tileLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenTopoMap contributors',
                maxZoom: 17
            });
            iconHtml = '<div style="background: linear-gradient(45deg, #28a745, #20c997); color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 4px solid white; box-shadow: 0 4px 12px rgba(40,167,69,0.4); animation: pulse 2s infinite;"><i class="fas fa-mountain"></i></div>';
            mapInfo = 'Carte topographique - Parfaite pour voir les reliefs et l\'altitude';
            break;
    }
    
    // Ajouter la couche de tuiles
    tileLayer.addTo(map);
    
    // Supprimer l'ancien marqueur et cercle
    if (marker) map.removeLayer(marker);
    if (circle) map.removeLayer(circle);
    
    // Créer le marqueur personnalisé
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: iconHtml + `
            <style>
                @keyframes pulse {
                    0% { box-shadow: 0 4px 12px rgba(0,123,255,0.4); }
                    50% { box-shadow: 0 4px 20px rgba(0,123,255,0.8); }
                    100% { box-shadow: 0 4px 12px rgba(0,123,255,0.4); }
                }
            </style>
        `,
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });
    
    // Ajouter le marqueur
    marker = L.marker([latitude, longitude], {icon: customIcon})
        .addTo(map)
        .bindPopup(`
            <div style="min-width: 250px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <div style="text-align: center; margin-bottom: 10px;">
                    <h6 style="color: #007bff; margin: 0; font-size: 16px;">
                        <i class="fas fa-graduation-cap"></i> {{ $etablissement->nom }}
                    </h6>
                </div>
                <hr style="margin: 10px 0;">
                <p style="margin-bottom: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: #dc3545; width: 16px;"></i> 
                    <strong>Adresse :</strong><br>
                    <span style="color: #6c757d;">{{ $etablissement->adresse }}</span>
                </p>
                <p style="margin-bottom: 8px;">
                    <i class="fas fa-mountain" style="color: #28a745; width: 16px;"></i> 
                    <strong>Altitude :</strong> {{ $etablissement->altitude }} m
                </p>
                <p style="margin-bottom: 8px;">
                    <i class="fas fa-building" style="color: #007bff; width: 16px;"></i> 
                    <strong>Complexe :</strong> {{ $etablissement->complexe->nom }}
                </p>
                @if($etablissement->directeur)
                <p style="margin-bottom: 0;">
                    <i class="fas fa-user-tie" style="color: #6f42c1; width: 16px;"></i> 
                    <strong>Directeur :</strong> {{ $etablissement->directeur->name }}
                </p>
                @endif
            </div>
        `, {
            maxWidth: 300,
            className: 'custom-popup'
        });
    
    // Ajouter le cercle
    circle = L.circle([latitude, longitude], {
        color: type === 'modern' ? '#ff6b6b' : (type === 'detailed' ? '#28a745' : '#007bff'),
        weight: 2,
        opacity: 0.8,
        fillColor: type === 'modern' ? '#ff6b6b' : (type === 'detailed' ? '#28a745' : '#007bff'),
        fillOpacity: 0.15,
        radius: 300
    }).addTo(map);
    
    // Mettre à jour l'info de la carte
    document.getElementById('map-info').textContent = mapInfo;
    
    // Ajuster la vue
    const group = new L.featureGroup([marker, circle]);
    map.fitBounds(group.getBounds().pad(0.1));
}

function changeMapType(type) {
    // Mettre à jour les boutons
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(`btn-${type}`).classList.add('active');
    
    // Changer la carte
    loadMapType(type);
    currentMapType = type;
}

// Initialiser la carte quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initMap, 500);
});
</script>
@endsection