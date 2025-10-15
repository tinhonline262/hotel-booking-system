@extends('layouts.main')

@section('content')
<div class="container">
    <div class="room-detail">
        <div class="room-gallery">
            @if(!empty($room->getImages()))
                <img src="{{ $room->getImages()[0] }}" alt="{{ $room->getType() }}" class="main-image">
            @else
                <img src="/images/placeholder-room.jpg" alt="{{ $room->getType() }}" class="main-image">
            @endif
        </div>

        <div class="room-info-detail">
            <h1>Room {{ $room->getRoomNumber() }} - {{ ucfirst($room->getType()) }}</h1>

            <div class="room-meta">
                <span class="badge">👥 {{ $room->getCapacity() }} Guests</span>
                <span class="badge">🏢 Floor {{ $room->getFloor() }}</span>
                <span class="badge status-{{ $room->getStatus() }}">{{ ucfirst($room->getStatus()) }}</span>
            </div>

            <div class="room-description">
                <h3>Description</h3>
                <p>{{ $room->getDescription() }}</p>
            </div>

            <div class="room-amenities-detail">
                <h3>Amenities</h3>
                <ul>
                    @foreach($room->getAmenities() as $amenity)
                        <li>✓ {{ $amenity }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="room-pricing">
                <div class="price-tag">
                    <span class="price">${{ number_format($room->getPricePerNight(), 2) }}</span>
                    <span class="per-night">per night</span>
                </div>

                @if($room->isAvailable())
                    <a href="/booking/create?room_id={{ $room->getId() }}" class="btn btn-primary btn-large">Book Now</a>
                @else
                    <button class="btn btn-secondary btn-large" disabled>Not Available</button>
                @endif
            </div>
        </div>
    </div>

    <div class="similar-rooms">
        <h2>Similar Rooms</h2>
        <p class="text-center">Browse more rooms from our collection</p>
        <div class="text-center">
            <a href="/rooms" class="btn btn-primary">View All Rooms</a>
        </div>
    </div>
</div>

<style>
.room-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin: 2rem 0;
}

.room-gallery {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.main-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

.room-info-detail {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

.room-meta {
    display: flex;
    gap: 0.5rem;
    margin: 1rem 0;
    flex-wrap: wrap;
}

.room-description,
.room-amenities-detail {
    margin: 2rem 0;
}

.room-amenities-detail ul {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.room-pricing {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    border-top: 2px solid var(--border-color);
    margin-top: 2rem;
}

.price-tag {
    display: flex;
    flex-direction: column;
}

.price-tag .price {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--secondary-color);
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.similar-rooms {
    margin: 4rem 0;
    text-align: center;
}

@media (max-width: 768px) {
    .room-detail {
        grid-template-columns: 1fr;
    }

    .room-pricing {
        flex-direction: column;
        gap: 1rem;
    }

    .btn-large {
        width: 100%;
    }
}
</style>
@endsection

