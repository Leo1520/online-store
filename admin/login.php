<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Administrativo — Electrohogar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <style>
        body { background: #1B3A6B; }
        .login-box { width: 380px; margin: 0 auto; padding-top: 80px; }
        .login-logo { text-align: center; margin-bottom: 24px; }
        .login-logo h1 { color: #fff; font-size: 28px; font-weight: 800; letter-spacing: 1px; }
        .login-logo span { color: #F5A623; }
        .login-logo small { display: block; color: #8898c4; font-size: 12px; margin-top: 4px; }
        .login-card { background: #fff; border-radius: 16px; padding: 36px 32px; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .login-card h5 { color: #1B3A6B; font-weight: 700; margin-bottom: 20px; font-size: 15px; text-align: center; }
        .login-card .form-control { border-radius: 10px; border: 1.5px solid #d8e0f0; font-size: 14px; height: 44px; }
        .login-card .form-control:focus { border-color: #1B3A6B; box-shadow: none; }
        .login-card .input-group-text { border-radius: 0 10px 10px 0; background: #f4f6fa; border: 1.5px solid #d8e0f0; border-left: none; color: #888; }
        .btn-login { background: #1B3A6B; color: #fff; border-radius: 10px; font-weight: 700; height: 44px; font-size: 14px; letter-spacing: .5px; }
        .btn-login:hover { background: #2751a3; color: #fff; }
        .login-footer { text-align: center; margin-top: 16px; }
        .login-footer a { color: #8898c4; font-size: 12px; }
        .login-footer a:hover { color: #F5A623; }
        .alert-danger { border-radius: 10px; font-size: 13px; }
    </style>
</head>
<body class="hold-transition">

<?php
session_start();

if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario !== '' && $password !== '') {
        $db   = Database::conectar();
        $stmt = $db->prepare("CALL sp_obtener_cuenta_por_usuario(?)");
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $res  = $stmt->get_result();
        $cuenta = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $rolesPermitidos = ['admin', 'vendedor', 'almacenero', 'repartidor', 'it'];
        if ($cuenta && password_verify($password, $cuenta['password']) && in_array($cuenta['rol'], $rolesPermitidos)) {
            $_SESSION['usuario']  = $cuenta['usuario'];
            $_SESSION['rol']      = $cuenta['rol'];
            $_SESSION['es_admin'] = true;
            header('Location: index.php');
            exit();
        } else {
            $error = 'Credenciales incorrectas o sin acceso al panel.';
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}
?>

<div class="login-box">
    <div class="login-logo">
        <h1>⚡ Electro<span>hogar</span></h1>
        <small><i class="fas fa-shield-alt mr-1"></i>Panel de Administración</small>
    </div>

    <div class="login-card">
        <h5><i class="fas fa-lock mr-2" style="color:#F5A623;"></i>Acceso al Panel</h5>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2">
                <i class="fas fa-exclamation-circle mr-1"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label style="font-size:12px;font-weight:600;color:#555;">Usuario</label>
                <div class="input-group">
                    <input type="text" name="usuario" class="form-control"
                           placeholder="Usuario" autofocus
                           value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label style="font-size:12px;font-weight:600;color:#555;">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">
                <i class="fas fa-sign-in-alt mr-2"></i>Ingresar al Panel
            </button>
        </form>
    </div>

    <div class="login-footer">
        <a href="/index.php?pagina=inicio">← Volver a la tienda</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
