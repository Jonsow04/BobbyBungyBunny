<?php

session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/controllers/pedidoController.php';

header('Content-Type: application/json');

$pdo = getConnection();
$pedidoController = new PedidoController($pdo);

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'detalle':
            $pedidoId = $_GET['id'] ?? 0;
            if ($pedidoId) {
                $detalle = $pedidoController->getDetallesPedido($pedidoId);
                if ($detalle) {
                    $response = [
                        'success' => true,
                        'pedido' => $detalle['pedido'],
                        'detalles' => $detalle['detalles']
                    ];
                } else {
                    $response['error'] = 'Pedido no encontrado';
                }
            } else {
                $response['error'] = 'ID de pedido requerido';
            }
            break;
            
        default:
            $response['error'] = 'Acción no válida';
    }
} else {
    $response['error'] = 'Método no permitido';
}

echo json_encode($response);
?>
