<?php
require_once __DIR__ . '/../config/database.php';

class Empleado {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listar(): array {
        $stmt = $this->db->prepare("CALL sp_listar_empleados()");
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

    public function listarTodosInternos(): array {
        $stmt = $this->db->prepare("CALL sp_listar_usuarios_internos()");
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

    public function obtenerPorUsuario(string $usuario): ?array {
        $stmt = $this->db->prepare("CALL sp_obtener_empleado_por_usuario(?)");
        if (!$stmt) return null;
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        $this->limpiar();
        return $row;
    }

    public function crearConCuenta(
        string $usuario, string $passwordHash, string $rol,
        string $ci, string $nombres, string $apPaterno, string $apMaterno,
        string $correo, string $nroCelular, string $cargo
    ): array {
        $stmt = $this->db->prepare("CALL sp_crear_empleado_con_cuenta(?,?,?,?,?,?,?,?,?,?)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar la consulta'];
        $stmt->bind_param("ssssssssss",
            $usuario, $passwordHash, $rol,
            $ci, $nombres, $apPaterno, $apMaterno,
            $correo, $nroCelular, $cargo
        );
        if ($stmt->execute()) {
            $stmt->close();
            $this->limpiar();
            return ['ok' => true];
        }
        $msg = $stmt->error;
        $stmt->close();
        $this->limpiar();
        return ['ok' => false, 'msg' => $msg];
    }

    public function actualizar(
        string $usuario, string $ci, string $nombres,
        string $apPaterno, string $apMaterno, string $correo,
        string $nroCelular, string $cargo, string $rol
    ): bool {
        $stmt = $this->db->prepare("CALL sp_actualizar_empleado(?,?,?,?,?,?,?,?,?)");
        if (!$stmt) return false;
        $stmt->bind_param("sssssssss",
            $usuario, $ci, $nombres, $apPaterno, $apMaterno,
            $correo, $nroCelular, $cargo, $rol
        );
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function actualizarPassword(string $usuario, string $passwordHash): bool {
        $stmt = $this->db->prepare("CALL sp_actualizar_password_empleado(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ss", $usuario, $passwordHash);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function eliminar(string $usuario): array {
        $stmt = $this->db->prepare("CALL sp_eliminar_empleado_y_cuenta(?)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar la consulta'];
        $stmt->bind_param("s", $usuario);
        if ($stmt->execute()) {
            $stmt->close();
            $this->limpiar();
            return ['ok' => true];
        }
        $msg = $stmt->error;
        $stmt->close();
        $this->limpiar();
        return ['ok' => false, 'msg' => $msg];
    }

    private function limpiar(): void {
        while ($this->db->more_results() && $this->db->next_result()) {
            $r = $this->db->use_result();
            if ($r instanceof mysqli_result) $r->free();
        }
    }
}
