@extends('layouts.app')

@section('title', 'Détails du Formateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Détails du Formateur</h3>
                    <div>
                        <a href="{{ route('administrationetablissement.formateurs.edit', $formateur) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('administrationetablissement.formateurs.index') }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="40%">ID:</th>
                                        <td>{{ $formateur->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nom:</th>
                                        <td>{{ $formateur->nom }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>
                                            <a href="mailto:{{ $formateur->email }}">{{ $formateur->email }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Masse Horaire Disponible:</th>
                                        <td>{{ $formateur->masse_horaire_disponible }} heures</td>
                                    </tr>
                                    <tr>
                                        <th>Établissement:</th>
                                        <td>{{ $formateur->etablissement->nom }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Métiers enseignés:</strong></label>
                                <div class="mt-2">
                                    @if($formateur->metiers->count() > 0)
                                        @foreach($formateur->metiers as $metier)
                                            <span class="dark badge-lg mr-2 mb-2">
                                                {{ $metier->nom }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Aucun métier assigné</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Informations supplémentaires</h5>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="20%">Créé le:</th>
                                        <td>{{ $formateur->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dernière modification:</th>
                                        <td>{{ $formateur->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <form action="{{ route('administrationetablissement.formateurs.destroy', $formateur) }}" 
                          method="POST" 
                          style="display: inline;"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection