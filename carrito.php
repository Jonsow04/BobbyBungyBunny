<?php

session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/controllers/carritoController.php';

$pdo = getConnection();
$carritoController = new CarritoController($pdo);
$resumen = $carritoController->getResumen();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito | Bobby Bunny Shop!</title>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/carritoStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="barra-nav">
            <a href="index.php"><img src="assets/multimedia/pictures/icon.png" alt="Miffy" class="icono"></a>
            <ul class="nav-ul">
                <li>
                    <form action="" class="barra-busqueda">
                        <input type="search" name="barra-busqueda" placeholder="Buscar productos...">
                        <button type="submit" class="boton-busqueda">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </li>
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
        <div class="carrito-container">
            <h1 class="carrito-titulo">
                <i class="fas fas fa-shopping-basket"></i> Mi carrito
            </h1>
        
            <div class="acciones-header">
                <button class="btn-vaciar" id="btnVaciarCarrito">
                    <i class="fas fa-trash-alt"></i> Vaciar carrito
                </button>
            </div>
            
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <div class="mensaje-login">
                    <i class="fas fa-info-circle"></i>
                    ¿Tienes cuenta? <a href="login.php">Inicia sesión</a> para guardar tu carrito y ver tus pedidos.
                </div>
            <?php endif; ?>
            
            <?php if ($resumen['total_items'] == 0): ?>
                <div class="carrito-vacio">
                    <i class="fas fa-shopping-basket"></i>
                    <p>Tu carrito está vacío</p>
                    <a href="index.php" class="btn-seguir-comprando">
                        <i class="fas fa-arrow-left" style="font-size: 1rem"></i>  Seguir comprando
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
                    <button class="btn-checkout" id="btnCheckout">
                        <i class="fas fa-credit-card"></i> Proceder al pago
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer></footer>

    <script>
        function actualizarCarrito(action, id, cantidad = null) {
            let body = `action=${action}&id=${id}`;
            if (cantidad !== null) body += `&cantidad=${cantidad}`;
            
            fetch('ajax_carrito.php', {
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
                        fetch('ajax_carrito.php', {
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
    
    <style>
        .carrito-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background-color: #e74c3c;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 18px;
            text-align: center;
        }
    </style>
</body>

</html>
