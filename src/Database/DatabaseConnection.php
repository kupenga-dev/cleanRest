<?php

namespace src\Database;
use PDO;
use PDOException;
use src\Exception\DatabaseException;

class DatabaseConnection implements DatabaseConnectionInterface
{
    private PDO $pdo;
    /**
     * @throws DatabaseException
     */
    public function __construct()
    {
        $this->initConnection();
    }
    /**
     * @throws DatabaseException
     */
    public function initConnection(): void
    {
        $dbHost = $_ENV['DB_DATABASE_HOST'];
        $dbName = $_ENV['DB_DATABASE_NAME'];
        $dbUser = $_ENV['DB_USERNAME'];
        $dbPassword = $_ENV['DB_PASSWORD'];
        try {
            $this->pdo = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new DatabaseException("Database query failed:", 0, $e);
        }
    }
    /**
     * @throws DatabaseException
     */
    public function executeQuery(string $query, ?array $params): void
    {
        if (!isset($params) || $params === []){
            return;
        }
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
        } catch (\PDOException $e) {
            throw new DatabaseException("Database query failed:", 0, $e);
        }
    }
}