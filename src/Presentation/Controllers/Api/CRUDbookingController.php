<?php

namespace App\Presentation\Controllers\Api;
use App\Application\DTOs\BookingDTO;
use App\Application\Interfaces\BookingServiceInterface;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Domain\Exceptions\InvalidBookingDataException;
class CRUDbookingController extends BaseRestController
{
    private BookingServiceInterface $bookingService;
    public function __construct(BookingServiceInterface $bookingService){
        parent::__construct();
        $this->bookingService = $bookingService;
    }
    /**
     * GET /api/bookings
     * Get all bookings
     */
    public function index():void{
        try{
            $bookings = $this->bookingService->GetAllBooking();
            $this->success(
                array_map(fn($rt)=>$rt->toArray(),$bookings),
                "Rooms retrieved successfully"
            );
        }
        catch (\Exception $e){
            $this->serverError('Failed to retrieve bookings ' . $e->getMessage());
        }
    }

    /**
     * GET /api/booking/{id}
     * Get single booking by ID
     */
    public function show(int $id):void{
        try{
            $booking = $this->bookingService->GetBookingById($id);
            $this->success(
                $booking->toArray(),
                "Booking retrieved successfully"
            );
        } catch(BookingNotFoundException $e){
            $this->notFound($e->getMessage());
        } catch (\Exception $e){
            $this->serverError('Failed to retrieve booking ' . $e->getMessage());
        }
    }

/**
 * PUT /api/bookings/{id}
 * Update existing booking (full update hoặc chỉ status)
 */
public function update(int $id): void
{
    try {
        $data = $this->getJsonInput();

        // CASE 1: Chỉ update status (check-in/check-out từ dashboard)
        if (isset($data['status']) && count($data) === 1) {
            // Validate status
            $allowedStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
            if (!in_array($data['status'], $allowedStatuses)) {
                $this->validationError(['status' => 'Invalid status value'], 'Invalid status value');
                return;
            }

            // Lấy booking hiện tại
            $currentBooking = $this->bookingService->GetBookingById($id);

            // Tạo DTO từ data hiện tại + status mới
            $dto = new BookingDTO(
    $currentBooking->getId(),                    // #1: id
    $currentBooking->getBookingCode(),           // #2: bookingCode ← THÊM
    (int) $currentBooking->getRoomId(),          // #3: roomId
    $currentBooking->getCustomerName(),          // #4: customerName
    $currentBooking->getCustomerEmail(),         // #5: customerEmail
    $currentBooking->getCustomerPhone(),         // #6: customerPhone
    $currentBooking->getCheckInDate(),           // #7: checkInDate
    $currentBooking->getCheckOutDate(),          // #8: checkOutDate
    (int) $currentBooking->getNumGuests(),       // #9: numGuests
    (float) $currentBooking->getTotalPrice(),    // #10: totalPrice
    $data['status'],                             // #11: status
    $currentBooking->getSpecialRequests()        // #12: specialRequests
);
        } 
        // CASE 2: Full update (từ form edit booking)
        else {
            $dto = BookingDTO::fromArray($data);
        }

        // Execute update
        $result = $this->bookingService->UpdateBooking($id, $dto);

        $this->success(
            ['success' => $result],
            'Booking updated successfully'
        );

    } catch (BookingNotFoundException $e) {
        $this->notFound($e->getMessage());
    } catch (InvalidBookingDataException $e) {
        $this->validationError($e->getErrors(), 'Validation failed');
    } catch (\Exception $e) {
        $this->serverError('Failed to update booking: ' . $e->getMessage());
    }
}

    /**
     * DELETE /api/booking/{id}
     * Delete booking
     */
    public function delete(int $id): void{
        try {
            $result = $this->bookingService->DeleteBooking($id);

            $this->success(
                ['success' => $result],
                'booking deleted successfully'
            );
        } catch (BookingNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError('Failed to delete booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/filter/checkin?checkin='yyyy-mm-dd'
     * Filter bookings by check in
     */
    public function filterByCheckIn(): void
    {
        try {
            $params = $this->getQueryParams();
            $checkin = ($params['checkin'] ?? '');

            $bookings = $this->bookingService->FilterBookingByCheckInDate($checkin);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'booking filtered by checkin'
            );
        } catch (BookingNotFoundException $e) {
            $this->notFound($e->getMessage());
        }catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/filter/checkout?checkout='yyyy-mm-dd'
     * Filter booking by check out
     */
    public function filterByCheckOut(): void
    {
        try {
            $params = $this->getQueryParams();
            $checkout = ($params['checkout'] ?? '');

            $bookings = $this->bookingService->FilterBookingByCheckOutDate($checkout);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'booking filtered by checkout'
            );
        }catch (BookingNotFoundException $e) {
            $this->notFound($e->getMessage());
        }
        catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }


    /**
     * GET /api/bookings/filter/code?code='BK-01-010101'
     * Filter bookings by code
     */
    public function filterByCode(): void
    {
        try {
            $params = $this->getQueryParams();
            $code = ($params['code'] ?? '');

            $bookings = $this->bookingService->FilterBookingByCode($code);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'Bookings filtered by code'
            );
        } catch (BookingNotFoundException $e) {
            $this->notFound($e->getMessage());
        }
        catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/search/code/{code}
     * Tìm kiếm chính xác một booking theo code
     * Endpoint mới - trả về một booking hoặc not found
     */
    public function findByCode(string $code): void
    {
        try {
            $booking = $this->bookingService->GetBookingByCode($code);

            if (!$booking) {
                $this->notFound("Booking with code '{$code}' not found");
                return;
            }

            $this->success(
                $booking,
                'Booking found successfully'
            );
        } catch (\Exception $e) {
            $this->serverError('Failed to find booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/filter/email?email='index@gmail.com'
     * Filter bookings by email
     */
    public function filterByEmail(): void
    {
        try {
            $params = $this->getQueryParams();
            $email = ($params['email'] ?? '');

            $bookings = $this->bookingService->FilterBookingByEmail($email);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'booking filtered by checkin'
            );
        } catch (BookingNotFoundException $e) {
            $this->notFound($e->getMessage());
        }
        catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/filter/name?name='an'
     * Filter bookings by name
     */
    public function filterByName(): void
    {
        try {
            $params = $this->getQueryParams();
            $name = ($params['name'] ?? '');

            $bookings = $this->bookingService->FilterBookingByName($name);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'booking filtered by checkin'
            );
        }
        catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/filter/phone?phone='0123456789'
     * Filter bookings by phone
     */
    public function filterByPhone(): void
    {
        try {
            $params = $this->getQueryParams();
            $phone = ($params['phone'] ?? '');

            $bookings = $this->bookingService->FilterBookingByPhone($phone);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'booking filtered by checkin'
            );
        } catch (BookingNotFoundException $e) {
            $this->notFound($e->getMessage());
        }
        catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/bookings/filter/status?status='pending'
     * Filter bookings by status
     */
    public function filterByStatus(): void
    {
        try {
            $params = $this->getQueryParams();
            $status = ($params['status'] ?? 'pending');

            $bookings = $this->bookingService->FilterBookingByStatus($status);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $bookings),
                'booking filtered by checkin'
            );
        } catch (\Exception $e) {
            $this->serverError('Failed to filter booking: ' . $e->getMessage());
        }
    }

}