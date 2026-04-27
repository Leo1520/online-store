<?php
/**
 * Servidor WebSocket del chat — ElectroHogar
 * Ejecutar: php chat_server.php start
 * Puerto  : 2346
 */
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;

$ws        = new Worker('websocket://0.0.0.0:2346');
$ws->count = 1;
$ws->name  = 'ChatElectroHogar';

// Almacena nombres de conexión por ID (evita dynamic properties en PHP 8.2+)
$nombres = [];

/* ── Conexión nueva ─────────────────────────────────────────── */
$ws->onConnect = function ($conn) use (&$nombres) {
    $nombres[$conn->id] = null;
};

/* ── Mensaje recibido ───────────────────────────────────────── */
$ws->onMessage = function ($conn, $data) use ($ws, &$nombres) {

    $data = trim($data);

    /* Primera vez: registrar nombre */
    if ($nombres[$conn->id] === null) {
        $nombres[$conn->id] = $data ?: 'Anónimo';
        _broadcast($ws, _sistema("✔ {$nombres[$conn->id]} se unió al chat"));
        _enviarUsuarios($ws, $nombres);
        return;
    }

    if ($data === '/salir') {
        $conn->close();
        return;
    }

    /* Mensaje privado: @Destino: texto */
    if (str_starts_with($data, '@')) {
        $sep = strpos($data, ':');
        if ($sep > 1) {
            $dest   = trim(substr($data, 1, $sep - 1));
            $txt    = trim(substr($data, $sep + 1));
            $miNombre = $nombres[$conn->id];
            $ok     = _privado($ws, $nombres, $dest, $miNombre, $txt);
            if ($ok) {
                $conn->send(_encode(['tipo' => 'privado_enviado', 'para' => $dest, 'texto' => $txt]));
            } else {
                $conn->send(_sistema("'$dest' no está conectado."));
            }
        } else {
            _broadcast($ws, _mensaje($nombres[$conn->id], $data));
        }
        return;
    }

    /* Broadcast */
    _broadcast($ws, _mensaje($nombres[$conn->id], $data));
};

/* ── Desconexión ────────────────────────────────────────────── */
$ws->onClose = function ($conn) use ($ws, &$nombres) {
    if (isset($nombres[$conn->id]) && $nombres[$conn->id] !== null) {
        _broadcast($ws, _sistema("✖ {$nombres[$conn->id]} salió del chat"));
    }
    unset($nombres[$conn->id]);
    _enviarUsuarios($ws, $nombres);
};

/* ── Helpers ────────────────────────────────────────────────── */
function _encode(array $data): string {
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

function _sistema(string $texto): string {
    return _encode(['tipo' => 'sistema', 'texto' => $texto]);
}

function _mensaje(string $de, string $texto): string {
    return _encode(['tipo' => 'mensaje', 'de' => $de, 'texto' => $texto]);
}

function _broadcast(Worker $ws, string $msg): void {
    foreach ($ws->connections as $c) {
        $c->send($msg);
    }
}

function _privado(Worker $ws, array $nombres, string $dest, string $de, string $txt): bool {
    foreach ($ws->connections as $c) {
        if (isset($nombres[$c->id]) && strtolower($nombres[$c->id]) === strtolower($dest)) {
            $c->send(_encode(['tipo' => 'privado', 'de' => $de, 'texto' => $txt]));
            return true;
        }
    }
    return false;
}

function _enviarUsuarios(Worker $ws, array $nombres): void {
    $lista = array_values(array_filter($nombres, fn($n) => $n !== null));
    $msg   = _encode(['tipo' => 'usuarios', 'lista' => $lista]);
    foreach ($ws->connections as $c) {
        $c->send($msg);
    }
}

Worker::runAll();
