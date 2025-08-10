@extends('layouts.app')

@section('title', 'Détails de l\'Année de Formation')

@section('content')
<div class="container-fluid">

    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt"></i> Année {{ $anneesdeformation->annee }}
        </h1>
        <div>
            <a href="{{ route('administrationetablissement.anneesdeformations.edit', $anneesdeformation) }}" 
               class="btn btn-warning btn-sm shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Modifier
            </a>
            <a href="{{ route('administrationetablissement.anneesdeformations.index') }}" 
               class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informations Générales
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold text-gray-800">
                                <i class="fas fa-hashtag text-primary"></i> ID :
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $anneesdeformation->id }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-800">
                                <i class="fas fa-calendar-alt text-primary"></i> Année :
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $anneesdeformation->annee }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-800">
                                <i class="fas fa-clock text-primary"></i> Créé le :
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $anneesdeformation->created_at->format('d/m/Y à H:i') }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-800">
                                <i class="fas fa-edit text-primary"></i> Modifié le :
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $anneesdeformation->updated_at->format('d/m/Y à H:i') }}
                                </small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-info mb-3">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Groupes associés
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $anneesdeformation->groupes()->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des groupes associés -->
    <div class="row">
        <div class="col-12">
            @if($anneesdeformation->groupes()->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users"></i> Groupes Associés
                            <span class="badge badge-info ml-2">{{ $anneesdeformation->groupes()->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Effectif</th>
                                        <th>Formation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anneesdeformation->groupes as $groupe)
                                        <tr>
                                            <td>{{ $groupe->id }}</td>
                                            <td><strong>{{ $groupe->nom }}</strong></td>
                                            <td><span class="badge badge-info">{{ $groupe->effectif }}</span></td>
                                            <td>
                                                @if($groupe->formation)
                                                    <small class="text-muted">{{ $groupe->formation->titre }}</small>
                                                @else
                                                    <small class="text-muted">Non assignée</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('administrationetablissement.groupes.show', $groupe) }}" 
                                                   class="btn btn-info btn-sm" title="Voir le groupe">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- Tu peux ajouter d'autres actions ici -->
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users"></i> Groupes Associés
                        </h6>
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">Aucun groupe associé</h5>
                        <p class="text-gray-500">Cette année de formation n'a pas encore de groupes assignés.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-12">
            <div class="card border-left-primary shadow mb-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Actions Rapides
                            </div>
                            <div class="text-sm text-gray-800 mb-3">
                                Gérez cette année de formation avec les actions suivantes :
                            </div>
                            <div class="btn-group" role="group">
                                <a href="{{ route('administrationetablissement.anneesdeformations.edit', $anneesdeformation) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="confirmDelete()" 
                                        @if($anneesdeformation->groupes()->count() > 0) disabled title="Impossible de supprimer : contient des groupes" @endif>
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Formulaire de suppression -->
<form id="delete-form" action="{{ route('administrationetablissement.anneesdeformations.destroy', $anneesdeformation) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette année de formation ? Cette action est irréversible.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
