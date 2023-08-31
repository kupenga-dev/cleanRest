<?php

namespace src\Controller;

use Psr\Http\Message\ResponseInterface;
use src\Collaction\UserCollection;
use src\CRUD\UserService;
use src\Database\DatabaseConnection;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $database = new DatabaseConnection();
        $this->userService = new UserService($database);
    }
    public function getUsers(): UserCollection
    {
        return $this->userService->getUsers();
    }
    public function show($vars): ResponseInterface
    {
        $userId = $vars['id'];
        // Обработка GET /users/{id}
    }
    public function store($vars): ResponseInterface
    {
        // Обработка POST /users
    }
    public function update($vars): ResponseInterface
    {
        $userId = $vars['id'];
        // Обработка PUT /users/{id}
    }
    public function partialUpdate($vars): ResponseInterface
    {
        $userId = $vars['id'];
        // Обработка PUT /users/{id}
    }
    public function destroy($vars): ResponseInterface
    {
        $userId = $vars['id'];
        // Обработка DELETE /users/{id}
    }
}