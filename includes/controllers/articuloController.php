<?php
// includes/controllers/ArticuloController.php
require_once __DIR__ . '/../models/articulo.php';
require_once __DIR__ . '/../helpers/validation.php';

class ArticuloController {
    private $articuloModel;
    
    // Mapeo de imágenes por ID (podría ir en config o BD)
    private $mapaImagenes = [
        1 => 'kaytee-fiesta-1_6kg.jpg',
        2 => 'kaytee-pellets-supreme-4_54kg.jpg',
        3 => 'tazas-apilables.jpg',
        4 => 'habitat-jaula.jpg',
        5 => 'kit-aseo.jpg',
    ];
    
    public function __construct($pdo) {
        $this->articuloModel = new Articulo($pdo);
    }
    
    /**
     * Obtener todos los productos para la vista
     */
    public function listarArticulos() {
        $articulos = $this->articuloModel->obtenerTodos();
        
        // Enriquecer artículos con la ruta de imagen
        foreach ($articulos as &$articulo) {
            $articulo['imagen_url'] = $this->getImagenUrl($articulo['idArticulo']);
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
        
        $articulo['imagen_url'] = $this->getImagenUrl($articulo['idArticulo']);
        return ValidationHelper::validateArticuloData($articulo);
    }
    
    /**
     * Obtener la URL de la imagen de un producto
     */
    private function getImagenUrl($idArticulo) {
        $id = ValidationHelper::validateId($idArticulo);
        if ($id && isset($this->mapaImagenes[$id])) {
            return 'assets/multimedia/pictures/articulos/' . $this->mapaImagenes[$id];
        }
        return null; // Usará placeholder
    }
}
?>