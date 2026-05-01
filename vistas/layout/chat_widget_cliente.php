<?php
$chatClienteUsuario = htmlspecialchars($_SESSION['usuario'] ?? '', ENT_QUOTES);
if (!$chatClienteUsuario) return; // no mostrar si no hay sesión
?>
<!-- ══════════════════════════════════════════════════════════
     CHAT WIDGET — Tienda (solo clientes)
══════════════════════════════════════════════════════════════ -->
<style>
#cliChatBtn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 54px; height: 54px;
    border-radius: 50%;
    background: #1B3A6B;
    color: #fff;
    border: none;
    cursor: pointer;
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

#cliChatPanel {
    position: fixed;
    bottom: 92px; right: 28px;
    width: 340px; height: 480px;
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

.cli-header {
    background: #1B3A6B; color: #fff;
    padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
    flex-shrink: 0;
}
.cli-header-ico {
    width: 34px; height: 34px;
    background: rgba(255,255,255,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
}
.cli-header-info { flex: 1; }
.cli-header-title { font-weight: 700; font-size: .9rem; }
.cli-header-sub   { font-size: .7rem; opacity: .75; }
.cli-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #aaa; flex-shrink: 0; transition: background .3s;
}
.cli-dot.online { background: #25d366; }
.cli-close {
    background: none; border: none; color: rgba(255,255,255,.8);
    font-size: 1.2rem; cursor: pointer; padding: 0 4px;
}
.cli-close:hover { color: #fff; }

.cli-messages {
    flex: 1; overflow-y: auto;
    padding: 12px 14px;
    display: flex; flex-direction: column; gap: 4px;
    background: #efeae2;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d9d9d9' fill-opacity='0.18'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.cli-messages::-webkit-scrollbar { width: 4px; }
.cli-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

.cli-bubble-wrap { display: flex; flex-direction: column; }
.cli-bubble-wrap.me    { align-items: flex-end; }
.cli-bubble-wrap.other { align-items: flex-start; }
.cli-sender {
    font-size: .68rem; font-weight: 600; color: #1B3A6B;
    margin-bottom: 2px; padding: 0 4px;
}
.cli-bubble {
    max-width: 78%; padding: 7px 12px;
    border-radius: 10px; font-size: .84rem;
    line-height: 1.4; word-break: break-word;
}
.cli-bubble-wrap.me    .cli-bubble { background: #1B3A6B; color: #fff; border-bottom-right-radius: 3px; }
.cli-bubble-wrap.other .cli-bubble { background: #fff; color: #111; border-bottom-left-radius: 3px; box-shadow: 0 1px 2px rgba(0,0,0,.1); }
.cli-time { font-size: .62rem; opacity: .6; margin-top: 2px; padding: 0 4px; }
.cli-system {
    text-align: center; font-size: .7rem; color: #888;
    background: rgba(255,255,255,.7); border-radius: 10px;
    padding: 3px 10px; align-self: center; margin: 3px 0;
}

.cli-input-wrap {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px;
    background: #f0f2f5;
    border-top: 1px solid #e5e5e5;
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

@media (max-width: 480px) {
    #cliChatPanel { width: calc(100vw - 20px); right: 10px; left: 10px; }
}
</style>

<!-- Botón flotante -->
<button id="cliChatBtn" title="Chat con clientes" onclick="cliToggle()">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
    </svg>
    <span class="cli-badge" id="cliBadge">0</span>
</button>

<!-- Panel -->
<div id="cliChatPanel">
    <div class="cli-header">
        <div class="cli-header-ico">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
            </svg>
        </div>
        <div class="cli-header-info">
            <div class="cli-header-title">Chat Clientes</div>
            <div class="cli-header-sub">Conectado como <strong><?php echo $chatClienteUsuario; ?></strong></div>
        </div>
        <span class="cli-dot" id="cliDot"></span>
        <button class="cli-close" onclick="cliToggle()">&#x2715;</button>
    </div>

    <div class="cli-messages" id="cliMessages"></div>

    <div class="cli-input-wrap">
        <input id="cliInput" type="text" placeholder="Escribe un mensaje..." maxlength="500"
               onkeydown="if(event.key==='Enter')cliEnviar()">
        <button class="cli-send-btn" id="cliSendBtn" onclick="cliEnviar()" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function () {
    const MI_USUARIO = <?php echo json_encode($chatClienteUsuario); ?>;
    const WS_URL     = 'ws://' + window.location.hostname + ':2346';

    let ws        = null;
    let conectado = false;
    let abierto   = false;
    let noLeidos  = 0;
    let histCargado = false;

    window.cliToggle = function () {
        abierto = !abierto;
        document.getElementById('cliChatPanel').classList.toggle('open', abierto);
        if (abierto && !ws) cliConectar();
        if (abierto) {
            noLeidos = 0;
            actualizarBadge();
            if (!histCargado) cliFetchHistorial();
        }
    };

    function cliConectar() {
        ws = new WebSocket(WS_URL);

        ws.onopen = function () {
            conectado = true;
            ws.send(JSON.stringify({ nombre: MI_USUARIO, canal: 'clientes' }));
            document.getElementById('cliDot').classList.add('online');
            document.getElementById('cliSendBtn').disabled = false;
            document.getElementById('cliInput').placeholder = 'Escribe un mensaje...';
        };

        ws.onmessage = function (e) {
            try { cliProcesar(JSON.parse(e.data)); } catch (_) {}
        };

        ws.onclose = function () {
            conectado = false;
            document.getElementById('cliDot').classList.remove('online');
            document.getElementById('cliSendBtn').disabled = true;
            document.getElementById('cliInput').placeholder = 'Sin conexión...';
            setTimeout(() => { if (abierto) cliConectar(); }, 4000);
        };

        ws.onerror = function () { ws.close(); };
    }

    function cliProcesar(msg) {
        // Solo procesamos mensajes del canal clientes
        if (msg.canal && msg.canal !== 'clientes') return;

        switch (msg.tipo) {
            case 'usuarios':
                // no necesitamos lista de contactos en el widget simple
                break;
            case 'mensaje':
                cliAgregarBurbuja({
                    tipo:  msg.de === MI_USUARIO ? 'yo' : 'otro',
                    de:    msg.de,
                    texto: msg.texto,
                    hora:  ahora()
                }, msg.de !== MI_USUARIO);
                break;
            case 'sistema':
                cliAgregarBurbuja({ tipo: 'sistema', texto: msg.texto });
                break;
        }
    }

    function cliAgregarBurbuja(entry, badge = true) {
        const area = document.getElementById('cliMessages');
        const wrap = document.createElement('div');

        if (entry.tipo === 'sistema') {
            wrap.className   = 'cli-system';
            wrap.textContent = entry.texto;
        } else {
            const esYo = entry.tipo === 'yo' || entry.de === MI_USUARIO;
            wrap.className = 'cli-bubble-wrap ' + (esYo ? 'me' : 'other');

            if (entry.de) {
                const sender = document.createElement('div');
                sender.className   = 'cli-sender';
                sender.textContent = esYo ? 'Tú' : entry.de;
                sender.style.textAlign = esYo ? 'right' : 'left';
                wrap.appendChild(sender);
            }

            const burbuja = document.createElement('div');
            burbuja.className   = 'cli-bubble';
            burbuja.textContent = entry.texto;
            wrap.appendChild(burbuja);

            const time = document.createElement('div');
            time.className   = 'cli-time';
            time.textContent = entry.hora || '';
            wrap.appendChild(time);
        }

        area.appendChild(wrap);
        area.scrollTop = area.scrollHeight;

        if (badge && !abierto) {
            noLeidos++;
            actualizarBadge();
        }
    }

    function cliFetchHistorial() {
        const area = document.getElementById('cliMessages');
        area.innerHTML = '<div class="cli-system" style="margin:auto 0;">Cargando historial...</div>';

        fetch('api/chat_historial_cliente.php')
            .then(r => r.json())
            .then(msgs => {
                area.innerHTML = '';
                msgs.forEach(m => cliAgregarBurbuja(m, false));
                histCargado = true;
                area.scrollTop = area.scrollHeight;
            })
            .catch(() => { area.innerHTML = ''; histCargado = true; });
    }

    window.cliEnviar = function () {
        if (!conectado || !ws) return;
        const input = document.getElementById('cliInput');
        const txt   = input.value.trim();
        if (!txt) return;
        ws.send(JSON.stringify({ texto: txt, canal: 'clientes' }));
        input.value = '';
        input.focus();
    };

    function actualizarBadge() {
        const badge = document.getElementById('cliBadge');
        badge.textContent = noLeidos > 99 ? '99+' : noLeidos;
        badge.classList.toggle('visible', noLeidos > 0);
    }

    function ahora() {
        return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }
})();
</script>
