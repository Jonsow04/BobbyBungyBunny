<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/helpers/sanitize.php';

// === DEPURACIÓN ===
error_log("=== PROCESAR REGISTRO ===");
error_log("POST: " . print_r($_POST, true));

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
        return true;
    }
    if (strlen($apellido) > 45) {
        return "El apellido materno no debe exceder 45 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $apellido)) {
        return "El apellido materno solo puede contener letras";
    }
    return true;
}

function validarEmail($email, $pdo) {
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
    $stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return "email_duplicado"; // Código especial
    }
    
    return true;
}

function validarCelular($celular) {
    $celular = trim($celular);
    if (empty($celular)) {
        return "El número de celular es obligatorio";
    }
    
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

// ========== VALIDACIONES DE DIRECCIÓN ==========

function validarCalle($calle) {
    $calle = trim($calle);
    if (empty($calle)) {
        return "La calle es obligatoria";
    }
    if (strlen($calle) > 50) {
        return "La calle no debe exceder 50 caracteres";
    }
    return true;
}

function validarNumCasa($numCasa) {
    $numCasa = trim($numCasa);
    if (empty($numCasa)) {
        return "El número de casa es obligatorio";
    }
    if (!preg_match('/^\d+$/', $numCasa)) {
        return "El número de casa solo debe contener números";
    }
    if (strlen($numCasa) > 5) {
        return "El número de casa no debe exceder 5 dígitos";
    }
    return true;
}

function validarColonia($colonia) {
    $colonia = trim($colonia);
    if (empty($colonia)) {
        return "La colonia es obligatoria";
    }
    if (strlen($colonia) > 50) {
        return "La colonia no debe exceder 50 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $colonia)) {
        return "La colonia solo puede contener letras";
    }
    return true;
}

function validarCP($cp) {
    $cp = trim($cp);
    if (empty($cp)) {
        return "El código postal es obligatorio";
    }
    if (!preg_match('/^\d{5}$/', $cp)) {
        return "El código postal debe tener 5 dígitos";
    }
    return true;
}

function validarCiudad($ciudad) {
    $ciudad = trim($ciudad);
    if (empty($ciudad)) {
        return "La ciudad es obligatoria";
    }
    if (strlen($ciudad) > 30) {
        return "La ciudad no debe exceder 30 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $ciudad)) {
        return "La ciudad solo puede contener letras";
    }
    return true;
}

function validarEstado($estado) {
    $estado = trim($estado);
    if (empty($estado)) {
        return "El estado es obligatorio";
    }
    if (strlen($estado) > 30) {
        return "El estado no debe exceder 30 caracteres";
    }
    if (!preg_match("/^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/", $estado)) {
        return "El estado solo puede contener letras";
    }
    return true;
}

// ========== PROCESAR FORMULARIO ==========

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar todos los campos
    $nombre = SanitizeHelper::sanitizarNombre($_POST['nombre'] ?? '');
    $apPat = SanitizeHelper::sanitizarNombre($_POST['apellido_paterno'] ?? '');
    $apMat = SanitizeHelper::sanitizarNombre($_POST['apellido_materno'] ?? '');
    $email = SanitizeHelper::sanitizarEmail($_POST['email'] ?? '');
    $celular = SanitizeHelper::sanitizarTelefono($_POST['celular'] ?? '');
    $fechaNac = $_POST['fecha_nacimiento'] ?? '';
    $password = $_POST['contrasena'] ?? '';
    $confirmPassword = $_POST['contrasenaconfirm'] ?? '';

    $tipoCuenta = $_POST['tipo_cuenta'] ?? 'cliente';
    $codigoAdmin = $_POST['codigo_admin'] ?? '';
    
    // Validar código de administrador
    $validacionCodigo = validarCodigoAdmin($codigoAdmin, $tipoCuenta);
    if ($validacionCodigo !== true) {
        $_SESSION['errores_registro'] = ['codigo_admin' => $validacionCodigo];
        $_SESSION['datos_registro'] = $_POST;
        header('Location: registro.php');
        exit();
    }
    
    // Determinar el idTipoUsuario basado en el tipo de cuenta
    if ($tipoCuenta === 'admin') {
        $idTipoUsuario = 1; // Administrador
    } else {
        $idTipoUsuario = 3; // Cliente
    }

    // Validar fecha
    if (!SanitizeHelper::validarFecha($fechaNac)) {
        $errores['fecha_nacimiento'] = "Formato de fecha inválido";
    }
    
    // Validar edad mínima
    if (!SanitizeHelper::validarEdadMinima($fechaNac, 18)) {
        $errores['fecha_nacimiento'] = "Debes tener al menos 18 años";
    }
    
    try {
        $pdo = getConnection();
    } catch (Exception $e) {
        error_log("Error de conexión: " . $e->getMessage());
        $_SESSION['errores_registro'] = ['general' => 'Error de conexión a la base de datos'];
        header('Location: registro.php');
        exit();
    }
    
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
    $calle = $_POST['calle'] ?? '';
    $numCasa = $_POST['num_casa'] ?? '';
    $colonia = $_POST['colonia'] ?? '';
    $cp = $_POST['cp'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $estado = $_POST['estado'] ?? '';
    
    $errores = [];
    
    // Validaciones de usuario
    $validacionNombre = validarNombre($nombre);
    if ($validacionNombre !== true) $errores['nombre'] = $validacionNombre;
    
    $validacionApPat = validarApellidoPaterno($apPat);
    if ($validacionApPat !== true) $errores['apellido_paterno'] = $validacionApPat;
    
    $validacionApMat = validarApellidoMaterno($apMat);
    if ($validacionApMat !== true) $errores['apellido_materno'] = $validacionApMat;
    
    $validacionEmail = validarEmail($email, $pdo);
    if ($validacionEmail !== true) $errores['email'] = $validacionEmail;
    
    $validacionCelular = validarCelular($celular);
    if ($validacionCelular !== true) $errores['celular'] = $validacionCelular;
    
    $validacionFecha = validarFechaNacimiento($fechaNac);
    if ($validacionFecha !== true) $errores['fecha_nacimiento'] = $validacionFecha;
    
    $validacionPassword = validarContrasena($password, $confirmPassword);
    if ($validacionPassword !== true) $errores['contrasena'] = $validacionPassword;
    
    // Validaciones de dirección
    $validacionCalle = validarCalle($calle);
    if ($validacionCalle !== true) $errores['calle'] = $validacionCalle;
    
    $validacionNumCasa = validarNumCasa($numCasa);
    if ($validacionNumCasa !== true) $errores['num_casa'] = $validacionNumCasa;
    
    $validacionColonia = validarColonia($colonia);
    if ($validacionColonia !== true) $errores['colonia'] = $validacionColonia;
    
    $validacionCP = validarCP($cp);
    if ($validacionCP !== true) $errores['cp'] = $validacionCP;
    
    $validacionCiudad = validarCiudad($ciudad);
    if ($validacionCiudad !== true) $errores['ciudad'] = $validacionCiudad;
    
    $validacionEstado = validarEstado($estado);
    if ($validacionEstado !== true) $errores['estado'] = $validacionEstado;
    
    // Si hay errores, guardar en sesión y redirigir
    if (!empty($errores)) {
        error_log("ERRORES ENCONTRADOS: " . print_r($errores, true));
        $_SESSION['errores_registro'] = $errores;
        $_SESSION['datos_registro'] = $_POST;
        header('Location: registro.php');
        exit();
    }
    
    // ========== INICIAR TRANSACCIÓN CON PDO ==========
    try {
        $pdo->beginTransaction();
        
        // 1. Insertar dirección
        $stmt = $pdo->prepare("INSERT INTO direccion (calle, numCasa, colonia, cp, ciudad, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$calle, $numCasa, $colonia, $cp, $ciudad, $estado]);
        $idDireccion = $pdo->lastInsertId();
        error_log("Dirección insertada con ID: " . $idDireccion);
        
        // 2. Insertar usuario
        $idTipoUsuario = 3;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $apMatFinal = empty($apMat) ? null : $apMat;
        
        $stmt = $pdo->prepare("INSERT INTO usuario (idTipoUsuario, idDireccion, nombre, apPat, apMat, email, password_hash, celular, fechaNac) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$idTipoUsuario, $idDireccion, $nombre, $apPat, $apMatFinal, $email, $password_hash, $celular, $fechaNac]);
        $idUsuario = $pdo->lastInsertId();
        error_log("Usuario insertado con ID: " . $idUsuario);
        
        // 3. Crear carrito
        $stmt = $pdo->prepare("INSERT INTO carrito (idUsuario) VALUES (?)");
        $stmt->execute([$idUsuario]);
        error_log("Carrito creado para usuario ID: " . $idUsuario);
        
        // Confirmar transacción
        $pdo->commit();
        
        $_SESSION['registro_exitoso'] = true;
        $_SESSION['usuario_nombre'] = $nombre;
        
        error_log("REGISTRO EXITOSO - Redirigiendo a login.php");
        header('Location: login.php?registro=exitoso');
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("ERROR EN TRANSACCIÓN: " . $e->getMessage());
        
        $_SESSION['errores_registro'] = ['general' => 'Ocurrió un error al registrar: ' . $e->getMessage()];
        $_SESSION['datos_registro'] = $_POST;
        header('Location: registro.php');
        exit();
    }
    
} else {
    header('Location: registro.php');
    exit();
}
?>