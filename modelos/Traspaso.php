<?php
require_once __DIR__ . '/../config/database.php';

class Traspaso {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        $stmt = $this->db->prepare("CALL sp_listar_traspasos()");
        if (!$stmt) return [];
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiar();
        return $data;
    }

    public function obtenerDetalle($nro) {
        $nro  = (int)$nro;
        $stmt = $this->db->prepare("CALL sp_detalle_traspaso(?)");
        if (!$stmt) return [];
        $stmt->bind_param('i', $nro);
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiar();
        return $data;
    }

    public function crear($codOrigen, $codDestino, $observacion, $usuario) {
        $stmt = $this->db->prepare("CALL sp_crear_traspaso(?, ?, ?, ?, @p_nro)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar'];
        $stmt->bind_param('iiss', $codOrigen, $codDestino, $observacion, $usuario);
        $ok  = $stmt->execute();
        $err = $stmt->error;
        $stmt->close();
        $this->limpiar();
        if (!$ok) return ['ok' => false, 'msg' => $err ?: 'Error al crear traspaso'];
        $res = $this->db->query("SELECT @p_nro AS nro");
        $nro = $res ? (int)$res->fetch_assoc()['nro'] : 0;
        return ['ok' => true, 'nro' => $nro];
    }

    public function agregarDetalle($nroTraspaso, $codProducto, $cantidad) {
        $stmt = $this->db->prepare("CALL sp_agregar_detalle_traspaso(?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('iii', $nroTraspaso, $codProducto, $cantidad);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function completar($nro, $usuario) {
        $nro  = (int)$nro;
        $stmt = $this->db->prepare("CALL sp_completar_traspaso(?, ?)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar'];
        $stmt->bind_param('is', $nro, $usuario);
        $ok  = $stmt->execute();
        $err = $stmt->error;
        $stmt->close();
        $this->limpiar();
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => $err ?: 'Error al completar traspaso'];
    }

    public function cancelar($nro) {
        $nro  = (int)$nro;
        $stmt = $this->db->prepare("CALL sp_cancelar_traspaso(?)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar'];
        $stmt->bind_param('i', $nro);
        $ok  = $stmt->execute();
        $err = $stmt->error;
        $stmt->close();
        $this->limpiar();
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => $err ?: 'Error al cancelar traspaso'];
    }

    private function limpiar() {
        while ($this->db->more_results() && $this->db->next_result()) {
            $r = $this->db->use_result();
            if ($r instanceof mysqli_result) $r->free();
        }
    }
}
