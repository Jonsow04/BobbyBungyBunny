<?php
session_start();
$titulo = 'Conejos.com';
$js_adicional = 'assets/js/registro.js';
include 'includes/header.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/controllers/ArticuloController.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../includes/controllers/' . $class . '.php',
        __DIR__ . '/../includes/models/' . $class . '.php',
        __DIR__ . '/../includes/helpers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// ============================================
// LÓGICA DE NEGOCIO (separada de presentación)
// ============================================

try {
    $pdo = getConnection();
    $articuloController = new ArticuloController($pdo);
    $articulos = $articuloController->listarArticulos();
} catch (Exception $e) {
    error_log("Error al cargar artículos: " . $e->getMessage());
    $articulos = [];
}

// ============================================
// VISTA (HTML)
// ============================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="./assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="./assets/js/bunnyScripts.js" defer></script>
    <script src="./assets/js/carritoIndex.js" defer></script>
</head>
<body>
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
                            <?php echo $articulo['descripcion']; ?>
                        </div>
                        
                        <?php if ($articulo['imagen_url']): ?>
                            <img src="<?php echo $articulo['imagen_url']; ?>" 
                                 alt="<?php echo $articulo['nombre']; ?>" 
                                 class="producto-imagen">
                        <?php else: ?>
                            <div class="imagen-placeholder">
                                <i class="fas fa-carrot"></i>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo $articulo['nombre']; ?></h3>
                        <p class="precio">$<?php echo number_format($articulo['precio'], 2); ?></p>
                        <p class="stock">
                            <i class="fas fa-boxes"></i> Stock: <?php echo $articulo['stock']; ?> unidades
                            <?php if ($articulo['stock'] <= 0): ?>
                                <span class="sin-stock">Agotado</span>
                            <?php endif; ?>
                        </p>
                        
                        <button class="btn-carrito" 
                                data-id="<?php echo $articulo['idArticulo']; ?>"
                                data-nombre="<?php echo $articulo['nombre']; ?>"
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>