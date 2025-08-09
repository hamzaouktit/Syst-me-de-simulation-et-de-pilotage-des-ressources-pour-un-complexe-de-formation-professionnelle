<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Système de Pilotage') - OFPPT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --ofppt-green: #2E8B57;
            --ofppt-blue: #1E5F99;
            --ofppt-gray: #6B7280;
            --ofppt-light-gray: #F8F9FA;
            --ofppt-dark-blue: #1a4a75;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--ofppt-light-gray);
        }

        /* Header Principal */
        .ofppt-header {
            background: linear-gradient(135deg, var(--ofppt-blue) 0%, var(--ofppt-dark-blue) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0;
        }

        .ofppt-top-bar {
            background-color: var(--ofppt-green);
            color: white;
            padding: 8px 0;
            font-size: 0.9rem;
        }

        .ofppt-navbar {
            padding: 15px 0;
        }

        .ofppt-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white !important;
            font-weight: bold;
            font-size: 1.3rem;
        }

        .ofppt-brand:hover {
            color: #e0e0e0 !important;
        }

        .ofppt-logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(45deg, var(--ofppt-green) 0%, var(--ofppt-gray) 50%, var(--ofppt-blue) 100%);
            border-radius: 8px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-1px);
        }

        .user-info {
            background-color: rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: 8px 15px;
            margin-right: 10px;
            font-size: 0.9rem;
        }

        .btn-logout {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220,53,69,0.3);
        }

        /* Breadcrumb Style */
        .ofppt-breadcrumb {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .breadcrumb {
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--ofppt-blue);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--ofppt-gray);
        }

        /* Contenu principal */
        .main-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        /* Footer */
        .ofppt-footer {
            background: linear-gradient(135deg, var(--ofppt-dark-blue) 0%, var(--ofppt-blue) 100%);
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        .footer-section h5 {
            color: var(--ofppt-green);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            color: rgba(255,255,255,0.7);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ofppt-logo {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
            
            .ofppt-brand {
                font-size: 1.1rem;
            }
            
            .main-content {
                padding: 20px;
                margin: 0 10px 20px;
            }
        }
    </style>
</head>
<body>
    {{-- Barre supérieure --}}
    <div class="ofppt-top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Office de la Formation Professionnelle et de la Promotion du Travail
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-phone me-2"></i>
                    <span class="me-3">0537 76 42 00</span>
                    <i class="fas fa-envelope me-2"></i>
                    <span>contact@ofppt.ma</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Header Principal --}}
    <header class="ofppt-header">
        <nav class="navbar navbar-expand-lg ofppt-navbar">
            <div class="container">
                <a class="ofppt-brand" href="@auth
                    @if(Auth::user()->role === 'directeur_complexe')
                        {{ route('dashboard.complexe') }}
                    @elseif(Auth::user()->role === 'directeur_etablissement')
                        {{ route('dashboard.etablissement') }}
                    @else
                        {{ route('dashboard.complexe') }}
                    @endif
                @else
                    {{ route('login') }}
                @endauth">
                    <div class="ofppt-logo">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Système de Pilotage
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        style="border: none; padding: 4px 8px;">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        @auth
                            <li class="nav-item">
                                <span class="nav-link user-info">
                                    <i class="fas fa-user-circle me-2"></i>
                                    Bonjour, {{ Auth::user()->nom }}
                                </span>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-logout">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Connexion
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    {{-- Fil d'Ariane --}}
    @hasSection('breadcrumb')
        <div class="container mt-4">
            <div class="ofppt-breadcrumb">
                @yield('breadcrumb')
            </div>
        </div>
    @endif

    {{-- Contenu principal --}}
    <div class="container mt-4">
        <div class="main-content fade-in-up">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Footer --}}
    <footer class="ofppt-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-section">
                    <h5><i class="fas fa-building me-2"></i>À propos de l'OFPPT</h5>
                    <p class="text-light">
                        L'Office de la Formation Professionnelle et de la Promotion du Travail, 
                        établissement public créé en 1974, est le premier opérateur de formation professionnelle au Maroc.
                    </p>
                </div>
                <div class="col-md-4 footer-section">
                    <h5><i class="fas fa-link me-2"></i>Liens utiles</h5>
                    <ul>
                        <li><a href="https://www.ofppt.ma">Site officiel OFPPT</a></li>
                        <li><a href="https://www.myway.ac.ma">MyWay - Orientation</a></li>
                        <li><a href="#" onclick="return false;">Formation continue</a></li>
                        <li><a href="#" onclick="return false;">Insertion professionnelle</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-section">
                    <h5><i class="fas fa-envelope me-2"></i>Contact</h5>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Angle Bd Bir Anzarane et Rue Bachir Lazrak, Casablanca</p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i>+212 537 76 42 00</p>
                    <p><i class="fas fa-envelope me-2"></i>contact@ofppt.ma</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} OFPPT - Office de la Formation Professionnelle et de la Promotion du Travail. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation au scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.ofppt-header');
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
            } else {
                header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            }
        });

        // Animation pour les alertes
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>