<?php
// includes/models/articulo.php

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
                ORDER BY a.idArticulo DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                WHERE a.idArticulo = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener productos destacados (con stock > 0)
     */
    public function obtenerDestacados($limite = 8) {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                WHERE a.stock > 0
                ORDER BY a.idArticulo DESC
                LIMIT :limite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener productos por categoría
     */
    public function obtenerPorCategoria($idCategoria) {
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                WHERE a.idCatArticulo = ?
                ORDER BY a.idArticulo DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar productos por término
     */
    public function buscar($termino) {
        $termino = '%' . $termino . '%';
        $sql = "SELECT a.*, c.nombre as nombre_categoria 
                FROM articulo a
                INNER JOIN catarticulo c ON a.idCatArticulo = c.idCatArticulo
                WHERE a.nombre LIKE ? OR a.descripcion LIKE ?
                ORDER BY a.idArticulo DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termino, $termino]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear un nuevo producto
     */
    public function crear($datos) {
        $sql = "INSERT INTO articulo (nombre, descripcion, precio, stock, idCatArticulo, imagen) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['stock'],
            $datos['categoria'],
            $datos['imagen'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Actualizar un producto
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE articulo 
                SET nombre = ?, descripcion = ?, precio = ?, stock = ?, idCatArticulo = ?, imagen = ? 
                WHERE idArticulo = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['stock'],
            $datos['categoria'],
            $datos['imagen'] ?? null,
            $id
        ]);
    }
    
    /**
     * Eliminar un producto
     */
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM articulo WHERE idArticulo = ?");
        return $stmt->execute([$id]);
    }
}
?>