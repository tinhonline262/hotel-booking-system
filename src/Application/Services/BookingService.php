<?php

namespace App\Application\Services;
use App\Application\DTOs\BookingDTO;
use App\Application\Interfaces\BookingServiceInterface;
use App\Domain\Entities\Booking;
use App\Domain\Exceptions\InvalidBookingDataException;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Application\UseCases\CreateBookingUseCase;
use App\Application\UseCases\DeleteBookingUseCase;
use App\Application\UseCases\UpdateBookingUseCase;
use App\Application\UseCases\GetBookingUseCase;
use App\Application\UseCases\GetAllBookingUseCase;
use App\Application\UseCases\FilterBookingByCheckInDateUseCase;
use App\Application\UseCases\FilterBookingByCheckOutDateUseCase;
use App\Application\UseCases\FilterBookingByCodeUseCase;
use App\Application\UseCases\FilterBookingByDayByDayUseCase;
use App\Application\UseCases\FilterBookingByEmailUseCase;
use App\Application\UseCases\FilterBookingByName;
use App\Application\UseCases\FilterBookingByPhoneUseCase;
use App\Application\UseCases\FilterBookingByStatus;
class BookingService implements BookingServiceInterface
{
    private CreateBookingUseCase  $createBookingUseCase;
    private UpdateBookingUseCase $updateBookingUseCase;
    private DeleteBookingUseCase $deleteBookingUseCase;
    private GetBookingUseCase $getBookingUseCase;
    private GetAllBookingUseCase $getAllBookingUseCase;
    private FilterBookingByCheckInDateUseCase  $filterBookingByCheckInDateUseCase;
    private FilterBookingByCheckOutDateUseCase  $filterBookingByCheckOutDateUseCase;
    private FilterBookingByCodeUseCase  $filterBookingByCodeUseCase;
    private FilterBookingByDayByDayUseCase  $filterBookingByDayUseCase;
    private FilterBookingByEmailUseCase  $filterBookingByEmailUseCase;
    private FilterBookingByPhoneUseCase  $filterBookingByPhoneUseCase;
    private FilterBookingByName  $filterBookingByName;
    private FilterBookingByStatus  $filterBookingByStatus;

    public function __construct(CreateBookingUseCase $createBookingUseCase,
    UpdateBookingUseCase $updateBookingUseCase,
    DeleteBookingUseCase $deleteBookingUseCase,
    GetBookingUseCase $getBookingUseCase,
    GetAllBookingUseCase $getAllBookingUseCase,
    FilterBookingByCheckInDateUseCase  $filterBookingByCheckInDateUseCase,
    FilterBookingByCheckOutDateUseCase  $filterBookingByCheckOutDateUseCase,
    FilterBookingByCodeUseCase  $filterBookingByCodeUseCase,
    FilterBookingByDayByDayUseCase  $filterBookingByDayUseCase,
    FilterBookingByEmailUseCase  $filterBookingByEmailUseCase,
    FilterBookingByPhoneUseCase  $filterBookingByPhoneUseCase,
    FilterBookingByName  $filterBookingByName,
    FilterBookingByStatus  $filterBookingByStatus
    ){
        $this->createBookingUseCase = $createBookingUseCase;
        $this->updateBookingUseCase = $updateBookingUseCase;
        $this->deleteBookingUseCase = $deleteBookingUseCase;
        $this->getBookingUseCase = $getBookingUseCase;
        $this->getAllBookingUseCase = $getAllBookingUseCase;
        $this->filterBookingByCheckInDateUseCase = $filterBookingByCheckInDateUseCase;
        $this->filterBookingByCheckOutDateUseCase = $filterBookingByCheckOutDateUseCase;
        $this->filterBookingByCodeUseCase = $filterBookingByCodeUseCase;
        $this->filterBookingByDayUseCase = $filterBookingByDayUseCase;
        $this->filterBookingByEmailUseCase = $filterBookingByEmailUseCase;
        $this->filterBookingByPhoneUseCase = $filterBookingByPhoneUseCase;
        $this->filterBookingByName = $filterBookingByName;
        $this->filterBookingByStatus = $filterBookingByStatus;
    }

    /**
     * @throws InvalidBookingDataException
     */
    public function CreateBooking(BookingDTO $bookingDTO): bool
    {
        // TODO: Implement CreateBooking() method.
        return $this->createBookingUseCase->Execute($bookingDTO);
    }

    /**
     * @throws BookingNotFoundException
     */
    public function DeleteBooking(int $id): bool
    {
        // TODO: Implement DeleteBooking() method.
        return $this->deleteBookingUseCase->Execute($id);
    }

    public function FilterBookingByCheckInDate(string $checkInDate): array
    {
        // TODO: Implement FilterBookingByCheckInDate() method.
        return $this->filterBookingByCheckInDateUseCase->Execute($checkInDate);
    }

    public function FilterBookingByCheckOutDate(string $checkOutDate): array
    {
        // TODO: Implement FilterBookingByCheckOutDate() method.
        return $this->filterBookingByCheckOutDateUseCase->Execute($checkOutDate);
    }

    public function FilterBookingByCode(string $code): array
    {
        // TODO: Implement FilterBookingByCode() method.
        return $this->filterBookingByCodeUseCase->Execute($code);
    }

    public function FilterBookingByDayByDay(string $start, string $end): array
    {
        // TODO: Implement FilterBookingByDayByDay() method.
        return $this->filterBookingByDayUseCase->Execute($start, $end);
    }

    public function FilterBookingByEmail(string $email): array
    {
        // TODO: Implement FilterBookingByEmail() method.
        return $this->filterBookingByEmailUseCase->Execute($email);
    }

    public function FilterBookingByName(string $name): array
    {
        // TODO: Implement FilterBookingByName() method.
        return $this->filterBookingByName->Execute($name);
    }

    public function FilterBookingByPhone(string $phone): array
    {
        // TODO: Implement FilterBookingByPhone() method.
        return $this->filterBookingByPhoneUseCase->Execute($phone);
    }

    public function FilterBookingByStatus(string $status): array
    {
        // TODO: Implement FilterBookingByStatus() method.
        return $this->filterBookingByStatus->Execute($status);
    }

    public function GetAllBooking(): array
    {
        // TODO: Implement GetAllBooking() method.
        return $this->getAllBookingUseCase->Execute();
    }

    /**
     * @throws BookingNotFoundException
     */
    public function GetBookingById(int $id): Booking
    {
        // TODO: Implement GetBookingById() method.
        return $this->getBookingUseCase->Execute($id);
    }

    /**
     * @throws InvalidBookingDataException
     * @throws BookingNotFoundException
     */
    public function UpdateBooking(int $id, BookingDTO $bookingDTO): bool
    {
        // TODO: Implement UpdateBooking() method.
        return $this->updateBookingUseCase->Execute($bookingDTO,$id);
    }
}