<?php
$chatUsuario = htmlspecialchars($_SESSION['usuario'] ?? 'Anónimo', ENT_QUOTES);
?>
<!-- ══════════════════════════════════════════════════════════
     CHAT WIDGET — Flotante estilo WhatsApp
══════════════════════════════════════════════════════════════ -->
<style>
/* ── Botón flotante ─────────────────────────────── */
#chatFloatBtn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    box-shadow: 0 4px 18px rgba(27,58,107,.45);
    z-index: 2000;
    transition: transform .2s, background .2s;
}
#chatFloatBtn:hover { background: #2751a3; transform: scale(1.08); }
#chatFloatBtn .chat-badge {
    position: absolute;
    top: -4px; right: -4px;
    background: #dc3545;
    color: #fff;
    font-size: .62rem;
    font-weight: 700;
    width: 20px; height: 20px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
}
#chatFloatBtn .chat-badge.visible { display: flex; }

/* ── Panel principal ────────────────────────────── */
#chatPanel {
    position: fixed;
    bottom: 94px;
    right: 28px;
    width: 720px;
    height: 520px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.22);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 1999;
    animation: chatSlideUp .22s ease;
}
#chatPanel.open { display: flex; }
@keyframes chatSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Header ─────────────────────────────────────── */
.ch-header {
    background: var(--primary);
    color: #fff;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.ch-header-ico {
    width: 36px; height: 36px;
    background: rgba(255,255,255,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.ch-header-info { flex: 1; }
.ch-header-info .ch-title  { font-weight: 700; font-size: .95rem; line-height: 1.2; }
.ch-header-info .ch-sub    { font-size: .72rem; opacity: .75; }
.ch-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    background: #aaa;
    flex-shrink: 0;
    transition: background .3s;
}
.ch-dot.online { background: #25d366; }
.ch-close {
    background: none; border: none; color: rgba(255,255,255,.8);
    font-size: 1.3rem; cursor: pointer; line-height: 1;
    padding: 0 4px;
}
.ch-close:hover { color: #fff; }

/* ── Cuerpo ─────────────────────────────────────── */
.ch-body {
    display: flex;
    flex: 1;
    overflow: hidden;
}

/* ── Columna izquierda: contactos ───────────────── */
.ch-contacts {
    width: 240px;
    border-right: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    background: #fafafa;
    flex-shrink: 0;
}
.ch-search {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f0f0;
}
.ch-search input {
    width: 100%;
    border: none;
    background: #f0f2f5;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: .82rem;
    outline: none;
    color: #333;
}
.ch-contact-list { flex: 1; overflow-y: auto; }
.ch-contact-list::-webkit-scrollbar { width: 3px; }
.ch-contact-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

.ch-contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f5f5f5;
    transition: background .12s;
}
.ch-contact-item:hover   { background: #f0f2f5; }
.ch-contact-item.active  { background: #e8edf5; }

.ch-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}
.ch-contact-info { flex: 1; min-width: 0; }
.ch-contact-name {
    font-size: .84rem;
    font-weight: 600;
    color: #111;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ch-contact-prev {
    font-size: .72rem;
    color: #888;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}
.ch-unread {
    background: #25d366;
    color: #fff;
    font-size: .65rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    flex-shrink: 0;
}
.ch-unread.visible { display: flex; }

/* ── Columna derecha: mensajes ──────────────────── */
.ch-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #efeae2;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d9d9d9' fill-opacity='0.18'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    overflow: hidden;
}
.ch-msg-header {
    background: #f0f2f5;
    border-bottom: 1px solid #e5e5e5;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.ch-msg-header .ch-avatar { width: 36px; height: 36px; font-size: .85rem; }
.ch-msg-header-name { font-weight: 600; font-size: .88rem; color: #111; }
.ch-msg-header-sub  { font-size: .72rem; color: #888; }

.ch-messages {
    flex: 1;
    overflow-y: auto;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.ch-messages::-webkit-scrollbar { width: 4px; }
.ch-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

/* Burbujas */
.ch-bubble-wrap {
    display: flex;
    flex-direction: column;
}
.ch-bubble-wrap.me  { align-items: flex-end; }
.ch-bubble-wrap.other { align-items: flex-start; }

.ch-sender-name {
    font-size: .7rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 2px;
    padding: 0 4px;
}
.ch-bubble {
    max-width: 70%;
    padding: 7px 12px;
    border-radius: 10px;
    font-size: .84rem;
    line-height: 1.4;
    word-break: break-word;
    position: relative;
}
.ch-bubble-wrap.me .ch-bubble {
    background: var(--primary);
    color: #fff;
    border-bottom-right-radius: 3px;
}
.ch-bubble-wrap.other .ch-bubble {
    background: #fff;
    color: #111;
    border-bottom-left-radius: 3px;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
}
.ch-bubble-time {
    font-size: .65rem;
    opacity: .6;
    margin-top: 2px;
    padding: 0 4px;
}

.ch-system-msg {
    text-align: center;
    font-size: .72rem;
    color: #888;
    background: rgba(255,255,255,.7);
    border-radius: 10px;
    padding: 3px 12px;
    align-self: center;
    margin: 4px 0;
}

/* Input */
.ch-input-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #f0f2f5;
    border-top: 1px solid #e5e5e5;
    flex-shrink: 0;
}
.ch-input-wrap input {
    flex: 1;
    border: none;
    background: #fff;
    border-radius: 22px;
    padding: 8px 16px;
    font-size: .87rem;
    outline: none;
    color: #333;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.ch-send-btn {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
    flex-shrink: 0;
    transition: background .15s;
}
.ch-send-btn:hover  { background: #2751a3; }
.ch-send-btn:disabled { background: #aaa; cursor: default; }

/* Estado vacío */
.ch-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #aaa;
    gap: 10px;
}
.ch-empty i { font-size: 3rem; opacity: .3; }
.ch-empty p { font-size: .85rem; }

/* Responsive */
@media (max-width: 800px) {
    #chatPanel { width: calc(100vw - 20px); right: 10px; left: 10px; }
    .ch-contacts { width: 200px; }
}
@media (max-width: 560px) {
    #chatPanel { height: 480px; }
    .ch-contacts { display: none; }
    #chatPanel.show-contacts .ch-contacts { display: flex; width: 100%; }
    #chatPanel.show-contacts .ch-right { display: none; }
}
</style>

<!-- ── Botón flotante ── -->
<button id="chatFloatBtn" title="Abrir chat" onclick="chatToggle()">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="chat-badge" id="chatGlobalBadge">0</span>
</button>

<!-- ── Panel chat ── -->
<div id="chatPanel">

    <!-- Header -->
    <div class="ch-header">
        <div class="ch-header-ico"><i class="bi bi-chat-dots-fill"></i></div>
        <div class="ch-header-info">
            <div class="ch-title">Chat ElectroHogar</div>
            <div class="ch-sub">Conectado como <strong><?php echo $chatUsuario; ?></strong></div>
        </div>
        <span class="ch-dot" id="chDot" title="Sin conexión"></span>
        <button class="ch-close" onclick="chatToggle()" title="Cerrar">&#x2715;</button>
    </div>

    <!-- Cuerpo -->
    <div class="ch-body">

        <!-- Columna izquierda: contactos -->
        <div class="ch-contacts">
            <div class="ch-search">
                <input id="chSearchInput" placeholder="Buscar contacto..." oninput="chFiltrar(this.value)">
            </div>
            <div class="ch-contact-list" id="chContactList"></div>
        </div>

        <!-- Columna derecha -->
        <div class="ch-right" id="chRight">

            <!-- Header del chat activo -->
            <div class="ch-msg-header" id="chMsgHeader" style="display:none;"></div>

            <!-- Estado vacío inicial -->
            <div class="ch-empty" id="chEmpty">
                <i class="bi bi-chat-square-dots"></i>
                <p>Selecciona un chat para comenzar</p>
            </div>

            <!-- Mensajes -->
            <div class="ch-messages" id="chMessages" style="display:none;"></div>

            <!-- Input -->
            <div class="ch-input-wrap" id="chInputWrap" style="display:none;">
                <input id="chInput" type="text" placeholder="Escribe un mensaje..." maxlength="500"
                       onkeydown="if(event.key==='Enter')chEnviar()">
                <button class="ch-send-btn" id="chSendBtn" onclick="chEnviar()" disabled title="Enviar">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    /* ── Constantes ── */
    const MI_USUARIO  = <?php echo json_encode($chatUsuario); ?>;
    const WS_URL      = 'ws://localhost:2346';
    const TODOS       = '★ Todos';

    /* ── Estado ── */
    let ws          = null;
    let conectado   = false;
    let chatActivo  = TODOS;
    let historiales = {};   // { chat: [{tipo,de,texto,hora}] }
    let noLeidos    = {};   // { chat: number }
    let contactos   = [];   // ['★ Todos', 'usuario1', ...]
    let abierto     = false;

    historiales[TODOS] = [];
    noLeidos[TODOS]    = 0;
    contactos          = [TODOS];

    /* ── Toggle del panel ── */
    window.chatToggle = function () {
        abierto = !abierto;
        document.getElementById('chatPanel').classList.toggle('open', abierto);
        if (abierto && !ws) chConectar();
        if (abierto) {
            noLeidos[chatActivo] = 0;
            chRenderContactos();
            actualizarBadgeGlobal();
        }
    };

    /* ── Conexión WebSocket ── */
    function chConectar() {
        ws = new WebSocket(WS_URL);

        ws.onopen = function () {
            conectado = true;
            ws.send(MI_USUARIO);
            document.getElementById('chDot').classList.add('online');
            document.getElementById('chDot').title = 'Conectado';
            document.getElementById('chSendBtn').disabled = false;
            document.getElementById('chInput').placeholder = 'Escribe un mensaje...';
        };

        ws.onmessage = function (e) {
            try {
                const msg = JSON.parse(e.data);
                chProcesar(msg);
            } catch (_) {}
        };

        ws.onclose = function () {
            conectado = false;
            document.getElementById('chDot').classList.remove('online');
            document.getElementById('chDot').title = 'Sin conexión';
            document.getElementById('chSendBtn').disabled = true;
            document.getElementById('chInput').placeholder = 'Sin conexión...';
            // Reconectar en 4s
            setTimeout(() => { if (abierto) chConectar(); }, 4000);
        };

        ws.onerror = function () { ws.close(); };
    }

    /* ── Procesar mensaje entrante ── */
    function chProcesar(msg) {
        switch (msg.tipo) {

            case 'usuarios':
                contactos = [TODOS];
                (msg.lista || []).forEach(n => {
                    if (n !== MI_USUARIO) {
                        contactos.push(n);
                        if (!historiales[n]) historiales[n] = [];
                        if (noLeidos[n] === undefined) noLeidos[n] = 0;
                    }
                });
                chRenderContactos();
                break;

            case 'mensaje':
                chAgregarMensaje(TODOS, {
                    tipo:  msg.de === MI_USUARIO ? 'yo' : 'otro',
                    de:    msg.de,
                    texto: msg.texto,
                    hora:  ahora()
                }, msg.de !== MI_USUARIO);
                break;

            case 'sistema':
                chAgregarMensaje(TODOS, { tipo: 'sistema', texto: msg.texto, hora: ahora() });
                break;

            case 'privado':
                if (!historiales[msg.de]) historiales[msg.de] = [];
                if (noLeidos[msg.de] === undefined) noLeidos[msg.de] = 0;
                if (!contactos.includes(msg.de)) { contactos.push(msg.de); }
                chAgregarMensaje(msg.de, { tipo: 'otro', de: msg.de, texto: msg.texto, hora: ahora() });
                break;

            case 'privado_enviado':
                if (!historiales[msg.para]) historiales[msg.para] = [];
                chAgregarMensaje(msg.para, { tipo: 'yo', texto: msg.texto, hora: ahora() }, false);
                break;
        }
    }

    /* ── Agregar mensaje al historial ── */
    function chAgregarMensaje(chat, entry, badge = true) {
        if (!historiales[chat]) historiales[chat] = [];
        historiales[chat].push(entry);

        if (chat === chatActivo && abierto) {
            chRenderBurbuja(entry);
            chScrollAbajo();
            noLeidos[chat] = 0;
        } else if (badge) {
            noLeidos[chat] = (noLeidos[chat] || 0) + 1;
            actualizarBadgeGlobal();
        }
        chRenderContactos();
    }

    /* ── Renderizar una burbuja ── */
    function chRenderBurbuja(entry) {
        const area = document.getElementById('chMessages');
        const wrap = document.createElement('div');

        if (entry.tipo === 'sistema') {
            wrap.className = 'ch-system-msg';
            wrap.textContent = entry.texto;
        } else {
            const esYo = entry.tipo === 'yo' || entry.de === MI_USUARIO;
            wrap.className = 'ch-bubble-wrap ' + (esYo ? 'me' : 'other');

            if (entry.de && chatActivo === TODOS) {
                const name = document.createElement('div');
                name.className = 'ch-sender-name';
                name.textContent = esYo ? 'Tú' : entry.de;
                name.style.textAlign = esYo ? 'right' : 'left';
                wrap.appendChild(name);
            }

            const burbuja = document.createElement('div');
            burbuja.className = 'ch-bubble';
            burbuja.textContent = entry.texto;
            wrap.appendChild(burbuja);

            const time = document.createElement('div');
            time.className = 'ch-bubble-time';
            time.textContent = entry.hora || '';
            wrap.appendChild(time);
        }
        area.appendChild(wrap);
    }

    /* ── Cargar historial del chat activo ── */
    function chCargarHistorial() {
        const area = document.getElementById('chMessages');
        area.innerHTML = '';
        (historiales[chatActivo] || []).forEach(e => chRenderBurbuja(e));
        chScrollAbajo();
    }

    /* ── Cambiar chat activo ── */
    function chCambiar(chat) {
        chatActivo = chat;
        noLeidos[chat] = 0;
        chRenderContactos();
        actualizarBadgeGlobal();

        // Header
        const header = document.getElementById('chMsgHeader');
        header.style.display = 'flex';
        const color = avatarColor(chat);
        const inicial = chat === TODOS ? '★' : chat.charAt(0).toUpperCase();
        header.innerHTML = `
            <div class="ch-avatar" style="background:${color};width:36px;height:36px;font-size:.85rem;">${inicial}</div>
            <div>
                <div class="ch-msg-header-name">${chat}</div>
                <div class="ch-msg-header-sub">${chat === TODOS ? 'Chat grupal' : 'Mensaje privado'}</div>
            </div>`;

        // Mostrar mensajes
        document.getElementById('chEmpty').style.display    = 'none';
        document.getElementById('chMessages').style.display = 'flex';
        document.getElementById('chInputWrap').style.display = 'flex';

        chCargarHistorial();
    }

    /* ── Renderizar lista de contactos ── */
    window.chFiltrar = function (q) {
        chRenderContactos(q.toLowerCase());
    };

    function chRenderContactos(filtro = '') {
        const lista = document.getElementById('chContactList');
        lista.innerHTML = '';
        contactos.forEach(chat => {
            if (filtro && !chat.toLowerCase().includes(filtro)) return;
            const unread  = noLeidos[chat] || 0;
            const hist    = historiales[chat] || [];
            const ultimo  = hist.length ? hist[hist.length - 1] : null;
            const preview = ultimo ? (ultimo.tipo === 'sistema' ? ultimo.texto
                : (ultimo.tipo === 'yo' ? 'Tú: ' + ultimo.texto : (ultimo.de ? ultimo.de + ': ' : '') + ultimo.texto)) : 'Sin mensajes';
            const color   = avatarColor(chat);
            const inicial = chat === TODOS ? '★' : chat.charAt(0).toUpperCase();

            const item = document.createElement('div');
            item.className = 'ch-contact-item' + (chat === chatActivo ? ' active' : '');
            item.innerHTML = `
                <div class="ch-avatar" style="background:${color};">${inicial}</div>
                <div class="ch-contact-info">
                    <div class="ch-contact-name">${chat}</div>
                    <div class="ch-contact-prev">${escHtml(preview.length > 35 ? preview.slice(0, 32) + '…' : preview)}</div>
                </div>
                <span class="ch-unread ${unread > 0 ? 'visible' : ''}">${unread > 99 ? '99+' : unread}</span>`;
            item.onclick = () => chCambiar(chat);
            lista.appendChild(item);
        });
    }

    /* ── Enviar mensaje ── */
    window.chEnviar = function () {
        if (!conectado || !ws) return;
        const input = document.getElementById('chInput');
        const txt   = input.value.trim();
        if (!txt) return;

        if (chatActivo === TODOS) {
            ws.send(txt);
            // El servidor hace eco del mensaje → lo recibimos en case 'mensaje' como tipo 'yo'
        } else {
            ws.send('@' + chatActivo + ': ' + txt);
            // privado_enviado lo maneja onmessage
        }
        input.value = '';
        input.focus();
    };

    /* ── Badge global (botón flotante) ── */
    function actualizarBadgeGlobal() {
        const total  = Object.values(noLeidos).reduce((a, b) => a + b, 0);
        const badge  = document.getElementById('chatGlobalBadge');
        badge.textContent = total > 99 ? '99+' : total;
        badge.classList.toggle('visible', total > 0);
    }

    /* ── Scroll al fondo ── */
    function chScrollAbajo() {
        const area = document.getElementById('chMessages');
        area.scrollTop = area.scrollHeight;
    }

    /* ── Hora actual ── */
    function ahora() {
        return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }

    /* ── Color de avatar según nombre ── */
    function avatarColor(name) {
        if (name === TODOS) return '#1d7a35';
        let h = 0;
        for (let i = 0; i < name.length; i++) h = name.charCodeAt(i) + ((h << 5) - h);
        const paleta = ['#1B3A6B','#0d6efd','#198754','#fd7e14','#6f42c1','#d63384','#0dcaf0','#20c997'];
        return paleta[Math.abs(h) % paleta.length];
    }

    /* ── Escapar HTML ── */
    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* Inicializar lista con "Todos" */
    chRenderContactos();

})();
</script>
