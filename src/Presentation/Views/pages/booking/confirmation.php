@extends('layouts.main')

@section('content')
<div class="container">
    <div class="confirmation-page">
        <div class="success-icon">✓</div>
        <h1>Booking Confirmed!</h1>
        <p>Your booking has been successfully created.</p>
        
        <div class="booking-details">
            <h3>Booking Details</h3>
            
            <div class="detail-row">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $booking->getId() }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Room:</span>
                <span class="value">{{ $room->getRoomNumber() }} - {{ ucfirst($room->getType()) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Check-In:</span>
                <span class="value">{{ $booking->getCheckInDate()->format('M d, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Check-Out:</span>
                <span class="value">{{ $booking->getCheckOutDate()->format('M d, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Guests:</span>
                <span class="value">{{ $booking->getNumberOfGuests() }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Total Nights:</span>
                <span class="value">{{ $booking->getDurationInDays() }}</span>
            </div>
            
            <div class="detail-row total">
                <span class="label">Total Price:</span>
                <span class="value">${{ number_format($booking->getTotalPrice(), 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value"><span class="status-badge status-pending">{{ ucfirst($booking->getStatus()) }}</span></span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="/dashboard/bookings" class="btn btn-primary">View My Bookings</a>
            <a href="/rooms" class="btn btn-secondary">Browse More Rooms</a>
        </div>
    </div>
</div>

<style>
.confirmation-page {
    max-width: 600px;
    margin: 2rem auto;
    text-align: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    background-color: var(--success-color);
    color: white;
    font-size: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.booking-details {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: var(--shadow);
    margin: 2rem 0;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-row.total {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--secondary-color);
    border-bottom: none;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-secondary {
    background-color: var(--light-bg);
    color: var(--dark-text);
}
</style>
@endsection

