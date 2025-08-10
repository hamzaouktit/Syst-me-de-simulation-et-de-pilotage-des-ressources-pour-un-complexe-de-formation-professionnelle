@extends('layouts.app')

@section('title', 'Gestion des Modules')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-primary">Gestion des Modules</h1>
        <a href="{{ route('administrationetablissement.modules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Ajouter un Module
        </a>
    </div>

    {{-- Alert succès --}}
   

    @if($modules->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nom du Module</th>
                        <th>Masse Horaire</th>
                        <th>Formation</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                        <tr>
                            <td>{{ $module->id }}</td>
                            <td>{{ $module->nom }}</td>
                            <td>{{ $module->masse_horaire }}h</td>
                            <td>{{ $module->formation ? $module->formation->titre : 'Non assigné' }}</td>
                            <td class="text-center">
                                <a href="{{ route('administrationetablissement.modules.show', $module) }}" 
                                   class="btn btn-sm btn-info text-white me-1" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('administrationetablissement.modules.edit', $module) }}" 
                                   class="btn btn-sm btn-warning me-1" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('administrationetablissement.modules.destroy', $module) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5 text-secondary">
            <p class="fs-5 mb-4">Aucun module trouvé.</p>
            <a href="{{ route('administrationetablissement.modules.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Créer le premier module
            </a>
        </div>
    @endif
</div>
@endsection
