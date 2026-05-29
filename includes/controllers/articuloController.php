<?php
// includes/controllers/ArticuloController.php
require_once __DIR__ . '/../models/articulo.php';
require_once __DIR__ . '/../helpers/validation.php';

class ArticuloController {
    private $articuloModel;
    
    public function __construct($pdo) {
        $this->articuloModel = new Articulo($pdo);
    }
    
    /**
     * Obtener todos los productos para la vista
     */
    public function listarArticulos() {
        $articulos = $this->articuloModel->obtenerTodos();
        
        // Enriquecer artículos con la ruta de imagen desde la BD
        foreach ($articulos as &$articulo) {
            $articulo['imagen_url'] = $this->getImagenUrl($articulo);
        }
        
        // Validar y sanitizar datos antes de devolverlos
        return ValidationHelper::validateArticulosArray($articulos);
    }
    
    /**
     * Obtener un producto por ID (validado)
     */
    public function obtenerArticulo($idArticulo) {
        $id = ValidationHelper::validateId($idArticulo);
        if (!$id) {
            return null;
        }
        
        $articulo = $this->articuloModel->obtenerPorId($id);
        if (!$articulo) {
            return null;
        }
        
        $articulo['imagen_url'] = $this->getImagenUrl($articulo);
        return ValidationHelper::validateArticuloData($articulo);
    }
    
    /**
     * Obtener la URL de la imagen de un producto desde la BD
     * Las imágenes se guardan en: assets/multimedia/productos/
     */
    private function getImagenUrl($articulo) {
        // Verificar si el producto tiene una imagen guardada en la BD
        if (!empty($articulo['imagen']) && file_exists(__DIR__ . '/../../public/assets/multimedia/productos/' . $articulo['imagen'])) {
            return 'assets/multimedia/productos/' . $articulo['imagen'];
        }
        
        // Si no tiene imagen, retornar null para usar el placeholder
        return null;
    }
    
    /**
     * Obtener productos destacados (con stock disponible)
     */
    public function listarArticulosDestacados($limite = 8) {
        $articulos = $this->articuloModel->obtenerDestacados($limite);
        
        foreach ($articulos as &$articulo) {
            $articulo['imagen_url'] = $this->getImagenUrl($articulo);
        }
        
        return ValidationHelper::validateArticulosArray($articulos);
    }
    
    /**
     * Obtener productos por categoría
     */
    public function listarArticulosPorCategoria($idCategoria) {
        $id = ValidationHelper::validateId($idCategoria);
        if (!$id) {
            return [];
        }
        
        $articulos = $this->articuloModel->obtenerPorCategoria($id);
        
        foreach ($articulos as &$articulo) {
            $articulo['imagen_url'] = $this->getImagenUrl($articulo);
        }
        
        return ValidationHelper::validateArticulosArray($articulos);
    }
    
    /**
     * Buscar productos por término
     */
    public function buscarArticulos($termino) {
        $termino = ValidationHelper::sanitizarTexto($termino);
        if (empty($termino)) {
            return [];
        }
        
        $articulos = $this->articuloModel->buscar($termino);
        
        foreach ($articulos as &$articulo) {
            $articulo['imagen_url'] = $this->getImagenUrl($articulo);
        }
        
        return ValidationHelper::validateArticulosArray($articulos);
    }
}
?>