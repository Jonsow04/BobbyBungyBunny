<?php
// includes/helpers/sanitize.php

/**
 * Clase para sanitización y validación de datos
 */
class SanitizeHelper {
    
    /**
     * Limpiar entrada de texto (evita XSS)
     */
    public static function limpiarTexto($input) {
        if ($input === null || $input === '') {
            return '';
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitizar email
     */
    public static function sanitizarEmail($email) {
        $email = trim($email);
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validar email
     */
    public static function validarEmail($email) {
        $email = self::sanitizarEmail($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitizar número entero
     */
    public static function sanitizarInt($input) {
        return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Validar número entero positivo
     */
    public static function validarInt($input, $min = 0, $max = null) {
        $int = filter_var($input, FILTER_VALIDATE_INT);
        if ($int === false || $int < $min) {
            return false;
        }
        if ($max !== null && $int > $max) {
            return false;
        }
        return $int;
    }
    
    /**
     * Sanitizar número flotante (precios)
     */
    public static function sanitizarFloat($input) {
        $input = str_replace(',', '.', $input);
        return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Validar número flotante positivo (precio)
     */
    public static function validarFloat($input, $min = 0) {
        $float = filter_var($input, FILTER_VALIDATE_FLOAT);
        if ($float === false || $float < $min) {
            return false;
        }
        return $float;
    }
    
    /**
     * Sanitizar URL
     */
    public static function sanitizarUrl($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
    
    /**
     * Validar URL
     */
    public static function validarUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Sanitizar texto para nombre (solo letras, espacios y caracteres especiales básicos)
     */
    public static function sanitizarNombre($nombre) {
        $nombre = trim($nombre);
        $nombre = preg_replace('/[^a-zA-ZáéíóúñÁÉÍÓÚÑ\s]/', '', $nombre);
        return htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitizar código postal (solo números)
     */
    public static function sanitizarCP($cp) {
        return preg_replace('/[^0-9]/', '', $cp);
    }
    
    /**
     * Sanitizar teléfono (solo números)
     */
    public static function sanitizarTelefono($telefono) {
        return preg_replace('/[^0-9]/', '', $telefono);
    }
    
    /**
     * Sanitizar dirección (permite más caracteres)
     */
    public static function sanitizarDireccion($direccion) {
        $direccion = trim($direccion);
        return htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Prevenir inyección SQL (escape básico)
     */
    public static function escapeSQL($input) {
        return addslashes($input);
    }
    
    /**
     * Validar fecha en formato Y-m-d
     */
    public static function validarFecha($fecha, $formato = 'Y-m-d') {
        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }
    
    /**
     * Validar que la fecha sea mayor de edad (18 años)
     */
    public static function validarEdadMinima($fecha, $minEdad = 18) {
        if (!self::validarFecha($fecha)) {
            return false;
        }
        $fechaNac = new DateTime($fecha);
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNac)->y;
        return $edad >= $minEdad;
    }
    
    /**
     * Sanitizar array completo de entrada POST o GET
     */
    public static function sanitizarArray($array, $camposObligatorios = []) {
        $sanitizado = [];
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitizado[$key] = self::sanitizarArray($value);
            } else {
                $sanitizado[$key] = self::limpiarTexto($value);
            }
        }
        
        // Verificar campos obligatorios
        $faltantes = [];
        foreach ($camposObligatorios as $campo) {
            if (!isset($sanitizado[$campo]) || $sanitizado[$campo] === '') {
                $faltantes[] = $campo;
            }
        }
        
        if (!empty($faltantes)) {
            return ['error' => true, 'faltantes' => $faltantes, 'data' => $sanitizado];
        }
        
        return ['error' => false, 'data' => $sanitizado];
    }
}
?>