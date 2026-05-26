<?php

require_once __DIR__ . '/../../includes/helpers/sessionHelper.php';
SessionHelper::iniciar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Bobby Bunny Shop'; ?></title>
    <link rel="stylesheet" href="assets/css/authStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php if (isset($js_adicional)): ?>
        <script src="<?php echo $js_adicional; ?>" defer></script>
    <?php endif; ?>
</head>
<body>
    <header>
        <!-- Barra de navegación principal -->
        <nav class="barra-nav">
            <a href="index.php">
                <img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="icono">
            </a>
            
            <form class="barra-busqueda" action="" method="GET">
                <input type="search" name="busqueda" placeholder="Buscar productos...">
                <button type="submit" class="boton-busqueda">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <ul class="nav-ul">
                <?php if (SessionHelper::isLoggedIn()): ?>
                    <!-- Usuario logueado -->
                    <?php if (SessionHelper::isAdminOrGerente()): ?>
                        <li><a href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a></li>
                    <?php endif; ?>
                    
                    <?php if (SessionHelper::isCliente()): ?>
                        <li><a href="misPedidos.php"><i class="fas fa-list"></i> Mis pedidos</a></li>
                    <?php endif; ?>
                    
                    <li><a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
                    <li><a href="cerrarSesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                    
                <?php else: ?>
                    <!-- Usuario NO logueado -->
                    <li><a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                    <li><a href="login.php">Iniciar sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Barra de navegación secundaria (categorías) -->
        <nav class="barra-nav-sec">
            <ul class="nav-ul">
                <li><a href="piensos.php">Piensos y henos</a></li>
                <li><a href="premios.php">Premios</a></li>
                <li><a href="juguetes.php">Juguetes</a></li>
                <li><a href="habitats.php">Habitats</a></li>
                <li><a href="limpieza.php">Limpieza y cuidado</a></li>
            </ul>
        </nav>
    </header>