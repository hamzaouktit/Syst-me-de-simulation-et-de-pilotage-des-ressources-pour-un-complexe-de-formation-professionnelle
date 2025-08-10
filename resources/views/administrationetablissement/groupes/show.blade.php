@extends('layouts.app')

@section('title', 'Détails du Groupe')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-users"></i> {{ $groupe->nom }}
                </h1>
                <div>
                    <a href="{{ route('administrationetablissement.groupes.edit', $groupe) }}" 
                       class="btn btn-warning btn-sm shadow-sm mr-2">
                        <i class="fas fa-edit fa-sm text-white-50"></i> Modifier
                    </a>
                    <a href="{{ route('administrationetablissement.groupes.index') }}" 
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
                                        <span class="badge bg-dark text-white ">{{ $groupe->id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-tag text-primary"></i> Nom :
                                    </td>
                                    <td>
                                        <span class="badge bg-dark text-white">{{ $groupe->nom }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-users text-primary"></i> Effectif :
                                    </td>
                                    <td>
                                        <span class="badge bg-dark text-white">{{ $groupe->effectif }} étudiant(s)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-clock text-primary"></i> Créé le :
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $groupe->created_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-edit text-primary"></i> Modifié le :
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $groupe->updated_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Informations de formation -->
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-graduation-cap"></i> Formation & Année
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="card border-left-success mb-3">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Formation
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                {{ $groupe->formation->titre }}
                                            </div>
                                            <small class="text-muted">
                                                Niveau: {{ $groupe->formation->niveau }}
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-graduation-cap fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-left-info mb-3">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Année de Formation
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                {{ $groupe->anneeDeFormation->annee }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-alt fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques du groupe -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie"></i> Statistiques du Groupe
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Effectif Total
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $groupe->effectif }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Formation
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        {{ Str::limit($groupe->formation->titre, 15) }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        Année
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $groupe->anneeDeFormation->annee }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                        Capacité
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        {{ number_format(($groupe->effectif / 100) * 100, 0) }}%
                                                    </div>
                                                    <small class="text-muted">{{ $groupe->effectif }}/100</small>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                        Gérez ce groupe avec les actions suivantes :
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('administrationetablissement.groupes.edit', $groupe) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="confirmDelete()">
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
    </div>
</div>

<!-- Formulaire de suppression -->
<form id="delete-form" action="{{ route('administrationetablissement.groupes.destroy', $groupe) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce groupe ? Cette action est irréversible.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection