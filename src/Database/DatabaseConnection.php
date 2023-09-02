<?php

namespace src\Database;

use PDO;
use PDOException;
use PDOStatement;
use src\Exception\DatabaseException;

class DatabaseConnection implements DatabaseConnectionInterface
{
    private PDO $pdo;
    /**
     * @throws DatabaseException
     */
    public function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPassword)
    {
        $this->initConnection($dbHost, $dbName, $dbUser, $dbPassword);
    }
    /**
     * @throws DatabaseException
     */
    public function initConnection(string $dbHost, string $dbName, string $dbUser, string $dbPassword): void
    {
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
    public function executeQuery(string $query, ?array $params): ?PDOStatement
    {
        if (!isset($params)){
            return null;
        }
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            throw new DatabaseException("Database query failed:", 0, $e);
        }
    }
}