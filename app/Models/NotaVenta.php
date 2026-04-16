<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de NotaVenta (Pedidos)
 */
class NotaVenta extends Modelo {
    protected $tabla = 'NotaVenta';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene una nota de venta por número
     */
    public function obtenerPorNro($nro) {
        $query = "SELECT nv.*, c.nombres, c.apPaterno, c.apMaterno, c.correo, c.direccion 
                  FROM " . $this->tabla . " nv
                  LEFT JOIN Cliente c ON nv.ciCliente = c.ci
                  WHERE nv.nro = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $nro);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene notas de venta por cliente
     */
    public function obtenerPorCliente($ciCliente) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE ciCliente = ? ORDER BY fechaHora DESC";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $ciCliente);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Crea una nueva nota de venta
     */
    public function crear($ciCliente) {
        $fechaHora = date('Y-m-d H:i:s');
        $query = "INSERT INTO " . $this->tabla . " (fechaHora, ciCliente) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaHora, $ciCliente);
        return $stmt->execute();
    }

    /**
     * Obtiene todas las notas de venta
     */
    public function obtenerTodos() {
        $query = "SELECT nv.*, c.nombres, c.apPaterno, c.apMaterno, c.correo 
                  FROM " . $this->tabla . " nv
                  LEFT JOIN Cliente c ON nv.ciCliente = c.ci
                  ORDER BY nv.fechaHora DESC";
        $resultado = $this->conexion->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
