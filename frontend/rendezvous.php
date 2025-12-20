<?php
session_start();

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../backend/Connection.php';
require_once '../backend/classes/rendezvous.php';

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

// ✅ CORRECTION ICI
$conn = $connection->getConn();

$rendezvous = RendezVous::selectRendezVousByPatient(
    "rendez_vous",
    $conn,
    $_SESSION['patient_id']
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Rendez-vous</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .rdv-card {
            transition: transform 0.2s;
            border-left: 4px solid;
        }
        .rdv-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-en_attente { border-color: #ffc107; }
        .status-confirme { border-color: #28a745; }
        .status-annule { border-color: #dc3545; }
        .status-termine { border-color: #6c757d; }
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
                        <a class="nav-link" href="medecins.php">Médecins</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="rendezvous.php">Mes RDV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dossier_medical.php">Mon Dossier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-calendar-alt text-primary"></i> Mes Rendez-vous
            </h2>
            <a href="medecins.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau RDV
            </a>
        </div>

        <?php if(empty($rendezvous)): ?>
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4>Vous n'avez aucun rendez-vous</h4>
                    <p class="text-muted mb-4">Prenez votre premier rendez-vous avec l'un de nos médecins</p>
                    <a href="medecins.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus"></i> Prendre un rendez-vous
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary"><?php echo count($rendezvous); ?></h3>
                            <p class="mb-0">Total RDV</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning">
                                <?php echo count(array_filter($rendezvous, function($r) { return $r['statut'] == 'en_attente'; })); ?>
                            </h3>
                            <p class="mb-0">En attente</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">
                                <?php echo count(array_filter($rendezvous, function($r) { return $r['statut'] == 'confirme'; })); ?>
                            </h3>
                            <p class="mb-0">Confirmés</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-secondary">
                                <?php echo count(array_filter($rendezvous, function($r) { return $r['statut'] == 'termine'; })); ?>
                            </h3>
                            <p class="mb-0">Terminés</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des RDV -->
            <div class="row g-3">
                <?php 
                $statusText = [
                    'en_attente' => 'En attente',
                    'confirme' => 'Confirmé',
                    'annule' => 'Annulé',
                    'termine' => 'Terminé'
                ];
                $statusBadge = [
                    'en_attente' => 'warning',
                    'confirme' => 'success',
                    'annule' => 'danger',
                    'termine' => 'secondary'
                ];
                $statusIcon = [
                    'en_attente' => 'clock',
                    'confirme' => 'check-circle',
                    'annule' => 'times-circle',
                    'termine' => 'check-double'
                ];
                
                foreach($rendezvous as $rdv): 
                    $statusClass = "status-" . $rdv['statut'];
                ?>
                    <div class="col-md-6">
                        <div class="card rdv-card <?php echo $statusClass; ?> shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-md text-primary"></i>
                                        Dr. <?php echo $rdv['medecin_prenom'] . ' ' . $rdv['medecin_nom']; ?>
                                    </h5>
                                    <span class="badge bg-<?php echo $statusBadge[$rdv['statut']]; ?>">
                                        <i class="fas fa-<?php echo $statusIcon[$rdv['statut']]; ?>"></i>
                                        <?php echo $statusText[$rdv['statut']]; ?>
                                    </span>
                                </div>

                                <p class="text-muted mb-3">
                                    <i class="fas fa-stethoscope"></i> <?php echo $rdv['specialite']; ?>
                                </p>

                                <hr>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <p class="mb-1">
                                            <i class="fas fa-calendar text-primary"></i>
                                            <strong>Date:</strong>
                                        </p>
                                        <p class="ms-4"><?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1">
                                            <i class="fas fa-clock text-success"></i>
                                            <strong>Heure:</strong>
                                        </p>
                                        <p class="ms-4"><?php echo date('H:i', strtotime($rdv['heure_rdv'])); ?></p>
                                    </div>
                                </div>

                                <?php if($rdv['telephone']): ?>
                                    <p class="mb-2">
                                        <i class="fas fa-phone text-info"></i>
                                        <strong>Contact:</strong> <?php echo $rdv['telephone']; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if($rdv['motif']): ?>
                                    <p class="mb-2">
                                        <i class="fas fa-comment text-warning"></i>
                                        <strong>Motif:</strong> <?php echo substr($rdv['motif'], 0, 100); ?>
                                        <?php if(strlen($rdv['motif']) > 100) echo '...'; ?>
                                    </p>
                                <?php endif; ?>

                                <hr>

                                <div class="d-flex gap-2">
                                    <?php if($rdv['statut'] == 'en_attente' || $rdv['statut'] == 'confirme'): ?>
                                        <a href="../back-end/rendezvous/delete.php?id=<?php echo $rdv['id']; ?>" 
                                           class="btn btn-danger btn-sm flex-fill"
                                           onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous?');">
                                            <i class="fas fa-times"></i> Annuler
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if($rdv['statut'] == 'termine'): ?>
                                        <span class="text-muted small">
                                            <i class="fas fa-check"></i> Rendez-vous terminé
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>
                                    <i class="fas fa-clock"></i> 
                                    Créé le <?php echo date('d/m/Y à H:i', strtotime($rdv['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Bouton flottant -->
            <a href="medecins.php" class="btn btn-primary btn-lg rounded-circle" 
               style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; 
                      display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0,0,0,0.2);"
               title="Prendre un nouveau RDV">
                <i class="fas fa-plus fa-lg"></i>
            </a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts JavaScript personnalisés -->
    <script src="js/main.js"></script>
    <script src="js/rendezvous.js"></script>
</body>
</html>