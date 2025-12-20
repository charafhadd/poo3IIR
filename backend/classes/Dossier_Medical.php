<?php

class FichierMedical {

    public $id;
    public $patient_id;
    public $medecin_id;
    public $diagnostic;
    public $traitement;
    public $notes;
    public $date_consultation;
    public $created_at;

    public static $errorMsg = "";
    public static $successMsg = "";

    //  Constructeur
    public function __construct(
        $patient_id,
        $medecin_id,
        $diagnostic,
        $traitement,
        $notes,
        $date_consultation
    ) {
        $this->patient_id = $patient_id;
        $this->medecin_id = $medecin_id;
        $this->diagnostic = $diagnostic;
        $this->traitement = $traitement;
        $this->notes = $notes;
        $this->date_consultation = $date_consultation;
    }

    //  Ajouter un dossier médical
    public function insertFichierMedical($tableName, $conn) {

        $sql = "INSERT INTO $tableName
        (patient_id, medecin_id, diagnostic, traitement, notes, date_consultation)
        VALUES
        ('$this->patient_id', '$this->medecin_id', '$this->diagnostic',
         '$this->traitement', '$this->notes', '$this->date_consultation')";

        if ($conn->query($sql)) {
            self::$successMsg = "Dossier médical ajouté avec succès";
        } else {
            self::$errorMsg = "Erreur : " . $conn->error;
        }
    }

    //  Récupérer tous les dossiers médicaux
    public static function selectAllFichiers($tableName, $conn) {

        $sql = "SELECT * FROM $tableName";
        $result = $conn->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public static function selectByPatient($tableName, $conn, $patient_id) {

    $sql = "SELECT * FROM $tableName
            WHERE patient_id = $patient_id
            ORDER BY date_consultation DESC";

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}
   


}



?>