@extends('layouts.app')

@section('title', 'Liste des Directeurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Directeurs d’établissement</h1>
    <a href="{{ route('administrationcomplexe.directeurs.create') }}" class="btn btn-primary">+ Ajouter</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($directeurs as $directeur)
        <tr>
            <td>{{ $directeur->nom }}</td>
            <td>{{ $directeur->email }}</td>
            <td>
                <a href="{{ route('administrationcomplexe.directeurs.show', $directeur->id) }}" class="btn btn-info btn-sm">Voir</a>
                <a href="{{ route('administrationcomplexe.directeurs.edit', $directeur->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                <form action="{{ route('administrationcomplexe.directeurs.destroy', $directeur->id) }}" method="POST" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce directeur ?')">Supprimer</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center">Aucun directeur trouvé.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
