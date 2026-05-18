<?php

session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/controllers/carritoController.php';

header('Content-Type: application/json');

$pdo = getConnection();
$carritoController = new CarritoController($pdo);
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'agregar':
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $cantidad = $_POST['cantidad'] ?? 1;
            
            if ($id && $nombre && $precio) {
                $response = $carritoController->agregarProducto($id, $nombre, $precio, $cantidad, $pdo);
            } else {
                $response['error'] = 'Faltan datos del producto';
            }
            break;
            
        case 'actualizar':
            $id = $_POST['id'] ?? null;
            $cantidad = $_POST['cantidad'] ?? 1;
            if ($id) {
                $response = $carritoController->actualizarCantidad($id, $cantidad);
            }
            break;
            
        case 'eliminar':
            $id = $_POST['id'] ?? null;
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
