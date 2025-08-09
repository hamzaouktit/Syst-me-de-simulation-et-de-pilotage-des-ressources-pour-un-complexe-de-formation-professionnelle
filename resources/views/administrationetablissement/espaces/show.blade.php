@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails de l'Espace</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Nom :</strong> {{ $espace->nom }}</li>
        <li class="list-group-item"><strong>Type :</strong> {{ $espace->type }}</li>
        <li class="list-group-item"><strong>Capacité :</strong> {{ $espace->capacite }}</li>
        <li class="list-group-item"><strong>Couverture Horaire Max :</strong> {{ $espace->couvertureHoraireMax }}</li>
        <li class="list-group-item"><strong>Établissement :</strong> {{ $espace->etablissement->nom ?? 'N/A' }}</li>
    </ul>

    <a href="{{ route('administrationetablissement.espaces.index') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection
