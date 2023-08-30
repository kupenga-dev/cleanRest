<?php

namespace src\Database;
class DatabaseConnection
{
    private PDO $pdo;

    protected function __construct(string $host, string $dbname, string $user, string $password)
    {
        $this->pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    }

    protected function executeQuery($query, $params = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}