<?php
session_start();

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$errorMessage = "";
$successMessage = "";
$medecinValue = "";
$dateValue = "";
$heureValue = "";
$motifValue = "";

require_once '../backend/Connection.php';
require_once '../backend/classes/medecin.php';
require_once '../backend/classes/rendezvous.php';

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

// ✅ CORRECTION ICI
$conn = $connection->getConn();

// ✅ Ligne 22 corrigée
$medecins = Medecin::selectAllMedecins("medecins", $conn);

// Pré-sélectionner un médecin si passé en paramètre
if (isset($_GET['medecin_id'])) {
    $medecinValue = $_GET['medecin_id'];
}

if (isset($_POST["submit"])) {
    $medecinValue = $_POST["medecin_id"];
    $dateValue = $_POST["date_rdv"];
    $heureValue = $_POST["heure_rdv"];
    $motifValue = $_POST["motif"];
    $patient_id = $_SESSION['patient_id'];

    if (empty($medecinValue) || empty($dateValue) || empty($heureValue)) {
        $errorMessage = "Veuillez remplir tous les champs obligatoires!";
    } else {
        $rdv = new RendezVous(
            $patient_id,
            $medecinValue,
            $dateValue,
            $heureValue,
            $motifValue
        );

        // ✅ Ligne 40 corrigée
        if ($rdv->insertRendezVous("rendez_vous", $conn)) {
            $successMessage = RendezVous::$successMsg;

            // Réinitialiser les champs
            $medecinValue = "";
            $dateValue = "";
            $heureValue = "";
            $motifValue = "";
        } else {
            $errorMessage = RendezVous::$errorMsg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre Rendez-vous</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-plus"></i> Prendre un Rendez-vous
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if(!empty($errorMessage)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($successMessage)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="medecin_id" class="form-label">
                                    <i class="fas fa-user-md"></i> Choisir un médecin *
                                </label>
                                <select class="form-select" id="medecin_id" name="medecin_id" required>
                                    <option value="">-- Sélectionnez un médecin --</option>
                                    <?php foreach($medecins as $medecin): ?>
                                        <option value="<?php echo $medecin['id']; ?>" 
                                                <?php echo ($medecinValue == $medecin['id']) ? 'selected' : ''; ?>>
                                            Dr. <?php echo $medecin['prenom'] . ' ' . $medecin['nom']; ?> 
                                            - <?php echo $medecin['specialite']; ?>
                                            <?php if($medecin['tarif'] > 0): ?>
                                                (<?php echo $medecin['tarif']; ?> MAD)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="date_rdv" class="form-label">
                                        <i class="fas fa-calendar"></i> Date du rendez-vous *
                                    </label>
                                    <input type="date" class="form-control" id="date_rdv" name="date_rdv" 
                                           value="<?php echo $dateValue; ?>" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="heure_rdv" class="form-label">
                                        <i class="fas fa-clock"></i> Heure du rendez-vous *
                                    </label>
                                    <select class="form-select" id="heure_rdv" name="heure_rdv" required>
                                        <option value="">-- Sélectionnez une heure --</option>
                                        <option value="08:00" <?php echo ($heureValue == '08:00') ? 'selected' : ''; ?>>08:00</option>
                                        <option value="08:30" <?php echo ($heureValue == '08:30') ? 'selected' : ''; ?>>08:30</option>
                                        <option value="09:00" <?php echo ($heureValue == '09:00') ? 'selected' : ''; ?>>09:00</option>
                                        <option value="09:30" <?php echo ($heureValue == '09:30') ? 'selected' : ''; ?>>09:30</option>
                                        <option value="10:00" <?php echo ($heureValue == '10:00') ? 'selected' : ''; ?>>10:00</option>
                                        <option value="10:30" <?php echo ($heureValue == '10:30') ? 'selected' : ''; ?>>10:30</option>
                                        <option value="11:00" <?php echo ($heureValue == '11:00') ? 'selected' : ''; ?>>11:00</option>
                                        <option value="11:30" <?php echo ($heureValue == '11:30') ? 'selected' : ''; ?>>11:30</option>
                                        <option value="14:00" <?php echo ($heureValue == '14:00') ? 'selected' : ''; ?>>14:00</option>
                                        <option value="14:30" <?php echo ($heureValue == '14:30') ? 'selected' : ''; ?>>14:30</option>
                                        <option value="15:00" <?php echo ($heureValue == '15:00') ? 'selected' : ''; ?>>15:00</option>
                                        <option value="15:30" <?php echo ($heureValue == '15:30') ? 'selected' : ''; ?>>15:30</option>
                                        <option value="16:00" <?php echo ($heureValue == '16:00') ? 'selected' : ''; ?>>16:00</option>
                                        <option value="16:30" <?php echo ($heureValue == '16:30') ? 'selected' : ''; ?>>16:30</option>
                                        <option value="17:00" <?php echo ($heureValue == '17:00') ? 'selected' : ''; ?>>17:00</option>
                                        <option value="17:30" <?php echo ($heureValue == '17:30') ? 'selected' : ''; ?>>17:30</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="motif" class="form-label">
                                    <i class="fas fa-comment-medical"></i> Motif de la consultation
                                </label>
                                <textarea class="form-control" id="motif" name="motif" rows="4" 
                                          placeholder="Décrivez brièvement le motif de votre consultation..."><?php echo $motifValue; ?></textarea>
                                <small class="text-muted">Optionnel - Cela aidera le médecin à mieux vous préparer</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Information:</strong> Votre rendez-vous sera enregistré avec le statut "En attente". 
                                Le cabinet vous contactera pour confirmer.
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check"></i> Confirmer le rendez-vous
                                </button>
                                <a href="medecins.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour à la liste des médecins
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts JavaScript personnalisés -->
    <script src="js/main.js"></script>
    <script src="js/form-validation.js"></script>
</body>
</html>