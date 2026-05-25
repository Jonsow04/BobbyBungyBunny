<?php

/**
 * Obtiene la conexión a la base de datos
 * @return PDO Objeto de conexión PDO
 */
function getConnection() {
    // Configuración de la base de datos
    $host = 'localhost';
    $dbname = 'bunnydotcom';
    $user = 'adminRabbit';
    $pass = 'jonc-Esp32-arD';
    
    try {
        // Crear conexión PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        
        // Configurar PDO para que lance excepciones en caso de error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Configurar el modo de fetch por defecto (asociativo)
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        
        // Mostrar un mensaje amigable
        
        die("Error de conexión a la base de datos. Por favor, intenta más tarde.");
        
        //mensaje de error real (temporal)
        /*
        die("Error de conexión: " . $e->getMessage());
        */
    }
}
?>