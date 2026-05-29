
// ============================================
// CARRUSEL DINÁMICO CON DATOS DE LA BASE DE DATOS
// ============================================
const API_URL = 'index.php?format=json';
const MAX_ITEMS = 5;

async function obtenerImagenesDelServidor() {
    try {
        const response = await fetch(API_URL, { cache: 'no-store' });
        if (!response.ok) {
            throw new Error(`Error ${response.status}`);
        }
        const productos = await response.json();
        if (!Array.isArray(productos) || productos.length === 0) {
            throw new Error('No se encontraron productos');
        }

        return productos.map(producto => ({
            id: producto.idArticulo,
            src: producto.imagen_url || 'assets/multimedia/pictures/articulos/placeholder.jpg',
            alt: producto.nombre || 'Producto',
        }));
    } catch (error) {
        console.error('No se pudo cargar el carrusel:', error);
        return [];
    }
}

function elegirProductosAleatorios(items, cantidad) {
    const copia = [...items];
    for (let i = copia.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [copia[i], copia[j]] = [copia[j], copia[i]];
    }
    return copia.slice(0, cantidad);
}

(function () {
    const track = document.getElementById('carruselTrack');
    const dotsContainer = document.getElementById('carruselDots');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');

    if (!track) return;

    let indiceActual = 0;
    let intervalo;
    let imagenesVista = [];

    async function inicializarCarrusel() {
        const imagenes = await obtenerImagenesDelServidor();
        if (imagenes.length === 0) {
            console.warn('Usando contenido por defecto para el carrusel.');
            return;
        }

        imagenesVista = elegirProductosAleatorios(imagenes, MAX_ITEMS);

        imagenesVista.forEach((item) => {
            const slide = document.createElement('div');
            slide.classList.add('carrusel-slide');

            const enlace = document.createElement('a');
            enlace.href = `detalleproducto.php?id=${encodeURIComponent(item.id)}`;
            enlace.setAttribute('aria-label', `Ver detalles de ${item.alt}`);

            const imagen = document.createElement('img');
            imagen.src = item.src;
            imagen.alt = item.alt;
            imagen.loading = 'lazy';
            imagen.classList.add('carrusel-imagen');

            enlace.appendChild(imagen);
            slide.appendChild(enlace);
            track.appendChild(slide);
        });

        imagenesVista.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.classList.add('carrusel-dot');
            dot.setAttribute('aria-label', `Ir a imagen ${i + 1}`);
            if (i === 0) dot.classList.add('activo');
            dot.addEventListener('click', () => irA(i));
            dotsContainer.appendChild(dot);
        });

        actualizarUI();
        reiniciarIntervalo();
    }

    function actualizarUI() {
        track.style.transform = `translateX(-${indiceActual * 100}%)`;
        document.querySelectorAll('.carrusel-dot').forEach((dot, i) => {
            dot.classList.toggle('activo', i === indiceActual);
        });
    }

    function irA(indice) {
        if (imagenesVista.length === 0) return;
        indiceActual = (indice + imagenesVista.length) % imagenesVista.length;
        actualizarUI();
        reiniciarIntervalo();
    }

    function siguiente() { irA(indiceActual + 1); }
    function anterior() { irA(indiceActual - 1); }

    function reiniciarIntervalo() {
        clearInterval(intervalo);
        intervalo = setInterval(siguiente, 5000);
    }

    btnNext.addEventListener('click', siguiente);
    btnPrev.addEventListener('click', anterior);

    let touchStartX = 0;
    track.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) diff > 0 ? siguiente() : anterior();
    }, { passive: true });

    inicializarCarrusel();
})();
