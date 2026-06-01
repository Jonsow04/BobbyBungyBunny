<?php
session_start();

// Verificar que el usuario sea administrador (solo admin puede eliminar)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/controllers/AdminController.php';

$pdo = getConnection();
$adminController = new AdminController($pdo);

$id = $_GET['id'] ?? 0;

// Validar que el ID sea un número válido
if ($id > 0) {
    $resultado = $adminController->eliminarProducto($id);
    
    if ($resultado['success']) {
        $_SESSION['admin_mensaje'] = 'Producto eliminado correctamente';
        $_SESSION['admin_mensaje_tipo'] = 'success';
    } else {
        $_SESSION['admin_mensaje'] = $resultado['error'];
        $_SESSION['admin_mensaje_tipo'] = 'error';
    }
} else {
    $_SESSION['admin_mensaje'] = 'ID de producto no válido';
    $_SESSION['admin_mensaje_tipo'] = 'error';
}

header('Location: dashboard.php');
exit();
?>