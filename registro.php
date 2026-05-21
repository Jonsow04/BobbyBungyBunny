<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | Bobby Bunny Shop!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/registerUserStyleSheet.css">
    <style>
        .error-msg {
            color: #c0392b;
            background: #fdecea;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: .88rem;
            margin-bottom: 10px;
            display: none;
        }
        .success-msg {
            color: #1e7e34;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: .88rem;
            margin-bottom: 10px;
            display: none;
        }
        .campo input.input-error { border-color: #e74c3c !important; }
        .btn-reg:disabled { opacity: .6; cursor: not-allowed; }
        /* requisitos de contraseña */
        .pass-requisitos {
            font-size: .78rem;
            color: #888;
            margin-top: 4px;
            list-style: none;
            padding: 0;
            line-height: 1.6;
        }
        .pass-requisitos li::before { content: '✗ '; color: #e74c3c; }
        .pass-requisitos li.ok::before { content: '✓ '; color: #27ae60; }
    </style>
</head>
<body>

    <header>
        <nav class="barra-nav">
            <a href="index.php">
                <img src="assets/multimedia/pictures/icon.png" alt="Bobby Bunny" class="logo-img"
                     onerror="this.outerHTML='<span class=logo-fallback>🐰</span>'">
            </a>
            <form class="barra-busqueda" action="">
                <input type="search" placeholder="Buscar productos...">
                <button type="submit" class="boton-busqueda"><i class="fas fa-search"></i></button>
            </form>
            <ul class="nav-ul">
                <li><a href="#"><i class="fas fa-shopping-bag"></i></a></li>
                <li><a href="login.php"
                    style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); padding:6px 14px; border-radius:6px;">
                    Iniciar sesión
                </a></li>
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

    <div class="reg-wrapper">
        <div class="reg-card">
            <div class="reg-icono"><span class="conejo">🐰</span></div>
            <h1>Bienvenido</h1>
            <p class="subtitulo">Registra tu cuenta en Conejos.com</p>
            <div class="divisor"><span>✦</span></div>

            <div class="campo">
                <label for="user">Usuario</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" id="user" placeholder="mínimo 3 caracteres" required autocomplete="username">
                </div>
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasena" placeholder="••••••••" autocomplete="new-password" required>
                </div>
                <ul class="pass-requisitos" id="passRequisitos">
                    <li id="req-len">Al menos 8 caracteres</li>
                    <li id="req-letra">Al menos una letra</li>
                    <li id="req-num">Al menos un número</li>
                </ul>
            </div>

            <div class="campo">
                <label for="contrasenaconfirm">Confirmar contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasenaconfirm" placeholder="••••••••" autocomplete="new-password" required>
                </div>
            </div>

            <p id="regError"   class="error-msg"   role="alert"></p>
            <p id="regSuccess" class="success-msg" role="status"></p>

            <button class="btn-reg" id="btnReg">Registrar</button>

            <p class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>

    <footer>© 2025 Bobby Bunny Shop · Todo para tu conejo</footer>

    <script>
    (function () {
        const btnReg     = document.getElementById('btnReg');
        const errorBox   = document.getElementById('regError');
        const successBox = document.getElementById('regSuccess');
        const inputUser  = document.getElementById('user');
        const inputPass  = document.getElementById('contrasena');
        const inputConf  = document.getElementById('contrasenaconfirm');

        const reqLen   = document.getElementById('req-len');
        const reqLetra = document.getElementById('req-letra');
        const reqNum   = document.getElementById('req-num');

        inputPass.addEventListener('input', function () {
            const v = this.value;
            reqLen.classList.toggle('ok',   v.length >= 8);
            reqLetra.classList.toggle('ok', /[A-Za-z]/.test(v));
            reqNum.classList.toggle('ok',   /[0-9]/.test(v));
        });

        function showError(msg) {
            errorBox.textContent    = msg;
            errorBox.style.display  = 'block';
            successBox.style.display = 'none';
        }
        function showSuccess(msg) {
            successBox.textContent  = msg;
            successBox.style.display = 'block';
            errorBox.style.display  = 'none';
        }
        function clearMessages() {
            errorBox.style.display   = 'none';
            successBox.style.display = 'none';
            [inputUser, inputPass, inputConf].forEach(el => el.classList.remove('input-error'));
        }

        function validateFrontend() {
            clearMessages();
            const user = inputUser.value.trim();
            const pass = inputPass.value;
            const conf = inputConf.value;

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
            if (!/^[a-zA-Z0-9._\-]+$/.test(user)) {
                inputUser.classList.add('input-error');
                showError('El usuario solo puede contener letras, números, puntos, guiones y guiones bajos.');
                return false;
            }
            if (pass.length < 8) {
                inputPass.classList.add('input-error');
                showError('La contraseña debe tener al menos 8 caracteres.');
                return false;
            }
            if (!/[A-Za-z]/.test(pass) || !/[0-9]/.test(pass)) {
                inputPass.classList.add('input-error');
                showError('La contraseña debe contener al menos una letra y un número.');
                return false;
            }
            if (pass !== conf) {
                inputConf.classList.add('input-error');
                showError('Las contraseñas no coinciden.');
                return false;
            }
            return true;
        }

        btnReg.addEventListener('click', async function () {
            if (!validateFrontend()) return;

            btnReg.disabled    = true;
            btnReg.textContent = 'Creando cuenta…';

            const body = new URLSearchParams({
                usuario:           inputUser.value.trim(),
                contrasena:        inputPass.value,
                contrasenaConfirm: inputConf.value,
            });

            try {
                const res  = await fetch('ajaxRegistro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString(),
                });
                const data = await res.json();

                if (data.success) {
                    showSuccess(data.mensaje ?? '¡Cuenta creada con éxito!');
                    setTimeout(() => { window.location.href = data.redirect ?? 'index.php'; }, 1200);
                } else {
                    showError(data.error ?? 'Error al registrar la cuenta.');
                    btnReg.disabled    = false;
                    btnReg.textContent = 'Registrar';
                }
            } catch (err) {
                showError('No se pudo conectar con el servidor. Inténtalo de nuevo.');
                btnReg.disabled    = false;
                btnReg.textContent = 'Registrar';
            }
        });

        [inputUser, inputPass, inputConf].forEach(el =>
            el.addEventListener('input', clearMessages)
        );

        [inputUser, inputPass, inputConf].forEach(el =>
            el.addEventListener('keydown', e => { if (e.key === 'Enter') btnReg.click(); })
        );
    })();
    </script>
</body>

</html>
