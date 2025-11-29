<?php

namespace App\Application\Interfaces;
use App\Domain\Entities\Booking;
use App\Application\DTOs\BookingDTO;
interface BookingServiceInterface
{
    public function CreateBooking(BookingDTO $bookingDTO) :bool;
    public function DeleteBooking(int $id) :bool;
    public function FilterBookingByCheckInDate(string $checkInDate):array;
    public function FilterBookingByCheckOutDate(string $checkOutDate):array;
    public function FilterBookingByCode(string $code):array;
    public function FilterBookingByDayByDay(string $start, string $end):array;
    public function FilterBookingByEmail(string $email):array;
    public function FilterBookingByName(string $name):array;
    public function FilterBookingByPhone(string $phone):array;
    public function FilterBookingByStatus(string $status):array;
    public function GetAllBooking():array;
    public function GetBookingById(int $id) :Booking;
    public function GetBookingByCode(string $code): ?array;
    public function UpdateBooking(int $id, BookingDTO $bookingDTO) :bool;
    public function CheckRoomAvailable(int $id, string $checkInDate, string $checkOutDate):bool;

}