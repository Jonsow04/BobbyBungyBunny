<?php
    include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juguetes | Bobby Bunny Shop!</title>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    
    <main>
        <div class="hero">
            <div class="hero-content">
                <h1>Juguetes Divertidos </h1>
                <p>Estimula la mente y el instinto de tu conejo con nuestros juguetes seguros.</p>
                <button class="btn-hero" onclick="document.querySelector('.productos-wrapper').scrollIntoView({behavior: 'smooth'})">
                    Ver juguetes →
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
            titulo: "Juguetes Confiables ⚽",
            descripcion: "Estimula la mente y el instinto de tu conejo con nuestros juguetes confiables.",
            seccion: "juguetes",
            categoriasMap: {
                8: { nombre: 'Juguetes para Roer', filtro: 'mordedores' },
                9: { nombre: 'Juguetes Interactivos', filtro: 'Inteligencia' },
                10: { nombre: 'Juguetes de Búsqueda', filtro: 'Búsqueda' },
                11: { nombre: 'Juguetes de Exploración', filtro: 'Exploración' },
                12: { nombre: 'Juguetes para Lanzar y Rodar', filtro: 'LanzarRodar' },
                13: { nombre: 'Juguetes de Excavación', filtro: 'Excavación' }
            },
            idCategoriasPermitidas: [8, 9, 10, 11, 12, 13],
        };
    </script>
    <script src="assets/js/productos.js"></script>
</body>

</html>
