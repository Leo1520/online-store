<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Cuenta (Usuario)
 */
class Cuenta extends Modelo {
    protected $tabla = 'Cuenta';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene una cuenta por usuario con su rol
     */
    public function obtenerPorUsuario($usuario) {
        $query = "SELECT c.usuario, c.password_hash, c.idRol, c.estado, r.nombre as rol 
                  FROM " . $this->tabla . " c
                  JOIN Rol r ON c.idRol = r.id
                  WHERE c.usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Verifica si un usuario es admin
     */
    public function esAdmin($usuario) {
        $cuenta = $this->obtenerPorUsuario($usuario);
        return $cuenta && $cuenta['rol'] === 'admin';
    }

    /**
     * Verifica si un usuario es trabajador
     */
    public function esTrabajador($usuario) {
        $cuenta = $this->obtenerPorUsuario($usuario);
        return $cuenta && ($cuenta['rol'] === 'trabajador' || $cuenta['rol'] === 'admin');
    }

    /**
     * Crea una nueva cuenta
     * @param $usuario string Nombre de usuario
     * @param $password string Contraseña en texto plano
     * @param $idRol int ID del rol (1=admin, 2=trabajador, 3=cliente)
     */
    public function crear($usuario, $password, $idRol = 3) {
        $query = "INSERT INTO " . $this->tabla . " (usuario, password_hash, idRol) VALUES (?, ?, ?)";
        $passwordHasheada = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ssi", $usuario, $passwordHasheada, $idRol);
        return $stmt->execute();
    }

    /**
     * Actualiza la contraseña
     */
    public function actualizarPassword($usuario, $password) {
        $query = "UPDATE " . $this->tabla . " SET password_hash = ? WHERE usuario = ?";
        $passwordHasheada = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $passwordHasheada, $usuario);
        return $stmt->execute();
    }

    /**
     * Elimina una cuenta
     */
    public function eliminar($usuario) {
        $query = "DELETE FROM " . $this->tabla . " WHERE usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $usuario);
        return $stmt->execute();
    }
}
?>
