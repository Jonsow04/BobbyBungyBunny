<?php

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/controllers/carritoController.php';
require_once __DIR__ . '/../includes/helpers/sanitize.php';

header('Content-Type: application/json');

$pdo = getConnection();
$carritoController = new CarritoController($pdo);
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = SanitizeHelper::limpiarTexto($_POST['action'] ?? '');
    
    switch ($action) {
        case 'agregar':
            $id = SanitizeHelper::validarInt($_POST['id'] ?? 0);
            $nombre = SanitizeHelper::limpiarTexto($_POST['nombre'] ?? '');
            $precio = SanitizeHelper::validarFloat($_POST['precio'] ?? 0);
            $cantidad = SanitizeHelper::validarInt($_POST['cantidad'] ?? 1, 1);
            
            if ($id && $nombre && $precio && $cantidad) {
                $response = $carritoController->agregarProducto($id, $nombre, $precio, $cantidad, $pdo);
            } else {
                $response['error'] = 'Datos de producto inválidos';
            }
            break;
            
        case 'actualizar':
            $id = SanitizeHelper::validarInt($_POST['id'] ?? 0);
            $cantidad = SanitizeHelper::validarInt($_POST['cantidad'] ?? 1, 1);
            if ($id && $cantidad) {
                $response = $carritoController->actualizarCantidad($id, $cantidad);
            }
            break;
            
        case 'eliminar':
            $id = SanitizeHelper::validarInt($_POST['id'] ?? 0);
            if ($id) {
                $response = $carritoController->eliminarProducto($id);
            }
            break;
            
        case 'vaciar':
            $response = $carritoController->vaciarCarrito();
            break;
            
        case 'get_count':
            $resumen = $carritoController->getResumen();
            $response = ['success' => true, 'total_items' => $resumen['total_items']];
            break;
    }
}

echo json_encode($response);
?>