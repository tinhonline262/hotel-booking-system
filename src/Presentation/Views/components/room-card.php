<div class="room-card">
    <div class="room-image">
        @if(!empty($room->getImages()))
            <img src="{{ $room->getImages()[0] }}" alt="{{ $room->getType() }}">
        @else
            <img src="/images/placeholder-room.jpg" alt="{{ $room->getType() }}">
        @endif
    </div>

    <div class="room-details">
        <h3>Room {{ $room->getRoomNumber() }} - {{ $room->getType() }}</h3>
        <p class="room-description">{{ $room->getDescription() }}</p>

        <div class="room-info">
            <span>👥 Capacity: {{ $room->getCapacity() }}</span>
            <span>🏢 Floor: {{ $room->getFloor() }}</span>
        </div>

        <div class="room-amenities">
            @foreach($room->getAmenities() as $amenity)
                <span class="badge">{{ $amenity }}</span>
            @endforeach
        </div>

        <div class="room-footer">
            <div class="room-price">
                <span class="price">${{ number_format($room->getPricePerNight(), 2) }}</span>
                <span class="per-night">per night</span>
            </div>
            <a href="/rooms/{{ $room->getId() }}" class="btn btn-primary">View Details</a>
        </div>
    </div>
</div>

