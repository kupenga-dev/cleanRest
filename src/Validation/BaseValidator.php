<?php

namespace src\Validation;

class BaseValidator
{
    private string $emailPattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
    private string $phonePattern = '/^(?:\+7|8)?[0-9]{10}$/';
    public function isEmailCorrect(string $email): bool
    {
        return preg_match($this->emailPattern, $email) === 1;
    }
    protected function validateStringLength(string $string, int $minLength, int $maxLength): bool
    {
        $length = strlen($string);
        return $length >= $minLength && $length <= $maxLength;
    }
    public function isCorrectNumber(string $phoneNumber): bool
    {
        return preg_match($this->phonePattern, $phoneNumber) === 1;
    }
}