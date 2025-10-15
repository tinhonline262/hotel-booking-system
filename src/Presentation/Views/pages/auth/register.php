@extends('layouts.main')

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Register</h1>

            @if(isset($errors) && !empty($errors))
                <div class="alert alert-danger">
                    @foreach($errors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="/register" method="POST" data-validate>
                @csrf

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ $old['first_name'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ $old['last_name'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="{{ $old['phone'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="/login">Login here</a>
            </p>
        </div>
    </div>
</div>
@endsection

