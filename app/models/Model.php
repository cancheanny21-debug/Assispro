<?php
/**
 * AsistePro - Modelo Base
 * 
 * Clase abstracta que proporciona acceso a la conexión PDO
 * para todos los modelos del sistema.
 */

abstract class Model
{
    /** @var PDO Conexión a la base de datos */
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ejecuta una consulta preparada y retorna los resultados
     *
     * @param string $sql    Consulta SQL con placeholders
     * @param array  $params Parámetros para la consulta
     * @return array Arreglo de resultados
     */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta preparada y retorna una sola fila
     *
     * @param string $sql    Consulta SQL con placeholders
     * @param array  $params Parámetros para la consulta
     * @return array|false Fila encontrada o false
     */
    protected function queryOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Ejecuta una sentencia preparada (INSERT, UPDATE, DELETE)
     *
     * @param string $sql    Consulta SQL con placeholders
     * @param array  $params Parámetros para la consulta
     * @return int Número de filas afectadas
     */
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Retorna el último ID insertado
     *
     * @return string
     */
    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}

