<?php
require_once __DIR__ . '/../config/database.php';

class NotaVenta {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodasConResumen() {
        $stmtSp = $this->db->prepare("CALL sp_resumen_ventas()");
        if ($stmtSp) {
            $ok = $stmtSp->execute();
            if ($ok) {
                $resultado = $stmtSp->get_result();
                $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
                $stmtSp->close();
                $this->limpiarResultadosPendientes();
                return $datos;
            }
            $stmtSp->close();
            $this->limpiarResultadosPendientes();
        }

        $sql = "SELECT nv.nro, nv.fechaHora, nv.ciCliente, nv.estado,
                       CONCAT(cl.nombres, ' ', cl.apPaterno, ' ', cl.apMaterno) AS cliente,
                       COALESCE(SUM(dnv.cant), 0) AS totalItems,
                       COALESCE(SUM(dnv.cant * p.precioVigente), 0) AS totalMonto
                FROM `NotaVenta` nv
                INNER JOIN `Cliente` cl ON cl.ci = nv.ciCliente
                LEFT JOIN `DetalleNotaVenta` dnv ON dnv.nroNotaVenta = nv.nro
                LEFT JOIN `Producto` p ON p.cod = dnv.codProducto
                GROUP BY nv.nro, nv.fechaHora, nv.ciCliente, nv.estado, cl.nombres, cl.apPaterno, cl.apMaterno
                ORDER BY nv.nro DESC";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerHistorialCliente($usuario) {
        $stmt = $this->db->prepare("CALL sp_historial_compras_cliente(?)");
        if (!$stmt) return [];
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function obtenerClientePorUsuario($usuario) {
        $stmt = $this->db->prepare("CALL sp_obtener_cliente_por_usuario(?)");
        if (!$stmt) return null;
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = ($resultado && $resultado->num_rows > 0) ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $dato;
    }

    public function actualizarPerfilCliente($ci, $usuario, $correo, $direccion, $nroCelular) {
        $stmt = $this->db->prepare("CALL sp_actualizar_perfil_cliente(?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('sssss', $ci, $usuario, $correo, $direccion, $nroCelular);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function actualizarEstado($nro, $estado) {
        $stmt = $this->db->prepare("CALL sp_actualizar_estado_venta(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('is', $nro, $estado);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function obtenerDetallesPorNota($nroNota) {
        $nroNota = (int)$nroNota;

        $stmtSp = $this->db->prepare("CALL sp_detalle_venta(?)");
        if ($stmtSp) {
            $stmtSp->bind_param('i', $nroNota);
            $ok = $stmtSp->execute();
            if ($ok) {
                $resultado = $stmtSp->get_result();
                $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
                $stmtSp->close();
                $this->limpiarResultadosPendientes();
                return $datos;
            }
            $stmtSp->close();
            $this->limpiarResultadosPendientes();
        }

        $sql = "SELECT dnv.nroNotaVenta, dnv.item, dnv.cant, dnv.codProducto, p.nombre AS producto, p.precioVigente AS precio
                FROM `DetalleNotaVenta` dnv
                INNER JOIN `Producto` p ON p.cod = dnv.codProducto
                WHERE dnv.nroNotaVenta = ?
                ORDER BY dnv.item ASC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $nroNota);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
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
