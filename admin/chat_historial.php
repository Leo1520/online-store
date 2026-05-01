<?php
/**
 * Endpoint AJAX — historial del chat
 * GET /admin/chat_historial.php?chat=todos|{username}&offset=0
 */
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    http_response_code(401);
    exit(json_encode(['error' => 'No autorizado']));
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$yo     = $_SESSION['usuario'];
$chat   = trim($_GET['chat'] ?? 'todos');
$canal  = trim($_GET['canal'] ?? 'internos');
if (!in_array($canal, ['internos', 'clientes'])) $canal = 'internos';
$offset = max(0, (int)($_GET['offset'] ?? 0));
$limite = 50;

$db = Database::conectar();
// Migración automática
$db->query("ALTER TABLE chat_mensajes ADD COLUMN IF NOT EXISTS canal VARCHAR(20) NOT NULL DEFAULT 'internos'");

if ($chat === 'todos') {
    $sql  = "SELECT tipo, de, para, texto, creado_en
             FROM chat_mensajes
             WHERE tipo = 'mensaje' AND canal = ?
             ORDER BY creado_en DESC
             LIMIT ? OFFSET ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('sii', $canal, $limite, $offset);
} else {
    $sql  = "SELECT tipo, de, para, texto, creado_en
             FROM chat_mensajes
             WHERE tipo = 'privado'
               AND ((de = ? AND para = ?) OR (de = ? AND para = ?))
             ORDER BY creado_en DESC
             LIMIT ? OFFSET ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssssii', $yo, $chat, $chat, $yo, $limite, $offset);
}

$stmt->execute();
$res  = $stmt->get_result();
$rows = [];

while ($row = $res->fetch_assoc()) {
    $rows[] = [
        'tipo'      => $row['de'] === $yo ? 'yo' : 'otro',
        'de'        => $row['de'],
        'texto'     => $row['texto'],
        'hora'      => (new DateTime($row['creado_en']))->format('H:i'),
    ];
}
$stmt->close();

// Los mensajes vienen DESC del DB → invertir para mostrar cronológicamente
echo json_encode(array_reverse($rows), JSON_UNESCAPED_UNICODE);
