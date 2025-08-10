@extends('layouts.app')

@section('title', 'Détails du Module')

@section('content')
<div class="container">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-outline-secondary" title="Retour à la liste">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 fw-bold mb-0">Détails du Module</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ $module->nom }}</h2>
            <div class="btn-group" role="group" aria-label="Actions">
                
                <a href="{{ route('administrationetablissement.modules.edit', $module) }}" 
                   class="btn btn-warning text-white rounded-circle action-btn" 
                   title="Modifier">
                    <i class="fas fa-edit"></i>
                </a>


                <form action="{{ route('administrationetablissement.modules.destroy', $module) }}" 
                      method="POST" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?')" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-circle action-btn" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <h5>ID du Module</h5>
                    <p class="mb-0">{{ $module->id }}</p>
                </div>

                <div class="col-md-6">
                    <h5>Masse Horaire</h5>
                    <p class="mb-0">{{ $module->masse_horaire }}h</p>
                </div>

                <div class="col-12">
                    <h5>Formation</h5>
                    @if($module->formation)
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-1 fw-semibold">{{ $module->formation->titre }}</p>
                            <p class="mb-1"><strong>Niveau :</strong> {{ $module->formation->niveau }}</p>
                            <p class="mb-0"><strong>Type :</strong> {{ $module->formation->type }}</p>
                        </div>
                    @else
                        <p class="text-muted">Aucune formation assignée</p>
                    @endif
                </div>

                <div class="col-12">
                    <h5>Dates</h5>
                    <p class="mb-1"><strong>Créé le :</strong> {{ $module->created_at ? $module->created_at->format('d/m/Y à H:i') : 'N/A' }}</p>
                    <p class="mb-0"><strong>Modifié le :</strong> {{ $module->updated_at ? $module->updated_at->format('d/m/Y à H:i') : 'N/A' }}</p>
                </div>
            </div>

            <hr>

            <h5>Statistiques</h5>
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="fs-3 fw-bold text-primary">{{ $module->masse_horaire }}</div>
                    <div>Heures totales</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fs-3 fw-bold text-primary">1</div>
                    <div>Formation associée</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fs-3 fw-bold text-primary">ID: {{ $module->id }}</div>
                    <div>Identifiant unique</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style personnalisé pour les boutons d'action */
    .action-btn {
        width: 38px;
        height: 38px;
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
        font-size: 1rem;
    }
</style>
@endsection