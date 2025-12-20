<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Médecins</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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
                    <?php
                  
                    if(isset($_SESSION['patient_id'])): ?>
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
    <h2>Gestion des Médecins</h2>
    <a class="btn btn-primary" href="create.php">Ajouter un Médecin</a>
    
    <br><br>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Spécialité</th>
                <th>Email</th>
                <th>Téléphone</th>
                 <th>Adresse</th>
                   <th>Tarif</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $host = "localhost:3307";
            $username = "root";
            $password = "";
            $database = "gestion_rdv_medical1";
            
            $conn = mysqli_connect($host, $username, $password, $database);
            
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            
            // Essayez d'abord 'medecins', puis 'medecin'
            $sql = "SELECT * FROM medecins";
            $result = mysqli_query($conn, $sql);
            
            // Si 'medecins' ne fonctionne pas, essayez 'medecin'
            if (!$result) {
                $sql = "SELECT * FROM medecin";
                $result = mysqli_query($conn, $sql);
            }
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nom']}</td>
                        <td>{$row['prenom']}</td>
                        <td>{$row['specialite']}</td>
                        <td>{$row['email']}</td>
                        <td>" . ($row['telephone'] ?? 'N/A') . "</td>
                         <td>{$row['adresse']}</td>
                          <td>{$row['tarif']}</td>
                        <td>
                            <a href='edit.php?id={$row['id']}' class='btn btn-warning'>Edit</a>
                            <a href='delete.php?id={$row['id']}' class='btn btn-danger'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Aucun médecin trouvé</td></tr>";
            }
            
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>

</body>
</html>