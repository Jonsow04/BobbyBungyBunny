<?php 
session_start();
$titulo = 'Registrarse | Bobby Bunny Shop';
$css_adicional = './assets/css/authStyleSheet.css';
$js_adicional = './assets/js/registro.js';
include 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card registro">
        <h1>Bienvenido</h1>
        <p class="subtitulo">Registra tu cuenta en Conejos.com</p>
        <div class="divisor"><span>✦</span></div>

        <form action="procesarRegistro.php" method="POST" id="registroForm">
            
            <!-- Selector de tipo de cuenta -->
            <div class="selector-tipo-cuenta">
                <div class="selector-opcion <?php echo (($_POST['tipo_cuenta'] ?? 'cliente') == 'cliente') ? 'active' : ''; ?>" 
                    data-tipo="cliente"
                    onclick="seleccionarTipoCuenta('cliente')">
                    <i class="fas fa-user"></i>
                    <span>Cuenta Cliente</span>
                </div>
                <div class="selector-opcion <?php echo (($_POST['tipo_cuenta'] ?? '') == 'admin') ? 'active' : ''; ?>" 
                    data-tipo="admin"
                    onclick="seleccionarTipoCuenta('admin')">
                    <i class="fas fa-user-shield"></i>
                    <span>Cuenta Administrador</span>
                </div>
            </div>

            <input type="hidden" name="tipo_cuenta" id="tipo_cuenta" value="<?php echo htmlspecialchars($_POST['tipo_cuenta'] ?? 'cliente'); ?>">
            
            <!-- Campo para código de administrador (oculto por defecto) -->
            <div class="campo" id="codigoAdminContainer" style="display: none;">
                <label for="codigo_admin">Código de administrador</label>
                <div class="input-wrap">
                    <i class="fas fa-key"></i>
                    <input type="password" id="codigo_admin" name="codigo_admin" placeholder="Ingresa el código de acceso">
                </div>
                <small id="codigoMessage" class="error-message"></small>
                <small class="ayuda-texto">* Solo requerido para cuentas de administrador</small>
            </div>

            <div class="divisor"><span>✦</span></div>
            
            <!-- Resto del formulario (nombre, apellidos, etc.) -->
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
            <p class="seccion-titulo">Dirección de envío</p>

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
                    <button type="button" class="toggle-pass" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
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

            <!-- Mostrar errores -->
            <?php if (isset($_SESSION['errores_registro']) && !empty($_SESSION['errores_registro'])): ?>
                <div class="alert alert-danger">
                    <strong>⚠️ No se pudo completar el registro</strong>
                    <ul>
                        <?php foreach ($_SESSION['errores_registro'] as $campo => $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errores_registro']); ?>
            <?php endif; ?>
            
            <p class="auth-link">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
            
        </form>
    </div>
</div>

<style>
    .selector-tipo-cuenta {
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
    .selector-opcion.active i {
        color: white;
    }
    .ayuda-texto {
        display: block;
        margin-top: 5px;
        font-size: 0.7rem;
        color: var(--verde-moss);
    }
</style>

<script>
    function seleccionarTipoCuenta(tipo) {
        document.getElementById('tipo_cuenta').value = tipo;
        
        // Actualizar clases activas
        document.querySelectorAll('.selector-opcion').forEach(opt => {
            opt.classList.remove('active');
            if (opt.dataset.tipo === tipo) opt.classList.add('active');
        });
        
        // Mostrar u ocultar el campo de código de administrador
        const codigoContainer = document.getElementById('codigoAdminContainer');
        if (tipo === 'admin') {
            codigoContainer.style.display = 'block';
            document.getElementById('codigo_admin').setAttribute('required', 'required');
        } else {
            codigoContainer.style.display = 'none';
            document.getElementById('codigo_admin').removeAttribute('required');
            document.getElementById('codigo_admin').value = '';
        }
    }
</script>

<?php include 'includes/footer.php'; ?>