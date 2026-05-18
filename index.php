<?php
// index.php - SOLO presentación
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/controllers/articuloController.php';

// Obtener datos del controlador
$pdo = getConnection();
$articuloController = new ArticuloController($pdo);
$articulos = $articuloController->listarArticulos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bobby Bunny Shop!</title>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="./assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="./assets/js/bunnyScripts.js" defer></script>
    <script src="./assets/js/carritoIndex.js" defer></script>
</head>
<body>
    <header>
        <nav class="barra-nav">
            <a href="index.php">
                <img src="assets/multimedia/pictures/icon.png" alt="Miffy" class="icono">
            </a>
            <ul class="nav-ul">
                <li>
                    <form action="" class="barra-busqueda">
                        <input type="search" name="barra-busqueda" placeholder="Buscar productos...">
                        <button type="submit" class="boton-busqueda">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </li>
                <li><a href="carrito.php"><i class="fas fa-shopping-bag"></i></a></li>
                <li><a href="registro.php">Registrarse</a></li>
                <li><a href="login.php">Iniciar sesión</a></li>
            </ul>
        </nav>
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

    <main>
        <div class="carrusel-wrapper">
            <div class="carrusel">
                <div class="carrusel-track" id="carruselTrack"></div>
                <button class="carrusel-btn carrusel-btn--prev" id="btnPrev">&#8592;</button>
                <button class="carrusel-btn carrusel-btn--next" id="btnNext">&#8594;</button>
                <div class="carrusel-dots" id="carruselDots"></div>
            </div>
        </div>

        <section class="contenido">
            <?php if (empty($articulos)): ?>
                <div class="no-productos">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    No hay productos disponibles.
                </div>
            <?php else: ?>
                <?php foreach ($articulos as $articulo): ?>
                    <div class="caja">
                        <div class="tooltip">
                            <?php echo htmlspecialchars($articulo['descripcion']); ?>
                        </div>
                        
                        <?php if ($articulo['imagen_url']): ?>
                            <img src="<?php echo $articulo['imagen_url']; ?>" 
                                 alt="<?php echo htmlspecialchars($articulo['nombre']); ?>" 
                                 class="producto-imagen">
                        <?php else: ?>
                            <div class="imagen-placeholder">
                                <i class="fas fa-carrot"></i>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($articulo['nombre']); ?></h3>
                        <p class="precio">$<?php echo number_format($articulo['precio'], 2); ?></p>
                        <p class="stock">
                            <i class="fas fa-boxes"></i> Stock: <?php echo $articulo['stock']; ?> unidades
                            <?php if ($articulo['stock'] <= 0): ?>
                                <span class="sin-stock">Agotado</span>
                            <?php endif; ?>
                        </p>
                        
                        <button class="btn-carrito" 
                                data-id="<?php echo $articulo['idArticulo']; ?>"
                                data-nombre="<?php echo htmlspecialchars($articulo['nombre']); ?>"
                                data-precio="<?php echo $articulo['precio']; ?>"
                                <?php echo ($articulo['stock'] <= 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-cart"></i>
                            <?php echo ($articulo['stock'] > 0) ? 'Añadir al carrito' : 'Agotado'; ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer></footer>
</body>
</html>
