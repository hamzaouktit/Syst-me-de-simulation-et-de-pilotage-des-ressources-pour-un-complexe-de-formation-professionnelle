@extends('layouts.app')

@section('title', 'Gestion des Années de Formation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-calendar-alt"></i> Années de Formation
                </h1>
                <a href="{{ route('administrationetablissement.anneesdeformations.create') }}" 
                   class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Nouvelle Année
                </a>
            </div>

            

            <!-- Tableau des années de formation -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Liste des Années de Formation
                        <span class="badge badge-info ml-2">{{ $anneesDeFormation->total() }} année(s)</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($anneesDeFormation->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Année</th>
                                        <th>Nombre de Groupes</th>
                                        <th>Date de Création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anneesDeFormation as $annee)
                                        <tr>
                                            <td>{{ $annee->id }}</td>
                                            <td>
                                                <span class=" font-weight-normal">
                                                    {{ $annee->annee }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-normal">
                                                    {{ $annee->groupes()->count() }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $annee->created_at->format('d/m/Y à H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('administrationetablissement.anneesdeformations.show', $annee) }}" 
                                                       class="btn btn-info btn-sm" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('administrationetablissement.anneesdeformations.edit', $annee) }}" 
                                                       class="btn btn-warning btn-sm" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="confirmDelete({{ $annee->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Formulaire de suppression caché -->
                                                <form id="delete-form-{{ $annee->id }}" 
                                                      action="{{ route('administrationetablissement.anneesdeformations.destroy', $annee) }}" 
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
                            {{ $anneesDeFormation->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Aucune année de formation</h5>
                            <p class="text-gray-500 mb-4">Commencez par créer votre première année de formation.</p>
                            <a href="{{ route('administrationetablissement.anneesdeformations.create') }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i>Créer une Année
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
    if (confirm('Êtes-vous sûr de vouloir supprimer cette année de formation ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection