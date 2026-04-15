<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function verificarCredenciales($usuario, $password) {
        $usuario  = $this->db->real_escape_string($usuario);
        $password = $this->db->real_escape_string($password);
        $resultado = $this->db->query(
            "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'"
        );
        return $resultado->num_rows == 1;
    }
}
