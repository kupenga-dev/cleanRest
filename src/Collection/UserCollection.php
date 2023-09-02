<?php

namespace src\Collection;

use src\Entity\User;

class UserCollection
{
    private array $users;
    public function __construct(array $users = [])
    {
        $this->users = $users;
    }
    public function addUser(User $user): void
    {
        $this->users[] = $user;
    }
    public function toArray(): array
    {
        return $this->users;
    }
    public function isEmpty(): bool
    {
        if ($this->users === []){
            return true;
        }
        return false;
    }
}