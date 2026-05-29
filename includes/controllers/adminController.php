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
    
    // ========== MÉTODOS EXISTENTES ==========
    
    public function getProductos() {
        return $this->articuloModel->obtenerTodos();
    }
    
    public function getProductoById($id) {
        return $this->articuloModel->obtenerPorId($id);
    }
    
    public function getCategorias() {
        $stmt = $this->pdo->prepare("SELECT * FROM catarticulo ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
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
    
    public function getDashboardStats() {
        $stats = [];
        
        $productos = $this->articuloModel->obtenerTodos();
        $stats['total_productos'] = count($productos);
        $stats['productos_bajo_stock'] = count($this->getProductosBajoStock(5));
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM pedido WHERE MONTH(fecha) = MONTH(CURDATE())");
        $stmt->execute();
        $stats['pedidos_mes'] = $stmt->fetchColumn();
        
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM pedido WHERE MONTH(fecha) = MONTH(CURDATE()) AND idEstatusPedido IN (2,3,4)");
        $stmt->execute();
        $stats['ventas_mes'] = $stmt->fetchColumn();
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuario WHERE idTipoUsuario = 3");
        $stmt->execute();
        $stats['total_clientes'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    public function actualizarEstatusPedido($pedidoId, $estatusId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE pedido SET idEstatusPedido = ? WHERE idPedido = ?");
            $stmt->execute([$estatusId, $pedidoId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getEstatusPedido() {
        $stmt = $this->pdo->prepare("SELECT * FROM estatuspedido");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ========== FUNCIONES DE REDIMENSIONAMIENTO DE IMÁGENES ==========
    
    /**
     * Redimensionar imagen manteniendo la relación de aspecto
     */
    private function redimensionarImagen($rutaOrigen, $rutaDestino, $nuevoAncho = 800, $nuevoAlto = 800) {
        // Obtener información de la imagen original
        list($anchoOriginal, $altoOriginal, $tipo) = getimagesize($rutaOrigen);
        
        // Calcular nuevas dimensiones manteniendo la relación de aspecto
        $relacionAncho = $nuevoAncho / $anchoOriginal;
        $relacionAlto = $nuevoAlto / $altoOriginal;
        
        if ($relacionAncho < $relacionAlto) {
            $anchoFinal = $nuevoAncho;
            $altoFinal = intval($altoOriginal * $relacionAncho);
        } else {
            $anchoFinal = intval($anchoOriginal * $relacionAlto);
            $altoFinal = $nuevoAlto;
        }
        
        // Crear imagen de destino
        $imagenDestino = imagecreatetruecolor($anchoFinal, $altoFinal);
        
        // Crear imagen de origen según el tipo
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                $imagenOrigen = imagecreatefromjpeg($rutaOrigen);
                break;
            case IMAGETYPE_PNG:
                $imagenOrigen = imagecreatefrompng($rutaOrigen);
                // Preservar transparencia en PNG
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
        
        // Redimensionar
        imagecopyresampled($imagenDestino, $imagenOrigen, 0, 0, 0, 0, 
                           $anchoFinal, $altoFinal, $anchoOriginal, $altoOriginal);
        
        // Guardar la imagen redimensionada según el formato original
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
        
        // Liberar memoria
        imagedestroy($imagenOrigen);
        imagedestroy($imagenDestino);
        
        return true;
    }
    
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
        $nombreBase = time() . '_' . uniqid();
        $nombreArchivo = $nombreBase . '.' . $extension;
        $rutaTemporal = $directorio . 'temp_' . $nombreArchivo;
        $rutaFinal = $directorio . $nombreArchivo;
        
        if (!move_uploaded_file($archivo['tmp_name'], $rutaTemporal)) {
            return ['success' => false, 'error' => 'Error al subir la imagen'];
        }
        
        if (!$this->redimensionarImagen($rutaTemporal, $rutaFinal, 800, 800)) {
            unlink($rutaTemporal);
            return ['success' => false, 'error' => 'Error al procesar la imagen'];
        }
        
        unlink($rutaTemporal);
        
        return ['success' => true, 'nombre' => $nombreArchivo];
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
    
    /**
     * Crear nuevo producto con imagen
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
     * Actualizar producto con imagen
     */
    public function actualizarProducto($id, $datos, $imagen = null) {
        try {
            if ($imagen) {
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
        if (!SessionHelper::isAdmin()) {
            return ['success' => false, 'error' => 'No tienes permisos para eliminar productos'];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detallepedido WHERE idArticulo = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'error' => 'No se puede eliminar un producto con pedidos asociados'];
            }
            
            $producto = $this->getProductoById($id);
            if ($producto && $producto['imagen']) {
                $this->eliminarImagen($producto['imagen']);
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM articulo WHERE idArticulo = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>