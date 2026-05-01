<?php
/**
 * Historial del chat de clientes — solo canal 'clientes'
 * Requiere sesión de cliente (no admin)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario']) || !empty($_SESSION['es_admin'])) {
    http_response_code(401);
    exit(json_encode([]));
}

require_once __DIR__ . '/../config/database.php';

$yo     = $_SESSION['usuario'];
$limite = 60;
$db     = Database::conectar();

// Migración automática por si el servidor no se ha iniciado aún
$db->query("ALTER TABLE chat_mensajes ADD COLUMN IF NOT EXISTS canal VARCHAR(20) NOT NULL DEFAULT 'internos'");

$sql  = "SELECT tipo, de, para, texto, creado_en
         FROM chat_mensajes
         WHERE tipo = 'mensaje' AND canal = 'clientes'
         ORDER BY creado_en DESC
         LIMIT ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $limite);
$stmt->execute();
$res  = $stmt->get_result();
$rows = [];

while ($row = $res->fetch_assoc()) {
    $rows[] = [
        'tipo'  => $row['de'] === $yo ? 'yo' : 'otro',
        'de'    => $row['de'],
        'texto' => $row['texto'],
        'hora'  => (new DateTime($row['creado_en']))->format('H:i'),
    ];
}
$stmt->close();

echo json_encode(array_reverse($rows), JSON_UNESCAPED_UNICODE);
