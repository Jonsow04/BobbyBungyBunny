<?php
session_start();

// Verificar que el usuario sea administrador o gerente
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] != 1 && $_SESSION['usuario_tipo'] != 2)) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/controllers/AdminController.php';

$pdo = getConnection();
$adminController = new AdminController($pdo);

$stats = $adminController->getDashboardStats();
$productosMasVendidos = $adminController->getProductosMasVendidos(5);
$productosBajoStock = $adminController->getProductosBajoStock(5);
$productos = $adminController->getProductos();
$pedidosRecientes = $adminController->getPedidosRecientes(10);

$titulo = 'Dashboard | Administración';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminStyleSheet.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Panel de Administración</h1>
            <div class="admin-info">
                <span><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                <a href="../cerrarSesion.php" class="btn-cerrar-sesion"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>
        
        <!-- Tarjetas de estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h3>Total Productos</h3>
                <div class="stat-number"><?php echo $stats['total_productos']; ?></div>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Stock Bajo</h3>
                <div class="stat-number"><?php echo $stats['productos_bajo_stock']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3>Pedidos del mes</h3>
                <div class="stat-number"><?php echo $stats['pedidos_mes']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Clientes</h3>
                <div class="stat-number"><?php echo $stats['total_clientes']; ?></div>
            </div>
        </div>
        
        <!-- Ventas del mes -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i> Ventas del mes</h2>
                <span class="ventas-total">$<?php echo number_format($stats['ventas_mes'], 2); ?></span>
            </div>
        </div>
        
        <!-- Productos más vendidos -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i> Productos más vendidos</h2>
                <a href="productos.php?top=ventas" class="btn-crear">Ver todos</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Unidades vendidas</th>
                        <th>Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productosMasVendidos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td><?php echo $producto['stock']; ?></td>
                        <td><strong><?php echo $producto['total_vendido']; ?></strong></td>
                        <td>$<?php echo number_format($producto['total_ingresos'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Productos con bajo stock -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-exclamation-circle"></i> Productos con bajo stock (≤5)</h2>
                <a href="productos.php?stock=bajo" class="btn-crear">Ver todos</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productosBajoStock as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre_categoria']); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td class="stock-bajo"><?php echo $producto['stock']; ?> unidades</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Productos disponibles -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Productos disponibles</h2>
                <a href="crear_producto.php" class="btn-crear"><i class="fas fa-plus"></i> Crear producto</a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>
                                <?php if ($producto['imagen']): ?>
                                    <img src="/assets/multimedia/productos/<?php echo $producto['imagen']; ?>" class="producto-miniatura">
                                <?php else: ?>
                                    <div class="sin-imagen">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $producto['idArticulo']; ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                            <td class="<?php echo $producto['stock'] == 0 ? 'stock-agotado' : ($producto['stock'] < 5 ? 'stock-bajo' : ''); ?>">
                                <?php echo $producto['stock']; ?> unidades
                            </td>
                            <td>
                                <a href="editar_producto.php?id=<?php echo $producto['idArticulo']; ?>" class="btn-editar"><i class="fas fa-edit"></i> Editar</a>
                                <?php if ($_SESSION['usuario_tipo'] == 1): ?>
                                    <button onclick="eliminarProducto(<?php echo $producto['idArticulo']; ?>)" class="btn-eliminar"><i class="fas fa-trash"></i> Eliminar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pedidos recientes -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-truck"></i> Pedidos recientes</h2>
                <a href="pedidos.php" class="btn-crear">Ver todos</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidosRecientes as $pedido): ?>
                    <tr>
                        <td>#<?php echo str_pad($pedido['idPedido'], 8, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($pedido['usuario_nombre']); ?></td>
                        <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                        <td><?php echo htmlspecialchars($pedido['estatus_nombre']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <style>
        .producto-miniatura {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .sin-imagen {
            width: 50px;
            height: 50px;
            background: var(--tan);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cafe-noir);
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
    
    <script>
        function eliminarProducto(id) {
            if (confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')) {
                window.location.href = 'eliminar_producto.php?id=' + id;
            }
        }
    </script>
</body>
</html>