<?php
require_once 'Connection.php';

$connection = new Connection();

// Créer la base de données
echo "Création de la base de données...<br>";
$connection->createDatabase("gestion_rdv_medical1");

// Sélectionner la base de données
echo "Sélection de la base de données...<br>";
$connection->selectDatabase("gestion_rdv_medical1");

// Table Patients
echo "Création de la table patients...<br>";
$queryPatients = "
CREATE TABLE IF NOT EXISTS patients (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    date_naissance DATE,
    adresse TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$connection->createTable($queryPatients);

// Table Médecins
echo "Création de la table medecins...<br>";
$queryMedecins = "
CREATE TABLE IF NOT EXISTS medecins (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    specialite VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    password VARCHAR(255) NOT NULL,
    tarif DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$connection->createTable($queryMedecins);

// Table Rendez-vous
echo "Création de la table rendez_vous...<br>";
$queryRendezVous = "
CREATE TABLE IF NOT EXISTS rendez_vous (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(6) UNSIGNED NOT NULL,
    medecin_id INT(6) UNSIGNED NOT NULL,
    date_rdv DATE NOT NULL,
    heure_rdv TIME NOT NULL,
    statut ENUM('en_attente', 'confirme', 'annule', 'termine') DEFAULT 'en_attente',
    motif TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE
)";
$connection->createTable($queryRendezVous);

// Table Dossiers Médicaux
echo "Création de la table dossiers_medicaux...<br>";
$queryDossiers = "
CREATE TABLE IF NOT EXISTS dossiers_medicaux (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(6) UNSIGNED NOT NULL,
    medecin_id INT(6) UNSIGNED NOT NULL,
    diagnostic TEXT NOT NULL,
    traitement TEXT,
    notes TEXT,
    date_consultation DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE
)";
$connection->createTable($queryDossiers);

echo "<br><strong style='color: green;'>✓ Base de données et tables créées avec succès!</strong><br>";
echo "<a href='../frontend/index.php'>Aller à la page d'accueil</a>";
?>