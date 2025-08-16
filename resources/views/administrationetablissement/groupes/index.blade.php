@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Groupes</h3>
                    <a href="{{ route('administrationetablissement.groupes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Groupe
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationetablissement.groupes.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Rechercher par nom..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="formation_id" class="form-control">
                                            <option value="">Toutes les formations</option>
                                            @foreach($formations as $formation)
                                                <option value="{{ $formation->id }}" 
                                                        {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                                                    {{ $formation->titre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="annee_id" class="form-control">
                                            <option value="">Toutes les années</option>
                                            @foreach($annees as $annee)
                                                <option value="{{ $annee->id }}" 
                                                        {{ request('annee_id') == $annee->id ? 'selected' : '' }}>
                                                    {{ $annee->annee }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('administrationetablissement.groupes.index') }}" class="btn btn-secondary">
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
                                    <th>Nom</th>
                                    <th>Effectif</th>
                                    <th>Formation</th>
                                    <th>Année</th>
                                    <th>Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupes as $groupe)
                                    <tr>
                                        <td>{{ $groupe->id }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $groupe->nom }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $groupe->effectif }}</span>
                                        </td>
                                        <td>{{ $groupe->formation->titre }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $groupe->anneeDeFormation->annee }}</span>
                                        </td>
                                        <td>{{ $groupe->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.groupes.show', $groupe) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('administrationetablissement.groupes.edit', $groupe) }}" 
                                                   class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('administrationetablissement.groupes.destroy', $groupe) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')">
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
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p>Aucun groupe trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $groupes->appends(request()->query())->links() }}
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