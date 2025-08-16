@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Modules</h3>
                    <a href="{{ route('administrationetablissement.modules.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Module
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationetablissement.modules.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Rechercher un module..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="formation_id" class="form-control">
                                            <option value="">Toutes les formations</option>
                                            @foreach($formations as $formation)
                                                <option value="{{ $formation->id }}" 
                                                    {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                                                    {{ $formation->titre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="masse_horaire_min" class="form-control" 
                                               placeholder="Heures min" value="{{ request('masse_horaire_min') }}" min="1">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="masse_horaire_max" class="form-control" 
                                               placeholder="Heures max" value="{{ request('masse_horaire_max') }}" min="1">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-search"></i> Filtrer
                                            </button>
                                            <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tableau -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom du Module</th>
                                    <th>Masse Horaire</th>
                                    <th>Formation</th>
                                    <th>Niveau Formation</th>
                                    <th>Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($modules as $module)
                                    <tr>
                                        <td>{{ $module->id }}</td>
                                        <td>
                                            <strong>{{ $module->nom }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($module->masse_horaire <= 20) bg-danger
                                                @elseif($module->masse_horaire <= 50) bg-warning
                                                @elseif($module->masse_horaire <= 100) bg-info
                                                @else bg-success
                                                @endif">
                                                {{ $module->masse_horaire }}h
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-primary fw-bold">
                                                {{ $module->formation ? $module->formation->titre : 'Non assigné' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($module->formation)
                                                <span class="badge bg-secondary">
                                                    {{ $module->formation->niveau }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $module->created_at ? $module->created_at->format('d/m/Y') : 'Non défini' }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.modules.show', $module) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('administrationetablissement.modules.edit', $module) }}" 
                                                   class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('administrationetablissement.modules.destroy', $module) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-circle action-btn" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-cube fa-3x mb-3"></i>
                                            <p>Aucun module trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $modules->appends(request()->query())->links() }}
                    </div>

                    <!-- Statistiques -->
                    @if($modules->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-chart-bar text-info"></i> Statistiques
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h4 text-primary">{{ $modules->total() }}</div>
                                                    <small class="text-muted">Total modules</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h4 text-success">{{ $modules->sum('masse_horaire') }}h</div>
                                                    <small class="text-muted">Total heures</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h4 text-info">{{ number_format($modules->avg('masse_horaire'), 1) }}h</div>
                                                    <small class="text-muted">Moyenne heures</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h4 text-warning">{{ $formations->count() }}</div>
                                                    <small class="text-muted">Formations liées</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style personnalisé pour les boutons d'action */
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .action-btn i {
        font-size: 0.9rem;
    }

    /* Badges colorés pour la masse horaire */
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* Effet hover sur les lignes du tableau */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>
@endsection