package chat; // Pertenece al paquete "chat"

// Componentes gráficos Swing
import javax.swing.*;          // JFrame, JTextArea, JTextField, JButton, JLabel, etc.
import javax.swing.border.*;   // BorderFactory para personalizar bordes de componentes

// Clases base de gráficos y eventos
import java.awt.*;             // Color, Font, BorderLayout, FlowLayout, Insets, Dimension, Cursor
import java.awt.event.*;       // WindowAdapter, WindowEvent, FocusAdapter, FocusEvent

// Red y streams
import java.io.*;              // PrintWriter, BufferedReader, InputStreamReader, OutputStreamWriter
import java.net.*;             // Socket para la conexión TCP al servidor

/**
 * VentanaCliente.java
 * --------------------
 * Esta clase es la INTERFAZ GRÁFICA del cliente (ventana Swing).
 * Extiende JFrame → es una ventana de escritorio.
 *
 * Responsabilidades:
 *   1. Mostrar el formulario de conexión (nombre, host, botones)
 *   2. Conectarse al servidor creando un Socket
 *   3. Iniciar un Thread receptor que escucha mensajes entrantes del servidor
 *   4. Enviar mensajes al servidor cuando el usuario escribe y presiona Enter/Enviar
 *   5. Mostrar los mensajes en el JTextArea del chat
 *
 * Tiene el método main() → es el punto de entrada del lado CLIENTE.
 */
public class VentanaCliente extends JFrame {
    // "extends JFrame" = esta clase es una ventana Swing.

    // ─────────────────────────────────────────────────────────────────────────
    // ATRIBUTOS — Componentes de la interfaz
    // ─────────────────────────────────────────────────────────────────────────

    private JTextArea areaChat;
    // El área principal donde se muestran todos los mensajes del chat.
    // Solo lectura para el usuario (setEditable = false).

    private JTextField campoMensaje;
    // Campo de texto de una línea donde el usuario escribe su mensaje.
    // Al presionar Enter o el botón "Enviar", se envía al servidor.

    private JButton btnEnviar, btnConectar, btnDesconectar;
    // btnConectar     → inicia la conexión al servidor
    // btnDesconectar  → cierra la conexión limpiamente
    // btnEnviar       → envía el texto de campoMensaje al servidor

    private JTextField campoNombre, campoHost;
    // campoNombre → el usuario escribe su nombre (ej: "Douglas")
    // campoHost   → dirección del servidor (por defecto "localhost")
    //               si el servidor está en otra PC, se pone su IP (ej: "192.168.1.5")

    private JLabel lblEstado;
    // Etiqueta que muestra el estado actual:
    //   "● Sin conexión"  (rojo) cuando no está conectado
    //   "● Conectado como Douglas" (verde) cuando está conectado

    // ─────────────────────────────────────────────────────────────────────────
    // ATRIBUTOS — Lógica de red
    // ─────────────────────────────────────────────────────────────────────────

    private Socket socket;
    // El canal TCP entre este cliente y el servidor.
    // Se crea en conectar() y se cierra en desconectar().

    private PrintWriter salida;
    // Stream de escritura hacia el servidor.
    // salida.println("Hola") envía "Hola\n" al servidor.

    private boolean conectado = false;
    // Flag (bandera) que indica si actualmente hay una conexión activa.
    // Se usa para validar antes de enviar mensajes o desconectar.

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────────────────────────────────────

    public VentanaCliente() {

        setTitle("Chat Java — Cliente");
        // Texto de la barra de título. Cambia a "Chat Java — [nombre]" al conectarse.

        setDefaultCloseOperation(JFrame.DO_NOTHING_ON_CLOSE);
        // La X de la ventana no hace nada sola. El comportamiento se define
        // en el WindowListener de abajo, que llama desconectar() antes de cerrar.

        setSize(600, 520);
        // Tamaño inicial: 600px ancho × 520px alto.

        setLocationRelativeTo(null);
        // Centra la ventana en la pantalla al abrirse.

        setMinimumSize(new Dimension(400, 400));
        // Tamaño mínimo: el usuario no puede reducir la ventana más de 400×400px.
        // Evita que los componentes se deformen al redimensionar.

        // ── Listener para el botón X de cierre ──
        addWindowListener(new WindowAdapter() {
            // WindowAdapter → clase base para listeners de ventana.
            // Solo sobreescribimos windowClosing (el evento de la X).

            @Override
            public void windowClosing(WindowEvent e) {
                // Se llama cuando el usuario intenta cerrar la ventana.
                desconectar();
                // Cierra la conexión limpiamente antes de salir.
                // Esto envía "/salir" al servidor y cierra el socket.
                System.exit(0);
                // Termina la JVM y todos sus hilos.
            }
        });

        construirUI();
        // Construye y coloca todos los componentes visuales en la ventana.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: construirUI()
    // Crea y organiza todos los componentes gráficos de la ventana
    // ─────────────────────────────────────────────────────────────────────────

    private void construirUI() {

        // ════════════════════════════════════════
        // PANEL DE CONEXIÓN (zona NORTH)
        // ════════════════════════════════════════

        JPanel panelConexion = new JPanel(new FlowLayout(FlowLayout.LEFT, 8, 8));
        // Panel con FlowLayout izquierdo: los componentes se alinean en fila.
        panelConexion.setBackground(new Color(40, 55, 80));
        // Fondo azul oscuro para diferenciarlo del área de chat.
        panelConexion.setBorder(BorderFactory.createEmptyBorder(4, 4, 4, 4));
        // Margen interior de 4px en todos los lados.

        // ── Label "Nombre:" ──
        JLabel lNombre = new JLabel("Nombre:");
        // Texto descriptivo antes del campo de nombre.
        lNombre.setForeground(Color.WHITE);
        // Blanco para que se vea sobre el fondo oscuro.

        // ── Campo del nombre ──
        campoNombre = new JTextField("Usuario", 10);
        // Campo de texto con texto inicial "Usuario" y ancho aproximado de 10 caracteres.
        // El usuario lo borra y escribe su nombre real.

        // ── Label "Servidor:" ──
        JLabel lHost = new JLabel("Servidor:");
        lHost.setForeground(Color.WHITE);

        // ── Campo del host ──
        campoHost = new JTextField("localhost", 12);
        // Campo con "localhost" por defecto (conecta al servidor en la misma PC).
        // Para conectar a otro equipo de la red, el usuario escribe su IP.

        // ── Botón CONECTAR ──
        btnConectar = new JButton("Conectar");
        btnConectar.setBackground(new Color(60, 160, 100));  // Verde
        btnConectar.setForeground(Color.WHITE);
        btnConectar.setFocusPainted(false);  // Sin borde de foco al seleccionar con Tab
        btnConectar.setBorderPainted(false); // Sin borde del sistema
        btnConectar.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));
        // Cursor de manito para indicar que es clickeable

        // ── Botón DESCONECTAR ──
        btnDesconectar = new JButton("Desconectar");
        btnDesconectar.setBackground(new Color(180, 60, 60));  // Rojo
        btnDesconectar.setForeground(Color.WHITE);
        btnDesconectar.setFocusPainted(false);
        btnDesconectar.setBorderPainted(false);
        btnDesconectar.setEnabled(false);
        // Empieza deshabilitado (gris) porque no hay conexión activa todavía.
        btnDesconectar.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));

        // ── Label de estado ──
        lblEstado = new JLabel("● Sin conexión");
        // El "●" es un círculo Unicode. Cambia de color (rojo/verde) según el estado.
        lblEstado.setForeground(new Color(200, 80, 80));
        // Rojo inicial: no conectado.
        lblEstado.setFont(new Font("SansSerif", Font.PLAIN, 12));

        // ── Agregar componentes al panel de conexión ──
        panelConexion.add(lNombre);
        panelConexion.add(campoNombre);
        panelConexion.add(Box.createHorizontalStrut(6));
        // Espacio de 6px entre grupos.
        panelConexion.add(lHost);
        panelConexion.add(campoHost);
        panelConexion.add(Box.createHorizontalStrut(6));
        panelConexion.add(btnConectar);
        panelConexion.add(btnDesconectar);
        panelConexion.add(Box.createHorizontalStrut(10));
        panelConexion.add(lblEstado);

        // ════════════════════════════════════════
        // ÁREA DE CHAT (zona CENTER)
        // ════════════════════════════════════════

        areaChat = new JTextArea();
        // Área principal del chat donde aparecen todos los mensajes.
        areaChat.setEditable(false);
        // El usuario NO puede escribir directamente aquí (solo en campoMensaje).
        areaChat.setBackground(new Color(20, 22, 30));
        // Fondo casi negro con tinte azul oscuro.
        areaChat.setForeground(new Color(220, 225, 240));
        // Texto en color blanco-azulado (más suave que blanco puro para los ojos).
        areaChat.setFont(new Font("SansSerif", Font.PLAIN, 14));
        // Fuente sans-serif, tamaño cómodo para leer.
        areaChat.setMargin(new Insets(10, 12, 10, 12));
        // Margen interno: 10px arriba/abajo, 12px izquierda/derecha.
        areaChat.setLineWrap(true);
        // Mensajes largos se cortan y continúan en la siguiente línea.
        areaChat.setWrapStyleWord(true);
        // El corte ocurre entre palabras, no a mitad de una palabra.

        JScrollPane scrollChat = new JScrollPane(areaChat);
        // Envuelve el areaChat con barras de scroll automáticas.
        scrollChat.setBorder(BorderFactory.createMatteBorder(1, 0, 1, 0,
            new Color(60, 65, 90)));
        // Borde solo arriba (1px) y abajo (1px), sin bordes laterales.
        // createMatteBorder(top, left, bottom, right, color)

        // ════════════════════════════════════════
        // PANEL INFERIOR: campo de mensaje + botón enviar
        // ════════════════════════════════════════

        campoMensaje = new JTextField();
        // Campo de texto donde el usuario escribe su mensaje.
        campoMensaje.setFont(new Font("SansSerif", Font.PLAIN, 14));
        campoMensaje.setEnabled(false);
        // Deshabilitado hasta que el usuario se conecte (evita enviar sin conexión).
        campoMensaje.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(new Color(80, 100, 140)),
            // Borde exterior: línea azul grisácea.
            BorderFactory.createEmptyBorder(6, 10, 6, 10)
            // Borde interior: padding de 6px arriba/abajo, 10px izquierda/derecha.
            // createCompoundBorder combina dos bordes en uno.
        ));

        // ── Botón ENVIAR ──
        btnEnviar = new JButton("Enviar");
        btnEnviar.setPreferredSize(new Dimension(90, 36));
        // Tamaño fijo: 90px de ancho, 36px de alto.
        btnEnviar.setBackground(new Color(70, 130, 200));  // Azul
        btnEnviar.setForeground(Color.WHITE);
        btnEnviar.setFocusPainted(false);
        btnEnviar.setBorderPainted(false);
        btnEnviar.setEnabled(false);
        // Deshabilitado hasta conectarse.
        btnEnviar.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));

        // ── Panel que contiene el campo y el botón ──
        JPanel panelSur = new JPanel(new BorderLayout(8, 0));
        // BorderLayout con gap horizontal de 8px entre el campo y el botón.
        panelSur.setBorder(BorderFactory.createEmptyBorder(8, 10, 10, 10));
        // Márgenes exteriores del panel: 8px arriba, 10 lados, 10 abajo.
        panelSur.setBackground(new Color(28, 30, 42));
        // Fondo ligeramente diferente al areaChat para distinguir las zonas.
        panelSur.add(campoMensaje, BorderLayout.CENTER);
        // El campo de texto ocupa todo el espacio central disponible.
        panelSur.add(btnEnviar, BorderLayout.EAST);
        // El botón se ancla a la derecha, con su tamaño preferido (90x36).

        // ════════════════════════════════════════
        // ASIGNAR ACCIONES A LOS COMPONENTES
        // ════════════════════════════════════════

        btnConectar.addActionListener(e -> conectar());
        // Al hacer clic en "Conectar" → llama al método conectar().

        btnDesconectar.addActionListener(e -> desconectar());
        // Al hacer clic en "Desconectar" → llama al método desconectar().

        btnEnviar.addActionListener(e -> enviarMensaje());
        // Al hacer clic en "Enviar" → llama al método enviarMensaje().

        campoMensaje.addActionListener(e -> enviarMensaje());
        // Al presionar ENTER en el campo de texto → también envía el mensaje.
        // ActionListener en JTextField se dispara con la tecla Enter.

        // ── Limpiar texto placeholder del campo nombre ──
        campoNombre.addFocusListener(new FocusAdapter() {
            // FocusAdapter → listener que detecta cuando un componente gana/pierde foco.
            @Override
            public void focusGained(FocusEvent e) {
                // Se llama cuando el usuario hace clic en el campo nombre.
                if (campoNombre.getText().equals("Usuario"))
                    campoNombre.setText("");
                // Si el campo aún tiene el texto por defecto "Usuario", lo borra.
                // Simula el comportamiento de un "placeholder" (texto de guía).
            }
        });

        // ════════════════════════════════════════
        // LAYOUT GENERAL DE LA VENTANA
        // ════════════════════════════════════════

        setLayout(new BorderLayout());
        // BorderLayout divide la ventana en: NORTH, CENTER, SOUTH.
        add(panelConexion, BorderLayout.NORTH);
        // Panel de conexión (nombre, host, botones) → arriba.
        add(scrollChat, BorderLayout.CENTER);
        // Área de chat con scroll → centro (expande con la ventana).
        add(panelSur, BorderLayout.SOUTH);
        // Campo de mensaje + botón Enviar → abajo.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: conectar()
    // Intenta abrir la conexión TCP al servidor y arranca el hilo receptor
    // ─────────────────────────────────────────────────────────────────────────

    private void conectar() {
        String nombre = campoNombre.getText().trim();
        // Obtiene el nombre del campo y elimina espacios al inicio/fin con trim().

        String host = campoHost.getText().trim();
        // Obtiene la dirección del servidor (ej: "localhost" o "192.168.1.5").

        if (nombre.isEmpty()) {
            // Si el usuario dejó el nombre en blanco:
            JOptionPane.showMessageDialog(this, "Ingresa tu nombre.", "Aviso",
                JOptionPane.WARNING_MESSAGE);
            // Muestra un diálogo de advertencia.
            return;
            // Sale del método sin intentar conectar.
        }

        new Thread(() -> {
            // Crea un Thread para la conexión.
            // La conexión de red puede tardar (o fallar con timeout).
            // Si lo hacemos en el EDT, la ventana se congela mientras espera.

            try {

                socket = new Socket(host, ServidorChat.PUERTO);
                // Crea el Socket TCP intentando conectar a "host" en el puerto 5000.
                // Si el servidor no está corriendo, lanza ConnectException (IOException).
                // Este es el "apretón de manos" TCP: SYN → SYN-ACK → ACK.

                salida = new PrintWriter(
                    new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true
                );
                // Crea el stream de escritura hacia el servidor.
                // "UTF-8" → codificación de caracteres (soporta tildes, ñ, etc.)
                // "true"  → autoFlush: envía los datos inmediatamente sin buffer

                salida.println(nombre);
                // PRIMER mensaje al servidor: el nombre del usuario.
                // ManejadorCliente.run() espera este primer readLine() para saber
                // cómo identificar al usuario en el chat.

                conectado = true;
                // Marca la conexión como activa. El método enviarMensaje() lo chequea.

                // ── Actualizar la UI (solo desde el EDT) ──
                SwingUtilities.invokeLater(() -> {
                    setTitle("Chat Java — " + nombre);
                    // Cambia el título de la ventana para mostrar el nombre conectado.
                    lblEstado.setText("● Conectado como " + nombre);
                    // Actualiza el label de estado con el nombre.
                    lblEstado.setForeground(new Color(80, 200, 120));
                    // Cambia el color del punto a verde (conectado).
                    btnConectar.setEnabled(false);
                    // Deshabilita "Conectar" (ya estamos conectados).
                    btnDesconectar.setEnabled(true);
                    // Habilita "Desconectar".
                    campoMensaje.setEnabled(true);
                    // Habilita el campo de texto para escribir mensajes.
                    btnEnviar.setEnabled(true);
                    // Habilita el botón enviar.
                    campoNombre.setEnabled(false);
                    // Bloquea el campo nombre (no se puede cambiar estando conectado).
                    campoHost.setEnabled(false);
                    // Bloquea el campo host (idem).
                    campoMensaje.requestFocus();
                    // Pone el foco en el campo de mensaje para que el usuario
                    // pueda escribir inmediatamente sin hacer clic.
                });

                // ── Thread receptor: escucha mensajes del servidor ──
                BufferedReader entrada = new BufferedReader(
                    new InputStreamReader(socket.getInputStream(), "UTF-8")
                );
                // Stream de lectura del servidor.
                // getInputStream() → bytes que llegan del servidor
                // InputStreamReader → convierte bytes a chars con UTF-8
                // BufferedReader → permite leer línea por línea con readLine()

                String msg;
                while ((msg = entrada.readLine()) != null) {
                    // Loop infinito: espera y lee mensajes que envía el servidor.
                    // readLine() es BLOQUEANTE: para aquí hasta que llegue un '\n'.
                    // Devuelve null cuando el servidor cierra la conexión.

                    final String linea = msg;
                    // Copia la variable a una "effectively final" para usarla
                    // dentro de la lambda (Java lo exige para lambdas).

                    SwingUtilities.invokeLater(() -> recibirMensaje(linea));
                    // Encola la actualización del JTextArea en el EDT.
                    // Nunca tocar componentes Swing desde un hilo que no sea EDT.
                }

            } catch (IOException ex) {
                // IOException ocurre si:
                //   - El servidor no está corriendo (ConnectException)
                //   - El servidor cerró la conexión abruptamente
                //   - Error de red

                SwingUtilities.invokeLater(() ->
                    JOptionPane.showMessageDialog(this,
                        "No se pudo conectar a " + host + ":" + ServidorChat.PUERTO
                        + "\n\nVerifica que el servidor esté iniciado.",
                        "Error de conexión", JOptionPane.ERROR_MESSAGE)
                    // Muestra un diálogo de error con detalles del problema.
                );
                desconectarUI();
                // Restaura la interfaz al estado "sin conexión" (botones, labels).
            }

        }).start();
        // ".start()" arranca el hilo de conexión + recepción.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: desconectar()
    // Cierra la conexión con el servidor limpiamente
    // ─────────────────────────────────────────────────────────────────────────

    private void desconectar() {
        if (conectado && salida != null) {
            // Solo enviamos "/salir" si estamos conectados y el stream existe.
            salida.println("/salir");
            // Envía el comando de salida al servidor.
            // ManejadorCliente.run() lo detecta con equalsIgnoreCase("/salir")
            // y sale del bucle de lectura, cerrando limpiamente el hilo.
        }
        try {
            if (socket != null && !socket.isClosed()) socket.close();
            // Cierra el socket si existe y no estaba ya cerrado.
            // Al cerrar el socket, el readLine() del thread receptor devuelve null
            // y el while termina naturalmente.
        } catch (IOException ignored) {
            // Si el socket ya estaba cerrado, ignoramos el error.
        }
        conectado = false;
        // Marca el estado como desconectado.
        desconectarUI();
        // Actualiza los componentes visuales al estado "sin conexión".
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: desconectarUI()
    // Restaura la interfaz gráfica al estado inicial (desconectado)
    // ─────────────────────────────────────────────────────────────────────────

    private void desconectarUI() {
        SwingUtilities.invokeLater(() -> {
            // Todo cambio en componentes Swing debe hacerse en el EDT.
            lblEstado.setText("● Sin conexión");
            lblEstado.setForeground(new Color(200, 80, 80));
            // El punto vuelve a rojo.
            btnConectar.setEnabled(true);
            // Re-habilita el botón conectar.
            btnDesconectar.setEnabled(false);
            // Deshabilita el botón desconectar.
            campoMensaje.setEnabled(false);
            // Bloquea el campo de texto (sin conexión no se puede enviar).
            btnEnviar.setEnabled(false);
            // Deshabilita el botón enviar.
            campoNombre.setEnabled(true);
            // Desbloquea el campo nombre para que el usuario pueda cambiarlo.
            campoHost.setEnabled(true);
            // Desbloquea el campo host.
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: enviarMensaje()
    // Lee el texto del campo, lo envía al servidor, y limpia el campo
    // ─────────────────────────────────────────────────────────────────────────

    private void enviarMensaje() {
        if (!conectado || salida == null) return;
        // Doble verificación de seguridad: no enviar si no hay conexión activa.
        // "return" sale inmediatamente del método sin hacer nada.

        String texto = campoMensaje.getText().trim();
        // Obtiene el texto escrito por el usuario y elimina espacios al inicio/fin.

        if (texto.isEmpty()) return;
        // Si el campo está vacío, no enviamos nada (evita mensajes en blanco).

        salida.println(texto);
        // Envía el texto al servidor a través del PrintWriter.
        // El servidor lo reenviará a todos los clientes conectados (broadcast).

        campoMensaje.setText("");
        // Limpia el campo de texto después de enviar, listo para el siguiente mensaje.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: recibirMensaje()
    // Agrega un mensaje recibido del servidor al área de chat
    // SIEMPRE debe llamarse desde el EDT (ver SwingUtilities.invokeLater en conectar)
    // ─────────────────────────────────────────────────────────────────────────

    private void recibirMensaje(String msg) {
        if (msg.startsWith("✔") || msg.startsWith("✖")) {
            // Los mensajes de sistema (conexión/desconexión) tienen iconos especiales:
            //   ✔ = alguien se unió
            //   ✖ = alguien salió
            areaChat.append("\n" + msg + "\n\n");
            // Los mensajes de sistema se muestran con líneas en blanco antes y después
            // para que destaquen visualmente del flujo de mensajes normales.
        } else {
            areaChat.append(msg + "\n");
            // Mensajes normales de chat: simplemente se agregan con un salto de línea.
        }
        areaChat.setCaretPosition(areaChat.getDocument().getLength());
        // Mueve el "cursor invisible" al final del documento.
        // Esto provoca que el JScrollPane haga scroll automático hacia abajo,
        // mostrando siempre el mensaje más reciente sin que el usuario tenga
        // que scrollear manualmente.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO MAIN — Punto de entrada del CLIENTE
    // Se ejecuta cuando corres VentanaCliente.java en Eclipse
    // ─────────────────────────────────────────────────────────────────────────

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            // Ejecuta el código dentro del EDT (Event Dispatch Thread).
            // Toda creación de ventanas Swing debe hacerse en el EDT
            // para evitar condiciones de carrera en la interfaz gráfica.

            try {
                UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
                // Aplica el Look & Feel nativo del sistema operativo.
                // getSystemLookAndFeelClassName() detecta automáticamente:
                //   Windows 10/11 → com.sun.java.swing.plaf.windows.WindowsLookAndFeel
                //   macOS         → com.apple.laf.AquaLookAndFeel
                //   Linux/GTK     → com.sun.java.swing.plaf.gtk.GTKLookAndFeel
                // Sin esto, Swing usa "Metal" (aspecto genérico gris de los años 90).
            } catch (Exception ignored) {
                // Si falla la carga del Look&Feel nativo (ej: servidor sin pantalla),
                // simplemente continuamos con el L&F por defecto.
            }

            new VentanaCliente().setVisible(true);
            // Crea la ventana del cliente y la hace visible en pantalla.
            // setVisible(true) también desencadena el primer renderizado (paint).
        });
    }
}
