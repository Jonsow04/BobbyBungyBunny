// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // Referencias a los campos de usuario
    const password = document.getElementById('contrasena');
    const confirmPassword = document.getElementById('contrasenaconfirm');
    const emailInput = document.getElementById('email');
    const celularInput = document.getElementById('celular');
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    const nombreInput = document.getElementById('nombre');
    const apellidoPaternoInput = document.getElementById('apellido_paterno');
    const apellidoMaternoInput = document.getElementById('apellido_materno');
    
    // Referencias a los campos de dirección
    const calleInput = document.getElementById('calle');
    const numCasaInput = document.getElementById('num_casa');
    const coloniaInput = document.getElementById('colonia');
    const cpInput = document.getElementById('cp');
    const ciudadInput = document.getElementById('ciudad');
    const estadoInput = document.getElementById('estado');
    
    const form = document.querySelector('form');
    
    // Función para crear contenedor de mensaje debajo de un campo
    function createMessageContainer(inputElement, messageId) {
        if (!inputElement) return null;
        
        const campoDiv = inputElement.closest('.campo');
        if (!campoDiv) return null;
        
        let messageContainer = document.getElementById(messageId);
        if (messageContainer) return messageContainer;
        
        messageContainer = document.createElement('small');
        messageContainer.id = messageId;
        messageContainer.style.display = 'block';
        messageContainer.style.marginTop = '5px';
        messageContainer.style.fontSize = '12px';
        messageContainer.style.lineHeight = '1.4';
        
        const inputWrap = campoDiv.querySelector('.input-wrap');
        if (inputWrap) {
            inputWrap.insertAdjacentElement('afterend', messageContainer);
        } else {
            campoDiv.appendChild(messageContainer);
        }
        
        return messageContainer;
    }
    
    // ========== VALIDACIONES DE USUARIO ==========
    
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
        
        if (nombre.length < 2 || nombre.length > 45) {
            if (messageContainer) {
                messageContainer.textContent = 'El nombre debe tener entre 2 y 45 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/.test(nombre)) {
            if (messageContainer) {
                messageContainer.textContent = 'El nombre solo puede contener letras';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
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
        
        if (apellido.length < 2 || apellido.length > 45) {
            if (messageContainer) {
                messageContainer.textContent = 'El apellido paterno debe tener entre 2 y 45 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/.test(apellido)) {
            if (messageContainer) {
                messageContainer.textContent = 'El apellido paterno solo puede contener letras';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateApellidoMaterno() {
        if (!apellidoMaternoInput) return true;
        
        const apellido = apellidoMaternoInput.value.trim();
        const messageContainer = createMessageContainer(apellidoMaternoInput, 'apellidoMaternoMessage');
        
        if (apellido !== '' && apellido.length > 45) {
            if (messageContainer) {
                messageContainer.textContent = 'El apellido materno no debe exceder 45 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (apellido !== '' && !/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/.test(apellido)) {
            if (messageContainer) {
                messageContainer.textContent = 'El apellido materno solo puede contener letras';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateEmail() {
        if (!emailInput) return true;
        
        const email = emailInput.value;
        const messageContainer = createMessageContainer(emailInput, 'emailMessage');
        
        if (email === '') {
            if (messageContainer) messageContainer.textContent = '';
            return false;
        }
        
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            if (messageContainer) {
                messageContainer.textContent = 'Formato de correo inválido';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        const allowedDomains = ['gmail.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
        const emailParts = email.split('@');
        
        if (emailParts.length !== 2) {
            if (messageContainer) {
                messageContainer.textContent = 'Formato de correo inválido';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        const domain = emailParts[1].toLowerCase();
        
        if (!allowedDomains.includes(domain)) {
            if (messageContainer) {
                messageContainer.textContent = 'Solo se permiten correos de: Gmail, Hotmail, Outlook o iCloud';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateCelular() {
        if (!celularInput) return true;
        
        let celular = celularInput.value;
        const messageContainer = createMessageContainer(celularInput, 'celularMessage');
        
        if (celular === '') {
            if (messageContainer) messageContainer.textContent = '';
            return false;
        }
        
        const cleanNumber = celular.replace(/[\s\-\(\)\+]/g, '');
        
        if (!/^\d+$/.test(cleanNumber)) {
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
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
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
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
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
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== VALIDACIONES DE DIRECCIÓN ==========
    
    function validateCalle() {
        if (!calleInput) return true;
        
        const calle = calleInput.value.trim();
        const messageContainer = createMessageContainer(calleInput, 'calleMessage');
        
        if (calle === '') {
            if (messageContainer) {
                messageContainer.textContent = 'La calle es obligatoria';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (calle.length > 50) {
            if (messageContainer) {
                messageContainer.textContent = 'La calle no debe exceder 50 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateNumCasa() {
        if (!numCasaInput) return true;
        
        let numCasa = numCasaInput.value.trim();
        const messageContainer = createMessageContainer(numCasaInput, 'numCasaMessage');
        
        if (numCasa === '') {
            if (messageContainer) {
                messageContainer.textContent = 'El número de casa es obligatorio';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^\d+$/.test(numCasa)) {
            if (messageContainer) {
                messageContainer.textContent = 'El número de casa solo debe contener números';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (numCasa.length > 5) {
            if (messageContainer) {
                messageContainer.textContent = 'El número de casa no debe exceder 5 dígitos';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateColonia() {
        if (!coloniaInput) return true;
        
        const colonia = coloniaInput.value.trim();
        const messageContainer = createMessageContainer(coloniaInput, 'coloniaMessage');
        
        if (colonia === '') {
            if (messageContainer) {
                messageContainer.textContent = 'La colonia es obligatoria';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (colonia.length > 50) {
            if (messageContainer) {
                messageContainer.textContent = 'La colonia no debe exceder 50 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/.test(colonia)) {
            if (messageContainer) {
                messageContainer.textContent = 'La colonia solo puede contener letras (sin números)';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateCP() {
        if (!cpInput) return true;
        
        const cp = cpInput.value.trim();
        const messageContainer = createMessageContainer(cpInput, 'cpMessage');
        
        if (cp === '') {
            if (messageContainer) {
                messageContainer.textContent = 'El código postal es obligatorio';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^\d{5}$/.test(cp)) {
            if (messageContainer) {
                messageContainer.textContent = 'El código postal debe tener 5 dígitos';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateCiudad() {
        if (!ciudadInput) return true;
        
        const ciudad = ciudadInput.value.trim();
        const messageContainer = createMessageContainer(ciudadInput, 'ciudadMessage');
        
        if (ciudad === '') {
            if (messageContainer) {
                messageContainer.textContent = 'La ciudad es obligatoria';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (ciudad.length > 30) {
            if (messageContainer) {
                messageContainer.textContent = 'La ciudad no debe exceder 30 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/.test(ciudad)) {
            if (messageContainer) {
                messageContainer.textContent = 'La ciudad solo puede contener letras (sin números)';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    function validateEstado() {
        if (!estadoInput) return true;
        
        const estado = estadoInput.value.trim();
        const messageContainer = createMessageContainer(estadoInput, 'estadoMessage');
        
        if (estado === '') {
            if (messageContainer) {
                messageContainer.textContent = 'El estado es obligatorio';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (estado.length > 30) {
            if (messageContainer) {
                messageContainer.textContent = 'El estado no debe exceder 30 caracteres';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (!/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/.test(estado)) {
            if (messageContainer) {
                messageContainer.textContent = 'El estado solo puede contener letras (sin números)';
                messageContainer.style.color = 'red';
            }
            return false;
        }
        
        if (messageContainer) {
            messageContainer.textContent = '';
        }
        return true;
    }
    
    // ========== MOSTRAR/OCULTAR AMBAS CONTRASEÑAS ==========
    function createPasswordToggleButton() {
        if (!password) return;
        
        if (document.getElementById('togglePasswordBtn')) return;
        
        const toggleBtn = document.createElement('button');
        toggleBtn.id = 'togglePasswordBtn';
        toggleBtn.type = 'button';
        toggleBtn.className = 'toggle-pass';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        toggleBtn.title = 'Mostrar contraseñas';
        
        let isVisible = false;
        toggleBtn.addEventListener('click', function() {
            isVisible = !isVisible;
            
            if (isVisible) {
                password.type = 'text';
                if (confirmPassword) confirmPassword.type = 'text';
                toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
                toggleBtn.title = 'Ocultar contraseñas';
            } else {
                password.type = 'password';
                if (confirmPassword) confirmPassword.type = 'password';
                toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
                toggleBtn.title = 'Mostrar contraseñas';
            }
        });
        
        const inputWrap = password.closest('.input-wrap');
        if (inputWrap) {
            inputWrap.appendChild(toggleBtn);
        }
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
            errorMessage += 'Ingresa tu nombre (2-45 caracteres, solo letras)\n';
        }
        
        if (!validateApellidoPaterno()) {
            isValid = false;
            errorMessage += 'Ingresa tu apellido paterno (2-45 caracteres, solo letras)\n';
        }
        
        if (!validateApellidoMaterno()) {
            isValid = false;
            errorMessage += 'El apellido materno no debe exceder 45 caracteres y solo letras\n';
        }
        
        if (!validateEmail()) {
            isValid = false;
            errorMessage += 'Ingresa un correo válido (Gmail, Hotmail, Outlook o iCloud)\n';
        }
        
        if (!validateCelular()) {
            isValid = false;
            errorMessage += 'Ingresa un número de celular válido (10 dígitos)\n';
        }
        
        if (!validateFechaNacimiento()) {
            isValid = false;
            errorMessage += 'Debes tener al menos 18 años para registrarte\n';
        }
        
        if (!validatePasswordRequirements()) {
            isValid = false;
            errorMessage += 'La contraseña debe tener mínimo 8 caracteres alfanuméricos\n';
        }
        
        if (!validatePasswordMatch()) {
            isValid = false;
            errorMessage += 'Las contraseñas no coinciden\n';
        }
        
        if (!validateCalle()) {
            isValid = false;
            errorMessage += 'La calle es obligatoria\n';
        }
        
        if (!validateNumCasa()) {
            isValid = false;
            errorMessage += 'El número de casa es obligatorio y solo números\n';
        }
        
        if (!validateColonia()) {
            isValid = false;
            errorMessage += 'La colonia es obligatoria y solo letras\n';
        }
        
        if (!validateCP()) {
            isValid = false;
            errorMessage += 'El código postal debe tener 5 dígitos\n';
        }
        
        if (!validateCiudad()) {
            isValid = false;
            errorMessage += 'La ciudad es obligatoria y solo letras\n';
        }
        
        if (!validateEstado()) {
            isValid = false;
            errorMessage += 'El estado es obligatorio y solo letras\n';
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
    
    if (apellidoMaternoInput) {
        apellidoMaternoInput.addEventListener('keyup', validateApellidoMaterno);
        apellidoMaternoInput.addEventListener('blur', validateApellidoMaterno);
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
    
    if (calleInput) {
        calleInput.addEventListener('keyup', validateCalle);
        calleInput.addEventListener('blur', validateCalle);
    }
    
    if (numCasaInput) {
        numCasaInput.addEventListener('keyup', validateNumCasa);
        numCasaInput.addEventListener('blur', validateNumCasa);
        
        numCasaInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    }
    
    if (coloniaInput) {
        coloniaInput.addEventListener('keyup', validateColonia);
        coloniaInput.addEventListener('blur', validateColonia);
    }
    
    if (cpInput) {
        cpInput.addEventListener('keyup', validateCP);
        cpInput.addEventListener('blur', validateCP);
        
        cpInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 5) value = value.slice(0, 5);
            this.value = value;
        });
    }
    
    if (ciudadInput) {
        ciudadInput.addEventListener('keyup', validateCiudad);
        ciudadInput.addEventListener('blur', validateCiudad);
    }
    
    if (estadoInput) {
        estadoInput.addEventListener('keyup', validateEstado);
        estadoInput.addEventListener('blur', validateEstado);
    }
    
    if (form) {
        form.addEventListener('submit', validateForm);
    }
    
    // ========== INICIALIZAR ==========
    createPasswordToggleButton();
    createStrengthIndicator();
});