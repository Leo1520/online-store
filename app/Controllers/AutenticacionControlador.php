<?php
require_once __DIR__ . '/Controlador.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../../config/Utilidades.php';

/**
 * Controlador de Autenticación
 */
class AutenticacionControlador extends Controlador {

    /**
     * Muestra el formulario de login
     */
    public function mostrarLogin() {
        if (isset($_SESSION['usuario'])) {
            $this->redirigir('index.php');
        }
        
        $this->cargarVista('Autenticacion/login');
    }

    /**
     * Procesa el login
     */
    public function iniciarSesion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?accion=mostrarLogin');
        }

        $usuario = Utilidades::obtenerDato('usuario');
        $password = Utilidades::obtenerDato('password');

        if (empty($usuario) || empty($password)) {
            $_SESSION['error'] = 'Usuario y contraseña son requeridos.';
            $this->redirigir('?accion=mostrarLogin');
        }

        $modeloCuenta = new Cuenta($this->conexion);
        $cuenta = $modeloCuenta->obtenerPorUsuario($usuario);

        if ($cuenta && Utilidades::verificarPassword($password, $cuenta['password_hash'])) {
            // Verificar si la cuenta está activa
            if ($cuenta['estado'] !== 'activo') {
                $_SESSION['error'] = 'Tu cuenta ha sido desactivada.';
                $this->redirigir('?accion=mostrarLogin');
            }

            Utilidades::establecerSesion('usuario', $usuario);
            Utilidades::establecerSesion('rol', $cuenta['rol']);
            
            // Si es cliente, obtener sus datos
            if ($cuenta['rol'] === 'cliente') {
                $modeloCliente = new Cliente($this->conexion);
                $cliente = $modeloCliente->obtenerPorUsuario($usuario);
                if ($cliente) {
                    Utilidades::establecerSesion('cliente', $cliente);
                }
            }

            $_SESSION['mensaje'] = '¡Bienvenido ' . htmlspecialchars($usuario) . '!';
            
            // Redirigir según el rol
            if ($cuenta['rol'] === 'admin' || $cuenta['rol'] === 'trabajador') {
                $this->redirigir('?controlador=admin&accion=panel');
            } else {
                $this->redirigir('?controlador=productos&accion=listar');
            }
        } else {
            $_SESSION['error'] = 'Usuario o contraseña incorrectos.';
            $this->redirigir('?accion=mostrarLogin');
        }
    }

    /**
     * Muestra el formulario de registro
     */
    public function mostrarRegistro() {
        if (isset($_SESSION['usuario'])) {
            $this->redirigir('index.php');
        }
        
        $this->cargarVista('Autenticacion/registro');
    }

    /**
     * Procesa el registro
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?accion=registro');
        }

        $usuario = Utilidades::obtenerDato('usuario');
        $password = Utilidades::obtenerDato('password');
        $confirmar_password = Utilidades::obtenerDato('confirmar_password');
        
        $ci = Utilidades::obtenerDato('ci');
        $nombres = Utilidades::obtenerDato('nombres');
        $apPaterno = Utilidades::obtenerDato('apPaterno');
        $apMaterno = Utilidades::obtenerDato('apMaterno');
        $correo = Utilidades::obtenerDato('correo');
        $direccion = Utilidades::obtenerDato('direccion');
        $nroCelular = Utilidades::obtenerDato('nroCelular');

        // Validaciones
        if (empty($usuario) || empty($password) || empty($ci) || empty($nombres)) {
            $_SESSION['error'] = 'Todos los campos son requeridos.';
            $this->redirigir('?accion=registro');
        }

        if ($password !== $confirmar_password) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            $this->redirigir('?accion=registro');
        }

        if (!Utilidades::validarEmail($correo)) {
            $_SESSION['error'] = 'Email inválido.';
            $this->redirigir('?accion=registro');
        }

        if (!Utilidades::validarPassword($password)) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
            $this->redirigir('?accion=registro');
        }

        $modeloCuenta = new Cuenta($this->conexion);
        $cuentaExistente = $modeloCuenta->obtenerPorUsuario($usuario);

        if ($cuentaExistente) {
            $_SESSION['error'] = 'El usuario ya existe.';
            $this->redirigir('?accion=registro');
        }

        $modeloCliente = new Cliente($this->conexion);
        $clienteExistente = $modeloCliente->obtenerPorCi($ci);

        if ($clienteExistente) {
            $_SESSION['error'] = 'El CI ya está registrado.';
            $this->redirigir('?accion=registro');
        }

        // Crear la cuenta
        if (!$modeloCuenta->crear($usuario, $password)) {
            $_SESSION['error'] = 'Error al crear la cuenta.';
            $this->redirigir('?accion=registro');
        }

        // Crear el cliente
        if (!$modeloCliente->crear($ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $usuario)) {
            $_SESSION['error'] = 'Error al registrar el cliente.';
            $this->redirigir('?accion=registro');
        }

        $_SESSION['mensaje'] = 'Registro exitoso. Por favor inicia sesión.';
        $this->redirigir('?accion=login');
    }

    /**
     * Cierra sesión
     */
    public function cerrarSesion() {
        session_destroy();
        $this->redirigir('index.php');
    }

    /**
     * Muestra el perfil del usuario
     */
    public function perfil() {
        $this->verificarAutenticacion();
        
        $usuario = Utilidades::obtenerSesion('usuario');
        $modeloCliente = new Cliente($this->conexion);
        $cliente = $modeloCliente->obtenerPorUsuario($usuario);

        $this->cargarVista('Autenticacion/perfil', [
            'cliente' => $cliente
        ]);
    }

    /**
     * Actualiza el perfil del usuario
     */
    public function actualizarPerfil() {
        $this->verificarAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?accion=perfil');
        }

        $usuario = Utilidades::obtenerSesion('usuario');
        $ci = Utilidades::obtenerDato('ci');
        $nombres = Utilidades::obtenerDato('nombres');
        $apPaterno = Utilidades::obtenerDato('apPaterno');
        $apMaterno = Utilidades::obtenerDato('apMaterno');
        $correo = Utilidades::obtenerDato('correo');
        $direccion = Utilidades::obtenerDato('direccion');
        $nroCelular = Utilidades::obtenerDato('nroCelular');

        if (!Utilidades::validarEmail($correo)) {
            $_SESSION['error'] = 'Email inválido.';
            $this->redirigir('?accion=perfil');
        }

        $modeloCliente = new Cliente($this->conexion);
        if ($modeloCliente->actualizar($ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular)) {
            $_SESSION['mensaje'] = 'Perfil actualizado exitosamente.';
            // Actualizar sesión
            $cliente = $modeloCliente->obtenerPorCi($ci);
            Utilidades::establecerSesion('cliente', $cliente);
        } else {
            $_SESSION['error'] = 'Error al actualizar el perfil.';
        }

        $this->redirigir('?accion=perfil');
    }
}
?>
