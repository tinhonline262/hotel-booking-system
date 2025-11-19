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
    public function FilterBookingByCode(string $code):Booking;
    public function FilterBookingByDayByDay(string $start, string $end):array;
    public function FilterBookingByEmail(string $email):Booking;
    public function FilterBookingByName(string $name):array;
    public function FilterBookingByPhone(string $phone):Booking;
    public function FilterBookingByStatus(string $status):array;
    public function GetAllBooking():array;
    public function GetBookingById(int $id) :Booking;
    public function UpdateBooking(int $id, BookingDTO $bookingDTO) :bool;

}