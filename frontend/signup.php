<?php
session_start();

$nomValue = $prenomValue = $emailValue = "";
$telephoneValue = $dateNaissanceValue = $adresseValue = "";
$errorMessage = $successMessage = "";

if (isset($_POST['submit'])) {

    $nomValue = trim($_POST['nom']);
    $prenomValue = trim($_POST['prenom']);
    $emailValue = trim($_POST['email']);
    $telephoneValue = trim($_POST['telephone']);
    $dateNaissanceValue = $_POST['date_naissance'];
    $adresseValue = trim($_POST['adresse']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($nomValue) || empty($prenomValue) || empty($emailValue) || empty($password)) {
        $errorMessage = "Champs obligatoires manquants";
    } elseif ($password !== $confirm) {
        $errorMessage = "Les mots de passe ne correspondent pas";
    } else {

        require_once '../backend/Connection.php';
        require_once '../backend/classes/Patient.php';

        $connection = new Connection();
        $connection->selectDatabase("gestion_rdv_medical1");
        $conn = $connection->getConn();

        $patient = new Patient(
            $nomValue,
            $prenomValue,
            $emailValue,
            $password,
            $telephoneValue,
            $dateNaissanceValue,
            $adresseValue
        );

        if ($patient->insert("patients", $conn)) {
            // ✅ redirection vers index.php après succès
            header("Location: index.php");
            exit();
        } else {
            $errorMessage = Patient::$errorMsg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - RDV Médical</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .signup-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<div class="container signup-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary"></i>
                        <h2 class="mt-3">Créer un compte</h2>
                        <p class="text-muted">Rejoignez-nous pour prendre vos rendez-vous</p>
                    </div>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger"><?= $errorMessage ?></div>
                    <?php endif; ?>

                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success"><?= $successMessage ?></div>
                    <?php endif; ?>

                    <form method="post">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom *</label>
                                <input class="form-control" name="nom" value="<?= $nomValue ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom *</label>
                                <input class="form-control" name="prenom" value="<?= $prenomValue ?>" required>
                            </div>
                        </div>

                        <label class="form-label">Email *</label>
                        <input class="form-control mb-3" name="email" type="email" value="<?= $emailValue ?>" required>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input class="form-control" name="telephone" value="<?= $telephoneValue ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de naissance</label>
                                <input class="form-control" type="date" name="date_naissance" value="<?= $dateNaissanceValue ?>">
                            </div>
                        </div>

                        <label class="form-label">Adresse</label>
                        <textarea class="form-control mb-3" name="adresse"><?= $adresseValue ?></textarea>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mot de passe *</label>
                                <input class="form-control" type="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmer mot de passe *</label>
                                <input class="form-control" type="password" name="confirm_password" required>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-lg w-100" name="submit">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>