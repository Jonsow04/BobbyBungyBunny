<?php
// Verificar si el usuario ha iniciado sesión
$sesion_iniciada = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Bobby Bunny Shop'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/authStyleSheet.css">
    <link rel="icon" href="assets/multimedia/pictures/icon.png" type="image/x-icon">
    
    <?php if (isset($css_adicional)): ?>
        <link rel="stylesheet" href="<?php echo $css_adicional; ?>">
    <?php endif; ?>
</head>
<body>

<header>
    <nav class="barra-nav">
        <a href="index.php">
            <img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="logo-img">
        </a>
        <form class="barra-busqueda" action="">
            <input type="search" placeholder="Buscar productos...">
            <button type="submit" class="boton-busqueda">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <ul class="nav-ul">
            <?php if ($sesion_iniciada): ?>
                <!-- Usuario con sesión iniciada -->
                <li><a href="perfil.php">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Mi cuenta'); ?>
                </a></li>
                <li><a href="cerrarSesion.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a></li>
            <?php else: ?>
                <!-- Usuario sin sesión -->
                <li><a href="login.php">Iniciar sesión</a></li>
                <li><a href="registro.php">Registrarse</a></li>
            <?php endif; ?>
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

<main>