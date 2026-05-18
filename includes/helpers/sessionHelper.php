<?php

class SessionHelper {
    
    /**
     * Iniciar sesión si no está iniciada
     */
    public static function iniciar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Obtener el carrito de la sesión
     */
    public static function getCarrito() {
        self::iniciar();
        return $_SESSION['carrito'] ?? [];
    }
    
    /**
     * Guardar el carrito en la sesión
     */
    public static function guardarCarrito($carrito) {
        self::iniciar();
        $_SESSION['carrito'] = $carrito;
    }
    
    /**
     * Vaciar el carrito
     */
    public static function vaciarCarrito() {
        self::iniciar();
        $_SESSION['carrito'] = [];
    }
    
    /**
     * Obtener el usuario logueado
     */
    public static function getUsuario() {
        self::iniciar();
        return $_SESSION['usuario'] ?? null;
    }
    
    /**
     * Verificar si hay un usuario logueado
     */
    public static function isLoggedIn() {
        self::iniciar();
        return isset($_SESSION['usuario_id']);
    }
}
?>
