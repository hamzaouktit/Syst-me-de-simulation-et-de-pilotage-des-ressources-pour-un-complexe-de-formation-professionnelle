@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des Espaces Pédagogiques</h1>
    <a href="{{ route('administrationetablissement.espaces.create') }}" class="btn btn-primary mb-3">+ Ajouter un espace</a>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Capacité</th>
                <th>Couverture Horaire Max</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($espaces as $espace)
                <tr>
                    <td>{{ $espace->nom }}</td>
                    <td>{{ $espace->type }}</td>
                    <td>{{ $espace->capacite }}</td>
                    <td>{{ $espace->couvertureHoraireMax }}</td>
                    <td>
                        <a href="{{ route('administrationetablissement.espaces.show', $espace->id) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('administrationetablissement.espaces.edit', $espace->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('administrationetablissement.espaces.destroy', $espace->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Supprimer cet espace ?')" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Aucun espace pédagogique trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
