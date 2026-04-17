<?php
/**
 * Clase Base para Controladores
 */
class Controlador {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Carga una vista
     */
    protected function cargarVista($vista, $datos = []) {
        extract($datos);
        require __DIR__ . '/../Views/' . $vista . '.php';
    }

    /**
     * Carga una vista dentro del layout admin
     */
    protected function cargarVistaAdmin($vista, $datos = [], $titulo = '') {
        extract($datos);
        $vistaContenido = __DIR__ . '/../Views/' . $vista . '.php';
        $titulo = $titulo;
        require __DIR__ . '/../Views/Admin/layoutAdmin.php';
    }

    /**
     * Envía respuesta JSON
     */
    protected function respuestaJSON($datos = [], $exito = true) {
        header('Content-Type: application/json');
        echo json_encode([
            'exito' => $exito,
            'datos' => $datos
        ]);
        exit();
    }

    /**
     * Redirige a una URL
     */
    protected function redirigir($url) {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Verifica si el usuario está autenticado
     */
    protected function verificarAutenticacion() {
        if (!isset($_SESSION['usuario'])) {
            $this->redirigir('?controlador=autenticacion&accion=mostrarLogin');
        }
    }

    /**
     * Verifica si es administrador
     */
    protected function verificarAdmin() {
        if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permiso para acceder a esta sección.';
            $this->redirigir('?controlador=productos&accion=listar');
        }
    }

    /**
     * Verifica si es trabajador (trabajador o admin)
     */
    protected function verificarTrabajador() {
        if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] !== 'trabajador' && $_SESSION['rol'] !== 'admin')) {
            $_SESSION['error'] = 'No tienes permiso para acceder a esta sección.';
            $this->redirigir('?controlador=productos&accion=listar');
        }
    }
}
?>
