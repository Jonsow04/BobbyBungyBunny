<?php
// includes/config.php

// Incluir constantes primero
require_once __DIR__ . '/constantes.php';

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'adminRabbit');
define('DB_PASS', 'jonc-Esp32-arD');
define('DB_NAME', 'bunnydotcom');

// Crear conexión PDO
function getConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        die("Error de conexión a la base de datos");
    }
}
?>