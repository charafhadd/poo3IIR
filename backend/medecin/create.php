<?php
session_start();
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    $host = "localhost:3307";
    $username = "root";
    $password = "";
    $database = "gestion_rdv_medical1";
    
    $conn = mysqli_connect($host, $username, $password, $database);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Récupérer les données du formulaire
    $nom = $_POST['firstName'];
    $prenom = $_POST['lastName'];
    $specialite = $_POST['specialite'];
    $email = $_POST['email'];
    $telephone = $_POST['text']; // Note: le nom du champ est "text", pas "telephone"
    $adresse = $_POST['adresse'];
    
    // Vérifier si la table existe (medecin ou medecins)
    $check_table = "SHOW TABLES LIKE 'medecins'";
    $result = mysqli_query($conn, $check_table);
    
    if (mysqli_num_rows($result) == 0) {
        // Essayez 'medecin' au singulier
        $check_table = "SHOW TABLES LIKE 'medecin'";
        $result = mysqli_query($conn, $check_table);
        
        if (mysqli_num_rows($result) == 0) {
            // Créer la table si elle n'existe pas
            $create_table = "CREATE TABLE medecin (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                specialite VARCHAR(100) NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                telephone VARCHAR(20),
                adresse TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if (!mysqli_query($conn, $create_table)) {
                die("Erreur création table: " . mysqli_error($conn));
            }
            $tableName = "medecin";
        } else {
            $tableName = "medecin";
        }
    } else {
        $tableName = "medecins";
    }
    
    // Insérer le médecin dans la base de données
    // Note: Votre formulaire a le champ "text" pour le téléphone, pas "telephone"
    $sql = "INSERT INTO $tableName (nom, prenom, specialite, email, telephone, adresse) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $nom, $prenom, $specialite, $email, $telephone, $adresse);
    
    if (mysqli_stmt_execute($stmt)) {
        // Succès - rediriger vers read.php
        mysqli_close($conn);
        header("Location: read.php");
        exit();
    } else {
        $error_message = "Erreur: " . mysqli_error($conn);
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Médecin</title>
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
                        <a class="nav-link active" href="medecins.php">Médecins</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="read.php">Liste des Médecins</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-user-md"></i> Ajouter un Nouveau Médecin</h2>

        <!-- Message d'erreur -->
        <?php if(isset($error_message)): ?>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Erreur :</strong> <?php echo $error_message; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <!-- Message de succès (ne devrait pas apparaître avec la redirection) -->
        <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Succès :</strong> Médecin ajouté avec succès !
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="fname">Nom :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" id="fname" name="firstName" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="lname">Prénom :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" id="lname" name="lastName" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="specialite">Spécialité :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" id="specialite" name="specialite" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="email">Email :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="email" id="email" name="email" required>
                </div>
            </div>
             <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="email">Password :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="password" id="password" name="password" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="telephone">Téléphone :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" id="telephone" name="text" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="adresse">Adresse :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" id="adresse" name="adresse">
                </div>
            </div>
             <div class="row mb-3">
                <label class="col-form-label col-sm-2" for="adresse">Tarif :</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" id="tarif" name="tarif">
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-2 col-sm-6">
                    <div class="d-grid gap-2 d-md-flex">
                        <button name="submit" type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-user-plus"></i> Ajouter le Médecin
                        </button>
                        <a href="read.php" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Voir la Liste
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
      <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p>&copy; 2024 Gestion RDV Médical. Tous droits réservés.</p>
            <p>
                <i class="fas fa-phone"></i> +212 5XX-XXXXXX | 
                <i class="fas fa-envelope"></i> contact@rdvmedical.ma
            </p>
        </div>
    </footer>

</body>
</html>