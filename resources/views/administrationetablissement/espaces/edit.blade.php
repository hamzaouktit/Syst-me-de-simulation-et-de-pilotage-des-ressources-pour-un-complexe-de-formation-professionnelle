@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier l'Espace : {{ $espace->nom }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('administrationetablissement.espaces.update', $espace->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nom :</label>
            <input type="text" name="nom" class="form-control" value="{{ old('nom', $espace->nom) }}">
        </div>

        <div class="mb-3">
            <label>Type :</label>
            <input type="text" name="type" class="form-control" value="{{ old('type', $espace->type) }}">
        </div>

        <div class="mb-3">
            <label>Capacité :</label>
            <input type="number" name="capacite" class="form-control" value="{{ old('capacite', $espace->capacite) }}">
        </div>

        <div class="mb-3">
            <label>Couverture Horaire Max :</label>
            <input type="number" name="couvertureHoraireMax" class="form-control" value="{{ old('couvertureHoraireMax', $espace->couvertureHoraireMax) }}">
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('administrationetablissement.espaces.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
