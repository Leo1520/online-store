<?php
require_once __DIR__ . '/../config/database.php';

class Rol {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listar(): array {
        $stmt = $this->db->prepare("CALL sp_listar_roles()");
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
        $stmt = $this->db->prepare("SELECT cod, nombre, descripcion FROM `Rol` WHERE cod = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $cod);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    public function crear(string $nombre, string $descripcion): bool {
        $stmt = $this->db->prepare("CALL sp_crear_rol(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ss", $nombre, $descripcion);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function actualizar(int $cod, string $nombre, string $descripcion): bool {
        $stmt = $this->db->prepare("CALL sp_actualizar_rol(?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("iss", $cod, $nombre, $descripcion);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function eliminar(int $cod): array {
        $stmt = $this->db->prepare("CALL sp_eliminar_rol(?)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar la consulta'];
        $stmt->bind_param("i", $cod);
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

    public function permisosDeRol(int $codRol): array {
        $stmt = $this->db->prepare("CALL sp_permisos_de_rol(?)");
        if (!$stmt) return [];
        $stmt->bind_param("i", $codRol);
        $stmt->execute();
        $res  = $stmt->get_result();
        $ids  = [];
        if ($res) while ($r = $res->fetch_assoc()) $ids[] = (int)$r['codPermiso'];
        $stmt->close();
        $this->limpiar();
        return $ids;
    }

    public function asignarPermisos(int $codRol, array $codPermisos): bool {
        // Limpiar permisos anteriores
        $s1 = $this->db->prepare("CALL sp_limpiar_permisos_rol(?)");
        if (!$s1) return false;
        $s1->bind_param("i", $codRol);
        $s1->execute();
        $s1->close();
        $this->limpiar();

        // Insertar los nuevos
        if (!empty($codPermisos)) {
            $s2 = $this->db->prepare("CALL sp_agregar_permiso_a_rol(?, ?)");
            if (!$s2) return false;
            foreach ($codPermisos as $cp) {
                $cp = (int)$cp;
                $s2->bind_param("ii", $codRol, $cp);
                $s2->execute();
                $this->limpiar();
            }
            $s2->close();
        }
        return true;
    }

    public function listarCuentas(): array {
        $stmt = $this->db->prepare("CALL sp_listar_cuentas_con_rol()");
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

    public function cambiarRolCuenta(string $usuario, string $rol): bool {
        $stmt = $this->db->prepare("CALL sp_cambiar_rol_cuenta(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ss", $usuario, $rol);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiar();
        return $ok;
    }

    public function permisosPorRolNombre(string $rol): array {
        $stmt = $this->db->prepare(
            "SELECT p.nombre FROM Permiso p
             JOIN RolPermiso rp ON rp.codPermiso = p.cod
             JOIN Rol r ON r.cod = rp.codRol
             WHERE r.nombre = ?"
        );
        if (!$stmt) return [];
        $stmt->bind_param("s", $rol);
        $stmt->execute();
        $res      = $stmt->get_result();
        $permisos = [];
        if ($res) while ($row = $res->fetch_row()) $permisos[] = $row[0];
        $stmt->close();
        $this->limpiar();
        return $permisos;
    }

    private function limpiar(): void {
        while ($this->db->more_results() && $this->db->next_result()) {
            $r = $this->db->use_result();
            if ($r instanceof mysqli_result) $r->free();
        }
    }
}
