package chat;

import javax.swing.*;
import javax.swing.border.*;
import java.awt.*;
import java.awt.event.*;

public class VentanaServidor extends JFrame {

    private JTextArea areaLog;
    private JLabel lblConectados;
    private JButton btnIniciar, btnDetener;
    private ServidorChat servidor;

    public VentanaServidor() {
        setTitle("Chat Java — Servidor");
        setDefaultCloseOperation(JFrame.DO_NOTHING_ON_CLOSE);
        setSize(520, 480);
        setLocationRelativeTo(null);
        setResizable(true);

        addWindowListener(new WindowAdapter() {
            @Override
            public void windowClosing(WindowEvent e) {
                int op = JOptionPane.showConfirmDialog(
                    VentanaServidor.this,
                    "¿Detener el servidor y cerrar?",
                    "Confirmar cierre",
                    JOptionPane.YES_NO_OPTION
                );
                if (op == JOptionPane.YES_OPTION) {
                    if (servidor != null) servidor.detener();
                    System.exit(0);
                }
            }
        });

        construirUI();
    }

    private void construirUI() {
        // Panel superior
        JPanel panelTop = new JPanel(new FlowLayout(FlowLayout.LEFT, 12, 8));
        panelTop.setBackground(new Color(45, 45, 60));

        JLabel titulo = new JLabel("  SERVIDOR CHAT");
        titulo.setForeground(Color.WHITE);
        titulo.setFont(new Font("SansSerif", Font.BOLD, 14));

        lblConectados = new JLabel("Clientes: 0");
        lblConectados.setForeground(new Color(100, 220, 150));
        lblConectados.setFont(new Font("SansSerif", Font.PLAIN, 13));

        btnIniciar = new JButton("▶  Iniciar");
        btnIniciar.setBackground(new Color(60, 170, 100));
        btnIniciar.setForeground(Color.WHITE);
        btnIniciar.setFocusPainted(false);
        btnIniciar.setBorderPainted(false);
        btnIniciar.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));

        btnDetener = new JButton("■  Detener");
        btnDetener.setBackground(new Color(190, 60, 60));
        btnDetener.setForeground(Color.WHITE);
        btnDetener.setFocusPainted(false);
        btnDetener.setBorderPainted(false);
        btnDetener.setEnabled(false);
        btnDetener.setCursor(Cursor.getPredefinedCursor(Cursor.HAND_CURSOR));

        panelTop.add(titulo);
        panelTop.add(Box.createHorizontalStrut(20));
        panelTop.add(lblConectados);
        panelTop.add(Box.createHorizontalStrut(20));
        panelTop.add(btnIniciar);
        panelTop.add(btnDetener);

        // Área de log
        areaLog = new JTextArea();
        areaLog.setEditable(false);
        areaLog.setBackground(new Color(25, 25, 35));
        areaLog.setForeground(new Color(200, 220, 200));
        areaLog.setFont(new Font("Monospaced", Font.PLAIN, 13));
        areaLog.setMargin(new Insets(8, 10, 8, 10));
        areaLog.setLineWrap(true);
        areaLog.setWrapStyleWord(true);

        JScrollPane scroll = new JScrollPane(areaLog);
        scroll.setBorder(BorderFactory.createEmptyBorder());

        // Botón limpiar log
        JButton btnLimpiar = new JButton("Limpiar log");
        btnLimpiar.setFocusPainted(false);
        btnLimpiar.addActionListener(e -> areaLog.setText(""));

        JPanel panelBottom = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 6));
        panelBottom.add(btnLimpiar);

        // Acciones
        btnIniciar.addActionListener(e -> {
            servidor = new ServidorChat(this);
            servidor.iniciar();
            btnIniciar.setEnabled(false);
            btnDetener.setEnabled(true);
        });

        btnDetener.addActionListener(e -> {
            if (servidor != null) servidor.detener();
            log("\n[Servidor detenido]");
            btnIniciar.setEnabled(true);
            btnDetener.setEnabled(false);
        });

        // Layout
        setLayout(new BorderLayout());
        add(panelTop, BorderLayout.NORTH);
        add(scroll, BorderLayout.CENTER);
        add(panelBottom, BorderLayout.SOUTH);
    }

    public void log(String mensaje) {
        SwingUtilities.invokeLater(() -> {
            areaLog.append(mensaje + "\n");
            areaLog.setCaretPosition(areaLog.getDocument().getLength());
        });
    }

    public void actualizarContador(int n) {
        SwingUtilities.invokeLater(() ->
            lblConectados.setText("Clientes: " + n)
        );
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            try {
                UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
            } catch (Exception ignored) {}
            new VentanaServidor().setVisible(true);
        });
    }
}
