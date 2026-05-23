<?php

/**
 * Validación y sanitización de datos
 */
class ValidationHelper {
    
    /**
     * Sanitiza un string para salida HTML
     */
    public static function sanitizeOutput($value) {
        if ($value === null) return '';
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valida que un ID sea numérico positivo
     */
    public static function validateId($id) {
        return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }
    
    /**
     * Valida que un precio sea numérico y positivo
     */
    public static function validatePrice($price) {
        if (!is_numeric($price)) return 0.0;
        $price = (float)$price;
        return $price > 0 ? $price : 0.0;
    }
    
    /**
     * Valida URL de imagen (solo rutas locales o URLs completas)
     */
    public static function validateImageUrl($url) {
        if (empty($url)) return null;
        
        // Permitir URLs completas
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        // Permitir rutas relativas seguras (empezando por assets/)
        if (preg_match('/^assets\//', $url)) {
            return $url;
        }
        
        return null;
    }
    
    /**
     * Valida y sanitiza datos de artículo antes de mostrar
     */
    public static function validateArticuloData($articulo) {
        if (!is_array($articulo)) {
            return [];
        }
        
        return [
            'idArticulo' => self::validateId($articulo['idArticulo'] ?? 0) ?: 0,
            'nombre' => self::sanitizeOutput($articulo['nombre'] ?? ''),
            'descripcion' => self::sanitizeOutput($articulo['descripcion'] ?? ''),
            'precio' => self::validatePrice($articulo['precio'] ?? 0),
            'stock' => self::validateId($articulo['stock'] ?? 0) ?: 0,
            'nombre_categoria' => self::sanitizeOutput($articulo['nombre_categoria'] ?? ''),
            'imagen_url' => self::validateImageUrl($articulo['imagen_url'] ?? null),
        ];
    }
    
    /**
     * Valida array de artículos
     */
    public static function validateArticulosArray($articulos) {
        if (!is_array($articulos)) {
            return [];
        }
        
        return array_map([self::class, 'validateArticuloData'], $articulos);
    }
}