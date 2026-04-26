<?php
require_once __DIR__ . '/../config/database.php';

class Permiso {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listar(): array {
        $stmt = $this->db->prepare("CALL sp_listar_permisos()");
        if ($stmt && $stmt->execute()) {
            $res  = $stmt->get_result();
            $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
            $this->limpiar();
            return $data;
        }
        $this->limpiar();
        return [];
    }

    public function obtenerPorCod(int $cod): ?array {
        $stmt = $this->db->prepare("SELECT cod, nombre, descripcion, modulo FROM `Permiso` WHERE cod = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $cod);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    public function crear(string $nombre, string $descripcion, string $modulo): bool {
        $stmt = $this->db->prepare("CALL sp_crear_permiso(?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("sss", $nombre, $descripcion, $modulo);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function actualizar(int $cod, string $nombre, string $descripcion, string $modulo): bool {
        $stmt = $this->db->prepare("CALL sp_actualizar_permiso(?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("isss", $cod, $nombre, $descripcion, $modulo);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function eliminar(int $cod): bool {
        $stmt = $this->db->prepare("CALL sp_eliminar_permiso(?)");
        if (!$stmt) return false;
        $stmt->bind_param("i", $cod);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    private function limpiar(): void {
        while ($this->db->more_results() && $this->db->next_result()) {
            $r = $this->db->use_result();
            if ($r instanceof mysqli_result) $r->free();
        }
    }
}
