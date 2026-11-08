<?php
/**
 * AsistePro - Configuración de Base de Datos
 * 
 * Gestiona la conexión a MySQL mediante PDO con patrón Singleton.
 * Para producción, usar variables de entorno en lugar de constantes.
 */

// Credenciales de conexión (valores por defecto de XAMPP)
define('DB_HOST', 'localhost');
define('DB_NAME', 'asistepro_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Contraseña vacía por defecto en XAMPP
define('DB_CHARSET', 'utf8mb4');

/**
 * Clase Database - Patrón Singleton para la conexión PDO
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Obtiene la instancia única de conexión PDO
     *
     * @return PDO Instancia de conexión
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_NAME,
                    DB_CHARSET
                );

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES " . DB_CHARSET,
                ];

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                die("Error de conexión a la base de datos. Contacte al administrador.");
            }
        }

        return self::$instance;
    }

    /** Evita la instanciación directa */
    private function __construct() {}

    /** Evita la clonación */
    private function __clone() {}
}

