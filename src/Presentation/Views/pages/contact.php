@extends('layouts.main')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Contact Us</h1>
    </div>

    @if(isset($_SESSION['success']))
        <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
        <?php unset($_SESSION['success']); ?>
    @endif

    <div class="contact-container">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <div class="info-item">
                <strong>📍 Address</strong>
                <p>123 Hotel Street, City, Country</p>
            </div>
            <div class="info-item">
                <strong>📞 Phone</strong>
                <p>+1 234 567 8900</p>
            </div>
            <div class="info-item">
                <strong>✉️ Email</strong>
                <p>info@hotel.com</p>
            </div>
            <div class="info-item">
                <strong>🕒 Hours</strong>
                <p>24/7 - We're always here for you</p>
            </div>
        </div>

        <div class="contact-form-container">
            <h2>Send us a Message</h2>

            @if(isset($errors) && !empty($errors))
                <div class="alert alert-danger">
                    @foreach($errors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="/contact" method="POST" data-validate>
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ $old['name'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required>{{ $old['message'] ?? '' }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<style>
.contact-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    margin-top: 2rem;
}

.contact-info, .contact-form-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

.info-item {
    margin-bottom: 1.5rem;
}

.info-item strong {
    display: block;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

