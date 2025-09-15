@extends('layouts.app')

@section('title', 'Gestion des Modules')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Gestion des Modules</h1>
                    <p class="text-muted">Gérer les modules de formation</p>
                </div>
                <a href="{{ route('administrationetablissement.modules.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouveau Module
                </a>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('administrationetablissement.modules.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Rechercher</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nom du module...">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="formation_id" class="form-label">Formation</label>
                                <select class="form-select" id="formation_id" name="formation_id">
                                    <option value="">Toutes les formations</option>
                                    @foreach($formations as $formation)
                                        <option value="{{ $formation->id }}" 
                                                {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                                            {{ $formation->titre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="metier_id" class="form-label">Métier</label>
                                <select class="form-select" id="metier_id" name="metier_id">
                                    <option value="">Tous les métiers</option>
                                    @foreach($metiers as $metier)
                                        <option value="{{ $metier->id }}" 
                                                {{ request('metier_id') == $metier->id ? 'selected' : '' }}>
                                            {{ $metier->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="masse_horaire_min" class="form-label">Heures min</label>
                                        <input type="number" class="form-control" id="masse_horaire_min" 
                                               name="masse_horaire_min" value="{{ request('masse_horaire_min') }}" 
                                               placeholder="Min">
                                    </div>
                                    <div class="col-6">
                                        <label for="masse_horaire_max" class="form-label">Heures max</label>
                                        <input type="number" class="form-control" id="masse_horaire_max" 
                                               name="masse_horaire_max" value="{{ request('masse_horaire_max') }}" 
                                               placeholder="Max">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Messages de succès -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tableau des modules -->
            <div class="card">
                <div class="card-body p-0">
                    @if($modules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nom du Module</th>
                                        <th>Masse Horaire</th>
                                        <th>Formations</th>
                                        <th>Métiers</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        <tr>
                                            <td>
                                                <strong>{{ $module->nom }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $module->masse_horaire }}h</span>
                                            </td>
                                            <td>
                                                @if($module->formations->count() > 0)
                                                    @foreach($module->formations as $formation)
                                                        <span class="badge bg-primary me-1 mb-1">{{ $formation->titre }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Aucune formation</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($module->metiers->count() > 0)
                                                    @foreach($module->metiers as $metier)
                                                        <span class="badge bg-secondary me-1 mb-1">{{ $metier->nom }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Aucun métier</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('administrationetablissement.modules.show', $module) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('administrationetablissement.modules.edit', $module) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer" onclick="confirmDelete({{ $module->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Formulaire de suppression caché -->
                                                <form id="delete-form-{{ $module->id }}" 
                                                      action="{{ route('administrationetablissement.modules.destroy', $module) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($modules->hasPages())
                            <div class="card-footer">
                                {{ $modules->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun module trouvé</h5>
                            <p class="text-muted">Créez votre premier module ou modifiez vos critères de recherche.</p>
                            <a href="{{ route('administrationetablissement.modules.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Créer un module
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de confirmation de suppression -->
<script>
function confirmDelete(moduleId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce module ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + moduleId).submit();
    }
}
</script>
@endsection