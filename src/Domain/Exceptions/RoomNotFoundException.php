<?php
namespace App\Domain\Exceptions;

use Exception;
class RoomNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Room with ID {$id} not found.");
    }
}