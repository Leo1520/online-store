<?php
/**
 * Clase de Configuración de Base de Datos
 * Maneja la conexión a MySQL
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'mydb';
    private $usuario = 'root';
    private $password = '';
    private $conexion;

    public function conectar() {
        $this->conexion = new mysqli(
            $this->host,
            $this->usuario,
            $this->password,
            $this->db_name
        );

        // Verificar conexión
        if ($this->conexion->connect_error) {
            die('Error de conexión: ' . $this->conexion->connect_error);
        }

        // Establecer charset a UTF-8
        $this->conexion->set_charset('utf8mb4');

        return $this->conexion;
    }

    public function obtenerConexion() {
        return $this->conexion;
    }

    public function cerrar() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
