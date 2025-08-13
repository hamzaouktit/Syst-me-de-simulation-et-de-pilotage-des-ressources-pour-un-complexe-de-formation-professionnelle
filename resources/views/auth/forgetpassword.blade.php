{{-- resources/views/auth/forgetpassword.blade.php --}}
@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Réinitialisation du mot de passe</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('forgot.password.send') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="Entrez votre e-mail">
        </div>
        <button type="submit" class="btn btn-primary">Envoyer le code</button>
    </form>
</div>
@endsection
