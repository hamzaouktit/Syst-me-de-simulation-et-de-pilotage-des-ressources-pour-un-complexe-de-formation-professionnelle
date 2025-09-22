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
                        <span class="mx-3">•</span>
                        <i class="fas fa-calendar me-2"></i>{{ date('d M Y') }}
                        <span class="mx-3">•</span>
                        <i class="fas fa-chart-line me-2"></i>Taux de couverture: {{ $stats['taux_couverture_heures'] }}%
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

    {{-- Notification Bell Améliorée --}}
    <div class="notification-system">
        <div class="notification-bell-container">
            <button class="notification-bell" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-bell"></i>
                @if($stats['etablissements_avec_alertes'] > 0)
                    @php
                        $hasCritical = $stats['nombre_alertes_critiques'] > 0;
                        $totalAlerts = $stats['nombre_alertes_critiques'] + $stats['nombre_alertes_avertissements'];
                    @endphp
                    <span class="notification-badge badge-{{ $hasCritical ? 'critical' : 'warning' }}">
                        {{ $totalAlerts }}
                    </span>
                @endif
            </button>
            
            <div class="dropdown-menu notification-dropdown" aria-labelledby="notificationDropdown">
                <div class="notification-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-bell me-2"></i>
                            Alertes des établissements
                        </h6>
                        @if($stats['etablissements_avec_alertes'] > 0)
                            <div class="alert-summary">
                                @if($stats['nombre_alertes_critiques'] > 0)
                                    <span class="alert-badge critical">{{ $stats['nombre_alertes_critiques'] }}</span>
                                @endif
                                @if($stats['nombre_alertes_avertissements'] > 0)
                                    <span class="alert-badge warning">{{ $stats['nombre_alertes_avertissements'] }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="notification-content">
                    @if($stats['etablissements_avec_alertes'] > 0)
                        @foreach($stats['etablissements_alertes'] as $etablissementData)
                        <div class="notification-item">
                            <div class="etablissement-notification">
                                <div class="notification-icon {{ $etablissementData['has_critical'] ? 'critical' : 'warning' }}">
                                    <i class="fas fa-school"></i>
                                </div>
                                
                                <div class="notification-content-body">
                                    <div class="notification-title">
                                        <h6>{{ $etablissementData['etablissement']->nom }}</h6>
                                        <div class="completion-indicator">
                                            <div class="completion-bar">
                                                <div class="completion-fill" style="width: {{ $etablissementData['completion_percentage'] }}%"></div>
                                            </div>
                                            <span class="completion-text">{{ $etablissementData['completion_percentage'] }}%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="issues-summary">
                                        @if($etablissementData['critical_count'] > 0)
                                            <span class="issue-count critical">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $etablissementData['critical_count'] }} Critique(s)
                                            </span>
                                        @endif
                                        
                                        @if($etablissementData['warning_count'] > 0)
                                            <span class="issue-count warning">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $etablissementData['warning_count'] }} Avertissement(s)
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="issues-details">
                                        @foreach(array_slice($etablissementData['issues'], 0, 3) as $issue)
                                        <div class="issue-item {{ $issue['type'] }}">
                                            <i class="fas {{ $issue['icon'] }}"></i>
                                            <span class="issue-message">{{ $issue['message'] }}</span>
                                            @if(isset($issue['details']))
                                                <small class="issue-details">{{ $issue['details'] }}</small>
                                            @endif
                                        </div>
                                        @endforeach
                                        
                                        @if(count($etablissementData['issues']) > 3)
                                            <div class="more-issues">
                                                +{{ count($etablissementData['issues']) - 3 }} autre(s) problème(s)
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="notification-actions">
                                    <a href="{{ route('administrationcomplexe.etablissements.show', $etablissementData['etablissement']->id) }}" 
                                       class="btn btn-sm btn-{{ $etablissementData['has_critical'] ? 'danger' : 'warning' }}">
                                        <i class="fas fa-eye me-1"></i>
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="no-notifications">
                            <i class="fas fa-check-circle"></i>
                            <h6>Excellent travail !</h6>
                            <p>Tous les établissements sont optimalement configurés</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques générales améliorées --}}
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-school fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_etablissements'] }}</h4>
                    <small>Établissements</small>
                    @if($stats['etablissements_sans_formateurs'] > 0)
                        <div class="stat-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $stats['etablissements_sans_formateurs'] }} sans formateurs
                        </div>
                    @endif
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
                    @if($stats['formations_incompletes'] > 0)
                        <div class="stat-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $stats['formations_incompletes'] }} incomplètes
                        </div>
                    @endif
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
                    <div class="stat-detail">
                        Moyenne: {{ $stats['total_groupes'] > 0 ? round($stats['total_etudiants'] / $stats['total_groupes'], 1) : 0 }} étudiants/groupe
                    </div>
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
                    <div class="stat-detail">
                        {{ $stats['total_formateurs'] }} formateurs disponibles
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card {{ $stats['deficit_heures_global'] > 0 ? 'bg-danger' : 'bg-success' }} text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_heures_disponibles'] }}h</h4>
                    <small>Heures disponibles</small>
                    @if($stats['deficit_heures_global'] > 0)
                        <div class="stat-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            -{{ $stats['deficit_heures_global'] }}h déficit
                        </div>
                    @else
                        <div class="stat-detail">
                            {{ $stats['taux_couverture_heures'] }}% couverture
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card stat-card bg-secondary text-white">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['taux_couverture_heures'] }}%</h4>
                    <small>Taux de couverture</small>
                    <div class="stat-detail">
                        {{ $stats['total_heures_requises'] }}h requises
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Résumé des alertes par catégorie --}}
    @if($stats['etablissements_avec_alertes'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert-summary-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Résumé des alertes - Action requise
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($stats['nombre_alertes_critiques'] > 0)
                        <div class="col-lg-4 mb-3">
                            <div class="alert-category critical">
                                <div class="alert-category-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="alert-category-content">
                                    <h6>Alertes Critiques</h6>
                                    <p class="mb-1">{{ $stats['nombre_alertes_critiques'] }} problème(s) urgent(s)</p>
                                    <small>Nécessitent une action immédiate</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($stats['nombre_alertes_avertissements'] > 0)
                        <div class="col-lg-4 mb-3">
                            <div class="alert-category warning">
                                <div class="alert-category-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="alert-category-content">
                                    <h6>Avertissements</h6>
                                    <p class="mb-1">{{ $stats['nombre_alertes_avertissements'] }} point(s) d'amélioration</p>
                                    <small>À traiter prochainement</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-4 mb-3">
                            <div class="alert-category info">
                                <div class="alert-category-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="alert-category-content">
                                    <h6>Établissements affectés</h6>
                                    <p class="mb-1">{{ $stats['etablissements_avec_alertes'] }}/{{ $stats['total_etablissements'] }} établissement(s)</p>
                                    <small>{{ round(($stats['etablissements_avec_alertes'] / max(1, $stats['total_etablissements'])) * 100, 1) }}% du complexe</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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

        {{-- Analyse des heures par établissement --}}
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Couverture horaire par établissement
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="heuresEtablissementChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques secondaires --}}
    <div class="row mb-4">
        {{-- Répartition étudiants par établissement --}}
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Étudiants par établissement
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="etudiantsEtablissementChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>

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

        {{-- Répartition des formateurs --}}
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>Formateurs par établissement
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="formateursEtablissementChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Analyse détaillée des établissements --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card etablissements-analysis-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Analyse détaillée des établissements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($complexe->etablissements as $etablissement)
                            @php
                                $etablissementStats = collect($stats['etablissements_alertes'])
                                    ->firstWhere('etablissement.id', $etablissement->id);
                                
                                $totalFormateurs = $etablissement->formateurs->count();
                                $totalFormations = $etablissement->formations->count();
                                $totalEtudiants = $etablissement->formations->sum(function($f) { return $f->groupes->sum('effectif'); });
                                $totalHeures = $etablissement->formations->sum(function($f) { return $f->modules->sum('masse_horaire'); });
                                $heuresDisponibles = $etablissement->formateurs->sum('masse_horaire_disponible');
                                $tauxCouverture = $totalHeures > 0 ? round(($heuresDisponibles / $totalHeures) * 100, 1) : 100;
                            @endphp
                            
                            <div class="col-lg-6 mb-4">
                                <div class="etablissement-analysis-item {{ $etablissementStats ? ($etablissementStats['has_critical'] ? 'has-critical' : 'has-warnings') : 'healthy' }}">
                                    <div class="etablissement-header">
                                        <div class="etablissement-icon">
                                            <i class="fas fa-school"></i>
                                        </div>
                                        <div class="etablissement-title">
                                            <h6>{{ $etablissement->nom }}</h6>
                                            @if($etablissementStats)
                                                <div class="status-indicator {{ $etablissementStats['has_critical'] ? 'critical' : 'warning' }}">
                                                    {{ $etablissementStats['completion_percentage'] }}% Complété
                                                </div>
                                            @else
                                                <div class="status-indicator healthy">100% Complété</div>
                                            @endif
                                        </div>
                                        <div class="etablissement-actions">
                                            <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="etablissement-metrics">
                                        <div class="metric">
                                            <span class="metric-label">Formations</span>
                                            <span class="metric-value">{{ $totalFormations }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">Formateurs</span>
                                            <span class="metric-value {{ $totalFormateurs == 0 ? 'alert' : '' }}">{{ $totalFormateurs }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">Étudiants</span>
                                            <span class="metric-value">{{ $totalEtudiants }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">Couverture</span>
                                            <span class="metric-value {{ $tauxCouverture < 100 ? 'alert' : '' }}">{{ $tauxCouverture }}%</span>
                                        </div>
                                    </div>
                                    
                                    @if($etablissementStats && count($etablissementStats['issues']) > 0)
                                        <div class="etablissement-issues">
                                            @foreach(array_slice($etablissementStats['issues'], 0, 2) as $issue)
                                            <div class="issue-item {{ $issue['type'] }}">
                                                <i class="fas {{ $issue['icon'] }}"></i>
                                                <span>{{ $issue['message'] }}</span>
                                            </div>
                                            @endforeach
                                            
                                            @if(count($etablissementStats['issues']) > 2)
                                                <div class="more-issues-link">
                                                    <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement->id) }}">
                                                        +{{ count($etablissementStats['issues']) - 2 }} autre(s) problème(s)
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions rapides et statistiques détaillées --}}
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

        {{-- Statistiques détaillées --}}
        <div class="col-lg-8 mb-4">
            <div class="card stats-detail-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>Analyse comparative
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-row">
                                <div class="stat-label">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    Total heures requises
                                </div>
                                <div class="stat-value">{{ number_format($stats['total_heures_requises']) }}h</div>
                            </div>
                            
                            <div class="stat-row">
                                <div class="stat-label">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Heures disponibles
                                </div>
                                <div class="stat-value">{{ number_format($stats['total_heures_disponibles']) }}h</div>
                            </div>
                            
                            <div class="stat-row">
                                <div class="stat-label">
                                    <i class="fas fa-exclamation-triangle {{ $stats['deficit_heures_global'] > 0 ? 'text-danger' : 'text-success' }} me-2"></i>
                                    Déficit/Surplus
                                </div>
                                <div class="stat-value {{ $stats['deficit_heures_global'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $stats['deficit_heures_global'] > 0 ? '-' : '+' }}{{ number_format(abs($stats['total_heures_disponibles'] - $stats['total_heures_requises'])) }}h
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="stat-row">
                                <div class="stat-label">
                                    <i class="fas fa-percentage text-info me-2"></i>
                                    Taux de couverture global
                                </div>
                                <div class="stat-value">{{ $stats['taux_couverture_heures'] }}%</div>
                            </div>
                            
                            <div class="stat-row">
                                <div class="stat-label">
                                    <i class="fas fa-balance-scale text-warning me-2"></i>
                                    Ratio étudiants/formateurs
                                </div>
                                <div class="stat-value">
                                    {{ $stats['total_formateurs'] > 0 ? round($stats['total_etudiants'] / $stats['total_formateurs'], 1) : 0 }}:1
                                </div>
                            </div>
                            
                            <div class="stat-row">
                                <div class="stat-label">
                                    <i class="fas fa-graduation-cap text-secondary me-2"></i>
                                    Formations par établissement
                                </div>
                                <div class="stat-value">
                                    {{ $stats['total_etablissements'] > 0 ? round($stats['total_formations'] / $stats['total_etablissements'], 1) : 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    
                    <div class="progress-section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Objectifs de performance globale</small>
                            <small class="text-muted">
                                {{ min(100, max(0, $stats['taux_couverture_heures'])) }}% atteint
                            </small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $stats['taux_couverture_heures'] >= 100 ? 'bg-success' : ($stats['taux_couverture_heures'] >= 80 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ min(100, $stats['taux_couverture_heures']) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles CSS améliorés --}}
<style>
/* Variables CSS */
:root {
    --ofppt-blue: #1E5F99;
    --ofppt-dark-blue: #0F3F66;
    --ofppt-green: #2E8B57;
    --critical-color: #dc3545;
    --warning-color: #ffc107;
    --success-color: #198754;
    --info-color: #0dcaf0;
}

/* Dashboard général */
.dashboard-title {
    color: var(--ofppt-blue);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Cartes statistiques */
.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card .stat-alert {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255,255,255,0.2);
    padding: 0.3rem;
    font-size: 0.75rem;
    backdrop-filter: blur(5px);
}

.stat-card .stat-detail {
    font-size: 0.75rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

/* Système de notifications amélioré */
.notification-system {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
}

.notification-bell-container {
    position: relative;
}

.notification-bell {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fff, #f8f9fa);
    border: 2px solid var(--ofppt-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(30, 95, 153, 0.2);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.notification-bell:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 6px 25px rgba(30, 95, 153, 0.3);
}

.notification-bell i {
    font-size: 1.5rem;
    color: var(--ofppt-blue);
    transition: transform 0.3s ease;
}

.notification-bell:hover i {
    animation: ring 0.6s ease-in-out;
}

@keyframes ring {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    50% { transform: rotate(-15deg); }
    75% { transform: rotate(10deg); }
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    min-width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    color: white;
    border: 2px solid white;
}

.notification-badge.badge-critical {
    background: var(--critical-color);
    animation: pulse 2s infinite;
}

.notification-badge.badge-warning {
    background: var(--warning-color);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.notification-dropdown {
    width: 600px !important;
    max-height: 80vh;
    overflow: hidden;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    border-radius: 15px;
    padding: 0;
}

.notification-header {
    background: linear-gradient(135deg, var(--ofppt-blue), var(--ofppt-dark-blue));
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 15px 15px 0 0;
}

.notification-content {
    max-height: 60vh;
    overflow-y: auto;
    padding: 0;
}

.alert-summary {
    display: flex;
    gap: 0.5rem;
}

.alert-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: bold;
}

.alert-badge.critical {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.alert-badge.warning {
    background: rgba(255, 193, 7, 0.2);
    color: #b8860b;
}

.notification-item {
    padding: 0;
    border-bottom: 1px solid #f0f0f0;
}

.notification-item:last-child {
    border-bottom: none;
}

.etablissement-notification {
    display: flex;
    padding: 1.5rem;
    gap: 1rem;
    transition: background-color 0.2s ease;
}

.etablissement-notification:hover {
    background-color: #f8f9fa;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-icon.critical {
    background: linear-gradient(135deg, var(--critical-color), #b02a37);
    color: white;
}

.notification-icon.warning {
    background: linear-gradient(135deg, var(--warning-color), #d39e00);
    color: white;
}

.notification-content-body {
    flex-grow: 1;
}

.notification-title {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.notification-title h6 {
    margin: 0;
    font-weight: 600;
    color: var(--ofppt-dark-blue);
}

.completion-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: auto;
}

.completion-bar {
    width: 60px;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.completion-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--critical-color), var(--warning-color), var(--success-color));
    border-radius: 3px;
    transition: width 0.3s ease;
}

.completion-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ofppt-blue);
}

.issues-summary {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.issue-count {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-weight: 500;
}

.issue-count.critical {
    background: rgba(220, 53, 69, 0.1);
    color: var(--critical-color);
}

.issue-count.warning {
    background: rgba(255, 193, 7, 0.1);
    color: #b8860b;
}

.issues-details {
    margin-bottom: 0.75rem;
}

.issue-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}

.issue-item i {
    margin-top: 0.1rem;
    flex-shrink: 0;
}

.issue-item.danger {
    color: var(--critical-color);
}

.issue-item.warning {
    color: #b8860b;
}

.issue-message {
    font-weight: 500;
    line-height: 1.3;
}

.issue-details {
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 0.25rem;
    font-style: italic;
}

.more-issues {
    color: var(--ofppt-blue);
    font-size: 0.8rem;
    font-weight: 500;
    text-align: center;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 0.5rem;
}

.no-notifications {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--success-color);
}

.no-notifications i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.no-notifications h6 {
    color: var(--ofppt-dark-blue);
    margin-bottom: 0.5rem;
}

.no-notifications p {
    color: #6c757d;
    margin: 0;
}

/* Résumé des alertes par catégorie */
.alert-summary-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.1);
    border-left: 5px solid var(--critical-color);
}

.alert-summary-card .card-header {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 193, 7, 0.1));
    border: none;
    border-radius: 15px 15px 0 0;
    color: var(--ofppt-dark-blue);
}

.alert-category {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.alert-category:hover {
    transform: translateY(-2px);
}

.alert-category.critical {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.alert-category.warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.alert-category.info {
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.1), rgba(13, 202, 240, 0.05));
    border: 1px solid rgba(13, 202, 240, 0.2);
}

.alert-category-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.alert-category.critical .alert-category-icon {
    background: var(--critical-color);
    color: white;
}

.alert-category.warning .alert-category-icon {
    background: var(--warning-color);
    color: white;
}

.alert-category.info .alert-category-icon {
    background: var(--info-color);
    color: white;
}

.alert-category-content h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--ofppt-dark-blue);
}

.alert-category-content p {
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.alert-category-content small {
    color: #6c757d;
}

/* Cartes de graphiques */
.chart-card, .etablissements-analysis-card, .actions-card, .stats-detail-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: box-shadow 0.3s ease;
}

.chart-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.etablissements-analysis-card:hover, .actions-card:hover, .stats-detail-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.chart-card .card-header, .etablissements-analysis-card .card-header, 
.actions-card .card-header, .stats-detail-card .card-header {
    background: linear-gradient(135deg, var(--ofppt-blue), var(--ofppt-dark-blue));
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 1rem 1.5rem;
    border: none;
}

.chart-card .card-body {
    position: relative;
    min-height: 300px;
    padding: 1.5rem;
}

.chart-card canvas {
    max-width: 100% !important;
    height: 280px !important;
    display: block;
}

/* Analyse détaillée des établissements */
.etablissement-analysis-item {
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.etablissement-analysis-item.healthy {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.05), rgba(25, 135, 84, 0.02));
    border-color: rgba(25, 135, 84, 0.2);
}

.etablissement-analysis-item.has-warnings {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.05), rgba(255, 193, 7, 0.02));
    border-color: rgba(255, 193, 7, 0.3);
}

.etablissement-analysis-item.has-critical {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05), rgba(220, 53, 69, 0.02));
    border-color: rgba(220, 53, 69, 0.3);
    animation: subtle-pulse 3s infinite;
}

@keyframes subtle-pulse {
    0%, 100% { border-color: rgba(220, 53, 69, 0.3); }
    50% { border-color: rgba(220, 53, 69, 0.5); }
}

.etablissement-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.etablissement-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ofppt-blue), var(--ofppt-dark-blue));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.etablissement-title {
    flex-grow: 1;
}

.etablissement-title h6 {
    margin: 0;
    font-weight: 600;
    color: var(--ofppt-dark-blue);
}

.status-indicator {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    margin-top: 0.25rem;
    display: inline-block;
}

.status-indicator.healthy {
    background: rgba(25, 135, 84, 0.1);
    color: var(--success-color);
}

.status-indicator.warning {
    background: rgba(255, 193, 7, 0.1);
    color: #b8860b;
}

.status-indicator.critical {
    background: rgba(220, 53, 69, 0.1);
    color: var(--critical-color);
}

.etablissement-metrics {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: rgba(255,255,255,0.5);
    border-radius: 8px;
}

.metric {
    text-align: center;
    flex: 1;
}

.metric-label {
    display: block;
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.metric-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--ofppt-dark-blue);
}

.metric-value.alert {
    color: var(--critical-color);
}

.etablissement-issues {
    margin-top: 1rem;
    padding: 0.75rem;
    background: rgba(248, 249, 250, 0.7);
    border-radius: 8px;
}

.etablissement-issues .issue-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    padding: 0.5rem;
    border-radius: 6px;
}

.etablissement-issues .issue-item.danger {
    background: rgba(220, 53, 69, 0.05);
    color: var(--critical-color);
    border-left: 3px solid var(--critical-color);
}

.etablissement-issues .issue-item.warning {
    background: rgba(255, 193, 7, 0.05);
    color: #b8860b;
    border-left: 3px solid var(--warning-color);
}

.more-issues-link {
    text-align: center;
    margin-top: 0.5rem;
}

.more-issues-link a {
    color: var(--ofppt-blue);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
}

.more-issues-link a:hover {
    text-decoration: underline;
}

/* Actions rapides */
.actions-card .btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.actions-card .btn:hover {
    transform: translateY(-1px);
}

/* Statistiques détaillées */
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

/* Responsive Design */
@media (max-width: 992px) {
    .notification-dropdown {
        width: 480px !important;
    }
    
    .chart-card .card-body {
        min-height: 250px;
        padding: 1rem;
    }
    
    .etablissement-metrics {
        flex-wrap: wrap;
    }
    
    .metric {
        flex: 0 0 50%;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 768px) {
    .notification-dropdown {
        width: 90vw !important;
        right: 5vw;
    }
    
    .dashboard-actions {
        width: 100%;
        margin-top: 1rem;
    }
    
    .dashboard-actions .btn-group {
        width: 100%;
    }
    
    .chart-card .card-body {
        min-height: 200px;
        padding: 0.75rem;
    }
    
    .chart-card canvas {
        height: 180px !important;
    }
    
    .etablissement-analysis-item {
        margin-bottom: 1rem;
    }
    
    .etablissement-metrics {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .metric {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .metric-label, .metric-value {
        display: inline;
    }
    
    /* Désactiver les effets hover sur mobile */
    .chart-card:hover, .etablissements-analysis-card:hover, 
    .actions-card:hover, .stats-detail-card:hover {
        transform: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
    }
}

@media (max-width: 576px) {
    .notification-bell {
        width: 48px;
        height: 48px;
    }
    
    .notification-bell i {
        font-size: 1.25rem;
    }
    
    .chart-card .card-body {
        min-height: 180px;
        padding: 0.5rem;
    }
    
    .chart-card canvas {
        height: 160px !important;
    }
}

/* Amélioration de la performance */
.chart-card, .etablissements-analysis-card, .actions-card, .stats-detail-card {
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

    // Configuration commune pour tous les graphiques
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

    // === 2. Graphique couverture horaire par établissement ===
    const heuresCtx = document.getElementById('heuresEtablissementChart');
    if (heuresCtx) {
        new Chart(heuresCtx, {
            type: 'bar',
            data: {
                labels: chartsData.heures_par_etablissement.map(item => item.name),
                datasets: [
                    {
                        label: 'Heures requises',
                        data: chartsData.heures_par_etablissement.map(item => item.heures_requises),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Heures disponibles',
                        data: chartsData.heures_par_etablissement.map(item => item.heures_disponibles),
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // === 3. Graphique étudiants par établissement ===
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
                    borderColor: '#ffffff'
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
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // === 4. Graphique types de formation ===
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
                    borderColor: '#ffffff'
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // === 5. Graphique formateurs par établissement ===
    const formateursCtx = document.getElementById('formateursEtablissementChart');
    if (formateursCtx) {
        new Chart(formateursCtx, {
            type: 'bar',
            data: {
                labels: chartsData.formateurs_par_etablissement.map(item => item.name),
                datasets: [{
                    label: 'Nombre de formateurs',
                    data: chartsData.formateurs_par_etablissement.map(item => item.value),
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    borderRadius: 6
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
});

function refreshDashboard() {
    window.location.reload();
}
</script>
@endpush