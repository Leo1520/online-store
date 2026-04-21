<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<style>
    .cuenta-wrap { background: var(--gris-bg); min-height: 80vh; padding: 36px 0 60px; }

    /* Cabecera de perfil */
    .perfil-header {
        background: linear-gradient(135deg, var(--azul) 0%, var(--azul-claro) 100%);
        border-radius: 16px; padding: 28px 32px;
        display: flex; align-items: center; gap: 24px;
        color: #fff; margin-bottom: 28px;
        box-shadow: 0 4px 20px rgba(27,58,107,.25);
    }
    .perfil-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        background: var(--amarillo); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 900; flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(0,0,0,.2);
    }
    .perfil-header h4 { margin: 0; font-weight: 800; font-size: 20px; }
    .perfil-header p  { margin: 4px 0 0; opacity: .8; font-size: 13px; }
    .perfil-header .badge-usuario {
        background: rgba(255,255,255,.18); border-radius: 20px;
        padding: 3px 12px; font-size: 12px; font-weight: 600; letter-spacing: .5px;
    }

    /* Tabs */
    .cuenta-tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
    .cuenta-tab {
        padding: 10px 20px; border-radius: 10px; cursor: pointer;
        font-size: 13px; font-weight: 600; border: 2px solid #e0e6f0;
        background: #fff; color: #888; transition: all .2s;
        display: flex; align-items: center; gap: 7px;
    }
    .cuenta-tab i { font-size: 16px; }
    .cuenta-tab.activo { background: var(--azul); border-color: var(--azul); color: #fff; }
    .cuenta-tab:hover:not(.activo) { border-color: var(--azul); color: var(--azul); }

    .cuenta-panel { display: none; }
    .cuenta-panel.activo { display: block; }

    /* Tarjetas genéricas */
    .c-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden;
    }
    .c-card-header {
        padding: 16px 22px; border-bottom: 2px solid #f0f4ff;
        font-size: 14px; font-weight: 700; color: var(--azul);
        display: flex; align-items: center; gap: 8px;
    }
    .c-card-header i { color: var(--amarillo); font-size: 18px; }
    .c-card-body { padding: 22px; }

    /* Datos CI / Nombre (solo lectura) */
    .dato-ro {
        background: #f8f9ff; border-radius: 8px; padding: 10px 14px;
        font-size: 13px; color: #555; border: 1px solid #e8ecf8;
    }
    .dato-ro .dato-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 2px; }
    .dato-ro .dato-valor { font-weight: 700; color: var(--azul); }

    /* Inputs del formulario */
    .c-card .form-control {
        border-radius: 8px; font-size: 14px;
        border: 1.5px solid #dde4f0;
    }
    .c-card .form-control:focus { border-color: var(--azul); box-shadow: 0 0 0 .18rem rgba(27,58,107,.13); }
    .c-card label { font-size: 12px; font-weight: 700; color: var(--azul); margin-bottom: 4px; }

    /* Alerta mensaje */
    .alerta-cuenta { border-radius: 10px; font-size: 13px; padding: 12px 16px; margin-bottom: 20px; }

    /* Historial */
    .compra-card {
        background: #fff; border-radius: 12px;
        border: 1.5px solid #e8ecf8;
        margin-bottom: 14px; overflow: hidden;
        transition: box-shadow .2s;
    }
    .compra-card:hover { box-shadow: 0 4px 16px rgba(27,58,107,.1); }
    .compra-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; cursor: pointer; gap: 12px;
        background: #fff;
    }
    .compra-header:hover { background: #f8f9ff; }
    .compra-nro { font-size: 15px; font-weight: 800; color: var(--azul); }
    .compra-fecha { font-size: 12px; color: #999; margin-top: 2px; }
    .compra-monto {
        font-size: 16px; font-weight: 900; color: var(--azul);
        background: #f0f4ff; padding: 6px 14px; border-radius: 20px; white-space: nowrap;
    }
    .compra-badge {
        font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
        background: #d4edda; color: #155724;
    }
    .compra-toggle { color: #aaa; font-size: 18px; transition: transform .25s; }
    .compra-card.abierto .compra-toggle { transform: rotate(180deg); }

    .compra-body { display: none; border-top: 1px solid #f0f0f0; padding: 0 18px 16px; }
    .compra-card.abierto .compra-body { display: block; }

    .compra-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f8f8f8; font-size: 13px; }
    .compra-item:last-of-type { border-bottom: none; }
    .compra-item .ci-nombre { flex: 1; color: #444; font-weight: 500; }
    .compra-item .ci-precio { color: #888; }
    .compra-item .ci-sub { font-weight: 700; color: var(--azul); white-space: nowrap; }

    .compra-acciones { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }

    /* Vacío */
    .historial-vacio { text-align: center; padding: 48px 20px; color: #bbb; }
    .historial-vacio i { font-size: 56px; display: block; margin-bottom: 12px; }
    .historial-vacio p { font-size: 14px; }

    /* Stats */
    .stats-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat-box {
        flex: 1; min-width: 100px; background: #fff; border-radius: 12px;
        padding: 16px; text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,.06); border-top: 3px solid var(--azul);
    }
    .stat-box .val { font-size: 22px; font-weight: 900; color: var(--azul); }
    .stat-box .lbl { font-size: 11px; color: #888; margin-top: 2px; }
</style>

<div class="cuenta-wrap">
<div class="container" style="max-width:940px;">

    <?php if ($mensaje): ?>
    <div class="alert alert-<?php echo htmlspecialchars($tipoMensaje); ?> alerta-cuenta">
        <i class="bi bi-<?php echo $tipoMensaje === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> mr-2"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
    <?php endif; ?>

    <!-- Cabecera del perfil -->
    <div class="perfil-header">
        <div class="perfil-avatar">
            <?php echo strtoupper(substr($_SESSION['usuario'], 0, 2)); ?>
        </div>
        <div style="flex:1;">
            <?php if ($cliente): ?>
                <h4><?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apPaterno']); ?></h4>
                <p><?php echo htmlspecialchars($cliente['correo']); ?></p>
            <?php else: ?>
                <h4><?php echo htmlspecialchars($_SESSION['usuario']); ?></h4>
            <?php endif; ?>
        </div>
        <div class="d-flex flex-column align-items-end gap-1">
            <span class="badge-usuario"><i class="bi bi-person-check-fill mr-1"></i><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
            <div class="mt-2" style="font-size:12px;opacity:.7;">
                <i class="bi bi-bag-check mr-1"></i><?php echo count($historial); ?> compra(s)
            </div>
        </div>
    </div>

    <!-- Tabs de navegación -->
    <div class="cuenta-tabs">
        <div class="cuenta-tab activo" onclick="cambiarTab('perfil', this)">
            <i class="bi bi-person-fill"></i> Mi Perfil
        </div>
        <div class="cuenta-tab" onclick="cambiarTab('compras', this)">
            <i class="bi bi-bag-check-fill"></i> Mis Compras
            <?php if (count($historial) > 0): ?>
                <span style="background:var(--amarillo);color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;">
                    <?php echo count($historial); ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="cuenta-tab" onclick="cambiarTab('seguridad', this)">
            <i class="bi bi-shield-lock-fill"></i> Seguridad
        </div>
    </div>

    <!-- ══ Panel: MI PERFIL ══ -->
    <div id="panelPerfil" class="cuenta-panel activo">
        <?php if ($cliente): ?>
        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="c-card">
                    <div class="c-card-header"><i class="bi bi-person-badge"></i>Datos de identidad</div>
                    <div class="c-card-body">
                        <div class="dato-ro mb-3">
                            <div class="dato-label">Cédula de identidad</div>
                            <div class="dato-valor"><?php echo htmlspecialchars($cliente['ci']); ?></div>
                        </div>
                        <div class="dato-ro mb-3">
                            <div class="dato-label">Nombre completo</div>
                            <div class="dato-valor">
                                <?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apPaterno'] . ' ' . $cliente['apMaterno']); ?>
                            </div>
                        </div>
                        <div class="dato-ro">
                            <div class="dato-label">Usuario</div>
                            <div class="dato-valor"><?php echo htmlspecialchars($_SESSION['usuario']); ?></div>
                        </div>
                        <p class="text-muted mt-3 mb-0" style="font-size:11px;">
                            <i class="bi bi-info-circle mr-1"></i>Estos datos no pueden modificarse. Contacta a soporte si necesitas un cambio.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-7 mb-4">
                <div class="c-card">
                    <div class="c-card-header"><i class="bi bi-pencil-square"></i>Editar información de contacto</div>
                    <div class="c-card-body">
                        <form id="formPerfil" method="POST" action="index.php?pagina=mi_cuenta">
                            <input type="hidden" name="accion" value="actualizar_perfil">
                            <div class="form-group">
                                <label><i class="bi bi-envelope mr-1"></i>Correo electrónico</label>
                                <input type="email" name="correo" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="bi bi-geo-alt mr-1"></i>Dirección</label>
                                <input type="text" name="direccion" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['direccion']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="bi bi-phone mr-1"></i>Número de celular</label>
                                <input type="text" name="nroCelular" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['nroCelular']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-azul btn-block" style="border-radius:8px;padding:10px;">
                                <i class="bi bi-save mr-1"></i>Guardar cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle mr-2"></i>No se encontraron datos de cliente para esta cuenta.</div>
        <?php endif; ?>
    </div>

    <!-- ══ Panel: MIS COMPRAS ══ -->
    <div id="panelCompras" class="cuenta-panel">
        <?php if (empty($historial)): ?>
            <div class="c-card">
                <div class="historial-vacio">
                    <i class="bi bi-bag-x"></i>
                    <p>Aún no tienes compras registradas.</p>
                    <a href="index.php?pagina=inicio" class="btn btn-azul px-4">
                        <i class="bi bi-shop mr-1"></i>Ir a la tienda
                    </a>
                </div>
            </div>
        <?php else: ?>

            <?php foreach ($historial as $compra): ?>
            <div class="compra-card" id="compra<?php echo (int)$compra['nro']; ?>">
                <div class="compra-header" onclick="toggleCompra(<?php echo (int)$compra['nro']; ?>)">
                    <div>
                        <div class="compra-nro">
                            <i class="bi bi-receipt mr-1" style="color:var(--amarillo);"></i>
                            Pedido #<?php echo str_pad((int)$compra['nro'], 6, '0', STR_PAD_LEFT); ?>
                        </div>
                        <div class="compra-fecha">
                            <i class="bi bi-calendar3 mr-1"></i>
                            <?php echo htmlspecialchars($compra['fechaHora']); ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="gap:10px;">
                        <span class="compra-badge"><i class="bi bi-check-circle-fill mr-1"></i>Facturado</span>
                        <span class="compra-monto">Bs. <?php echo number_format((float)$compra['totalMonto'], 2); ?></span>
                        <i class="bi bi-chevron-down compra-toggle"></i>
                    </div>
                </div>
                <div class="compra-body">
                    <?php if (!empty($detalles[$compra['nro']])): ?>
                        <?php foreach ($detalles[$compra['nro']] as $d): ?>
                        <div class="compra-item">
                            <i class="bi bi-box-seam" style="color:var(--azul-claro);font-size:16px;"></i>
                            <span class="ci-nombre"><?php echo htmlspecialchars($d['producto']); ?></span>
                            <span class="ci-precio">Bs. <?php echo number_format((float)$d['precio'], 2); ?> × <?php echo (int)$d['cant']; ?></span>
                            <span class="ci-sub">Bs. <?php echo number_format((float)$d['precio'] * (int)$d['cant'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="compra-acciones">
                        <a href="index.php?pagina=factura&nro=<?php echo (int)$compra['nro']; ?>" target="_blank"
                           class="btn btn-sm btn-amarillo" style="border-radius:8px;">
                            <i class="bi bi-file-earmark-pdf mr-1"></i>Ver factura
                        </a>
                        <a href="index.php?pagina=inicio" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                            <i class="bi bi-arrow-repeat mr-1"></i>Volver a comprar
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ══ Panel: SEGURIDAD ══ -->
    <div id="panelSeguridad" class="cuenta-panel">
        <div class="row">
            <div class="col-md-6">
                <div class="c-card">
                    <div class="c-card-header"><i class="bi bi-key-fill"></i>Cambiar contraseña</div>
                    <div class="c-card-body">
                        <form id="formPassword" method="POST" action="index.php?pagina=mi_cuenta">
                            <input type="hidden" name="accion" value="cambiar_password">
                            <div class="form-group">
                                <label><i class="bi bi-lock mr-1"></i>Contraseña actual</label>
                                <input type="password" name="password_actual" class="form-control"
                                       placeholder="Tu contraseña actual" required>
                            </div>
                            <div class="form-group">
                                <label><i class="bi bi-lock-fill mr-1"></i>Nueva contraseña</label>
                                <input type="password" name="password_nuevo" class="form-control"
                                       placeholder="Mínimo 4 caracteres" required>
                            </div>
                            <button type="submit" class="btn btn-azul btn-block" style="border-radius:8px;padding:10px;">
                                <i class="bi bi-shield-check mr-1"></i>Actualizar contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="c-card">
                    <div class="c-card-header"><i class="bi bi-shield-exclamation"></i>Consejos de seguridad</div>
                    <div class="c-card-body">
                        <ul style="font-size:13px;color:#555;padding-left:18px;line-height:2;">
                            <li>Usa al menos 8 caracteres en tu contraseña.</li>
                            <li>Combina letras, números y símbolos.</li>
                            <li>No compartas tu contraseña con nadie.</li>
                            <li>Cierra sesión en dispositivos compartidos.</li>
                        </ul>
                        <div class="mt-3 pt-3" style="border-top:1px solid #f0f0f0;">
                            <a href="index.php?pagina=logout" class="btn btn-outline-danger btn-block btn-sm" style="border-radius:8px;">
                                <i class="bi bi-box-arrow-right mr-1"></i>Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
function cambiarTab(tab, el) {
    document.querySelectorAll('.cuenta-tab').forEach(function(t) { t.classList.remove('activo'); });
    document.querySelectorAll('.cuenta-panel').forEach(function(p) { p.classList.remove('activo'); });
    el.classList.add('activo');
    document.getElementById('panel' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('activo');
}

function toggleCompra(nro) {
    var card = document.getElementById('compra' + nro);
    card.classList.toggle('abierto');
}

// Si viene mensaje de éxito de contraseña, abrir tab seguridad
<?php if ($tipoMensaje === 'success' && strpos($mensaje ?? '', 'ontraseña') !== false): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cuenta-tab')[2].click();
});
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('formPerfil')) {
        Validacion.iniciar(document.getElementById('formPerfil'), {
            correo:     [Validacion.reglas.requerido, Validacion.reglas.email],
            direccion:  [Validacion.reglas.requerido, Validacion.reglas.minLen(5)],
            nroCelular: [Validacion.reglas.requerido, Validacion.reglas.soloDigitos],
        });
    }
    if (document.getElementById('formPassword')) {
        Validacion.iniciar(document.getElementById('formPassword'), {
            password_actual: [Validacion.reglas.requerido],
            password_nuevo:  [Validacion.reglas.requerido, Validacion.reglas.minLen(4)],
        });
    }
});
</script>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
