// arreglo de imágenes para el carrusel
const imagenesCarrusel = [
    {
        src: "assets/multimedia/pictures/conejos1.png",
        alt: "Conejos en el heno"
    },
    {
        src: "assets/multimedia/pictures/conejos2.png",
        alt: "Conejos blancos comiendo heno"
    },
    {
        src: "assets/multimedia/pictures/conejos3.png",
        alt: "Conejo blanco en jaula"
    },
    {
        src: "assets/multimedia/pictures/articulo1.png",
        alt: "Túnel para conejo"
    },
    {
        src: "assets/multimedia/pictures/articulo2.png",
        alt: "Alimento para conejo RedKite"
    },
];

// codigo para el carrusel, no moverle
window.addEventListener("load", () => {
(function () {
    const track = document.getElementById("carruselTrack");
    const dotsContainer = document.getElementById("carruselDots");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");

    if (!track) return;

    let indiceActual = 0;
    let intervalo;

    // slides
    imagenesCarrusel.forEach((img) => {
        const slide = document.createElement("div");
        slide.classList.add("carrusel-slide");

        const imagen = document.createElement("img");
        imagen.src = img.src;
        imagen.alt = img.alt;
        imagen.loading = "lazy";

        slide.appendChild(imagen);
        track.appendChild(slide);
    });

    // dots
    imagenesCarrusel.forEach((_, i) => {
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
        indiceActual = (indice + imagenesCarrusel.length) % imagenesCarrusel.length;
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

    // táctil
    let touchStartX = 0;
    track.addEventListener("touchstart", (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener("touchend", (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) diff > 0 ? siguiente() : anterior();
    }, { passive: true });

    reiniciarIntervalo();
})();
});
