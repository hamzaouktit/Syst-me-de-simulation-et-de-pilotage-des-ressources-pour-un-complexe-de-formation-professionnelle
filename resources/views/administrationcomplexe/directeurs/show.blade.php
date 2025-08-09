@extends('layouts.app')

@section('title', 'Détails Directeur')

@section('content')
<h1>Détails Directeur</h1>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Nom :</strong> {{ $directeur->nom }}</li>
    <li class="list-group-item"><strong>Email :</strong> {{ $directeur->email }}</li>
    <li class="list-group-item"><strong>Rôle :</strong> {{ $directeur->role }}</li>
</ul>

<a href="{{ route('administrationcomplexe.directeurs.index') }}" class="btn btn-secondary">Retour</a>
@endsection
