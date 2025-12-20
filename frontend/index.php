<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion RDV Médical - Accueil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .feature-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-heartbeat"></i> RDV Médical
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medecins.php">Médecins</a>
                    </li>
                    <?php if(isset($_SESSION['patient_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="rendezvous.php">Mes RDV</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dossier_medical.php">Mon Dossier</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white ms-2" href="signup.php">Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 mb-4">Bienvenue sur votre plateforme de santé</h1>
            <p class="lead mb-5">Prenez rendez-vous facilement avec nos médecins qualifiés</p>
            <?php if(!isset($_SESSION['patient_id'])): ?>
                <a href="signup.php" class="btn btn-light btn-lg me-3">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </a>
                <a href="medecins.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-search"></i> Voir les médecins
                </a>
            <?php else: ?>
                <a href="medecins.php" class="btn btn-light btn-lg">
                    <i class="fas fa-calendar-plus"></i> Prendre un rendez-vous
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mb-5">
        <h2 class="text-center mb-5">Nos Services</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Médecins Qualifiés</h5>
                        <p class="card-text">Accédez à une équipe de médecins spécialisés et expérimentés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Prise de RDV Facile</h5>
                        <p class="card-text">Réservez vos rendez-vous en quelques clics</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Disponibilité 24/7</h5>
                        <p class="card-text">Consultez et gérez vos rendez-vous à tout moment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="bg-light py-5 mb-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4">
                    <h2 class="text-primary">500+</h2>
                    <p class="lead">Patients satisfaits</p>
                </div>
                <div class="col-md-4">
                    <h2 class="text-success">50+</h2>
                    <p class="lead">Médecins experts</p>
                </div>
                <div class="col-md-4">
                    <h2 class="text-warning">10+</h2>
                    <p class="lead">Spécialités médicales</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p>&copy; 2024 Gestion RDV Médical. Tous droits réservés.</p>
            <p>
                <i class="fas fa-phone"></i> +212 5XX-XXXXXX | 
                <i class="fas fa-envelope"></i> contact@rdvmedical.ma
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>