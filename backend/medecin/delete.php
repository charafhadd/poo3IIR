<?php
session_start();

// Vérifier si l'ID du médecin est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: read.php?error=Aucun ID spécifié");
    exit();
}

$medecin_id = $_GET['id'];

// Connexion à la base de données
$host = "localhost:3307";
$username = "root";
$password = "";
$database = "gestion_rdv_medical1";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Vérifier si la table existe
$check_table = "SHOW TABLES LIKE 'medecins'";
$result = mysqli_query($conn, $check_table);

if (mysqli_num_rows($result) == 0) {
    $check_table = "SHOW TABLES LIKE 'medecin'";
    $result = mysqli_query($conn, $check_table);
    
    if (mysqli_num_rows($result) == 0) {
        header("Location: read.php?error=Table non trouvée");
        exit();
    } else {
        $tableName = "medecin";
    }
} else {
    $tableName = "medecins";
}

// Récupérer les informations du médecin avant suppression (pour affichage)
$sql_select = "SELECT * FROM $tableName WHERE id = ?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $medecin_id);
mysqli_stmt_execute($stmt_select);
$result_select = mysqli_stmt_get_result($stmt_select);

if (mysqli_num_rows($result_select) == 0) {
    header("Location: read.php?error=Médecin non trouvé");
    exit();
}

$medecin = mysqli_fetch_assoc($result_select);

// Vérifier si la suppression est confirmée
$confirmed = isset($_GET['confirm']) && $_GET['confirm'] == 'yes';

if ($confirmed) {
    // Supprimer le médecin
    $sql_delete = "DELETE FROM $tableName WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $medecin_id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        mysqli_close($conn);
        header("Location: read.php?success=Médecin supprimé avec succès");
        exit();
    } else {
        $error_message = "Erreur lors de la suppression: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Médecin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .danger-card {
            border: 2px solid #dc3545;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    </style>
</head>
<body>
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
                        <a class="nav-link" href="read.php">Liste des Médecins</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Message d'erreur -->
                <?php if(isset($error_message)): ?>
                    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php endif; ?>
                
                <!-- Carte de confirmation -->
                <div class="card danger-card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Confirmation de suppression</h4>
                    </div>
                    <div class="card-body">
                        <!-- Informations du médecin à supprimer -->
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">
                                <i class="fas fa-user-md"></i> 
                                Dr. <?php echo htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']); ?>
                            </h5>
                            <p class="mb-2">
                                <strong>ID :</strong> <?php echo $medecin['id']; ?><br>
                                <strong>Spécialité :</strong> 
                                <span class="badge bg-primary"><?php echo htmlspecialchars($medecin['specialite']); ?></span><br>
                                <strong>Email :</strong> <?php echo htmlspecialchars($medecin['email']); ?><br>
                                <strong>Téléphone :</strong> <?php echo htmlspecialchars($medecin['telephone'] ?? 'Non défini'); ?>
                            </p>
                        </div>
                        
                        <!-- Avertissement important -->
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-radiation"></i> Attention ! Action irréversible</h6>
                            <ul class="mb-0">
                                <li>Cette action supprimera définitivement le médecin de la base de données</li>
                                <li>Les données ne pourront pas être récupérées</li>
                                <li>Cette action affectera tous les rendez-vous associés à ce médecin</li>
                            </ul>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="d-grid gap-3">
                            <a href="delete.php?id=<?php echo $medecin_id; ?>&confirm=yes" 
                               class="btn btn-danger btn-lg"
                               onclick="showLoading(event)">
                                <i class="fas fa-trash"></i> OUI, Supprimer définitivement ce médecin
                            </a>
                            
                            <a href="read.php" class="btn btn-success btn-lg">
                                <i class="fas fa-times"></i> NON, Annuler et retourner à la liste
                            </a>
                            
                            <a href="edit.php?id=<?php echo $medecin_id; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier à la place de supprimer
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            ID du médecin : <?php echo $medecin_id; ?> | 
                            Date de la demande : <?php echo date('d/m/Y H:i:s'); ?>
                        </small>
                    </div>
                </div>
                
                <!-- Détails supplémentaires -->
                <div class="mt-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-question-circle"></i> Que se passe-t-il après la suppression ?</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Le médecin sera immédiatement retiré de la liste</li>
                                <li>Son compte (si existant) sera désactivé</li>
                                <li>Les patients ne pourront plus prendre rendez-vous avec ce médecin</li>
                                <li>Une notification peut être envoyée aux patients concernés</li>
                                <li>Un rapport de suppression sera généré</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de chargement -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <h4 class="mt-3 text-danger">Suppression en cours...</h4>
                    <p class="text-muted">Veuillez patienter pendant la suppression du médecin.</p>
                    <p><i class="fas fa-exclamation-triangle text-warning"></i> Cette opération peut prendre quelques secondes</p>
                </div>
            </div>
        </div>
    </div>

   
</body>
</html>