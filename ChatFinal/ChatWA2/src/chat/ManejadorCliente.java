package chat;

import java.io.*;
import java.net.*;

public class ManejadorCliente implements Runnable {

    private Socket socket;
    private ServidorChat servidor;
    private PrintWriter salida;
    private String nombre;

    public ManejadorCliente(Socket socket, ServidorChat servidor) {
        this.socket = socket;
        this.servidor = servidor;
    }

    @Override
    public void run() {
        try {
            BufferedReader entrada = new BufferedReader(
                new InputStreamReader(socket.getInputStream(), "UTF-8")
            );
            salida = new PrintWriter(
                new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true
            );

            // El primer mensaje que manda el cliente es su nombre
            nombre = entrada.readLine();
            if (nombre == null || nombre.isBlank()) nombre = "Anónimo";

            servidor.broadcast("✔ " + nombre + " se unió al chat", this);
            servidor.enviarListaUsuarios(); // notificar a todos quiénes hay

            String mensaje;
            while ((mensaje = entrada.readLine()) != null) {
                if (mensaje.equalsIgnoreCase("/salir")) break;

                // Mensaje privado: formato "@Destino: texto"
                if (mensaje.startsWith("@")) {
                    int sep = mensaje.indexOf(":");
                    if (sep > 1) {
                        String destino = mensaje.substring(1, sep).trim();
                        String texto   = mensaje.substring(sep + 1).trim();
                        boolean ok = servidor.enviarPrivado(destino,
                            "[PRIVADO de " + nombre + "]: " + texto);
                        if (ok) {
                            // Confirmación solo para el remitente
                            this.enviarMensaje("[PRIVADO → " + destino + "]: " + texto);
                        } else {
                            this.enviarMensaje("[Sistema]: '" + destino + "' no está conectado.");
                        }
                    } else {
                        servidor.broadcast(nombre + ": " + mensaje, this);
                    }
                } else {
                    servidor.broadcast(nombre + ": " + mensaje, this);
                }
            }

        } catch (IOException e) {
            // Desconexión abrupta — no hacer nada extra
        } finally {
            servidor.eliminarCliente(this);
            servidor.broadcast("✖ " + nombre + " salió del chat", this);
            servidor.enviarListaUsuarios(); // actualizar lista tras la salida
            try { socket.close(); } catch (IOException ex) {}
        }
    }

    public void enviarMensaje(String msg) {
        if (salida != null) salida.println(msg);
    }

    public String getNombre() {
        return nombre;
    }
}
