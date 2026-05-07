<?php
/**
 * Servidor WebSocket del chat — ElectroHogar
 * Ejecutar: php chat_server.php start
 * Puerto  : 2346
 * Canales : internos | clientes | admin (recibe ambos)
 */
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;

$ws        = new Worker('websocket://0.0.0.0:2346');
$ws->count = 1;
$ws->name  = 'ChatElectroHogar';

$nombres    = [];  // [connId => nombre]
$canales    = [];  // [connId => 'internos'|'clientes'|'admin']
$bloqueados = [];  // ['usuario' => ['bloqueado1', ...]] — quién bloqueó a quién
$db         = null;

/* ── DB helpers ─────────────────────────────────────────────── */
function dbConectar(): ?mysqli {
    $conn = new mysqli('localhost', 'root', '', 'mydb');
    if ($conn->connect_error) return null;
    $conn->set_charset('utf8mb4');
    $conn->query("ALTER TABLE chat_mensajes ADD COLUMN IF NOT EXISTS canal VARCHAR(20) NOT NULL DEFAULT 'internos'");
    return $conn;
}

function dbGuardar(string $tipo, string $de, ?string $para, string $texto, string $canal = 'internos'): void {
    global $db;
    if ($db === null || !$db->ping()) $db = dbConectar();
    if (!$db) return;
    $stmt = $db->prepare("INSERT INTO chat_mensajes (tipo, de, para, texto, canal) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) return;
    $stmt->bind_param('sssss', $tipo, $de, $para, $texto, $canal);
    $stmt->execute();
    $stmt->close();
}

/* ── Conexión nueva ─────────────────────────────────────────── */
$ws->onConnect = function ($conn) use (&$nombres, &$canales) {
    $nombres[$conn->id] = null;
    $canales[$conn->id] = 'internos';
};

/* ── Mensaje recibido ───────────────────────────────────────── */
$ws->onMessage = function ($conn, $data) use ($ws, &$nombres, &$canales, &$bloqueados) {
    $data = trim($data);

    /* Primera vez: registrar — acepta JSON {"nombre":"...","canal":"..."} */
    if ($nombres[$conn->id] === null) {
        $reg = json_decode($data, true);
        if ($reg && isset($reg['nombre'])) {
            $nombre = trim($reg['nombre']) ?: 'Anónimo';
            $canal  = $reg['canal'] ?? 'internos';
            if (!in_array($canal, ['internos', 'clientes', 'admin'])) $canal = 'internos';
        } else {
            $nombre = $data ?: 'Anónimo';
            $canal  = 'internos';
        }
        $nombres[$conn->id] = $nombre;
        $canales[$conn->id] = $canal;

        $canalMensaje = ($canal === 'admin') ? 'internos' : $canal;
        _broadcastCanal($ws, $canales, $canalMensaje, _sistema("✔ {$nombre} se unió", $canalMensaje));
        _enviarUsuarios($ws, $nombres, $canales);

        // Notificar al nuevo usuario quiénes lo tienen bloqueado
        foreach ($bloqueados as $bloqueador => $lista) {
            foreach ($lista as $bloqueado) {
                if (strtolower($bloqueado) === strtolower($nombre)) {
                    $conn->send(_encode(['tipo' => 'bloqueado_por', 'usuario' => $bloqueador]));
                }
            }
        }
        return;
    }

    if ($data === '/salir') {
        $conn->close();
        return;
    }

    $miNombre = $nombres[$conn->id];
    $miCanal  = $canales[$conn->id];

    $json = json_decode($data, true);

    /* ── Comandos de bloqueo / desbloqueo ── */
    if ($json && isset($json['tipo'])) {
        if ($json['tipo'] === 'bloquear' && isset($json['usuario'])) {
            $objetivo = $json['usuario'];
            if (!isset($bloqueados[$miNombre])) $bloqueados[$miNombre] = [];
            $yaExiste = false;
            foreach ($bloqueados[$miNombre] as $u) {
                if (strtolower($u) === strtolower($objetivo)) { $yaExiste = true; break; }
            }
            if (!$yaExiste) $bloqueados[$miNombre][] = $objetivo;
            _notificarUsuario($ws, $nombres, $objetivo, _encode(['tipo' => 'bloqueado_por', 'usuario' => $miNombre]));
            return;
        }

        if ($json['tipo'] === 'desbloquear' && isset($json['usuario'])) {
            $objetivo = $json['usuario'];
            if (isset($bloqueados[$miNombre])) {
                $nueva = [];
                foreach ($bloqueados[$miNombre] as $u) {
                    if (strtolower($u) !== strtolower($objetivo)) $nueva[] = $u;
                }
                $bloqueados[$miNombre] = $nueva;
            }
            _notificarUsuario($ws, $nombres, $objetivo, _encode(['tipo' => 'desbloqueado_por', 'usuario' => $miNombre]));
            return;
        }
    }

    /* ── Mensaje JSON: {"texto":"...","canal":"..."} o {"para":"...","texto":"...","canal":"..."} ── */
    if ($json && isset($json['texto'])) {
        $txt   = trim($json['texto']);
        $canal = $json['canal'] ?? (($miCanal === 'admin') ? 'internos' : $miCanal);
        if (!in_array($canal, ['internos', 'clientes'])) $canal = 'internos';

        if (isset($json['para'])) {
            $dest = $json['para'];
            // Si el destinatario bloqueó al remitente → rechazar mensaje
            if (isset($bloqueados[$dest])) {
                foreach ($bloqueados[$dest] as $u) {
                    if (strtolower($u) === strtolower($miNombre)) {
                        $conn->send(_encode(['tipo' => 'privado_bloqueado', 'para' => $dest, 'texto' => $txt, 'canal' => $canal]));
                        return;
                    }
                }
            }
            $ok = _privadoCanal($ws, $nombres, $dest, $miNombre, $txt, $canal);
            if ($ok) {
                $conn->send(_encode(['tipo' => 'privado_enviado', 'para' => $dest, 'texto' => $txt, 'canal' => $canal]));
                dbGuardar('privado', $miNombre, $dest, $txt, $canal);
            } else {
                $conn->send(_sistema("'{$dest}' no está conectado.", $canal));
            }
        } else {
            _broadcastCanal($ws, $canales, $canal, _mensaje($miNombre, $txt, $canal));
            dbGuardar('mensaje', $miNombre, null, $txt, $canal);
        }
        return;
    }

    /* ── Texto plano (compatibilidad) ── */
    $canal = ($miCanal === 'admin') ? 'internos' : $miCanal;

    if (str_starts_with($data, '@')) {
        $sep = strpos($data, ':');
        if ($sep > 1) {
            $dest = trim(substr($data, 1, $sep - 1));
            $txt  = trim(substr($data, $sep + 1));
            // Si el destinatario bloqueó al remitente → rechazar
            if (isset($bloqueados[$dest])) {
                foreach ($bloqueados[$dest] as $u) {
                    if (strtolower($u) === strtolower($miNombre)) {
                        $conn->send(_encode(['tipo' => 'privado_bloqueado', 'para' => $dest, 'texto' => $txt, 'canal' => $canal]));
                        return;
                    }
                }
            }
            $ok = _privadoCanal($ws, $nombres, $dest, $miNombre, $txt, $canal);
            if ($ok) {
                $conn->send(_encode(['tipo' => 'privado_enviado', 'para' => $dest, 'texto' => $txt, 'canal' => $canal]));
                dbGuardar('privado', $miNombre, $dest, $txt, $canal);
            } else {
                $conn->send(_sistema("'{$dest}' no está conectado.", $canal));
            }
        } else {
            _broadcastCanal($ws, $canales, $canal, _mensaje($miNombre, $data, $canal));
            dbGuardar('mensaje', $miNombre, null, $data, $canal);
        }
        return;
    }

    _broadcastCanal($ws, $canales, $canal, _mensaje($miNombre, $data, $canal));
    dbGuardar('mensaje', $miNombre, null, $data, $canal);
};

/* ── Desconexión ────────────────────────────────────────────── */
$ws->onClose = function ($conn) use ($ws, &$nombres, &$canales) {
    $nombre = $nombres[$conn->id] ?? null;
    $canal  = $canales[$conn->id] ?? 'internos';
    if ($nombre !== null) {
        $canalMensaje = ($canal === 'admin') ? 'internos' : $canal;
        _broadcastCanal($ws, $canales, $canalMensaje, _sistema("✖ {$nombre} salió", $canalMensaje));
    }
    unset($nombres[$conn->id], $canales[$conn->id]);
    _enviarUsuarios($ws, $nombres, $canales);
};

/* ── Helpers ────────────────────────────────────────────────── */
function _encode(array $data): string {
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

function _sistema(string $texto, string $canal = 'internos'): string {
    return _encode(['tipo' => 'sistema', 'texto' => $texto, 'canal' => $canal]);
}

function _mensaje(string $de, string $texto, string $canal = 'internos'): string {
    return _encode(['tipo' => 'mensaje', 'de' => $de, 'texto' => $texto, 'canal' => $canal]);
}

function _broadcastCanal(Worker $ws, array $canales, string $canal, string $msg): void {
    foreach ($ws->connections as $c) {
        $cCanal = $canales[$c->id] ?? 'internos';
        if ($cCanal === $canal || $cCanal === 'admin') {
            $c->send($msg);
        }
    }
}

function _privadoCanal(Worker $ws, array $nombres, string $dest, string $de, string $txt, string $canal): bool {
    foreach ($ws->connections as $c) {
        if (isset($nombres[$c->id]) && strtolower($nombres[$c->id]) === strtolower($dest)) {
            $c->send(_encode(['tipo' => 'privado', 'de' => $de, 'texto' => $txt, 'canal' => $canal]));
            return true;
        }
    }
    return false;
}

function _notificarUsuario(Worker $ws, array $nombres, string $dest, string $msg): void {
    foreach ($ws->connections as $c) {
        if (isset($nombres[$c->id]) && strtolower($nombres[$c->id]) === strtolower($dest)) {
            $c->send($msg);
            return;
        }
    }
}

function _enviarUsuarios(Worker $ws, array $nombres, array $canales): void {
    $internos = [];
    $clientes = [];
    foreach ($nombres as $id => $nombre) {
        if ($nombre === null) continue;
        $c = $canales[$id] ?? 'internos';
        if ($c === 'clientes') {
            $clientes[] = $nombre;
        } else {
            $internos[] = $nombre;
        }
    }
    $msg = _encode(['tipo' => 'usuarios', 'internos' => $internos, 'clientes' => $clientes]);
    foreach ($ws->connections as $c) {
        $c->send($msg);
    }
}

Worker::runAll();
