<?php

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/controllers/pedidoController.php';

$pdo = getConnection();
$pedidoController = new PedidoController($pdo);

// Verificar que el usuario esté logueado
$usuarioId = $pedidoController->verificarLogin();

// Obtener pedidos del usuario
$pedidos = $pedidoController->getMisPedidos();

// Obtener detalles de un pedido específico si se solicita
$detallePedido = null;
$pedidoSeleccionado = null;

if (isset($_GET['ver']) && is_numeric($_GET['ver'])) {
    $detalleData = $pedidoController->getDetallesPedido($_GET['ver']);
    if ($detalleData) {
        $pedidoSeleccionado = $detalleData['pedido'];
        $detallePedido = $detalleData['detalles'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pedidos | Bobby Bunny Shop!</title>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/misPedidosStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="barra-nav">
            <a href="index.php"><img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="icono"></a>
            <form class="barra-busqueda" action="">
                <input type="search" placeholder="Buscar productos...">
                <button type="submit" class="boton-busqueda">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <ul class="nav-ul">
                <li><a href="carrito.php"><i class="fas fa-shopping-bag"></i></a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
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
        <div class="pedidos-container">
            <h1 class="pedidos-titulo">
                <i class="fas fa-box"></i> Mis pedidos
            </h1>
            
            <?php if (empty($pedidos)): ?>
                <div class="sin-pedidos">
                    <i class="fas fa-shopping-bag"></i>
                    <p>No has realizado ningún pedido aún.</p>
                    <a href="index.php" class="btn-ver-detalle">
                        <i class="fas fa-shopping-cart"></i> Comenzar a comprar
                    </a>
                </div>
            <?php else: ?>
                <div class="pedidos-grid">
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php
                        // Determinar clase de estatus
                        $estatusClase = 'estatus-pendiente';
                        switch ($pedido['estatus_nombre']) {
                            case 'Pagado': $estatusClase = 'estatus-pagado'; break;
                            case 'Enviado': $estatusClase = 'estatus-enviado'; break;
                            case 'Entregado': $estatusClase = 'estatus-entregado'; break;
                            case 'Cancelado': $estatusClase = 'estatus-cancelado'; break;
                        }
                        ?>
                        <div class="pedido-card">
                            <div class="pedido-header">
                                <div>
                                    <span class="pedido-numero">
                                        Pedido #<?php echo str_pad($pedido['idPedido'], 8, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <span class="pedido-fecha">
                                        <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?>
                                    </span>
                                </div>
                                <span class="pedido-estatus <?php echo $estatusClase; ?>">
                                    <?php echo htmlspecialchars($pedido['estatus_nombre']); ?>
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                                <div>
                                    <span class="pedido-total">
                                        Total: $<?php echo number_format($pedido['total'], 2); ?>
                                    </span>
                                </div>
                                <button class="btn-ver-detalle" onclick="verDetallePedido(<?php echo $pedido['idPedido']; ?>)">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal para detalles del pedido -->
    <div id="modalDetalle" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-receipt"></i> Detalle del pedido</h3>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>
            <div id="modalDetalleBody">
                <!-- Contenido cargado vía AJAX -->
                <div class="loader" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                </div>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 2rem; color: var(--marron-acento);">
        <p>Bobby Bunny Shop - Tu tienda de confianza para conejos felices 🐰</p>
    </footer>

    <script>
        const modal = document.getElementById('modalDetalle');
        
        function verDetallePedido(pedidoId) {
            modal.style.display = 'flex';
            
            // Cargar detalles vía AJAX
            fetch(`ajaxPedido.php?action=detalle&id=${pedidoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderizarDetalle(data);
                    } else {
                        document.getElementById('modalDetalleBody').innerHTML = `
                            <div style="text-align: center; color: #e74c3c;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>${data.error || 'Error al cargar los detalles'}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalDetalleBody').innerHTML = `
                        <div style="text-align: center; color: #e74c3c;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Error al cargar los detalles</p>
                        </div>
                    `;
                });
        }
        
        function renderizarDetalle(data) {
            const pedido = data.pedido;
            const detalles = data.detalles;
            
            let html = `
                <p><strong>Pedido #${String(pedido.idPedido).padStart(8, '0')}</strong></p>
                <p><strong>Fecha:</strong> ${new Date(pedido.fecha).toLocaleDateString()}</p>
                <p><strong>Estado:</strong> ${pedido.estatus_nombre}</p>
                <p><strong>Dirección de envío:</strong><br>${pedido.direccion.replace(/\n/g, '<br>')}</p>
                
                <h4 style="margin-top: 1rem;">Productos:</h4>
                <table class="detalle-tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            detalles.forEach(detalle => {
                const subtotal = detalle.precioUnitario * detalle.cantidad;
                html += `
                    <tr>
                        <td>${escapeHTML(detalle.articulo_nombre)}</td>
                        <td>${detalle.cantidad}</td>
                        <td>$${parseFloat(detalle.precioUnitario).toFixed(2)}</td>
                        <td>$${subtotal.toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
                <p style="text-align: right; margin-top: 1rem; font-size: 1.2rem; font-weight: bold;">
                    Total: $${parseFloat(pedido.total).toFixed(2)}
                </p>
            `;
            
            document.getElementById('modalDetalleBody').innerHTML = html;
        }
        
        function cerrarModal() {
            modal.style.display = 'none';
            document.getElementById('modalDetalleBody').innerHTML = `
                <div class="loader" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                </div>
            `;
        }
        
        // Cerrar modal al hacer clic fuera
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModal();
            }
        });
        
        function escapeHTML(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
    </script>
</body>

</html>