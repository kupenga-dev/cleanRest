<?php

namespace src\Database;

interface DatabaseConnectionInterface
{
    public function executeQuery(string $query, ?array $params);
}