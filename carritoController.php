<?php

require_once __DIR__ . '/../models/carritoBD.php';
require_once __DIR__ . '/../helpers/sessionHelper.php';

class CarritoController {
    private $carritoBD;
    private $usuarioId;
    private $carritoId;
    private $carritoSesion;
    
    public function __construct($pdo = null) {
        SessionHelper::iniciar();
        
        // Obtener usuario logueado
        $this->usuarioId = $_SESSION['usuario_id'] ?? null;
        
        // Cargar carrito según si está logueado o no
        if ($this->usuarioId && $pdo) {
            $this->carritoBD = new CarritoBD($pdo);
            $carritoBD = $this->carritoBD->obtenerCarritoActivo($this->usuarioId);
            
            if ($carritoBD) {
                $this->carritoId = $carritoBD['idCarrito'];
                $this->carritoSesion = $carritoBD['items'];
            } else {
                $this->carritoId = $this->carritoBD->crearCarrito($this->usuarioId);
                $this->carritoSesion = [];
            }
        } else {
            // Usar sesión para invitados
            $this->carritoSesion = $_SESSION['carrito_invitado'] ?? [];
        }
    }
    
    /**
     * Obtener todos los items del carrito
     */
    public function getItems() {
        return $this->carritoSesion;
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregarProducto($productoId, $nombre, $precio, $cantidad = 1, $pdo = null) {
        $cantidad = max(1, intval($cantidad));
        
        if ($this->usuarioId && $this->carritoBD) {
            // Usuario logueado - guardar en BD
            $this->carritoBD->agregarProducto($this->carritoId, $productoId, $cantidad);
            $carritoActualizado = $this->carritoBD->obtenerCarritoConDetalles($this->carritoId);
            $this->carritoSesion = $carritoActualizado['items'];
            $totalItems = $carritoActualizado['total_items'];
            $subtotal = $carritoActualizado['total'];
        } else {
            // Invitado - guardar en sesión
            if (isset($this->carritoSesion[$productoId])) {
                $this->carritoSesion[$productoId]['cantidad'] += $cantidad;
            } else {
                $this->carritoSesion[$productoId] = [
                    'id' => $productoId,
                    'nombre' => $nombre,
                    'precio' => $precio,
                    'cantidad' => $cantidad
                ];
            }
            
            // Calcular subtotal
            $subtotal = 0;
            foreach ($this->carritoSesion as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }
            $totalItems = array_sum(array_column($this->carritoSesion, 'cantidad'));
            
            // Guardar en sesión
            $_SESSION['carrito_invitado'] = $this->carritoSesion;
        }
        
        return [
            'success' => true,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'total' => $subtotal
        ];
    }
    
    /**
     * Actualizar cantidad de un producto
     */
    public function actualizarCantidad($productoId, $cantidad) {
        $cantidad = max(0, intval($cantidad));
        
        if ($this->usuarioId && $this->carritoBD) {
            $this->carritoBD->actualizarCantidad($this->carritoId, $productoId, $cantidad);
            $carritoActualizado = $this->carritoBD->obtenerCarritoConDetalles($this->carritoId);
            $this->carritoSesion = $carritoActualizado['items'];
            $totalItems = $carritoActualizado['total_items'];
            $subtotal = $carritoActualizado['total'];
        } else {
            if ($cantidad <= 0) {
                unset($this->carritoSesion[$productoId]);
            } else {
                $this->carritoSesion[$productoId]['cantidad'] = $cantidad;
            }
            
            $subtotal = 0;
            foreach ($this->carritoSesion as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }
            $totalItems = array_sum(array_column($this->carritoSesion, 'cantidad'));
            
            $_SESSION['carrito_invitado'] = $this->carritoSesion;
        }
        
        return [
            'success' => true,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'total' => $subtotal
        ];
    }
    
    /**
     * Eliminar producto del carrito
     */
    public function eliminarProducto($productoId) {
        if ($this->usuarioId && $this->carritoBD) {
            $this->carritoBD->eliminarProducto($this->carritoId, $productoId);
            $carritoActualizado = $this->carritoBD->obtenerCarritoConDetalles($this->carritoId);
            $this->carritoSesion = $carritoActualizado['items'];
            $totalItems = $carritoActualizado['total_items'];
            $subtotal = $carritoActualizado['total'];
        } else {
            unset($this->carritoSesion[$productoId]);
            
            $subtotal = 0;
            foreach ($this->carritoSesion as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }
            $totalItems = array_sum(array_column($this->carritoSesion, 'cantidad'));
            
            $_SESSION['carrito_invitado'] = $this->carritoSesion;
        }
        
        return [
            'success' => true,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'total' => $subtotal
        ];
    }
    
    /**
     * Vaciar carrito completo
     */
    public function vaciarCarrito() {
        if ($this->usuarioId && $this->carritoBD) {
            $this->carritoBD->vaciarCarrito($this->carritoId);
            $this->carritoSesion = [];
        } else {
            $this->carritoSesion = [];
            $_SESSION['carrito_invitado'] = [];
        }
        
        return [
            'success' => true,
            'total_items' => 0,
            'subtotal' => 0,
            'total' => 0
        ];
    }
    
    /**
     * Obtener resumen del carrito
     */
    public function getResumen() {
        $subtotal = 0;
        foreach ($this->carritoSesion as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }
        
        return [
            'items' => $this->carritoSesion,
            'total_items' => array_sum(array_column($this->carritoSesion, 'cantidad')),
            'subtotal' => $subtotal,
            'total' => $subtotal
        ];
    }
    
    /**
     * Sincronizar carrito invitado con usuario (al hacer login)
     */
    public function sincronizarConUsuario($usuarioId, $pdo) {
        $carritoInvitado = $_SESSION['carrito_invitado'] ?? [];
        
        if (!empty($carritoInvitado)) {
            $carritoBD = new CarritoBD($pdo);
            $carritoBD->sincronizarCarrito($usuarioId, $carritoInvitado);
            
            // Limpiar carrito invitado
            unset($_SESSION['carrito_invitado']);
            
            // Recargar carrito de BD
            $this->usuarioId = $usuarioId;
            $this->carritoBD = $carritoBD;
            $carrito = $this->carritoBD->obtenerCarritoActivo($usuarioId);
            if ($carrito) {
                $this->carritoId = $carrito['idCarrito'];
                $this->carritoSesion = $carrito['items'];
            }
        }
    }
}
?>