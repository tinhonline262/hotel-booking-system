<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Booking;
interface BookingRepositoryInterface
{
    public function save(Booking $booking): bool;

    public function update(Booking $booking, int $id): bool;

    public function delete(int $id): bool;

    public function findById(int $id): ?Booking;

    public function findAll(): array;

    public function findByCheckInDate(string $date): array;

    public function findByCheckOutDate(string $date): array;

    public function findByStatus(string $status): array;

    public function findByCode(string $code): array;

    public function exists(int $id): bool;

    public function filterDayByDay(string $start, string $end): array;

    public function findByPhone(string $phone): array;

    public function findByEmail(string $email): array;

    public function findByName(string $name): array;
}