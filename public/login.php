<?php 
session_start();
$titulo = 'Iniciar sesión | Bobby Bunny Shop';
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            
            <h1>Bienvenido</h1>
            <p class="subtitulo">Inicia sesión en Conejos.com</p>

            //Selector de tipo de acceso
            <div class="selector-tipo">
                <div class="selector-opcion <?php echo ($_POST['tipo_acceso'] ?? 'cliente') == 'cliente' ? 'active' : ''; ?>" 
                    data-tipo="cliente"
                    onclick="seleccionarTipo('cliente')">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Soy Cliente</span>
                </div>
                <div class="selector-opcion <?php echo ($_POST['tipo_acceso'] ?? '') == 'admin' ? 'active' : ''; ?>" 
                    data-tipo="admin"
                    onclick="seleccionarTipo('admin')">
                    <i class="fas fa-store"></i>
                    <span>Soy Admin/Tienda</span>
                </div>
            </div>

            <input type="hidden" name="tipo_acceso" id="tipo_acceso" value="<?php echo htmlspecialchars($_POST['tipo_acceso'] ?? 'cliente'); ?>">
            
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
                        <strong>⚠️ Error al iniciar sesión</strong>
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
                        <strong>✅ ¡Cuenta creada exitosamente!</strong>
                        <p style="margin: 10px 0 0 0;">Ahora puedes iniciar sesión con tu correo y contraseña.</p>
                    </div>
                    <?php unset($_SESSION['registro_exitoso']); ?>
                <?php endif; ?>

                <!-- Mostrar mensaje de cierre de sesión -->
                <?php if (isset($_SESSION['logout_exitoso'])): ?>
                    <div class="alert alert-success">
                        <strong>✅ Sesión cerrada correctamente</strong>
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

    <style>
    .selector-tipo {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .selector-opcion {
        flex: 1;
        text-align: center;
        padding: 0.8rem;
        border: 2px solid var(--tan);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .selector-opcion.active {
        background: var(--cafe-noir);
        color: white;
        border-color: var(--cafe-noir);
    }
    .selector-opcion i {
        font-size: 1.2rem;
        display: block;
        margin-bottom: 0.3rem;
    }
    </style>

    <script>
        // Mostrar/ocultar contraseña
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('contrasena');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        function seleccionarTipo(tipo) {
            document.getElementById('tipo_acceso').value = tipo;
            document.querySelectorAll('.selector-opcion').forEach(opt => {
                opt.classList.remove('active');
                if (opt.dataset.tipo === tipo) opt.classList.add('active');
            });
        }

    </script>
</body>

</html>