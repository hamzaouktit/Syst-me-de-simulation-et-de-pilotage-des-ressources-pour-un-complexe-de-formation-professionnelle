@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="row justify-content-center min-vh-75 align-items-center">
    <div class="col-lg-5 col-md-7 col-sm-9">
        {{-- Card de connexion --}}
        <div class="login-card">
            {{-- Header de la card --}}
            <div class="login-header">
                <div class="login-logo">
                    <div class="ofppt-logo-auth">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="login-title">Système de Pilotage</h3>
                    <p class="login-subtitle">Office de la Formation Professionnelle et de la Promotion du Travail</p>
                </div>
            </div>

            {{-- Corps de la card --}}
            <div class="login-body">
                <h4 class="form-title">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Connexion à votre compte
                </h4>
                
                <p class="form-subtitle">Veuillez saisir vos identifiants pour accéder au système</p>

                {{-- Messages d'erreur --}}
                @if ($errors->any())
                    <div class="alert alert-danger custom-alert" role="alert">
                        <div class="alert-content">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <div>
                                <strong>Erreur de connexion !</strong><br>
                                {{ $errors->first() }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Formulaire de connexion --}}
                <form method="POST" action="{{ route('login.submit') }}" class="login-form">
                    @csrf
                    
                    {{-- Champ Email --}}
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>
                            Adresse e-mail
                        </label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   id="email"
                                   name="email" 
                                   class="form-control custom-input @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}"
                                   placeholder="exemple@ofppt.ma"
                                   required 
                                   autocomplete="email"
                                   autofocus>
                            <i class="input-icon fas fa-envelope"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Champ Mot de passe --}}
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>
                            Mot de passe
                        </label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password"
                                   name="password" 
                                   class="form-control custom-input @error('password') is-invalid @enderror" 
                                   placeholder="Saisissez votre mot de passe"
                                   required 
                                   autocomplete="current-password">
                            <i class="input-icon fas fa-lock"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Options supplémentaires --}}
                    <div class="form-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="#" class="forgot-password" onclick="return false;">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    {{-- Bouton de connexion --}}
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span class="btn-text">Se connecter</span>
                        <div class="btn-loader d-none">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </form>
            </div>

            {{-- Footer de la card --}}
            <div class="login-footer">
                <div class="footer-links">
                    <a href="https://www.ofppt.ma" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Site officiel OFPPT
                    </a>
                    <span class="divider">|</span>
                    <a href="#" onclick="return false;">
                        <i class="fas fa-question-circle me-1"></i>
                        Aide
                    </a>
                </div>
                <p class="copyright">
                    &copy; {{ date('Y') }} OFPPT - Tous droits réservés
                </p>
            </div>
        </div>

        {{-- Informations supplémentaires --}}
        <div class="info-box mt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div class="info-item">
                        <i class="fas fa-shield-alt info-icon"></i>
                        <p class="info-text">Sécurisé</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-item">
                        <i class="fas fa-clock info-icon"></i>
                        <p class="info-text">24h/24</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-item">
                        <i class="fas fa-headset info-icon"></i>
                        <p class="info-text">Support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Variables CSS pour la cohérence */
:root {
    --ofppt-green: #2E8B57;
    --ofppt-blue: #1E5F99;
    --ofppt-gray: #6B7280;
    --ofppt-light-gray: #F8F9FA;
    --ofppt-dark-blue: #1a4a75;
}

/* Container principal */
.min-vh-75 {
    min-height: 75vh;
}

/* Card de connexion */
.login-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
}

/* Header de la card */
.login-header {
    background: linear-gradient(135deg, var(--ofppt-blue) 0%, var(--ofppt-dark-blue) 100%);
    padding: 40px 30px 30px;
    text-align: center;
    color: white;
}

.login-logo {
    margin-bottom: 0;
}

.ofppt-logo-auth {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, var(--ofppt-green) 0%, var(--ofppt-gray) 50%, white 100%);
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--ofppt-blue);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: logoFloat 3s ease-in-out infinite;
}

@keyframes logoFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.login-title {
    margin: 0 0 10px;
    font-size: 1.8rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.login-subtitle {
    margin: 0;
    font-size: 0.95rem;
    opacity: 0.9;
    line-height: 1.4;
}

/* Corps de la card */
.login-body {
    padding: 40px 30px;
}

.form-title {
    color: var(--ofppt-blue);
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 8px;
    text-align: center;
}

.form-subtitle {
    color: var(--ofppt-gray);
    text-align: center;
    margin-bottom: 30px;
    font-size: 0.95rem;
}

/* Groupes de formulaire */
.form-group {
    margin-bottom: 25px;
}

.form-label {
    color: var(--ofppt-blue);
    font-weight: 600;
    margin-bottom: 10px;
    display: block;
    font-size: 0.95rem;
}

/* Wrapper des inputs */
.input-wrapper {
    position: relative;
}

.custom-input {
    background-color: #f8f9fc;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    padding: 15px 50px 15px 45px;
    font-size: 1rem;
    transition: all 0.3s ease;
    height: auto;
}

.custom-input:focus {
    background-color: white;
    border-color: var(--ofppt-green);
    box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.1);
    transform: translateY(-1px);
}

.custom-input.is-invalid {
    border-color: #dc3545;
    background-color: #fdf2f2;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ofppt-gray);
    transition: color 0.3s ease;
}

.custom-input:focus + .input-icon {
    color: var(--ofppt-green);
}

/* Bouton toggle mot de passe */
.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--ofppt-gray);
    cursor: pointer;
    padding: 5px;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--ofppt-blue);
}

/* Options du formulaire */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    font-size: 0.9rem;
}

.form-check-input:checked {
    background-color: var(--ofppt-green);
    border-color: var(--ofppt-green);
}

.forgot-password {
    color: var(--ofppt-blue);
    text-decoration: none;
    font-weight: 500;
}

.forgot-password:hover {
    color: var(--ofppt-green);
    text-decoration: underline;
}

/* Bouton de connexion */
.btn-login {
    width: 100%;
    background: linear-gradient(135deg, var(--ofppt-green) 0%, #228B22 100%);
    border: none;
    border-radius: 12px;
    padding: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-login:hover {
    background: linear-gradient(135deg, #228B22 0%, var(--ofppt-green) 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(46, 139, 87, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

.btn-login.loading .btn-text {
    opacity: 0;
}

.btn-login.loading .btn-loader {
    display: inline-block !important;
}

/* Alertes personnalisées */
.custom-alert {
    border-radius: 12px;
    border: none;
    margin-bottom: 25px;
    padding: 15px;
}

.alert-content {
    display: flex;
    align-items: flex-start;
}

.alert-icon {
    font-size: 1.2rem;
    margin-right: 12px;
    margin-top: 2px;
}

/* Footer de la card */
.login-footer {
    background-color: #f8f9fc;
    padding: 25px 30px;
    text-align: center;
    border-top: 1px solid #e3e6f0;
}

.footer-links {
    margin-bottom: 15px;
}

.footer-links a {
    color: var(--ofppt-blue);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.footer-links a:hover {
    color: var(--ofppt-green);
}

.divider {
    margin: 0 15px;
    color: var(--ofppt-gray);
}

.copyright {
    margin: 0;
    color: var(--ofppt-gray);
    font-size: 0.85rem;
}

/* Boîte d'informations */
.info-box {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.info-item {
    padding: 10px;
}

.info-icon {
    font-size: 1.5rem;
    color: var(--ofppt-green);
    margin-bottom: 8px;
}

.info-text {
    margin: 0;
    font-size: 0.85rem;
    color: var(--ofppt-gray);
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .login-header {
        padding: 30px 20px 20px;
    }
    
    .login-body {
        padding: 30px 20px;
    }
    
    .login-footer {
        padding: 20px;
    }
    
    .ofppt-logo-auth {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .login-title {
        font-size: 1.5rem;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .info-box {
        margin: 0 15px;
    }
}

/* Animations d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-card {
    animation: fadeInUp 0.6s ease-out;
}

.info-box {
    animation: fadeInUp 0.6s ease-out 0.2s both;
}
</style>

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

// Effet de loading sur le bouton de connexion
document.querySelector('.login-form').addEventListener('submit', function() {
    const btn = document.querySelector('.btn-login');
    btn.classList.add('loading');
    btn.disabled = true;
});

// Animation des inputs au focus
document.querySelectorAll('.custom-input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});
</script>
@endsection