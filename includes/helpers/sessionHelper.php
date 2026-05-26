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
        return $_SESSION['carrito_invitado'] ?? [];
    }
    
    /**
     * Guardar el carrito en la sesión
     */
    public static function guardarCarrito($carrito) {
        self::iniciar();
        $_SESSION['carrito_invitado'] = $carrito;
    }
    
    /**
     * Vaciar el carrito
     */
    public static function vaciarCarrito() {
        self::iniciar();
        $_SESSION['carrito_invitado'] = [];
    }
    
    /**
     * Obtener el usuario logueado
     */
    public static function getUsuario() {
        self::iniciar();
        return $_SESSION['usuario_id'] ?? null;
    }
    
    /**
     * Verificar si hay un usuario logueado
     */
    public static function isLoggedIn() {
        self::iniciar();
        return isset($_SESSION['usuario_id']);
    }
    public static function isAdmin() {
        self::iniciar();
        return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 1;
    }
    
    public static function isGerente() {
        self::iniciar();
        return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 2;
    }
    
    public static function isAdminOrGerente() {
        self::iniciar();
        return isset($_SESSION['usuario_tipo']) && ($_SESSION['usuario_tipo'] == 1 || $_SESSION['usuario_tipo'] == 2);
    }
    
    public static function isCliente() {
        self::iniciar();
        return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 3;
    }
    
    public static function requireAdmin() {
        self::iniciar();
        if (!self::isAdmin()) {
            header('Location: ../index.php');
            exit();
        }
    }
    
    public static function requireAdminOrGerente() {
        self::iniciar();
        if (!self::isAdminOrGerente()) {
            header('Location: ../index.php');
            exit();
        }
    }
}
?>