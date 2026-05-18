
document.addEventListener('DOMContentLoaded', function() {
    inicializarBotonesCarrito();
    actualizarContadorCarrito();
});

function inicializarBotonesCarrito() {
    const botones = document.querySelectorAll('.btn-carrito');
    
    botones.forEach(button => {
        button.removeEventListener('click', manejarClickCarrito);
        button.addEventListener('click', manejarClickCarrito);
    });
}

function manejarClickCarrito(event) {
    const button = event.currentTarget;
    
    if (button.disabled) return;
    
    const producto = {
        id: button.dataset.id,
        nombre: button.dataset.nombre,
        precio: parseFloat(button.dataset.precio)
    };
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Añadiendo...';
    
    fetch('ajaxCarrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=agregar&id=${producto.id}&nombre=${encodeURIComponent(producto.nombre)}&precio=${producto.precio}&cantidad=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensajeExito(`${producto.nombre} añadido al carrito`);
            actualizarContadorCarrito();
            animarBotonCarrito();
        } else {
            mostrarMensajeError('Error al añadir el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensajeError('Error al añadir el producto');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-shopping-cart"></i> Añadir al carrito';
    });
}

function actualizarContadorCarrito() {
    fetch('ajaxCarrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_count'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let badge = document.querySelector('.carrito-badge');
            const carritoLink = document.querySelector('.barra-nav a[href="carrito.php"]');
            
            if (data.total_items > 0) {
                if (!badge && carritoLink) {
                    badge = document.createElement('span');
                    badge.className = 'carrito-badge';
                    carritoLink.style.position = 'relative';
                    carritoLink.appendChild(badge);
                }
                if (badge) {
                    badge.textContent = data.total_items;
                    badge.style.display = 'inline-flex';
                }
            } else if (badge) {
                badge.style.display = 'none';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function animarBotonCarrito() {
    const carritoIcono = document.querySelector('.barra-nav a[href="carrito.php"] i');
    if (carritoIcono) {
        carritoIcono.style.transform = 'scale(1.2)';
        carritoIcono.style.transition = 'transform 0.2s ease';
        setTimeout(() => {
            carritoIcono.style.transform = 'scale(1)';
        }, 200);
    }
}

function mostrarMensajeExito(mensaje) {
    mostrarMensaje(mensaje, 'success');
}

function mostrarMensajeError(mensaje) {
    mostrarMensaje(mensaje, 'error');
}

function mostrarMensaje(mensaje, tipo = 'success') {
    const mensajeAnterior = document.querySelector('.floating-message');
    if (mensajeAnterior) mensajeAnterior.remove();
    
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `floating-message ${tipo}`;
    mensajeDiv.textContent = mensaje;
    
    mensajeDiv.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 1000;
        animation: slideInRight 0.3s ease;
        background-color: ${tipo === 'success' ? '#4CAF50' : '#e74c3c'};
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    document.body.appendChild(mensajeDiv);
    
    setTimeout(() => {
        mensajeDiv.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => mensajeDiv.remove(), 300);
    }, 2000);
}

// Agregar estilos
if (!document.querySelector('#carrito-styles')) {
    const style = document.createElement('style');
    style.id = 'carrito-styles';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    `;
    document.head.appendChild(style);
}
