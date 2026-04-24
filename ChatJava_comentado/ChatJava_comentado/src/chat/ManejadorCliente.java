package chat; // Pertenece al paquete "chat", igual que las demás clases del proyecto

import java.io.*;  // Para InputStreamReader, OutputStreamWriter, BufferedReader, PrintWriter
import java.net.*; // Para usar la clase Socket

/**
 * ManejadorCliente.java
 * ----------------------
 * Esta clase representa UNA conexión activa con UN cliente.
 * El servidor crea una instancia de esta clase por cada cliente que se conecta,
 * y la ejecuta en su propio Thread independiente.
 *
 * Sus responsabilidades son:
 *   1. Leer el nombre del cliente (primer mensaje que manda)
 *   2. Estar en loop leyendo mensajes del cliente
 *   3. Pedirle al servidor que haga broadcast de cada mensaje recibido
 *   4. Manejar la desconexión limpiamente
 *
 * Implementa Runnable para poder ejecutarse en un Thread separado.
 */
public class ManejadorCliente implements Runnable {
    // "implements Runnable" significa que esta clase puede ejecutarse en un Thread.
    // Para eso DEBE implementar el método run(), que es el punto de entrada del hilo.

    // ─────────────────────────────────────────────────────────────────────────
    // ATRIBUTOS
    // ─────────────────────────────────────────────────────────────────────────

    private Socket socket;
    // El canal de comunicación TCP con este cliente específico.
    // A través de él se obtienen los streams de entrada y salida.

    private ServidorChat servidor;
    // Referencia al servidor principal.
    // Se usa para llamar a broadcast() y a eliminarCliente() cuando desconecta.

    private PrintWriter salida;
    // Stream de ESCRITURA hacia el cliente.
    // Con "salida.println(msg)" enviamos texto al cliente.
    // PrintWriter añade automáticamente el salto de línea y hace flush automático
    // porque fue creado con autoFlush = true.

    private String nombre;
    // Nombre del usuario, recibido como primer mensaje al conectarse.
    // Se usa para identificarlo en los mensajes del chat: "Douglas: Hola!"

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────────────────────────────────────

    public ManejadorCliente(Socket socket, ServidorChat servidor) {
        this.socket = socket;
        // Guarda el socket (canal de red) para usarlo dentro de run().
        this.servidor = servidor;
        // Guarda la referencia al servidor para llamar sus métodos luego.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: run()
    // Este es el método que ejecuta el Thread cuando se llama .start().
    // Contiene el ciclo de vida completo de la conexión con el cliente.
    // ─────────────────────────────────────────────────────────────────────────

    @Override
    public void run() {
        // @Override indica que estamos implementando el método de la interfaz Runnable.

        try {

            // ── Preparar el stream de LECTURA (datos que llegan del cliente) ──
            BufferedReader entrada = new BufferedReader(
                new InputStreamReader(socket.getInputStream(), "UTF-8")
            );
            // socket.getInputStream() → obtiene el flujo de bytes que llegan del cliente
            // InputStreamReader       → convierte esos bytes en caracteres usando UTF-8
            //                           (UTF-8 soporta tildes, ñ, caracteres especiales)
            // BufferedReader          → agrega buffer y el método readLine() que lee
            //                           línea por línea (termina al encontrar '\n')

            // ── Preparar el stream de ESCRITURA (datos que enviamos al cliente) ──
            salida = new PrintWriter(
                new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true
            );
            // socket.getOutputStream() → flujo de bytes hacia el cliente
            // OutputStreamWriter       → convierte caracteres a bytes con codificación UTF-8
            // PrintWriter              → agrega métodos convenientes como println()
            // true                     → autoFlush: envía los datos inmediatamente al escribir
            //                           sin autoFlush, los mensajes quedarían en buffer
            //                           y el cliente los recibiría tarde o nunca

            // ── Recibir el NOMBRE del cliente (protocolo: primer mensaje = nombre) ──
            nombre = entrada.readLine();
            // readLine() espera hasta que el cliente envíe una línea de texto.
            // El cliente VentanaCliente.java envía el nombre como primero inmediatamente
            // al conectarse. Si el stream se cierra antes, devuelve null.

            if (nombre == null || nombre.isBlank()) nombre = "Anónimo";
            // Validación: si el cliente no mandó nombre (null) o mandó espacios vacíos,
            // le asignamos "Anónimo" para que los mensajes sigan siendo legibles.
            // isBlank() devuelve true si la cadena está vacía o solo tiene espacios.

            // ── Notificar a todos que este cliente se conectó ──
            servidor.broadcast("✔ " + nombre + " se unió al chat", this);
            // Llama al broadcast del servidor enviando mensaje de bienvenida.
            // El "✔" es un carácter Unicode visible como símbolo de check.
            // "this" = referencia a este ManejadorCliente (el que envió el mensaje).

            // ── BUCLE PRINCIPAL: leer mensajes del cliente indefinidamente ──
            String mensaje;
            while ((mensaje = entrada.readLine()) != null) {
                // entrada.readLine() → espera y lee la siguiente línea del cliente
                // El while se repite mientras el cliente siga enviando mensajes
                // Si el cliente se desconecta (cierra el socket), readLine() devuelve null
                // y el while termina.

                if (mensaje.equalsIgnoreCase("/salir")) break;
                // Comando especial: si el cliente escribe "/salir", salimos del bucle.
                // equalsIgnoreCase = no importa si escribe "/SALIR" o "/Salir".
                // break = sale inmediatamente del while.

                servidor.broadcast(nombre + ": " + mensaje, this);
                // Pide al servidor que envíe el mensaje a TODOS los clientes.
                // Formato: "Douglas: Hola a todos!"
            }

        } catch (IOException e) {
            // IOException ocurre si:
            //   - El cliente cerró la ventana abruptamente (sin /salir)
            //   - Hubo un corte de red
            //   - El socket fue cerrado desde otro hilo
            // En todos estos casos simplemente salimos del try sin mostrar error,
            // porque es un comportamiento normal en una app de red.

        } finally {
            // El bloque finally SIEMPRE se ejecuta, haya excepción o no.
            // Es el lugar correcto para hacer limpieza de recursos.

            servidor.eliminarCliente(this);
            // Elimina este ManejadorCliente de la lista del servidor.
            // Así los futuros broadcasts no intentarán escribirle a un socket cerrado.

            servidor.broadcast("✖ " + nombre + " salió del chat", this);
            // Notifica a todos los demás que este usuario se desconectó.
            // "✖" es el símbolo Unicode de X (salida/desconexión).

            try {
                socket.close();
                // Cierra el socket y libera los recursos de red del sistema operativo.
                // Si no cerramos, el socket quedaría "zombi" hasta que el SO lo limpie.
            } catch (IOException ex) {
                // Si ya estaba cerrado o falla el cierre, ignoramos el error.
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: enviarMensaje()
    // Llamado por ServidorChat.broadcast() para mandar texto a ESTE cliente
    // ─────────────────────────────────────────────────────────────────────────

    public void enviarMensaje(String msg) {
        if (salida != null) salida.println(msg);
        // Verifica que salida no sea null (podría serlo si el cliente nunca llegó
        // a mandar su nombre y el stream no fue inicializado).
        // salida.println(msg) escribe el mensaje + '\n' y hace flush automático,
        // enviando los bytes al cliente a través del socket TCP.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: getNombre()
    // Getter simple para obtener el nombre del usuario desde otras clases
    // ─────────────────────────────────────────────────────────────────────────

    public String getNombre() {
        return nombre;
        // Retorna el nombre del usuario que se conectó.
        // Útil si en el futuro el servidor quiere listar los usuarios activos.
    }
}
