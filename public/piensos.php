<?php
    include 'includes/header.php';
?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piensos y henos | Bobby Bunny Shop!</title>

    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <main>
        <div class="hero">
            <div class="hero-content">
                <h1>Piensos y henos más populares 🐰</h1>
                <p>Alimentación balanceada, heno y pienso aprobado por los consumidores. ¡Amor en cada bocado!</p>
                <button class="btn-hero" onclick="document.querySelector('.productos-wrapper').scrollIntoView({behavior: 'smooth'})">
                    Explorar colección →
                </button>
            </div>
        </div>

        <div class="filtros">
            <ul></ul>
        </div>

        <div class="productos-wrapper">
            <div class="productos-grid" id="productosGrid"></div>
        </div>
    </main>

    <script>
        window.CONFIG_SECCION = {
            categoriasMap: {
                1: { nombre: 'Junior/Gazapos', filtro: 'piensoJunior' },
                2: { nombre: 'Adulto', filtro: 'piensoAdulto' },
                3: { nombre: 'Senior/Especiales', filtro: 'piensoSenior' }
            },
            idCategoriasPermitidas: [1, 2, 3],
        };
    </script>
    <script src="assets/js/productos.js"></script>
</body>

</html>
