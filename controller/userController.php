<?php

class UserController {
    private $_method;
    private $_complement;
    private $_data;

    public function __construct($_method, $_complement, $_data) {
        $this->_method = $_method;
        $this->_complement = $_complement ?? 0;
        $this->_data = $_data !== 0 ? $_data : "";
    }

    public function index() {
        switch ($this->_method) {
            case "GET":
                $user = $this->_complement == 0 ? UserModel::getUsers(0) : UserModel::getUsers($this->_complement);
                $json = $user;
                break;
            case "POST":
                $createUser = UserModel::createUser($this->generateSalting());
                $json = ["response" => $createUser];
                break;
            case "PUT":
                $updateUser = UserModel::updateUser($this->generateSalting());
                $json = ["responses" => $updateUser];
                break;
                
            case "PATCH":
                $activateUser = UserModel::activateUser($this->_data);
                $json = ["ruta" => $activateUser];
                break;
                
            case "DELETE":
                $deleteUser = UserModel::deleteUser($this->_data);
                $json = ["ruta" => $deleteUser];
                break;
            
            default:
                $json = ["ruta" => "not found"];
        }

        echo json_encode($json, true);
        return;
    }

    private function generateSalting() {
        if ($this->_data !== "") {
            $trimmedData = array_map('trim', $this->_data);
            $trimmedData['use_pss'] = md5($trimmedData['use_pss']);
            $identifier = str_replace("$", "ue3", crypt($trimmedData['use_mail'], 'ue56'));
            $key = str_replace("$", "2023", crypt($trimmedData['use_mail'], '56ue'));
            $trimmedData['us_identifier'] = $identifier;
            $trimmedData['us_key'] = $key;
            return $trimmedData;
        }

        return null;
    }
}

?>
