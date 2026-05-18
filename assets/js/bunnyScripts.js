// ============================================
// ARREGLO PRINCIPAL DEL CARRUSEL
// ============================================
const imagenesCarrusel = [
    {
        src: "assets/multimedia/pictures/articulos/habitat-jaula.jpg",
        alt: "Jaula para conejos"
    },
    {
        src: "assets/multimedia/pictures/articulos/kaytee-fiesta-1_6kg.jpg",
        alt: "Comida para conejos Kaytee 6kg"
    },
    {
        src: "assets/multimedia/pictures/articulos/kaytee-pellets-supreme-4_54kg.jpg",
        alt: "Alimento para conejo Kaytee 54kg"
    },
    {
        src: "assets/multimedia/pictures/articulos/kit-aseo.jpg",
        alt: "Kit de aseo del conejo"
    },
    {
        src: "assets/multimedia/pictures/articulos/tazas-apilables.jpg",
        alt: "Tazas apilables"
    },
];

// Selecciona 4 imágenes aleatorias del arreglo principal para mostrar en el carrusel
const imagenesVista = [...imagenesCarrusel]
    .sort(() => Math.random() - 0.5)
    .slice(0, 4);

// ============================================
// LÓGICA DEL CARRUSEL (no tocar xd)
// ============================================
(function () {
    const track = document.getElementById("carruselTrack");
    const dotsContainer = document.getElementById("carruselDots");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");

    if (!track) return;

    let indiceActual = 0;
    let intervalo;

    // Construir slides
    imagenesVista.forEach((img) => {
        const slide = document.createElement("div");
        slide.classList.add("carrusel-slide");

        const imagen = document.createElement("img");
        imagen.src = img.src;
        imagen.alt = img.alt;
        imagen.loading = "lazy";

        slide.appendChild(imagen);
        track.appendChild(slide);
    });

    // Construir dots
    imagenesVista.forEach((_, i) => {
        const dot = document.createElement("button");
        dot.classList.add("carrusel-dot");
        dot.setAttribute("aria-label", `Ir a imagen ${i + 1}`);
        if (i === 0) dot.classList.add("activo");
        dot.addEventListener("click", () => irA(i));
        dotsContainer.appendChild(dot);
    });

    function actualizarUI() {
        track.style.transform = `translateX(-${indiceActual * 100}%)`;
        document.querySelectorAll(".carrusel-dot").forEach((dot, i) => {
            dot.classList.toggle("activo", i === indiceActual);
        });
    }

    function irA(indice) {
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

    btnNext.addEventListener("click", siguiente);
    btnPrev.addEventListener("click", anterior);

    // Swipe táctil
    let touchStartX = 0;
    track.addEventListener("touchstart", (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener("touchend", (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) diff > 0 ? siguiente() : anterior();
    }, { passive: true });

    reiniciarIntervalo();
})();
