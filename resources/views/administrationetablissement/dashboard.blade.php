@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>
        Bienvenue dans l’espace Directeur Établissement, Bonjour {{ Auth::user()->nom }}
    </h1>
    <p class="lead">Gérez facilement vos modules, espaces pédagogiques et formateurs.</p>

    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ route('administrationetablissement.espaces.index') }}" class="btn btn-primary btn-lg">
            📚 Gérer les Espaces Pédagogiques
        </a>
        <a href="{{ route('administrationetablissement.formations.index') }}" class="btn btn-info btn-lg">
            📖 Gérer les Formations
        </a>
        
        <a href="{{ route('administrationetablissement.anneesdeformations.index') }}" class="btn btn-info btn-lg">
            📅 Années des Formations
        </a>

        <a href="{{ route('administrationetablissement.groupes.index') }}" class="btn btn-info btn-lg">
            👥 Gérer les Groupes
        </a>

        
        <a href="{{ route('administrationetablissement.modules.index') }}" class="btn btn-secondary btn-lg">
            📘 Gérer les Modules
        </a>
        <a href="{{ route('administrationetablissement.formateurs.index') }}" class="btn btn-success btn-lg">
            👩‍🏫 Gérer les Formateurs
        </a>
        <a href="{{ route('administrationetablissement.metiers.index') }}" class="btn btn-success btn-lg">
            🛠️ Gérer les Métiers
        </a>
    </div>
</div>
@endsection
