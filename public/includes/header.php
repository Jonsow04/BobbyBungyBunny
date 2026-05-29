<?php
// Verificar si el usuario ha iniciado sesión
$sesion_iniciada = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);

// Obtener el contador del carrito
$carrito_count = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $carrito_count += $item['cantidad'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Bobby Bunny Shop'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="/assets/multimedia/pictures/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/indexStyleSheet.css">
    
    <?php if (isset($css_adicional)): ?>
        <link rel="stylesheet" href="<?php echo $css_adicional; ?>">
    <?php endif; ?>
</head>
<body>

<header>
    <nav class="barra-nav">
        <a href="/index.php">
            <img src="/assets/multimedia/pictures/icon.png" alt="Bobby" class="logo-img">
        </a>
        <form class="barra-busqueda" action="">
            <input type="search" placeholder="Buscar productos...">
            <button type="submit" class="boton-busqueda">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <ul class="nav-ul">
            <li style="position: relative;">
                <a href="carrito.php">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if ($carrito_count > 0): ?>
                        <span class="carrito-badge"><?php echo $carrito_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if ($sesion_iniciada): ?>
                <li><a href="perfil.php">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Mi cuenta'); ?>
                </a></li>
                <li><a href="cerrarSesion.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a></li>
            <?php else: ?>
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