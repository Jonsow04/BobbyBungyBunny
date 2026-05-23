<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | Bobby Bunny Shop!</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/registerUserStyleSheet.css">
    
    <script src="assets/js/registro.js" defer></script>
</head>
<body>

    <header>
        <nav class="barra-nav">
            <a href="index.php">
                <img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="logo-img"
                     onerror="this.outerHTML='<span class=logo-fallback>🐰</span>'">
            </a>
            <form class="barra-busqueda" action="">
                <input type="search" placeholder="Buscar productos...">
                <button type="submit" class="boton-busqueda"><i class="fas fa-search"></i></button>
            </form>
            <ul class="nav-ul">
                <li><a href="#"><i class="fas fa-shopping-bag"></i></a></li>
                <li><a href="login.php"
                    style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); padding:6px 14px; border-radius:6px;">
                    Iniciar sesión
                </a></li>
            </ul>
        </nav>
        <nav class="barra-nav-sec">
            <ul class="nav-ul">
                <li><a href="piensos.php">Piensos</a></li>
                <li><a href="premios.php">Premios</a></li>
                <li><a href="juguetes.php">Juguetes</a></li>
                <li><a href="habitats.php">Habitats</a></li>
                <li><a href="limpieza.php">Limpieza y cuidado</a></li>
            </ul>
        </nav>
    </header>

    <div class="reg-wrapper">
        <div class="reg-card">
            <div class="reg-icono"><span class="conejo">🐰</span></div>
            <h1>Bienvenido</h1>
            <p class="subtitulo">Registra tu cuenta en Conejos.com</p>
            <div class="divisor"><span>✦</span></div>

            <div class="campo">
                <label for="nombre">Nombre</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" id="nombre" placeholder="Tu nombre" required>
                </div>
            </div>

            <div class="campo">
                <label for="apellido_paterno">Apellido paterno</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" id="apellido_paterno" placeholder="Apellido paterno" required>
                </div>
            </div>

            <div class="campo">
                <label for="apellido_materno">Apellido materno (opcional)</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" id="apellido_materno" placeholder="Apellido materno (opcional)">
                </div>
            </div>

            <div class="campo">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <div class="input-wrap">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" id="fecha_nacimiento" required>
                </div>
            </div>

            <div class="campo">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" placeholder="correo@ejemplo.com" required>
                </div>
            </div>

            <div class="campo">
                <label for="celular">Celular</label>
                <div class="input-wrap">
                    <i class="fas fa-phone-alt"></i>
                    <input type="tel" id="celular" placeholder="+52 123 456 7890" required>
                </div>
            </div>

            <!-- Sección de dirección (después de los campos de usuario) -->
            <div class="divisor"><span>✦</span></div>
            <h3>Dirección de envío</h3>

            <div class="campo">
                <label for="calle">Calle</label>
                <div class="input-wrap">
                    <i class="fas fa-road"></i>
                    <input type="text" id="calle" name="calle" placeholder="Calle" value="<?php echo $_SESSION['datos_registro']['calle'] ?? ''; ?>" required>
                </div>
                <small id="calleMessage" class="error-message"></small>
            </div>

            <div class="campo">
                <label for="num_casa">Número de casa</label>
                <div class="input-wrap">
                    <i class="fas fa-hashtag"></i>
                    <input type="text" id="num_casa" name="num_casa" placeholder="Número exterior e interior" value="<?php echo $_SESSION['datos_registro']['num_casa'] ?? ''; ?>" required>
                </div>
                <small id="numCasaMessage" class="error-message"></small>
            </div>

            <div class="campo">
                <label for="colonia">Colonia</label>
                <div class="input-wrap">
                    <i class="fas fa-location-dot"></i>
                    <input type="text" id="colonia" name="colonia" placeholder="Colonia" value="<?php echo $_SESSION['datos_registro']['colonia'] ?? ''; ?>" required>
                </div>
                <small id="coloniaMessage" class="error-message"></small>
            </div>

            <div class="campo">
                <label for="cp">Código Postal</label>
                <div class="input-wrap">
                    <i class="fas fa-mail-bulk"></i>
                    <input type="text" id="cp" name="cp" placeholder="Código Postal (5 dígitos)" value="<?php echo $_SESSION['datos_registro']['cp'] ?? ''; ?>" required>
                </div>
                <small id="cpMessage" class="error-message"></small>
            </div>

            <div class="campo">
                <label for="ciudad">Ciudad</label>
                <div class="input-wrap">
                    <i class="fas fa-city"></i>
                    <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad" value="<?php echo $_SESSION['datos_registro']['ciudad'] ?? ''; ?>" required>
                </div>
                <small id="ciudadMessage" class="error-message"></small>
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <div class="input-wrap">
                    <i class="fas fa-map"></i>
                    <input type="text" id="estado" name="estado" placeholder="Estado" value="<?php echo $_SESSION['datos_registro']['estado'] ?? ''; ?>" required>
                </div>
                <small id="estadoMessage" class="error-message"></small>
            </div>

            <!-- Contraseña -->

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasena" placeholder="••••••••" autocomplete="new-password" required>
                </div>
            </div>

            <div class="campo">
                <label for="contrasenaconfirm">Confirmar contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasenaconfirm" placeholder="••••••••" autocomplete="new-password" required onkeyup="checkPasswordMatch()">
                </div>
            </div>

            <span id="message"></span>
            <button class="btn-reg">Registrar</button>

            <?php if (isset($_SESSION['errores_registro'])): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($_SESSION['errores_registro'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errores_registro']); ?>
            <?php endif; ?>
            
            <p class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>

    <footer>© 2025 Bobby Bunny Shop · Todo para tu conejo</footer>
</body>
</html>