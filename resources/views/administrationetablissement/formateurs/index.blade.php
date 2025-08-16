@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Formateurs</h3>
                    <a href="{{ route('administrationetablissement.formateurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Formateur
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('administrationetablissement.formateurs.index') }}">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" name="search_nom" class="form-control" 
                                               placeholder="Rechercher par nom..." value="{{ request('search_nom') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="email" name="search_email" class="form-control" 
                                               placeholder="Rechercher par email..." value="{{ request('search_email') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="search_metier" class="form-control">
                                            <option value="">Tous les métiers</option>
                                            @foreach($metiers as $metier)
                                                <option value="{{ $metier->id }}" 
                                                        {{ request('search_metier') == $metier->id ? 'selected' : '' }}>
                                                    {{ $metier->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="search_masse_horaire_min" class="form-control" 
                                               placeholder="Masse h. min" value="{{ request('search_masse_horaire_min') }}" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="search_masse_horaire_max" class="form-control" 
                                               placeholder="Masse h. max" value="{{ request('search_masse_horaire_max') }}" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('administrationetablissement.formateurs.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <select name="sort_by" class="form-control">
                                            <option value="">Trier par</option>
                                            <option value="nom" {{ request('sort_by', 'nom') == 'nom' ? 'selected' : '' }}>Nom</option>
                                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                            <option value="masse_horaire_disponible" {{ request('sort_by') == 'masse_horaire_disponible' ? 'selected' : '' }}>Masse horaire</option>
                                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date création</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
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
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_direction' => request('sort_direction', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-white">
                                            Email
                                            @if(request('sort_by') == 'email')
                                                <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'masse_horaire_disponible', 'sort_direction' => request('sort_direction', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-white">
                                            Masse Horaire
                                            @if(request('sort_by') == 'masse_horaire_disponible')
                                                <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Métiers</th>
                                    <th>Établissement</th>
                                    <th>Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formateurs as $formateur)
                                    <tr>
                                        <td>{{ $formateur->id }}</td>
                                        <td>{{ $formateur->nom }}</td>
                                        <td>
                                            <a href="mailto:{{ $formateur->email }}" class="text-decoration-none">
                                                {{ $formateur->email }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $formateur->masse_horaire_disponible }}h</span>
                                        </td>
                                        <td>
                                            @forelse($formateur->metiers as $metier)
                                                <span class="badge bg-secondary me-1">{{ $metier->nom }}</span>
                                            @empty
                                                <span class="text-muted small">Aucun métier</span>
                                            @endforelse
                                        </td>
                                        <td>{{ $formateur->etablissement->nom }}</td>
                                        <td>{{ $formateur->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Voir -->
                                                <a href="{{ route('administrationetablissement.formateurs.show', $formateur) }}" 
                                                   class="btn btn-sm btn-info rounded-circle action-btn" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('administrationetablissement.formateurs.edit', $formateur) }}" 
                                                   class="btn btn-sm btn-warning rounded-circle action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('administrationetablissement.formateurs.destroy', $formateur) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?')">
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
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                            <p>Aucun formateur trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $formateurs->appends(request()->query())->links() }}
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit sur changement des selects
    const autoSubmitSelects = document.querySelectorAll('select[name="search_metier"]');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Raccourci clavier pour la recherche
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            document.getElementById('search_nom').focus();
        }
    });
});
</script>
@endpush
@endsection