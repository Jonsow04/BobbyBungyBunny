<?php

class Pedido {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Crear un nuevo pedido
     */
    public function crear($usuarioId, $direccion, $total, $idEstatusPedido = 1) {
        $sql = "INSERT INTO pedido (idEstatusPedido, idUsuario, total, direccion) 
                VALUES (:idEstatusPedido, :idUsuario, :total, :direccion)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':idEstatusPedido' => $idEstatusPedido,
            ':idUsuario' => $usuarioId,
            ':total' => $total,
            ':direccion' => $direccion
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Agregar detalle de pedido
     */
    public function agregarDetalle($pedidoId, $articuloId, $cantidad, $precioUnitario) {
        $sql = "INSERT INTO detallepedido (idPedido, idArticulo, cantidad, precioUnitario) 
                VALUES (:idPedido, :idArticulo, :cantidad, :precioUnitario)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':idPedido' => $pedidoId,
            ':idArticulo' => $articuloId,
            ':cantidad' => $cantidad,
            ':precioUnitario' => $precioUnitario
        ]);
    }
    
    /**
     * Obtener pedido por ID
     */
    public function obtenerPorId($pedidoId) {
        $sql = "SELECT p.*, e.estatus as estatus_nombre 
                FROM pedido p
                INNER JOIN estatuspedido e ON p.idEstatusPedido = e.idEstatusPedido
                WHERE p.idPedido = :pedidoId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pedidoId' => $pedidoId]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener detalles de un pedido
     */
    public function obtenerDetalles($pedidoId) {
        $sql = "SELECT dp.*, a.nombre as articulo_nombre 
                FROM detallepedido dp
                INNER JOIN articulo a ON dp.idArticulo = a.idArticulo
                WHERE dp.idPedido = :pedidoId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pedidoId' => $pedidoId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener pedidos de un usuario
     */
    public function obtenerPorUsuario($usuarioId) {
        $sql = "SELECT p.*, e.estatus as estatus_nombre 
                FROM pedido p
                INNER JOIN estatuspedido e ON p.idEstatusPedido = e.idEstatusPedido
                WHERE p.idUsuario = :usuarioId
                ORDER BY p.fecha DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Actualizar estatus de un pedido
     */
    public function actualizarEstatus($pedidoId, $idEstatusPedido) {
        $sql = "UPDATE pedido SET idEstatusPedido = :idEstatusPedido 
                WHERE idPedido = :pedidoId";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':pedidoId' => $pedidoId,
            ':idEstatusPedido' => $idEstatusPedido
        ]);
    }
    
    /**
     * Actualizar stock de productos después de una compra
     */
    public function actualizarStock($detalles) {
        foreach ($detalles as $detalle) {
            $sql = "UPDATE articulo SET stock = stock - :cantidad 
                    WHERE idArticulo = :idArticulo AND stock >= :cantidad";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':cantidad' => $detalle['cantidad'],
                ':idArticulo' => $detalle['idArticulo']
            ]);
            
            if ($stmt->rowCount() == 0) {
                throw new Exception("Stock insuficiente para el producto ID: " . $detalle['idArticulo']);
            }
        }
        return true;
    }
}
?>
