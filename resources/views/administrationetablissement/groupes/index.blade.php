@extends('layouts.app')

@section('title', 'Gestion des Groupes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-users"></i> Gestion des Groupes
                </h1>
                <a href="{{ route('administrationetablissement.groupes.create') }}" 
                   class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Nouveau Groupe
                </a>
            </div>


            <!-- Filtres -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> Filtres
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('administrationetablissement.groupes.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Rechercher par nom</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Nom du groupe...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="formation_id">Formation</label>
                                    <select class="form-control" id="formation_id" name="formation_id">
                                        <option value="">Toutes les formations</option>
                                        @foreach($formations as $formation)
                                            <option value="{{ $formation->id }}" 
                                                    {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                                                {{ $formation->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="annee_id">Année</label>
                                    <select class="form-control" id="annee_id" name="annee_id">
                                        <option value="">Toutes les années</option>
                                        @foreach($annees as $annee)
                                            <option value="{{ $annee->id }}" 
                                                    {{ request('annee_id') == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->annee }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('administrationetablissement.groupes.index') }}" 
                                           class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des groupes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Liste des Groupes
                        <span class="badge badge-info ml-2">{{ $groupes->total() }} groupe(s)</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($groupes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Effectif</th>
                                        <th>Formation</th>
                                        <th>Année</th>
                                        <th>Date de Création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupes as $groupe)
                                        <tr>
                                            <td>{{ $groupe->id }}</td>
                                            <td>
                                                <span class="font-weight-bold ">{{ $groupe->nom }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark text-white">{{ $groupe->effectif }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $groupe->formation->titre }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark text-white">{{ $groupe->anneeDeFormation->annee }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $groupe->created_at->format('d/m/Y à H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- Bouton Voir -->
                                                    <a href="{{ route('administrationetablissement.groupes.show', $groupe) }}" 
                                                       class="btn btn-info btn-sm rounded-circle action-btn" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Bouton Modifier -->
                                                    <a href="{{ route('administrationetablissement.groupes.edit', $groupe) }}" 
                                                       class="btn btn-warning btn-sm rounded-circle action-btn" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Bouton Supprimer -->
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm rounded-circle action-btn" 
                                                            onclick="confirmDelete({{ $groupe->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Formulaire de suppression caché -->
                                                <form id="delete-form-{{ $groupe->id }}" 
                                                      action="{{ route('administrationetablissement.groupes.destroy', $groupe) }}" 
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
                        <div class="d-flex justify-content-center">
                            {{ $groupes->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Aucun groupe trouvé</h5>
                            <p class="text-gray-500 mb-4">
                                @if(request()->hasAny(['search', 'formation_id', 'annee_id']))
                                    Aucun groupe ne correspond aux critères de recherche.
                                @else
                                    Commencez par créer votre premier groupe.
                                @endif
                            </p>
                            <a href="{{ route('administrationetablissement.groupes.create') }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i>Créer un Groupe
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour la confirmation de suppression -->
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce groupe ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>

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
</style>
@endsection