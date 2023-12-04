<?php
require_once "ConDB.php";

class UserModel {
    public static function createUser($data) {
        $cantMail = self::getMail($data["use_mail"]);

        if ($cantMail == 0) {
            $query = "INSERT INTO users(use_mail, use_pss, use_dateCreate, us_identifier, us_key, us_status)
                      VALUES (:use_mail, :use_pss, :use_dateCreate, :us_identifier, :us_key, :us_status)";
            $status = "0";
            $stament = Conection::connection()->prepare($query);
            $stament->bindParam(":use_mail", $data["use_mail"], PDO::PARAM_STR);
            $stament->bindParam(":use_pss", $data["use_pss"], PDO::PARAM_STR);
            $stament->bindParam(":use_dateCreate", $data["use_dateCreate"], PDO::PARAM_STR);
            $stament->bindParam(":us_identifier", $data["us_identifier"], PDO::PARAM_STR);
            $stament->bindParam(":us_key", $data["us_key"], PDO::PARAM_STR);
            $stament->bindParam(":us_status", $status, PDO::PARAM_STR);

            $message = $stament->execute() ? "ok" : Conection::connection()->errorInfo();

            $stament->closeCursor();
            $stament = null;

        } else {
            $message = "Usuario ya está registrado";
        }

        return $message;
    }

    private static function getMail($mail) {
        $query = "SELECT use_mail FROM users WHERE use_mail = :mail";
        $stament = Conection::connection()->prepare($query);
        $stament->bindParam(":mail", $mail, PDO::PARAM_STR);
        $stament->execute();

        $result = $stament->rowCount();

        return $result;
    }

    public static function getUsers($id) {
        $id = is_numeric($id) ? $id : 0;
        $query = "SELECT use_id, use_mail, use_dateCreate FROM users";
        $query .= ($id > 0) ? " WHERE users.use_id = :id AND us_status='1';" : " WHERE us_status = '1';";

        $stament = Conection::connection()->prepare($query);
        $stament->bindParam(":id", $id, PDO::PARAM_INT);
        $stament->execute();

        $result = $stament->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public static function login($data) {
        $user = $data['use_mail'];
        $pss = md5($data['use_pss']);

        if (!empty($user) && !empty($pss)) {
            $query = "SELECT us_identifier, us_key, use_id FROM users
                      WHERE use_mail = :user AND use_pss = :pss AND us_status = '1'";
            $stament = Conection::connection()->prepare($query);
            $stament->bindParam(":user", $user, PDO::PARAM_STR);
            $stament->bindParam(":pss", $pss, PDO::PARAM_STR);
            $stament->execute();

            $result = $stament->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } else {
            $mensaje = array(
                "COD" => "001",
                "MENSAJE" => "Error en credenciales"
            );

            return $mensaje;
        }
    }
    
    //Credenciales 

    public static function getUserAuth() {
        $query = "SELECT us_identifier, us_key FROM users WHERE us_status = '1'";
        $stament = Conection::connection()->prepare($query);
        $stament->execute();

        $result = $stament->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    // metodo de activacion
    public static function activateUser($userId) {
        $query = "UPDATE users SET us_status = '1' WHERE use_id = :user_id";
        $stament = Conection::connection()->prepare($query);
        $stament->bindParam(":use_id", $userId["use_id"], PDO::PARAM_INT);

        $message = $stament->execute() ? "Usuario activado correctamente" : Conection::connection()->errorInfo();

        $stament->closeCursor();
        $stament = null;

        return $message;
    }

    // Borrar Usuario

    public static function deleteUser($userId) {
        $query="";
        $query = "DELETE FROM users WHERE use_id = :use_id";
        $stament = Conection::connection()->prepare($query);
        $stament->bindParam(":use_id", $userId["use_id"], PDO::PARAM_INT);
    
        $message = $stament->execute() ? "Usuario eliminado correctamente" : Conection::connection()->errorInfo();
        $stament->closeCursor();
        $stament = null;
        return $message;
    }

    //metodo actualizar
    public static function updateUser($data) {
        $query="";
        $query = "UPDATE users 
                  SET use_mail = :use_mail, 
                      use_pss = :use_pss 
                  WHERE use_id = :use_id";

        $stament = Conection::connection()->prepare($query);
        $stament->bindParam(":use_mail", $data["use_mail"], PDO::PARAM_STR);
        $stament->bindParam(":use_pss", $data["use_pss"], PDO::PARAM_STR);
        $stament->bindParam(":use_id", $data["use_id"], PDO::PARAM_INT);
        $message = $stament->execute() ? "Usuario actualizado correctamente" : Conection::connection()->errorInfo();
        $stament->closeCursor();
        $stament = null;
        $query="";
        return $message;
    }
}
?>

