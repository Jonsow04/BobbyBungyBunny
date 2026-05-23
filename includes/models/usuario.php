<?php

class Usuario {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT u.*, tu.nombre as tipo_usuario 
                FROM usuario u
                INNER JOIN tipousuario tu ON u.idTipoUsuario = tu.idTipoUsuario
                WHERE u.idUsuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener usuario por nombre de usuario
     */
    public function obtenerPorNomUser($nomUser) {
        $sql = "SELECT u.*, tu.nombre as tipo_usuario 
                FROM usuario u
                INNER JOIN tipousuario tu ON u.idTipoUsuario = tu.idTipoUsuario
                WHERE u.nomUser = :nomUser";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nomUser' => $nomUser]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT u.*, tu.nombre as tipo_usuario 
                FROM usuario u
                INNER JOIN tipousuario tu ON u.idTipoUsuario = tu.idTipoUsuario
                WHERE u.email = :email";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * Verificar credenciales de login
     */
    public function verificarCredenciales($nomUser, $password) {
        $sql = "SELECT idUsuario, nomUser, nombre, email, password_hash, idTipoUsuario 
                FROM usuario WHERE nomUser = :nomUser";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nomUser' => $nomUser]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            unset($usuario['password_hash']);
            return $usuario;
        }
        
        return false;
    }
    
    /**
     * Obtener direcciones de un usuario
     */
    public function obtenerDirecciones($usuarioId) {
        $sql = "SELECT d.* FROM direccion d
                INNER JOIN usuario u ON u.idDireccion = d.idDireccion
                WHERE u.idUsuario = :usuarioId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $stmt->fetchAll();
    }
}
?>