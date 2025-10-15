@extends('layouts.main')

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Login</h1>

            @if(isset($_SESSION['success']))
                <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
                <?php unset($_SESSION['success']); ?>
            @endif

            @if(isset($errors) && !empty($errors))
                <div class="alert alert-danger">
                    @foreach($errors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="/login" method="POST" data-validate>
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="text-center mt-3">
                Don't have an account? <a href="/register">Register here</a>
            </p>
        </div>
    </div>
</div>

<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
}

.auth-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 400px;
}

.auth-card h1 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.btn-block {
    width: 100%;
}

.text-center {
    text-align: center;
}

.mt-3 {
    margin-top: 1rem;
}
</style>
@endsection

