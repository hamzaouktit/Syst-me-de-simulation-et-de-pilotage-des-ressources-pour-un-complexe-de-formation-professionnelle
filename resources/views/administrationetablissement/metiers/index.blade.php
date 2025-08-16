@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Métiers</h3>
                    <a href="{{ route('administrationetablissement.metiers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Métier
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationetablissement.metiers.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search_nom" class="form-control" 
                                               placeholder="Rechercher par nom..." value="{{ request('search_nom') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="search_description" class="form-control" 
                                               placeholder="Rechercher dans la description..." value="{{ request('search_description') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="sort_by" class="form-control">
                                            <option value="">Trier par</option>
                                            <option value="nom" {{ request('sort_by', 'nom') == 'nom' ? 'selected' : '' }}>Nom</option>
                                            <option value="description" {{ request('sort_by') == 'description' ? 'selected' : '' }}>Description</option>
                                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date création</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('administrationetablissement.metiers.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <select name="sort_direction" class="form-control">
                                            <option value="asc" {{ request('sort_direction', 'asc') == 'asc' ? 'selected' : '' }}>Ordre croissant</option>
                                            <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Ordre décroissant</option>
                                        </select>
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
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nom', 'sort_direction' => request('sort_direction', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-white">
                                            Nom 
                                            @if(request('sort_by', 'nom') == 'nom')
                                                <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'description', 'sort_direction' => request('sort_direction', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-white">
                                            Description
                                            @if(request('sort_by') == 'description')
                                                <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => request('sort_direction', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-white">
                                            Date de création
                                            @if(request('sort_by') == 'created_at')
                                                <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metiers as $metier)
                                    <tr>
                                        <td>{{ $metier->id }}</td>
                                        <td>{{ $metier->nom }}</td>
                                        <td>
                                            @if($metier->description)
                                                {{ Str::limit($metier->description, 80) }}
                                            @else
                                                <span class="text-muted">Aucune description</span>
                                            @endif
                                        </td>
                                        <td>{{ $metier->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.metiers.show', $metier) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('administrationetablissement.metiers.edit', $metier) }}" 
                                                   class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('administrationetablissement.metiers.destroy', $metier) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce métier ?')">
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
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-briefcase fa-3x mb-3"></i>
                                            <p>Aucun métier trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $metiers->appends(request()->query())->links() }}
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