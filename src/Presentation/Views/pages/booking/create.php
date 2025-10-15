@extends('layouts.main')

@section('content')
<div class="container">
    <div class="booking-form">
        <h1>Book Room {{ $room->getRoomNumber() }}</h1>

        @if(isset($_SESSION['error']))
            <div class="alert alert-danger">{{ $_SESSION['error'] }}</div>
            <?php unset($_SESSION['error']); ?>
        @endif

        <div class="booking-summary">
            <h3>Room Details</h3>
            <div class="summary-item">
                <span>Room Type:</span>
                <span>{{ ucfirst($room->getType()) }}</span>
            </div>
            <div class="summary-item">
                <span>Capacity:</span>
                <span>{{ $room->getCapacity() }} guests</span>
            </div>
            <div class="summary-item">
                <span>Price per night:</span>
                <span>${{ number_format($room->getPricePerNight(), 2) }}</span>
            </div>
        </div>

        <form action="/booking/store" method="POST" data-validate>
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->getId() }}">
            <input type="hidden" id="price_per_night" value="{{ $room->getPricePerNight() }}">

            <div class="form-group">
                <label for="check_in_date">Check-In Date</label>
                <input type="date" id="check_in_date" name="check_in_date" value="{{ $checkIn }}" required>
            </div>

            <div class="form-group">
                <label for="check_out_date">Check-Out Date</label>
                <input type="date" id="check_out_date" name="check_out_date" value="{{ $checkOut }}" required>
            </div>

            <div class="form-group">
                <label for="number_of_guests">Number of Guests</label>
                <input type="number" id="number_of_guests" name="number_of_guests" min="1" max="{{ $room->getCapacity() }}" required>
            </div>

            <div class="form-group">
                <label for="special_requests">Special Requests (Optional)</label>
                <textarea id="special_requests" name="special_requests" rows="3"></textarea>
            </div>

            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <div class="summary-item">
                    <span>Total Nights:</span>
                    <span id="total_nights">0</span>
                </div>
                <div class="summary-item total">
                    <span>Total Price:</span>
                    <span>$<span id="total_price">0.00</span></span>
                </div>
                <input type="hidden" name="total_price">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="/js/modules/booking.js"></script>
@endsection

