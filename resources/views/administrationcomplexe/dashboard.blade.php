@extends('layouts.app')

@section('title', 'Dashboard Complexe')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Complexe
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="dashboard-complexe">
    {{-- Header Dashboard --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-3 mb-lg-0">
                    <h1 class="dashboard-title">
                        <i class="fas fa-building text-primary me-3"></i>
                        Dashboard - {{ $complexe->nom }}
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-user-tie me-2"></i>Directeur: {{ $complexe->directeur->nom }}
                    </p>
                </div>
                <div class="dashboard-actions">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt me-2"></i>Actualiser
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('administrationcomplexe.dashboard.export', ['format' => 'pdf']) }}">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('administrationcomplexe.dashboard.export', ['format' => 'excel']) }}">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques générales --}}
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-school fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_etablissements'] }}</h4>
                    <small>Établissements</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-book-open fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_formations'] }}</h4>
                    <small>Formations</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_groupes'] }}</h4>
                    <small>Groupes</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_etudiants'] }}</h4>
                    <small>Étudiants</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['directeurs_etablissement'] }}</h4>
                    <small>Directeurs</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-secondary text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h4 class="mb-1">100%</h4>
                    <small>Performance</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques principaux --}}
    <div class="row mb-4">
        {{-- Répartition formations par établissement --}}
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Formations par établissement
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="formationsEtablissementChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Répartition étudiants par établissement --}}
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Étudiants par établissement
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="etudiantsEtablissementChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Types de formation et détails par établissement --}}
    <div class="row mb-4">
        {{-- Types de formation --}}
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-doughnut me-2"></i>Types de formation
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typesFormationChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Liste des établissements --}}
        <div class="col-lg-8 mb-4">
            <div class="card etablissements-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Vue d'ensemble des établissements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($complexe->etablissements as $etablissement)
                            <div class="col-lg-6 mb-3">
                                <div class="etablissement-item">
                                    <div class="d-flex align-items-center">
                                        <div class="etablissement-icon">
                                            <i class="fas fa-school"></i>
                                        </div>
                                        <div class="etablissement-info flex-grow-1">
                                            <h6 class="mb-1">{{ $etablissement->nom }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-book me-1"></i>{{ $etablissement->formations->count() }} formations
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-users me-1"></i>{{ $etablissement->formations->sum(function($f) { return $f->groupes->sum('effectif'); }) }} étudiants
                                            </small>
                                        </div>
                                        <div class="etablissement-actions">
                                            <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions rapides et informations supplémentaires --}}
    <div class="row">
        {{-- Actions rapides --}}
        <div class="col-lg-4 mb-4">
            <div class="card actions-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('administrationcomplexe.etablissements.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Nouvel établissement
                        </a>
                        <a href="{{ route('administrationcomplexe.directeurs.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>Nouveau directeur
                        </a>
                        <hr class="my-3">
                        <a href="{{ route('administrationcomplexe.etablissements.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-2"></i>Gérer établissements
                        </a>
                        <a href="{{ route('administrationcomplexe.directeurs.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-users me-2"></i>Gérer directeurs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications/Alertes --}}
        <div class="col-lg-4 mb-4">
            <div class="card alerts-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-sm">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Système à jour</strong><br>
                        <small>Dernière mise à jour: {{ date('d/m/Y H:i') }}</small>
                    </div>
                    <div class="alert alert-success alert-sm">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Performances optimales</strong><br>
                        <small>Tous les établissements fonctionnent normalement</small>
                    </div>
                    @if($stats['total_etablissements'] > 0)
                        <div class="alert alert-warning alert-sm">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention</strong><br>
                            <small>Vérifiez la répartition des étudiants</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistiques détaillées --}}
        <div class="col-lg-4 mb-4">
            <div class="card stats-detail-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>Statistiques détaillées
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stat-row">
                        <div class="stat-label">
                            <i class="fas fa-school text-primary me-2"></i>
                            Établissements actifs
                        </div>
                        <div class="stat-value">{{ $stats['total_etablissements'] }}/{{ $stats['total_etablissements'] }}</div>
                    </div>
                    
                    <div class="stat-row">
                        <div class="stat-label">
                            <i class="fas fa-graduation-cap text-success me-2"></i>
                            Taux de remplissage
                        </div>
                        <div class="stat-value">
                            {{ $stats['total_etudiants'] > 0 ? round(($stats['total_etudiants'] / ($stats['total_groupes'] * 25)) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    
                    <div class="stat-row">
                        <div class="stat-label">
                            <i class="fas fa-book text-info me-2"></i>
                            Formations par établissement
                        </div>
                        <div class="stat-value">
                            {{ $stats['total_etablissements'] > 0 ? round($stats['total_formations'] / $stats['total_etablissements'], 1) : 0 }}
                        </div>
                    </div>
                    
                    <div class="stat-row">
                        <div class="stat-label">
                            <i class="fas fa-users text-warning me-2"></i>
                            Étudiants par groupe
                        </div>
                        <div class="stat-value">
                            {{ $stats['total_groupes'] > 0 ? round($stats['total_etudiants'] / $stats['total_groupes'], 1) : 0 }}
                        </div>
                    </div>

                    <hr class="my-3">
                    
                    <div class="progress-section">
                        <small class="text-muted">Objectifs de l'année</small>
                        <div class="progress mt-2 mb-2">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                        <small>75% des objectifs atteints</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau récapitulatif des établissements --}}
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Récapitulatif détaillé des établissements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-school me-1"></i>Établissement</th>
                                    <th><i class="fas fa-user-tie me-1"></i>Directeur</th>
                                    <th><i class="fas fa-book me-1"></i>Formations</th>
                                    <th><i class="fas fa-users me-1"></i>Groupes</th>
                                    <th><i class="fas fa-user-graduate me-1"></i>Étudiants</th>
                                    <th><i class="fas fa-map-marker-alt me-1"></i>Localisation</th>
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complexe->etablissements as $etablissement)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="etablissement-avatar me-2">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $etablissement->nom }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($etablissement->directeur)
                                                <span class="badge bg-success">{{ $etablissement->directeur->nom }}</span>
                                            @else
                                                <span class="badge bg-warning">Non assigné</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $etablissement->formations->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info rounded-pill">
                                                {{ $etablissement->formations->sum(function($f) { return $f->groupes->count(); }) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success rounded-pill">
                                                {{ $etablissement->formations->sum(function($f) { return $f->groupes->sum('effectif'); }) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ Str::limit($etablissement->adresse, 30) }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement->id) }}" 
                                                   class="btn btn-outline-primary" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('administrationcomplexe.etablissements.edit', $etablissement->id) }}" 
                                                   class="btn btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">Aucun établissement trouvé</p>
                                            <a href="{{ route('administrationcomplexe.etablissements.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Créer le premier établissement
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>/* Styles corrigés pour le dashboard */

/* Styles corrigés pour le dashboard */
.dashboard-title {
    color: var(--ofppt-blue);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* SOLUTION PRINCIPALE: Séparer les effets hover pour les cartes avec graphiques */
.chart-card, .etablissements-card, .actions-card, .alerts-card, .stats-detail-card, .table-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

/* Effet hover SANS transform pour les cartes de graphiques */
.chart-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    /* PAS de transform pour éviter le redimensionnement des canvas */
}

/* Effet hover AVEC transform pour les autres cartes */
.etablissements-card:hover, .actions-card:hover, 
.alerts-card:hover, .stats-detail-card:hover, .table-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Stabilisation des canvas dans les cartes de graphiques */
.chart-card canvas {
    transform: none !important;
    transition: none !important;
    max-width: 100% !important;
    height: auto !important;
}

/* Conteneurs de graphiques avec dimensions stables */
.chart-card .card-body {
    position: relative;
    min-height: 300px;
    padding: 1.5rem;
}

/* Headers des cartes */
.chart-card .card-header, .etablissements-card .card-header, .actions-card .card-header, 
.alerts-card .card-header, .stats-detail-card .card-header, .table-card .card-header {
    background: linear-gradient(135deg, var(--ofppt-blue), var(--ofppt-dark-blue));
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 1rem 1.5rem;
}

.etablissement-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.etablissement-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.etablissement-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ofppt-blue), var(--ofppt-dark-blue));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.etablissement-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ofppt-green), #228B22);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 8px;
}

.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.stat-row:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--ofppt-gray);
}

.stat-value {
    font-weight: 600;
    color: var(--ofppt-dark-blue);
    font-size: 1.1rem;
}

.progress-section {
    margin-top: 1rem;
}

.dashboard-actions .btn-group {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.table-hover tbody tr:hover {
    background-color: rgba(30, 95, 153, 0.05);
}

/* Protection spéciale pour les canvas Chart.js */
canvas {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: -moz-crisp-edges;
    image-rendering: pixelated;
}

/* Responsive Design */
@media (max-width: 992px) {
    .chart-card .card-body {
        min-height: 250px;
        padding: 1rem;
    }
}

@media (max-width: 768px) {
    .dashboard-actions {
        width: 100%;
        margin-top: 1rem;
    }
    
    .dashboard-actions .btn-group {
        width: 100%;
    }
    
    .dashboard-actions .btn-group .btn {
        flex: 1;
    }
    
    .chart-card .card-body {
        min-height: 200px;
        padding: 0.75rem;
    }
    
    /* Désactiver TOUS les effets hover sur mobile */
    .chart-card:hover, .etablissements-card:hover, .actions-card:hover, 
    .alerts-card:hover, .stats-detail-card:hover, .table-card:hover {
        transform: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
    }
}

@media (max-width: 576px) {
    .chart-card .card-body {
        min-height: 180px;
        padding: 0.5rem;
    }
}

/* Animation de chargement pour les cartes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

/* Amélioration de la performance pour les transformations */
.chart-card, .etablissements-card, .actions-card, .alerts-card, .stats-detail-card, .table-card {
    will-change: transform, box-shadow;
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartsData = @json($chartsData);

    // إعدادات مشتركة
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 12,
                        family: 'Inter, sans-serif'
                    }
                }
            }
        }
    };

    // === 1. Graphique formations par établissement ===
    const formationsCtx = document.getElementById('formationsEtablissementChart');
    if (formationsCtx) {
        new Chart(formationsCtx, {
            type: 'bar',
            data: {
                labels: chartsData.formations_par_etablissement.map(item => item.name),
                datasets: [{
                    label: 'Nombre de formations',
                    data: chartsData.formations_par_etablissement.map(item => item.value),
                    backgroundColor: 'rgba(30, 95, 153, 0.8)',
                    borderColor: 'rgba(30, 95, 153, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // === 2. Graphique étudiants par établissement ===
    const etudiantsCtx = document.getElementById('etudiantsEtablissementChart');
    if (etudiantsCtx) {
        new Chart(etudiantsCtx, {
            type: 'doughnut',
            data: {
                labels: chartsData.etudiants_par_etablissement.map(item => item.name),
                datasets: [{
                    data: chartsData.etudiants_par_etablissement.map(item => item.value),
                    backgroundColor: [
                        '#1E5F99', '#2E8B57', '#FF6B6B', '#4ECDC4', '#45B7D1', 
                        '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8E8', '#F7DC6F'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                    hoverOffset: 8
                }]
            },
            options: {
                ...commonOptions,
                cutout: '50%',
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a,b) => a+b, 0);
                                const percentage = Math.round((context.parsed * 100) / total);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // === 3. Graphique types de formation ===
    const typesCtx = document.getElementById('typesFormationChart');
    if (typesCtx) {
        new Chart(typesCtx, {
            type: 'pie',
            data: {
                labels: chartsData.formations_par_type.map(item => item.name),
                datasets: [{
                    data: chartsData.formations_par_type.map(item => item.value),
                    backgroundColor: [
                        '#2E8B57', '#1E5F99', '#FF6B6B', '#4ECDC4', '#45B7D1',
                        '#96CEB4', '#FFEAA7', '#DDA0DD'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                    hoverOffset: 6
                }]
            },
            options: {
                ...commonOptions,
                aspectRatio: 1,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a,b) => a+b, 0);
                                const percentage = Math.round((context.parsed * 100) / total);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // CSS لتثبيت الحجم ومنع أي اهتزاز
    const style = document.createElement('style');
    style.textContent = `
        .chart-card .card-body {
            position: relative;
            min-height: 300px;
            padding: 1.5rem;
        }
        .chart-card canvas {
            max-width: 100% !important;
            height: 280px !important;
            display: block;
            transform: none !important;
            transition: none !important;
        }
        @media (max-width: 768px) {
            .chart-card .card-body {
                min-height: 250px;
                padding: 1rem;
            }
            .chart-card canvas {
                height: 230px !important;
            }
        }
    `;
    document.head.appendChild(style);
});

function refreshDashboard() {
    window.location.reload();
}
</script>
@endpush