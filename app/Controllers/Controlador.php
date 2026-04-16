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
            $this->redirigir('../Admin/inicio_sesion.php');
        }
    }

    /**
     * Verifica si es administrador
     */
    protected function verificarAdmin() {
        if (!isset($_SESSION['admin'])) {
            $this->redirigir('../Admin/inicio_sesion.php');
        }
    }
}
?>
