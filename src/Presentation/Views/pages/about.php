@extends('layouts.main')

@section('styles')
<style>
.about-content {
    max-width: 800px;
    margin: 0 auto;
}

.about-section {
    background: white;
    padding: 2rem;
    margin-bottom: 2rem;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

.about-section h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.about-section ul {
    list-style: none;
    padding: 0;
}

.about-section li {
    padding: 0.5rem 0;
    font-size: 1.1rem;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <h1>About Us</h1>
    </div>

    <div class="about-content">
        <section class="about-section">
            <h2>Welcome to Our Hotel</h2>
            <p>We are committed to providing exceptional hospitality and comfortable accommodations for all our guests. With years of experience in the industry, we pride ourselves on delivering memorable experiences.</p>
        </section>

        <section class="about-section">
            <h2>Our Mission</h2>
            <p>To provide world-class service and comfortable accommodations that exceed our guests' expectations while maintaining the highest standards of quality and professionalism.</p>
        </section>

        <section class="about-section">
            <h2>Why Choose Us?</h2>
            <ul>
                <li>✓ Prime location with easy access to major attractions</li>
                <li>✓ Comfortable and well-appointed rooms</li>
                <li>✓ 24/7 customer service</li>
                <li>✓ Modern amenities and facilities</li>
                <li>✓ Competitive pricing</li>
            </ul>
        </section>
    </div>
</div>
@endsection
