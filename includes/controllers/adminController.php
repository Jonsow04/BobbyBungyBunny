<?php
// includes/controllers/AdminController.php

require_once __DIR__ . '/../models/articulo.php';
require_once __DIR__ . '/../models/pedido.php';
require_once __DIR__ . '/../models/usuario.php';

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
    }
    
    // ========== MÉTODOS PARA PRODUCTOS ==========
    
    /**
     * Obtener todos los productos
     */
    public function getProductos() {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                ORDER BY a.idArticulo DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener producto por ID
     */
    public function getProductoById($id) {
        $sql = "SELECT a.*, c.idCatArticulo as categoria_id, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                WHERE a.idArticulo = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las categorías
     */
    public function getCategorias() {
        $stmt = $this->pdo->prepare("SELECT * FROM catarticulo ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear nuevo producto
     */
    public function crearProducto($datos, $imagen = null) {
        try {
            $sql = "INSERT INTO articulo (nombre, descripcion, precio, stock, idCatArticulo, imagen) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['stock'],
                $datos['categoria'],
                $imagen
            ]);
            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Actualizar producto
     */
    public function actualizarProducto($id, $datos, $imagen = null) {
        try {
            if ($imagen) {
                // Obtener imagen anterior para eliminarla
                $producto = $this->getProductoById($id);
                if ($producto && $producto['imagen']) {
                    $this->eliminarImagen($producto['imagen']);
                }
                
                $sql = "UPDATE articulo 
                        SET nombre = ?, descripcion = ?, precio = ?, stock = ?, idCatArticulo = ?, imagen = ? 
                        WHERE idArticulo = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $datos['nombre'],
                    $datos['descripcion'],
                    $datos['precio'],
                    $datos['stock'],
                    $datos['categoria'],
                    $imagen,
                    $id
                ]);
            } else {
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
            }
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Eliminar producto
     */
    public function eliminarProducto($id) {
        try {
            // Verificar que el ID sea válido
            $id = intval($id);
            if ($id <= 0) {
                return ['success' => false, 'error' => 'ID de producto no válido'];
            }
            
            // Verificar si el producto existe
            $stmt = $this->pdo->prepare("SELECT * FROM articulo WHERE idArticulo = ?");
            $stmt->execute([$id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                return ['success' => false, 'error' => 'El producto no existe'];
            }
            
            // Verificar si tiene pedidos asociados
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM detallepedido WHERE idArticulo = ?");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                return ['success' => false, 'error' => 'No se puede eliminar el producto porque tiene ' . $resultado['total'] . ' pedido(s) asociado(s)'];
            }
            
            // Eliminar la imagen del servidor si existe
            if (!empty($producto['imagen'])) {
                $this->eliminarImagen($producto['imagen']);
            }
            
            // Eliminar el producto
            $stmt = $this->pdo->prepare("DELETE FROM articulo WHERE idArticulo = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    // ========== FUNCIONES PARA IMÁGENES ==========
    
    /**
     * Subir y redimensionar imagen
     */
    public function subirImagen($archivo, $multiplesTamaños = false) {
        $directorio = __DIR__ . '/../../public/assets/multimedia/productos/';
        
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return ['success' => false, 'error' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF o WEBP.'];
        }
        
        if ($archivo['size'] > 10 * 1024 * 1024) {
            return ['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 10MB.'];
        }
        
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        $rutaFinal = $directorio . $nombreArchivo;
        
        // Verificar si la extensión GD está disponible para redimensionar
        if (extension_loaded('gd')) {
            $rutaTemporal = $directorio . 'temp_' . $nombreArchivo;
            if (move_uploaded_file($archivo['tmp_name'], $rutaTemporal)) {
                $this->redimensionarImagen($rutaTemporal, $rutaFinal, 800, 800);
                unlink($rutaTemporal);
            } else {
                return ['success' => false, 'error' => 'Error al subir la imagen'];
            }
        } else {
            // Si GD no está disponible, solo mover el archivo
            if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
                return ['success' => false, 'error' => 'Error al subir la imagen'];
            }
        }
        
        return ['success' => true, 'nombre' => $nombreArchivo];
    }
    
    /**
     * Redimensionar imagen manteniendo la relación de aspecto
     */
    private function redimensionarImagen($rutaOrigen, $rutaDestino, $nuevoAncho = 800, $nuevoAlto = 800) {
        list($anchoOriginal, $altoOriginal, $tipo) = getimagesize($rutaOrigen);
        
        $relacionAncho = $nuevoAncho / $anchoOriginal;
        $relacionAlto = $nuevoAlto / $altoOriginal;
        
        if ($relacionAncho < $relacionAlto) {
            $anchoFinal = $nuevoAncho;
            $altoFinal = intval($altoOriginal * $relacionAncho);
        } else {
            $anchoFinal = intval($anchoOriginal * $relacionAlto);
            $altoFinal = $nuevoAlto;
        }
        
        $imagenDestino = imagecreatetruecolor($anchoFinal, $altoFinal);
        
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                $imagenOrigen = imagecreatefromjpeg($rutaOrigen);
                break;
            case IMAGETYPE_PNG:
                $imagenOrigen = imagecreatefrompng($rutaOrigen);
                imagealphablending($imagenDestino, false);
                imagesavealpha($imagenDestino, true);
                break;
            case IMAGETYPE_GIF:
                $imagenOrigen = imagecreatefromgif($rutaOrigen);
                break;
            case IMAGETYPE_WEBP:
                $imagenOrigen = imagecreatefromwebp($rutaOrigen);
                break;
            default:
                return false;
        }
        
        imagecopyresampled($imagenDestino, $imagenOrigen, 0, 0, 0, 0, 
                           $anchoFinal, $altoFinal, $anchoOriginal, $altoOriginal);
        
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                imagejpeg($imagenDestino, $rutaDestino, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($imagenDestino, $rutaDestino, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($imagenDestino, $rutaDestino);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($imagenDestino, $rutaDestino, 85);
                break;
        }
        
        imagedestroy($imagenOrigen);
        imagedestroy($imagenDestino);
        
        return true;
    }
    
    /**
     * Eliminar imagen del servidor
     */
    public function eliminarImagen($nombreImagen) {
        if (!$nombreImagen) {
            return true;
        }
        
        $directorio = __DIR__ . '/../../public/assets/multimedia/productos/';
        $rutaPrincipal = $directorio . $nombreImagen;
        
        if (file_exists($rutaPrincipal)) {
            return unlink($rutaPrincipal);
        }
        
        return true;
    }
    
    // ========== MÉTODOS PARA REPORTES Y ESTADÍSTICAS ==========
    
    /**
     * Obtener productos más vendidos
     */
    public function getProductosMasVendidos($limite = 10) {
        $sql = "SELECT a.idArticulo, a.nombre, a.precio, a.stock, a.imagen,
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
    
    /**
     * Obtener productos con bajo stock
     */
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
    
    /**
     * Obtener pedidos recientes
     */
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
    
    /**
     * Obtener estadísticas del dashboard
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total productos
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM articulo");
        $stats['total_productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Productos bajo stock
        $stats['productos_bajo_stock'] = count($this->getProductosBajoStock(5));
        
        // Pedidos del mes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM pedido WHERE MONTH(fecha) = MONTH(CURDATE())");
        $stmt->execute();
        $stats['pedidos_mes'] = $stmt->fetchColumn();
        
        // Ventas del mes
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM pedido WHERE MONTH(fecha) = MONTH(CURDATE()) AND idEstatusPedido IN (2,3,4)");
        $stmt->execute();
        $stats['ventas_mes'] = $stmt->fetchColumn();
        
        // Total clientes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM usuario WHERE idTipoUsuario = 3");
        $stmt->execute();
        $stats['total_clientes'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    /**
     * Obtener estatus de pedido
     */
    public function getEstatusPedido() {
        $stmt = $this->pdo->prepare("SELECT * FROM estatuspedido");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualizar estatus de pedido
     */
    public function actualizarEstatusPedido($pedidoId, $estatusId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE pedido SET idEstatusPedido = ? WHERE idPedido = ?");
            $stmt->execute([$estatusId, $pedidoId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>