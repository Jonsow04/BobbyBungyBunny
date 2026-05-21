<?php
/**
 * ajaxLogin.php
 * Maneja el inicio de sesión via AJAX.
 * Valida credenciales contra la base de datos y devuelve JSON.
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
$usuario    = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

// ── 2. Validaciones básicas de formato ───────────────────────────────────────
if ($usuario === '' || $contrasena === '') {
    $response['error'] = 'El usuario y la contraseña son obligatorios.';
    echo json_encode($response);
    exit();
}

if (strlen($usuario) < 3 || strlen($usuario) > 50) {
    $response['error'] = 'El nombre de usuario debe tener entre 3 y 50 caracteres.';
    echo json_encode($response);
    exit();
}

if (strlen($contrasena) < 6) {
    $response['error'] = 'La contraseña debe tener al menos 6 caracteres.';
    echo json_encode($response);
    exit();
}

// Caracteres permitidos en el usuario (alfanumérico + guion + punto + guion_bajo)
if (!preg_match('/^[a-zA-Z0-9._\-]+$/', $usuario)) {
    $response['error'] = 'El usuario contiene caracteres no válidos.';
    echo json_encode($response);
    exit();
}

// ── 3. Consulta a la base de datos ───────────────────────────────────────────
try {
    $pdo = getConnection();

    $stmt = $pdo->prepare(
        'SELECT idUsuario, usuario, contrasena, activo
         FROM usuarios
         WHERE usuario = :usuario
         LIMIT 1'
    );
    $stmt->execute([':usuario' => $usuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // No exponer detalles del error al cliente
    error_log('Login DB error: ' . $e->getMessage());
    $response['error'] = 'Error interno del servidor. Inténtalo más tarde.';
    echo json_encode($response);
    exit();
}

// ── 4. Verificar existencia y contraseña ─────────────────────────────────────
/*
 * IMPORTANTE: se asume que las contraseñas se almacenaron con password_hash().
 * Si tu proyecto todavía guarda en MD5/SHA1, cambia password_verify() por
 * la función correspondiente y migra cuanto antes a password_hash().
 */
if (!$row || !password_verify($contrasena, $row['contrasena'])) {
    // Mensaje genérico para no revelar si el usuario existe o no
    $response['error'] = 'Usuario o contraseña incorrectos.';
    echo json_encode($response);
    exit();
}

// ── 5. Comprobar que la cuenta está activa ───────────────────────────────────
if (isset($row['activo']) && $row['activo'] == 0) {
    $response['error'] = 'Tu cuenta está desactivada. Contacta con soporte.';
    echo json_encode($response);
    exit();
}

// ── 6. Regenerar sesión (previene session fixation) ──────────────────────────
session_regenerate_id(true);

$_SESSION['usuario_id']     = $row['idUsuario'];
$_SESSION['usuario_nombre'] = $row['usuario'];

$response = [
    'success'  => true,
    'redirect' => 'index.php',
    'mensaje'  => '¡Bienvenido, ' . htmlspecialchars($row['usuario']) . '!',
];

echo json_encode($response);
