package chat;

import java.io.*;
import java.net.*;
import java.util.*;

public class ServidorChat {

    public static final int PUERTO = 5000;
    private static final List<ManejadorCliente> clientes = new ArrayList<>();
    private ServerSocket serverSocket;
    private VentanaServidor ventana;

    public ServidorChat(VentanaServidor ventana) {
        this.ventana = ventana;
    }

    public void iniciar() {
        new Thread(() -> {
            try {
                serverSocket = new ServerSocket(PUERTO);
                ventana.log("Servidor iniciado en puerto " + PUERTO);
                ventana.log("Esperando conexiones...\n");

                while (true) {
                    Socket socket = serverSocket.accept();
                    ManejadorCliente manejador = new ManejadorCliente(socket, this);
                    agregarCliente(manejador);
                    new Thread(manejador).start();
                }
            } catch (IOException e) {
                if (!serverSocket.isClosed()) {
                    ventana.log("[ERROR] " + e.getMessage());
                }
            }
        }).start();
    }

    public void detener() {
        try {
            if (serverSocket != null) serverSocket.close();
        } catch (IOException e) {
            ventana.log("[ERROR al detener] " + e.getMessage());
        }
    }

    public synchronized void broadcast(String mensaje, ManejadorCliente origen) {
        ventana.log(mensaje);
        for (ManejadorCliente c : clientes) {
            c.enviarMensaje(mensaje);
        }
    }

    // Envía un mensaje solo al cliente con ese nombre exacto.
    // Devuelve true si lo encontró y entregó, false si no está conectado.
    public synchronized boolean enviarPrivado(String nombreDestino, String mensajeFormateado) {
        for (ManejadorCliente c : clientes) {
            if (c.getNombre().equalsIgnoreCase(nombreDestino)) {
                c.enviarMensaje(mensajeFormateado);
                return true;
            }
        }
        return false;
    }

    // Construye "##USUARIOS##Ana,Carlos,Pedro" y lo envía a todos.
    // El cliente detecta ese prefijo y actualiza su JList, no lo muestra en el chat.
    public synchronized void enviarListaUsuarios() {
        if (clientes.isEmpty()) return;
        StringBuilder sb = new StringBuilder("##USUARIOS##");
        for (int i = 0; i < clientes.size(); i++) {
            sb.append(clientes.get(i).getNombre());
            if (i < clientes.size() - 1) sb.append(",");
        }
        String lista = sb.toString();
        for (ManejadorCliente c : clientes) {
            c.enviarMensaje(lista);
        }
    }

    public synchronized void agregarCliente(ManejadorCliente c) {
        clientes.add(c);
        ventana.actualizarContador(clientes.size());
    }

    public synchronized void eliminarCliente(ManejadorCliente c) {
        clientes.remove(c);
        ventana.actualizarContador(clientes.size());
    }
}
