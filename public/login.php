<?php 
session_start();
$titulo = 'Iniciar sesión | Bobby Bunny Shop';

// Verificar si viene de cerrar sesión
if (isset($_GET['logout']) && $_GET['logout'] === 'exitoso') {
    $_SESSION['logout_exitoso'] = true;
}

include 'includes/header.php';
?>

<!DOCTYPE html>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Bienvenido</h1>
            <p class="subtitulo">Inicia sesión en Conejos.com</p>

            <div class="divisor"><span>✦</span></div>

            <form action="procesarLogin.php" method="POST" id="loginForm">
                
                <div class="campo">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" value="<?php echo isset($_SESSION['datos_login']['email']) ? htmlspecialchars($_SESSION['datos_login']['email']) : ''; ?>" autocomplete="username" required>
                    </div>
                    <small id="emailMessage" class="error-message"></small>
                </div>

                <div class="campo">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="toggle-pass" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small id="passwordMessage" class="error-message"></small>
                </div>

                <button type="submit" class="btn-auth">Iniciar sesión</button>

                <!-- Mostrar errores de login -->
                <?php if (isset($_SESSION['errores_login']) && !empty($_SESSION['errores_login'])): ?>
                    <div class="alert alert-danger">
                        <strong>Error al iniciar sesión</strong>
                        <ul>
                            <?php foreach ($_SESSION['errores_login'] as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errores_login']); ?>
                <?php endif; ?>

                <!-- Mostrar mensaje de registro exitoso -->
                <?php if (isset($_SESSION['registro_exitoso'])): ?>
                    <div class="alert alert-success">
                        <strong>¡Cuenta creada exitosamente!</strong>
                        <p style="margin: 10px 0 0 0;">Ahora puedes iniciar sesión con tu correo y contraseña.</p>
                    </div>
                    <?php unset($_SESSION['registro_exitoso']); ?>
                <?php endif; ?>

                <!-- Mostrar mensaje de cierre de sesión -->
                <?php if (isset($_SESSION['logout_exitoso'])): ?>
                    <div class="alert alert-success">
                        <strong>Sesión cerrada correctamente</strong>
                        <p style="margin: 10px 0 0 0;">Has cerrado sesión exitosamente.</p>
                    </div>
                    <?php unset($_SESSION['logout_exitoso']); ?>
                <?php endif; ?>

                <p class="auth-link">
                    ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
                </p>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/loginScripts.js"></script>
</body>

</html>