<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Marca
 */
class Marca extends Modelo {
    protected $tabla = 'Marca';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene una marca por ID
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
     * Crea una nueva marca
     */
    public function crear($nombre) {
        $query = "INSERT INTO " . $this->tabla . " (nombre) VALUES (?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $nombre);
        return $stmt->execute();
    }

    /**
     * Actualiza una marca
     */
    public function actualizar($cod, $nombre) {
        $query = "UPDATE " . $this->tabla . " SET nombre = ? WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("si", $nombre, $cod);
        return $stmt->execute();
    }

    /**
     * Elimina una marca
     */
    public function eliminar($cod) {
        $query = "DELETE FROM " . $this->tabla . " WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        return $stmt->execute();
    }
}
?>
