<?php
namespace App\Domain\Repositories; 
interface BookingRepositoryInterface
{
    public function findByCode(string $code): ?array;
}