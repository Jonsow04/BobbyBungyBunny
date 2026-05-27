// ========== loginScripts.js ==========

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== MOSTRAR/OCULTAR CONTRASEÑA ==========
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('contrasena');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const isVisible = passwordField.type === 'text';
            passwordField.type = isVisible ? 'password' : 'text';
            
            // Cambiar el icono del ojo
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        });
    }
    
    // ========== SELECTOR DE TIPO DE ACCESO ==========
    window.seleccionarTipo = function(tipo) {
        const tipoAccesoInput = document.getElementById('tipo_acceso');
        if (tipoAccesoInput) {
            tipoAccesoInput.value = tipo;
        }
        
        // Actualizar clases active en los selectores
        const opciones = document.querySelectorAll('.selector-opcion');
        opciones.forEach(opt => {
            opt.classList.remove('active');
            if (opt.dataset.tipo === tipo) {
                opt.classList.add('active');
            }
        });
    };
    
    // ========== VALIDACIÓN DEL FORMULARIO ANTES DE ENVIAR ==========
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const email = document.getElementById('email');
            const password = document.getElementById('contrasena');
            const emailMessage = document.getElementById('emailMessage');
            const passwordMessage = document.getElementById('passwordMessage');
            
            let isValid = true;
            
            // Limpiar mensajes previos
            if (emailMessage) emailMessage.textContent = '';
            if (passwordMessage) passwordMessage.textContent = '';
            
            // Validar email
            if (!email || !email.value.trim()) {
                if (emailMessage) {
                    emailMessage.textContent = 'El correo electrónico es obligatorio';
                    emailMessage.style.color = '#b33a3a';
                }
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                if (emailMessage) {
                    emailMessage.textContent = 'Formato de correo inválido';
                    emailMessage.style.color = '#b33a3a';
                }
                isValid = false;
            }
            
            // Validar contraseña
            if (!password || !password.value) {
                if (passwordMessage) {
                    passwordMessage.textContent = 'La contraseña es obligatoria';
                    passwordMessage.style.color = '#b33a3a';
                }
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
    
    // ========== VALIDACIÓN EN TIEMPO REAL ==========
    const emailInput = document.getElementById('email');
    const emailMessage = document.getElementById('emailMessage');
    
    if (emailInput && emailMessage) {
        emailInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                emailMessage.textContent = '';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim())) {
                emailMessage.textContent = 'Formato de correo inválido';
                emailMessage.style.color = '#b33a3a';
            } else {
                emailMessage.textContent = '';
            }
        });
    }
    
    const passwordInput = document.getElementById('contrasena');
    const passwordMessageSpan = document.getElementById('passwordMessage');
    
    if (passwordInput && passwordMessageSpan) {
        passwordInput.addEventListener('input', function() {
            if (this.value === '') {
                passwordMessageSpan.textContent = '';
            } else {
                passwordMessageSpan.textContent = '';
            }
        });
    }
});