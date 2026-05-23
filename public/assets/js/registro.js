// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // Referencias a los campos
    const password = document.getElementById('contrasena');
    const confirmPassword = document.getElementById('contrasenaconfirm');
    const emailInput = document.getElementById('email');
    const celularInput = document.getElementById('celular');
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    const nombreInput = document.getElementById('nombre');
    const apellidoPaternoInput = document.getElementById('apellido_paterno');
    const form = document.querySelector('form');
    
    // Función para crear contenedor de mensaje debajo de un campo
    function createMessageContainer(inputElement, messageId) {
        if (!inputElement) return null;
        
        // Buscar el div .campo que contiene el input
        const campoDiv = inputElement.closest('.campo');
        if (!campoDiv) return null;
        
        // Verificar si ya existe el mensaje
        let messageContainer = document.getElementById(messageId);
        if (messageContainer) return messageContainer;
        
        // Crear el contenedor del mensaje
        messageContainer = document.createElement('small');
        messageContainer.id = messageId;
        messageContainer.style.display = 'block';
        messageContainer.style.marginTop = '5px';
        messageContainer.style.fontSize = '12px';
        messageContainer.style.lineHeight = '1.4';
        
        // Insertar después del input-wrap (que contiene el input)
        const inputWrap = campoDiv.querySelector('.input-wrap');
        if (inputWrap) {
            inputWrap.insertAdjacentElement('afterend', messageContainer);
        } else {
            campoDiv.appendChild(messageContainer);
        }
        
        return messageContainer;
    }
    
    // ========== FUNCIÓN VALIDAR NOMBRE ==========
    function validateNombre() {
        if (!nombreInput) return true;
        
        const nombre = nombreInput.value.trim();
        const messageContainer = createMessageContainer(nombreInput, 'nombreMessage');
        
        if (nombre === '') {
            if (messageContainer) {
                messageContainer.textContent = 'Ingresa tu nombre';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        // Todo correcto
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== FUNCIÓN VALIDAR APELLIDO PATERNO ==========
    function validateApellidoPaterno() {
        if (!apellidoPaternoInput) return true;
        
        const apellido = apellidoPaternoInput.value.trim();
        const messageContainer = createMessageContainer(apellidoPaternoInput, 'apellidoMessage');
        
        if (apellido === '') {
            if (messageContainer) {
                messageContainer.textContent = 'Ingresa tu apellido paterno';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        // Todo correcto
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== FUNCIÓN VALIDAR CORREO ==========
    function validateEmail() {
        if (!emailInput) return true;
        
        const email = emailInput.value;
        const messageContainer = createMessageContainer(emailInput, 'emailMessage');
        
        if (email === '') {
            if (messageContainer) messageContainer.textContent = '';
            return false;
        }
        
        // Dominios permitidos
        const allowedDomains = ['gmail.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
        
        // Extraer el dominio del correo
        const emailParts = email.split('@');
        if (emailParts.length !== 2) {
            if (messageContainer) {
                messageContainer.textContent = 'Formato de correo inválido';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        const domain = emailParts[1].toLowerCase();
        
        // Verificar si el dominio está permitido
        if (!allowedDomains.includes(domain)) {
            if (messageContainer) {
                messageContainer.textContent = 'Solo se permiten correos de: Gmail, Hotmail, Outlook o iCloud';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        // Todo correcto
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== FUNCIÓN VALIDAR CELULAR ==========
    function validateCelular() {
        if (!celularInput) return true;
        
        const celular = celularInput.value;
        const messageContainer = createMessageContainer(celularInput, 'celularMessage');
        
        if (celular === '') {
            if (messageContainer) messageContainer.textContent = '';
            return false;
        }
        
        // Eliminar espacios, guiones, paréntesis, etc.
        const cleanNumber = celular.replace(/[\s\-\(\)\+]/g, '');
        
        // Verificar que sean solo números
        const onlyNumbers = /^\d+$/.test(cleanNumber);
        
        if (!onlyNumbers) {
            if (messageContainer) {
                messageContainer.textContent = 'El celular solo debe contener números';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (cleanNumber.length !== 10) {
            if (messageContainer) {
                messageContainer.textContent = 'El celular debe tener exactamente 10 dígitos';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        // Todo correcto
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== FUNCIÓN VALIDAR FECHA DE NACIMIENTO ==========
    function validateFechaNacimiento() {
        if (!fechaNacimientoInput) return true;
        
        const fecha = fechaNacimientoInput.value;
        const messageContainer = createMessageContainer(fechaNacimientoInput, 'fechaMessage');
        
        if (fecha === '') {
            if (messageContainer) {
                messageContainer.textContent = 'Selecciona tu fecha de nacimiento';
                messageContainer.style.color = 'orange';
            }
            return false;
        }
        
        const fechaNacimiento = new Date(fecha);
        const hoy = new Date();
        
        let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        const mes = hoy.getMonth() - fechaNacimiento.getMonth();
        
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
            edad--;
        }
        
        if (edad < 18) {
            if (messageContainer) {
                messageContainer.textContent = 'Debes tener al menos 18 años para registrarte';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (fechaNacimiento > hoy) {
            if (messageContainer) {
                messageContainer.textContent = 'La fecha de nacimiento no puede ser futura';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (edad > 100) {
            if (messageContainer) {
                messageContainer.textContent = 'Por favor, verifica tu fecha de nacimiento';
                messageContainer.style.color = 'orange';
            }
            return false;
        }
        
        // Todo correcto
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== FUNCIÓN VALIDAR REQUISITOS CONTRASEÑA ==========
    function validatePasswordRequirements() {
        if (!password) return true;
        
        const passValue = password.value;
        const messageContainer = createMessageContainer(password, 'requirementsMessage');
        
        if (passValue === '') {
            if (messageContainer) messageContainer.textContent = '';
            return false;
        }
        
        const hasMinLength = passValue.length >= 8;
        const isAlphanumeric = /^[a-zA-Z0-9]+$/.test(passValue);
        
        let isValid = true;
        let errorText = '';
        
        if (!hasMinLength) {
            errorText += 'Mínimo 8 caracteres\n';
            isValid = false;
        }
        
        if (!isAlphanumeric) {
            errorText += 'Solo caracteres alfanuméricos (letras y números)';
            isValid = false;
        }
        
        if (messageContainer) {
            if (!isValid) {
                messageContainer.textContent = 'Requisitos: ' + errorText;
                messageContainer.style.color = 'red';
                messageContainer.style.whiteSpace = 'pre-line';
            } else {
                messageContainer.textContent = '';
            }
        }
        
        return isValid;
    }
    
    // ========== FUNCIÓN VALIDAR COINCIDENCIA CONTRASEÑAS ==========
    function validatePasswordMatch() {
        if (!password || !confirmPassword) return true;
        
        const passValue = password.value;
        const confirmValue = confirmPassword.value;
        const messageContainer = createMessageContainer(confirmPassword, 'matchMessage');
        
        const isRequirementsValid = validatePasswordRequirements();
        
        if (passValue === '' && confirmValue === '') {
            if (messageContainer) messageContainer.textContent = '';
            return false;
        }
        
        if (passValue === '' || confirmValue === '') {
            if (messageContainer) {
                messageContainer.textContent = 'Completa ambos campos de contraseña';
                messageContainer.style.color = 'orange';
            }
            return false;
        }
        
        if (passValue !== confirmValue) {
            if (messageContainer) {
                messageContainer.textContent = 'Las contraseñas no coinciden';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!isRequirementsValid) {
            if (messageContainer) {
                messageContainer.textContent = 'La contraseña no cumple los requisitos';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        // Todo correcto
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== BOTÓN PARA MOSTRAR CONTRASEÑAS ==========
    function createPasswordToggleButton() {
        if (!password || !confirmPassword) return;
        
        // Buscar el div .campo de confirmar contraseña
        const confirmField = confirmPassword.closest('.campo');
        if (!confirmField) return;
        
        // Verificar si el botón ya existe
        if (document.getElementById('togglePasswordBtn')) return;
        
        // Crear el botón único
        const toggleBtn = document.createElement('button');
        toggleBtn.id = 'togglePasswordBtn';
        toggleBtn.type = 'button';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i> Mostrar contraseñas';
        toggleBtn.style.marginTop = '10px';
        toggleBtn.style.marginBottom = '10px';
        toggleBtn.style.background = '#f0f0f0';
        toggleBtn.style.border = '1px solid #ccc';
        toggleBtn.style.borderRadius = '5px';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.padding = '8px 12px';
        toggleBtn.style.fontSize = '14px';
        toggleBtn.style.width = '100%';
        
        let isVisible = false;
        
        toggleBtn.addEventListener('click', function() {
            isVisible = !isVisible;
            
            if (isVisible) {
                password.type = 'text';
                confirmPassword.type = 'text';
                toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar contraseñas';
            } else {
                password.type = 'password';
                confirmPassword.type = 'password';
                toggleBtn.innerHTML = '<i class="fas fa-eye"></i> Mostrar contraseñas';
            }
        });
        
        // Insertar el botón después del campo de confirmar contraseña
        confirmField.insertAdjacentElement('afterend', toggleBtn);
    }
    
    // ========== INDICADOR DE FORTALEZA DE CONTRASEÑA ==========
    function createStrengthIndicator() {
        if (!password) return;
        
        const messageContainer = createMessageContainer(password, 'strengthIndicator');
        if (!messageContainer) return;
        
        function checkPasswordStrength(passValue) {
            const strength = {
                length: passValue.length >= 8,
                uppercase: /[A-Z]/.test(passValue),
                lowercase: /[a-z]/.test(passValue),
                numbers: /[0-9]/.test(passValue),
                special: /[!@#$%^&*]/.test(passValue)
            };
            
            const score = Object.values(strength).filter(Boolean).length;
            
            if (passValue === '') return '';
            if (score <= 2) return 'Débil';
            if (score === 3) return 'Media';
            if (score >= 4) return 'Fuerte';
            return '';
        }
        
        password.addEventListener('keyup', function() {
            const strength = checkPasswordStrength(this.value);
            messageContainer.textContent = strength;
            
            if (strength.includes('Fuerte')) {
                messageContainer.style.color = 'green';
            } else if (strength.includes('Media')) {
                messageContainer.style.color = 'orange';
            } else if (strength.includes('Débil')) {
                messageContainer.style.color = 'red';
            } else {
                messageContainer.textContent = '';
            }
        });
    }
    
    // ========== VALIDAR FORMULARIO COMPLETO ==========
    function validateForm(event) {
        let isValid = true;
        let errorMessage = '';
        
        if (!validateNombre()) {
            isValid = false;
            errorMessage += '• Ingresa tu nombre\n';
        }
        
        if (!validateApellidoPaterno()) {
            isValid = false;
            errorMessage += '• Ingresa tu apellido paterno\n';
        }
        
        if (!validateEmail()) {
            isValid = false;
            errorMessage += '• Ingresa un correo válido (Gmail, Hotmail, Outlook o iCloud)\n';
        }
        
        if (!validateCelular()) {
            isValid = false;
            errorMessage += '• Ingresa un número de celular válido (10 dígitos)\n';
        }
        
        if (!validateFechaNacimiento()) {
            isValid = false;
            errorMessage += '• Debes tener al menos 18 años para registrarte\n';
        }
        
        if (!validatePasswordRequirements()) {
            isValid = false;
            errorMessage += '• La contraseña debe tener mínimo 8 caracteres alfanuméricos\n';
        }
        
        if (!validatePasswordMatch()) {
            isValid = false;
            errorMessage += '• Las contraseñas no coinciden\n';
        }
        
        if (!isValid) {
            event.preventDefault();
            alert('Por favor, corrige los siguientes errores:\n\n' + errorMessage);
            return false;
        }
        
        return true;
    }
    
    // ========== EVENT LISTENERS ==========
    
    if (nombreInput) {
        nombreInput.addEventListener('keyup', validateNombre);
        nombreInput.addEventListener('blur', validateNombre);
    }
    
    if (apellidoPaternoInput) {
        apellidoPaternoInput.addEventListener('keyup', validateApellidoPaterno);
        apellidoPaternoInput.addEventListener('blur', validateApellidoPaterno);
    }
    
    if (emailInput) {
        emailInput.addEventListener('keyup', validateEmail);
        emailInput.addEventListener('blur', validateEmail);
    }
    
    if (celularInput) {
        celularInput.addEventListener('keyup', validateCelular);
        celularInput.addEventListener('blur', validateCelular);
        
        celularInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            this.value = value;
        });
    }
    
    if (fechaNacimientoInput) {
        fechaNacimientoInput.addEventListener('change', validateFechaNacimiento);
        fechaNacimientoInput.addEventListener('blur', validateFechaNacimiento);
    }
    
    if (password) {
        password.addEventListener('keyup', function() {
            validatePasswordRequirements();
            validatePasswordMatch();
        });
        password.addEventListener('blur', function() {
            validatePasswordRequirements();
            validatePasswordMatch();
        });
    }
    
    if (confirmPassword) {
        confirmPassword.addEventListener('keyup', validatePasswordMatch);
        confirmPassword.addEventListener('blur', validatePasswordMatch);
    }
    
    if (form) {
        form.addEventListener('submit', validateForm);
    }
    
    // ========== INICIALIZAR ==========
    createPasswordToggleButton();
    createStrengthIndicator();
    
});