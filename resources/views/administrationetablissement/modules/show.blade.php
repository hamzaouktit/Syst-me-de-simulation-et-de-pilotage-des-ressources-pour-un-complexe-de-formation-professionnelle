@extends('layouts.app')

@section('title', 'Détails du Module')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $module->nom }}</h1>
                    <p class="text-muted">Détails du module de formation</p>
                </div>
                <div>
                    <a href="{{ route('administrationetablissement.modules.edit', $module) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informations principales -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nom du Module</label>
                                <p class="fw-bold">{{ $module->nom }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Masse Horaire</label>
                                <p>
                                    <span class="badge bg-info fs-6">{{ $module->masse_horaire }} heures</span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Date de Création</label>
                                <p>{{ $module->created_at->format('d/m/Y à H:i') }}</p>
                            </div>

                            <div>
                                <label class="form-label text-muted">Dernière Modification</label>
                                <p>{{ $module->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formations associées -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-graduation-cap me-2"></i>Formations Associées
                                <span class="badge bg-primary rounded-pill ms-2">{{ $module->formations->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($module->formations->count() > 0)
                                @foreach($module->formations as $formation)
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="mb-2">{{ $formation->titre }}</h6>
                                        <div class="small text-muted">
                                            <div><strong>Niveau :</strong> {{ $formation->niveau }}</div>
                                            <div><strong>Type :</strong> 
                                                <span class="badge bg-secondary">{{ ucfirst($formation->type) }}</span>
                                            </div>
                                            @if($formation->etablissement)
                                                <div><strong>Établissement :</strong> {{ $formation->etablissement->nom ?? 'N/A' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune formation associée</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Métiers associés -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase me-2"></i>Métiers Associés
                                <span class="badge bg-secondary rounded-pill ms-2">{{ $module->metiers->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($module->metiers->count() > 0)
                                @foreach($module->metiers as $metier)
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="mb-2">{{ $metier->nom }}</h6>
                                        @if($metier->description)
                                            <p class="small text-muted mb-0">{{ $metier->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun métier associé</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ $module->formations->count() }}</h3>
                                <p class="text-muted mb-0">Formation(s) associée(s)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-secondary mb-1">{{ $module->metiers->count() }}</h3>
                                <p class="text-muted mb-0">Métier(s) associé(s)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-info mb-1">{{ $module->masse_horaire }}</h3>
                                <p class="text-muted mb-0">Heures de formation</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">
                                    {{ $module->formations->sum(function($formation) { return $formation->groupes->count(); }) }}
                                </h3>
                                <p class="text-muted mb-0">Groupe(s) concerné(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('administrationetablissement.modules.edit', $module) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier ce module
                        </a>
                        
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Supprimer ce module
                        </button>

                        <a href="{{ route('administrationetablissement.modules.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Créer un nouveau module
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="delete-form" action="{{ route('administrationetablissement.modules.destroy', $module) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce module ? Cette action est irréversible et supprimera toutes les associations avec les formations et métiers.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection