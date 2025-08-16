@extends('layouts.app')

@section('title', 'Détails du Directeur')

@section('content')
<div class="container-fluid">
    {{-- En-tête de page --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Détails du Directeur</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.complexe') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('administrationcomplexe.directeurs.index') }}">Directeurs</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $directeur->nom }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('administrationcomplexe.directeurs.edit', $directeur->id) }}" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('administrationcomplexe.directeurs.index') }}" 
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Informations principales --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-tie"></i> Informations du Directeur
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('administrationcomplexe.directeurs.edit', $directeur->id) }}">
                                <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                Modifier
                            </a>
                            @if(!$directeur->etablissement)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>
                                Supprimer
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Nom complet :</label>
                            <p class="text-gray-900 mb-0">{{ $directeur->nom }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Email :</label>
                            <p class="text-gray-900 mb-0">
                                <a href="mailto:{{ $directeur->email }}" class="text-decoration-none">
                                    {{ $directeur->email }}
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Rôle :</label>
                            <p class="mb-0">
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $directeur->role)) }}</span>
                            </p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Date de création :</label>
                            <p class="text-gray-900 mb-0">{{ $directeur->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Dernière modification :</label>
                            <p class="text-gray-900 mb-0">{{ $directeur->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Statut :</label>
                            <p class="mb-0">
                                @if($directeur->etablissement)
                                    <span class="badge bg-success">Affecté à un établissement</span>
                                @else
                                    <span class="badge bg-warning">Non affecté</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Établissement associé --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Établissement
                    </h6>
                </div>
                <div class="card-body">
                    @if($directeur->etablissement)
                        <div class="text-center mb-3">
                            <i class="fas fa-building fa-3x text-success mb-3"></i>
                            <h5 class="text-gray-800">{{ $directeur->etablissement->nom }}</h5>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-2">
                            <small class="font-weight-bold text-gray-800">Adresse :</small>
                            <p class="text-gray-900 mb-0">{{ $directeur->etablissement->adresse }}</p>
                        </div>
                        
                        @if($directeur->etablissement->complexe)
                        <div class="mb-2">
                            <small class="font-weight-bold text-gray-800">Complexe :</small>
                            <p class="text-gray-900 mb-0">{{ $directeur->etablissement->complexe->nom }}</p>
                        </div>
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ route('administrationcomplexe.etablissements.show', $directeur->etablissement->id) }}" 
                               class="btn btn-outline-primary btn-sm btn-block">
                                <i class="fas fa-eye"></i> Voir l'établissement
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <i class="fas fa-building fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-3">Aucun établissement associé</p>
                            <small class="text-muted">
                                Ce directeur n'est actuellement affecté à aucun établissement.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Statistiques rapides --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    @if($directeur->etablissement)
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <h4 class="text-primary">{{ $directeur->etablissement->formations->count() }}</h4>
                                    <small class="text-gray-600">Formations</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ $directeur->etablissement->formateurs->count() }}</h4>
                                <small class="text-gray-600">Formateurs</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <h4 class="text-info">
                                        {{ $directeur->etablissement->formations->sum(function($formation) {
                                            return $formation->groupes->count();
                                        }) }}
                                    </h4>
                                    <small class="text-gray-600">Groupes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-warning">{{ $directeur->etablissement->espacesPedagogiques->count() }}</h4>
                                <small class="text-gray-600">Espaces</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            <i class="fas fa-chart-line fa-2x mb-3"></i>
                            <p class="mb-0">Aucune statistique disponible</p>
                            <small>Affectez ce directeur à un établissement pour voir les statistiques</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Historique des activités --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Informations système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold text-gray-800" style="width: 200px;">ID Système :</td>
                                    <td class="text-gray-900">{{ $directeur->id }}</td>
                                    <td class="fw-bold text-dark bg-white p-1" style="width: 200px;">Email vérifié :</td>
                                    <td class="text-gray-900">
                                        @if($directeur->email_verified_at)
                                            <span class="badge bg-success">Vérifié le {{ $directeur->email_verified_at->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-warning">Non vérifié</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">Date de création :</td>
                                    <td class="text-gray-900">{{ $directeur->created_at->format('d/m/Y à H:i:s') }}</td>
                                    <td class="font-weight-bold text-gray-800">Dernière modification :</td>
                                    <td class="text-gray-900">{{ $directeur->updated_at->format('d/m/Y à H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">Temps depuis création :</td>
                                    <td class="text-gray-900">{{ $directeur->created_at->diffForHumans() }}</td>
                                    <td class="font-weight-bold text-gray-800">Dernière mise à jour :</td>
                                    <td class="text-gray-900">{{ $directeur->updated_at->diffForHumans() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
@if(!$directeur->etablissement)
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Attention !</strong> Cette action est irréversible.
                </div>
                <p>Êtes-vous sûr de vouloir supprimer le directeur <strong>{{ $directeur->nom }}</strong> ?</p>
                <p class="text-muted">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        Cette action supprimera définitivement toutes les informations associées à ce directeur.
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <form action="{{ route('administrationcomplexe.directeurs.destroy', $directeur->id) }}" 
                      method="POST" 
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animation des cartes au chargement
    $('.card').hide().fadeIn(800);
    
    // Tooltip pour les badges
    $('[data-toggle="tooltip"]').tooltip();
    
    // Confirmation avant suppression
    $('#deleteModal form').on('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: 'Cette action supprimera définitivement le directeur {{ $directeur->nom }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    
    // Auto-refresh des statistiques si établissement existe
    @if($directeur->etablissement)
    function refreshStats() {
        // Ici vous pouvez ajouter du code AJAX pour rafraîchir les statistiques
        console.log('Refreshing stats...');
    }
    
    // Refresh toutes les 30 secondes
    setInterval(refreshStats, 30000);
    @endif
});
</script>
@endpush

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,.1) !important;
}

.badge {
    font-size: 0.85em;
}

.table td {
    vertical-align: middle;
}

.border-right {
    border-right: 1px solid #e3e6f0;
}

@media (max-width: 768px) {
    .border-right {
        border-right: none;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
}
</style>
@endpush