<?php

class Patient
{
    //  Attributs privés
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $date_naissance;
    private $adresse;
    private $password;

    // Messages
    public static $errorMsg = "";
    public static $successMsg = "";

    //  Constructeur
    public function __construct(
        $nom,
        $prenom,
        $email,
        $password,
        $telephone = "",
        $date_naissance = "",
        $adresse = ""
    ) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->date_naissance = $date_naissance;
        $this->adresse = $adresse;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /* =========================
       GETTERS
    ========================== */

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getDateNaissance()
    {
        return $this->date_naissance;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /* =========================
       SETTERS
    ========================== */

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    public function setDateNaissance($date)
    {
        $this->date_naissance = $date;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /* =========================
       MÉTHODES MÉTIER (CRUD)
    ========================== */

    //  Ajouter un patient
    public function insert($tableName, $conn)
    {
        $nom = mysqli_real_escape_string($conn, $this->nom);
        $prenom = mysqli_real_escape_string($conn, $this->prenom);
        $email = mysqli_real_escape_string($conn, $this->email);
        $telephone = mysqli_real_escape_string($conn, $this->telephone);
        $date_naissance = mysqli_real_escape_string($conn, $this->date_naissance);
        $adresse = mysqli_real_escape_string($conn, $this->adresse);
        $password = mysqli_real_escape_string($conn, $this->password);

        $sql = "INSERT INTO $tableName
                (nom, prenom, email, telephone, date_naissance, adresse, password)
                VALUES
                ('$nom', '$prenom', '$email', '$telephone', '$date_naissance', '$adresse', '$password')";

        if (mysqli_query($conn, $sql)) {
            self::$successMsg = "Inscription réussie";
            return true;
        } else {
            self::$errorMsg = mysqli_error($conn);
            return false;
        }
    }

    //  Tous les patients
    public static function getAll($tableName, $conn)
    {
        $sql = "SELECT * FROM $tableName ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);
        $patients = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }

        return $patients;
    }

    //  Patient par ID
    public static function getById($tableName, $conn, $id)
    {
        $id = mysqli_real_escape_string($conn, $id);
        $sql = "SELECT * FROM $tableName WHERE id='$id'";
        $result = mysqli_query($conn, $sql);

        return mysqli_fetch_assoc($result) ?: null;
    }

    //  Patient par email
    public static function getByEmail($tableName, $conn, $email)
    {
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM $tableName WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        return mysqli_fetch_assoc($result) ?: null;
    }

    //  Mise à jour
    public function update($tableName, $conn, $id)
    {
        $id = mysqli_real_escape_string($conn, $id);

        $sql = "UPDATE $tableName SET
                nom='{$this->nom}',
                prenom='{$this->prenom}',
                email='{$this->email}',
                telephone='{$this->telephone}',
                date_naissance='{$this->date_naissance}',
                adresse='{$this->adresse}'
                WHERE id='$id'";

        return mysqli_query($conn, $sql);
    }

    //  Supprimer
    public static function delete($tableName, $conn, $id)
    {
        $id = mysqli_real_escape_string($conn, $id);
        return mysqli_query($conn, "DELETE FROM $tableName WHERE id='$id'");
    }

    //  Login
    public static function login($tableName, $conn, $email, $password)
    {
        $patient = self::getByEmail($tableName, $conn, $email);

        if ($patient && password_verify($password, $patient['password'])) {
            return $patient;
        }
        return false;
    }
}