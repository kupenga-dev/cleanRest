<?php

namespace src\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\Database\DatabaseConnection;
use src\Entity\User;
use src\Service\UserService;
use src\Validation\Validator;

class UserController
{
    private UserService $userService;
    private Validator $validator;

    public function __construct()
    {
        $database = new DatabaseConnection();
        $this->userService = new UserService($database);
        $this->validator = new Validator();
    }
    public function getUsers(): Response
    {
        $userCollection = $this->userService->getUsers();
        if (!isset($userCollection)){
            return new Response(404, [], 'Users not found');
        }
        $userCollection = $userCollection->toArray();
        $responseData = [];
        foreach ($userCollection as $user) {
            $responseData[] = $user->toArray();
        }
        $responseBody = json_encode($responseData);
        return new Response(200, ['Content-Type' => 'application/json'], $responseBody);
    }
    public function show(?string $login): Response
    {
        if (!isset($login)){
            return new Response(404, [], 'Invalid value of login.');
        }
        $user = $this->userService->getUserByLogin($login);
        if (!isset($user)){
            return new Response(404, [], 'User not found');
        }
        $responseBody = json_encode($user->toArray());
        return new Response(200, ['Content-Type' => 'application/json'], $responseBody);
    }
    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!isset($data)){
            return new Response(404, [], 'Invalid query. Data does not exists.');
        }
        if (!$this->validator->validateLogin($data['login'])){
            return new Response(404, [], 'Invalid login. Must me from 5 to 10 letters.');
        }
        $userExists = $this->userService->getUserByLogin($data['login']);
        if (isset($userExists)){
            return new Response(404, [], 'User with the same login found. Edit the login.');
        }
        if (!$this->validator->validateEmail($data['email'])){
            return new Response(404, [], 'Invalid email.');
        }
        if (!$this->validator->validatePhone($data['phone'])){
            return new Response(404, [], 'Invalid value of phone.');
        }
        $user = new User();
        $user->setLogin($data['login']);
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setName($data['name']);
        $isCreated = $this->userService->createUser($user);
        if (!$isCreated){
            return new Response(404, [], 'Something went wrong. Try later.');
        }
        return new Response(200, [], 'User successfully created.');
    }
    public function update(?string $login, ServerRequestInterface $request): ResponseInterface
    {
        if ($this->validator->validateLogin($login)){
            return new Response(404, [], 'Invalid value of login.');
        }
        $userExists = $this->userService->getUserByLogin($login);
        if (isset($userExists)){
            return new Response(404, [], 'User with the same login not found. Edit the login.');
        }
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->validator->validateEmail($data['email'])){
            return new Response(404, [], 'Invalid email.');
        }
        if (!$this->validator->validatePhone($data['phone'])){
            return new Response(404, [], 'Invalid value of phone.');
        }
        $user = new User();
        $user->setLogin($login);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        isset($data['name']) ? $user->setName($data['name']) : $user->setName(null);
        $updatedUser = $this->userService->updateUser($user);
        if (!$updatedUser){
            return new Response(404, [], 'Something went wrong. Try later.');
        }
        return new Response(200, [], 'User successfully created.');
    }
    public function partialUpdate(?string $login, ServerRequestInterface $request): ResponseInterface
    {
        if ($this->validator->validateLogin($login)){
            return new Response(404, [], 'Invalid value of login.');
        }
        $userExists = $this->userService->getUserByLogin($login);
        if (isset($userExists)){
            return new Response(404, [], 'User with the same login not found. Edit the login.');
        }
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->validator->validateEmail($data['email'])){
            return new Response(404, [], 'Invalid email.');
        }
        if (!$this->validator->validatePhone($data['phone'])){
            return new Response(404, [], 'Invalid value of phone.');
        }
        $user = new User();
        $user->setLogin($login);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        if (isset($data['name'])){
            $user->setName($data['name']);
        }
        $updatedUser = $this->userService->partialUpdateUser($user);
        if (!$updatedUser){
            return new Response(404, [], 'Something went wrong. Try later.');
        }
        return new Response(200, [], 'User successfully created.');
    }
    public function destroy(?string $login): ResponseInterface
    {
        if (!isset($login)){
            return new Response(404, [], 'Invalid value of login.');
        }
        $userDeleted = $this->userService->deleteUser($login);
        if (!$userDeleted){
            return new Response(404, [], 'User not found.');
        }
        return new Response(200, [], 'User successfully delete.');
    }
}