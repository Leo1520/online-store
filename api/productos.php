<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../modelos/Producto.php';

$modelo    = new Producto();
$productos = $modelo->obtenerTodos();

echo json_encode(['ok' => true, 'productos' => $productos]);
