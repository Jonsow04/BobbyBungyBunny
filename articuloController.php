<?php
// includes/controllers/articuloController.php
require_once __DIR__ . '/../models/articulo.php';

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
        
        return $articulos;
    }
    
    /**
     * Obtener la URL de la imagen de un producto
     */
    private function getImagenUrl($idArticulo) {
        if (isset($this->mapaImagenes[$idArticulo])) {
            return 'assets/multimedia/pictures/articulos/' . $this->mapaImagenes[$idArticulo];
        }
        return null; // Usará placeholder
    }
}
?>