<?php
session_start();
$titulo = 'Mi carrito | Bobby Bunny Shop';
$css_adicional = 'assets/css/carritoStyleSheet.css';
include 'includes/header.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/controllers/carritoController.php';

$pdo = getConnection();
$carritoController = new CarritoController($pdo);
$resumen = $carritoController->getResumen();
?>

<div class="carrito-container">
    <h1 class="carrito-titulo">
        <i class="fas fa-shopping-basket"></i> Mi carrito
    </h1>

    <div class="acciones-header">
        <button class="btn-vaciar" id="btnVaciarCarrito">
            <i class="fas fa-trash-alt"></i> Vaciar carrito
        </button>
    </div>

    <?php if ($resumen['total_items'] == 0): ?>
        <div class="carrito-vacio">
            <i class="fas fa-shopping-basket"></i>
            <p>Tu carrito está vacío</p>
            <a href="index.php" class="btn-seguir-comprando">
                <i class="fas fa-arrow-left" style="font-size: 1rem"></i> Seguir comprando
            </a>
        </div>
    <?php else: ?>
        <table class="carrito-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="carritoBody">
                <?php foreach ($resumen['items'] as $item): ?>
                    <tr data-id="<?php echo $item['id']; ?>">
                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                        <td>$<?php echo number_format($item['precio'], 2); ?></td>
                        <td>
                            <input type="number" class="cantidad-input" value="<?php echo $item['cantidad']; ?>" min="1" data-id="<?php echo $item['id']; ?>">
                        </td>
                        <td class="item-subtotal">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
                        <td>
                            <button class="btn-eliminar" data-id="<?php echo $item['id']; ?>">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="carrito-resumen">
            <div class="resumen-linea">
                <span>Subtotal:</span>
                <span id="subtotal">$<?php echo number_format($resumen['subtotal'], 2); ?></span>
            </div>
            <div class="resumen-linea">
                <span>Envío:</span>
                <span>A calcular</span>
            </div>
            <div class="resumen-linea resumen-total">
                <span>Total:</span>
                <span id="total">$<?php echo number_format($resumen['total'], 2); ?></span>
            </div>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <button class="btn-checkout" id="btnCheckout">
                    <i class="fas fa-credit-card"></i> Proceder al pago
                </button>
            <?php else: ?>
                <a href="login.php" class="btn-checkout" style="display:block; text-align:center; text-decoration:none;">
                    <i class="fas fa-sign-in-alt"></i> Inicia sesión para pagar
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function actualizarCarrito(action, id, cantidad = null) {
        let body = `action=${action}&id=${id}`;
        if (cantidad !== null) body += `&cantidad=${cantidad}`;
        
        fetch('ajaxCarrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (action === 'eliminar') {
                    const fila = document.querySelector(`tr[data-id="${id}"]`);
                    if (fila) fila.remove();
                }
                
                document.getElementById('subtotal').textContent = '$' + data.subtotal.toFixed(2);
                document.getElementById('total').textContent = '$' + data.total.toFixed(2);
                
                if (data.total_items === 0) {
                    location.reload();
                }
                
                actualizarContadorHeader(data.total_items);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function actualizarContadorHeader(total) {
        const carritoLink = document.querySelector('.barra-nav a[href="carrito.php"]');
        let badge = document.querySelector('.carrito-badge');
        
        if (total > 0) {
            if (!badge && carritoLink) {
                badge = document.createElement('span');
                badge.className = 'carrito-badge';
                carritoLink.style.position = 'relative';
                carritoLink.appendChild(badge);
            }
            if (badge) {
                badge.textContent = total;
                badge.style.display = 'inline-flex';
            }
        } else if (badge) {
            badge.style.display = 'none';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cantidad-input').forEach(input => {
            input.addEventListener('change', function() {
                const id = this.dataset.id;
                const nuevaCantidad = parseInt(this.value);
                if (nuevaCantidad > 0) {
                    actualizarCarrito('actualizar', id, nuevaCantidad);
                    const fila = this.closest('tr');
                    const precio = parseFloat(fila.querySelector('td:nth-child(2)').textContent.replace('$', ''));
                    fila.querySelector('.item-subtotal').textContent = '$' + (precio * nuevaCantidad).toFixed(2);
                }
            });
        });
        
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                if (confirm('¿Eliminar este producto?')) {
                    actualizarCarrito('eliminar', id);
                }
            });
        });
        
        const btnVaciar = document.getElementById('btnVaciarCarrito');
        if (btnVaciar) {
            btnVaciar.addEventListener('click', function() {
                if (confirm('¿Vaciar completamente el carrito?')) {
                    fetch('ajaxCarrito.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=vaciar'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) location.reload();
                    });
                }
            });
        }
        
        const btnCheckout = document.getElementById('btnCheckout');
        if (btnCheckout) {
            btnCheckout.addEventListener('click', function() {
                window.location.href = 'checkout.php';
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>