// ---- loginScripts.js ----

// Mostrar / ocultar contraseña
const togglePass = document.getElementById('togglePass');
const inputPass  = document.getElementById('contrasena');

if (togglePass && inputPass) {
    togglePass.addEventListener('click', () => {
        const visible = inputPass.type === 'text';
        inputPass.type = visible ? 'password' : 'text';
        togglePass.querySelector('i').className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
}

// Validación básica al hacer clic en "Iniciar sesión"
const btnLogin  = document.getElementById('btnLogin');
const inputMail = document.getElementById('correo');

if (btnLogin) {
    btnLogin.addEventListener('click', () => {
        const correo = inputMail ? inputMail.value.trim() : '';
        const pass   = inputPass ? inputPass.value.trim() : '';

        if (!correo || !pass) {
            alert('Por favor completa todos los campos.');
            return;
        }

        // Aquí iría la lógica de autenticación real
        console.log('Iniciando sesión con:', correo);
    });
}
