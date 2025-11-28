<?php

namespace App\Domain\Exceptions;

class InvalidRoomDataExceptions extends \Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $message = "Invalid room data: " . implode(', ', $errors);
        parent::__construct($message, 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
