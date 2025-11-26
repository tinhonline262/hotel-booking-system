<?php

namespace App\Presentation\Controllers\Api;
use App\Application\DTOs\BookingDTO;
use App\Application\Interfaces\BookingServiceInterface;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Domain\Exceptions\InvalidBookingDataException;
class BookingController extends BaseRestController
{
    private BookingServiceInterface $bookingService;
    public function __construct(BookingServiceInterface $bookingService){
        parent::__construct();
        $this->bookingService = $bookingService;
    }

    public function booking():void{
        try{
            $data = $this->getJsonInput();

            // Handle amenities conversion
            $dto = BookingDTO::fromArray($data);
            $result = $this->bookingService->CreateBooking($dto);

            $this->created(
                ['success' => $result],
                'Booking created successfully'
            );
        } catch (InvalidBookingDataException $e) {
            $this->validationError($e->getErrors(),'validate fail');
        } catch (\Exception $e){
            $this->serverError('Failed to create room ' . $e->getMessage());
        }
    }

    public function check(int $id):void{
        try{
            $checkInDate = $_GET['checkInDate'] ?? null;
            $checkOutDate = $_GET['checkOutDate'] ?? null;
            $check = $this->bookingService->CheckRoomAvailable($id, $checkInDate, $checkOutDate);
            $this->success(
                ['success' => $check],
                'Check room available successfully'
            );
        } catch (\Exception $e){
            $this->serverError('Failed to check available' . $e->getMessage());
        }
    }
}