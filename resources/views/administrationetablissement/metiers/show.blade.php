@extends('layouts.app')

@section('title', 'Détails du Métier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Détails du Métier</h3>
                    <div class="btn-group">
                        <a href="{{ route('administrationetablissement.metiers.edit', $metier->id) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('administrationetablissement.metiers.index') }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $metier->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nom</th>
                                    <td>{{ $metier->nom }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $metier->description ?? 'Aucune description' }}</td>
                                </tr>
                                <tr>
                                    <th>Date de création</th>
                                    <td>{{ $metier->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière modification</th>
                                    <td>{{ $metier->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Formateurs associés -->
                    @if($metier->formateurs->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Formateurs associés</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($metier->formateurs as $formateur)
                                                <tr>
                                                    <td>{{ $formateur->nom }}</td>
                                                    <td>{{ $formateur->email }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Modules associés -->
                    @if($metier->modules->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Modules associés</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Masse Horaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($metier->modules as $module)
                                                <tr>
                                                    <td>{{ $module->nom }}</td>
                                                    <td>{{ $module->masse_horaire }}h</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection