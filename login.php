<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bobby Bunny Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/loginUserStyleSheet.css">
</head>

<body>
    <header>
        <nav class="barra-nav">
            <a href="index.php">
                <img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="icono">
            </a>
            <form class="barra-busqueda" action="">
                <input type="search" placeholder="Buscar productos...">
                <button type="submit" class="boton-busqueda">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <ul class="nav-ul">
                <li><a href="registro.php">Registrarse</a></li>
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

    <div class="login-wrapper">
        <div class="login-card">
            <h1>Bienvenido</h1>
            <p class="subtitulo">Inicia sesión en Conejos.com</p>
            
            <div class="divisor"><span>✦</span></div>

            <div class="campo">
                <label for="correo">Usuario</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="correo" placeholder="Nombre de usuario" autocomplete="username">
                </div>
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasena" placeholder="••••••••" autocomplete="current-password">
                </div>
            </div>

            <button class="btn-login">Iniciar sesión</button>

            <p class="registro-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <footer>© 2026 Bobby Bunny Shop · Todo para tu conejo</footer>
</body>

</html>
