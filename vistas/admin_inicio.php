<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<style>
/* ── Hero banner ── */
.inicio-hero {
    background: linear-gradient(135deg, #1B3A6B 0%, #2751a3 60%, #1a6eb5 100%);
    border-radius: 16px;
    padding: 40px 48px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 32px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.inicio-hero::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.inicio-hero::after {
    content: '';
    position: absolute;
    right: 80px; bottom: -80px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(245,166,35,.1);
}

.inicio-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 3px solid rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; font-weight: 900;
    color: #fff;
    flex-shrink: 0;
    letter-spacing: -1px;
    backdrop-filter: blur(4px);
    position: relative; z-index: 1;
}

.inicio-hero-text { position: relative; z-index: 1; }
.inicio-saludo {
    font-size: 13px;
    opacity: .75;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .08em;
}
.inicio-nombre {
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 8px;
    line-height: 1.2;
}
.inicio-rol {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(245,166,35,.25);
    border: 1px solid rgba(245,166,35,.5);
    color: #F5A623;
    font-size: 12px; font-weight: 700;
    padding: 4px 12px; border-radius: 20px;
    letter-spacing: .05em;
}
.inicio-subtitulo {
    font-size: 13px; opacity: .65;
    margin-top: 8px;
}
.inicio-fecha {
    margin-left: auto;
    text-align: right;
    position: relative; z-index: 1;
    flex-shrink: 0;
}
.inicio-fecha .dia {
    font-size: 42px; font-weight: 900; line-height: 1;
    opacity: .9;
}
.inicio-fecha .mes {
    font-size: 14px; opacity: .65; text-transform: uppercase; letter-spacing: .06em;
}

/* ── Stats rápidas ── */
.inicio-stat {
    background: #fff;
    border-radius: 14px;
    padding: 22px 24px;
    box-shadow: 0 2px 12px rgba(27,58,107,.08);
    display: flex; align-items: center; gap: 16px;
    height: 100%;
}
.inicio-stat-ico {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; flex-shrink: 0;
}
.ico-blue   { background: #e8f0fe; color: #1B3A6B; }
.ico-green  { background: #e6f9ed; color: #1a7a3f; }
.ico-yellow { background: #fff7e0; color: #b87a00; }
.ico-red    { background: #fdecea; color: #c0392b; }
.ico-purple { background: #f3e8ff; color: #6d28d9; }
.ico-teal   { background: #e0f7f7; color: #0d7377; }
.inicio-stat-num { font-size: 28px; font-weight: 900; color: #1B3A6B; line-height: 1; }
.inicio-stat-lbl { font-size: 12px; color: #888; margin-top: 3px; }

/* ── Módulos de acceso rápido ── */
.modulo-card {
    background: #fff;
    border-radius: 14px;
    padding: 24px 20px;
    box-shadow: 0 2px 12px rgba(27,58,107,.07);
    text-align: center;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: all .2s;
    border: 2px solid transparent;
    height: 100%;
}
.modulo-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(27,58,107,.15);
    border-color: #1B3A6B;
    color: inherit;
}
.modulo-card-ico {
    width: 56px; height: 56px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; margin: 0 auto 12px;
}
.modulo-card-titulo { font-size: 14px; font-weight: 700; color: #1B3A6B; margin-bottom: 4px; }
.modulo-card-desc   { font-size: 11px; color: #888; }

/* ── Sección título ── */
.sec-titulo {
    font-size: 14px; font-weight: 700; color: #1B3A6B;
    border-left: 3px solid #F5A623;
    padding-left: 10px; margin-bottom: 16px;
}

@media(max-width:768px) {
    .inicio-hero { flex-direction: column; padding: 28px 24px; gap: 20px; }
    .inicio-fecha { margin-left: 0; text-align: left; }
    .inicio-nombre { font-size: 20px; }
}
</style>

<!-- ══ HERO ══ -->
<div class="inicio-hero">
    <div class="inicio-avatar"><?php
        $iniciales = '';
        if ($nombreCompleto) {
            $partes = explode(' ', trim($nombreCompleto));
            foreach (array_slice($partes, 0, 2) as $p) $iniciales .= strtoupper(mb_substr($p, 0, 1));
        } else {
            $iniciales = strtoupper(mb_substr($_SESSION['usuario'] ?? 'A', 0, 2));
        }
        echo $iniciales;
    ?></div>

    <div class="inicio-hero-text">
        <div class="inicio-saludo">Bienvenido de nuevo</div>
        <h2 class="inicio-nombre"><?php echo htmlspecialchars($nombreCompleto ?: ($_SESSION['usuario'] ?? 'Administrador')); ?></h2>
        <div class="inicio-rol">
            <i class="bi bi-shield-check"></i>
            <?php echo htmlspecialchars($rolLabel); ?>
        </div>
        <div class="inicio-subtitulo">
            <i class="bi bi-lightning-charge-fill me-1" style="color:#F5A623;"></i>
            ElectroHogar &mdash; Sistema de Gestión de Tienda
        </div>
    </div>

    <div class="inicio-fecha d-none d-lg-block">
        <div class="dia"><?php echo date('d'); ?></div>
        <div class="mes"><?php echo strftime('%B %Y') ?: date('F Y'); ?></div>
        <div style="font-size:12px;opacity:.6;margin-top:4px;"><?php
            $dias = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
            echo $dias[date('w')];
        ?></div>
    </div>
</div>

<!-- ══ STATS RÁPIDAS ══ -->
<div class="sec-titulo"><i class="bi bi-bar-chart-line me-2"></i>Resumen de hoy</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="inicio-stat">
            <div class="inicio-stat-ico ico-blue"><i class="bi bi-cart-check"></i></div>
            <div>
                <div class="inicio-stat-num"><?php echo (int)($dash['ventasHoy'] ?? 0); ?></div>
                <div class="inicio-stat-lbl">Ventas hoy</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="inicio-stat">
            <div class="inicio-stat-ico ico-green"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="inicio-stat-num" style="font-size:18px;">Bs.<?php echo number_format((float)($dash['ingresoHoy'] ?? 0), 0); ?></div>
                <div class="inicio-stat-lbl">Ingresos hoy</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="inicio-stat">
            <div class="inicio-stat-ico ico-yellow"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="inicio-stat-num"><?php echo (int)($dash['pedidosPendientes'] ?? 0); ?></div>
                <div class="inicio-stat-lbl">Pedidos pend.</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="inicio-stat">
            <div class="inicio-stat-ico ico-purple"><i class="bi bi-people"></i></div>
            <div>
                <div class="inicio-stat-num"><?php echo (int)($dash['totalClientes'] ?? 0); ?></div>
                <div class="inicio-stat-lbl">Clientes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="inicio-stat">
            <div class="inicio-stat-ico ico-teal"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="inicio-stat-num"><?php echo (int)($dash['totalProductos'] ?? 0); ?></div>
                <div class="inicio-stat-lbl">Productos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="inicio-stat">
            <div class="inicio-stat-ico ico-red"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="inicio-stat-num"><?php echo (int)($totalCriticos ?? 0); ?></div>
                <div class="inicio-stat-lbl">Stock crítico</div>
            </div>
        </div>
    </div>
</div>

<!-- ══ ACCESO RÁPIDO ══ -->
<div class="sec-titulo"><i class="bi bi-grid me-2"></i>Acceso rápido</div>
<div class="row g-3">
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=productos" class="modulo-card">
            <div class="modulo-card-ico ico-blue"><i class="bi bi-box-seam"></i></div>
            <div class="modulo-card-titulo">Productos</div>
            <div class="modulo-card-desc">Catálogo completo</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=pedidos" class="modulo-card">
            <div class="modulo-card-ico ico-yellow"><i class="bi bi-clock-history"></i></div>
            <div class="modulo-card-titulo">Pedidos</div>
            <div class="modulo-card-desc">Órdenes activas</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=ventas" class="modulo-card">
            <div class="modulo-card-ico ico-green"><i class="bi bi-receipt"></i></div>
            <div class="modulo-card-titulo">Ventas</div>
            <div class="modulo-card-desc">Historial de ventas</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=clientes" class="modulo-card">
            <div class="modulo-card-ico ico-purple"><i class="bi bi-people"></i></div>
            <div class="modulo-card-titulo">Clientes</div>
            <div class="modulo-card-desc">Gestión de cuentas</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=almacen" class="modulo-card">
            <div class="modulo-card-ico ico-teal"><i class="bi bi-archive"></i></div>
            <div class="modulo-card-titulo">Stock</div>
            <div class="modulo-card-desc">Inventario actual</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=almacen_critico" class="modulo-card">
            <div class="modulo-card-ico ico-red"><i class="bi bi-exclamation-diamond"></i></div>
            <div class="modulo-card-titulo">Stock Crítico</div>
            <div class="modulo-card-desc">Productos agotados</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=vendedores" class="modulo-card">
            <div class="modulo-card-ico" style="background:#fff3e0;color:#e65100;"><i class="bi bi-person-badge"></i></div>
            <div class="modulo-card-titulo">Vendedores</div>
            <div class="modulo-card-desc">Gestión de staff</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=categorias" class="modulo-card">
            <div class="modulo-card-ico" style="background:#e8f5e9;color:#1b5e20;"><i class="bi bi-tag"></i></div>
            <div class="modulo-card-titulo">Categorías</div>
            <div class="modulo-card-desc">Organización</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=marcas" class="modulo-card">
            <div class="modulo-card-ico" style="background:#ede9fe;color:#5b21b6;"><i class="bi bi-patch-check"></i></div>
            <div class="modulo-card-titulo">Marcas</div>
            <div class="modulo-card-desc">Catálogo de marcas</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=almacen_traspasos" class="modulo-card">
            <div class="modulo-card-ico" style="background:#e0f2fe;color:#0369a1;"><i class="bi bi-arrow-left-right"></i></div>
            <div class="modulo-card-titulo">Traspasos</div>
            <div class="modulo-card-desc">Entre sucursales</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=sucursales" class="modulo-card">
            <div class="modulo-card-ico" style="background:#fce7f3;color:#9d174d;"><i class="bi bi-shop"></i></div>
            <div class="modulo-card-titulo">Sucursales</div>
            <div class="modulo-card-desc">Puntos de venta</div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="/admin/index.php?page=dashboard" class="modulo-card">
            <div class="modulo-card-ico" style="background:#e8f0fe;color:#1B3A6B;"><i class="bi bi-speedometer2"></i></div>
            <div class="modulo-card-titulo">Dashboard</div>
            <div class="modulo-card-desc">Análisis completo</div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
