<?php

class CarritoBD {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Obtener carrito activo de un usuario
     */
    public function obtenerCarritoActivo($usuarioId) {
        $sql = "SELECT c.idCarrito FROM carrito c 
                WHERE c.idUsuario = :usuarioId 
                ORDER BY c.fecha DESC LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        $result = $stmt->fetch();
        
        if ($result) {
            return $this->obtenerCarritoConDetalles($result['idCarrito']);
        }
        
        return null;
    }
    
    /**
     * Obtener carrito completo con detalles
     */
    public function obtenerCarritoConDetalles($carritoId) {
        $sql = "SELECT a.idArticulo, a.nombre, a.precio, dc.cantidad,
                (a.precio * dc.cantidad) as subtotal
                FROM detallecarrito dc
                INNER JOIN articulo a ON dc.idArticulo = a.idArticulo
                WHERE dc.idCarrito = :carritoId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':carritoId' => $carritoId]);
        
        $items = [];
        $total = 0;
        
        while ($row = $stmt->fetch()) {
            $items[$row['idArticulo']] = [
                'id' => $row['idArticulo'],
                'nombre' => $row['nombre'],
                'precio' => floatval($row['precio']),
                'cantidad' => $row['cantidad'],
                'subtotal' => floatval($row['subtotal'])
            ];
            $total += $row['subtotal'];
        }
        
        return [
            'idCarrito' => $carritoId,
            'items' => $items,
            'total' => $total,
            'total_items' => array_sum(array_column($items, 'cantidad'))
        ];
    }
    
    /**
     * Crear nuevo carrito para un usuario
     */
    public function crearCarrito($usuarioId) {
        $sql = "INSERT INTO carrito (idUsuario) VALUES (:usuarioId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregarProducto($carritoId, $articuloId, $cantidad) {
        // Verificar si ya existe
        $sql = "SELECT cantidad FROM detallecarrito 
                WHERE idCarrito = :carritoId AND idArticulo = :articuloId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':carritoId' => $carritoId,
            ':articuloId' => $articuloId
        ]);
        
        $existente = $stmt->fetch();
        
        if ($existente) {
            // Actualizar cantidad
            $nuevaCantidad = $existente['cantidad'] + $cantidad;
            return $this->actualizarCantidad($carritoId, $articuloId, $nuevaCantidad);
        } else {
            // Insertar nuevo
            $sql = "INSERT INTO detallecarrito (idCarrito, idArticulo, cantidad) 
                    VALUES (:carritoId, :articuloId, :cantidad)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':carritoId' => $carritoId,
                ':articuloId' => $articuloId,
                ':cantidad' => $cantidad
            ]);
        }
    }
    
    /**
     * Actualizar cantidad de un producto
     */
    public function actualizarCantidad($carritoId, $articuloId, $cantidad) {
        if ($cantidad <= 0) {
            return $this->eliminarProducto($carritoId, $articuloId);
        }
        
        $sql = "UPDATE detallecarrito SET cantidad = :cantidad 
                WHERE idCarrito = :carritoId AND idArticulo = :articuloId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':carritoId' => $carritoId,
            ':articuloId' => $articuloId,
            ':cantidad' => $cantidad
        ]);
    }
    
    /**
     * Eliminar producto del carrito
     */
    public function eliminarProducto($carritoId, $articuloId) {
        $sql = "DELETE FROM detallecarrito 
                WHERE idCarrito = :carritoId AND idArticulo = :articuloId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':carritoId' => $carritoId,
            ':articuloId' => $articuloId
        ]);
    }
    
    /**
     * Vaciar carrito completo
     */
    public function vaciarCarrito($carritoId) {
        $sql = "DELETE FROM detallecarrito WHERE idCarrito = :carritoId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':carritoId' => $carritoId]);
    }
    
    /**
     * Sincronizar carrito de sesión con BD (al hacer login)
     */
    public function sincronizarCarrito($usuarioId, $carritoSesion) {
        $carritoBD = $this->obtenerCarritoActivo($usuarioId);
        $carritoId = $carritoBD ? $carritoBD['idCarrito'] : $this->crearCarrito($usuarioId);
        
        foreach ($carritoSesion as $item) {
            $this->agregarProducto($carritoId, $item['id'], $item['cantidad']);
        }
        
        return $this->obtenerCarritoConDetalles($carritoId);
    }
}
?>