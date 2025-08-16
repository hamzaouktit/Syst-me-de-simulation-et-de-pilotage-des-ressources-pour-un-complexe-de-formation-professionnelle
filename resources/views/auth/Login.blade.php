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
                        <a href="{{ route('forgot.password.form') }}" class="forgot-password">
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
                    <a href="#" onclick="openHelpModal(); return false;">
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

{{-- Modal d'aide --}}
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content help-modal-content">
            <div class="modal-header help-modal-header">
                <h5 class="modal-title help-modal-title" id="helpModalLabel">
                    <i class="fas fa-question-circle me-2"></i>
                    Centre d'aide - Connexion
                </h5>
                <button type="button" class="btn-close help-btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body help-modal-body">
                <div class="help-section">
                    <h6 class="help-section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Comment se connecter ?
                    </h6>
                    <div class="help-content">
                        <ol class="help-list">
                            <li>Saisissez votre adresse e-mail professionnelle OFPPT dans le champ "Adresse e-mail"</li>
                            <li>Entrez votre mot de passe dans le champ correspondant</li>
                            <li>Si vous souhaitez rester connecté, cochez "Se souvenir de moi"</li>
                            <li>Cliquez sur le bouton "Se connecter"</li>
                        </ol>
                    </div>
                </div>

                <div class="help-section">
                    <h6 class="help-section-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Problèmes de connexion
                    </h6>
                    <div class="help-content">
                        <div class="help-item">
                            <strong>Mot de passe oublié ?</strong>
                            <p>Cliquez sur le lien "Mot de passe oublié ?" sous les champs de connexion pour réinitialiser votre mot de passe.</p>
                        </div>
                        <div class="help-item">
                            <strong>Compte bloqué ?</strong>
                            <p>Après plusieurs tentatives de connexion échouées, votre compte peut être temporairement bloqué. Contactez l'administrateur système.</p>
                        </div>
                        <div class="help-item">
                            <strong>Problème technique ?</strong>
                            <p>Vérifiez votre connexion Internet et assurez-vous d'utiliser un navigateur compatible (Chrome, Firefox, Safari, Edge).</p>
                        </div>
                    </div>
                </div>

                <div class="help-section">
                    <h6 class="help-section-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Sécurité
                    </h6>
                    <div class="help-content">
                        <ul class="help-list">
                            <li>Ne partagez jamais vos identifiants avec d'autres personnes</li>
                            <li>Déconnectez-vous toujours après utilisation, surtout sur un ordinateur partagé</li>
                            <li>Utilisez un mot de passe fort contenant au moins 8 caractères</li>
                            <li>Changez régulièrement votre mot de passe</li>
                        </ul>
                    </div>
                </div>

                <div class="help-section">
                    <h6 class="help-section-title">
                        <i class="fas fa-phone me-2"></i>
                        Besoin d'aide supplémentaire ?
                    </h6>
                    <div class="help-content">
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-envelope text-primary"></i>
                                <span>support@ofppt.ma</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone text-success"></i>
                                <span>0537-68-20-00</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock text-warning"></i>
                                <span>Lun-Ven : 8h00-17h00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer help-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Fermer
                </button>
                <button type="button" class="btn btn-primary" onclick="contactSupport()">
                    <i class="fas fa-headset me-1"></i>
                    Contacter le support
                </button>
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
    cursor: pointer;
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

/* Styles pour la modal d'aide */
.help-modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.help-modal-header {
    background: linear-gradient(135deg, var(--ofppt-blue) 0%, var(--ofppt-dark-blue) 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px 25px;
}

.help-modal-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.help-btn-close {
    filter: brightness(0) invert(1);
}

.help-modal-body {
    padding: 25px;
    max-height: 60vh;
    overflow-y: auto;
}

.help-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.help-section:last-child {
    margin-bottom: 0;
    border-bottom: none;
}

.help-section-title {
    color: var(--ofppt-blue);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.help-content {
    color: var(--ofppt-gray);
    line-height: 1.6;
}

.help-list {
    padding-left: 20px;
    margin-bottom: 0;
}

.help-list li {
    margin-bottom: 8px;
}

.help-item {
    margin-bottom: 15px;
}

.help-item:last-child {
    margin-bottom: 0;
}

.help-item strong {
    color: var(--ofppt-blue);
    display: block;
    margin-bottom: 5px;
}

.help-item p {
    margin: 0;
    font-size: 0.95rem;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
}

.contact-item i {
    width: 20px;
    text-align: center;
}

.help-modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 15px 25px;
}

.help-modal-footer .btn {
    padding: 8px 20px;
    border-radius: 8px;
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
    
    .help-modal-body {
        padding: 20px;
        max-height: 50vh;
    }
    
    .contact-info {
        gap: 15px;
    }
}

/* Styles pour la modal de contact support */
.contact-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.contact-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.contact-option:hover {
    border-color: var(--ofppt-green);
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.contact-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.3rem;
    color: white;
    background: var(--ofppt-blue);
}

.contact-icon.phone {
    background: #28a745;
}

.contact-icon.whatsapp {
    background: #25d366;
}

.contact-icon.ticket {
    background: #ffc107;
    color: #333;
}

.contact-details {
    flex: 1;
}

.contact-details h6 {
    margin: 0 0 5px 0;
    color: var(--ofppt-blue);
    font-weight: 600;
}

.contact-details p {
    margin: 0 0 3px 0;
    font-weight: 500;
    color: #333;
}

.contact-details small {
    color: var(--ofppt-gray);
    font-size: 0.8rem;
}

.contact-arrow {
    color: var(--ofppt-green);
    font-size: 1.2rem;
}

/* Styles pour les notifications */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    padding: 15px 40px 15px 15px;
    max-width: 350px;
    z-index: 9999;
    border-left: 4px solid #007bff;
    animation: slideIn 0.3s ease;
}

.notification-success {
    border-left-color: #28a745;
}

.notification-warning {
    border-left-color: #ffc107;
}

.notification-error {
    border-left-color: #dc3545;
}

.notification-info {
    border-left-color: #17a2b8;
}

.notification-content {
    color: #333;
    line-height: 1.4;
}

.notification-close {
    position: absolute;
    top: 5px;
    right: 10px;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-close:hover {
    color: #333;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive pour les modals de contact */
@media (max-width: 768px) {
    .contact-option {
        padding: 12px;
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
        margin-right: 12px;
    }
    
    .notification {
        right: 10px;
        left: 10px;
        max-width: none;
    }
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

// Fonction pour ouvrir la modal d'aide
function openHelpModal() {
    const helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
    helpModal.show();
}

// Fonction pour contacter le support
function contactSupport() {
    // Créer une modal de contact
    const contactModal = `
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, var(--ofppt-green) 0%, #228B22 100%); color: white;">
                        <h5 class="modal-title" id="contactModalLabel">
                            <i class="fas fa-headset me-2"></i>
                            Contacter le Support
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" style="filter: brightness(0) invert(1);"></button>
                    </div>
                    <div class="modal-body">
                        <div class="contact-options">
                            <div class="contact-option" onclick="openEmailSupport()">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h6>Email Support</h6>
                                    <p>support@ofppt.ma</p>
                                    <small>Réponse sous 24h</small>
                                </div>
                                <i class="fas fa-external-link-alt contact-arrow"></i>
                            </div>
                            
                            <div class="contact-option" onclick="callSupport()">
                                <div class="contact-icon phone">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-details">
                                    <h6>Assistance téléphonique</h6>
                                    <p>0537-68-20-00</p>
                                    <small>Lun-Ven : 8h00-17h00</small>
                                </div>
                                <i class="fas fa-phone-alt contact-arrow"></i>
                            </div>
                            
                            <div class="contact-option" onclick="openWhatsAppSupport()">
                                <div class="contact-icon whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div class="contact-details">
                                    <h6>WhatsApp Support</h6>
                                    <p>+212 6XX-XX-XX-XX</p>
                                    <small>Réponse rapide</small>
                                </div>
                                <i class="fab fa-whatsapp contact-arrow"></i>
                            </div>
                            
                            <div class="contact-option" onclick="openTicketForm()">
                                <div class="contact-icon ticket">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h6>Créer un ticket</h6>
                                    <p>Système de tickets</p>
                                    <small>Suivi personnalisé</small>
                                </div>
                                <i class="fas fa-arrow-right contact-arrow"></i>
                            </div>
                        </div>
                        
                        <div class="urgent-help mt-3">
                            <div class="alert" style="background: linear-gradient(45deg, #ff6b6b, #ffa500); color: white; border: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Urgent ?</strong> Appelez directement le 0537-68-20-00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au DOM si elle n'existe pas
    if (!document.getElementById('contactModal')) {
        document.body.insertAdjacentHTML('beforeend', contactModal);
    }
    
    // Fermer la modal d'aide et ouvrir celle de contact
    const helpModal = bootstrap.Modal.getInstance(document.getElementById('helpModal'));
    if (helpModal) {
        helpModal.hide();
    }
    
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('contactModal'));
        modal.show();
    }, 300);
}

// Fonction pour ouvrir l'email de support
function openEmailSupport() {
    const email = 'support@ofppt.ma';
    const subject = 'Demande d\'aide - Système de Pilotage OFPPT';
    const body = `Bonjour,

J'ai besoin d'aide concernant le système de pilotage OFPPT.

Détails de ma demande :
- Type de problème : [Connexion/Technique/Autre]
- Description : [Décrivez votre problème ici]
- Navigateur utilisé : ${navigator.userAgent.split(' ').slice(-1)[0]}
- Date et heure : ${new Date().toLocaleString('fr-FR')}

Merci de votre aide.

Cordialement,
[Votre nom]`;
    
    const mailtoLink = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.open(mailtoLink, '_blank');
}

// Fonction pour appeler le support
function callSupport() {
    const phoneNumber = '0537682000';
    
    // Vérifier si c'est un appareil mobile
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        window.location.href = `tel:${phoneNumber}`;
    } else {
        // Afficher une notification avec le numéro
        showNotification('Numéro de téléphone', `Appelez le : ${phoneNumber.replace(/(\d{4})(\d{2})(\d{2})(\d{2})/, '$1-$2-$3-$4')}`, 'info');
    }
}

// Fonction pour WhatsApp support
function openWhatsAppSupport() {
    // Remplacez par votre vrai numéro WhatsApp
    const whatsappNumber = '212XXXXXXXXX'; // Format international sans +
    const message = 'Bonjour, j\'ai besoin d\'aide pour le système de pilotage OFPPT.';
    
    const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
    window.open(whatsappLink, '_blank');
}

// Fonction pour créer un ticket
function openTicketForm() {
    // Vous pouvez rediriger vers votre système de tickets ou ouvrir un formulaire
    const ticketUrl = '/support/create-ticket'; // Remplacez par votre URL
    
    // Ou afficher un formulaire dans une modal
    showTicketForm();
}

// Fonction pour afficher le formulaire de ticket
function showTicketForm() {
    const ticketFormModal = `
        <div class="modal fade" id="ticketModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--ofppt-blue); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-ticket-alt me-2"></i>
                            Créer un ticket de support
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: brightness(0) invert(1);"></button>
                    </div>
                    <div class="modal-body">
                        <form id="ticketForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Priorité *</label>
                                    <select class="form-control" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="low">Basse</option>
                                        <option value="normal">Normale</option>
                                        <option value="high">Haute</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catégorie *</label>
                                <select class="form-control" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="login">Problème de connexion</option>
                                    <option value="technical">Problème technique</option>
                                    <option value="account">Gestion de compte</option>
                                    <option value="feature">Demande de fonctionnalité</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Objet *</label>
                                <input type="text" class="form-control" placeholder="Résumez votre problème en quelques mots" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description détaillée *</label>
                                <textarea class="form-control" rows="5" placeholder="Décrivez votre problème en détail..." required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Annuler
                        </button>
                        <button type="button" class="btn btn-primary" onclick="submitTicket()">
                            <i class="fas fa-paper-plane me-1"></i>
                            Envoyer le ticket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    if (!document.getElementById('ticketModal')) {
        document.body.insertAdjacentHTML('beforeend', ticketFormModal);
    }
    
    const contactModal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
    if (contactModal) {
        contactModal.hide();
    }
    
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
        modal.show();
    }, 300);
}

// Fonction pour soumettre le ticket
function submitTicket() {
    // Ici vous pouvez traiter la soumission du ticket
    // Exemple : envoyer les données à votre backend
    
    showNotification('Ticket créé', 'Votre ticket a été créé avec succès. Vous recevrez une confirmation par email.', 'success');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('ticketModal'));
    modal.hide();
}

// Fonction pour afficher les notifications
function showNotification(title, message, type = 'info') {
    const notification = `
        <div class="notification notification-${type}" id="notification-${Date.now()}">
            <div class="notification-content">
                <strong>${title}</strong><br>
                ${message}
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', notification);
    
    // Auto-remove après 5 secondes
    setTimeout(() => {
        const notif = document.querySelector('.notification:last-child');
        if (notif) notif.remove();
    }, 5000);
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

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si Bootstrap Modal est disponible
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap JS n\'est pas chargé. La modal d\'aide ne fonctionnera pas correctement.');
    }
});
</script>
@endsection