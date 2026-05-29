<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['contrasena'] ?? '';

    $errores = [];
    
    // Validar que los campos no estén vacíos
    if (empty($email)) {
        $errores[] = "El correo electrónico es obligatorio";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria";
    }
    
    // Si hay errores, guardar en sesión y redirigir
    if (!empty($errores)) {
        $_SESSION['errores_login'] = $errores;
        $_SESSION['datos_login'] = ['email' => $email];
        header('Location: login.php');
        exit();
    }
    
    try {
        $pdo = getConnection();
        
        // Buscar usuario por email
        $stmt = $pdo->prepare("SELECT u.idUsuario, u.nombre, u.email, u.password_hash, u.idTipoUsuario, tu.nombre as tipo_nombre 
                               FROM usuario u 
                               INNER JOIN tipousuario tu ON u.idTipoUsuario = tu.idTipoUsuario 
                               WHERE u.email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 0) {
            $_SESSION['errores_login'] = ["No existe una cuenta asociada a este correo electrónico"];
            $_SESSION['datos_login'] = ['email' => $email];
            header('Location: login.php');
            exit();
        }
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar contraseña
        if (!password_verify($password, $usuario['password_hash'])) {
            $_SESSION['errores_login'] = ["Contraseña incorrecta"];
            $_SESSION['datos_login'] = ['email' => $email];
            header('Location: login.php');
            exit();
        }

        // Login exitoso - guardar datos en sesión
        $_SESSION['usuario_id'] = $usuario['idUsuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_tipo'] = $usuario['idTipoUsuario'];
        $_SESSION['usuario_tipo_nombre'] = $usuario['tipo_nombre'];
        $_SESSION['login_exitoso'] = true;

        // Sincronizar carrito invitado si existe
        if (isset($_SESSION['carrito_invitado']) && !empty($_SESSION['carrito_invitado'])) {
            if (file_exists('../includes/controllers/carritoController.php')) {
                require_once '../includes/controllers/carritoController.php';
                $carritoController = new CarritoController($pdo);
                $carritoController->sincronizarConUsuario($usuario['idUsuario'], $pdo);
            }
        }
        
        // Redirigir según el tipo de usuario (sin necesidad de selector)
        // idTipoUsuario: 1=Administrador, 2=Gerente, 3=Cliente
        if ($usuario['idTipoUsuario'] == 1 || $usuario['idTipoUsuario'] == 2) {
            // Administrador o Gerente
            header('Location: admin/dashboard.php');
        } else {
            // Cliente normal
            header('Location: index.php');
        }
        exit();
        
    } catch (PDOException $e) {
        error_log("Error en login: " . $e->getMessage());
        $_SESSION['errores_login'] = ["Ocurrió un error al iniciar sesión. Por favor, intenta de nuevo."];
        $_SESSION['datos_login'] = ['email' => $email];
        header('Location: login.php');
        exit();
    }
    
} else {
    // Si no es POST, redirigir al formulario
    header('Location: login.php');
    exit();
}
?>