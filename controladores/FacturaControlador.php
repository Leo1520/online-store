<?php
require_once __DIR__ . '/../modelos/NotaVenta.php';
require_once __DIR__ . '/../config/database.php';

class FacturaControlador {

    public function ver() {
        if (empty($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit();
        }

        $nroVenta = (int)($_GET['nro'] ?? 0);
        if ($nroVenta <= 0) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $modelo   = new NotaVenta();
        $detalles = $modelo->obtenerDetallesPorNota($nroVenta);

        if (empty($detalles)) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        // Datos de cabecera de la venta
        $db     = Database::conectar();
        $sql    = "SELECT nv.nro, nv.fechaHora, nv.estado,
                          cl.ci, cl.nombres, cl.apPaterno, cl.apMaterno,
                          cl.correo, cl.direccion, cl.nroCelular
                   FROM NotaVenta nv
                   INNER JOIN Cliente cl ON cl.ci = nv.ciCliente
                   WHERE nv.nro = ?";
        $stmt   = $db->prepare($sql);
        $stmt->bind_param('i', $nroVenta);
        $stmt->execute();
        $res    = $stmt->get_result();
        $row    = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $venta  = $row;
        $cliente = $row;

        require_once __DIR__ . '/../vistas/factura.php';
    }
}
