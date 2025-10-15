@extends('layouts.main')

@section('content')
<div class="container">
    <div class="dashboard">
        <aside class="sidebar">
            <h3>Dashboard</h3>
            <ul class="sidebar-menu">
                <li><a href="/dashboard" class="active">Overview</a></li>
                <li><a href="/dashboard/bookings">My Bookings</a></li>
                <li><a href="/dashboard/profile">Profile</a></li>
            </ul>
        </aside>
        
        <div class="dashboard-content">
            <h1>Welcome, {{ $currentUser['name'] }}!</h1>
            
            @if(isset($_SESSION['success']))
                <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
                <?php unset($_SESSION['success']); ?>
            @endif
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>{{ count($bookings) }}</h3>
                    <p>Total Bookings</p>
                </div>
                
                <div class="stat-card">
                    <h3>{{ count(array_filter($bookings, fn($b) => $b->getStatus() === 'pending')) }}</h3>
                    <p>Pending</p>
                </div>
                
                <div class="stat-card">
                    <h3>{{ count(array_filter($bookings, fn($b) => $b->getStatus() === 'confirmed')) }}</h3>
                    <p>Confirmed</p>
                </div>
            </div>
            
            <div class="recent-bookings">
                <h2>Recent Bookings</h2>
                
                @if(empty($bookings))
                    <p>No bookings yet. <a href="/rooms">Browse available rooms</a></p>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($bookings, 0, 5) as $booking)
                                <tr>
                                    <td>#{{ $booking->getId() }}</td>
                                    <td>{{ $booking->getCheckInDate()->format('M d, Y') }}</td>
                                    <td>{{ $booking->getCheckOutDate()->format('M d, Y') }}</td>
                                    <td><span class="status-badge status-{{ $booking->getStatus() }}">{{ ucfirst($booking->getStatus()) }}</span></td>
                                    <td>${{ number_format($booking->getTotalPrice(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <a href="/dashboard/bookings" class="btn btn-primary mt-2">View All Bookings</a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.stat-card {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    color: white;
    padding: 2rem;
    border-radius: 8px;
    text-align: center;
}

.stat-card h3 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.recent-bookings {
    margin-top: 2rem;
}

.mt-2 {
    margin-top: 1rem;
}
</style>
@endsection

