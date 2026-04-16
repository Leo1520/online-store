<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de DetalleProductoSucursal (Stock)
 */
class DetalleProductoSucursal extends Modelo {
    protected $tabla = 'DetalleProductoSucursal';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene el stock de un producto en una sucursal
     */
    public function obtenerStock($codProducto, $codSucursal) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE codProducto = ? AND codSucursal = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ii", $codProducto, $codSucursal);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene todos los stocks de un producto
     */
    public function obtenerPorProducto($codProducto) {
        $query = "SELECT dps.*, s.nombre as sucursalNombre, s.direccion, s.nroTelefono
                  FROM " . $this->tabla . " dps
                  LEFT JOIN Sucursal s ON dps.codSucursal = s.cod
                  WHERE dps.codProducto = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $codProducto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene todos los stocks de una sucursal
     */
    public function obtenerPorSucursal($codSucursal) {
        $query = "SELECT dps.*, p.nombre as productoNombre, p.precio
                  FROM " . $this->tabla . " dps
                  LEFT JOIN Producto p ON dps.codProducto = p.cod
                  WHERE dps.codSucursal = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $codSucursal);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Crea un nuevo registro de stock
     */
    public function crear($codProducto, $codSucursal, $stock) {
        $query = "INSERT INTO " . $this->tabla . " (codProducto, codSucursal, stock) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("iii", $codProducto, $codSucursal, $stock);
        return $stmt->execute();
    }

    /**
     * Actualiza el stock
     */
    public function actualizarStock($codProducto, $codSucursal, $stock) {
        $query = "UPDATE " . $this->tabla . " SET stock = ? WHERE codProducto = ? AND codSucursal = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("iii", $stock, $codProducto, $codSucursal);
        return $stmt->execute();
    }

    /**
     * Disminuye el stock
     */
    public function disminuirStock($codProducto, $codSucursal, $cantidad) {
        $query = "UPDATE " . $this->tabla . " SET stock = stock - ? WHERE codProducto = ? AND codSucursal = ? AND stock >= ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("iiii", $cantidad, $codProducto, $codSucursal, $cantidad);
        return $stmt->execute();
    }

    /**
     * Aumenta el stock
     */
    public function aumentarStock($codProducto, $codSucursal, $cantidad) {
        $query = "UPDATE " . $this->tabla . " SET stock = stock + ? WHERE codProducto = ? AND codSucursal = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("iii", $cantidad, $codProducto, $codSucursal);
        return $stmt->execute();
    }

    /**
     * Elimina un registro de stock
     */
    public function eliminar($codProducto, $codSucursal) {
        $query = "DELETE FROM " . $this->tabla . " WHERE codProducto = ? AND codSucursal = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ii", $codProducto, $codSucursal);
        return $stmt->execute();
    }
}
?>
