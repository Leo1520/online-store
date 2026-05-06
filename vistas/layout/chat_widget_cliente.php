<?php
$chatClienteUsuario = htmlspecialchars($_SESSION['usuario'] ?? '', ENT_QUOTES);
if (!$chatClienteUsuario) return;
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
            <div class="cli-header-sub">Conectado como <strong><?php echo $chatClienteUsuario; ?></strong></div>
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
        </div>
    </div>
</div>

<script>
(function () {
    const MI_USUARIO = <?php echo json_encode($chatClienteUsuario); ?>;
    const WS_URL     = 'ws://' + window.location.hostname + ':2346';
    const TODOS_KEY  = '★ Todos';

    let ws          = null;
    let conectado   = false;
    let abierto     = false;
    let chatActivo  = TODOS_KEY;

    let contactos   = [TODOS_KEY];  // "★ Todos" + usuarios conectados
    let historiales = {};
    let noLeidos    = {};
    let histCargado = {};

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
        const area   = document.getElementById('cliMessages');
        const wrap   = document.createElement('div');
        const esTodos = (chatActivo === TODOS_KEY);

        if (entry.tipo === 'sistema') {
            wrap.className   = 'cli-system-msg';
            wrap.textContent = entry.texto;
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
        const isTodos = (chat === TODOS_KEY);
        const header  = document.getElementById('cliMsgHeader');
        const color   = avatarColor(chat);
        const inicial = isTodos ? '★' : chat.charAt(0).toUpperCase();

        header.style.display = 'flex';
        header.innerHTML = `
            <div class="cli-avatar" style="background:${color};width:36px;height:36px;font-size:.85rem;">${inicial}</div>
            <div>
                <div class="cli-msg-header-name">${escHtml(chat)}</div>
                <div class="cli-msg-header-sub">${isTodos ? 'Chat grupal' : 'Mensaje privado'}</div>
            </div>`;

        document.getElementById('cliEmpty').style.display    = 'none';
        document.getElementById('cliMessages').style.display = 'flex';
        document.getElementById('cliInputWrap').style.display = 'flex';
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

            const item = document.createElement('div');
            item.className = 'cli-contact-item' + (chat === chatActivo ? ' active' : '');
            item.innerHTML = `
                <div class="cli-avatar" style="background:${color};">${inicial}</div>
                <div class="cli-contact-info">
                    <div class="cli-contact-name">${escHtml(isTodos ? '★ Todos' : chat)}</div>
                    <div class="cli-contact-prev">${escHtml(preview.length > 35 ? preview.slice(0,32) + '…' : preview)}</div>
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

    // Renderizar contactos iniciales (solo "★ Todos" hasta que conecte)
    cliRenderContactos();
})();
</script>
