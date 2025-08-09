@extends('layouts.app')

@section('title', 'Créer Directeur')

@section('content')
<h1>Créer un Directeur</h1>

<form action="{{ route('administrationcomplexe.directeurs.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" required>
        @error('nom') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Mot de passe</label>
        <input type="password" name="password" class="form-control" required>
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-success">Enregistrer</button>
    <a href="{{ route('administrationcomplexe.directeurs.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
