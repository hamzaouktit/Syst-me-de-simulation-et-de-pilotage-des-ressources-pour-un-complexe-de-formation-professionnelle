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
                <div class="d-flex align-items-start">
                    <div>
                        <div class="d-flex align-items-center">
                            <h1 class="dashboard-title mb-0">
                                <i class="fas fa-school text-primary me-3"></i>
                                Dashboard - {{ $etablissement->nom }}
                            </h1>
                            <div class="ms-3">
                                {{-- Bouton des notifications avec design moderne --}}
                                <div class="notification-hub">
                                    <div class="dropdown">
                                        <button class="notification-button" 
                                                type="button" 
                                                id="notificationDropdown" 
                                                data-bs-toggle="dropdown" 
                                                aria-expanded="false">
                                            <div class="notification-icon">
                                                <i class="fas fa-bell"></i>
                                                @if(!empty($stats['issues']))
                                                    @php
                                                        $criticalCount = collect($stats['issues'])->where('priority_class', 'critical-alert')->count();
                                                        $urgentCount = collect($stats['issues'])->where('priority_class', 'urgent-alert')->count();
                                                        $totalCount = count($stats['issues']);
                                                    @endphp
                                                    <span class="notification-badge {{ $criticalCount > 0 ? 'critical' : ($urgentCount > 0 ? 'urgent' : 'normal') }}">
                                                        {{ $totalCount }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="notification-pulse"></div>
                                        </button>
                                        
                                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                                            {{-- Header --}}
                                            <div class="notification-header">
                                                <h6 class="notification-title">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Alertes Système
                                                </h6>
                                                <div class="notification-summary">
                                                    @if(!empty($stats['issues']))
                                                        @php
                                                            $criticalCount = collect($stats['issues'])->where('priority_class', 'critical-alert')->count();
                                                            $urgentCount = collect($stats['issues'])->where('priority_class', 'urgent-alert')->count();
                                                            $warningCount = collect($stats['issues'])->where('priority_class', 'warning-alert')->count();
                                                        @endphp
                                                        <div class="severity-badges">
                                                            @if($criticalCount > 0)
                                                                <span class="severity-badge critical">{{ $criticalCount }} Critique</span>
                                                            @endif
                                                            @if($urgentCount > 0)
                                                                <span class="severity-badge urgent">{{ $urgentCount }} Urgent</span>
                                                            @endif
                                                            @if($warningCount > 0)
                                                                <span class="severity-badge warning">{{ $warningCount }} Attention</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Liste des notifications --}}
                                            <div class="notification-list">
                                                @if(!empty($stats['issues']))
                                                    @foreach(array_slice($stats['issues'], 0, 6) as $issue)
                                                    <div class="alert-item {{ $issue['priority_class'] ?? 'info-alert' }}" 
                                                         data-category="{{ $issue['category'] ?? 'general' }}">
                                                        <div class="alert-icon">
                                                            <i class="{{ $issue['icon'] ?? 'fas fa-info-circle' }}"></i>
                                                        </div>
                                                        
                                                        <div class="alert-content">
                                                            <div class="alert-header">
                                                                <h6 class="alert-title">{{ $issue['title'] }}</h6>
                                                                <span class="alert-time">{{ now()->format('H:i') }}</span>
                                                            </div>
                                                            
                                                            <p class="alert-message">{{ $issue['message'] }}</p>
                                                            
                                                            @if(isset($issue['impact']))
                                                                <div class="alert-impact">
                                                                    <i class="fas fa-chart-line me-1"></i>
                                                                    <small>{{ $issue['impact'] }}</small>
                                                                </div>
                                                            @endif
                                                            
                                                            @if(isset($issue['action_route']))
                                                                <div class="alert-actions">
                                                                    <a href="{{ isset($issue['action_params']) ? route($issue['action_route'], $issue['action_params']) : route($issue['action_route']) }}" 
                                                                       class="alert-action-btn">
                                                                        <i class="fas fa-arrow-right me-1"></i>
                                                                        {{ $issue['action_text'] }}
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="alert-dismiss">
                                                            <button class="dismiss-btn" onclick="dismissAlert('{{ $issue['id'] }}')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    
                                                    @if(count($stats['issues']) > 6)
                                                    <div class="notification-footer">
                                                        <a href="#" class="view-all-btn">
                                                            Voir les {{ count($stats['issues']) - 6 }} autres alertes
                                                            <i class="fas fa-arrow-right ms-1"></i>
                                                        </a>
                                                    </div>
                                                    @endif
                                                @else
                                                    <div class="no-alerts">
                                                        <div class="success-icon">
                                                            <i class="fas fa-check-circle"></i>
                                                        </div>
                                                        <h6>Tout va bien !</h6>
                                                        <p>Aucune alerte détectée</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Actions globales --}}
                                            @if(!empty($stats['issues']))
                                            <div class="notification-actions">
                                                <button class="action-btn secondary" onclick="refreshAlerts()">
                                                    <i class="fas fa-sync-alt me-1"></i>
                                                    Actualiser
                                                </button>
                                                <button class="action-btn primary" onclick="markAllAsResolved()">
                                                    <i class="fas fa-check-double me-1"></i>
                                                    Tout résoudre
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0 mt-2">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $etablissement->adresse }}
                        </p>
                    </div>
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

    {{-- Bannière d'alertes critiques --}}
    @if(!empty($stats['issues']))
        @php
            $criticalIssues = collect($stats['issues'])->where('priority_class', 'critical-alert')->take(2);
        @endphp
        @if($criticalIssues->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="critical-banner">
                    <div class="critical-banner-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="critical-banner-content">
                        <h5 class="critical-banner-title">
                            Action Immédiate Requise
                        </h5>
                        <div class="critical-issues">
                            @foreach($criticalIssues as $issue)
                            <div class="critical-issue">
                                <strong>{{ $issue['title'] }}</strong>: {{ $issue['message'] }}
                                @if(isset($issue['action_route']))
                                    <a href="{{ isset($issue['action_params']) ? route($issue['action_route'], $issue['action_params']) : route($issue['action_route']) }}" 
                                       class="critical-action-btn">
                                        {{ $issue['action_text'] }}
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="critical-banner-close">
                        <button class="close-btn" onclick="hideCriticalBanner()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

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
                        @if($stats['besoins_calcules']['formations_sous_minimum'] > 0)
                            <span class="badge bg-warning ms-1">{{ $stats['besoins_calcules']['formations_sous_minimum'] }} incomplètes</span>
                        @endif
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
            <div class="card stat-card {{ $stats['besoins_calcules']['deficit_heures'] > 0 ? 'bg-warning' : 'bg-info' }} text-white">
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
                        @if($stats['besoins_calcules']['deficit_heures'] > 0)
                            <i class="fas fa-exclamation-triangle me-1"></i>Déficit: {{ $stats['besoins_calcules']['deficit_heures'] }}h
                        @else
                            <i class="fas fa-check me-1"></i>{{ number_format($stats['masse_horaire_disponible']) }}h disponibles
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card {{ $stats['besoins_calcules']['deficit_espaces_heures'] > 0 ? 'bg-danger' : 'bg-warning' }} text-white">
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
                        @if($stats['besoins_calcules']['deficit_espaces_heures'] > 0)
                            <i class="fas fa-exclamation-triangle me-1"></i>Manque {{ $stats['besoins_calcules']['deficit_espaces_heures'] }}h
                        @else
                            <i class="fas fa-chair me-1"></i>{{ $stats['capacite_totale_espaces'] }} places
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Résumé des besoins --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card needs-summary-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Analyse des Besoins Horaires
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="need-metric">
                                <div class="need-value">{{ $stats['besoins_calcules']['heures_totales_necessaires'] }}h</div>
                                <div class="need-label">Besoins Totaux</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="need-metric">
                                <div class="need-value">{{ $stats['besoins_calcules']['heures_disponibles'] }}h</div>
                                <div class="need-label">Disponibles</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="need-metric {{ $stats['besoins_calcules']['deficit_heures'] > 0 ? 'deficit' : 'surplus' }}">
                                <div class="need-value">
                                    @if($stats['besoins_calcules']['deficit_heures'] > 0)
                                        -{{ $stats['besoins_calcules']['deficit_heures'] }}h
                                    @else
                                        +{{ $stats['besoins_calcules']['heures_disponibles'] - $stats['besoins_calcules']['heures_totales_necessaires'] }}h
                                    @endif
                                </div>
                                <div class="need-label">
                                    {{ $stats['besoins_calcules']['deficit_heures'] > 0 ? 'Déficit' : 'Surplus' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="need-metric">
                                <div class="need-value">{{ $stats['besoins_calcules']['formateurs_requis'] }}</div>
                                <div class="need-label">Formateurs Requis</div>
                            </div>
                        </div>
                    </div>
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
:root {
    --primary-color: #1E5F99;
    --success-color: #2E8B57;
    --warning-color: #FF8C00;
    --danger-color: #DC143C;
    --info-color: #4169E1;
}

/* Notification Hub - Design Moderne */
.notification-hub {
    position: relative;
}

.notification-button {
    width: 60px;
    height: 60px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative;
    overflow: hidden;
}

.notification-button:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
}

.notification-icon {
    position: relative;
    z-index: 2;
}

.notification-icon i {
    font-size: 1.5rem;
    color: white;
    transition: all 0.3s ease;
}

.notification-button:hover .notification-icon i {
    animation: bellRing 0.6s ease-in-out;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    min-width: 24px;
    height: 24px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    z-index: 3;
    animation: pulse 2s infinite;
}

.notification-badge.critical {
    background: linear-gradient(135deg, #ff416c 0%, #ff4757 100%);
    box-shadow: 0 4px 15px rgba(255, 65, 108, 0.4);
}

.notification-badge.urgent {
    background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
    box-shadow: 0 4px 15px rgba(255, 167, 38, 0.4);
}

.notification-badge.normal {
    background: linear-gradient(135deg, #42a5f5 0%, #2196f3 100%);
    box-shadow: 0 4px 15px rgba(66, 165, 245, 0.4);
}

.notification-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    animation: ripple 2s infinite;
    pointer-events: none;
}

/* Dropdown des notifications */
.notification-dropdown {
    width: 420px;
    max-height: 80vh;
    border: none;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    margin-top: 10px;
}

.notification-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    text-align: center;
}

.notification-title {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
}

.notification-summary {
    margin-top: 10px;
}

.severity-badges {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.severity-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.severity-badge.critical {
    background: rgba(255, 65, 108, 0.2);
    border: 1px solid rgba(255, 65, 108, 0.5);
    color: #ffccd5;
}

.severity-badge.urgent {
    background: rgba(255, 167, 38, 0.2);
    border: 1px solid rgba(255, 167, 38, 0.5);
    color: #ffe0b3;
}

.severity-badge.warning {
    background: rgba(255, 193, 7, 0.2);
    border: 1px solid rgba(255, 193, 7, 0.5);
    color: #fff3cd;
}

/* Liste des alertes */
.notification-list {
    max-height: 50vh;
    overflow-y: auto;
    padding: 10px 0;
}

.alert-item {
    display: flex;
    align-items: flex-start;
    padding: 15px 20px;
    border-bottom: 1px solid #f0f2f5;
    transition: all 0.3s ease;
    position: relative;
}

.alert-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.alert-item:last-child {
    border-bottom: none;
}

.alert-item.critical-alert {
    border-left: 4px solid #ff416c;
    background: linear-gradient(90deg, rgba(255, 65, 108, 0.05) 0%, transparent 100%);
}

.alert-item.urgent-alert {
    border-left: 4px solid #ffa726;
    background: linear-gradient(90deg, rgba(255, 167, 38, 0.05) 0%, transparent 100%);
}

.alert-item.warning-alert {
    border-left: 4px solid #ffc107;
    background: linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, transparent 100%);
}

.alert-item.info-alert {
    border-left: 4px solid #42a5f5;
    background: linear-gradient(90deg, rgba(66, 165, 245, 0.05) 0%, transparent 100%);
}

.alert-item.success-alert {
    border-left: 4px solid #4caf50;
    background: linear-gradient(90deg, rgba(76, 175, 80, 0.05) 0%, transparent 100%);
}

.alert-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
    font-size: 1.2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.critical-alert .alert-icon {
    background: linear-gradient(135deg, #ff416c 0%, #ff4757 100%);
}

.urgent-alert .alert-icon {
    background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
}

.warning-alert .alert-icon {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
}

.info-alert .alert-icon {
    background: linear-gradient(135deg, #42a5f5 0%, #2196f3 100%);
}

.success-alert .alert-icon {
    background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
}

.alert-content {
    flex: 1;
    min-width: 0;
}

.alert-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 5px;
}

.alert-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
    margin: 0;
    flex: 1;
}

.alert-time {
    font-size: 0.75rem;
    color: #6c757d;
    margin-left: 10px;
    flex-shrink: 0;
}

.alert-message {
    color: #495057;
    font-size: 0.85rem;
    line-height: 1.4;
    margin: 5px 0;
}

.alert-impact {
    margin: 8px 0;
    padding: 6px 10px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    font-size: 0.8rem;
    color: #666;
}

.alert-actions {
    margin-top: 10px;
}

.alert-action-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.alert-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

.alert-dismiss {
    margin-left: 10px;
    flex-shrink: 0;
}

.dismiss-btn {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.1);
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dismiss-btn:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    transform: scale(1.1);
}

/* Footer des notifications */
.notification-footer {
    text-align: center;
    padding: 15px;
    border-top: 1px solid #f0f2f5;
}

.view-all-btn {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.view-all-btn:hover {
    color: #764ba2;
    text-decoration: none;
}

/* Actions globales */
.notification-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.action-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.action-btn.secondary {
    background: #6c757d;
    color: white;
}

.action-btn.secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.action-btn.primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* No alerts state */
.no-alerts {
    text-align: center;
    padding: 40px 20px;
}

.success-icon {
    font-size: 3rem;
    color: #4caf50;
    margin-bottom: 15px;
}

.no-alerts h6 {
    color: #4caf50;
    font-weight: 600;
    margin-bottom: 8px;
}

.no-alerts p {
    color: #6c757d;
    margin: 0;
}

/* Bannière critique */
.critical-banner {
    background: linear-gradient(135deg, #ff416c 0%, #ff4757 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    box-shadow: 0 8px 25px rgba(255, 65, 108, 0.3);
    animation: criticalPulse 2s infinite alternate;
}

.critical-banner-icon {
    font-size: 2.5rem;
    margin-right: 20px;
    animation: bounce 2s infinite;
}

.critical-banner-content {
    flex: 1;
}

.critical-banner-title {
    font-weight: 700;
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.critical-issues {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.critical-issue {
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    justify-content: between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.critical-issue:last-child {
    border-bottom: none;
}

.critical-action-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.critical-action-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.critical-banner-close {
    margin-left: 15px;
}

.close-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Cartes de besoins */
.needs-summary-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.needs-summary-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 20px;
}

.need-metric {
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.need-metric:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.need-metric.deficit {
    background: linear-gradient(135deg, rgba(255, 65, 108, 0.1) 0%, rgba(255, 71, 87, 0.1) 100%);
    border: 1px solid rgba(255, 65, 108, 0.2);
}

.need-metric.surplus {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(56, 142, 60, 0.1) 100%);
    border: 1px solid rgba(76, 175, 80, 0.2);
}

.need-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.need-metric.deficit .need-value {
    color: #ff416c;
}

.need-metric.surplus .need-value {
    color: #4caf50;
}

.need-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Animations */
@keyframes bellRing {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    75% { transform: rotate(-15deg); }
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes ripple {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
    }
}

@keyframes criticalPulse {
    0% { box-shadow: 0 8px 25px rgba(255, 65, 108, 0.3); }
    100% { box-shadow: 0 12px 35px rgba(255, 65, 108, 0.5); }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

/* Styles existants améliorés */
.dashboard-title {
    color: var(--primary-color);
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
    background: linear-gradient(135deg, var(--primary-color), #0d4f8c);
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
    background: linear-gradient(135deg, var(--success-color), #228B22);
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
    color: var(--primary-color);
}

.dashboard-actions .btn-group {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .notification-dropdown {
        width: 350px;
    }
    
    .alert-item {
        padding: 12px 15px;
    }
    
    .alert-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .critical-banner {
        flex-direction: column;
        text-align: center;
    }
    
    .critical-banner-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .critical-issues {
        align-items: center;
    }
}
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartsData = @json($chartsData);

    // Configuration commune pour tous les graphiques
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1000,
            easing: 'easeInOutCubic'
        },
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 12,
                        family: 'Inter, sans-serif'
                    },
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                intersect: false,
                mode: 'index'
            }
        }
    };

    // === 1. Graphique étudiants par formation ===
    const etudiantsCtx = document.getElementById('etudiantsFormationChart');
    if (etudiantsCtx && chartsData.etudiants_par_formation) {
        new Chart(etudiantsCtx, {
            type: 'doughnut',
            data: {
                labels: chartsData.etudiants_par_formation.map(item => item.name),
                datasets: [{
                    data: chartsData.etudiants_par_formation.map(item => item.value),
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f093fb', '#f5576c', 
                        '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                        '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3'
                    ],
                    borderWidth: 0,
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                ...commonOptions,
                cutout: '60%',
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            ...commonOptions.plugins.legend.labels,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a,b) => a+b, 0);
                                        const percentage = ((value * 100) / total).toFixed(1);
                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i,
                                            pointStyle: 'circle'
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    }
                }
            }
        });
    }

    // === 2. Graphique types de formation ===
    const typesCtx = document.getElementById('typesFormationChart');
    if (typesCtx && chartsData.formations_par_type) {
        new Chart(typesCtx, {
            type: 'bar',
            data: {
                labels: chartsData.formations_par_type.map(item => item.name),
                datasets: [{
                    label: 'Nombre de formations',
                    data: chartsData.formations_par_type.map(item => item.value),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(118, 75, 162, 0.8)',
                    hoverBorderColor: 'rgba(118, 75, 162, 1)'
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
                        ticks: { 
                            stepSize: 1,
                            color: '#6c757d',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // === 3. Graphique évolution ===
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx && chartsData.evolution_inscriptions) {
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: chartsData.evolution_inscriptions.map(item => item.month),
                datasets: [{
                    label: 'Nouvelles Inscriptions',
                    data: chartsData.evolution_inscriptions.map(item => item.inscriptions),
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    pointBackgroundColor: '#4facfe',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // === 4. Graphique espaces ===
    const espacesCtx = document.getElementById('espacesChart');
    if (espacesCtx && chartsData.utilisation_espaces) {
        new Chart(espacesCtx, {
            type: 'pie',
            data: {
                labels: chartsData.utilisation_espaces.map(item => `${item.name} (${item.count})`),
                datasets: [{
                    data: chartsData.utilisation_espaces.map(item => item.count),
                    backgroundColor: ['#ff9f43', '#10ac84', '#ee5a52', '#5f27cd', '#00d2d3', '#ff6b6b'],
                    borderWidth: 0,
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            ...commonOptions.plugins.legend.labels,
                            padding: 15
                        }
                    }
                }
            }
        });
    }
});

// Fonctions de gestion des notifications
function dismissAlert(alertId) {
    const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
    if (alertElement) {
        alertElement.style.opacity = '0.5';
        alertElement.style.transform = 'translateX(-100%)';
        setTimeout(() => {
            alertElement.remove();
            updateNotificationCount();
        }, 300);
    }
}

function hideCriticalBanner() {
    const banner = document.querySelector('.critical-banner');
    if (banner) {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(-100%)';
        setTimeout(() => {
            banner.remove();
        }, 300);
    }
}

function refreshAlerts() {
    // Animation de chargement
    const button = event.target;
    const icon = button.querySelector('i');
    icon.classList.add('fa-spin');
    
    // Simuler le rechargement
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

function markAllAsResolved() {
    const alerts = document.querySelectorAll('.alert-item');
    alerts.forEach((alert, index) => {
        setTimeout(() => {
            alert.style.opacity = '0.5';
            alert.style.transform = 'translateX(-100%)';
            setTimeout(() => alert.remove(), 200);
        }, index * 100);
    });
    
    setTimeout(() => {
        updateNotificationCount();
        showSuccessMessage();
    }, alerts.length * 100 + 500);
}

function updateNotificationCount() {
    const badge = document.querySelector('.notification-badge');
    const remainingAlerts = document.querySelectorAll('.alert-item').length;
    
    if (remainingAlerts === 0) {
        if (badge) badge.style.display = 'none';
        showNoAlertsState();
    } else {
        if (badge) badge.textContent = remainingAlerts;
    }
}

function showNoAlertsState() {
    const notificationList = document.querySelector('.notification-list');
    if (notificationList) {
        notificationList.innerHTML = `
            <div class="no-alerts">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h6>Toutes les alertes sont résolues !</h6>
                <p>Votre établissement fonctionne parfaitement</p>
            </div>
        `;
    }
}

function showSuccessMessage() {
    // Créer un toast de succès
    const toast = document.createElement('div');
    toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        <strong>Parfait !</strong> Toutes les alertes ont été marquées comme résolues.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

function refreshDashboard() {
    // Animation de chargement sur le bouton
    const button = event.target;
    const icon = button.querySelector('i');
    const originalText = button.innerHTML;
    
    button.disabled = true;
    icon.classList.add('fa-spin');
    
    // Simuler le chargement puis recharger
    setTimeout(() => {
        window.location.reload();
    }, 800);
}

// Filtrage des alertes par catégorie
function filterAlertsByCategory(category) {
    const alerts = document.querySelectorAll('.alert-item');
    alerts.forEach(alert => {
        const alertCategory = alert.getAttribute('data-category');
        if (category === 'all' || alertCategory === category) {
            alert.style.display = 'flex';
        } else {
            alert.style.display = 'none';
        }
    });
}

// Animation d'apparition progressive des éléments
function animateOnLoad() {
    const cards = document.querySelectorAll('.stat-card, .chart-card, .activity-card, .actions-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    animateOnLoad();
    
    // Ajouter des tooltips Bootstrap aux éléments avec title
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Gestion de la fermeture automatique du dropdown des notifications
document.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.notification-dropdown');
    const button = document.querySelector('.notification-button');
    
    if (dropdown && button && !dropdown.contains(e.target) && !button.contains(e.target)) {
        const bsDropdown = bootstrap.Dropdown.getInstance(button);
        if (bsDropdown) {
            bsDropdown.hide();
        }
    }
});

// Raccourcis clavier pour les actions rapides
document.addEventListener('keydown', function(e) {
    // Ctrl + R pour rafraîchir
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        refreshDashboard();
    }
    
    // Ctrl + N pour nouvelle formation
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        window.location.href = "{{ route('administrationetablissement.formations.create') }}";
    }
});



function updateNotificationDisplay(alerts) {
    const notificationList = document.querySelector('.notification-list');
    const badge = document.querySelector('.notification-badge');
    
    if (alerts.length > 0 && badge) {
        badge.textContent = alerts.length;
        badge.style.display = 'flex';
        
        // Animation de pulsation pour signaler les nouvelles alertes
        badge.style.animation = 'pulse 1s infinite';
        setTimeout(() => {
            badge.style.animation = 'pulse 2s infinite';
        }, 3000);
    }
}
</script>

{{-- Scripts de graphiques préchargés pour éviter les délais --}}
<script>
// Préchargement des données pour une meilleure performance
window.dashboardData = {
    etablissement: @json($etablissement->nom),
    stats: @json($stats),
    charts: @json($chartsData),
    constants: {
        HEURES_FORMATEUR_PAR_AN: {{ App\Http\Controllers\AdministrationEtablissement\DashboardEtablissementController::HEURES_FORMATEUR_PAR_AN }},
        HEURES_ESPACE_PAR_AN: {{ App\Http\Controllers\AdministrationEtablissement\DashboardEtablissementController::HEURES_ESPACE_PAR_AN }},
        HEURES_FORMATION_MIN: {{ App\Http\Controllers\AdministrationEtablissement\DashboardEtablissementController::HEURES_FORMATION_MIN }}
    }
};

// Console log pour debug (à retirer en production)
console.log('Dashboard Data:', window.dashboardData);
</script>
@endpush