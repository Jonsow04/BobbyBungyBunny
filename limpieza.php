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
    <header>
        <nav class="barra-nav">
            <a href="index.php">
                <img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="icono">
            </a>
            <form class="barra-busqueda" action="">
                <input type="search" placeholder="Buscar productos...">
                <button type="submit" class="boton-busqueda">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <ul class="nav-ul">
                <li><a href="#"><i class="fas fa-shopping-bag"></i></a></li>
                <li><a href="#">Registrarse</a></li>
                <li><a href="login.php">Iniciar sesión</a></li>
            </ul>
        </nav>

        <nav class="barra-nav-sec">
            <ul class="nav-ul">
                <li><a href="piensos.php">Piensos</a></li>
                <li><a href="premios.php">Premios</a></li>
                <li><a href="juguetes.php">Juguetes</a></li>
                <li><a href="habitats.php">Habitats</a></li>
                <li>
                    <a href="limpieza.php" style="color: white; border-bottom: 2px solid white;">
                        Limpieza y cuidado
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="hero">
            <div class="hero-content">
                <h1>Limpieza y Cuidado 🧼</h1>
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
                18: { nombre: 'Desinfectantes de Entorno', filtro: 'DesinfectantesEntorno' },
                19: { nombre: 'bandeja Sanitaria', filtro: 'Baño' },
                20: { nombre: 'Cepillos y peines', filtro: 'Cepillos' },
                21: { nombre: 'Cortauñas', filtro: 'Cortauñas' },
                22: { nombre: 'Limpieza en seco', filtro: 'Toallitas' },
                23: { nombre: 'higiene dental', filtro: 'higieneDental' },
                24: { nombre: 'Antimiasis', filtro: 'Antimiasis' },
                25: { nombre: 'Cuidado de patas', filtro: 'Patas' }
            },
            idCategoriasPermitidas: [18, 19, 20, 21, 22, 23, 24, 25],
        };
    </script>
    <script src="assets/js/productos.js"></script>
</body>

</html>
