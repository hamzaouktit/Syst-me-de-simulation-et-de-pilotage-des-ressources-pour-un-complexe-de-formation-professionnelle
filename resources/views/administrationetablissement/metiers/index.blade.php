@extends('layouts.app')

@section('title', 'Gestion des Métiers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Liste des Métiers</h3>
                    <a href="{{ route('administrationetablissement.metiers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Métier
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metiers as $metier)
                                    <tr>
                                        <td>{{ $metier->id }}</td>
                                        <td>{{ $metier->nom }}</td>
                                        <td>{{ Str::limit($metier->description, 50) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('administrationetablissement.metiers.show', $metier->id) }}" 
                                                   class="btn btn-info btn-sm" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('administrationetablissement.metiers.edit', $metier->id) }}" 
                                                   class="btn btn-warning btn-sm" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('administrationetablissement.metiers.destroy', $metier->id) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce métier ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun métier trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $metiers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection