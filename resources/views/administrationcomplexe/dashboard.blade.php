@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>Bienvenue dans l’espace Directeur Complexe</h1>
        <p class="lead">Ici, vous pouvez gérer les établissements, les formations, et les ressources.</p>
        <a href="{{route('administrationcomplexe.directeurs.index')}}">gestion directeurs</a>
        <a href="{{route('administrationcomplexe.etablissements.index')}}">gestion établissements</a>
    </div>
@endsection