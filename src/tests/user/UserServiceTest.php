<?php

namespace user;

use PHPUnit\Framework\TestCase;
use src\Database\DatabaseConnectionInterface;
use src\Entity\User;
use src\Service\UserService;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    protected function setUp(): void
    {
        $this->databaseConnection = $this->createMock(DatabaseConnectionInterface::class);
        $this->userService = new UserService($this->databaseConnection);
    }
    protected function configureMocks(): void
    {
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->method('rowCount')->willReturn(1);

        $this->databaseConnection
            ->expects($this->once())
            ->method('executeQuery')
            ->willReturn($mockStatement);
    }
    public function testCreateUser(): void
    {
        $this->configureMocks();
        $user = new User();
        $user->setName('John Doe');
        $user->setLogin('johndoe');
        $user->setEmail('johndoe@example.com');
        $user->setPhone('+1234567890');
        $result = $this->userService->createUser($user);
        $this->assertTrue($result);
    }
    public function testUpdateUser(): void
    {
        $this->configureMocks();
        $user = new User();
        $user->setName('John Doe');
        $user->setLogin('johndoe');
        $user->setEmail('johndoe@example.com');
        $user->setPhone('+7234567890');
        $result = $this->userService->updateUser($user);
        $this->assertTrue($result);
    }
    public function testPartialUpdateUser(): void
    {
        $this->configureMocks();
        $user = new User();
        $user->setLogin('johndoe');
        $user->setEmail('johndoe@example.com');
        $result = $this->userService->updateUser($user);
        $this->assertTrue($result);
    }
    public function testGetUserByLogin(): void
    {
        $userRow = [
            'id' => 1,
            'name' => 'John Doe',
            'login' => 'johndoe',
            'email' => 'johndoe@example.com',
            'phone' => '+1234567890',
        ];
        $mockResult = $this->createMock(\PDOStatement::class);
        $mockResult->expects($this->once())
            ->method('fetch')
            ->willReturn($userRow);

        $this->databaseConnection
            ->expects($this->once())
            ->method('executeQuery')
            ->willReturn($mockResult);
        $user = $this->userService->getUserByLogin('johndoe');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userRow['name'], $user->getName());
        $this->assertEquals($userRow['login'], $user->getLogin());
        $this->assertEquals($userRow['email'], $user->getEmail());
        $this->assertEquals($userRow['phone'], $user->getPhone());
    }
    public function testDeleteUser(): void
    {
        $this->configureMocks();
        $result = $this->userService->deleteUser('johndoe');
        $this->assertTrue($result);
    }
}