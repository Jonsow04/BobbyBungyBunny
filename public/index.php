<?php
session_start();

// Si la petición incluye ?format=json, devolvemos solo los datos en JSON y salimos
$formato = $_GET['format'] ?? '';
if ($formato === 'json') {
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/models/articulo.php';
    
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
    try {
        $pdo = getConnection();
        $articuloModel = new Articulo($pdo);
        $articulos = $articuloModel->obtenerTodos();
        echo json_encode($articulos, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit; // Termina la ejecución aquí
}

// Si no se pidió JSON, continúa con la lógica normal de mostrar HTML
require_once __DIR__ . '/../includes/config.php';

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
    <link rel="icon" href="assets/multimedia/pictures/icon.png" type="image/x-icon">
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
                        <a class="producto-link" href="detalleproducto.php?id=<?php echo $articulo['idArticulo']; ?>" style="display:block; color:inherit; text-decoration:none;">
                            <div class="tooltip">
                                <?php echo $articulo['descripcion']; ?>
                            </div>
                            
                            <?php 
                            $rutaImagen = 'assets/multimedia/pictures/articulos/' . $articulo['idArticulo'] . '.png';?>
                                <img src="<?php echo $rutaImagen; ?>" 
                                    alt="<?php echo $articulo['nombre']; ?>" 
                                    class="producto-imagen"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="imagen-placeholder" style="display: none;">
                                    <i class="fas fa-carrot"></i>
                                </div>
                            
                            <h3><?php echo $articulo['nombre']; ?></h3>
                            <p class="precio">$<?php echo number_format($articulo['precio'], 2); ?></p>
                            <p class="stock">
                                <i class="fas fa-boxes"></i> Stock: <?php echo $articulo['stock']; ?> unidades
                                <?php if ($articulo['stock'] <= 0): ?>
                                    <span class="sin-stock">Agotado</span>
                                <?php endif; ?>
                            </p>
                        </a>

                        
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
