<?php

require_once __DIR__ . '/../models/pedido.php';
require_once __DIR__ . '/../helpers/sessionHelper.php';

class PedidoController {
    private $pdo;
    private $pedidoModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->pedidoModel = new Pedido($pdo);
        SessionHelper::iniciar();
    }
    
    /**
     * Verificar si el usuario está logueado
     */
    public function verificarLogin() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: login.php');
            exit();
        }
        return $_SESSION['usuario_id'];
    }
    
    /**
     * Obtener todos los pedidos del usuario logueado
     */
    public function getMisPedidos() {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) {
            return [];
        }
        
        return $this->pedidoModel->obtenerPorUsuario($usuarioId);
    }
    
    /**
     * Obtener detalles de un pedido específico
     */
    public function getDetallesPedido($pedidoId) {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) {
            return null;
        }
        
        $pedido = $this->pedidoModel->obtenerPorId($pedidoId);
        
        // Verificar que el pedido pertenece al usuario
        if (!$pedido || $pedido['idUsuario'] != $usuarioId) {
            return null;
        }
        
        $detalles = $this->pedidoModel->obtenerDetalles($pedidoId);
        
        return [
            'pedido' => $pedido,
            'detalles' => $detalles
        ];
    }
}
?>