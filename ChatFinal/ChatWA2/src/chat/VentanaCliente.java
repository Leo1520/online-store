package chat;

import javax.swing.*;
import javax.swing.border.*;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;
import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.net.*;
import java.util.*;

public class VentanaCliente extends JFrame {

    private static final String CHAT_TODOS = "★ Todos";

    private final Map<String, StringBuilder> historiales = new LinkedHashMap<>();
    private final Map<String, Integer>       noLeidos    = new HashMap<>();
    private final java.util.List<String>     todosChats  = new ArrayList<>();
    private final DefaultListModel<String>   modeloVis   = new DefaultListModel<>();
    private JList<String>   listaChats;
    private String          chatActivo = CHAT_TODOS;

    private JLabel     lblTitulo, lblSubtitulo;
    private JTextArea  areaChat;
    private JTextField campoMensaje, campoBusqueda;
    private JButton    btnEnviar;

    private JTextField campoNombre, campoHost;
    private JButton    btnConectar, btnDesconectar;
    private JLabel     lblEstado;

    private Socket      socket;
    private PrintWriter salida;
    private boolean     conectado = false;
    private String      miNombre  = "";

    private static final Color BG_OSCURO   = new Color(15,  18,  30);
    private static final Color BG_PANEL    = new Color(20,  25,  40);
    private static final Color BG_ITEM     = new Color(24,  30,  48);
    private static final Color BG_SEL      = new Color(38,  52,  88);
    private static final Color BG_CAB      = new Color(18,  24,  42);
    private static final Color BG_BARRA    = new Color(13,  17,  32);
    private static final Color BG_SRCH     = new Color(28,  35,  56);
    private static final Color BG_CHAT     = new Color(12,  15,  26);
    private static final Color BG_INPUT    = new Color(22,  28,  46);
    private static final Color VERDE       = new Color(37,  165,  90);
    private static final Color TX1         = new Color(225, 230, 250);
    private static final Color TX2         = new Color(140, 155, 195);
    private static final Color TX3         = new Color(80,  95,  140);
    private static final Color BORDE       = new Color(30,  38,  64);

    public VentanaCliente() {
        setTitle("Chat Java");
        setDefaultCloseOperation(JFrame.DO_NOTHING_ON_CLOSE);
        setSize(920, 610);
        setMinimumSize(new Dimension(700, 460));
        setLocationRelativeTo(null);
        historiales.put(CHAT_TODOS, new StringBuilder());
        noLeidos.put(CHAT_TODOS, 0);
        todosChats.add(CHAT_TODOS);
        modeloVis.addElement(CHAT_TODOS);
        addWindowListener(new WindowAdapter() {
            @Override public void windowClosing(WindowEvent e) { desconectar(); System.exit(0); }
        });
        construirUI();
    }

    private void construirUI() {

        // ── BARRA SUPERIOR ────────────────────────────────────────────────
        JPanel barra = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 8));
        barra.setBackground(BG_BARRA);
        barra.setBorder(BorderFactory.createMatteBorder(0, 0, 1, 0, BORDE));
        JLabel logo = new JLabel("  \uD83D\uDCAC  ChatJava");
        logo.setForeground(VERDE);
        logo.setFont(new Font("SansSerif", Font.BOLD, 15));
        JLabel sep = new JLabel("  |  "); sep.setForeground(TX3);
        campoNombre = input("Usuario", 10);
        campoHost   = input("localhost", 11);
        btnConectar    = btn("Conectar",    VERDE);
        btnDesconectar = btn("Desconectar", new Color(180, 55, 55));
        btnDesconectar.setEnabled(false);
        lblEstado = new JLabel("● Sin conexión");
        lblEstado.setForeground(new Color(180, 65, 65));
        lblEstado.setFont(new Font("SansSerif", Font.PLAIN, 12));
        barra.add(logo); barra.add(sep);
        barra.add(lbl("Nombre:")); barra.add(campoNombre);
        barra.add(Box.createHorizontalStrut(4));
        barra.add(lbl("Servidor:")); barra.add(campoHost);
        barra.add(Box.createHorizontalStrut(4));
        barra.add(btnConectar); barra.add(btnDesconectar);
        barra.add(Box.createHorizontalStrut(8));
        barra.add(lblEstado);

        // ── PANEL IZQUIERDO ───────────────────────────────────────────────
        JPanel izq = new JPanel(new BorderLayout());
        izq.setPreferredSize(new Dimension(255, 0));
        izq.setBackground(BG_PANEL);
        izq.setBorder(BorderFactory.createMatteBorder(0, 0, 0, 1, BORDE));

        // Cabecera izq
        JPanel cabIzq = new JPanel(new BorderLayout());
        cabIzq.setBackground(BG_CAB);
        cabIzq.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(0, 0, 1, 0, BORDE),
            BorderFactory.createEmptyBorder(12, 14, 12, 14)));
        JLabel lblChats = new JLabel("Chats");
        lblChats.setForeground(TX1);
        lblChats.setFont(new Font("SansSerif", Font.BOLD, 15));
        cabIzq.add(lblChats, BorderLayout.WEST);

        // Buscador
        JPanel panelBusq = new JPanel(new BorderLayout(6, 0));
        panelBusq.setBackground(BG_PANEL);
        panelBusq.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(0, 0, 1, 0, BORDE),
            BorderFactory.createEmptyBorder(8, 10, 8, 10)));
        JLabel icoBusq = new JLabel("\uD83D\uDD0D");
        icoBusq.setFont(new Font("SansSerif", Font.PLAIN, 13));
        campoBusqueda = new JTextField();
        campoBusqueda.setFont(new Font("SansSerif", Font.PLAIN, 13));
        campoBusqueda.setBackground(BG_SRCH);
        campoBusqueda.setForeground(TX3);
        campoBusqueda.setCaretColor(TX1);
        campoBusqueda.setText("Buscar contacto...");
        campoBusqueda.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(BORDE),
            BorderFactory.createEmptyBorder(5, 10, 5, 10)));
        campoBusqueda.addFocusListener(new FocusAdapter() {
            @Override public void focusGained(FocusEvent e) {
                if (campoBusqueda.getText().equals("Buscar contacto...")) {
                    campoBusqueda.setText(""); campoBusqueda.setForeground(TX1);
                }
            }
            @Override public void focusLost(FocusEvent e) {
                if (campoBusqueda.getText().isEmpty()) {
                    campoBusqueda.setText("Buscar contacto..."); campoBusqueda.setForeground(TX3);
                }
            }
        });
        campoBusqueda.getDocument().addDocumentListener(new DocumentListener() {
            @Override public void insertUpdate(DocumentEvent e)  { filtrar(); }
            @Override public void removeUpdate(DocumentEvent e)  { filtrar(); }
            @Override public void changedUpdate(DocumentEvent e) { filtrar(); }
        });
        panelBusq.add(icoBusq,       BorderLayout.WEST);
        panelBusq.add(campoBusqueda, BorderLayout.CENTER);

        JPanel topIzq = new JPanel(new BorderLayout());
        topIzq.add(cabIzq,    BorderLayout.NORTH);
        topIzq.add(panelBusq, BorderLayout.CENTER);

        // Lista de chats
        listaChats = new JList<>(modeloVis);
        listaChats.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        listaChats.setSelectedIndex(0);
        listaChats.setBackground(BG_PANEL);
        listaChats.setFixedCellHeight(65);
        listaChats.setBorder(BorderFactory.createEmptyBorder());

        listaChats.setCellRenderer(new ListCellRenderer<String>() {
            @Override
            public Component getListCellRendererComponent(JList<? extends String> list,
                    String chat, int idx, boolean sel, boolean focus) {

                JPanel row = new JPanel(new BorderLayout(10, 0));
                row.setBackground(sel ? BG_SEL : BG_ITEM);
                row.setBorder(BorderFactory.createCompoundBorder(
                    BorderFactory.createMatteBorder(0, 0, 1, 0, BORDE),
                    BorderFactory.createEmptyBorder(10, 12, 10, 10)));

                // Avatar
                JPanel av = new JPanel() {
                    @Override protected void paintComponent(Graphics g) {
                        Graphics2D g2 = (Graphics2D) g.create();
                        g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
                        g2.setColor(colorAv(chat));
                        g2.fillOval(0, 0, getWidth()-1, getHeight()-1);
                        g2.dispose(); super.paintComponent(g);
                    }
                };
                av.setOpaque(false);
                av.setPreferredSize(new Dimension(42, 42));
                av.setLayout(new GridBagLayout());
                String ini = CHAT_TODOS.equals(chat) ? "\u2605"
                           : String.valueOf(chat.charAt(0)).toUpperCase();
                JLabel lIni = new JLabel(ini);
                lIni.setForeground(Color.WHITE);
                lIni.setFont(new Font("SansSerif", Font.BOLD, CHAT_TODOS.equals(chat) ? 14 : 16));
                av.add(lIni);

                // Nombre + preview
                JPanel info = new JPanel(new GridLayout(2, 1, 0, 3));
                info.setOpaque(false);
                JLabel lNom = new JLabel(chat);
                lNom.setForeground(sel ? Color.WHITE : TX1);
                lNom.setFont(new Font("SansSerif", Font.BOLD, 13));
                String prev = preview(chat);
                JLabel lPrev = new JLabel(prev);
                lPrev.setForeground(TX2);
                lPrev.setFont(new Font("SansSerif", Font.PLAIN, 11));
                info.add(lNom); info.add(lPrev);

                // Badge de no leídos
                int cnt = noLeidos.getOrDefault(chat, 0);
                JPanel derecho = new JPanel(new BorderLayout());
                derecho.setOpaque(false);
                derecho.setPreferredSize(new Dimension(36, 42));
                if (cnt > 0) {
                    String btxt = cnt > 99 ? "99+" : String.valueOf(cnt);
                    JLabel badge = new JLabel(btxt) {
                        @Override protected void paintComponent(Graphics g) {
                            Graphics2D g2 = (Graphics2D) g.create();
                            g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
                            g2.setColor(VERDE);
                            g2.fillRoundRect(0, 0, getWidth(), getHeight(), getHeight(), getHeight());
                            g2.dispose(); super.paintComponent(g);
                        }
                    };
                    badge.setForeground(Color.WHITE);
                    badge.setFont(new Font("SansSerif", Font.BOLD, 10));
                    badge.setHorizontalAlignment(SwingConstants.CENTER);
                    badge.setOpaque(false);
                    int bw = cnt > 9 ? 28 : 20;
                    badge.setPreferredSize(new Dimension(bw, 18));
                    JPanel wp = new JPanel(new FlowLayout(FlowLayout.RIGHT, 0, 12));
                    wp.setOpaque(false); wp.add(badge);
                    derecho.add(wp, BorderLayout.CENTER);
                }
                row.add(av,      BorderLayout.WEST);
                row.add(info,    BorderLayout.CENTER);
                row.add(derecho, BorderLayout.EAST);
                return row;
            }
        });

        listaChats.addListSelectionListener(e -> {
            if (!e.getValueIsAdjusting()) {
                String sel = listaChats.getSelectedValue();
                if (sel != null) {
                    noLeidos.put(sel, 0);
                    listaChats.repaint();
                    cambiarChat(sel);
                }
            }
        });

        JScrollPane scrollLista = new JScrollPane(listaChats);
        scrollLista.setBorder(BorderFactory.createEmptyBorder());
        scrollLista.getViewport().setBackground(BG_PANEL);

        izq.add(topIzq,      BorderLayout.NORTH);
        izq.add(scrollLista, BorderLayout.CENTER);

        // ── PANEL DERECHO ──────────────────────────────────────────────────
        // Cabecera der
        JPanel cabDer = new JPanel(new BorderLayout(12, 0));
        cabDer.setBackground(BG_CAB);
        cabDer.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(0, 0, 1, 0, BORDE),
            BorderFactory.createEmptyBorder(10, 16, 10, 16)));

        JPanel avCab = new JPanel() {
            @Override protected void paintComponent(Graphics g) {
                Graphics2D g2 = (Graphics2D) g.create();
                g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
                g2.setColor(colorAv(chatActivo));
                g2.fillOval(0, 0, getWidth()-1, getHeight()-1);
                g2.dispose(); super.paintComponent(g);
            }
        };
        avCab.setOpaque(false);
        avCab.setPreferredSize(new Dimension(38, 38));
        avCab.setLayout(new GridBagLayout());
        JLabel iCab = new JLabel("\u2605");
        iCab.setForeground(Color.WHITE);
        iCab.setFont(new Font("SansSerif", Font.BOLD, 15));
        avCab.add(iCab);

        JPanel infoCab = new JPanel();
        infoCab.setOpaque(false);
        infoCab.setLayout(new BoxLayout(infoCab, BoxLayout.Y_AXIS));
        lblTitulo    = new JLabel(CHAT_TODOS);
        lblTitulo.setForeground(TX1);
        lblTitulo.setFont(new Font("SansSerif", Font.BOLD, 14));
        lblSubtitulo = new JLabel("Chat grupal");
        lblSubtitulo.setForeground(TX2);
        lblSubtitulo.setFont(new Font("SansSerif", Font.PLAIN, 11));
        infoCab.add(lblTitulo); infoCab.add(lblSubtitulo);
        cabDer.add(avCab,    BorderLayout.WEST);
        cabDer.add(infoCab,  BorderLayout.CENTER);

        // Área de mensajes
        areaChat = new JTextArea();
        areaChat.setEditable(false);
        areaChat.setBackground(BG_CHAT);
        areaChat.setForeground(TX1);
        areaChat.setFont(new Font("SansSerif", Font.PLAIN, 14));
        areaChat.setMargin(new Insets(14, 18, 14, 18));
        areaChat.setLineWrap(true);
        areaChat.setWrapStyleWord(true);
        JScrollPane scrollChat = new JScrollPane(areaChat);
        scrollChat.setBorder(BorderFactory.createEmptyBorder());
        scrollChat.getViewport().setBackground(BG_CHAT);

        // Panel de envío
        campoMensaje = new JTextField();
        campoMensaje.setFont(new Font("SansSerif", Font.PLAIN, 14));
        campoMensaje.setEnabled(false);
        campoMensaje.setBackground(BG_INPUT);
        campoMensaje.setForeground(TX1);
        campoMensaje.setCaretColor(Color.WHITE);
        campoMensaje.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(BORDE),
            BorderFactory.createEmptyBorder(8, 14, 8, 14)));
        btnEnviar = btn("Enviar \u27A4", VERDE);
        btnEnviar.setPreferredSize(new Dimension(105, 38));
        btnEnviar.setEnabled(false);
        JPanel envio = new JPanel(new BorderLayout(10, 0));
        envio.setBackground(BG_PANEL);
        envio.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(1, 0, 0, 0, BORDE),
            BorderFactory.createEmptyBorder(10, 16, 12, 16)));
        envio.add(campoMensaje, BorderLayout.CENTER);
        envio.add(btnEnviar,    BorderLayout.EAST);

        JPanel der = new JPanel(new BorderLayout());
        der.setBackground(BG_CHAT);
        der.add(cabDer,     BorderLayout.NORTH);
        der.add(scrollChat, BorderLayout.CENTER);
        der.add(envio,      BorderLayout.SOUTH);

        // ── ENSAMBLAJE ─────────────────────────────────────────────────────
        JPanel cuerpo = new JPanel(new BorderLayout());
        cuerpo.setBackground(BG_OSCURO);
        cuerpo.add(izq, BorderLayout.WEST);
        cuerpo.add(der, BorderLayout.CENTER);
        setLayout(new BorderLayout());
        add(barra,  BorderLayout.NORTH);
        add(cuerpo, BorderLayout.CENTER);

        // ── ACCIONES ──────────────────────────────────────────────────────
        btnConectar.addActionListener(e    -> conectar());
        btnDesconectar.addActionListener(e -> desconectar());
        btnEnviar.addActionListener(e      -> enviar());
        campoMensaje.addActionListener(e   -> enviar());
        campoNombre.addFocusListener(new FocusAdapter() {
            @Override public void focusGained(FocusEvent e) {
                if (campoNombre.getText().equals("Usuario")) campoNombre.setText("");
            }
        });
    }

    // ── Filtrar lista con buscador ───────────────────────────────────────────
    private void filtrar() {
        String f = campoBusqueda.getText().trim().toLowerCase();
        if (f.equals("buscar contacto...")) f = "";
        String antes = listaChats.getSelectedValue();
        modeloVis.clear();
        for (String c : todosChats) {
            if (f.isEmpty() || c.toLowerCase().contains(f)) modeloVis.addElement(c);
        }
        if (antes != null && modeloVis.contains(antes)) listaChats.setSelectedValue(antes, true);
        else if (!modeloVis.isEmpty()) listaChats.setSelectedIndex(0);
    }

    // ── Cambiar chat activo ──────────────────────────────────────────────────
    private void cambiarChat(String chat) {
        chatActivo = chat;
        historiales.putIfAbsent(chat, new StringBuilder());
        lblTitulo.setText(chat);
        lblSubtitulo.setText(CHAT_TODOS.equals(chat) ? "Chat grupal" : "Mensaje privado");
        areaChat.setText(historiales.get(chat).toString());
        areaChat.setCaretPosition(areaChat.getDocument().getLength());
    }

    // ── Enviar mensaje ───────────────────────────────────────────────────────
    private void enviar() {
        if (!conectado || salida == null) return;
        String txt = campoMensaje.getText().trim();
        if (txt.isEmpty()) return;
        if (CHAT_TODOS.equals(chatActivo)) {
            salida.println(txt);
        } else {
            salida.println("@" + chatActivo + ": " + txt);
            addHistorial(chatActivo, "Tú:  " + txt + "\n\n", false);
        }
        campoMensaje.setText("");
        campoMensaje.requestFocus();
    }

    // ── Procesar mensaje del servidor ────────────────────────────────────────
    private void procesar(String msg) {

        if (msg.startsWith("##USUARIOS##")) {
            String[] ns = msg.substring("##USUARIOS##".length()).split(",");
            todosChats.clear(); todosChats.add(CHAT_TODOS);
            for (String n : ns) {
                String nombre = n.trim();
                if (!nombre.equalsIgnoreCase(miNombre)) {
                    todosChats.add(nombre);
                    historiales.putIfAbsent(nombre, new StringBuilder());
                    noLeidos.putIfAbsent(nombre, 0);
                }
            }
            filtrar();
            listaChats.setSelectedValue(chatActivo, true);
            return;
        }

        if (msg.startsWith("[PRIVADO de ")) {
            int i = "[PRIVADO de ".length(), f = msg.indexOf("]:");
            if (f > i) {
                String rem  = msg.substring(i, f).trim();
                String txt2 = msg.substring(f + 2).trim();
                if (!todosChats.contains(rem)) {
                    todosChats.add(rem);
                    historiales.putIfAbsent(rem, new StringBuilder());
                    noLeidos.put(rem, 0);
                    filtrar();
                }
                addHistorial(rem, rem + ":  " + txt2 + "\n\n", true);
            }
            return;
        }

        if (msg.startsWith("[PRIVADO \u2192 ")) return;

        String linea;
        if (msg.startsWith("\u2714") || msg.startsWith("\u2716")) {
            linea = "\n" + msg + "\n\n";
            if (msg.startsWith("\u2716")) {
                String nom = msg.substring(2).replace(" salió del chat", "").trim();
                if (historiales.containsKey(nom))
                    addHistorial(nom, "\n\u2500\u2500 " + nom + " se desconectó \u2500\u2500\n\n", false);
            }
        } else {
            linea = msg + "\n";
        }
        addHistorial(CHAT_TODOS, linea, true);
    }

    // ── Agregar al historial y gestionar badge ───────────────────────────────
    private void addHistorial(String chat, String txt, boolean badge) {
        historiales.computeIfAbsent(chat, k -> new StringBuilder()).append(txt);
        if (chat.equals(chatActivo)) {
            areaChat.append(txt);
            areaChat.setCaretPosition(areaChat.getDocument().getLength());
        } else if (badge) {
            noLeidos.merge(chat, 1, Integer::sum);
            listaChats.repaint();
        }
    }

    // ── Vista previa del último mensaje ─────────────────────────────────────
    private String preview(String chat) {
        StringBuilder sb = historiales.get(chat);
        if (sb == null || sb.length() == 0) return "Sin mensajes";
        String[] ls = sb.toString().trim().split("\n");
        for (int i = ls.length - 1; i >= 0; i--) {
            String l = ls[i].trim();
            if (!l.isEmpty() && !l.startsWith("\u2500")) {
                return l.length() > 32 ? l.substring(0, 29) + "..." : l;
            }
        }
        return "Sin mensajes";
    }

    // ── Conectar ─────────────────────────────────────────────────────────────
    private void conectar() {
        String nombre = campoNombre.getText().trim();
        String host   = campoHost.getText().trim();
        if (nombre.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Ingresa tu nombre.", "Aviso", JOptionPane.WARNING_MESSAGE);
            return;
        }
        miNombre = nombre;
        new Thread(() -> {
            try {
                socket = new Socket(host, ServidorChat.PUERTO);
                salida = new PrintWriter(new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true);
                salida.println(nombre);
                conectado = true;
                SwingUtilities.invokeLater(() -> {
                    setTitle("ChatJava \u2014 " + nombre);
                    lblEstado.setText("\u25CF " + nombre);
                    lblEstado.setForeground(new Color(60, 200, 110));
                    btnConectar.setEnabled(false); btnDesconectar.setEnabled(true);
                    campoMensaje.setEnabled(true); btnEnviar.setEnabled(true);
                    campoNombre.setEnabled(false); campoHost.setEnabled(false);
                    campoMensaje.requestFocus();
                });
                BufferedReader ent = new BufferedReader(new InputStreamReader(socket.getInputStream(), "UTF-8"));
                String msg;
                while ((msg = ent.readLine()) != null) {
                    final String l = msg;
                    SwingUtilities.invokeLater(() -> procesar(l));
                }
            } catch (IOException ex) {
                SwingUtilities.invokeLater(() ->
                    JOptionPane.showMessageDialog(this,
                        "No se pudo conectar a " + host + ":" + ServidorChat.PUERTO
                        + "\n\nVerifica que el servidor esté iniciado.",
                        "Error de conexión", JOptionPane.ERROR_MESSAGE));
                desconectarUI();
            }
        }).start();
    }

    // ── Desconectar ───────────────────────────────────────────────────────────
    private void desconectar() {
        if (conectado && salida != null) salida.println("/salir");
        try { if (socket != null && !socket.isClosed()) socket.close(); } catch (IOException ignored) {}
        conectado = false;
        SwingUtilities.invokeLater(() -> {
            todosChats.clear(); todosChats.add(CHAT_TODOS);
            historiales.clear(); historiales.put(CHAT_TODOS, new StringBuilder());
            noLeidos.clear(); noLeidos.put(CHAT_TODOS, 0);
            modeloVis.clear(); modeloVis.addElement(CHAT_TODOS);
            chatActivo = CHAT_TODOS; cambiarChat(CHAT_TODOS);
        });
        desconectarUI();
    }

    private void desconectarUI() {
        SwingUtilities.invokeLater(() -> {
            lblEstado.setText("\u25CF Sin conexión"); lblEstado.setForeground(new Color(180, 65, 65));
            btnConectar.setEnabled(true); btnDesconectar.setEnabled(false);
            campoMensaje.setEnabled(false); btnEnviar.setEnabled(false);
            campoNombre.setEnabled(true); campoHost.setEnabled(true);
        });
    }

    // ── Utilidades ────────────────────────────────────────────────────────────
    private Color colorAv(String n) {
        if (CHAT_TODOS.equals(n)) return new Color(37, 130, 60);
        int h = Math.abs(n.hashCode());
        return new Color(Math.min(50+(h%100),180), Math.min(70+((h/100)%100),160), Math.min(130+((h/10000)%80),210));
    }
    private JLabel lbl(String t) {
        JLabel l = new JLabel(t); l.setForeground(TX2); l.setFont(new Font("SansSerif", Font.PLAIN, 12)); return l;
    }
    private JTextField input(String t, int c) {
        JTextField f = new JTextField(t, c); f.setFont(new Font("SansSerif", Font.PLAIN, 13)); return f;
    }
    private JButton btn(String t, Color bg) {
        JButton b = new JButton(t);
        b.setBackground(bg); b.setForeground(Color.WHITE);
        b.setFocusPainted(false); b.setBorderPainted(false);
        b.setFont(new Font("SansSerif", Font.BOLD, 12));
        b.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));
        b.setBorder(BorderFactory.createEmptyBorder(6, 14, 6, 14));
        return b;
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            try { UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName()); }
            catch (Exception ignored) {}
            new VentanaCliente().setVisible(true);
        });
    }
}
