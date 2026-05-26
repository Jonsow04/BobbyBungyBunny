<?php

require_once __DIR__ . '/../models/articulo.php';
require_once __DIR__ . '/../models/pedido.php';
require_once __DIR__ . '/../models/usuario.php';
require_once __DIR__ . '/../helpers/sessionHelper.php';

class AdminController {
    private $pdo;
    private $articuloModel;
    private $pedidoModel;
    private $usuarioModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->articuloModel = new Articulo($pdo);
        $this->pedidoModel = new Pedido($pdo);
        $this->usuarioModel = new Usuario($pdo);
        SessionHelper::requireAdminOrGerente();
    }
    
    
    //Obtener todos los productos 
     
    public function getProductos() {
        return $this->articuloModel->obtenerTodos();
    }
    
    
    //Obtener producto por ID 
    
    public function getProductoById($id) {
        return $this->articuloModel->obtenerPorId($id);
    }
    
    
    //Crear nuevo producto
    
    public function crearProducto($datos) {
        try {
            $sql = "INSERT INTO articulo (nombre, descripcion, precio, stock, idCatArticulo) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['stock'],
                $datos['categoria']
            ]);
            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    
    //Actualizar producto
    
    public function actualizarProducto($id, $datos) {
        try {
            $sql = "UPDATE articulo 
                    SET nombre = ?, descripcion = ?, precio = ?, stock = ?, idCatArticulo = ? 
                    WHERE idArticulo = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['stock'],
                $datos['categoria'],
                $id
            ]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    
    //Eliminar producto (solo admin)
    
    public function eliminarProducto($id) {
        if (!SessionHelper::isAdmin()) {
            return ['success' => false, 'error' => 'No tienes permisos para eliminar productos'];
        }
        
        try {
            // Verificar si tiene pedidos asociados
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detallepedido WHERE idArticulo = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'error' => 'No se puede eliminar un producto con pedidos asociados'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM articulo WHERE idArticulo = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    
    //Obtener categorías
    
    public function getCategorias() {
        $stmt = $this->pdo->prepare("SELECT * FROM catarticulo ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    //Reporte: Productos más vendidos
    
    public function getProductosMasVendidos($limite = 10) {
        $sql = "SELECT a.idArticulo, a.nombre, a.precio, a.stock, 
                       SUM(dp.cantidad) as total_vendido,
                       SUM(dp.cantidad * dp.precioUnitario) as total_ingresos
                FROM articulo a
                INNER JOIN detallepedido dp ON a.idArticulo = dp.idArticulo
                INNER JOIN pedido p ON dp.idPedido = p.idPedido
                WHERE p.idEstatusPedido IN (2, 3, 4)
                GROUP BY a.idArticulo
                ORDER BY total_vendido DESC
                LIMIT :limite";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    //Reporte: Ventas por mes
    
    public function getVentasPorMes($anio = null) {
        $anio = $anio ?? date('Y');
        
        $sql = "SELECT MONTH(p.fecha) as mes, 
                       COUNT(p.idPedido) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_ventas
                FROM pedido p
                WHERE YEAR(p.fecha) = :anio
                AND p.idEstatusPedido IN (2, 3, 4)
                GROUP BY MONTH(p.fecha)
                ORDER BY mes ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':anio' => $anio]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ventasPorMes = array_fill(1, 12, ['total_pedidos' => 0, 'total_ventas' => 0]);
        foreach ($resultados as $row) {
            $ventasPorMes[$row['mes']] = [
                'total_pedidos' => $row['total_pedidos'],
                'total_ventas' => $row['total_ventas']
            ];
        }
        
        return $ventasPorMes;
    }
    
    
    //Reporte: Productos con bajo stock
    
    public function getProductosBajoStock($umbral = 5) {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                WHERE a.stock <= :umbral
                ORDER BY a.stock ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':umbral' => $umbral]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    //Reporte: Pedidos recientes (usando Pedido model)
     
    public function getPedidosRecientes($limite = 10) {
        $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email, e.estatus as estatus_nombre
                FROM pedido p
                INNER JOIN usuario u ON p.idUsuario = u.idUsuario
                INNER JOIN estatuspedido e ON p.idEstatusPedido = e.idEstatusPedido
                ORDER BY p.fecha DESC
                LIMIT :limite";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    //Obtener stats del dashboard
    
    public function getDashboardStats() {
        $stats = [];
        
        // Total productos
        $productos = $this->articuloModel->obtenerTodos();
        $stats['total_productos'] = count($productos);
        
        // Productos bajo stock
        $stats['productos_bajo_stock'] = count($this->getProductosBajoStock(5));
        
        // Pedidos del mes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM pedido WHERE MONTH(fecha) = MONTH(CURDATE())");
        $stmt->execute();
        $stats['pedidos_mes'] = $stmt->fetchColumn();
        
        // Ventas del mes
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM pedido WHERE MONTH(fecha) = MONTH(CURDATE()) AND idEstatusPedido IN (2,3,4)");
        $stmt->execute();
        $stats['ventas_mes'] = $stmt->fetchColumn();
        
        // Total clientes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuario WHERE idTipoUsuario = 3");
        $stmt->execute();
        $stats['total_clientes'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    
    //Actualizar estatus de pedido
    
    public function actualizarEstatusPedido($pedidoId, $estatusId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE pedido SET idEstatusPedido = ? WHERE idPedido = ?");
            $stmt->execute([$estatusId, $pedidoId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    
    //Obtener estatus de pedido
    
    public function getEstatusPedido() {
        $stmt = $this->pdo->prepare("SELECT * FROM estatuspedido");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}