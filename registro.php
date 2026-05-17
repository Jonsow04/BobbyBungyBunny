<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | Bobby Bunny Shop!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/registerUserStyleSheet.css">
    <script src="assets/js/registro.js"></script>
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
                <label for="user">Usuario</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="user" placeholder="usuario" required>
                </div>
            </div>

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

            <p class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>

    <footer>© 2025 Bobby Bunny Shop · Todo para tu conejo</footer>
</body>
</html>
