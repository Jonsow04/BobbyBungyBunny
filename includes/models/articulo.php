<?php

class Articulo {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Obtener todos los productos
     */
    public function obtenerTodos() {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a 
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo 
                ORDER BY a.idArticulo ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un producto por ID
     */
    public function obtenerPorId($idArticulo) {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a 
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo 
                WHERE a.idArticulo = :idArticulo 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idArticulo' => $idArticulo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener productos por categoría
     */
    public function obtenerPorCategoria($idCategoria) {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a 
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo 
                WHERE a.idCatArticulo = :idCategoria
                ORDER BY a.idArticulo ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCategoria' => $idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>