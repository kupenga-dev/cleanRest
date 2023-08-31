<?php

namespace src\CRUD;
use src\Collaction\UserCollection;
use src\Database\DatabaseConnectionInterface;
use src\Entity\User;

class UserCRUD
{
    private DatabaseConnectionInterface $databaseConnection;

    public function __construct(DatabaseConnectionInterface $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }
    public function createUser(User $user): void
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
        $this->databaseConnection->executeQuery($query, $values);
    }
    public function deleteUser(User $user): void
    {
        $query = "DELETE FROM users WHERE id = ?";
        $this->databaseConnection->executeQuery($query, [$user->getId()]);
    }
    public function updateUser(User $user): void
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

        $values[] = $user->getId();
        $updateFieldsString = implode(', ', $updateFields);

        $query = "UPDATE users SET $updateFieldsString WHERE id = ?";
        $this->databaseConnection->executeQuery($query, $values);
    }
    public function partialUpdateUser(User $user): void
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

        $values[] = $user->getId();
        $updateFieldsString = implode(', ', $updateFields);

        $query = "UPDATE users SET $updateFieldsString WHERE id = ?";
        $this->databaseConnection->executeQuery($query, $values);
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
}