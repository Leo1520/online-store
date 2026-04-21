<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../modelos/Cuenta.php';

$usuario  = trim($_POST['usuario']  ?? '');
$password = trim($_POST['password'] ?? '');

if ($usuario === '' || $password === '') {
    echo json_encode(['ok' => false, 'mensaje' => 'Ingresa usuario y contraseña.']);
    exit();
}

$cuentaModel = new Cuenta();
$cuenta      = $cuentaModel->verificarCredenciales($usuario, $password);

if ($cuenta) {
    $_SESSION['usuario']  = $cuenta['usuario'];
    $_SESSION['rol']      = $cuenta['rol'] ?? 'cliente';
    $_SESSION['es_admin'] = ($_SESSION['rol'] === 'admin');
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Usuario o contraseña incorrectos.']);
}
