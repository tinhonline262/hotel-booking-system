<?php

namespace App\Domain\Exceptions;

class BookingNotFoundException extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct("BookingController with ID {$id} not found.");
    }
}