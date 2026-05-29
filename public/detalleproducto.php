<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/controllers/ArticuloController.php';
require_once __DIR__ . '/includes/helpers/validation.php';

$sesion_iniciada = isset($_SESSION['usuario_id']);
$titulo = 'Detalle del Producto - Conejos.com';

$error = null;
$articulo = null;
$imagenUrl = 'public/assets/multimedia/pictures/articulos/placeholder.jpg';
$stockClass = '';

$id = $_GET['id'] ?? null;
if (!$id || !ValidationHelper::validateId($id)) {
    $error = 'El producto solicitado no es válido.';
} else {
    try {
        $pdo = getConnection();
        $articuloController = new ArticuloController($pdo);
        $articulo = $articuloController->obtenerArticulo($id);
        if (!$articulo) {
            $error = 'No se encontró el producto solicitado.';
        } else {
            $imagenUrl = $articulo['imagen_url'] ? 'public/' . $articulo['imagen_url'] : $imagenUrl;
            if ($articulo['stock'] <= 0) {
                $stockClass = 'agotado';
            } elseif ($articulo['stock'] < 5) {
                $stockClass = 'bajo';
            }
        }
    } catch (Exception $e) {
        error_log('Error al cargar detalle del producto: ' . $e->getMessage());
        $error = 'Ocurrió un error al cargar el producto. Intenta más tarde.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="public/assets/css/authStyleSheet.css">
    <link rel="stylesheet" href="public/assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="public/assets/css/detalleProductoStyleSheet.css">
</head>
<body>

<header>
    <nav class="barra-nav">
        <a href="public/index.php">
            <img src="public/assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="logo-img">
        </a>
        <form class="barra-busqueda" action="public/index.php" method="get">
            <input type="search" name="q" placeholder="Buscar productos...">
            <button type="submit" class="boton-busqueda">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <ul class="nav-ul">
            <?php if ($sesion_iniciada): ?>
                <li><a href="public/perfil.php">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Mi cuenta'); ?>
                </a></li>
                <li><a href="public/cerrarSesion.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a></li>
            <?php else: ?>
                <li><a href="public/login.php">Iniciar sesión</a></li>
                <li><a href="public/registro.php">Registrarse</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <nav class="barra-nav-sec">
        <ul class="nav-ul">
            <li><a href="public/piensos.php">Piensos</a></li>
            <li><a href="public/premios.php">Premios</a></li>
            <li><a href="public/juguetes.php">Juguetes</a></li>
            <li><a href="public/habitats.php">Habitats</a></li>
            <li><a href="public/limpieza.php">Limpieza y cuidado</a></li>
        </ul>
    </nav>
</header>

<main class="detalle-producto-main">
    <?php if ($error): ?>
        <section class="detalle-error">
            <div class="error-card">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Producto no disponible</h2>
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="public/index.php" class="btn-agregar-carrito">Volver al catálogo</a>
            </div>
        </section>
    <?php else: ?>
        <section class="detalle-container">
            <div class="producto-imagen-wrapper">
                <div class="producto-imagen-principal">
                    <img id="imagenPrincipal"
                         src="<?php echo htmlspecialchars($imagenUrl); ?>"
                         alt="<?php echo htmlspecialchars($articulo['nombre']); ?>"
                         class="imagen-grande">
                </div>
                <div class="galeria-imagenes" id="galeriaImagenes" style="display: none;"></div>
            </div>

            <div class="producto-info-wrapper">
                <div class="producto-header">
                    <span class="categoria-badge"><?php echo htmlspecialchars($articulo['nombre_categoria']); ?></span>
                    <h1 class="producto-titulo"><?php echo htmlspecialchars($articulo['nombre']); ?></h1>
                </div>

                <div class="producto-descripcion-full">
                    <p><?php echo nl2br(htmlspecialchars($articulo['descripcion'])); ?></p>
                </div>

                <div class="producto-precio-stock">
                    <div class="precio-container">
                        <span class="label-precio">Precio:</span>
                        <span class="precio-valor">$<?php echo number_format($articulo['precio'], 2); ?></span>
                    </div>
                    <div class="stock-container">
                        <span class="label-stock">Stock disponible:</span>
                        <span class="stock-valor"><?php echo $articulo['stock']; ?> unidades</span>
                        <span class="stock-indicador <?php echo $stockClass; ?>" id="stockIndicador"></span>
                    </div>
                </div>

                <div class="producto-acciones">
                    <div class="cantidad-selector">
                        <label for="cantidad">Cantidad:</label>
                        <div class="cantidad-input-group">
                            <button class="btn-cantidad" id="btnMenos">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number"
                                   id="cantidad"
                                   class="input-cantidad"
                                   value="1"
                                   min="1"
                                   max="<?php echo max(1, $articulo['stock']); ?>">
                            <button class="btn-cantidad" id="btnMas">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <button class="btn-agregar-carrito" id="btnAgregarCarrito" <?php echo ($articulo['stock'] <= 0) ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart"></i>
                        <?php echo ($articulo['stock'] <= 0) ? 'Agotado' : 'Agregar al carrito'; ?>
                    </button>
                </div>

                <div class="producto-extra-info">
                    <div class="info-item">
                        <i class="fas fa-truck"></i>
                        <div>
                            <h4>Envío rápido</h4>
                            <p>Entrega en 2-3 días hábiles</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-undo"></i>
                        <div>
                            <h4>Devoluciones fáciles</h4>
                            <p>30 días para devolver el producto</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <h4>Garantía de calidad</h4>
                            <p>Productos certificados y seguros</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="productos-relacionados" id="productosRelacionados" style="display: none;">
            <h2>Productos Relacionados</h2>
            <div class="grid-relacionados"></div>
        </section>
    <?php endif; ?>
</main>

<footer>
    <p>© <?php echo date('Y'); ?> Conejos.com · Todo para tu conejo</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnMenos = document.getElementById('btnMenos');
        const btnMas = document.getElementById('btnMas');
        const inputCantidad = document.getElementById('cantidad');
        const btnAgregar = document.getElementById('btnAgregarCarrito');

        if (!btnMenos || !btnMas || !inputCantidad || !btnAgregar) {
            return;
        }

        btnMenos.addEventListener('click', function() {
            let valor = parseInt(inputCantidad.value);
            if (valor > 1) inputCantidad.value = valor - 1;
        });

        btnMas.addEventListener('click', function() {
            let valor = parseInt(inputCantidad.value);
            let max = parseInt(inputCantidad.max);
            if (valor < max) inputCantidad.value = valor + 1;
        });

        inputCantidad.addEventListener('change', function() {
            let valor = parseInt(this.value);
            let max = parseInt(this.max);
            if (isNaN(valor) || valor < 1) this.value = 1;
            if (valor > max) this.value = max;
        });

        btnAgregar.addEventListener('click', function() {
            const cantidad = parseInt(inputCantidad.value);
            console.log('Agregar al carrito:', cantidad, 'unidades');
        });
    });
</script>

</body>
</html>
