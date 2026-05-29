<?php 
session_start();
$titulo = 'Registrarse | Bobby Bunny Shop';
$js_adicional = 'assets/js/registro.js';
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <script src="assets/js/registro.js" defer></script>
    <link rel="stylesheet" type="text/css" href="assets\css\registroUserStyleSheet.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card-registro">
            <h1>Bienvenido</h1>
            <p class="subtitulo">Registra tu cuenta en Conejos.com</p>
            <div class="divisor"><span>✦</span></div>

            <form action="procesarRegistro.php" method="POST" id="registroForm">

                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo isset($_SESSION['datos_registro']['nombre']) ? htmlspecialchars($_SESSION['datos_registro']['nombre']) : ''; ?>" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="apellido_paterno">Apellido paterno</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido paterno" value="<?php echo isset($_SESSION['datos_registro']['apellido_paterno']) ? htmlspecialchars($_SESSION['datos_registro']['apellido_paterno']) : ''; ?>" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="apellido_materno">Apellido materno (opcional)</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Apellido materno" value="<?php echo isset($_SESSION['datos_registro']['apellido_materno']) ? htmlspecialchars($_SESSION['datos_registro']['apellido_materno']) : ''; ?>">
                    </div>
                </div>

                <div class="campo">
                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                    <div class="input-wrap">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo isset($_SESSION['datos_registro']['fecha_nacimiento']) ? htmlspecialchars($_SESSION['datos_registro']['fecha_nacimiento']) : ''; ?>" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" value="<?php echo isset($_SESSION['datos_registro']['email']) ? htmlspecialchars($_SESSION['datos_registro']['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="celular">Celular</label>
                    <div class="input-wrap">
                        <i class="fas fa-phone-alt"></i>
                        <input type="tel" id="celular" name="celular" placeholder="1234567890" value="<?php echo isset($_SESSION['datos_registro']['celular']) ? htmlspecialchars($_SESSION['datos_registro']['celular']) : ''; ?>" required>
                    </div>
                </div>

                <!-- Sección de dirección -->
                <div class="divisor"><span>✦</span></div>
                <p class="seccion-titulo">Dirección</p>

                <div class="campo">
                    <label for="calle">Calle</label>
                    <div class="input-wrap">
                        <i class="fas fa-road"></i>
                        <input type="text" id="calle" name="calle" placeholder="Calle" value="<?php echo isset($_SESSION['datos_registro']['calle']) ? htmlspecialchars($_SESSION['datos_registro']['calle']) : ''; ?>" required>
                    </div>
                    <small id="calleMessage" class="error-message"></small>
                </div>

                <div class="campo">
                    <label for="num_casa">Número de casa</label>
                    <div class="input-wrap">
                        <i class="fas fa-hashtag"></i>
                        <input type="text" id="num_casa" name="num_casa" placeholder="Número exterior e interior" value="<?php echo isset($_SESSION['datos_registro']['num_casa']) ? htmlspecialchars($_SESSION['datos_registro']['num_casa']) : ''; ?>" required>
                    </div>
                    <small id="numCasaMessage" class="error-message"></small>
                </div>

                <div class="campo">
                    <label for="colonia">Colonia</label>
                    <div class="input-wrap">
                        <i class="fas fa-location-dot"></i>
                        <input type="text" id="colonia" name="colonia" placeholder="Colonia" value="<?php echo isset($_SESSION['datos_registro']['colonia']) ? htmlspecialchars($_SESSION['datos_registro']['colonia']) : ''; ?>" required>
                    </div>
                    <small id="coloniaMessage" class="error-message"></small>
                </div>

                <div class="campo">
                    <label for="cp">Código Postal</label>
                    <div class="input-wrap">
                        <i class="fas fa-mail-bulk"></i>
                        <input type="text" id="cp" name="cp" placeholder="Código Postal (5 dígitos)" value="<?php echo isset($_SESSION['datos_registro']['cp']) ? htmlspecialchars($_SESSION['datos_registro']['cp']) : ''; ?>" required>
                    </div>
                    <small id="cpMessage" class="error-message"></small>
                </div>

                <div class="campo">
                    <label for="ciudad">Ciudad</label>
                    <div class="input-wrap">
                        <i class="fas fa-city"></i>
                        <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad" value="<?php echo isset($_SESSION['datos_registro']['ciudad']) ? htmlspecialchars($_SESSION['datos_registro']['ciudad']) : ''; ?>" required>
                    </div>
                    <small id="ciudadMessage" class="error-message"></small>
                </div>

                <div class="campo">
                    <label for="estado">Estado</label>
                    <div class="input-wrap">
                        <i class="fas fa-map"></i>
                        <input type="text" id="estado" name="estado" placeholder="Estado" value="<?php echo isset($_SESSION['datos_registro']['estado']) ? htmlspecialchars($_SESSION['datos_registro']['estado']) : ''; ?>" required>
                    </div>
                    <small id="estadoMessage" class="error-message"></small>
                </div>

                <!-- Contraseñas -->
                <div class="divisor"><span>✦</span></div>

                <div class="campo">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" autocomplete="new-password" required>
                        <!-- El botón de mostrar/ocultar se agregará aquí mediante JavaScript -->
                    </div>
                </div>

                <div class="campo">
                    <label for="contrasenaconfirm">Confirmar contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="contrasenaconfirm" name="contrasenaconfirm" placeholder="••••••••" autocomplete="new-password" required>
                    </div>
                </div>

                <span id="message" class="error-message"></span>
                
                <button type="submit" class="btn-auth">Registrar</button>

                <!-- Mostrar errores de registro -->
                <?php if (isset($_SESSION['errores_registro']) && !empty($_SESSION['errores_registro'])): ?>
                    <div class="alert alert-danger">
                        <strong>No se pudo completar el registro</strong>
                        <ul>
                            <?php foreach ($_SESSION['errores_registro'] as $campo => $error): ?>
                                <li>
                                    <?php if ($error === 'email_duplicado'): ?>
                                        Este correo electrónico ya está asociado a una cuenta de Conejos.com. 
                                        <a href="login.php" style="color: #b33a3a; font-weight: bold;">Inicia sesión aquí</a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($error); ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errores_registro']); ?>
                <?php endif; ?>

                <!-- Mostrar éxito si viene de registro -->
                <?php if (isset($_SESSION['registro_exitoso'])): ?>
                    <div class="alert alert-success">
                        <strong>¡Cuenta creada exitosamente!</strong>
                        <p style="margin: 10px 0 0 0;">Bienvenido/a a Conejos.com. Ahora puedes <a href="login.php" style="color: #2d6a4f; font-weight: bold;">iniciar sesión</a> con tu correo y contraseña.</p>
                    </div>
                    <?php unset($_SESSION['registro_exitoso']); ?>
                <?php endif; ?>
                
                <p class="auth-link">
                    ¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a>
                </p>
                
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>