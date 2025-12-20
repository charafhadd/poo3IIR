<?php
include "Connection.php";

$connection = new Connection();
$connection->selectDatabase("gestion_rdv_medical1");

$id = $_GET["id"];

$sql = "DELETE FROM dossiers_medicaux WHERE id=$id";

if ( $connection->getConn()->query($sql)) {
    header("Location: read.php");
} else {
    echo "Erreur : " .  $connection->getConn()->error;
}