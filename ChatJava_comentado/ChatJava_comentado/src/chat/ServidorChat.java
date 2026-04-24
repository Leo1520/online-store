package chat; // Define el paquete "chat" donde viven todas las clases del proyecto

// Importaciones necesarias para sockets, streams y colecciones
import java.io.*;           // Para leer y escribir datos por los streams (flujos de datos)
import java.net.*;          // Para usar ServerSocket y Socket (comunicación por red)
import java.util.*;         // Para usar ArrayList y List (lista de clientes conectados)

/**
 * ServidorChat.java
 * -----------------
 * Esta clase es el NÚCLEO del servidor.
 * Su trabajo es:
 *   1. Escuchar en un puerto (5000) esperando conexiones de clientes
 *   2. Cuando llega un cliente, crear un ManejadorCliente para atenderlo
 *   3. Guardar la lista de todos los clientes conectados
 *   4. Hacer BROADCAST: reenviar cada mensaje a TODOS los clientes
 */
public class ServidorChat {

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTANTES Y ATRIBUTOS
    // ─────────────────────────────────────────────────────────────────────────

    public static final int PUERTO = 5000;
    // Puerto TCP donde el servidor "escucha" conexiones entrantes.
    // "public static final" = constante pública accesible desde otras clases.
    // El cliente también debe conocer este número para conectarse.

    private static final List<ManejadorCliente> clientes = new ArrayList<>();
    // Lista que almacena UN objeto ManejadorCliente por cada cliente conectado.
    // "private" = solo esta clase puede modificarla directamente.
    // "static" = la lista existe una sola vez para toda la clase (no por instancia).
    // ArrayList = lista dinámica que crece o se reduce según se conectan/desconectan.

    private ServerSocket serverSocket;
    // El ServerSocket es el "portero": está permanentemente escuchando en el puerto 5000.
    // Cuando detecta una conexión entrante, la acepta y devuelve un Socket normal.

    private VentanaServidor ventana;
    // Referencia a la ventana gráfica (Swing) del servidor.
    // Se usa para mostrar logs y actualizar el contador de clientes en pantalla.

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────────────────────────────────────

    public ServidorChat(VentanaServidor ventana) {
        // Constructor: recibe la ventana gráfica y la guarda en el atributo.
        // Así el servidor puede escribir mensajes en la interfaz visual.
        this.ventana = ventana;
        // "this.ventana" = el atributo de esta clase
        // "ventana"      = el parámetro que llegó al constructor
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: iniciar()
    // Arranca el servidor en un hilo separado para no bloquear la interfaz gráfica
    // ─────────────────────────────────────────────────────────────────────────

    public void iniciar() {

        new Thread(() -> {
            // Crea un nuevo Thread (hilo de ejecución paralela).
            // Sin esto, el bucle infinito de abajo congelaría la ventana Swing.
            // El código dentro de "() -> { ... }" se ejecuta en ese hilo nuevo.

            try {

                serverSocket = new ServerSocket(PUERTO);
                // Crea el ServerSocket y lo "ata" al puerto 5000.
                // A partir de este momento el sistema operativo empieza a
                // recibir solicitudes de conexión en ese puerto.

                ventana.log("Servidor iniciado en puerto " + PUERTO);
                // Muestra un mensaje en el JTextArea de la ventana del servidor.

                ventana.log("Esperando conexiones...\n");
                // Otro mensaje informativo en la ventana.

                while (true) {
                    // Bucle infinito: el servidor nunca para de esperar clientes.
                    // Solo se rompe si se llama a serverSocket.close() externamente.

                    Socket socket = serverSocket.accept();
                    // BLOQUEANTE: este método para aquí y espera hasta que llegue
                    // un cliente. Cuando llega, devuelve un Socket que representa
                    // el canal de comunicación exclusivo con ese cliente.

                    ManejadorCliente manejador = new ManejadorCliente(socket, this);
                    // Crea un ManejadorCliente pasándole:
                    //   - socket: el canal de comunicación con ese cliente
                    //   - this:   referencia a este servidor (para hacer broadcast)

                    agregarCliente(manejador);
                    // Agrega el nuevo manejador a la lista de clientes conectados.

                    new Thread(manejador).start();
                    // Lanza el ManejadorCliente en su propio Thread.
                    // Así cada cliente tiene su propio hilo de atención,
                    // y el servidor puede seguir aceptando más conexiones
                    // sin esperar a que ese cliente termine.
                }

            } catch (IOException e) {
                // Se lanza si ocurre un error de red o si serverSocket fue cerrado.

                if (!serverSocket.isClosed()) {
                    // Solo muestra el error si el socket NO fue cerrado intencionalmente.
                    // (Si el admin hizo clic en "Detener", no queremos mostrar un error.)
                    ventana.log("[ERROR] " + e.getMessage());
                }
            }

        }).start();
        // ".start()" arranca el hilo que acabamos de definir arriba.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: detener()
    // Cierra el ServerSocket, lo que provoca que accept() lance IOException
    // y el bucle while(true) termine limpiamente.
    // ─────────────────────────────────────────────────────────────────────────

    public void detener() {
        try {
            if (serverSocket != null) serverSocket.close();
            // Cierra el ServerSocket solo si fue creado (no es null).
            // Esto interrumpe el accept() bloqueante en el hilo del servidor.
        } catch (IOException e) {
            ventana.log("[ERROR al detener] " + e.getMessage());
            // Si hay un error al cerrar, lo reportamos en la ventana.
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: broadcast()
    // Envía un mensaje a TODOS los clientes conectados
    // ─────────────────────────────────────────────────────────────────────────

    public synchronized void broadcast(String mensaje, ManejadorCliente origen) {
        // "synchronized" es clave: garantiza que solo UN hilo a la vez ejecuta
        // este método. Sin esto, dos clientes enviando mensajes al mismo tiempo
        // podrían corromper la lista o mezclar los mensajes.
        // "origen" = el ManejadorCliente que envió el mensaje (por ahora no se usa
        // para filtrar, pero permite excluirlo del broadcast si se desea).

        ventana.log(mensaje);
        // Muestra el mensaje en el log de la ventana del servidor.

        for (ManejadorCliente c : clientes) {
            // Recorre TODOS los clientes conectados en la lista.
            c.enviarMensaje(mensaje);
            // Le manda el mensaje a cada uno usando su PrintWriter interno.
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: agregarCliente()
    // Registra un nuevo cliente en la lista cuando se conecta
    // ─────────────────────────────────────────────────────────────────────────

    public synchronized void agregarCliente(ManejadorCliente c) {
        // "synchronized": protege la lista de modificaciones simultáneas.
        clientes.add(c);
        // Agrega el ManejadorCliente a la ArrayList de clientes activos.
        ventana.actualizarContador(clientes.size());
        // Actualiza el label "Clientes: N" en la ventana con el nuevo total.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: eliminarCliente()
    // Quita un cliente de la lista cuando se desconecta
    // ─────────────────────────────────────────────────────────────────────────

    public synchronized void eliminarCliente(ManejadorCliente c) {
        // "synchronized": protege la lista de modificaciones simultáneas.
        clientes.remove(c);
        // Elimina ese ManejadorCliente específico de la lista.
        ventana.actualizarContador(clientes.size());
        // Actualiza el contador en la ventana con el nuevo total (uno menos).
    }
}
