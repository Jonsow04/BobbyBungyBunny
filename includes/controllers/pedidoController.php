<?php
// includes/controllers/pedidoController.php

require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/carritoController.php';

class PedidoController {
    private $pdo;
    private $pedidoModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->pedidoModel = new Pedido($pdo);
    }
    
    public function getResumen() {
        $carritoController = new CarritoController($this->pdo);
        return $carritoController->getResumen();
    }
    
    public function crearPedido($usuarioId, $direccion) {
        try {
            $carritoController = new CarritoController($this->pdo);
            $resumen = $carritoController->getResumen();
            
            if ($resumen['total_items'] == 0) {
                return ['success' => false, 'error' => 'El carrito está vacío'];
            }
            
            // Verificar stock disponible antes de procesar
            foreach ($resumen['items'] as $item) {
                $stmt = $this->pdo->prepare("SELECT stock FROM articulo WHERE idArticulo = ?");
                $stmt->execute([$item['id']]);
                $stockActual = $stmt->fetchColumn();
                
                if ($stockActual < $item['cantidad']) {
                    return [
                        'success' => false, 
                        'error' => "No hay suficiente stock de {$item['nombre']}. Disponible: {$stockActual}"
                    ];
                }
            }
            
            $this->pdo->beginTransaction();
            
            // 1. Crear el pedido (idEstatusPedido = 1 = Pendiente)
            $pedidoId = $this->pedidoModel->crear(
                $usuarioId, 
                $direccion, 
                $resumen['total'], 
                1
            );
            
            if (!$pedidoId) {
                throw new Exception("Error al crear el pedido");
            }
            
            // 2. Preparar detalles y agregarlos
            $detalles = [];
            foreach ($resumen['items'] as $item) {
                $detalle = [
                    'idArticulo' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precioUnitario' => $item['precio']
                ];
                $detalles[] = $detalle;
                
                // Agregar detalle del pedido
                $this->pedidoModel->agregarDetalle(
                    $pedidoId,
                    $item['id'],
                    $item['cantidad'],
                    $item['precio']
                );
            }
            
            // 3. Actualizar stock (descontar productos)
            $this->pedidoModel->actualizarStock($detalles);
            
            // 4. Vaciar carrito
            $carritoController->vaciarCarrito();
            
            $this->pdo->commit();
            
            return ['success' => true, 'pedido_id' => $pedidoId];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al crear pedido: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getDetallesPedido($pedidoId) {
        try {
            $pedido = $this->pedidoModel->obtenerPorId($pedidoId);
            
            if (!$pedido) {
                return null;
            }
            
            $detalles = $this->pedidoModel->obtenerDetalles($pedidoId);
            
            return [
                'pedido' => $pedido,
                'detalles' => $detalles
            ];
            
        } catch (Exception $e) {
            error_log("Error al obtener detalles del pedido: " . $e->getMessage());
            return null;
        }
    }
    
    public function getPedidosByUsuario($usuarioId) {
        try {
            return $this->pedidoModel->obtenerPorUsuario($usuarioId);
        } catch (Exception $e) {
            error_log("Error al obtener pedidos: " . $e->getMessage());
            return [];
        }
    }
}
?>