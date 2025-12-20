<?php
include "Connection.php";

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

$id = $_GET["id"];

$sql = "SELECT * FROM dossiers_medicaux WHERE id=$id";
$result = $connection->getConn()->query($sql);
$row = $result->fetch_assoc();

if (isset($_POST["submit"])) {

    $patient_id = $_POST["patient_id"];
    $medecin_id = $_POST["medecin_id"];
    $diagnostic = $_POST["diagnostic"];
    $traitement = $_POST["traitement"];
    $notes = $_POST["notes"];
    $date_consultation = $_POST["date_consultation"];

    $updateSql = "UPDATE dossiers_medicaux SET
        patient_id='$patient_id',
        medecin_id='$medecin_id',
        diagnostic='$diagnostic',
        traitement='$traitement',
        notes='$notes',
        date_consultation='$date_consultation'
        WHERE id=$id";

    if ($connection->getConn()->query($updateSql)) {
        header("Location: read.php");
    } else {
        echo "Erreur : " . $connection->getConn()->error;
    }
}
?>

<form method="post">
    Patient ID: <input type="number" name="patient_id" value="<?= $row['patient_id'] ?>"><br><br>
    Médecin ID: <input type="number" name="medecin_id" value="<?= $row['medecin_id'] ?>"><br><br>
    Diagnostic: <textarea name="diagnostic"><?= $row['diagnostic'] ?></textarea><br><br>
    Traitement: <textarea name="traitement"><?= $row['traitement'] ?></textarea><br><br>
    Notes: <textarea name="notes"><?= $row['notes'] ?></textarea><br><br>
    Date consultation: <input type="date" name="date_consultation" value="<?= $row['date_consultation'] ?>"><br><br>

    <button name="submit">Mettre à jour</button>
</form>