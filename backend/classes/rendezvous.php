<?php
class RendezVous{
    public $id;
    public $patient_id;
    public $medecin_id;
    public $date_rdv;
    public $heure_rdv;
    public $statut;
    public $motif;
    
    public static $errorMsg = "";
    public static $successMsg = "";

    public function __construct($patient_id, $medecin_id, $date_rdv, $heure_rdv, $motif = "", $statut = "en_attente"){
        $this->patient_id = $patient_id;
        $this->medecin_id = $medecin_id;
        $this->date_rdv = $date_rdv;
        $this->heure_rdv = $heure_rdv;
        $this->motif = $motif;
        $this->statut = $statut;
    }

    public function insertRendezVous($tableName, $conn){
        // Vérifier si le créneau est disponible
        if(!self::isCreneauDisponible($conn, $this->medecin_id, $this->date_rdv, $this->heure_rdv)){
            self::$errorMsg = "Ce créneau horaire n'est pas disponible";
            return false;
        }

        $sql = "INSERT INTO $tableName (patient_id, medecin_id, date_rdv, heure_rdv, statut, motif) 
                VALUES ('$this->patient_id', '$this->medecin_id', '$this->date_rdv', '$this->heure_rdv', '$this->statut', '$this->motif')";
        
        if(mysqli_query($conn, $sql)){
            self::$successMsg = "Rendez-vous pris avec succès!";
            return true;
        } else {
            self::$errorMsg = "Erreur: " . mysqli_error($conn);
            return false;
        }
    }

    public static function selectAllRendezVous($tableName, $conn){
        $sql = "SELECT r.*, 
                p.nom as patient_nom, p.prenom as patient_prenom,
                m.nom as medecin_nom, m.prenom as medecin_prenom, m.specialite
                FROM $tableName r
                JOIN patients p ON r.patient_id = p.id
                JOIN medecins m ON r.medecin_id = m.id
                ORDER BY r.date_rdv DESC, r.heure_rdv DESC";
        
        $result = mysqli_query($conn, $sql);
        $data = [];
        
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $data[] = $row;
            }
        }
        return $data;
    }

    static function selectRendezVousByPatient($tableName, $conn, $patient_id){
        $sql = "SELECT r.*, 
                m.nom as medecin_nom, m.prenom as medecin_prenom, m.specialite, m.telephone
                FROM $tableName r
                JOIN medecins m ON r.medecin_id = m.id
                WHERE r.patient_id = $patient_id
                ORDER BY r.date_rdv DESC, r.heure_rdv DESC";
        
        $result = mysqli_query($conn, $sql);
        $data = [];
        
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $data[] = $row;
            }
        }
        return $data;
    }

    static function selectRendezVousByMedecin($tableName, $conn, $medecin_id){
        $sql = "SELECT r.*, 
                p.nom as patient_nom, p.prenom as patient_prenom, p.telephone
                FROM $tableName r
                JOIN patients p ON r.patient_id = p.id
                WHERE r.medecin_id = $medecin_id
                ORDER BY r.date_rdv DESC, r.heure_rdv DESC";
        
        $result = mysqli_query($conn, $sql);
        $data = [];
        
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $data[] = $row;
            }
        }
        return $data;
    }

    static function deleteRendezVous($tableName, $conn, $id){
        $sql = "DELETE FROM $tableName WHERE id = $id";
        
        if(mysqli_query($conn, $sql)){
            self::$successMsg = "Rendez-vous annulé";
            return true;
        }
        return false;
    }

    static function updateStatut($tableName, $conn, $id, $statut){
        $sql = "UPDATE $tableName SET statut = '$statut' WHERE id = $id";
        
        if(mysqli_query($conn, $sql)){
            self::$successMsg = "Statut mis à jour";
            return true;
        }
        return false;
    }

    static function isCreneauDisponible($conn, $medecin_id, $date_rdv, $heure_rdv){
        $sql = "SELECT * FROM rendez_vous 
                WHERE medecin_id = $medecin_id 
                AND date_rdv = '$date_rdv' 
                AND heure_rdv = '$heure_rdv'
                AND statut != 'annule'";
        
        $result = mysqli_query($conn, $sql);
        return mysqli_num_rows($result) == 0;
    }
}
?>