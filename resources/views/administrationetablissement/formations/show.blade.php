@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Détails de la Formation : {{ $formation->titre }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('administrationetablissement.formations.edit', $formation) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('administrationetablissement.formations.index') }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Informations de la Formation</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">ID :</dt>
                                                <dd class="col-sm-8">{{ $formation->id }}</dd>
                                                
                                                <dt class="col-sm-4">Titre :</dt>
                                                <dd class="col-sm-8"><strong>{{ $formation->titre }}</strong></dd>
                                                
                                                <dt class="col-sm-4">Niveau :</dt>
                                                <dd class="col-sm-8">
                                                    <span class="badge bg-info">{{ $formation->niveau }}</span>
                                                </dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Type :</dt>
                                                <dd class="col-sm-8">
                                                    <span class="badge 
                                                        @switch($formation->type)
                                                            @case('initiale') bg-primary @break
                                                            @case('continue') bg-success @break
                                                            @case('alternance') bg-warning @break
                                                            @case('distance') bg-info @break
                                                            @default bg-secondary
                                                        @endswitch">
                                                        @switch($formation->type)
                                                            @case('initiale')
                                                                Formation Initiale
                                                                @break
                                                            @case('continue')
                                                                Formation Continue
                                                                @break
                                                            @case('alternance')
                                                                Formation en Alternance
                                                                @break
                                                            @case('distance')
                                                                Formation à Distance
                                                                @break
                                                            @default
                                                                {{ ucfirst($formation->type) }}
                                                        @endswitch
                                                    </span>
                                                </dd>
                                                
                                                <dt class="col-sm-4">Nom complet :</dt>
                                                <dd class="col-sm-8"><em>{{ $formation->nom_complet }}</em></dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Établissement -->
                            <div class="card mt-3">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-building"></i> Établissement</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Nom :</dt>
                                                <dd class="col-sm-8">{{ $formation->etablissement->nom }}</dd>
                                                
                                                <dt class="col-sm-4">Adresse :</dt>
                                                <dd class="col-sm-8">{{ $formation->etablissement->adresse }}</dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Longitude :</dt>
                                                <dd class="col-sm-8">{{ $formation->etablissement->longitude ?? 'Non définie' }}</dd>
                                                
                                                <dt class="col-sm-4">Latitude :</dt>
                                                <dd class="col-sm-8">{{ $formation->etablissement->altitude ?? 'Non définie' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations secondaires -->
                        <div class="col-md-4">
                            <!-- Dates -->
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-calendar"></i> Dates importantes</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Créée le :</strong><br>
                                       {{ $formation->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Modifiée le :</strong><br>
                                       {{ $formation->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>

                            <!-- Actions rapides -->
                            <div class="card mt-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-tools"></i> Actions rapides</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('administrationetablissement.formations.edit', $formation) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Modifier la formation
                                        </a>
                                        <form action="{{ route('administrationetablissement.formations.destroy', $formation) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ? Cette action est irréversible.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="fas fa-trash"></i> Supprimer la formation
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques -->
                            <div class="card mt-3">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistiques</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ $formation->groupes->count() }}</h4>
                                                <small class="text-muted">Groupes</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">{{ $formation->modules->count() }}</h4>
                                            <small class="text-muted">Modules</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <h4 class="text-info">{{ $formation->anneesFormation->count() }}</h4>
                                        <small class="text-muted">Années de formation</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections détaillées -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <!-- Groupes -->
                            @if($formation->groupes->count() > 0)
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users"></i> Groupes associés ({{ $formation->groupes->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nom</th>
                                                        <th>Effectif</th>
                                                        <th>Année de formation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formation->groupes as $groupe)
                                                        <tr>
                                                            <td>{{ $groupe->id }}</td>
                                                            <td>{{ $groupe->nom }}</td>
                                                            <td>{{ $groupe->effectif }}</td>
                                                            <td>{{ $groupe->anneeFormation->annee ?? 'Non définie' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Modules -->
                            @if($formation->modules->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-book"></i> Modules associés ({{ $formation->modules->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nom</th>
                                                        <th>Masse horaire</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formation->modules as $module)
                                                        <tr>
                                                            <td>{{ $module->id }}</td>
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

                            <!-- Années de formation -->
                            @if($formation->anneesFormation->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calendar-alt"></i> Années de formation ({{ $formation->anneesFormation->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($formation->anneesFormation as $annee)
                                                <div class="col-md-4 mb-2">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <h6 class="card-title">{{ $annee->annee }}</h6>
                                                            <p class="card-text">ID: {{ $annee->id }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Message si aucune donnée associée -->
                            @if($formation->groupes->count() == 0 && $formation->modules->count() == 0 && $formation->anneesFormation->count() == 0)
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <h5>Aucune donnée associée</h5>
                                    <p>Cette formation n'a pas encore de groupes, modules ou années de formation associés.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Boutons d'action finaux -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('administrationetablissement.formations.index') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour à la liste
                                </a>
                                <div>
                                    <a href="{{ route('administrationetablissement.formations.edit', $formation) }}" 
                                       class="btn btn-warning me-2">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <button type="button" 
                                            class="btn btn-info" 
                                            onclick="window.print()">
                                        <i class="fas fa-print"></i> Imprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header .btn-group {
            display: none !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Fonction pour afficher les détails en modal si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'entrée pour les cartes
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });
        
        // Confirmation avant suppression
        const deleteForm = document.querySelector('form[method="POST"]');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous absolument sûr de vouloir supprimer cette formation ?\n\nCette action supprimera également tous les éléments associés (groupes, modules, etc.) et ne peut pas être annulée.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush