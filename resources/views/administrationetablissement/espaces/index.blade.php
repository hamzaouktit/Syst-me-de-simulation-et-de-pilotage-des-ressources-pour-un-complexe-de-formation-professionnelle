@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Espaces Pédagogiques</h3>
                    <a href="{{ route('administrationetablissement.espaces.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvel Espace
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationetablissement.espaces.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Rechercher nom ou type..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="type" class="form-control">
                                            <option value="">Tous les types</option>
                                            @foreach($typesEspaces as $type)
                                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="capacite_min" class="form-control" 
                                               placeholder="Capacité min" value="{{ request('capacite_min') }}" min="1">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="capacite_max" class="form-control" 
                                               placeholder="Capacité max" value="{{ request('capacite_max') }}" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-search"></i> Filtrer
                                            </button>
                                            <a href="{{ route('administrationetablissement.espaces.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <input type="number" name="couverture_min" class="form-control" 
                                               placeholder="Couverture horaire min" value="{{ request('couverture_min') }}" min="1" max="60">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="couverture_max" class="form-control" 
                                               placeholder="Couverture horaire max" value="{{ request('couverture_max') }}" min="1" max="60">
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
                                    <th>Type</th>
                                    <th>Capacité</th>
                                    <th>Couverture Horaire Max</th>
                                    <th>Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($espaces as $espace)
                                    <tr>
                                        <td>{{ $espace->id }}</td>
                                        <td>{{ $espace->nom }}</td>
                                        <td>
                                            <span class="badge 
                                                @switch(strtolower($espace->type))
                                                    @case('amphitheatre') bg-primary @break
                                                    @case('salle_cours') bg-success @break
                                                    @case('laboratoire') bg-warning @break
                                                    @case('atelier') bg-info @break
                                                    @case('bibliotheque') bg-secondary @break
                                                    @default bg-dark
                                                @endswitch">
                                                {{ ucfirst($espace->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($espace->capacite <= 20) bg-danger
                                                @elseif($espace->capacite <= 50) bg-warning
                                                @else bg-success
                                                @endif">
                                                {{ $espace->capacite }} places
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($espace->couvertureHoraireMax <= 20) bg-danger
                                                @elseif($espace->couvertureHoraireMax <= 40) bg-warning
                                                @else bg-success
                                                @endif">
                                                {{ $espace->couvertureHoraireMax }}h
                                            </span>
                                        </td>
                                        <td>{{ $espace->created_at ? $espace->created_at->format('d/m/Y') : 'Non défini' }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.espaces.show', $espace) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('administrationetablissement.espaces.edit', $espace) }}" 
                                                   class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('administrationetablissement.espaces.destroy', $espace) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet espace pédagogique ?')">
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
                                            <i class="fas fa-building fa-3x mb-3"></i>
                                            <p>Aucun espace pédagogique trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $espaces->appends(request()->query())->links() }}
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

    /* Badges colorés pour la capacité et couverture horaire */
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
    }
</style>
@endsection