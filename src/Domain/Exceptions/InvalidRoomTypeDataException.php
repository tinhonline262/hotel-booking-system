<?php

namespace App\Domain\Exceptions;

class InvalidRoomTypeDataException extends \Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $message = "Invalid room type data: " . implode(', ', $errors);
        parent::__construct($message, 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

