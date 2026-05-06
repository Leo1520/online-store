<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$chatClienteUsuario = $_SESSION['usuario'] ?? '';
$esInvitado = false;

if (empty($chatClienteUsuario)) {
    // Generar nombre de invitado basado en IP + session_id (único por sesión)
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0')[0]);
    if (!filter_var($ip, FILTER_VALIDATE_IP)) $ip = '0.0.0.0';

    if (empty($_SESSION['chat_guest'])) {
        $_SESSION['chat_guest'] = 'Invitado_' . substr(md5($ip . session_id()), 0, 6);
    }
    $chatClienteUsuario = $_SESSION['chat_guest'];
    $esInvitado = true;
}

$chatClienteUsuario = htmlspecialchars($chatClienteUsuario, ENT_QUOTES);
?>
<!-- ══════════════════════════════════════════════════════════
     CHAT WIDGET — Tienda / Clientes  (panel con contactos)
══════════════════════════════════════════════════════════════ -->
<style>
/* ── Botón flotante ─────────────────────────────── */
#cliChatBtn {
    position: fixed;
    bottom: 28px; right: 28px;
    width: 54px; height: 54px;
    border-radius: 50%;
    background: #1B3A6B; color: #fff;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    box-shadow: 0 4px 18px rgba(27,58,107,.45);
    z-index: 2000;
    transition: transform .2s, background .2s;
}
#cliChatBtn:hover { background: #2751a3; transform: scale(1.08); }
#cliChatBtn .cli-badge {
    position: absolute; top: -4px; right: -4px;
    background: #dc3545; color: #fff;
    font-size: .6rem; font-weight: 700;
    width: 19px; height: 19px; border-radius: 50%;
    display: none; align-items: center; justify-content: center;
    border: 2px solid #fff;
}
#cliChatBtn .cli-badge.visible { display: flex; }

/* ── Panel principal ────────────────────────────── */
#cliChatPanel {
    position: fixed;
    bottom: 92px; right: 28px;
    width: 700px; height: 510px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.22);
    display: none; flex-direction: column;
    overflow: hidden;
    z-index: 1999;
    animation: cliSlide .22s ease;
}
#cliChatPanel.open { display: flex; }
@keyframes cliSlide {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ── Header ─────────────────────────────────────── */
.cli-header {
    background: #1B3A6B; color: #fff;
    padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
    flex-shrink: 0;
}
.cli-header-ico {
    width: 36px; height: 36px;
    background: rgba(255,255,255,.15); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.cli-header-info { flex: 1; }
.cli-header-title { font-weight: 700; font-size: .95rem; line-height: 1.2; }
.cli-header-sub   { font-size: .72rem; opacity: .75; }
.cli-dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: #aaa; flex-shrink: 0; transition: background .3s;
}
.cli-dot.online { background: #25d366; }
.cli-close {
    background: none; border: none; color: rgba(255,255,255,.8);
    font-size: 1.3rem; cursor: pointer; padding: 0 4px;
}
.cli-close:hover { color: #fff; }

/* ── Cuerpo: dos columnas ───────────────────────── */
.cli-body {
    display: flex; flex: 1; overflow: hidden;
}

/* ── Columna izquierda: contactos ───────────────── */
.cli-contacts {
    width: 230px;
    border-right: 1px solid #f0f0f0;
    display: flex; flex-direction: column;
    background: #fafafa; flex-shrink: 0;
}
.cli-search {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f0f0;
}
.cli-search input {
    width: 100%; border: none;
    background: #f0f2f5; border-radius: 20px;
    padding: 6px 14px; font-size: .82rem;
    outline: none; color: #333;
}
.cli-contact-list { flex: 1; overflow-y: auto; }
.cli-contact-list::-webkit-scrollbar { width: 3px; }
.cli-contact-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

.cli-contact-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid #f5f5f5;
    transition: background .12s;
}
.cli-contact-item:hover  { background: #f0f2f5; }
.cli-contact-item.active { background: #e8edf5; }

.cli-avatar {
    width: 42px; height: 42px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 1rem;
    flex-shrink: 0;
}
.cli-contact-info { flex: 1; min-width: 0; }
.cli-contact-name {
    font-size: .84rem; font-weight: 600; color: #111;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.cli-contact-prev {
    font-size: .72rem; color: #888;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-top: 2px;
}
.cli-unread {
    background: #25d366; color: #fff;
    font-size: .65rem; font-weight: 700;
    min-width: 18px; height: 18px; border-radius: 9px;
    display: none; align-items: center; justify-content: center;
    padding: 0 4px; flex-shrink: 0;
}
.cli-unread.visible { display: flex; }

/* ── Columna derecha: mensajes ──────────────────── */
.cli-right {
    flex: 1; display: flex; flex-direction: column;
    background: #efeae2;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d9d9d9' fill-opacity='0.18'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    overflow: hidden;
}
.cli-msg-header {
    background: #f0f2f5; border-bottom: 1px solid #e5e5e5;
    padding: 10px 16px;
    display: none; align-items: center; gap: 10px;
    flex-shrink: 0;
}
.cli-msg-header .cli-avatar { width: 36px; height: 36px; font-size: .85rem; }
.cli-msg-header-name { font-weight: 600; font-size: .88rem; color: #111; }
.cli-msg-header-sub  { font-size: .72rem; color: #888; }

.cli-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #aaa; gap: 10px;
}
.cli-empty i { font-size: 3rem; opacity: .3; }
.cli-empty p { font-size: .85rem; }

.cli-messages {
    flex: 1; overflow-y: auto;
    padding: 12px 14px;
    display: none; flex-direction: column; gap: 4px;
}
.cli-messages::-webkit-scrollbar { width: 4px; }
.cli-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

.cli-bubble-wrap { display: flex; flex-direction: column; }
.cli-bubble-wrap.me    { align-items: flex-end; }
.cli-bubble-wrap.other { align-items: flex-start; }
.cli-sender-name {
    font-size: .7rem; font-weight: 600; color: #1B3A6B;
    margin-bottom: 2px; padding: 0 4px;
}
.cli-bubble {
    max-width: 70%; padding: 7px 12px;
    border-radius: 10px; font-size: .84rem;
    line-height: 1.4; word-break: break-word;
}
.cli-bubble-wrap.me    .cli-bubble { background: #1B3A6B; color: #fff; border-bottom-right-radius: 3px; }
.cli-bubble-wrap.other .cli-bubble { background: #fff; color: #111; border-bottom-left-radius: 3px; box-shadow: 0 1px 2px rgba(0,0,0,.1); }
.cli-bubble-time { font-size: .65rem; opacity: .6; margin-top: 2px; padding: 0 4px; }
.cli-system-msg {
    text-align: center; font-size: .72rem; color: #888;
    background: rgba(255,255,255,.7); border-radius: 10px;
    padding: 3px 12px; align-self: center; margin: 4px 0;
}

.cli-input-wrap {
    display: none; align-items: center; gap: 8px;
    padding: 10px 12px;
    background: #f0f2f5; border-top: 1px solid #e5e5e5;
    flex-shrink: 0;
}
.cli-input-wrap input {
    flex: 1; border: none; background: #fff;
    border-radius: 22px; padding: 8px 14px;
    font-size: .85rem; outline: none; color: #333;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.cli-send-btn {
    width: 38px; height: 38px; border-radius: 50%;
    background: #1B3A6B; color: #fff;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0; transition: background .15s;
}
.cli-send-btn:hover    { background: #2751a3; }
.cli-send-btn:disabled { background: #aaa; cursor: default; }

/* ── Menú tres puntos ───────────────────────────── */
.cli-menu-wrap {
    position: relative; flex-shrink: 0; margin-left: auto;
}
.cli-menu-btn {
    background: none; border: none;
    color: #555; font-size: 1.4rem; line-height: 1;
    cursor: pointer; padding: 4px 8px; border-radius: 6px;
    transition: background .12s;
}
.cli-menu-btn:hover { background: #e0e3e8; color: #111; }
.cli-dropdown {
    position: absolute; top: calc(100% + 4px); right: 0;
    background: #fff; border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,.18);
    min-width: 185px; z-index: 300;
    overflow: hidden; border: 1px solid #eee;
    display: none;
}
.cli-dropdown.open { display: block; }
.cli-dropdown-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; font-size: .84rem; color: #333;
    cursor: pointer; transition: background .1s;
    border-bottom: 1px solid #f5f5f5;
}
.cli-dropdown-item:last-child { border-bottom: none; }
.cli-dropdown-item:hover { background: #f5f7fa; }
.cli-dropdown-item i { font-size: .9rem; width: 16px; text-align: center; }
.cli-item-danger       { color: #dc3545; }
.cli-item-danger:hover { background: #fff0f0; }
.cli-block-badge {
    display: inline-block;
    background: #dc3545; color: #fff;
    font-size: .58rem; padding: 1px 6px;
    border-radius: 10px; margin-left: 5px;
    vertical-align: middle; font-weight: 600;
}

/* ── Estado bloqueado ───────────────────────────── */
.cli-blocked-bar {
    background: #fff3cd; border-bottom: 1px solid #ffc107;
    color: #7d4e00; font-size: .78rem;
    padding: 7px 14px; display: none; align-items: center;
    gap: 8px; flex-shrink: 0;
}
.cli-blocked-bar.visible { display: flex; }
.cli-blocked-bar button {
    margin-left: auto; background: none; cursor: pointer;
    border: 1px solid #7d4e00; color: #7d4e00;
    border-radius: 4px; padding: 2px 9px; font-size: .73rem;
}
.cli-bloqueado-por-bar {
    background: #f8d7da; border-bottom: 1px solid #f5c6cb;
    color: #721c24; font-size: .78rem;
    padding: 7px 14px; display: none; align-items: center;
    gap: 8px; flex-shrink: 0;
}
.cli-bloqueado-por-bar.visible { display: flex; }
.cli-blocked-input-notice {
    display: none; align-items: center; justify-content: center;
    gap: 8px; padding: 10px 14px;
    background: #fff3cd; border-top: 1px solid #ffc107;
    font-size: .8rem; color: #7d4e00; flex-shrink: 0;
}
.cli-blocked-input-notice.visible { display: flex; }
.cli-bloqueado-por-input {
    display: none; align-items: center; justify-content: center;
    gap: 8px; padding: 10px 14px;
    background: #f8d7da; border-top: 1px solid #f5c6cb;
    font-size: .8rem; color: #721c24; flex-shrink: 0;
}
.cli-bloqueado-por-input.visible { display: flex; }
.cli-bubble-wrap.me.yo-bloqueado .cli-bubble { background: #6c757d; opacity: .85; }
.cli-msg-blocked-status {
    font-size: .65rem; color: #dc3545;
    margin-top: 2px; padding: 0 4px; text-align: right;
}

/* ── Responsive ─────────────────────────────────── */
@media (max-width: 760px) {
    #cliChatPanel { width: calc(100vw - 20px); right: 10px; left: 10px; }
    .cli-contacts { width: 200px; }
}
@media (max-width: 540px) {
    #cliChatPanel { height: 480px; }
    .cli-contacts { display: none; }
    #cliChatPanel.show-contacts .cli-contacts { display: flex; width: 100%; }
    #cliChatPanel.show-contacts .cli-right { display: none; }
}
</style>

<!-- ── Botón flotante ── -->
<button id="cliChatBtn" title="Chat con clientes" onclick="cliToggle()">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="cli-badge" id="cliBadge">0</span>
</button>

<!-- ── Panel ── -->
<div id="cliChatPanel">

    <!-- Header -->
    <div class="cli-header">
        <div class="cli-header-ico"><i class="bi bi-chat-dots-fill"></i></div>
        <div class="cli-header-info">
            <div class="cli-header-title">Chat Clientes</div>
            <div class="cli-header-sub">
                <?php if ($esInvitado): ?>
                    Modo invitado &mdash; <strong><?php echo $chatClienteUsuario; ?></strong>
                <?php else: ?>
                    Conectado como <strong><?php echo $chatClienteUsuario; ?></strong>
                <?php endif; ?>
            </div>
        </div>
        <span class="cli-dot" id="cliDot" title="Sin conexión"></span>
        <button class="cli-close" onclick="cliToggle()" title="Cerrar">&#x2715;</button>
    </div>

    <!-- Cuerpo -->
    <div class="cli-body">

        <!-- Columna izquierda: contactos -->
        <div class="cli-contacts">
            <div class="cli-search">
                <input id="cliSearchInput" placeholder="Buscar cliente..." oninput="cliFiltrar(this.value)">
            </div>
            <div class="cli-contact-list" id="cliContactList"></div>
        </div>

        <!-- Columna derecha: mensajes -->
        <div class="cli-right" id="cliRight">
            <div class="cli-msg-header" id="cliMsgHeader"></div>
            <div class="cli-blocked-bar" id="cliBlockedBar">
                <i class="bi bi-slash-circle-fill"></i>
                Has bloqueado a este usuario.
                <button onclick="cliBloquear()">Desbloquear</button>
            </div>
            <div class="cli-bloqueado-por-bar" id="cliBloqueadoPorBar">
                <i class="bi bi-x-circle-fill"></i>
                <span id="cliBloqueadoPorText">Este usuario te ha bloqueado.</span>
            </div>
            <div class="cli-empty" id="cliEmpty">
                <i class="bi bi-chat-square-dots"></i>
                <p>Selecciona un chat para comenzar</p>
            </div>
            <div class="cli-messages" id="cliMessages"></div>
            <div class="cli-input-wrap" id="cliInputWrap">
                <input id="cliInput" type="text" placeholder="Escribe un mensaje..." maxlength="500"
                       onkeydown="if(event.key==='Enter')cliEnviar()">
                <button class="cli-send-btn" id="cliSendBtn" onclick="cliEnviar()" disabled title="Enviar">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
            <div class="cli-blocked-input-notice" id="cliBlockedInputNotice">
                <i class="bi bi-slash-circle"></i> Has bloqueado a este usuario. No puedes enviarle mensajes.
            </div>
            <div class="cli-bloqueado-por-input" id="cliBloqueadoPorInputNotice">
                <i class="bi bi-x-circle"></i> No puedes enviar mensajes — te han bloqueado.
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const MI_USUARIO  = <?php echo json_encode($chatClienteUsuario); ?>;
    const ES_INVITADO = <?php echo $esInvitado ? 'true' : 'false'; ?>;
    const WS_URL      = 'ws://' + window.location.hostname + ':2346';
    const TODOS_KEY   = '★ Todos';

    let ws          = null;
    let conectado   = false;
    let abierto     = false;
    let chatActivo  = TODOS_KEY;

    let contactos       = [TODOS_KEY];  // "★ Todos" + usuarios conectados
    let historiales     = {};
    let noLeidos        = {};
    let histCargado     = {};
    let bloqueadoPorSet = new Set();  // usuarios que ME bloquearon

    historiales[TODOS_KEY] = [];
    noLeidos[TODOS_KEY]    = 0;

    /* ── Toggle ── */
    window.cliToggle = function () {
        abierto = !abierto;
        document.getElementById('cliChatPanel').classList.toggle('open', abierto);
        if (abierto && !ws) cliConectar();
        if (abierto) {
            noLeidos[chatActivo] = 0;
            cliRenderContactos();
            actualizarBadge();
            if (!histCargado[chatActivo]) cliFetchHistorial(chatActivo);
        }
    };

    /* ── WebSocket ── */
    function cliConectar() {
        ws = new WebSocket(WS_URL);

        ws.onopen = function () {
            conectado = true;
            ws.send(JSON.stringify({ nombre: MI_USUARIO, canal: 'clientes' }));
            // Re-enviar bloqueos al servidor tras (re)conexión
            if (!ES_INVITADO) {
                cliBlockedList().forEach(function (user) {
                    ws.send(JSON.stringify({ tipo: 'bloquear', usuario: user }));
                });
            }
            document.getElementById('cliDot').classList.add('online');
            document.getElementById('cliDot').title = 'Conectado';
            document.getElementById('cliSendBtn').disabled = false;
            document.getElementById('cliInput').placeholder = 'Escribe un mensaje...';
        };

        ws.onmessage = function (e) {
            try { cliProcesar(JSON.parse(e.data)); } catch (_) {}
        };

        ws.onclose = function () {
            conectado = false;
            document.getElementById('cliDot').classList.remove('online');
            document.getElementById('cliDot').title = 'Sin conexión';
            document.getElementById('cliSendBtn').disabled = true;
            document.getElementById('cliInput').placeholder = 'Sin conexión...';
            setTimeout(() => { if (abierto) cliConectar(); }, 4000);
        };

        ws.onerror = function () { ws.close(); };
    }

    /* ── Procesar mensajes del servidor ── */
    function cliProcesar(msg) {
        switch (msg.tipo) {

            case 'usuarios': {
                // Reconstruir lista de clientes conectados
                contactos = [TODOS_KEY];
                (msg.clientes || []).forEach(n => {
                    if (n === MI_USUARIO) return;
                    contactos.push(n);
                    if (!historiales[n]) historiales[n] = [];
                    if (noLeidos[n] === undefined) noLeidos[n] = 0;
                });
                cliRenderContactos();
                break;
            }

            case 'mensaje': {
                if (msg.canal && msg.canal !== 'clientes') return;
                // Silenciar en Todos mensajes de usuarios bloqueados
                if (!ES_INVITADO && msg.de && msg.de !== MI_USUARIO && cliEstaBlockeado(msg.de)) return;
                cliAgregarMensaje(TODOS_KEY, {
                    tipo:  msg.de === MI_USUARIO ? 'yo' : 'otro',
                    de:    msg.de,
                    texto: msg.texto,
                    hora:  ahora()
                }, msg.de !== MI_USUARIO);
                break;
            }

            case 'sistema': {
                if (msg.canal && msg.canal !== 'clientes') return;
                cliAgregarMensaje(TODOS_KEY, { tipo: 'sistema', texto: msg.texto, hora: ahora() }, false);
                break;
            }

            case 'privado': {
                if (msg.canal && msg.canal !== 'clientes') return;
                // Silenciar mensajes de usuarios bloqueados
                if (!ES_INVITADO && cliEstaBlockeado(msg.de)) return;
                if (!historiales[msg.de]) historiales[msg.de] = [];
                if (noLeidos[msg.de] === undefined) noLeidos[msg.de] = 0;
                if (!contactos.includes(msg.de)) contactos.push(msg.de);
                cliAgregarMensaje(msg.de, { tipo: 'otro', de: msg.de, texto: msg.texto, hora: ahora() });
                break;
            }

            case 'privado_enviado': {
                if (msg.canal && msg.canal !== 'clientes') return;
                if (!historiales[msg.para]) historiales[msg.para] = [];
                cliAgregarMensaje(msg.para, { tipo: 'yo', texto: msg.texto, hora: ahora() }, false);
                break;
            }

            case 'privado_bloqueado': {
                if (msg.canal && msg.canal !== 'clientes') return;
                if (!historiales[msg.para]) historiales[msg.para] = [];
                cliAgregarMensaje(msg.para, { tipo: 'yo_bloqueado', texto: msg.texto, hora: ahora() }, false);
                break;
            }

            case 'bloqueado_por': {
                bloqueadoPorSet.add(msg.usuario);
                cliRenderContactos();
                if (chatActivo === msg.usuario) cliMostrarChat(msg.usuario);
                break;
            }

            case 'desbloqueado_por': {
                bloqueadoPorSet.delete(msg.usuario);
                cliRenderContactos();
                if (chatActivo === msg.usuario) cliMostrarChat(msg.usuario);
                break;
            }
        }
    }

    /* ── Agregar mensaje al historial ── */
    function cliAgregarMensaje(chat, entry, badge = true) {
        if (!historiales[chat]) historiales[chat] = [];
        historiales[chat].push(entry);

        if (chat === chatActivo && abierto) {
            cliRenderBurbuja(entry);
            cliScrollAbajo();
            noLeidos[chat] = 0;
        } else if (badge) {
            noLeidos[chat] = (noLeidos[chat] || 0) + 1;
            actualizarBadge();
        }
        cliRenderContactos();
    }

    /* ── Renderizar una burbuja ── */
    function cliRenderBurbuja(entry) {
        const area    = document.getElementById('cliMessages');
        const wrap    = document.createElement('div');
        const esTodos = (chatActivo === TODOS_KEY);

        if (entry.tipo === 'sistema') {
            wrap.className   = 'cli-system-msg';
            wrap.textContent = entry.texto;

        } else if (entry.tipo === 'yo_bloqueado') {
            wrap.className = 'cli-bubble-wrap me yo-bloqueado';
            const burbuja = document.createElement('div');
            burbuja.className   = 'cli-bubble';
            burbuja.textContent = entry.texto;
            wrap.appendChild(burbuja);
            const status = document.createElement('div');
            status.className   = 'cli-msg-blocked-status';
            status.textContent = '🚫 No enviado — bloqueado';
            wrap.appendChild(status);
            const time = document.createElement('div');
            time.className   = 'cli-bubble-time';
            time.textContent = entry.hora || '';
            wrap.appendChild(time);

        } else {
            const esYo = entry.tipo === 'yo' || entry.de === MI_USUARIO;
            wrap.className = 'cli-bubble-wrap ' + (esYo ? 'me' : 'other');

            if (entry.de && esTodos) {
                const name = document.createElement('div');
                name.className   = 'cli-sender-name';
                name.textContent = esYo ? 'Tú' : entry.de;
                name.style.textAlign = esYo ? 'right' : 'left';
                wrap.appendChild(name);
            }

            const burbuja = document.createElement('div');
            burbuja.className   = 'cli-bubble';
            burbuja.textContent = entry.texto;
            wrap.appendChild(burbuja);

            const time = document.createElement('div');
            time.className   = 'cli-bubble-time';
            time.textContent = entry.hora || '';
            wrap.appendChild(time);
        }
        area.appendChild(wrap);
    }

    /* ── Cargar historial del chat activo ── */
    function cliCargarHistorial() {
        const area = document.getElementById('cliMessages');
        area.innerHTML = '';
        (historiales[chatActivo] || []).forEach(e => cliRenderBurbuja(e));
        cliScrollAbajo();
    }

    /* ── Mostrar área de mensajes ── */
    function cliMostrarChat(chat) {
        const isTodos     = (chat === TODOS_KEY);
        const header      = document.getElementById('cliMsgHeader');
        const color       = avatarColor(chat);
        const inicial     = isTodos ? '★' : chat.charAt(0).toUpperCase();
        const iBlocked    = !ES_INVITADO && !isTodos && cliEstaBlockeado(chat);
        const theyBlocked = !ES_INVITADO && !isTodos && bloqueadoPorSet.has(chat);

        const menuHtml = (!ES_INVITADO && !isTodos) ? `
            <div class="cli-menu-wrap">
                <button class="cli-menu-btn" onclick="cliToggleMenu(event)" title="Opciones">&#8942;</button>
                <div class="cli-dropdown" id="cliDropdown">
                    <div class="cli-dropdown-item" onclick="cliBloquear()">
                        <i class="bi ${iBlocked ? 'bi-slash-circle-fill' : 'bi-slash-circle'}"></i>
                        ${iBlocked ? 'Desbloquear usuario' : 'Bloquear usuario'}
                    </div>
                    <div class="cli-dropdown-item" onclick="cliBorrarChat()">
                        <i class="bi bi-trash3"></i> Borrar historial
                    </div>
                    <div class="cli-dropdown-item cli-item-danger" onclick="cliEliminarContacto()">
                        <i class="bi bi-person-x-fill"></i> Eliminar contacto
                    </div>
                </div>
            </div>` : '';

        header.style.display = 'flex';
        header.innerHTML = `
            <div class="cli-avatar" style="background:${color};width:36px;height:36px;font-size:.85rem;">${inicial}</div>
            <div style="flex:1;min-width:0;">
                <div class="cli-msg-header-name">
                    ${escHtml(chat)}
                    ${iBlocked    ? '<span class="cli-block-badge">Bloqueado</span>' : ''}
                    ${theyBlocked ? '<span class="cli-block-badge" style="background:#721c24;">Te bloqueó</span>' : ''}
                </div>
                <div class="cli-msg-header-sub">${isTodos ? 'Chat grupal' : 'Mensaje privado'}</div>
            </div>
            ${menuHtml}`;

        // Barras de estado y controles de entrada
        const blockedBar      = document.getElementById('cliBlockedBar');
        const bloqueadoPorBar = document.getElementById('cliBloqueadoPorBar');
        const inputWrap       = document.getElementById('cliInputWrap');
        const blockedNotice   = document.getElementById('cliBlockedInputNotice');
        const blockedByNotice = document.getElementById('cliBloqueadoPorInputNotice');

        blockedBar.classList.remove('visible');
        bloqueadoPorBar.classList.remove('visible');
        blockedNotice.classList.remove('visible');
        blockedByNotice.classList.remove('visible');
        inputWrap.style.display = 'flex';

        if (!isTodos) {
            if (iBlocked) {
                blockedBar.classList.add('visible');
                inputWrap.style.display = 'none';
                blockedNotice.classList.add('visible');
            } else if (theyBlocked) {
                bloqueadoPorBar.classList.add('visible');
                document.getElementById('cliBloqueadoPorText').textContent =
                    chat + ' te ha bloqueado. No puedes enviarle mensajes.';
                inputWrap.style.display = 'none';
                blockedByNotice.classList.add('visible');
            }
        }

        document.getElementById('cliEmpty').style.display    = 'none';
        document.getElementById('cliMessages').style.display = 'flex';
    }

    /* ── Cambiar chat activo ── */
    function cliCambiar(chat) {
        chatActivo = chat;
        noLeidos[chat] = 0;
        cliRenderContactos();
        actualizarBadge();
        cliMostrarChat(chat);
        if (!histCargado[chat]) {
            cliFetchHistorial(chat);
        } else {
            cliCargarHistorial();
        }
    }

    /* ── Fetch historial desde BD ── */
    function cliFetchHistorial(chat) {
        // Invitados: sin historial persistente (su nombre cambia entre sesiones)
        if (ES_INVITADO) {
            histCargado[chat] = true;
            if (chatActivo === chat) cliCargarHistorial();
            return;
        }

        const chatParam = (chat === TODOS_KEY) ? 'todos' : chat;
        const area = document.getElementById('cliMessages');
        if (chatActivo === chat) {
            area.innerHTML = '<div class="cli-system-msg" style="margin:auto;">Cargando historial...</div>';
            area.style.display = 'flex';
        }

        fetch('api/chat_historial_cliente.php?chat=' + encodeURIComponent(chatParam))
            .then(r => r.json())
            .then(msgs => {
                if (!historiales[chat]) historiales[chat] = [];
                historiales[chat] = msgs.concat(historiales[chat]);
                histCargado[chat] = true;
                if (chatActivo === chat) cliCargarHistorial();
            })
            .catch(() => {
                histCargado[chat] = true;
                if (chatActivo === chat) cliCargarHistorial();
            });
    }

    /* ── Renderizar lista de contactos ── */
    window.cliFiltrar = function (q) { cliRenderContactos(q.toLowerCase()); };

    function cliRenderContactos(filtro = '') {
        const lista = document.getElementById('cliContactList');
        lista.innerHTML = '';

        contactos.forEach(chat => {
            if (filtro && !chat.toLowerCase().includes(filtro)) return;
            const unread  = noLeidos[chat] || 0;
            const hist    = historiales[chat] || [];
            const ultimo  = hist.length ? hist[hist.length - 1] : null;
            const preview = ultimo
                ? (ultimo.tipo === 'sistema' ? ultimo.texto
                    : (ultimo.tipo === 'yo' ? 'Tú: ' + ultimo.texto
                    : (ultimo.de ? ultimo.de + ': ' : '') + ultimo.texto))
                : 'Sin mensajes';
            const color   = avatarColor(chat);
            const isTodos = (chat === TODOS_KEY);
            const inicial = isTodos ? '★' : chat.charAt(0).toUpperCase();

            const blocked     = !ES_INVITADO && !isTodos && cliEstaBlockeado(chat);
            const theyBlocked = !ES_INVITADO && !isTodos && bloqueadoPorSet.has(chat);
            const item = document.createElement('div');
            item.className = 'cli-contact-item' + (chat === chatActivo ? ' active' : '');
            item.innerHTML = `
                <div class="cli-avatar" style="background:${color};${blocked ? 'opacity:.5;' : ''}">${inicial}</div>
                <div class="cli-contact-info">
                    <div class="cli-contact-name">
                        ${escHtml(isTodos ? '★ Todos' : chat)}
                        ${blocked     ? '<span class="cli-block-badge">Bloq.</span>' : ''}
                        ${theyBlocked ? '<span class="cli-block-badge" style="background:#721c24;">Te bloqueó</span>' : ''}
                    </div>
                    <div class="cli-contact-prev" style="${blocked ? 'color:#dc3545;' : theyBlocked ? 'color:#721c24;' : ''}">
                        ${blocked ? '🚫 Bloqueado' : theyBlocked ? '🔴 Te ha bloqueado' : escHtml(preview.length > 35 ? preview.slice(0,32) + '…' : preview)}
                    </div>
                </div>
                <span class="cli-unread ${unread > 0 ? 'visible' : ''}">${unread > 99 ? '99+' : unread}</span>`;
            item.onclick = () => cliCambiar(chat);
            lista.appendChild(item);
        });
    }

    /* ── Enviar mensaje ── */
    window.cliEnviar = function () {
        if (!conectado || !ws) return;
        const input = document.getElementById('cliInput');
        const txt   = input.value.trim();
        if (!txt) return;

        // Si el destinatario me bloqueó: mostrar mensaje fallido localmente
        if (!ES_INVITADO && chatActivo !== TODOS_KEY && bloqueadoPorSet.has(chatActivo)) {
            cliAgregarMensaje(chatActivo, { tipo: 'yo_bloqueado', texto: txt, hora: ahora() }, false);
            input.value = '';
            return;
        }

        if (chatActivo === TODOS_KEY) {
            ws.send(JSON.stringify({ texto: txt, canal: 'clientes' }));
        } else {
            ws.send(JSON.stringify({ para: chatActivo, texto: txt, canal: 'clientes' }));
        }
        input.value = '';
        input.focus();
    };

    /* ── Badge global ── */
    function actualizarBadge() {
        const total = Object.values(noLeidos).reduce((a, b) => a + b, 0);
        const badge = document.getElementById('cliBadge');
        badge.textContent = total > 99 ? '99+' : total;
        badge.classList.toggle('visible', total > 0);
    }

    function cliScrollAbajo() {
        const area = document.getElementById('cliMessages');
        area.scrollTop = area.scrollHeight;
    }

    function ahora() {
        return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }

    function avatarColor(name) {
        if (name === TODOS_KEY) return '#1d7a35';
        let h = 0;
        for (let i = 0; i < name.length; i++) h = name.charCodeAt(i) + ((h << 5) - h);
        const p = ['#1B3A6B','#0d6efd','#198754','#fd7e14','#6f42c1','#d63384','#0dcaf0','#20c997'];
        return p[Math.abs(h) % p.length];
    }

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* ── Bloqueo: lista guardada en localStorage ── */
    function cliBlockedList() {
        try { return JSON.parse(localStorage.getItem('cliBlocked_' + MI_USUARIO) || '[]'); }
        catch (_) { return []; }
    }
    function cliEstaBlockeado(user) {
        return cliBlockedList().includes(user);
    }

    /* ── Toggle dropdown ── */
    window.cliToggleMenu = function (e) {
        e.stopPropagation();
        const d = document.getElementById('cliDropdown');
        if (d) d.classList.toggle('open');
    };
    document.addEventListener('click', function () {
        const d = document.getElementById('cliDropdown');
        if (d) d.classList.remove('open');
    });

    /* ── Bloquear / Desbloquear ── */
    window.cliBloquear = function () {
        const user = chatActivo;
        const list = cliBlockedList();
        const idx  = list.indexOf(user);
        const accion = idx === -1 ? 'bloquear' : 'desbloquear';
        const texto  = idx === -1
            ? `Has bloqueado a ${user}. Ya no recibirás sus mensajes privados.`
            : `Has desbloqueado a ${user}.`;

        // Notificar servidor para que avise al otro usuario
        if (conectado && ws && !ES_INVITADO) {
            ws.send(JSON.stringify({ tipo: accion, usuario: user }));
        }

        if (idx === -1) list.push(user); else list.splice(idx, 1);
        localStorage.setItem('cliBlocked_' + MI_USUARIO, JSON.stringify(list));
        cliAgregarMensaje(user, { tipo: 'sistema', texto }, false);
        cliMostrarChat(user);
        cliRenderContactos();
        const d = document.getElementById('cliDropdown');
        if (d) d.classList.remove('open');
    };

    /* ── Borrar historial del chat activo ── */
    window.cliBorrarChat = function () {
        if (!confirm('¿Borrar el historial de este chat? Esta acción no se puede deshacer.')) return;
        historiales[chatActivo]  = [];
        histCargado[chatActivo]  = true;
        document.getElementById('cliMessages').innerHTML = '';
        const d = document.getElementById('cliDropdown');
        if (d) d.classList.remove('open');
    };

    /* ── Eliminar contacto y volver al grupo ── */
    window.cliEliminarContacto = function () {
        const user = chatActivo;
        if (!confirm(`¿Eliminar a ${user} de tus contactos?`)) return;
        const idx = contactos.indexOf(user);
        if (idx !== -1) contactos.splice(idx, 1);
        delete historiales[user];
        delete noLeidos[user];
        delete histCargado[user];
        chatActivo = TODOS_KEY;
        document.getElementById('cliMsgHeader').style.display  = 'none';
        document.getElementById('cliEmpty').style.display      = 'flex';
        document.getElementById('cliMessages').style.display   = 'none';
        document.getElementById('cliInputWrap').style.display  = 'none';
        cliRenderContactos();
        actualizarBadge();
    };

    // Renderizar contactos iniciales (solo "★ Todos" hasta que conecte)
    cliRenderContactos();
})();
</script>
