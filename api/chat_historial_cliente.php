<?php
/**
 * Historial del chat de clientes — canal 'clientes'
 * GET ?chat=todos          → mensajes grupales
 * GET ?chat={usuario}      → mensajes privados entre yo y ese usuario
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

// Aceptar usuario logueado o invitado de sesión; rechazar admins y sin sesión
$yo = $_SESSION['usuario'] ?? $_SESSION['chat_guest'] ?? '';
if (empty($yo) || !empty($_SESSION['es_admin'])) {
    http_response_code(401);
    exit(json_encode([]));
}

require_once __DIR__ . '/../config/database.php';
$chat   = trim($_GET['chat'] ?? 'todos');
$limite = 60;
$db     = Database::conectar();

$db->query("ALTER TABLE chat_mensajes ADD COLUMN IF NOT EXISTS canal VARCHAR(20) NOT NULL DEFAULT 'internos'");

if ($chat === 'todos') {
    $sql  = "SELECT tipo, de, para, texto, creado_en
             FROM chat_mensajes
             WHERE tipo = 'mensaje' AND canal = 'clientes'
             ORDER BY creado_en DESC
             LIMIT ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $limite);
} else {
    $sql  = "SELECT tipo, de, para, texto, creado_en
             FROM chat_mensajes
             WHERE tipo = 'privado' AND canal = 'clientes'
               AND ((de = ? AND para = ?) OR (de = ? AND para = ?))
             ORDER BY creado_en DESC
             LIMIT ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssssi', $yo, $chat, $chat, $yo, $limite);
}

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
