<?php
/**
 * Clase de Utilidades y Funciones Auxiliares
 */
class Utilidades {
    
    /**
     * Protege contra inyección SQL
     */
    public static function sanitizar($entrada) {
        return htmlspecialchars(trim($entrada), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida un email
     */
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Valida contraseña (mínimo 6 caracteres)
     */
    public static function validarPassword($password) {
        return strlen($password) >= 6;
    }

    /**
     * Hashea una contraseña
     */
    public static function hashearPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifica una contraseña
     */
    public static function verificarPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Redirige a una URL
     */
    public static function redirigir($url) {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Retorna el valor GET o POST
     */
    public static function obtenerDato($clave, $default = '') {
        if (isset($_POST[$clave])) {
            return self::sanitizar($_POST[$clave]);
        } elseif (isset($_GET[$clave])) {
            return self::sanitizar($_GET[$clave]);
        }
        return $default;
    }

    /**
     * Verifica si existe una sesión
     */
    public static function verificarSesion($sesion) {
        return isset($_SESSION[$sesion]);
    }

    /**
     * Obtiene el valor de sesión
     */
    public static function obtenerSesion($sesion, $default = null) {
        return $_SESSION[$sesion] ?? $default;
    }

    /**
     * Establece un valor en sesión
     */
    public static function establecerSesion($sesion, $valor) {
        $_SESSION[$sesion] = $valor;
    }

    /**
     * Destruye una sesión
     */
    public static function destruirSesion($sesion) {
        if (isset($_SESSION[$sesion])) {
            unset($_SESSION[$sesion]);
        }
    }

    /**
     * Calcula el total con impuesto
     */
    public static function calcularTotal($subtotal, $impuesto = 0.13) {
        return $subtotal + ($subtotal * $impuesto);
    }

    /**
     * Formatea dinero
     */
    public static function formatearDinero($cantidad, $moneda = 'Bs') {
        return $moneda . ' ' . number_format($cantidad, 2, '.', ',');
    }

    /**
     * Obtiene la fecha actual formateada
     */
    public static function fechaActual($formato = 'Y-m-d H:i:s') {
        return date($formato);
    }
}
?>
