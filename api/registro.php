<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../modelos/Cliente.php';

$usuario    = trim($_POST['usuario']    ?? '');
$password   = trim($_POST['password']   ?? '');
$ci         = trim($_POST['ci']         ?? '');
$nombres    = trim($_POST['nombres']    ?? '');
$apPaterno  = trim($_POST['apPaterno']  ?? '');
$apMaterno  = trim($_POST['apMaterno']  ?? '');
$correo     = trim($_POST['correo']     ?? '');
$direccion  = trim($_POST['direccion']  ?? '');
$nroCelular = trim($_POST['nroCelular'] ?? '');

if (!$usuario || !$password || !$ci || !$nombres || !$apPaterno || !$apMaterno || !$correo || !$direccion || !$nroCelular) {
    echo json_encode(['ok' => false, 'mensaje' => 'Todos los campos son obligatorios.']);
    exit();
}

$clienteModel = new Cliente();
$hash = password_hash($password, PASSWORD_DEFAULT);
$ok = $clienteModel->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);

if ($ok) {
    // Auto-login tras registro
    require_once __DIR__ . '/../modelos/Cuenta.php';
    $cuenta = (new Cuenta())->verificarCredenciales($usuario, $password);
    if ($cuenta) {
        $_SESSION['usuario']  = $cuenta['usuario'];
        $_SESSION['rol']      = $cuenta['rol'] ?? 'cliente';
        $_SESSION['es_admin'] = false;
    }
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'El usuario o CI ya existe. Prueba con otros datos.']);
}
