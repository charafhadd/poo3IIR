<?php
session_start();
require_once '../backend/Connection.php';
require_once '../backend/classes/Medecin.php';

// Création de la connexion
$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

// ✅ Récupération de la connexion mysqli
$conn = $connection->getConn();

// Récupération de tous les médecins
$medecins = Medecin::selectAllMedecins("medecins", $conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Médecins - RDV Médical</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .medecin-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
        }
        .medecin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .specialite-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="medecins.php">Médecins</a>
                    </li>
                    <?php if(isset($_SESSION['patient_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="rendezvous.php">Mes RDV</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="display-4"><i class="fas fa-user-md text-primary"></i> Nos Médecins</h1>
            <p class="lead text-muted">Choisissez votre médecin et prenez rendez-vous</p>
        </div>

        <?php if(empty($medecins)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Aucun médecin disponible pour le moment.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($medecins as $medecin): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card medecin-card">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4">
                                        <i class="fas fa-user-md fa-3x text-primary"></i>
                                    </div>
                                </div>
                                <h5 class="card-title text-center">
                                    Dr. <?php echo $medecin['prenom'] . ' ' . $medecin['nom']; ?>
                                </h5>
                                <p class="text-center">
                                    <span class="badge specialite-badge">
                                        <?php echo $medecin['specialite']; ?>
                                    </span>
                                </p>
                                <hr>
                                <p class="card-text">
                                    <i class="fas fa-envelope text-primary"></i> <?php echo $medecin['email']; ?><br>
                                    <?php if($medecin['telephone']): ?>
                                        <i class="fas fa-phone text-success"></i> <?php echo $medecin['telephone']; ?><br>
                                    <?php endif; ?>
                                    <?php if($medecin['tarif'] > 0): ?>
                                        <i class="fas fa-money-bill-wave text-warning"></i> <?php echo $medecin['tarif']; ?> MAD
                                    <?php endif; ?>
                                </p>
                                <?php if(isset($_SESSION['patient_id'])): ?>
                                    <a href="prendre_rdv.php?medecin_id=<?php echo $medecin['id']; ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-plus"></i> Prendre RDV
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-sign-in-alt"></i> Se connecter pour prendre RDV
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>