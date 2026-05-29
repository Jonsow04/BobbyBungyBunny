<?php
session_start();
$titulo = 'Finalizar compra | Bobby Bunny Shop';
$css_adicional = 'assets/css/checkoutStyleSheet.css';
include 'includes/header.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/controllers/pedidoController.php';
require_once __DIR__ . '/../includes/controllers/carritoController.php';
require_once __DIR__ . '/../includes/helpers/sanitize.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = getConnection();
$usuarioId = $_SESSION['usuario_id'];

// Obtener la dirección del usuario desde la base de datos
$stmt = $pdo->prepare("
    SELECT d.* 
    FROM direccion d 
    INNER JOIN usuario u ON u.idDireccion = d.idDireccion 
    WHERE u.idUsuario = ?
");
$stmt->execute([$usuarioId]);
$direccionUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el usuario tiene dirección registrada
if (!$direccionUsuario) {
    $_SESSION['error_direccion'] = "No tienes una dirección registrada. Por favor, actualiza tu perfil antes de continuar.";
    header('Location: perfil.php');
    exit();
}

// Formatear dirección completa con valores sanitizados
$direccionCompleta = sprintf(
    "%s %s, %s, %s, %s, CP %s",
    $direccionUsuario['calle'],
    $direccionUsuario['numCasa'],
    $direccionUsuario['colonia'],
    $direccionUsuario['ciudad'],
    $direccionUsuario['estado'],
    SanitizeHelper::sanitizarCP($direccionUsuario['cp'])
);

$pedidoController = new PedidoController($pdo);
$carritoController = new CarritoController($pdo);
$resumenCarrito = $carritoController->getResumen();

if ($resumenCarrito['total_items'] == 0) {
    header('Location: index.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar la dirección por si viene del formulario (aunque sea readonly)
    $direccionEnvio = SanitizeHelper::sanitizarDireccion($direccionCompleta);
    
    $resultado = $pedidoController->crearPedido($usuarioId, $direccionEnvio);
    
    if ($resultado['success']) {
        header('Location: confirmacion.php?pedido=' . $resultado['pedido_id']);
        exit();
    } else {
        $error = SanitizeHelper::limpiarTexto($resultado['error']);
    }
}
?>

<head>
    <link rel="stylesheet" href="../assets/css/checkoutStyleSheet.css">
</head>

<main class="checkout-main">
    <div class="checkout-header">
        <h1><i class="fas fa-credit-card"></i> Finalizar compra</h1>
        <p>Revisa los detalles de tu pedido antes de confirmar</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" id="checkoutForm">
        <div class="checkout-grid">
            <!-- Columna izquierda: Dirección de envío -->
            <div class="checkout-section">
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Dirección de envío</span>
                </div>
                
                <div class="direccion-card">
                    <i class="fas fa-home"></i>
                    <div class="direccion-texto">
                        <?php echo nl2br(htmlspecialchars($direccionCompleta)); ?>
                    </div>
                </div>
            </div>
            
            <!-- Columna derecha: Resumen del pedido -->
            <div class="checkout-section">
                <div class="section-title">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Resumen del pedido</span>
                </div>
                
                <div class="resumen-items">
                    <?php foreach ($resumenCarrito['items'] as $item): ?>
                        <div class="resumen-item">
                            <div class="resumen-item-nombre">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                                <small>x<?php echo $item['cantidad']; ?></small>
                            </div>
                            <div class="resumen-item-precio">
                                $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="resumen-divisor"></div>
                
                <div class="resumen-linea">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($resumenCarrito['subtotal'], 2); ?></span>
                </div>
                
                <div class="resumen-linea">
                    <span>Envío</span>
                    <span>Gratis</span>
                </div>
                
                <div class="resumen-linea resumen-total">
                    <span>Total</span>
                    <span class="total-valor">$<?php echo number_format($resumenCarrito['total'], 2); ?></span>
                </div>
                
                <button type="submit" class="btn-confirmar">
                    <i class="fas fa-check-circle"></i> Confirmar pedido
                </button>
            </div>
        </div>
    </form>
</main>

<?php include 'includes/footer.php'; ?>