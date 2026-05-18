<?php
// public/confirmacion.php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/models/pedido.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$pedidoId = $_GET['pedido'] ?? 0;

if (!$pedidoId) {
    header('Location: index.php');
    exit();
}

$pdo = getConnection();
$pedidoModel = new Pedido($pdo);
$pedido = $pedidoModel->obtenerPorId($pedidoId);
$detalles = $pedidoModel->obtenerDetalles($pedidoId);

// Verificar que el pedido pertenece al usuario
if ($pedido['idUsuario'] != $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido confirmado | Bobby Bunny Shop!</title>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/checkoutStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="barra-nav">
            <a href="index.php"><img src="assets/multimedia/pictures/icon.png" alt="Miffy" class="icono"></a>
            <ul class="nav-ul">
                <li><a href="index.php">Tienda</a></li>
                <li><a href="mis-pedidos.php">Mis pedidos</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="confirmacion-container">
            <div class="icono-exito">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>¡Pedido confirmado!</h1>
            <p>Gracias por tu compra. Hemos recibido tu pedido correctamente.</p>
            
            <div class="pedido-info">
                <p><strong>Número de pedido:</strong> #<?php echo str_pad($pedido['idPedido'], 8, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></p>
                <p><strong>Estado:</strong> <?php echo $pedido['estatus_nombre']; ?></p>
                <p><strong>Dirección de envío:</strong> <?php echo nl2br(htmlspecialchars($pedido['direccion'])); ?></p>
                
                <h3>Productos:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['articulo_nombre']); ?></td>
                            <td><?php echo $detalle['cantidad']; ?></td>
                            <td>$<?php echo number_format($detalle['precioUnitario'], 2); ?></td>
                            <td>$<?php echo number_format($detalle['precioUnitario'] * $detalle['cantidad'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <p style="text-align: right; font-size: 1.2rem; margin-top: 1rem;">
                    <strong>Total: $<?php echo number_format($pedido['total'], 2); ?></strong>
                </p>
            </div>
            
            <a href="index.php" class="btn-seguir">
                <i class="fas fa-shopping-cart"></i> Seguir comprando
            </a>
            <a href="mis-pedidos.php" class="btn-seguir btn-ver-pedidos">
                <i class="fas fa-list"></i> Ver mis pedidos
            </a>
        </div>
    </main>

</body>

</html>
