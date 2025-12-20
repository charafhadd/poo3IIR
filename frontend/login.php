<?php
session_start();
$errorMessage = "";

if (isset($_POST["submit"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $errorMessage = "Tous les champs doivent être remplis !";
    } else {
        require_once '../backend/Connection.php';
        require_once '../backend/classes/Patient.php';

        $connection = new Connection();
        $connection->selectDatabase("gestion_rdv_medical1");
        $conn = $connection->getConn(); // ✅ Utiliser getConn() pour la connexion

        $patient = Patient::login("patients", $conn, $email, $password);

        if ($patient) {
            $_SESSION['patient_id'] = $patient['id'];
            $_SESSION['patient_nom'] = $patient['nom'];
            $_SESSION['patient_prenom'] = $patient['prenom'];
            $_SESSION['patient_email'] = $patient['email'];

            header("Location: index.php"); // redirection vers index
            exit();
        } else {
            $errorMessage = "Email ou mot de passe incorrect !";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - RDV Médical</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-heartbeat fa-3x text-primary"></i>
                            <h2 class="mt-3">Connexion</h2>
                            <p class="text-muted">Accédez à votre espace patient</p>
                        </div>

                        <?php if(!empty($errorMessage)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Se connecter
                                </button>
                            </div>

                            <div class="text-center">
                                <p>Pas encore de compte? <a href="signup.php">S'inscrire</a></p>
                                <a href="index.php" class="text-muted">
                                    <i class="fas fa-home"></i> Retour à l'accueil
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>