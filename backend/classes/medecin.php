<?php

class Medecin
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $telephone;
    private string $specialite;
    private float $tarif;

    public function __construct(
        int $id,
        string $nom,
        string $prenom,
        string $email,
        string $telephone,
        string $specialite,
        float $tarif
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->specialite = $specialite;
        $this->tarif = $tarif;
    }

    // ---------- GETTERS ----------
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getTelephone(): string { return $this->telephone; }
    public function getSpecialite(): string { return $this->specialite; }
    public function getTarif(): float { return $this->tarif; }
    
        // MÉTHODE POUR INSÉRER UN MÉDECIN
    public function insertMedecin($tableName, $conn) {
        $sql = "INSERT INTO $tableName (nom, prenom, specialite, email, telephone, tarif, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssds", $this->nom, $this->prenom, $this->specialite, $this->email, $this->telephone, $this->tarif,);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // ---------- MÉTHODE STATIQUE (READ) ----------
    public static function selectAllMedecins(string $table, mysqli $conn): array
    {
        $sql = "SELECT * FROM $table ORDER BY nom";
        $result = $conn->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

public static function selectMedecinById($tableName, $conn, $id) {
    $sql = "SELECT * FROM $tableName WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

public static function updateMedecin($id, $nom, $prenom, $specialite, $email, $telephone, $tarif, $password, $conn) {
    $sql = "UPDATE medecins SET nom = ?, prenom = ?, specialite = ?, email = ?, telephone = ?, tarif = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdsi", $nom, $prenom, $specialite, $email, $telephone, $tarif, $password, $id);
    return $stmt->execute();
}

public static function deleteMedecin($tableName, $conn, $id) {
    $sql = "DELETE FROM $tableName WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
}

