<?php
include "Connection.php";

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

$sql = "SELECT * FROM dossiers_medicaux";
$result = $connection->getConn()->query($sql);
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Médecin</th>
        <th>Diagnostic</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>

<?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["patient_id"] ?></td>
        <td><?= $row["medecin_id"] ?></td>
        <td><?= $row["diagnostic"] ?></td>
        <td><?= $row["date_consultation"] ?></td>
        <td>
            <a href="update.php?id=<?= $row['id'] ?>">Modifier</a> |
            <a href="delete.php?id=<?= $row['id'] ?>">Supprimer</a>
        </td>
    </tr>
<?php } ?>
</table>

<br>
<a href="create.php">➕ Ajouter un dossier</a>