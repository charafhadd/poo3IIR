<?php
include ("../Connection.php");

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

$error = "";
$success = "";

if (isset($_POST["submit"])) {

    $patient_id = $_POST["patient_id"];
    $medecin_id = $_POST["medecin_id"];
    $diagnostic = $_POST["diagnostic"];
    $traitement = $_POST["traitement"];
    $notes = $_POST["notes"];
    $date_consultation = $_POST["date_consultation"];

    if (empty($patient_id) || empty($medecin_id) || empty($diagnostic) || empty($date_consultation)) {
        $error = "Champs obligatoires manquants";
    } else {

        $sql = "INSERT INTO dossiers_medicaux
        (patient_id, medecin_id, diagnostic, traitement, notes, date_consultation)
        VALUES
        ('$patient_id', '$medecin_id', '$diagnostic', '$traitement', '$notes', '$date_consultation')";

        if ($connection->$connection->getConn()->query($sql)) {
            $success = "Dossier médical ajouté avec succès";
        } else {
$conn = $connection->getConn();        }
    }
}
?>

<form method="post">
    Patient ID: <input type="number" name="patient_id"><br><br>
    Médecin ID: <input type="number" name="medecin_id"><br><br>
    Diagnostic: <textarea name="diagnostic"></textarea><br><br>
    Traitement: <textarea name="traitement"></textarea><br><br>
    Notes: <textarea name="notes"></textarea><br><br>
    Date consultation: <input type="date" name="date_consultation"><br><br>
    <button name="submit">Ajouter</button>
</form>

<p style="color:red"><?= $error ?></p>
<p style="color:green"><?= $success ?></p>