@extends('layouts.app')

@section('title', 'Dashboard Établissement')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Établissement
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="dashboard-etablissement">
    {{-- Header Dashboard --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="dashboard-title">
                        <i class="fas fa-school text-primary me-3"></i>
                        Dashboard - {{ $etablissement->nom }}
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>{{ $etablissement->adresse }}
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
                                <li><a class="dropdown-item" href="{{ route('administrationetablissement.dashboard.export', ['format' => 'pdf']) }}">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('administrationetablissement.dashboard.export', ['format' => 'excel']) }}">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cartes de statistiques --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Formations</h6>
                            <h3 class="mb-0">{{ $stats['total_formations'] }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-book-open fa-2x"></i>
                        </div>
                    </div>
                    <small class="mt-2 d-block">
                        <i class="fas fa-chart-line me-1"></i>Total formations actives
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Étudiants</h6>
                            <h3 class="mb-0">{{ $stats['total_etudiants'] }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                    <small class="mt-2 d-block">
                        <i class="fas fa-users me-1"></i>{{ $stats['total_groupes'] }} groupes
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Formateurs</h6>
                            <h3 class="mb-0">{{ $stats['total_formateurs'] }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                    <small class="mt-2 d-block">
                        <i class="fas fa-clock me-1"></i>{{ number_format($stats['masse_horaire_disponible']) }}h disponibles
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Espaces</h6>
                            <h3 class="mb-0">{{ $stats['total_espaces'] }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-door-open fa-2x"></i>
                        </div>
                    </div>
                    <small class="mt-2 d-block">
                        <i class="fas fa-chair me-1"></i>{{ $stats['capacite_totale_espaces'] }} places
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row mb-4">
        {{-- Répartition des étudiants par formation --}}
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Répartition des étudiants par formation
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="etudiantsFormationChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Types de formation --}}
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Types de formation
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typesFormationChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Étudiants par année --}}
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Évolution des inscriptions
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="600" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Utilisation des espaces --}}
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Espaces pédagogiques
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="espacesChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Activités récentes et Actions rapides --}}
    <div class="row">
        {{-- Activités récentes --}}
        <div class="col-lg-8 mb-4">
            <div class="card activity-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Activités récentes
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($recentActivities) > 0)
                        <div class="activity-list">
                            @foreach($recentActivities as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-{{ $activity['icon'] ?? 'circle' }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-message mb-1">{{ $activity['message'] }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune activité récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

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
                        <a href="{{ route('administrationetablissement.formations.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Nouvelle formation
                        </a>
                        <a href="{{ route('administrationetablissement.groupes.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-users me-2"></i>Nouveau groupe
                        </a>
                        <a href="{{ route('administrationetablissement.formateurs.create') }}" class="btn btn-outline-info">
                            <i class="fas fa-user-plus me-2"></i>Nouveau formateur
                        </a>
                        <a href="{{ route('administrationetablissement.espaces.create') }}" class="btn btn-outline-warning">
                            <i class="fas fa-door-open me-2"></i>Nouvel espace
                        </a>
                        <hr class="my-3">
                        <a href="{{ route('administrationetablissement.formations.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-2"></i>Gérer formations
                        </a>
                        <a href="{{ route('administrationetablissement.groupes.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-users me-2"></i>Gérer groupes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-title {
    color: var(--ofppt-blue);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    opacity: 0.8;
}

.chart-card, .activity-card, .actions-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
}

.chart-card:hover, .activity-card:hover, .actions-card:hover {
    transform: translateY(-2px);
}

.chart-card .card-header, .activity-card .card-header, .actions-card .card-header {
    background: linear-gradient(135deg, var(--ofppt-blue), var(--ofppt-dark-blue));
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 1rem 1.5rem;
}

.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ofppt-green), #228B22);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-message {
    font-weight: 500;
    color: var(--ofppt-dark-blue);
}

.dashboard-actions .btn-group {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
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
        maintainAspectRatio: false, // باش يتبع الحجم ديال الحاوية
        animation: false, // نحيد أي animation كيعمل اهتزاز
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

    // === 1. Graphique étudiants par formation ===
    const etudiantsCtx = document.getElementById('etudiantsFormationChart');
    if (etudiantsCtx) {
        new Chart(etudiantsCtx, {
            type: 'doughnut',
            data: {
                labels: chartsData.etudiants_par_formation.map(item => item.name),
                datasets: [{
                    data: chartsData.etudiants_par_formation.map(item => item.value),
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

    // === 2. Graphique types de formation ===
    const typesCtx = document.getElementById('typesFormationChart');
    if (typesCtx) {
        new Chart(typesCtx, {
            type: 'bar',
            data: {
                labels: chartsData.formations_par_type.map(item => item.name),
                datasets: [{
                    label: 'Nombre de formations',
                    data: chartsData.formations_par_type.map(item => item.value),
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
                    legend: { display: false }
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

    // === 3. Graphique évolution ===
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx) {
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: chartsData.evolution_inscriptions.map(item => item.month),
                datasets: [{
                    label: 'Inscriptions',
                    data: chartsData.evolution_inscriptions.map(item => item.inscriptions),
                    borderColor: 'rgba(46, 139, 87, 1)',
                    backgroundColor: 'rgba(46, 139, 87, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // === 4. Graphique espaces ===
    const espacesCtx = document.getElementById('espacesChart');
    if (espacesCtx) {
        new Chart(espacesCtx, {
            type: 'pie',
            data: {
                labels: chartsData.utilisation_espaces.map(item => item.name),
                datasets: [{
                    data: chartsData.utilisation_espaces.map(item => item.count),
                    backgroundColor: ['#FF9F43', '#10AC84', '#EE5A52', '#5F27CD'],
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
            height: 280px !important; /* تثبيت الارتفاع */
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
