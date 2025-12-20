<?php
session_start();

// Vérifier si l'ID du médecin est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: read.php");
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

// Récupérer les informations du médecin
$sql = "SELECT * FROM $tableName WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $medecin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: read.php?error=Médecin non trouvé");
    exit();
}

$medecin = mysqli_fetch_assoc($result);

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $tarif = $_POST['tarif'];
    
    // Nettoyer et valider le tarif
    $tarif = str_replace(',', '.', $tarif); // Remplacer les virgules par des points
    $tarif = floatval($tarif); // Convertir en float
    
    // Vérifier si la colonne tarif existe dans la table
    $check_column = "SHOW COLUMNS FROM $tableName LIKE 'tarif'";
    $column_result = mysqli_query($conn, $check_column);
    $has_tarif_column = (mysqli_num_rows($column_result) > 0);
    
    if ($has_tarif_column) {
        // Mettre à jour avec tarif
        $update_sql = "UPDATE $tableName SET email = ?, telephone = ?, adresse = ?, tarif = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssdi", $email, $telephone, $adresse, $tarif, $medecin_id);
    } else {
        // Mettre à jour sans tarif
        $update_sql = "UPDATE $tableName SET email = ?, telephone = ?, adresse = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssi", $email, $telephone, $adresse, $medecin_id);
    }
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Mettre à jour les données locales
        $medecin['email'] = $email;
        $medecin['telephone'] = $telephone;
        $medecin['adresse'] = $adresse;
        if ($has_tarif_column) {
            $medecin['tarif'] = $tarif;
        }
        
        $success_message = "Médecin modifié avec succès!";
    } else {
        $error_message = "Erreur lors de la modification: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Médecin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
        <h2 class="mb-4"><i class="fas fa-edit text-warning"></i> Modifier le Médecin</h2>
        
        <!-- Afficher les informations non modifiables -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Informations du Médecin</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID :</strong> <?php echo $medecin['id']; ?></p>
                        <p><strong>Nom :</strong> <?php echo $medecin['nom']; ?></p>
                        <p><strong>Prénom :</strong> <?php echo $medecin['prenom']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Spécialité :</strong> 
                            <span class="badge bg-primary"><?php echo $medecin['specialite']; ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        <?php if(isset($success_message)): ?>
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <?php if(isset($error_message)): ?>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire de modification -->
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-pencil-alt"></i> Modifier les informations</h5>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="email"><i class="fas fa-envelope"></i> Email :</label>
                            <input class="form-control" type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($medecin['email']); ?>" required>
                            <div class="form-text">Adresse email professionnelle</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="telephone"><i class="fas fa-phone"></i> Téléphone :</label>
                            <input class="form-control" type="text" id="telephone" name="telephone" 
                                   value="<?php echo htmlspecialchars($medecin['telephone'] ?? ''); ?>" required>
                            <div class="form-text">Format: 0612345678</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="tarif"><i class="fas fa-money-bill-wave"></i> Tarif (MAD) :</label>
                            <div class="input-group">
                                <input class="form-control" type="number" id="tarif" name="tarif" 
                                       step="0.01" min="0"
                                       value="<?php echo isset($medecin['tarif']) ? htmlspecialchars($medecin['tarif']) : '0'; ?>" required>
                                <span class="input-group-text">MAD</span>
                            </div>
                            <div class="form-text">Tarif de consultation en Dirhams</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="adresse"><i class="fas fa-map-marker-alt"></i> Adresse :</label>
                            <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($medecin['adresse'] ?? ''); ?></textarea>
                            <div class="form-text">Adresse du cabinet médical</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-warning me-2">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                                <a href="read.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour à la liste
                                </a>
                                <a href="read.php" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Résumé des modifications -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Champs modifiables</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Email :</strong> Adresse de contact principale</li>
                            <li><strong>Téléphone :</strong> Numéro de contact professionnel</li>
                            <li><strong>Tarif :</strong> Prix de la consultation en MAD</li>
                            <li><strong>Adresse :</strong> Localisation du cabinet</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-lock"></i> Champs non modifiables</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>ID :</strong> Identifiant unique (fixe)</li>
                            <li><strong>Nom :</strong> Nom de famille</li>
                            <li><strong>Prénom :</strong> Prénom</li>
                            <li><strong>Spécialité :</strong> Domaine médical</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validation du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const telephoneInput = document.getElementById('telephone');
            const tarifInput = document.getElementById('tarif');
            
            // Formatage du téléphone
            telephoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                e.target.value = value;
            });
            
            // Validation du tarif
            tarifInput.addEventListener('input', function(e) {
                let value = e.target.value;
                // Remplacer les virgules par des points
                value = value.replace(',', '.');
                
                // Empêcher plus de 2 décimales
                const parts = value.split('.');
                if (parts.length > 1 && parts[1].length > 2) {
                    value = parts[0] + '.' + parts[1].substring(0, 2);
                }
                
                e.target.value = value;
                
                // Mettre à jour l'affichage en temps réel
                const tarifValue = parseFloat(value) || 0;
                document.getElementById('tarif-preview').textContent = tarifValue.toFixed(2) + ' MAD';
            });
            
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const telephone = document.getElementById('telephone').value;
                const tarif = document.getElementById('tarif').value;
                
                // Validation de l'email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Veuillez entrer une adresse email valide');
                    e.preventDefault();
                    return false;
                }
                
                // Validation du téléphone (10 chiffres)
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(telephone)) {
                    alert('Le téléphone doit contenir exactement 10 chiffres');
                    e.preventDefault();
                    return false;
                }
                
                // Validation du tarif (nombre positif)
                const tarifValue = parseFloat(tarif);
                if (isNaN(tarifValue) || tarifValue < 0) {
                    alert('Le tarif doit être un nombre positif');
                    e.preventDefault();
                    return false;
                }
                
                // Confirmation avant envoi
                if (!confirm('Êtes-vous sûr de vouloir modifier ce médecin ?')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Aperçu du tarif en temps réel
            const tarifPreview = document.createElement('div');
            tarifPreview.id = 'tarif-preview';
            tarifPreview.className = 'text-success fw-bold mt-1';
            tarifPreview.textContent = (parseFloat(tarifInput.value) || 0).toFixed(2) + ' MAD';
            tarifInput.parentNode.appendChild(tarifPreview);
        });
    </script>
</body>
</html>