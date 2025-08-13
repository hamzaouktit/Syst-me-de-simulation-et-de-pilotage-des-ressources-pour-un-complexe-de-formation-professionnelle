{{-- resources/views/auth/verifycode.blade.php --}}
@extends('layouts.app')

@section('title', 'Vérifier le code')

@section('content')
<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow-sm">
        <div class="card-header text-center" style="background: linear-gradient(90deg, #004d40, #00796b); color: white;">
            <h3>Réinitialisation du mot de passe</h3>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('forgot.password.verify') }}">
                @csrf

                {{-- Code reçu --}}
                <div class="form-group mb-3">
                    <label for="code">Code reçu</label>
                    <input type="text" name="code" id="code" class="form-control" required placeholder="Entrez le code reçu">
                </div>

                {{-- Nouveau mot de passe --}}
                <div class="form-group mb-3 position-relative">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Entrez le nouveau mot de passe">
                    <button type="button" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" onclick="togglePassword()">
                        <i class="fas fa-eye" id="password-eye"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-success w-100">Changer le mot de passe</button>
            </form>
        </div>
    </div>
</div>

<script>
// Fonction pour basculer la visibilité du mot de passe
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordEye = document.getElementById('password-eye');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordEye.classList.remove('fa-eye');
        passwordEye.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordEye.classList.remove('fa-eye-slash');
        passwordEye.classList.add('fa-eye');
    }
}
</script>
@endsection
