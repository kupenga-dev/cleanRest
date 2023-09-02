<?php

namespace src\Service;
use src\Collection\UserCollection;
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
        if (!$result){
            return null;
        }
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
        $fields = ['name', 'login', 'email', 'phone'];
        $placeholders = [];
        $values = [];
        foreach ($fields as $field) {
            $getter = 'get' . ucfirst($field);
            $value = $user->$getter();
            $placeholders[] = "?";
            $values[] = $value;
        }
        $fieldsString = implode(', ', $fields);
        $placeholdersString = implode(', ', $placeholders);
        $query = "INSERT INTO users ($fieldsString) VALUES ($placeholdersString)";
        $dbStatement = $this->databaseConnection->executeQuery($query, $values);
        return $dbStatement->rowCount() > 0;
    }
    public function updateUser(User $user): bool
    {
        $fields = ['name', 'email', 'phone'];
        $updateFields = [];
        $values = [];

        foreach ($fields as $field) {
            if ($field === 'id') {
                continue;
            }
            $getter = 'get' . ucfirst($field);
            $value = $user->$getter();
            $updateFields[] = "$field = ?";
            $values[] = $value;
        }

        $values[] = $user->getLogin();
        $updateFieldsString = implode(', ', $updateFields);

        $query = "UPDATE users SET $updateFieldsString WHERE login = ?";
        $dbStatement = $this->databaseConnection->executeQuery($query, $values);
        return $dbStatement->rowCount() > 0;
    }
    public function partialUpdateUser(User $user): bool
    {
        $fields = ['name', 'email', 'phone'];
        $updateFields = [];
        $values = [];

        foreach ($fields as $field) {
            if ($field === 'id') {
                continue;
            }
            $getter = 'get' . ucfirst($field);
            $value = $user->$getter();
            if (!isset($value)){
                continue;
            }
            $updateFields[] = "$field = ?";
            $values[] = $value;
        }

        $values[] = $user->getLogin();
        $updateFieldsString = implode(', ', $updateFields);

        $query = "UPDATE users SET $updateFieldsString WHERE login = ?";
        $dbStatement = $this->databaseConnection->executeQuery($query, $values);
        return $dbStatement->rowCount() > 0;
    }
    public function deleteUser(string $login): bool
    {
        $query = "DELETE FROM users WHERE login = ?";
        $dbStatement = $this->databaseConnection->executeQuery($query, [$login]);
        return $dbStatement->rowCount() > 0;
    }
}