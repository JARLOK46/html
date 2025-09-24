<?php
/**
 * Archivo de conexión a la base de datos
 * Este archivo establece la conexión con MySQL usando PDO
 */

// Configuración de la base de datos
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';  // Servidor de base de datos
$DB_NAME = getenv('DB_NAME') ?: 'mercado_campesino';  // Nombre de la base de datos
$DB_USER = getenv('DB_USER') ?: 'root';  // Usuario de MySQL
$DB_PASS = getenv('DB_PASS') ?: '';  // Contraseña de MySQL
$DB_CHARSET = 'utf8mb4';  // Codificación de caracteres

try {
    // Crear la conexión PDO
    // PDO es más seguro que mysqli porque previene inyecciones SQL
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Mostrar errores como excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Devolver arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false,  // Usar prepared statements reales
    ]);
    
    // Mensaje de éxito (solo para desarrollo, quitar en producción)
    // echo "Conexión exitosa a la base de datos";
    
} catch (PDOException $e) {
    // Manejo de errores de conexión
    die("Error de conexión: " . $e->getMessage());
}

?>