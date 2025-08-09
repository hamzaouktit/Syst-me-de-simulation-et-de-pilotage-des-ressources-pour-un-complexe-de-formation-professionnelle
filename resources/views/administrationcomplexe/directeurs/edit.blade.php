@extends('layouts.app')

@section('title', 'Modifier Directeur')

@section('content')
<h1>Modifier Directeur</h1>

<form action="{{ route('administrationcomplexe.directeurs.update', $directeur->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="{{ $directeur->nom }}" required>
        @error('nom') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ $directeur->email }}" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Nouveau mot de passe (laisser vide si inchangé)</label>
        <input type="password" name="password" class="form-control">
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-warning">Mettre à jour</button>
    <a href="{{ route('administrationcomplexe.directeurs.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
