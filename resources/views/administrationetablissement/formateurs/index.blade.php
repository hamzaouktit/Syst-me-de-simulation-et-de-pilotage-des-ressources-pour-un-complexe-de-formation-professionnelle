@extends('layouts.app')

@section('title', 'Liste des Formateurs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Liste des Formateurs</h3>
                    <a href="{{ route('administrationetablissement.formateurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un Formateur
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

                    @if($formateurs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Masse Horaire Disponible</th>
                                        <th>Métiers</th>
                                        <th>Établissement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formateurs as $formateur)
                                        <tr>
                                            <td>{{ $formateur->id }}</td>
                                            <td>{{ $formateur->nom }}</td>
                                            <td>{{ $formateur->email }}</td>
                                            <td>{{ $formateur->masse_horaire_disponible }}h</td>
                                            <td>
                                                @foreach($formateur->metiers as $metier)
                                                    <span class=" text-primary">{{ $metier->nom }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $formateur->etablissement->nom }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('administrationetablissement.formateurs.show', $formateur) }}" 
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('administrationetablissement.formateurs.edit', $formateur) }}" 
                                                       class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrationetablissement.formateurs.destroy', $formateur) }}" 
                                                          method="POST" style="display: inline;"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $formateurs->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Aucun formateur trouvé.
                            <br>
                            <a href="{{ route('administrationetablissement.formateurs.create') }}" class="btn btn-primary mt-2">
                                Créer le premier formateur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection