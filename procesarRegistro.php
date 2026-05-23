<?php
session_start();
require_once 'includes/config.php'; // Aquí va tu conexión a la BD

// ========== FUNCIONES DE VALIDACIÓN ==========

function limpiarDato($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

function validarNombre($nombre) {
    $nombre = trim($nombre);
    if (empty($nombre)) {
        return "El nombre es obligatorio";
    }
    if (strlen($nombre) < 2 || strlen($nombre) > 45) {
        return "El nombre debe tener entre 2 y 45 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $nombre)) {
        return "El nombre solo puede contener letras";
    }
    return true;
}

function validarApellidoPaterno($apellido) {
    $apellido = trim($apellido);
    if (empty($apellido)) {
        return "El apellido paterno es obligatorio";
    }
    if (strlen($apellido) < 2 || strlen($apellido) > 45) {
        return "El apellido paterno debe tener entre 2 y 45 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $apellido)) {
        return "El apellido paterno solo puede contener letras";
    }
    return true;
}

function validarApellidoMaterno($apellido) {
    if (empty(trim($apellido))) {
        return true; // Opcional, puede estar vacío
    }
    if (strlen($apellido) > 45) {
        return "El apellido materno no debe exceder 45 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $apellido)) {
        return "El apellido materno solo puede contener letras";
    }
    return true;
}

function validarEmail($email, $conn) {
    $email = trim($email);
    if (empty($email)) {
        return "El correo electrónico es obligatorio";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Formato de correo inválido";
    }
    
    // Dominios permitidos
    $allowedDomains = ['gmail.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
    $domain = substr(strrchr($email, "@"), 1);
    
    if (!in_array(strtolower($domain), $allowedDomains)) {
        return "Solo se permiten correos de: Gmail, Hotmail, Outlook o iCloud";
    }
    
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return "Este correo ya está registrado";
    }
    $stmt->close();
    
    return true;
}

function validarCelular($celular) {
    $celular = trim($celular);
    if (empty($celular)) {
        return "El número de celular es obligatorio";
    }
    
    // Eliminar caracteres no numéricos
    $cleanNumber = preg_replace('/[^0-9]/', '', $celular);
    
    if (!preg_match('/^[0-9]{10}$/', $cleanNumber)) {
        return "El celular debe tener exactamente 10 dígitos";
    }
    
    return true;
}

function validarFechaNacimiento($fecha) {
    if (empty($fecha)) {
        return "La fecha de nacimiento es obligatoria";
    }
    
    $fechaNacimiento = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fechaNacimiento) {
        return "Formato de fecha inválido";
    }
    
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y;
    
    if ($fechaNacimiento > $hoy) {
        return "La fecha de nacimiento no puede ser futura";
    }
    
    if ($edad < 18) {
        return "Debes tener al menos 18 años para registrarte";
    }
    
    if ($edad > 100) {
        return "Por favor, verifica tu fecha de nacimiento";
    }
    
    return true;
}

function validarContrasena($password, $confirmPassword) {
    if (empty($password)) {
        return "La contraseña es obligatoria";
    }
    
    if (strlen($password) < 8) {
        return "La contraseña debe tener al menos 8 caracteres";
    }
    
    if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
        return "La contraseña solo puede contener letras y números (caracteres alfanuméricos)";
    }
    
    if ($password !== $confirmPassword) {
        return "Las contraseñas no coinciden";
    }
    
    return true;
}

function validarDireccion($datos) {
    $errores = [];
    
    if (empty($datos['calle'])) {
        $errores['calle'] = "La calle es obligatoria";
    } elseif (strlen($datos['calle']) > 50) {
        $errores['calle'] = "La calle no debe exceder 50 caracteres";
    }
    
    if (empty($datos['numCasa'])) {
        $errores['numCasa'] = "El número de casa es obligatorio";
    } elseif (strlen($datos['numCasa']) > 10) {
        $errores['numCasa'] = "El número de casa no debe exceder 10 caracteres";
    }
    
    if (empty($datos['colonia'])) {
        $errores['colonia'] = "La colonia es obligatoria";
    } elseif (strlen($datos['colonia']) > 50) {
        $errores['colonia'] = "La colonia no debe exceder 50 caracteres";
    }
    
    if (empty($datos['cp'])) {
        $errores['cp'] = "El código postal es obligatorio";
    } elseif (!preg_match('/^[0-9]{5}$/', $datos['cp'])) {
        $errores['cp'] = "El código postal debe tener 5 dígitos";
    }
    
    if (empty($datos['ciudad'])) {
        $errores['ciudad'] = "La ciudad es obligatoria";
    } elseif (strlen($datos['ciudad']) > 30) {
        $errores['ciudad'] = "La ciudad no debe exceder 30 caracteres";
    }
    
    if (empty($datos['estado'])) {
        $errores['estado'] = "El estado es obligatorio";
    } elseif (strlen($datos['estado']) > 30) {
        $errores['estado'] = "El estado no debe exceder 30 caracteres";
    }
    
    return empty($errores) ? true : $errores;
}

// ========== PROCESAR FORMULARIO ==========

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recibir datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apPat = $_POST['apellido_paterno'] ?? '';
    $apMat = $_POST['apellido_materno'] ?? '';
    $email = $_POST['email'] ?? '';
    $celular = $_POST['celular'] ?? '';
    $fechaNac = $_POST['fecha_nacimiento'] ?? '';
    $password = $_POST['contrasena'] ?? '';
    $confirmPassword = $_POST['contrasenaconfirm'] ?? '';
    
    // Datos de dirección
    $direccion = [
        'calle' => $_POST['calle'] ?? '',
        'numCasa' => $_POST['num_casa'] ?? '',
        'colonia' => $_POST['colonia'] ?? '',
        'cp' => $_POST['cp'] ?? '',
        'ciudad' => $_POST['ciudad'] ?? '',
        'estado' => $_POST['estado'] ?? ''
    ];
    
    $errores = [];
    
    // Validaciones de usuario
    $validacionNombre = validarNombre($nombre);
    if ($validacionNombre !== true) $errores['nombre'] = $validacionNombre;
    
    $validacionApPat = validarApellidoPaterno($apPat);
    if ($validacionApPat !== true) $errores['apellido_paterno'] = $validacionApPat;
    
    $validacionApMat = validarApellidoMaterno($apMat);
    if ($validacionApMat !== true) $errores['apellido_materno'] = $validacionApMat;
    
    $validacionEmail = validarEmail($email, $conn);
    if ($validacionEmail !== true) $errores['email'] = $validacionEmail;
    
    $validacionCelular = validarCelular($celular);
    if ($validacionCelular !== true) $errores['celular'] = $validacionCelular;
    
    $validacionFecha = validarFechaNacimiento($fechaNac);
    if ($validacionFecha !== true) $errores['fecha_nacimiento'] = $validacionFecha;
    
    $validacionPassword = validarContrasena($password, $confirmPassword);
    if ($validacionPassword !== true) $errores['contrasena'] = $validacionPassword;
    
    // Validaciones de dirección
    $validacionDireccion = validarDireccion($direccion);
    if ($validacionDireccion !== true) {
        $errores = array_merge($errores, $validacionDireccion);
    }
    
    // Si hay errores, guardar en sesión y redirigir
    if (!empty($errores)) {
        $_SESSION['errores_registro'] = $errores;
        $_SESSION['datos_registro'] = $_POST;
        header('Location: registro.php');
        exit();
    }
    
    // ========== INICIAR TRANSACCIÓN ==========
    $conn->begin_transaction();
    
    try {
        // 1. Insertar dirección
        $stmt = $conn->prepare("INSERT INTO direccion (calle, numCasa, colonia, cp, ciudad, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", 
            $direccion['calle'], 
            $direccion['numCasa'], 
            $direccion['colonia'], 
            $direccion['cp'], 
            $direccion['ciudad'], 
            $direccion['estado']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al guardar la dirección: " . $stmt->error);
        }
        
        $idDireccion = $conn->insert_id;
        $stmt->close();
        
        // 2. Insertar usuario (idTipoUsuario = 3 para "Cliente")
        $idTipoUsuario = 3;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO usuario (idTipoUsuario, idDireccion, nombre, apPat, apMat, email, password_hash, celular, fechaNac) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Apellido materno puede ser NULL si viene vacío
        $apMatFinal = empty($apMat) ? null : $apMat;
        
        $stmt->bind_param("iisssssss", 
            $idTipoUsuario, 
            $idDireccion, 
            $nombre, 
            $apPat, 
            $apMatFinal, 
            $email, 
            $password_hash, 
            $celular, 
            $fechaNac
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al guardar el usuario: " . $stmt->error);
        }
        
        $idUsuario = $conn->insert_id;
        $stmt->close();
        
        // 3. Crear carrito para el nuevo usuario
        $stmt = $conn->prepare("INSERT INTO carrito (idUsuario) VALUES (?)");
        $stmt->bind_param("i", $idUsuario);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al crear el carrito: " . $stmt->error);
        }
        $stmt->close();
        
        // Confirmar transacción
        $conn->commit();
        
        // Registrar éxito en sesión
        $_SESSION['registro_exitoso'] = true;
        $_SESSION['usuario_nombre'] = $nombre;
        
        // Redirigir a login o a la página de éxito
        header('Location: login.php?registro=exitoso');
        exit();
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        
        $_SESSION['errores_registro'] = ['general' => $e->getMessage()];
        $_SESSION['datos_registro'] = $_POST;
        header('Location: registro.php');
        exit();
    }
    
} else {
    // Si no es POST, redirigir al formulario
    header('Location: registro.php');
    exit();
}
?>