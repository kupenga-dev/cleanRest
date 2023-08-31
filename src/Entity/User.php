<?php

namespace src\Entity;

class User
{
    private ?int $id;
    private ?string $name;
    private ?string $login;
    private ?string $phone;
    private ?string $email;
    public function __construct(
        ?int $id = null, ?string $name = null, ?string $login = null,
        ?string $email = null, ?string $phone = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->login = $login;
        $this->email = $email;
        $this->phone = $phone;
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLogin()
    {
        return $this->login;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'login' => $this->login,
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }
}