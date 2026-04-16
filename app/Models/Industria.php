<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Industria
 */
class Industria extends Modelo {
    protected $tabla = 'Industria';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene una industria por ID
     */
    public function obtenerPorId($cod) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Crea una nueva industria
     */
    public function crear($nombre) {
        $query = "INSERT INTO " . $this->tabla . " (nombre) VALUES (?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $nombre);
        return $stmt->execute();
    }

    /**
     * Actualiza una industria
     */
    public function actualizar($cod, $nombre) {
        $query = "UPDATE " . $this->tabla . " SET nombre = ? WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("si", $nombre, $cod);
        return $stmt->execute();
    }

    /**
     * Elimina una industria
     */
    public function eliminar($cod) {
        $query = "DELETE FROM " . $this->tabla . " WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        return $stmt->execute();
    }
}
?>
