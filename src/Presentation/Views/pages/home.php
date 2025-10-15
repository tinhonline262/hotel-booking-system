@extends('layouts.main')

@section('content')
<div class="hero-section">
    <div class="container">
        <h1>Welcome to Our Hotel</h1>
        <p>Experience luxury and comfort</p>

        <div class="search-box">
            <form action="/rooms/search" method="GET" class="search-form">
                <div class="form-group">
                    <label for="check_in">Check In</label>
                    <input type="date" id="check_in" name="check_in" required>
                </div>

                <div class="form-group">
                    <label for="check_out">Check Out</label>
                    <input type="date" id="check_out" name="check_out" required>
                </div>

                <div class="form-group">
                    <label for="type">Room Type</label>
                    <select id="type" name="type">
                        <option value="">All Types</option>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="suite">Suite</option>
                        <option value="deluxe">Deluxe</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Search Rooms</button>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <section class="features">
        <h2>Why Choose Us</h2>
        <div class="feature-grid">
            @component('components.feature-card', ['icon' => '🏨', 'title' => 'Luxury Rooms', 'description' => 'Comfortable and well-equipped rooms'])
            @component('components.feature-card', ['icon' => '🍽️', 'title' => 'Fine Dining', 'description' => 'Delicious cuisine from around the world'])
            @component('components.feature-card', ['icon' => '🏊', 'title' => 'Amenities', 'description' => 'Pool, gym, spa and more'])
        </div>
    </section>
</div>
@endsection
