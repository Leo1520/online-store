<?php
$chatUsuario = htmlspecialchars($_SESSION['usuario'] ?? 'Anónimo', ENT_QUOTES);
$chatRol     = $_SESSION['rol'] ?? 'interno';
$chatCanal   = ($chatRol === 'admin') ? 'admin' : 'internos';
$esAdmin     = ($chatCanal === 'admin');
?>
<!-- ══════════════════════════════════════════════════════════
     CHAT WIDGET — Panel Admin (internos + clientes para admin)
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

/* ── Tab bar (admin) ────────────────────────────── */
.ch-tab-bar {
    display: flex;
    background: #162f58;
    padding: 5px 8px;
    gap: 4px;
    flex-shrink: 0;
}
.ch-tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    color: rgba(255,255,255,.6);
    padding: 6px 8px;
    border-radius: 8px;
    font-size: .78rem;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.ch-tab-btn:hover  { color: #fff; background: rgba(255,255,255,.1); }
.ch-tab-btn.active { background: rgba(255,255,255,.2); color: #fff; font-weight: 600; }
.ch-tab-badge {
    background: #dc3545;
    color: #fff;
    font-size: .6rem;
    min-width: 16px; height: 16px;
    border-radius: 8px;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}
.ch-tab-badge.visible { display: inline-flex; }

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

.ch-bubble-wrap { display: flex; flex-direction: column; }
.ch-bubble-wrap.me    { align-items: flex-end; }
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
}
.ch-bubble-wrap.me    .ch-bubble { background: var(--primary); color: #fff; border-bottom-right-radius: 3px; }
.ch-bubble-wrap.other .ch-bubble { background: #fff; color: #111; border-bottom-left-radius: 3px; box-shadow: 0 1px 2px rgba(0,0,0,.1); }
.ch-bubble-time { font-size: .65rem; opacity: .6; margin-top: 2px; padding: 0 4px; }

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
.ch-send-btn:hover    { background: #2751a3; }
.ch-send-btn:disabled { background: #aaa; cursor: default; }

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

    <?php if ($esAdmin): ?>
    <!-- Tab bar — solo admin -->
    <div class="ch-tab-bar">
        <button id="chTab-internos" class="ch-tab-btn active" onclick="chCambiarTab('internos')">
            <i class="bi bi-people-fill"></i> Internos
            <span class="ch-tab-badge" id="chTabBadge-internos"></span>
        </button>
        <button id="chTab-clientes" class="ch-tab-btn" onclick="chCambiarTab('clientes')">
            <i class="bi bi-person-badge-fill"></i> Clientes
            <span class="ch-tab-badge" id="chTabBadge-clientes"></span>
        </button>
    </div>
    <?php endif; ?>

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
            <div class="ch-msg-header" id="chMsgHeader" style="display:none;"></div>
            <div class="ch-empty" id="chEmpty">
                <i class="bi bi-chat-square-dots"></i>
                <p>Selecciona un chat para comenzar</p>
            </div>
            <div class="ch-messages" id="chMessages" style="display:none;"></div>
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
    const MI_USUARIO = <?php echo json_encode($chatUsuario); ?>;
    const MI_CANAL   = <?php echo json_encode($chatCanal); ?>;   // 'admin'|'internos'
    const ES_ADMIN   = <?php echo $esAdmin ? 'true' : 'false'; ?>;
    const WS_URL     = 'ws://' + window.location.hostname + ':2346';

    // Claves de "Todos" por canal
    const TODOS_INT = '★ Todos';
    const TODOS_CLI = '★ Clientes';

    let ws          = null;
    let conectado   = false;
    let tabActivo   = 'internos';   // 'internos' | 'clientes'
    let chatActivo  = TODOS_INT;
    let abierto     = false;

    // Listas de contactos por canal
    let contactosInt = [TODOS_INT];
    let contactosCli = [TODOS_CLI];

    // Para saber a qué canal pertenece cada contacto privado
    let contactCanal = {};
    contactCanal[TODOS_INT] = 'internos';
    contactCanal[TODOS_CLI] = 'clientes';

    let historiales = {};
    let noLeidos    = {};
    let histCargado = {};

    historiales[TODOS_INT] = [];
    historiales[TODOS_CLI] = [];
    noLeidos[TODOS_INT]    = 0;
    noLeidos[TODOS_CLI]    = 0;

    /* ── Toggle del panel ── */
    window.chatToggle = function () {
        abierto = !abierto;
        document.getElementById('chatPanel').classList.toggle('open', abierto);
        if (abierto && !ws) chConectar();
        if (abierto) {
            noLeidos[chatActivo] = 0;
            chRenderContactos();
            actualizarBadgeGlobal();
            if (!histCargado[chatActivo]) chFetchHistorial(chatActivo);
        }
    };

    /* ── Cambiar tab (solo admin) ── */
    window.chCambiarTab = function (tab) {
        tabActivo = tab;
        document.querySelectorAll('.ch-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('chTab-' + tab).classList.add('active');

        // Cambiar chatActivo al "Todos" del tab
        chatActivo = (tab === 'clientes') ? TODOS_CLI : TODOS_INT;
        noLeidos[chatActivo] = 0;
        chRenderContactos();
        actualizarBadgeGlobal();
        chMostrarChat(chatActivo);
        if (!histCargado[chatActivo]) chFetchHistorial(chatActivo);
    };

    /* ── Conexión WebSocket ── */
    function chConectar() {
        ws = new WebSocket(WS_URL);

        ws.onopen = function () {
            conectado = true;
            ws.send(JSON.stringify({ nombre: MI_USUARIO, canal: MI_CANAL }));
            document.getElementById('chDot').classList.add('online');
            document.getElementById('chDot').title = 'Conectado';
            document.getElementById('chSendBtn').disabled = false;
            document.getElementById('chInput').placeholder = 'Escribe un mensaje...';
        };

        ws.onmessage = function (e) {
            try { chProcesar(JSON.parse(e.data)); } catch (_) {}
        };

        ws.onclose = function () {
            conectado = false;
            document.getElementById('chDot').classList.remove('online');
            document.getElementById('chDot').title = 'Sin conexión';
            document.getElementById('chSendBtn').disabled = true;
            document.getElementById('chInput').placeholder = 'Sin conexión...';
            setTimeout(() => { if (abierto) chConectar(); }, 4000);
        };

        ws.onerror = function () { ws.close(); };
    }

    /* ── Procesar mensaje entrante ── */
    function chProcesar(msg) {
        switch (msg.tipo) {

            case 'usuarios':
                // Actualizar listas
                const prevInt = contactosInt.slice(1);
                const prevCli = contactosCli.slice(1);

                contactosInt = [TODOS_INT];
                (msg.internos || []).forEach(n => {
                    if (n !== MI_USUARIO) {
                        contactosInt.push(n);
                        contactCanal[n] = 'internos';
                        if (!historiales[n]) historiales[n] = [];
                        if (noLeidos[n] === undefined) noLeidos[n] = 0;
                    }
                });
                contactosCli = [TODOS_CLI];
                (msg.clientes || []).forEach(n => {
                    if (n !== MI_USUARIO) {
                        contactosCli.push(n);
                        contactCanal[n] = 'clientes';
                        if (!historiales[n]) historiales[n] = [];
                        if (noLeidos[n] === undefined) noLeidos[n] = 0;
                    }
                });
                chRenderContactos();
                break;

            case 'mensaje': {
                const canal   = msg.canal || 'internos';
                const todosKey = (canal === 'clientes') ? TODOS_CLI : TODOS_INT;
                chAgregarMensaje(todosKey, {
                    tipo:  msg.de === MI_USUARIO ? 'yo' : 'otro',
                    de:    msg.de,
                    texto: msg.texto,
                    hora:  ahora()
                }, msg.de !== MI_USUARIO);
                // Badge de tab si no es la tab activa
                if (ES_ADMIN && canal !== tabActivo) {
                    actualizarBadgeTab(canal);
                }
                break;
            }

            case 'sistema': {
                const canal    = msg.canal || 'internos';
                const todosKey = (canal === 'clientes') ? TODOS_CLI : TODOS_INT;
                chAgregarMensaje(todosKey, { tipo: 'sistema', texto: msg.texto, hora: ahora() });
                break;
            }

            case 'privado': {
                const canal = msg.canal || 'internos';
                if (!historiales[msg.de]) historiales[msg.de] = [];
                if (noLeidos[msg.de] === undefined) noLeidos[msg.de] = 0;
                contactCanal[msg.de] = canal;
                const lista = (canal === 'clientes') ? contactosCli : contactosInt;
                if (!lista.includes(msg.de)) lista.push(msg.de);
                chAgregarMensaje(msg.de, { tipo: 'otro', de: msg.de, texto: msg.texto, hora: ahora() });
                if (ES_ADMIN && canal !== tabActivo) actualizarBadgeTab(canal);
                break;
            }

            case 'privado_enviado': {
                const canal = msg.canal || 'internos';
                if (!historiales[msg.para]) historiales[msg.para] = [];
                chAgregarMensaje(msg.para, { tipo: 'yo', texto: msg.texto, hora: ahora() }, false);
                break;
            }
        }
    }

    /* ── Badge por tab ── */
    function actualizarBadgeTab(canal) {
        if (!ES_ADMIN) return;
        const badge = document.getElementById('chTabBadge-' + canal);
        if (!badge) return;
        const lista = (canal === 'clientes') ? contactosCli : contactosInt;
        let total = 0;
        lista.forEach(c => { total += noLeidos[c] || 0; });
        badge.textContent = total > 99 ? '99+' : total;
        badge.classList.toggle('visible', total > 0);
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
        const esTodos = (chatActivo === TODOS_INT || chatActivo === TODOS_CLI);

        if (entry.tipo === 'sistema') {
            wrap.className = 'ch-system-msg';
            wrap.textContent = entry.texto;
        } else {
            const esYo = entry.tipo === 'yo' || entry.de === MI_USUARIO;
            wrap.className = 'ch-bubble-wrap ' + (esYo ? 'me' : 'other');

            if (entry.de && esTodos) {
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

    /* ── Mostrar área de mensajes para un chat ── */
    function chMostrarChat(chat) {
        const isTodos = (chat === TODOS_INT || chat === TODOS_CLI);
        const header  = document.getElementById('chMsgHeader');
        header.style.display = 'flex';
        const color   = avatarColor(chat);
        const inicial = (chat === TODOS_INT || chat === TODOS_CLI) ? '★' : chat.charAt(0).toUpperCase();
        header.innerHTML = `
            <div class="ch-avatar" style="background:${color};width:36px;height:36px;font-size:.85rem;">${inicial}</div>
            <div>
                <div class="ch-msg-header-name">${chat}</div>
                <div class="ch-msg-header-sub">${isTodos ? 'Chat grupal' : 'Mensaje privado'}</div>
            </div>`;

        document.getElementById('chEmpty').style.display     = 'none';
        document.getElementById('chMessages').style.display  = 'flex';
        document.getElementById('chInputWrap').style.display = 'flex';
    }

    /* ── Cambiar chat activo ── */
    function chCambiar(chat) {
        chatActivo = chat;
        noLeidos[chat] = 0;
        chRenderContactos();
        actualizarBadgeGlobal();
        if (ES_ADMIN) actualizarBadgeTab(tabActivo);
        chMostrarChat(chat);
        if (!histCargado[chat]) {
            chFetchHistorial(chat);
        } else {
            chCargarHistorial();
        }
    }

    /* ── Fetch historial desde BD ── */
    function chFetchHistorial(chat) {
        const esTodos = (chat === TODOS_INT || chat === TODOS_CLI);
        const canal   = (chat === TODOS_CLI) ? 'clientes' : 'internos';
        const chatParam = esTodos ? 'todos' : chat;
        const area    = document.getElementById('chMessages');
        if (chatActivo === chat) {
            area.innerHTML = '<div class="ch-system-msg" style="margin:auto;">Cargando historial...</div>';
        }

        fetch(`chat_historial.php?chat=${encodeURIComponent(chatParam)}&canal=${canal}`)
            .then(r => r.json())
            .then(mensajes => {
                if (!historiales[chat]) historiales[chat] = [];
                const sesion = historiales[chat];
                historiales[chat] = mensajes.concat(sesion);
                histCargado[chat] = true;
                if (chatActivo === chat) chCargarHistorial();
            })
            .catch(() => {
                histCargado[chat] = true;
                if (chatActivo === chat) chCargarHistorial();
            });
    }

    /* ── Renderizar lista de contactos del tab activo ── */
    window.chFiltrar = function (q) { chRenderContactos(q.toLowerCase()); };

    function chRenderContactos(filtro = '') {
        const lista      = document.getElementById('chContactList');
        const contactos  = (tabActivo === 'clientes') ? contactosCli : contactosInt;
        lista.innerHTML  = '';

        contactos.forEach(chat => {
            if (filtro && !chat.toLowerCase().includes(filtro)) return;
            const unread  = noLeidos[chat] || 0;
            const hist    = historiales[chat] || [];
            const ultimo  = hist.length ? hist[hist.length - 1] : null;
            const preview = ultimo ? (ultimo.tipo === 'sistema' ? ultimo.texto
                : (ultimo.tipo === 'yo' ? 'Tú: ' + ultimo.texto
                : (ultimo.de ? ultimo.de + ': ' : '') + ultimo.texto)) : 'Sin mensajes';
            const color   = avatarColor(chat);
            const isTodos = (chat === TODOS_INT || chat === TODOS_CLI);
            const inicial = isTodos ? '★' : chat.charAt(0).toUpperCase();

            const item = document.createElement('div');
            item.className = 'ch-contact-item' + (chat === chatActivo ? ' active' : '');
            item.innerHTML = `
                <div class="ch-avatar" style="background:${color};">${inicial}</div>
                <div class="ch-contact-info">
                    <div class="ch-contact-name">${chat}</div>
                    <div class="ch-contact-prev">${escHtml(preview.length > 35 ? preview.slice(0,32) + '…' : preview)}</div>
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

        const canal    = (chatActivo === TODOS_CLI || contactCanal[chatActivo] === 'clientes') ? 'clientes' : 'internos';
        const esTodos  = (chatActivo === TODOS_INT || chatActivo === TODOS_CLI);

        if (esTodos) {
            ws.send(JSON.stringify({ texto: txt, canal: canal }));
        } else {
            ws.send(JSON.stringify({ para: chatActivo, texto: txt, canal: canal }));
        }
        input.value = '';
        input.focus();
    };

    /* ── Badge global ── */
    function actualizarBadgeGlobal() {
        const total = Object.values(noLeidos).reduce((a, b) => a + b, 0);
        const badge = document.getElementById('chatGlobalBadge');
        badge.textContent = total > 99 ? '99+' : total;
        badge.classList.toggle('visible', total > 0);
    }

    function chScrollAbajo() {
        const area = document.getElementById('chMessages');
        area.scrollTop = area.scrollHeight;
    }

    function ahora() {
        return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }

    function avatarColor(name) {
        if (name === TODOS_INT) return '#1d7a35';
        if (name === TODOS_CLI) return '#b45309';
        let h = 0;
        for (let i = 0; i < name.length; i++) h = name.charCodeAt(i) + ((h << 5) - h);
        const paleta = ['#1B3A6B','#0d6efd','#198754','#fd7e14','#6f42c1','#d63384','#0dcaf0','#20c997'];
        return paleta[Math.abs(h) % paleta.length];
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    chRenderContactos();
})();
</script>
