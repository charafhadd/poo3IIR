<?php
session_start();

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../backend/Connection.php';
require_once '../backend/classes/Dossier_Medical.php';

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

//  appel correct
$dossiers = fichierMedical::selectAllFichiers(
    "dossiers_medicaux",
     $connection->getConn(),
    $_SESSION['patient_id']      
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Dossier Médical - RDV Médical</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dossier-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 0;
            margin-bottom: 30px;
        }
        .dossier-card {
            border-left: 5px solid #667eea;
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        .dossier-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #667eea;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -35px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #667eea;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
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
                        <a class="nav-link" href="medecins.php">Médecins</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rendezvous.php">Mes RDV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dossier_medical.php">Mon Dossier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">Mon Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="dossier-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4">
                        <i class="fas fa-file-medical"></i> Mon Dossier Médical
                    </h1>
                    <p class="lead mb-0">
                        <i class="fas fa-user"></i> 
                        <?php echo $_SESSION['patient_prenom'] . ' ' . $_SESSION['patient_nom']; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <button class="btn btn-outline-light btn-lg" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <?php if(empty($dossiers)): ?>
            <!-- Empty State -->
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-lg border-0 text-center p-5">
                        <div class="card-body">
                            <i class="fas fa-folder-open fa-5x text-muted mb-4"></i>
                            <h3 class="mb-3">Aucun dossier médical</h3>
                            <p class="text-muted mb-4">
                                Votre dossier médical sera créé automatiquement lors de vos consultations avec nos médecins.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="medecins.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-md"></i> Voir les médecins
                                </a>
                                <a href="prendre_rdv.php" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-calendar-plus"></i> Prendre RDV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Statistics Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-notes-medical fa-3x mb-2"></i>
                        <h3 class="mb-0"><?php echo count($dossiers); ?></h3>
                        <p class="mb-0">Consultations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-user-md fa-3x mb-2"></i>
                        <h3 class="mb-0">
                            <?php 
                            $medecins_uniques = array_unique(array_column($dossiers, 'medecin_id'));
                            echo count($medecins_uniques); 
                            ?>
                        </h3>
                        <p class="mb-0">Médecins consultés</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-calendar-check fa-3x mb-2"></i>
                        <h3 class="mb-0"><?php echo date('d/m/Y', strtotime($dossiers[0]['date_consultation'])); ?></h3>
                        <p class="mb-0">Dernière visite</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-clock fa-3x mb-2"></i>
                        <h3 class="mb-0">
                            <?php 
                            $premiere_consultation = end($dossiers);
                            $date1 = new DateTime($premiere_consultation['date_consultation']);
                            $date2 = new DateTime();
                            echo $date1->diff($date2)->days . ' j'; 
                            ?>
                        </h3>
                        <p class="mb-0">Depuis la 1ère visite</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0">
                                <i class="fas fa-filter text-primary"></i> Filtrer les consultations
                            </h5>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterMedecin" onchange="filterDossiers()">
                                <option value="">Tous les médecins</option>
                                <?php 
                                $medecins_list = [];
                                foreach($dossiers as $d) {
                                    $key = $d['medecin_id'];
                                    if(!isset($medecins_list[$key])) {
                                        $medecins_list[$key] = 'Dr. ' . $d['medecin_prenom'] . ' ' . $d['medecin_nom'];
                                    }
                                }
                                foreach($medecins_list as $id => $nom): ?>
                                    <option value="<?php echo $id; ?>"><?php echo $nom; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterYear" onchange="filterDossiers()">
                                <option value="">Toutes les années</option>
                                <?php 
                                $years = array_unique(array_map(function($d) {
                                    return date('Y', strtotime($d['date_consultation']));
                                }, $dossiers));
                                foreach($years as $year): ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline View Toggle -->
            <div class="mb-4">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary active" onclick="showView('cards')">
                        <i class="fas fa-th-large"></i> Vue Cartes
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="showView('timeline')">
                        <i class="fas fa-stream"></i> Vue Chronologique
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="showView('table')">
                        <i class="fas fa-table"></i> Vue Tableau
                    </button>
                </div>
            </div>

            <!-- Cards View (Default) -->
            <div id="cardsView">
                <div class="row g-4">
                    <?php foreach($dossiers as $dossier): ?>
                        <div class="col-md-12 dossier-item" 
                             data-medecin="<?php echo $dossier['medecin_id']; ?>"
                             data-year="<?php echo date('Y', strtotime($dossier['date_consultation'])); ?>">
                            <div class="card dossier-card shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="d-flex align-items-start mb-3">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="fas fa-user-md fa-2x text-primary"></i>
                                                </div>
                                                <div>
                                                    <h4 class="mb-1">
                                                        Dr. <?php echo $dossier['medecin_prenom'] . ' ' . $dossier['medecin_nom']; ?>
                                                    </h4>
                                                    <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <?php echo $dossier['specialite']; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="mb-3">
                                                <h6 class="text-primary fw-bold">
                                                    <i class="fas fa-stethoscope"></i> Diagnostic
                                                </h6>
                                                <div class="ms-4 p-3 bg-light rounded">
                                                    <?php echo nl2br(htmlspecialchars($dossier['diagnostic'])); ?>
                                                </div>
                                            </div>

                                            <?php if($dossier['traitement']): ?>
                                                <div class="mb-3">
                                                    <h6 class="text-success fw-bold">
                                                        <i class="fas fa-pills"></i> Traitement prescrit
                                                    </h6>
                                                    <div class="ms-4 p-3 bg-light rounded">
                                                        <?php echo nl2br(htmlspecialchars($dossier['traitement'])); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if($dossier['notes']): ?>
                                                <div class="mb-3">
                                                    <h6 class="text-info fw-bold">
                                                        <i class="fas fa-notes-medical"></i> Notes du médecin
                                                    </h6>
                                                    <div class="ms-4 p-3 bg-light rounded">
                                                        <?php echo nl2br(htmlspecialchars($dossier['notes'])); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-3 border-start">
                                            <div class="text-center mb-4">
                                                <div class="bg-primary bg-opacity-10 rounded p-3 mb-3">
                                                    <i class="fas fa-calendar-day fa-3x text-primary"></i>
                                                </div>
                                                <h5 class="text-primary mb-1">
                                                    <?php echo date('d', strtotime($dossier['date_consultation'])); ?>
                                                </h5>
                                                <p class="mb-0">
                                                    <?php 
                                                    $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                                             'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                                    echo $mois[(int)date('m', strtotime($dossier['date_consultation']))] . ' ' . 
                                                         date('Y', strtotime($dossier['date_consultation']));
                                                    ?>
                                                </p>
                                            </div>

                                            <hr>

                                            <div class="mb-3">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-clock"></i> Créé le
                                                </small>
                                                <small class="fw-bold">
                                                    <?php echo date('d/m/Y H:i', strtotime($dossier['created_at'])); ?>
                                                </small>
                                            </div>

                                            <div class="d-grid gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewDetails(<?php echo $dossier['id']; ?>)">
                                                    <i class="fas fa-eye"></i> Détails
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="printDossier(<?php echo $dossier['id']; ?>)">
                                                    <i class="fas fa-print"></i> Imprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Timeline View (Hidden by default) -->
            <div id="timelineView" style="display: none;">
                <div class="timeline">
                    <?php foreach($dossiers as $index => $dossier): ?>
                        <div class="timeline-item dossier-item" 
                             data-medecin="<?php echo $dossier['medecin_id']; ?>"
                             data-year="<?php echo date('Y', strtotime($dossier['date_consultation'])); ?>">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5>
                                            Dr. <?php echo $dossier['medecin_prenom'] . ' ' . $dossier['medecin_nom']; ?>
                                        </h5>
                                        <span class="badge bg-primary">
                                            <?php echo date('d/m/Y', strtotime($dossier['date_consultation'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-stethoscope"></i> <?php echo $dossier['specialite']; ?>
                                    </p>
                                    <p class="mb-0"><strong>Diagnostic:</strong> <?php echo substr($dossier['diagnostic'], 0, 150); ?>...</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Table View (Hidden by default) -->
            <div id="tableView" style="display: none;">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Date</th>
                                        <th>Médecin</th>
                                        <th>Spécialité</th>
                                        <th>Diagnostic</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($dossiers as $dossier): ?>
                                        <tr class="dossier-item" 
                                            data-medecin="<?php echo $dossier['medecin_id']; ?>"
                                            data-year="<?php echo date('Y', strtotime($dossier['date_consultation'])); ?>">
                                            <td><?php echo date('d/m/Y', strtotime($dossier['date_consultation'])); ?></td>
                                            <td>Dr. <?php echo $dossier['medecin_prenom'] . ' ' . $dossier['medecin_nom']; ?></td>
                                            <td><span class="badge bg-info"><?php echo $dossier['specialite']; ?></span></td>
                                            <td><?php echo substr($dossier['diagnostic'], 0, 80); ?>...</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="viewDetails(<?php echo $dossier['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card shadow text-center p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h4 class="text-white mb-3">
                            <i class="fas fa-heartbeat"></i> Prenez soin de votre santé
                        </h4>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="medecins.php" class="btn btn-light btn-lg">
                                <i class="fas fa-user-md"></i> Consulter un médecin
                            </a>
                            <a href="prendre_rdv.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-calendar-plus"></i> Nouveau rendez-vous
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Print Button -->
    <button class="btn btn-primary btn-lg rounded-circle print-btn" onclick="window.print()" title="Imprimer">
        <i class="fas fa-print fa-lg"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle between views
        function showView(view) {
            document.getElementById('cardsView').style.display = 'none';
            document.getElementById('timelineView').style.display = 'none';
            document.getElementById('tableView').style.display = 'none';
            
            if(view === 'cards') {
                document.getElementById('cardsView').style.display = 'block';
            } else if(view === 'timeline') {
                document.getElementById('timelineView').style.display = 'block';
            } else if(view === 'table') {
                document.getElementById('tableView').style.display = 'block';
            }
        }

        // Filter dossiers
        function filterDossiers() {
            const medecinFilter = document.getElementById('filterMedecin').value;
            const yearFilter = document.getElementById('filterYear').value;
            const items = document.querySelectorAll('.dossier-item');
            
            items.forEach(item => {
                const itemMedecin = item.getAttribute('data-medecin');
                const itemYear = item.getAttribute('data-year');
                
                const matchMedecin = !medecinFilter || itemMedecin === medecinFilter;
                const matchYear = !yearFilter || itemYear === yearFilter;
                
                if(matchMedecin && matchYear) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // View details
        function viewDetails(id) {
            alert('Fonctionnalité de détails - ID: ' + id);
            // Vous pouvez créer une modal ou rediriger vers une page de détails
        }

        // Print specific dossier
        function printDossier(id) {
            window.print();
        }

        // Export to PDF
        function exportToPDF() {
            alert('Fonctionnalité d\'export PDF à implémenter');
            // Vous pouvez utiliser jsPDF ou une bibliothèque similaire
        }
    </script>
</body>
</html>