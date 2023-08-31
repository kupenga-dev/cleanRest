<?php

namespace src\Validation;

class Validator extends BaseValidator
{
    public function validateLogin(?string $login): bool
    {
        if (!isset($login)){
            return false;
        }
        return parent::validateStringLength($login, 5, 10);
    }
    public function validateEmail(?string $email): bool
    {
        if (!isset($email)){
            return false;
        }
        return parent::isEmailCorrect($email);
    }
    public function validateName(?string $name): bool
    {
        if (!isset($name)){
            return false;
        }
        return parent::validateStringLength($name, 5, 20);
    }
    public function validatePhone(?string $phone): bool
    {
        if (!isset($phone)){
            return false;
        }
        return parent::isCorrectNumber($phone);
    }
}