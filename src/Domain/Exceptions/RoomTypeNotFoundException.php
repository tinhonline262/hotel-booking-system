<?php
namespace App\Domain\Exceptions;

use Exception;
class RoomTypeNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Room type with ID {$id} not found.");
    }
}