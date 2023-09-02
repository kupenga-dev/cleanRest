<?php

namespace src\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\Database\DatabaseConnection;
use src\Entity\User;
use src\Exception\DatabaseException;
use src\Service\UserService;
use src\Validation\Validator;

class UserController
{
    private UserService $userService;
    private Validator $validator;

    /**
     * @throws DatabaseException
     */
    public function __construct()
    {
        $dbHost = $_ENV['DB_DATABASE_HOST'];
        $dbName = $_ENV['DB_DATABASE_NAME'];
        $dbUser = $_ENV['DB_USERNAME'];
        $dbPassword = $_ENV['DB_PASSWORD'];
        $database = new DatabaseConnection($dbHost, $dbName, $dbUser, $dbPassword);
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
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->validator->validateLogin($data['login'])){
            return new Response(404, [], 'Invalid value of login.');
        }
        $userExists = $this->userService->getUserByLogin($data['login']);
        if (!isset($userExists)){
            return new Response(404, [], 'User with the same login not found. Edit the login.');
        }
        $user = new User();
        $user->setLogin($data['login']);
        if (isset($data['name'])){
            $user->setName($data['name']);
        } else {
            $user->setName(null);
        }
        if (!isset($data['email']) || !$this->validator->isEmailCorrect($data['email'])){
            $user->setEmail(null);
        } else {
            $user->setEmail($data['email']);
        }
        if (!isset($data['phone']) || !$this->validator->isCorrectNumber($data['phone'])){
            $user->setPhone(null);
        } else {
            $user->setPhone($data['phone']);
        }
        $updatedUser = $this->userService->updateUser($user);
        if (!$updatedUser){
            return new Response(404, [], 'Something went wrong. Try later.');
        }
        return new Response(200, [], 'User successfully updated.');
    }
    public function partialUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->validator->validateLogin($data['login'])){
            return new Response(404, [], 'Invalid value of login.');
        }
        $userExists = $this->userService->getUserByLogin($data['login']);
        if (!isset($userExists)){
            return new Response(404, [], 'User with the same login not found. Edit the login.');
        }
        if (!$this->validator->validatePhone($data['phone'])){
            return new Response(404, [], 'Invalid value of phone.');
        }
        $user = new User();
        $user->setLogin($data['login']);
        if (isset($data['email']) && $this->validator->isEmailCorrect($data['email'])){
            $user->setEmail($data['email']);
        }
        if (isset($data['phone']) && $this->validator->isCorrectNumber($data['phone'])){
            $user->setPhone($data['phone']);
        }
        if (isset($data['name'])){
            $user->setName($data['name']);
        }
        $updatedUser = $this->userService->partialUpdateUser($user);
        if (!$updatedUser){
            return new Response(404, [], 'Something went wrong. Try later.');
        }
        return new Response(200, [], 'User successfully updated.');
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