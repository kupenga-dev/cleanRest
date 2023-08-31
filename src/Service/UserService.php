<?php

namespace src\Service;
use src\Collaction\UserCollection;
use src\Database\DatabaseConnectionInterface;
use src\Entity\User;

class UserService
{
    private DatabaseConnectionInterface $databaseConnection;

    public function __construct(DatabaseConnectionInterface $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }
    public function getUsers(): ?UserCollection
    {
        $query = "SELECT * FROM users";
        $result = $this->databaseConnection->executeQuery($query, []);
        $userCollection = new UserCollection();
        while ($row = $result->fetch()) {
            $user = new User(
                $row['id'],
                $row['name'],
                $row['login'],
                $row['email'],
                $row['phone']
            );
            $userCollection->addUser($user);
        }
        if ($userCollection->isEmpty()){
            return null;
        }
        return $userCollection;
    }
    public function getUserByLogin(string $login): ?User
    {
        $query = "SELECT * FROM users WHERE login = ?";
        $result = $this->databaseConnection->executeQuery($query, [$login]);
        $row = $result->fetch();

        if (!$row) {
            return null;
        }
        return new User(
            $row['id'],
            $row['name'],
            $row['login'],
            $row['email'],
            $row['phone']
        );
    }
    public function createUser(User $user): bool
    {
        $userProperties = get_object_vars($user);
        $fields = [];
        $values = [];

        foreach ($userProperties as $property => $value) {
            if ($property == 'id'){
                continue;
            }
            $fields[] = $property;
            $values[] = $value;
        }
        $fieldsString = implode(', ', $fields);
        $fieldsValues = implode(', ', array_fill(0, count($values), '?'));
        $query = "INSERT INTO users ($fieldsString) VALUES ($fieldsValues)";
        $rowCount = $this->databaseConnection->executeQuery($query, $values);
        return $rowCount > 0;
    }
    public function updateUser(User $user): bool
    {
        $userProperties = get_object_vars($user);
        $updateFields = [];
        $values = [];

        foreach ($userProperties as $property => $value) {
            if ($property === 'id') {
                continue;
            }
            $updateFields[] = "$property = ?";
            $values[] = $value;
        }

        $values[] = $user->getLogin();
        $updateFieldsString = implode(', ', $updateFields);

        $query = "UPDATE users SET $updateFieldsString WHERE login = ?";
        $rows = $this->databaseConnection->executeQuery($query, $values);
        return $rows > 0;
    }
    public function partialUpdateUser(User $user): bool
    {
        $userProperties = get_object_vars($user);
        $updateFields = [];
        $values = [];

        foreach ($userProperties as $property => $value) {
            if (empty($value)){
                continue;
            }
            if ($property === 'id') {
                continue;
            }
            $updateFields[] = "$property = ?";
            $values[] = $value;
        }

        $values[] = $user->getLogin();
        $updateFieldsString = implode(', ', $updateFields);

        $query = "UPDATE users SET $updateFieldsString WHERE login = ?";
        $rows = $this->databaseConnection->executeQuery($query, $values);
        return $rows > 0;
    }
    public function deleteUser(string $login): bool
    {
        $query = "DELETE FROM users WHERE login = ?";
        $rowsCount = $this->databaseConnection->executeQuery($query, [$login]);
        if ($rowsCount > 0){
            return true;
        }
        return false;
    }
}