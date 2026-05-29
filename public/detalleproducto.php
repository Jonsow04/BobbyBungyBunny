<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/controllers/ArticuloController.php';
require_once __DIR__ . '/../includes/controllers/carritoController.php';
require_once __DIR__ . '/../includes/helpers/validation.php';

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
        $carritoController = new CarritoController($pdo);
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
    <link rel="stylesheet" href="./assets/css/authStyleSheet.css">
    <link rel="stylesheet" href="./assets/css/detalleProductoStyleSheet.css">
</head>
<body>

<header>
    <nav class="barra-nav">
        <a href="/index.php">
            <img src="./assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="logo-img">
        </a>
        <form class="barra-busqueda" action="/index.php" method="get">
            <input type="search" name="q" placeholder="Buscar productos...">
            <button type="submit" class="boton-busqueda">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <ul class="nav-ul">
            <li style="position: relative;">
                <a href="carrito.php">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="carrito-badge" id="carritoBadge" style="display: none;">0</span>
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

<main class="detalle-producto-main">
    <?php if ($error): ?>
        <section class="detalle-error">
            <div class="error-card">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Producto no disponible</h2>
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="index.php" class="btn-agregar-carrito">Volver al catálogo</a>
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
                        <span class="stock-valor" id="stockValor"><?php echo $articulo['stock']; ?> unidades</span>
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

                <!-- Mensaje de notificación -->
                <div id="notificacionCarrito" class="notificacion-carrito" style="display: none;">
                    <i class="fas fa-check-circle"></i> Producto agregado al carrito
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

<style>
    .carrito-badge {
        position: absolute;
        top: -10px;
        right: -12px;
        background-color: #e74c3c;
        color: white;
        font-size: 0.65rem;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 50%;
        min-width: 18px;
        text-align: center;
        font-family: 'Cormorant Garamond', serif;
    }
    
    .notificacion-carrito {
        margin-top: 15px;
        padding: 10px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        text-align: center;
        font-size: 0.9rem;
    }
    
    .notificacion-carrito i {
        margin-right: 8px;
    }
    
    .btn-agregar-carrito.loading {
        opacity: 0.7;
        cursor: wait;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del DOM
        const btnMenos = document.getElementById('btnMenos');
        const btnMas = document.getElementById('btnMas');
        const inputCantidad = document.getElementById('cantidad');
        const btnAgregar = document.getElementById('btnAgregarCarrito');
        const notificacion = document.getElementById('notificacionCarrito');
        const carritoBadge = document.getElementById('carritoBadge');
        
        // Datos del producto
        const productoId = <?php echo $articulo['idArticulo']; ?>;
        const productoNombre = "<?php echo addslashes($articulo['nombre']); ?>";
        const productoPrecio = <?php echo $articulo['precio']; ?>;
        const stockMaximo = <?php echo $articulo['stock']; ?>;
        
        // Función para mostrar notificación temporal
        function mostrarNotificacion(mensaje, esError = false) {
            notificacion.innerHTML = esError ? 
                '<i class="fas fa-exclamation-circle"></i> ' + mensaje : 
                '<i class="fas fa-check-circle"></i> ' + mensaje;
            notificacion.style.backgroundColor = esError ? '#f8d7da' : '#d4edda';
            notificacion.style.color = esError ? '#721c24' : '#155724';
            notificacion.style.display = 'block';
            
            setTimeout(() => {
                notificacion.style.display = 'none';
            }, 3000);
        }
        
        // Función para actualizar el badge del carrito
        function actualizarBadgeCarrito() {
            fetch('ajaxCarrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=get_count'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.total_items > 0) {
                    carritoBadge.textContent = data.total_items;
                    carritoBadge.style.display = 'inline-flex';
                } else {
                    carritoBadge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Función para agregar al carrito
        function agregarAlCarrito() {
            const cantidad = parseInt(inputCantidad.value);
            
            if (cantidad < 1) {
                mostrarNotificacion('La cantidad debe ser al menos 1', true);
                return;
            }
            
            if (cantidad > stockMaximo) {
                mostrarNotificacion('No hay suficiente stock disponible', true);
                return;
            }
            
            // Deshabilitar botón mientras se procesa
            btnAgregar.disabled = true;
            btnAgregar.classList.add('loading');
            
            const body = `action=agregar&id=${productoId}&nombre=${encodeURIComponent(productoNombre)}&precio=${productoPrecio}&cantidad=${cantidad}`;
            
            fetch('ajaxCarrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(`¡${productoNombre} agregado al carrito!`);
                    actualizarBadgeCarrito();
                    
                    // Actualizar stock mostrado si es necesario
                    if (data.nuevo_stock !== undefined) {
                        const stockValor = document.getElementById('stockValor');
                        if (stockValor) {
                            stockValor.textContent = data.nuevo_stock + ' unidades';
                        }
                    }
                } else {
                    mostrarNotificacion(data.error || 'Error al agregar al carrito', true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión al servidor', true);
            })
            .finally(() => {
                btnAgregar.disabled = false;
                btnAgregar.classList.remove('loading');
            });
        }
        
        // Eventos de cantidad
        if (btnMenos) {
            btnMenos.addEventListener('click', function() {
                let valor = parseInt(inputCantidad.value);
                if (valor > 1) inputCantidad.value = valor - 1;
            });
        }
        
        if (btnMas) {
            btnMas.addEventListener('click', function() {
                let valor = parseInt(inputCantidad.value);
                if (valor < stockMaximo) inputCantidad.value = valor + 1;
            });
        }
        
        if (inputCantidad) {
            inputCantidad.addEventListener('change', function() {
                let valor = parseInt(this.value);
                if (isNaN(valor) || valor < 1) this.value = 1;
                if (valor > stockMaximo) this.value = stockMaximo;
            });
        }
        
        // Evento del botón agregar
        if (btnAgregar) {
            btnAgregar.addEventListener('click', agregarAlCarrito);
        }
        
        // Actualizar badge al cargar la página
        actualizarBadgeCarrito();
    });
</script>

</body>
</html>