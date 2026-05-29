// ========== loginScripts.js ==========

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== MOSTRAR/OCULTAR CONTRASEÑA ==========
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('contrasena');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const isVisible = passwordField.type === 'text';
            passwordField.type = isVisible ? 'password' : 'text';
            
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        });
    }
    
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
    const emailMessageSpan = document.getElementById('emailMessage');
    
    if (emailInput && emailMessageSpan) {
        emailInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                emailMessageSpan.textContent = '';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim())) {
                emailMessageSpan.textContent = 'Formato de correo inválido';
                emailMessageSpan.style.color = '#b33a3a';
            } else {
                emailMessageSpan.textContent = '';
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