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

if ($id) {
    $resultado = $adminController->eliminarProducto($id);
    $_SESSION['admin_mensaje'] = $resultado['success'] ? 'Producto eliminado correctamente' : $resultado['error'];
    $_SESSION['admin_mensaje_tipo'] = $resultado['success'] ? 'success' : 'error';
}

header('Location: dashboard.php');
exit();
?>