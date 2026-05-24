<?php
session_start();

// Destruir todas las variables de sesión
session_unset();
session_destroy();

// Guardar mensaje de éxito
session_start(); // Iniciar nueva sesión para el mensaje
$_SESSION['logout_exitoso'] = true;

// Redirigir al login
header('Location: login.php');
exit();
?>