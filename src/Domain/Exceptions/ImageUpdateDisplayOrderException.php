<?php

namespace App\Domain\Exceptions;

class ImageUpdateDisplayOrderException extends \Exception
{
    private array $errors = [];

    public function __construct(array $errors = [], string $message = "Update display order image failed", int $code = 0, ?\Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

