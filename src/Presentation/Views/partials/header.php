<header class="header">
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">
                <a href="/">{{ $appName }}</a>
            </div>

            <ul class="navbar-menu">
                <li><a href="/">Home</a></li>
                <li><a href="/rooms">Rooms</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/contact">Contact</a></li>

                @if($isAuthenticated)
                    <li><a href="/dashboard">Dashboard</a></li>
                    @if($currentUser['role'] === 'admin')
                        <li><a href="/admin">Admin</a></li>
                    @endif
                    <li>
                        <form action="/logout" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-link">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                @endif
            </ul>
        </nav>
    </div>
</header>

