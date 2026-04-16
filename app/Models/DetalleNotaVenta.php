<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de DetalleNotaVenta
 */
class DetalleNotaVenta extends Modelo {
    protected $tabla = 'DetalleNotaVenta';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene detalles de una nota de venta
     */
    public function obtenerPorNotaVenta($nroNotaVenta) {
        $query = "SELECT dnv.*, p.nombre, p.precio, p.imagen
                  FROM " . $this->tabla . " dnv
                  LEFT JOIN Producto p ON dnv.codProducto = p.cod
                  WHERE dnv.nroNotaVenta = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $nroNotaVenta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Añade un detalle a la nota de venta
     */
    public function crear($nroNotaVenta, $codProducto, $cant) {
        $query = "INSERT INTO " . $this->tabla . " (nroNotaVenta, codProducto, cant) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("iii", $nroNotaVenta, $codProducto, $cant);
        return $stmt->execute();
    }

    /**
     * Actualiza la cantidad
     */
    public function actualizarCantidad($item, $cant) {
        $query = "UPDATE " . $this->tabla . " SET cant = ? WHERE item = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ii", $cant, $item);
        return $stmt->execute();
    }

    /**
     * Elimina un detalle
     */
    public function eliminar($item) {
        $query = "DELETE FROM " . $this->tabla . " WHERE item = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $item);
        return $stmt->execute();
    }

    /**
     * Obtiene el total de una nota de venta
     */
    public function obtenerTotal($nroNotaVenta) {
        $query = "SELECT SUM(dnv.cant * p.precio) as total
                  FROM " . $this->tabla . " dnv
                  LEFT JOIN Producto p ON dnv.codProducto = p.cod
                  WHERE dnv.nroNotaVenta = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $nroNotaVenta);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['total'] ?? 0;
    }
}
?>
