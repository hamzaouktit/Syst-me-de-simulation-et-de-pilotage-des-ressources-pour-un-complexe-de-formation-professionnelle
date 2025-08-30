@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title">Gestion des Formations</h3>
                        <small class="text-muted">
                            Établissement: {{ $etablissement->nom }}
                            (ID: {{ $etablissement->id }})
                        </small>
                    </div>
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
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Rechercher par titre ou niveau..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <select name="type" class="form-control">
                                            <option value="">Tous les types</option>
                                            <option value="initiale" {{ request('type') == 'initiale' ? 'selected' : '' }}>Formation Initiale</option>
                                            <option value="continue" {{ request('type') == 'continue' ? 'selected' : '' }}>Formation Continue</option>
                                            <option value="alternance" {{ request('type') == 'alternance' ? 'selected' : '' }}>Alternance</option>
                                            <option value="distance" {{ request('type') == 'distance' ? 'selected' : '' }}>Formation à Distance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
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

                    <!-- Statistiques -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>{{ $formations->total() }}</strong> formation(s) dans votre établissement
                                @if(request()->hasAny(['search', 'type']))
                                    <span class="text-muted">(filtré)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Niveau</th>
                                    <th>Type</th>
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
                                                    @case('alternance') bg-warning text-dark @break
                                                    @case('distance') bg-info @break
                                                    @default bg-secondary
                                                @endswitch">
                                                {{ ucfirst($formation->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $formation->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.formations.show', $formation) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir le détail">
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
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer la formation {{ $formation->titre }} ?\n\nCette action est irréversible et supprimera aussi tous les groupes et modules associés.')">
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
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                            <h5>Aucune formation trouvée</h5>
                                            @if(request()->hasAny(['search', 'type']))
                                                <p>Aucune formation ne correspond aux critères de recherche.</p>
                                                <a href="{{ route('administrationetablissement.formations.index') }}" class="btn btn-secondary">
                                                    <i class="fas fa-refresh"></i> Voir toutes les formations
                                                </a>
                                            @else
                                                <p>Aucune formation n'est encore enregistrée dans votre établissement.</p>
                                                <a href="{{ route('administrationetablissement.formations.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Créer la première formation
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Affichage de {{ $formations->firstItem() ?? 0 }} à {{ $formations->lastItem() ?? 0 }} 
                            sur {{ $formations->total() }} formation(s)
                        </div>
                        <div>
                            {{ $formations->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

    .badge {
        font-size: 0.75em;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit sur changement du select type
    const autoSubmitSelects = document.querySelectorAll('select[name="type"]');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Raccourci clavier pour la recherche
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });

    // Debug info
    console.log('Établissement ID:', {{ $etablissement->id }});
    console.log('Établissement nom:', '{{ $etablissement->nom }}');
    console.log('Nombre de formations:', {{ $formations->total() }});
});
</script>
@endpush
@endsection