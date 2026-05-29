<?php
    include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hábitats | Bobby Bunny Shop!</title>
    <script src="assets/js/productos.js" defer></script>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <main>
        <div class="hero">
            <div class="hero-content">
                <h1>Hábitats Confortables </h1>
                <p>Jaulas, parques y accesorios para crear el hogar perfecto para tu conejo.</p>
                <button class="btn-hero" onclick="document.querySelector('.productos-wrapper').scrollIntoView({behavior: 'smooth'})">
                    Ver hábitats →
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
            titulo: "Los Mejores Hábitats 🏠",
            descripcion: "Lo mejor segun los usuarios para crear el hogar perfecto para tu conejo.",
            seccion: "habitats",
            categoriasMap: {
                14: { nombre: 'Corrales de Interior', filtro: 'CorralesInterior' },
                15: { nombre: 'Madrigueras Interiores', filtro: 'MadriguerasInteriores' },
                16: { nombre: 'Recintos de Exterior', filtro: 'RecintosExterior' },
                17: { nombre: 'Complementos y Adornos', filtro: 'ComplementosAdornos' },
                18: { nombre: 'Zonas con Suelo Seguro', filtro: 'sueloSeguro' }
            },
            idCategoriasPermitidas: [14, 15, 16, 17, 18],
        };
    </script>
</body>

</html>
