@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Établissements</h3>
                    @if(auth()->user()->role === 'directeur_complexe')
                        <a href="{{ route('administrationcomplexe.etablissements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel Établissement
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationcomplexe.etablissements.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Rechercher..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="directeur" class="form-control">
                                            <option value="">Tous les directeurs</option>
                                            <option value="with_directeur" {{ request('directeur') == 'with_directeur' ? 'selected' : '' }}>Avec directeur</option>
                                            <option value="without_directeur" {{ request('directeur') == 'without_directeur' ? 'selected' : '' }}>Sans directeur</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="sort" class="form-control">
                                            <option value="">Trier par...</option>
                                            <option value="nom_asc" {{ request('sort') == 'nom_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                                            <option value="nom_desc" {{ request('sort') == 'nom_desc' ? 'selected' : '' }}>Nom (Z-A)</option>
                                            <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Plus ancien</option>
                                            <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Plus récent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('administrationcomplexe.etablissements.index') }}" class="btn btn-secondary">
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
                                    <th>Adresse</th>
                                    <th>Directeur</th>
                                    <th>Complexe</th>
                                    <th>Coordonnées</th>
                                    <th>Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($etablissements as $etablissement)
                                    <tr>
                                        <td>{{ $etablissement->id }}</td>
                                        <td>
                                            <strong class="text-primary">{{ $etablissement->nom }}</strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ $etablissement->adresse }}
                                        </td>
                                        <td>
                                            @if($etablissement->directeur)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-success me-2"></i>
                                                    <div>
                                                        <span class="fw-bold">{{ $etablissement->directeur->nom }}</span><br>
                                                        <small class="text-muted">{{ $etablissement->directeur->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-user-slash me-1"></i>
                                                    Sans directeur
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $etablissement->complexe->nom }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-globe me-1"></i>
                                                Long: {{ number_format($etablissement->longitude, 4) }}<br>
                                                <i class="fas fa-mountain me-1"></i>
                                                Alt: {{ number_format($etablissement->altitude, 0) }}m
                                            </small>
                                        </td>
                                        <td>
                                            {{ $etablissement->created_at ? $etablissement->created_at->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(auth()->user()->role === 'directeur_complexe')
                                                    <!-- Bouton Modifier -->
                                                    <a href="{{ route('administrationcomplexe.etablissements.edit', $etablissement) }}" 
                                                       class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Bouton Supprimer -->
                                                    <form action="{{ route('administrationcomplexe.etablissements.destroy', $etablissement) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet établissement ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger rounded-circle action-btn" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-school fa-3x mb-3"></i>
                                            <p>
                                                @if(auth()->user()->role === 'directeur_complexe')
                                                    Aucun établissement trouvé. Créez votre premier établissement.
                                                @else
                                                    Vous n'êtes directeur d'aucun établissement pour le moment.
                                                @endif
                                            </p>
                                            @if(auth()->user()->role === 'directeur_complexe')
                                                <a href="{{ route('administrationcomplexe.etablissements.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Créer un établissement
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($etablissements, 'links'))
                        <div class="d-flex justify-content-center">
                            {{ $etablissements->appends(request()->query())->links() }}
                        </div>
                    @endif
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
    
    .fw-bold {
        font-weight: bold;
    }
</style>
@endsection
