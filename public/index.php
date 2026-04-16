<?php
/**
 * ARCHIVO DE ENRUTAMIENTO PRINCIPAL (INDEX)
 * Punto de entrada a la aplicación MVC
 */

session_start();

// Incluir configuración y utilidades
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Utilidades.php';

// Instanciar la base de datos
$db = new Database();
$conexion = $db->conectar();

// Obtener parámetros de la URL
$controlador = isset($_GET['controlador']) ? Utilidades::sanitizar($_GET['controlador']) : 'productos';
$accion = isset($_GET['accion']) ? Utilidades::sanitizar($_GET['accion']) : 'listar';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Mapeo de controladores
$controladores = [
    'productos' => 'ProductoControlador',
    'autenticacion' => 'AutenticacionControlador',
    'carrito' => 'CarritoControlador',
    'pago' => 'PagoControlador',
    'admin' => 'AdminControlador'
];

// Validar controlador
if (!isset($controladores[$controlador])) {
    $controlador = 'productos';
}

// Incluir el controlador
$nombreClase = $controladores[$controlador];
require_once __DIR__ . '/../app/Controllers/' . $nombreClase . '.php';

// Instanciar el controlador
$controladorObj = new $nombreClase($conexion);

// Llamar a la acción
if ($id !== null) {
    if (method_exists($controladorObj, $accion)) {
        call_user_func([$controladorObj, $accion], $id);
    } else {
        die("Acción no encontrada: $accion");
    }
} else {
    if (method_exists($controladorObj, $accion)) {
        call_user_func([$controladorObj, $accion]);
    } else {
        die("Acción no encontrada: $accion");
    }
}

// Cerrar conexión
$db->cerrar();
?>
