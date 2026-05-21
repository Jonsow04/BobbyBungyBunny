<?php
/**
 * ajaxRegistro.php
 * Maneja el registro de nuevos usuarios via AJAX.
 * Valida los datos, comprueba duplicados y guarda en la BD.
 */

session_start();
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit();
}

$response = ['success' => false, 'error' => ''];

// ── 1. Recoger y sanear entrada ──────────────────────────────────────────────
$usuario          = trim($_POST['usuario']           ?? '');
$contrasena       = $_POST['contrasena']              ?? '';
$contrasenaConfirm = $_POST['contrasenaConfirm']      ?? '';

// ── 2. Validaciones de formato ───────────────────────────────────────────────

// 2a. Campos vacíos
if ($usuario === '' || $contrasena === '' || $contrasenaConfirm === '') {
    $response['error'] = 'Todos los campos son obligatorios.';
    echo json_encode($response);
    exit();
}

// 2b. Longitud del usuario
if (strlen($usuario) < 3) {
    $response['error'] = 'El usuario debe tener al menos 3 caracteres.';
    echo json_encode($response);
    exit();
}
if (strlen($usuario) > 50) {
    $response['error'] = 'El usuario no puede superar los 50 caracteres.';
    echo json_encode($response);
    exit();
}

// 2c. Caracteres permitidos en el usuario
if (!preg_match('/^[a-zA-Z0-9._\-]+$/', $usuario)) {
    $response['error'] = 'El usuario solo puede contener letras, números, puntos, guiones y guiones bajos.';
    echo json_encode($response);
    exit();
}

// 2d. Longitud mínima de contraseña
if (strlen($contrasena) < 8) {
    $response['error'] = 'La contraseña debe tener al menos 8 caracteres.';
    echo json_encode($response);
    exit();
}

// 2e. Complejidad: al menos una letra y un número
if (!preg_match('/[A-Za-z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena)) {
    $response['error'] = 'La contraseña debe contener al menos una letra y un número.';
    echo json_encode($response);
    exit();
}

// 2f. Confirmación de contraseña
if ($contrasena !== $contrasenaConfirm) {
    $response['error'] = 'Las contraseñas no coinciden.';
    echo json_encode($response);
    exit();
}

// ── 3. Consulta a la base de datos ───────────────────────────────────────────
try {
    $pdo = getConnection();

    // 3a. Comprobar si el usuario ya existe (case-insensitive)
    $stmt = $pdo->prepare(
        'SELECT idUsuario FROM usuarios WHERE LOWER(usuario) = LOWER(:usuario) LIMIT 1'
    );
    $stmt->execute([':usuario' => $usuario]);

    if ($stmt->fetch()) {
        $response['error'] = 'Ese nombre de usuario ya está en uso. Elige otro.';
        echo json_encode($response);
        exit();
    }

    // 3b. Hashear contraseña con bcrypt
    $hash = password_hash($contrasena, PASSWORD_BCRYPT);

    // 3c. Insertar nuevo usuario
    $insert = $pdo->prepare(
        'INSERT INTO usuarios (usuario, contrasena, activo, fecha_registro)
         VALUES (:usuario, :contrasena, 1, NOW())'
    );
    $insert->execute([
        ':usuario'    => $usuario,
        ':contrasena' => $hash,
    ]);

    $nuevoId = (int) $pdo->lastInsertId();

} catch (PDOException $e) {
    error_log('Registro DB error: ' . $e->getMessage());
    $response['error'] = 'Error interno del servidor. Inténtalo más tarde.';
    echo json_encode($response);
    exit();
}

// ── 4. Iniciar sesión automáticamente tras el registro ───────────────────────
session_regenerate_id(true);
$_SESSION['usuario_id']     = $nuevoId;
$_SESSION['usuario_nombre'] = $usuario;

$response = [
    'success'  => true,
    'redirect' => 'index.php',
    'mensaje'  => '¡Cuenta creada! Bienvenido, ' . htmlspecialchars($usuario) . '.',
];

echo json_encode($response);
