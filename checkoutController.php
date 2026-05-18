<?php

require_once __DIR__ . '/../models/pedido.php';
require_once __DIR__ . '/../models/usuario.php';
require_once __DIR__ . '/../helpers/sessionHelper.php';

class CheckoutController {
    private $pdo;
    private $pedidoModel;
    private $usuarioModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->pedidoModel = new Pedido($pdo);
        $this->usuarioModel = new Usuario($pdo);
        SessionHelper::iniciar();
    }
    
    /**
     * Verificar si el usuario está logueado
     */
    public function verificarLogin() {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['checkout_redirect'] = true;
            header('Location: login.php');
            exit();
        }
        return $_SESSION['usuario_id'];
    }
    
    /**
     * Obtener datos del usuario para el checkout
     */
    public function getDatosUsuario() {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) return null;
        
        $usuario = $this->usuarioModel->obtenerPorId($usuarioId);
        $direcciones = $this->usuarioModel->obtenerDirecciones($usuarioId);
        
        return [
            'usuario' => $usuario,
            'direcciones' => $direcciones,
            'direccion_principal' => $direcciones[0] ?? null
        ];
    }
    
    /**
     * Procesar el pedido
     */
    public function procesarPedido($carritoItems, $direccionCompleta, $total) {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        
        if (!$usuarioId) {
            return ['success' => false, 'error' => 'Usuario no autenticado'];
        }
        
        if (empty($carritoItems)) {
            return ['success' => false, 'error' => 'El carrito está vacío'];
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // 1. Actualizar stock
            $detallesStock = [];
            foreach ($carritoItems as $item) {
                $detallesStock[] = [
                    'idArticulo' => $item['id'],
                    'cantidad' => $item['cantidad']
                ];
            }
            $this->pedidoModel->actualizarStock($detallesStock);
            
            // 2. Crear el pedido
            $pedidoId = $this->pedidoModel->crear($usuarioId, $direccionCompleta, $total);
            
            // 3. Agregar detalles del pedido
            foreach ($carritoItems as $item) {
                $this->pedidoModel->agregarDetalle(
                    $pedidoId,
                    $item['id'],
                    $item['cantidad'],
                    $item['precio']
                );
            }
            
            // 4. Vaciar el carrito (BD o sesión)
            $this->vaciarCarrito($usuarioId);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'pedido_id' => $pedidoId
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Vaciar carrito después de la compra
     */
    private function vaciarCarrito($usuarioId) {
        // Eliminar carrito de BD
        $sql = "DELETE FROM detallecarrito WHERE idCarrito IN 
                (SELECT idCarrito FROM carrito WHERE idUsuario = :usuarioId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        
        // Limpiar sesión
        unset($_SESSION['carrito_invitado']);
    }
    
    /**
     * Calcular costo de envío (ejemplo básico)
     */
    public function calcularEnvio($cp) {
        // Aquí puedes integrar con API de paquetería
        // Por ahora, lógica simple
        if (empty($cp)) return 0;
        
        $cp = intval($cp);
        if ($cp >= 10000 && $cp <= 19999) return 150; // Zona norte
        if ($cp >= 20000 && $cp <= 29999) return 120; // Zona centro
        if ($cp >= 30000 && $cp <= 39999) return 180; // Zona sur
        return 200; // Otras zonas
    }
}
?>