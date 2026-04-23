<?php
require_once __DIR__ . '/../config/database.php';

class Venta {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function registrarVenta($carrito, $usuarioSesion) {
        if (empty($carrito) || empty($usuarioSesion)) {
            return false;
        }

        $ci = $this->obtenerCiClientePorUsuario($usuarioSesion);
        if (!$ci) {
            return false;
        }

        $this->db->begin_transaction();

        try {
            $nroVenta = $this->crearNotaVenta($ci);

            $item = 1;
            foreach ($carrito as $linea) {
                $codProducto = isset($linea['id_producto']) ? (int)$linea['id_producto'] : 0;
                $cantidad    = isset($linea['cantidad'])    ? (int)$linea['cantidad']    : 0;

                if ($codProducto <= 0 || $cantidad <= 0) {
                    continue;
                }

                if (!$this->existeProducto($codProducto)) {
                    continue;
                }

                if (!$this->insertarDetalleVenta($nroVenta, $codProducto, $item, $cantidad)) {
                    throw new Exception('No se pudo registrar el detalle de venta.');
                }

                if (!$this->descontarStock($codProducto, $cantidad, $nroVenta, $usuarioSesion)) {
                    throw new Exception('Stock insuficiente para el producto ' . $codProducto . '.');
                }

                $item++;
            }

            if ($item === 1) {
                throw new Exception('No hay productos validos para registrar.');
            }

            $this->db->commit();
            return $nroVenta;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    private function obtenerCiClientePorUsuario($usuario) {
        $stmt = $this->db->prepare("CALL sp_obtener_ci_cliente_por_usuario(?)");
        if (!$stmt) return null;
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $fila ? $fila['ci'] : null;
    }

    private function existeProducto($codProducto) {
        $stmt = $this->db->prepare("CALL sp_existe_producto(?)");
        if (!$stmt) return false;
        $stmt->bind_param('i', $codProducto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $fila && (int)$fila['existe'] > 0;
    }

    private function crearNotaVenta($ciCliente) {
        $stmt = $this->db->prepare("CALL sp_crear_nota_venta(?, @p_nro_venta)");
        if (!$stmt) throw new Exception('No se pudo preparar la nota de venta.');
        $stmt->bind_param('s', $ciCliente);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();

        if (!$ok) throw new Exception('No se pudo registrar la nota de venta.');

        $resultado = $this->db->query("SELECT @p_nro_venta AS nro");
        if (!$resultado) throw new Exception('No se pudo obtener el numero de venta.');
        $fila = $resultado->fetch_assoc();
        $nro  = isset($fila['nro']) ? (int)$fila['nro'] : 0;

        if ($nro <= 0) throw new Exception('Numero de venta invalido.');
        return $nro;
    }

    private function descontarStock($codProducto, $cantidad, $nroVenta, $usuario) {
        $stmt = $this->db->prepare("CALL sp_descontar_stock_producto(?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('iiis', $codProducto, $cantidad, $nroVenta, $usuario);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    private function insertarDetalleVenta($nroVenta, $codProducto, $item, $cantidad) {
        $stmt = $this->db->prepare("CALL sp_insertar_detalle_venta(?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('iiii', $nroVenta, $codProducto, $item, $cantidad);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    private function limpiarResultadosPendientes() {
        while ($this->db->more_results() && $this->db->next_result()) {
            $resultado = $this->db->use_result();
            if ($resultado instanceof mysqli_result) {
                $resultado->free();
            }
        }
    }
}
