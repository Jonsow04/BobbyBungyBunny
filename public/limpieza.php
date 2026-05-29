<?php
    include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpieza y cuidado | Bobby Bunny Shop!</title>
    <link rel="icon" href="assets/multimedia/pictures/icon-pagina.png">
    <link rel="stylesheet" href="assets/css/indexStyleSheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    
    <main>
        <div class="hero">
            <div class="hero-content">
                <h1>Limpieza y Cuidado </h1>
                <p>Mantén a tu conejo sano y su entorno impecable con nuestros productos especializados.</p>
                <button class="btn-hero" onclick="document.querySelector('.productos-wrapper').scrollIntoView({behavior: 'smooth'})">
                    Ver productos →
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
            titulo: "Limpieza y Cuidado Confiable 🧼",
            descripcion: "Mantén a tu conejo sano y su entorno impecable con nuestros productos mas usados.",
            seccion: "limpieza",
            categoriasMap: {
                19: { nombre: 'Desinfectantes de Entorno', filtro: 'DesinfectantesEntorno' },
                20: { nombre: 'bandeja Sanitaria', filtro: 'Baño' },
                21: { nombre: 'Cepillos y peines', filtro: 'Cepillos' },
                22: { nombre: 'Cortauñas', filtro: 'Cortauñas' },
                23: { nombre: 'Limpieza en seco', filtro: 'Toallitas' },
                24: { nombre: 'higiene dental', filtro: 'higieneDental' },
                25: { nombre: 'Antimiasis', filtro: 'Antimiasis' },
                26: { nombre: 'Cuidado de patas', filtro: 'Patas' }
            },
            idCategoriasPermitidas: [19, 20, 21, 22, 23, 24, 25, 26],
        };
    </script>
    <script src="assets/js/productos.js"></script>
</body>

</html>
