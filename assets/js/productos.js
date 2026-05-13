//     titulo: "Piensos Más Populares ",
     

(function() {
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        // Verificar que exista la configuración
        if (!window.CONFIG_SECCION) {
            console.error("No se encontró window.CONFIG_SECCION. Define la configuración para esta página.");
            return;
        }

        const config = window.CONFIG_SECCION;
        
        // Elementos del DOM
        const heroTitle = document.querySelector('.hero h1');
        const heroDesc = document.querySelector('.hero p');
        const filtrosContainer = document.querySelector('.filtros ul');
        const productosGrid = document.getElementById('productosGrid');

        // Si no existe el grid, salir
        if (!productosGrid) return;

        // Aplicar título y descripción del héroe 
        if (heroTitle && config.titulo) heroTitle.textContent = config.titulo;
        if (heroDesc && config.descripcion) heroDesc.textContent = config.descripcion;

        //  Filtros basados en categoriasMap
        if (filtrosContainer && config.categoriasMap) {
            // Limpiar filtros existentes (excepto el primero)
            filtrosContainer.innerHTML = '';
            // Agregar filtro "Todos"
            const liTodo = document.createElement('li');
            liTodo.innerHTML = `<a href="#" data-filtro="todo" class="activo">Todos</a>`;
            filtrosContainer.appendChild(liTodo);
            // Agregar cada categoría
            Object.values(config.categoriasMap).forEach(cat => {
                const li = document.createElement('li');
                li.innerHTML = `<a href="#" data-filtro="${cat.filtro}">${cat.nombre}</a>`;
                filtrosContainer.appendChild(li);
            });
        }

        // Estado global
        let productosOriginales = [];
        let productosFiltrados = [];
        let categoriaActiva = 'todo';

        //para la futura BD
        // Endpoint de API (puede venir en la configuración o usar uno por defecto)
        const API_URL = config.apiUrl || 'http://localhost:3000/api/articulos';

        
         // Mapea un producto desde la respuesta de la API al formato interno
         // Ajusta según la estructura real de tu base de datos
         
        function mapearProducto(item) {
            return {
                id: item.idArticulo,
                nombre: item.nombre,
                descripcion: item.descripcion,
                precio: parseFloat(item.precio),
                stock: item.stock,
                idCategoria: item.idCatArticulo,
                imagen: item.imagen || config.defaultImagen || `assets/multimedia/pictures/producto-${item.idArticulo}.png`
            };
        }

        
         // Renderiza los productos en el grid
         
        function renderizarProductos(productos) {
            if (!productos || productos.length === 0) {
                productosGrid.innerHTML = `
                    <div class="sin-resultados">
                        <i class="fas fa-carrot" style="font-size: 3rem;"></i>
                        <p>No hay productos en esta categoría</p>
                        <p style="font-size: 0.8rem;">¡Pronto tendremos más variedad para tu conejo! </p>
                    </div>
                `;
                return;
            }

            productosGrid.innerHTML = productos.map(producto => `
                <div class="producto-card" data-id="${producto.id}">
                    <img class="producto-imagen" 
                         src="${producto.imagen}" 
                         alt="${escapeHTML(producto.nombre)}"
                         onerror="this.src='${config.defaultImagen || 'assets/multimedia/pictures/default-producto.png'}'">
                    <div class="producto-info">
                        <h3 class="producto-titulo">${escapeHTML(producto.nombre)}</h3>
                        <p class="producto-descripcion">${escapeHTML(producto.descripcion?.substring(0, 100) || 'Sin descripción')}${producto.descripcion?.length > 100 ? '...' : ''}</p>
                        <div class="producto-precio">$${producto.precio.toFixed(2)}</div>
                        <div class="producto-stock ${producto.stock < 5 ? 'stock-bajo' : ''}">
                            ${obtenerMensajeStock(producto.stock)}
                        </div>
                        <button class="btn-agregar" 
                                onclick="agregarAlCarrito(${producto.id})"
                                ${producto.stock <= 0 ? 'disabled' : ''}>
                            <i class="fas fa-cart-plus"></i> 
                            ${producto.stock <= 0 ? 'Sin stock' : 'Agregar'}
                        </button>
                    </div>
                </div>
            `).join('');

            // Animación de entrada
            setTimeout(() => {
                const cards = document.querySelectorAll('.producto-card');
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 50);
                });
            }, 50);
        }

        function obtenerMensajeStock(stock) {
            if (stock <= 0) return '<span class="sin-stock">Agotado</span>';
            if (stock < 5) return `¡Últimas ${stock} unidades!`;
            if (stock < 15) return `Stock: ${stock} unidades`;
            return `Stock disponible`;
        }

        
         // Filtra productos según la categoría activa (basado en el mapeo de filtros)
         
        function filtrarPorCategoria(filtro) {
            categoriaActiva = filtro;
            if (filtro === 'todo') {
                productosFiltrados = [...productosOriginales];
            } else {
                // Obtener los ids de categoría que corresponden a este filtro
                const idsCategoria = Object.keys(config.categoriasMap)
                    .filter(id => config.categoriasMap[id].filtro === filtro)
                    .map(id => parseInt(id));
                productosFiltrados = productosOriginales.filter(p => idsCategoria.includes(p.idCategoria));
            }
            renderizarProductos(productosFiltrados);
            // Actualizar clase activa en los filtros
            document.querySelectorAll('.filtros ul li a').forEach(link => {
                const filtroLink = link.getAttribute('data-filtro');
                if (filtroLink === filtro) {
                    link.classList.add('activo');
                } else {
                    link.classList.remove('activo');
                }
            });
        }

        
         // Carga productos desde la API y luego aplica filtro si la configuración tiene una categoría principal
         
        async function cargarProductos() {
            productosGrid.innerHTML = `
                <div class="loader">
                    <i class="fas fa-spinner"></i>
                    <p>Cargando productos...</p>
                </div>
            `;

            try {
                const respuesta = await fetch(API_URL);
                if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);
                const datos = await respuesta.json();

                // Mapear todos los productos
                let todos = datos.map(item => mapearProducto(item));

                // Si la configuración tiene una categoría principal (rango de ids o array de ids permitidos)
                if (config.idCategoriasPermitidas && Array.isArray(config.idCategoriasPermitidas)) {
                    todos = todos.filter(p => config.idCategoriasPermitidas.includes(p.idCategoria));
                }

                productosOriginales = todos;
                productosFiltrados = [...productosOriginales];
                renderizarProductos(productosFiltrados);
                configurarFiltros();
            } catch (error) {
                console.error('Error al cargar productos:', error);
                productosGrid.innerHTML = `
                    <div class="sin-resultados">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #c0392b;"></i>
                        <p>Error al cargar productos: ${error.message}</p>
                        <button onclick="location.reload()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--cafe-noir); color: white; border: none; border-radius: 5px; cursor: pointer;">Reintentar</button>
                    </div>
                `;
            }
        }

        function configurarFiltros() {
            const filtrosItems = document.querySelectorAll('.filtros ul li a');
            filtrosItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const filtro = item.getAttribute('data-filtro');
                    filtrarPorCategoria(filtro);
                });
            });
        }

        // Iniciar carga
        cargarProductos();
    });
})();

// Función global para agregar al carrito (puedes implementar tu lógica)
window.agregarAlCarrito = function(idProducto) {
    // Aquí debes implementar la lógica real de carrito
    console.log(`Agregar al carrito producto ID: ${idProducto}`);
    mostrarNotificacion(`Producto agregado al carrito`, 'success');
};

function mostrarNotificacion(mensaje, tipo = 'info') {
    const notificacion = document.createElement('div');
    notificacion.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${tipo === 'success' ? '#4c3d19' : '#c0392b'};
        color: white;
        padding: 12px 20px;
        border-radius: 30px;
        font-family: 'Quicksand', sans-serif;
        font-size: 0.9rem;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease;
    `;
    notificacion.innerHTML = `<i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${mensaje}`;
    document.body.appendChild(notificacion);
    setTimeout(() => {
        notificacion.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notificacion.remove(), 300);
    }, 2500);
}

function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// Agregar estilos de animación si no existen
if (!document.querySelector('#animaciones-notificaciones')) {
    const style = document.createElement('style');
    style.id = 'animaciones-notificaciones';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}