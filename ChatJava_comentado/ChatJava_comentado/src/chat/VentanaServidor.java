package chat; // Pertenece al paquete "chat"

// Swing: librería gráfica de Java para crear ventanas y componentes visuales
import javax.swing.*;          // JFrame, JTextArea, JButton, JLabel, JScrollPane, etc.
import javax.swing.border.*;   // BorderFactory para crear bordes visuales en componentes

// AWT: librería base de gráficos (Swing está construido sobre AWT)
import java.awt.*;             // Color, Font, BorderLayout, FlowLayout, Insets, Dimension
import java.awt.event.*;       // WindowAdapter, WindowEvent, ActionListener (eventos de UI)

/**
 * VentanaServidor.java
 * ---------------------
 * Esta clase es la INTERFAZ GRÁFICA del servidor (ventana Swing).
 * Extiende JFrame, que es la clase base de una ventana en Java Swing.
 *
 * Contiene:
 *   - Un área de texto (JTextArea) que muestra el log de actividad del servidor
 *   - Un botón para INICIAR el servidor (crea ServidorChat y llama iniciar())
 *   - Un botón para DETENER el servidor
 *   - Un label que muestra cuántos clientes están conectados
 *
 * También tiene el método main() → es el punto de entrada del lado SERVIDOR.
 */
public class VentanaServidor extends JFrame {
    // "extends JFrame" = VentanaServidor ES una ventana Swing.
    // Hereda todos los métodos de JFrame: setTitle, setSize, setVisible, add, etc.

    // ─────────────────────────────────────────────────────────────────────────
    // ATRIBUTOS (componentes de la interfaz)
    // ─────────────────────────────────────────────────────────────────────────

    private JTextArea areaLog;
    // Área de texto de múltiples líneas donde se muestran los eventos del servidor:
    // quién se conectó, qué mensajes se enviaron, errores, etc.
    // No es editable por el usuario (setEditable(false)).

    private JLabel lblConectados;
    // Etiqueta de texto que muestra "Clientes: 0", "Clientes: 2", etc.
    // Se actualiza cada vez que alguien se conecta o desconecta.

    private JButton btnIniciar, btnDetener;
    // Dos botones de acción:
    //   btnIniciar → crea un ServidorChat y llama iniciar()
    //   btnDetener → llama detener() y deshabilita el botón

    private ServidorChat servidor;
    // Referencia al objeto ServidorChat que maneja la lógica de red.
    // Es null hasta que el usuario presiona "Iniciar".

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────────────────────────────────────

    public VentanaServidor() {

        setTitle("Chat Java — Servidor");
        // Establece el texto de la barra de título de la ventana.

        setDefaultCloseOperation(JFrame.DO_NOTHING_ON_CLOSE);
        // Por defecto, cerrar la "X" de la ventana cerraría todo sin avisar.
        // DO_NOTHING_ON_CLOSE = la X no hace NADA por sí sola.
        // El comportamiento real se define en el WindowListener de abajo,
        // que muestra un diálogo de confirmación antes de cerrar.

        setSize(520, 480);
        // Tamaño inicial de la ventana: 520 píxeles de ancho, 480 de alto.

        setLocationRelativeTo(null);
        // Centra la ventana en la pantalla.
        // "null" = relativo al centro del monitor principal.

        setResizable(true);
        // Permite que el usuario redimensione la ventana arrastrando los bordes.

        // ── Listener para el cierre de la ventana ──
        addWindowListener(new WindowAdapter() {
            // WindowAdapter es una clase abstracta que implementa WindowListener.
            // Solo sobreescribimos el método que nos interesa: windowClosing.

            @Override
            public void windowClosing(WindowEvent e) {
                // Se llama cuando el usuario presiona la X de la ventana.

                int op = JOptionPane.showConfirmDialog(
                    VentanaServidor.this,
                    // Ventana padre del diálogo (para centrarlo sobre ella)
                    "¿Detener el servidor y cerrar?",
                    // Mensaje mostrado en el diálogo
                    "Confirmar cierre",
                    // Título del diálogo
                    JOptionPane.YES_NO_OPTION
                    // Muestra botones "Sí" y "No"
                );

                if (op == JOptionPane.YES_OPTION) {
                    // Si el usuario confirmó con "Sí":
                    if (servidor != null) servidor.detener();
                    // Detiene el servidor (cierra el ServerSocket) solo si fue iniciado.
                    System.exit(0);
                    // Termina completamente la JVM (cierra todos los threads).
                }
                // Si eligió "No", el método termina sin hacer nada → ventana sigue abierta.
            }
        });

        construirUI();
        // Llama al método que construye y agrega todos los componentes visuales.
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: construirUI()
    // Construye toda la interfaz gráfica: paneles, botones, área de log, etc.
    // ─────────────────────────────────────────────────────────────────────────

    private void construirUI() {

        // ════════════════════════════════════════
        // PANEL SUPERIOR (barra de controles)
        // ════════════════════════════════════════

        JPanel panelTop = new JPanel(new FlowLayout(FlowLayout.LEFT, 12, 8));
        // JPanel = contenedor rectangular para agrupar componentes.
        // FlowLayout = los componentes se acomodan en fila de izquierda a derecha.
        //   LEFT  = alineados a la izquierda
        //   12    = espacio horizontal entre componentes
        //   8     = espacio vertical (padding arriba/abajo)

        panelTop.setBackground(new Color(45, 45, 60));
        // Color de fondo del panel superior: azul oscuro en formato RGB (R, G, B).

        // ── Título del panel ──
        JLabel titulo = new JLabel("  SERVIDOR CHAT");
        // JLabel = componente de texto no editable (etiqueta).
        titulo.setForeground(Color.WHITE);
        // Color del texto: blanco.
        titulo.setFont(new Font("SansSerif", Font.BOLD, 14));
        // Fuente: SansSerif (sin serifa), negrita, tamaño 14px.

        // ── Label de contador de clientes ──
        lblConectados = new JLabel("Clientes: 0");
        // Texto inicial: 0 clientes conectados.
        lblConectados.setForeground(new Color(100, 220, 150));
        // Color verde suave para el texto del contador.
        lblConectados.setFont(new Font("SansSerif", Font.PLAIN, 13));
        // Fuente normal (no negrita), tamaño 13.

        // ── Botón INICIAR ──
        btnIniciar = new JButton("▶  Iniciar");
        // JButton = botón clickeable. El "▶" es un carácter Unicode de play.
        btnIniciar.setBackground(new Color(60, 170, 100));
        // Fondo verde para el botón de iniciar.
        btnIniciar.setForeground(Color.WHITE);
        // Texto blanco sobre el fondo verde.
        btnIniciar.setFocusPainted(false);
        // Elimina el borde de "foco" azul que aparece al seleccionar con Tab.
        btnIniciar.setBorderPainted(false);
        // Elimina el borde predeterminado del botón para un look más limpio.
        btnIniciar.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));
        // Cambia el cursor a "manito" cuando el mouse está sobre el botón.

        // ── Botón DETENER ──
        btnDetener = new JButton("■  Detener");
        // El "■" es un carácter Unicode de cuadrado (parar).
        btnDetener.setBackground(new Color(190, 60, 60));
        // Fondo rojo para el botón de detener.
        btnDetener.setForeground(Color.WHITE);
        btnDetener.setFocusPainted(false);
        btnDetener.setBorderPainted(false);
        btnDetener.setEnabled(false);
        // El botón Detener empieza DESHABILITADO (gris) porque no hay servidor activo.
        // Se habilita cuando el usuario presiona Iniciar.
        btnDetener.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));

        // ── Agregar todos los componentes al panel superior ──
        panelTop.add(titulo);
        panelTop.add(Box.createHorizontalStrut(20));
        // Box.createHorizontalStrut(20) = espacio invisible horizontal de 20px.
        // Sirve para separar visualmente grupos de componentes.
        panelTop.add(lblConectados);
        panelTop.add(Box.createHorizontalStrut(20));
        panelTop.add(btnIniciar);
        panelTop.add(btnDetener);

        // ════════════════════════════════════════
        // ÁREA DE LOG (centro de la ventana)
        // ════════════════════════════════════════

        areaLog = new JTextArea();
        // JTextArea = campo de texto multilínea.
        areaLog.setEditable(false);
        // El usuario no puede escribir en el log, solo leer.
        areaLog.setBackground(new Color(25, 25, 35));
        // Fondo oscuro tipo "consola" (casi negro con tinte azul).
        areaLog.setForeground(new Color(200, 220, 200));
        // Texto en verde pálido, estilo terminal.
        areaLog.setFont(new Font("Monospaced", Font.PLAIN, 13));
        // Fuente monoespaciada (tipo consola), cada carácter tiene el mismo ancho.
        // Facilita leer logs alineados.
        areaLog.setMargin(new Insets(8, 10, 8, 10));
        // Insets = márgenes internos del texto: (top=8, left=10, bottom=8, right=10).
        areaLog.setLineWrap(true);
        // Las líneas largas se cortan y continúan en la siguiente línea (no se salen).
        areaLog.setWrapStyleWord(true);
        // Al hacer wrap, corta por palabras completas (no a mitad de una palabra).

        JScrollPane scroll = new JScrollPane(areaLog);
        // JScrollPane = envuelve el JTextArea con barras de scroll automáticas.
        // Cuando el texto supera el área visible, aparece la barra de desplazamiento.
        scroll.setBorder(BorderFactory.createEmptyBorder());
        // Elimina el borde del JScrollPane para que no se vea el doble borde.

        // ════════════════════════════════════════
        // PANEL INFERIOR (botón limpiar)
        // ════════════════════════════════════════

        JButton btnLimpiar = new JButton("Limpiar log");
        // Botón para borrar todo el contenido del areaLog.
        btnLimpiar.setFocusPainted(false);
        btnLimpiar.addActionListener(e -> areaLog.setText(""));
        // Listener de acción: cuando se clickea, setText("") borra todo el texto.
        // La lambda "e -> ..." es equivalente a new ActionListener() { actionPerformed... }

        JPanel panelBottom = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 6));
        // Panel con FlowLayout alineado a la derecha, para poner el botón al final.
        panelBottom.add(btnLimpiar);

        // ════════════════════════════════════════
        // ACCIONES DE LOS BOTONES PRINCIPALES
        // ════════════════════════════════════════

        btnIniciar.addActionListener(e -> {
            // Se ejecuta cuando el usuario hace clic en "▶ Iniciar"
            servidor = new ServidorChat(this);
            // Crea una nueva instancia de ServidorChat,
            // pasando "this" (esta ventana) para que pueda llamar log() y actualizarContador().
            servidor.iniciar();
            // Arranca el servidor: abre el ServerSocket y empieza a aceptar conexiones.
            btnIniciar.setEnabled(false);
            // Deshabilita el botón Iniciar para que no se pulse dos veces.
            btnDetener.setEnabled(true);
            // Habilita el botón Detener ahora que el servidor está activo.
        });

        btnDetener.addActionListener(e -> {
            // Se ejecuta cuando el usuario hace clic en "■ Detener"
            if (servidor != null) servidor.detener();
            // Llama a detener() solo si el servidor fue iniciado (no es null).
            log("\n[Servidor detenido]");
            // Agrega un mensaje al log indicando que el servidor paró.
            btnIniciar.setEnabled(true);
            // Re-habilita el botón Iniciar para poder volver a arrancar.
            btnDetener.setEnabled(false);
            // Deshabilita Detener porque ya no hay servidor activo.
        });

        // ════════════════════════════════════════
        // LAYOUT GENERAL DE LA VENTANA
        // ════════════════════════════════════════

        setLayout(new BorderLayout());
        // BorderLayout divide la ventana en 5 zonas: NORTH, SOUTH, EAST, WEST, CENTER.
        // Es el layout más común para ventanas principales.

        add(panelTop, BorderLayout.NORTH);
        // El panel de controles va arriba (zona NORTH).
        add(scroll, BorderLayout.CENTER);
        // El área de log con scroll va en el centro (ocupa todo el espacio restante).
        add(panelBottom, BorderLayout.SOUTH);
        // El panel con el botón "Limpiar" va abajo (zona SOUTH).
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: log()
    // Agrega una línea de texto al JTextArea del log
    // Llamado desde ServidorChat y otros hilos → DEBE usar SwingUtilities
    // ─────────────────────────────────────────────────────────────────────────

    public void log(String mensaje) {
        SwingUtilities.invokeLater(() -> {
            // SwingUtilities.invokeLater = encola la actualización en el EDT
            // (Event Dispatch Thread), que es el único hilo autorizado a modificar
            // componentes Swing. Sin esto, modificar la UI desde otro hilo
            // causaría errores aleatorios o pantallas congeladas.

            areaLog.append(mensaje + "\n");
            // append() agrega el texto al FINAL del área (no lo reemplaza).
            // + "\n" agrega un salto de línea después de cada mensaje.

            areaLog.setCaretPosition(areaLog.getDocument().getLength());
            // Mueve el cursor (caret) al final del documento.
            // Esto hace que el scroll baje automáticamente para mostrar
            // el mensaje más reciente. Sin esta línea, el scroll se queda arriba.
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO: actualizarContador()
    // Actualiza el label "Clientes: N" con el número actual de conectados
    // ─────────────────────────────────────────────────────────────────────────

    public void actualizarContador(int n) {
        SwingUtilities.invokeLater(() ->
            // Igual que log(), debe ejecutarse en el EDT para modificar un JLabel.
            lblConectados.setText("Clientes: " + n)
            // Cambia el texto del JLabel al nuevo valor.
            // Java convierte el int n a String automáticamente con la concatenación.
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODO MAIN — Punto de entrada del SERVIDOR
    // Se ejecuta cuando corres VentanaServidor.java en Eclipse
    // ─────────────────────────────────────────────────────────────────────────

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            // Crea y muestra la ventana DENTRO del EDT.
            // Nunca se debe crear una ventana Swing fuera del EDT.

            try {
                UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
                // Aplica el Look & Feel nativo del sistema operativo:
                //   Windows → apariencia Windows
                //   macOS   → apariencia macOS
                //   Linux   → apariencia GTK
                // Sin esto, Swing usa su apariencia genérica "Metal" (gris clásico).
            } catch (Exception ignored) {
                // Si falla (entorno sin display o Look&Feel no disponible), ignoramos.
                // La app igual funciona con el Look&Feel por defecto.
            }

            new VentanaServidor().setVisible(true);
            // Crea la ventana del servidor y la hace visible.
            // setVisible(true) = mostrar la ventana en pantalla.
        });
    }
}
