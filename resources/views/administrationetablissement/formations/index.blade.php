@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Formations</h3>
                    <a href="{{ route('administrationetablissement.formations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Formation
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationetablissement.formations.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Rechercher..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="type" class="form-control">
                                            <option value="">Tous les types</option>
                                            <option value="initiale" {{ request('type') == 'initiale' ? 'selected' : '' }}>Formation Initiale</option>
                                            <option value="continue" {{ request('type') == 'continue' ? 'selected' : '' }}>Formation Continue</option>
                                            <option value="alternance" {{ request('type') == 'alternance' ? 'selected' : '' }}>Alternance</option>
                                            <option value="distance" {{ request('type') == 'distance' ? 'selected' : '' }}>Formation à Distance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="etablissement_id" class="form-control">
                                            <option value="">Tous les établissements</option>
                                            @foreach($etablissements as $etablissement)
                                                <option value="{{ $etablissement->id }}" 
                                                    {{ request('etablissement_id') == $etablissement->id ? 'selected' : '' }}>
                                                    {{ $etablissement->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('administrationetablissement.formations.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tableau -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Niveau</th>
                                    <th>Type</th>
                                    <th>Établissement</th>
                                    <th>Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formations as $formation)
                                    <tr>
                                        <td>{{ $formation->id }}</td>
                                        <td>{{ $formation->titre }}</td>
                                        <td>{{ $formation->niveau }}</td>
                                        <td>
                                            <span class="badge 
                                                @switch($formation->type)
                                                    @case('initiale') bg-primary @break
                                                    @case('continue') bg-success @break
                                                    @case('alternance') bg-warning @break
                                                    @case('distance') bg-info @break
                                                    @default bg-secondary
                                                @endswitch">
                                                {{ ucfirst($formation->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $formation->etablissement->nom }}</td>
                                        <td>{{ $formation->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.formations.show', $formation) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('administrationetablissement.formations.edit', $formation) }}" 
                                                   class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('administrationetablissement.formations.destroy', $formation) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-circle action-btn" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Aucune formation trouvée</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $formations->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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