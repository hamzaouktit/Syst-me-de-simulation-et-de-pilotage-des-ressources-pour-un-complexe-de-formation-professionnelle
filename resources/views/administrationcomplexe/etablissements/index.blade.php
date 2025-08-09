<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Établissements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard.complexe') }}">
                <i class="fas fa-building"></i> Administration Complexe
            </a>
            <div class="navbar-nav ms-auto">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-school"></i> Liste des Établissements</h2>
                    @if(auth()->user()->role === 'directeur_complexe')
                        <a href="{{ route('administrationcomplexe.etablissements.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nouvel Établissement
                        </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-body">
                        @if($etablissements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> ID</th>
                                            <th><i class="fas fa-school"></i> Nom</th>
                                            <th><i class="fas fa-map-marker-alt"></i> Adresse</th>
                                            <th><i class="fas fa-user-tie"></i> Directeur</th>
                                            <th><i class="fas fa-building"></i> Complexe</th>
                                            <th><i class="fas fa-calendar"></i> Créé le</th>
                                            <th><i class="fas fa-cogs"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($etablissements as $etablissement)
                                            <tr>
                                                <td>{{ $etablissement->id }}</td>
                                                <td>
                                                    <strong>{{ $etablissement->nom }}</strong>
                                                </td>
                                                <td>
                                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                                    {{ $etablissement->adresse }}
                                                </td>
                                                <td>
                                                    @if($etablissement->directeur)
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                                            <div>
                                                                <strong>{{ $etablissement->directeur->nom }}</strong><br>
                                                                <small class="text-muted">{{ $etablissement->directeur->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-user-slash"></i> Aucun directeur
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $etablissement->complexe->nom }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ optional($etablissement->created_at)->format('d/m/Y') ?? 'Non défini' }}

                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('administrationcomplexe.etablissements.show', $etablissement) }}" 
                                                           class="btn btn-outline-info" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(auth()->user()->role === 'directeur_complexe')
                                                            <a href="{{ route('administrationcomplexe.etablissements.edit', $etablissement) }}" 
                                                               class="btn btn-outline-warning" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('administrationcomplexe.etablissements.destroy', $etablissement) }}" 
                                                                  method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet établissement ?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-school fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Aucun établissement trouvé</h4>
                                <p class="text-muted">
                                    @if(auth()->user()->role === 'directeur_complexe')
                                        Commencez par créer votre premier établissement.
                                    @else
                                        Vous n'êtes directeur d'aucun établissement pour le moment.
                                    @endif
                                </p>
                                @if(auth()->user()->role === 'directeur_complexe')
                                    <a href="{{ route('administrationcomplexe.etablissements.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Créer un établissement
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard.complexe') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>