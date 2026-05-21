<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bobby Bunny Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/loginUserStyleSheet.css">
    <style>
        .error-msg {
            color: #c0392b;
            background: #fdecea;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: .88rem;
            margin-bottom: 12px;
            display: none;
        }
        .campo input.input-error { border-color: #e74c3c !important; }
        .btn-login:disabled { opacity: .6; cursor: not-allowed; }
    </style>
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
                <li><a href="registro.php">Registrarse</a></li>
            </ul>
        </nav>

        <nav class="barra-nav-sec">
            <ul class="nav-ul">
                <li><a href="piensos.php">Piensos</a></li>
                <li><a href="premios.php">Premios</a></li>
                <li><a href="juguetes.php">Juguetes</a></li>
                <li><a href="habitats.php">Habitats</a></li>
                <li><a href="limpieza.php">Limpieza y cuidado</a></li>
            </ul>
        </nav>
    </header>

    <div class="login-wrapper">
        <div class="login-card">
            <h1>Bienvenido</h1>
            <p class="subtitulo">Inicia sesión en Conejos.com</p>
            
            <div class="divisor"><span>✦</span></div>

            <div class="campo">
                <label for="correo">Usuario</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="correo" placeholder="Nombre de usuario" autocomplete="username">
                </div>
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasena" placeholder="••••••••" autocomplete="current-password">
                </div>
            </div>

            <p id="loginError" class="error-msg" role="alert"></p>

            <button class="btn-login" id="btnLogin">Iniciar sesión</button>

            <p class="registro-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <footer>© 2026 Bobby Bunny Shop · Todo para tu conejo</footer>

    <script>
    (function () {
        const btnLogin  = document.getElementById('btnLogin');
        const errorBox  = document.getElementById('loginError');
        const inputUser = document.getElementById('correo');
        const inputPass = document.getElementById('contrasena');

        function showError(msg) {
            errorBox.textContent = msg;
            errorBox.style.display = 'block';
        }
        function clearError() {
            errorBox.textContent = '';
            errorBox.style.display = 'none';
            [inputUser, inputPass].forEach(el => el.classList.remove('input-error'));
        }

        function validateFrontend() {
            clearError();
            const user = inputUser.value.trim();
            const pass = inputPass.value;

            if (!user) {
                inputUser.classList.add('input-error');
                showError('El nombre de usuario es obligatorio.');
                return false;
            }
            if (user.length < 3) {
                inputUser.classList.add('input-error');
                showError('El usuario debe tener al menos 3 caracteres.');
                return false;
            }
            if (!pass) {
                inputPass.classList.add('input-error');
                showError('La contraseña es obligatoria.');
                return false;
            }
            if (pass.length < 6) {
                inputPass.classList.add('input-error');
                showError('La contraseña debe tener al menos 6 caracteres.');
                return false;
            }
            return true;
        }

        btnLogin.addEventListener('click', async function () {
            if (!validateFrontend()) return;

            btnLogin.disabled = true;
            btnLogin.textContent = 'Iniciando sesión…';

            const body = new URLSearchParams({
                usuario:    inputUser.value.trim(),
                contrasena: inputPass.value,
            });

            try {
                const res  = await fetch('ajaxLogin.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString(),
                });
                const data = await res.json();

                if (data.success) {
                    window.location.href = data.redirect ?? 'index.php';
                } else {
                    showError(data.error ?? 'Error al iniciar sesión.');
                    btnLogin.disabled = false;
                    btnLogin.textContent = 'Iniciar sesión';
                }
            } catch (err) {
                showError('No se pudo conectar con el servidor. Inténtalo de nuevo.');
                btnLogin.disabled = false;
                btnLogin.textContent = 'Iniciar sesión';
            }
        });

        [inputUser, inputPass].forEach(el =>
            el.addEventListener('input', clearError)
        );

        [inputUser, inputPass].forEach(el =>
            el.addEventListener('keydown', e => { if (e.key === 'Enter') btnLogin.click(); })
        );
    })();
    </script>
</body>

</html>
