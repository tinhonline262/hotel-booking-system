# Hotel Booking System

A comprehensive hotel booking system built with Clean Architecture, Domain-Driven Design (DDD), and design patterns.

## Technology Stack

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Database**: MySQL
- **Architecture**: Clean Architecture, DDD
- **Template Engine**: Custom Template Engine
- **Design Patterns**: Repository, Singleton, Dependency Injection, MVC

## Project Structure

```
hotel/
├── config/                 # Configuration files
│   ├── app.php            # Application config
│   ├── database.php       # Database config
│   └── routes.php         # Route definitions
├── database/              # Database files
│   └── schema.sql         # Database schema
├── public/                # Public files (web root)
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── images/           # Images
│   ├── fonts/            # Fonts
│   ├── uploads/          # User uploads
│   └── index.php         # Entry point
├── src/                   # Application source code
│   ├── Core/             # Core framework components
│   │   ├── Container/    # Dependency injection
│   │   ├── Database/     # Database connection
│   │   ├── Router/       # HTTP routing
│   │   └── Template/     # Template engine
│   ├── Domain/           # Domain layer (Business Logic)
│   │   ├── Entities/     # Domain entities
│   │   ├── Repositories/ # Repository interfaces
│   │   ├── Services/     # Domain services
│   │   ├── ValueObjects/ # Value objects
│   │   ├── Events/       # Domain events
│   │   └── Exceptions/   # Domain exceptions
│   ├── Application/      # Application layer
│   │   ├── UseCases/     # Use cases
│   │   ├── DTOs/         # Data transfer objects
│   │   ├── Services/     # Application services
│   │   ├── Validators/   # Input validation
│   │   └── Interfaces/   # Application interfaces
│   ├── Infrastructure/   # Infrastructure layer
│   │   ├── Persistence/  # Data persistence
│   │   ├── Repositories/ # Repository implementations
│   │   ├── Database/     # Database utilities
│   │   ├── Session/      # Session management
│   │   ├── Validation/   # Validation utilities
│   │   └── Email/        # Email services
│   └── Presentation/     # Presentation layer
│       ├── Controllers/  # HTTP controllers
│       ├── Middlewares/  # HTTP middlewares
│       ├── Helpers/      # View helpers
│       └── Views/        # View templates
│           ├── layouts/  # Layout templates
│           ├── components/ # Reusable components
│           ├── pages/    # Page templates
│           └── partials/ # Partial templates
├── storage/              # Storage directory
│   ├── cache/           # Cached files
│   ├── logs/            # Application logs
│   ├── sessions/        # Session files
│   └── views/           # Compiled views
├── tests/               # Test files
└── vendor/              # Composer dependencies

```

## Features

- ✅ Clean Architecture with DDD principles
- ✅ Custom Template Engine with Blade-like syntax
- ✅ Dependency Injection Container
- ✅ Repository Pattern
- ✅ MVC Architecture
- ✅ User Authentication & Authorization
- ✅ Room Management
- ✅ Booking System with conflict detection
- ✅ Admin Dashboard
- ✅ Responsive UI with reusable components
- ✅ CSRF Protection
- ✅ Input Validation
- ✅ Session Management

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone or navigate to the project directory**
   ```bash
   cd D:\PHPCode\hotel
   ```

2. **Install dependencies**
   ```bash
   php composer.phar install
   ```

3. **Configure database**
   - Edit `config/database.php` with your database credentials
   - Create a database named `hotel_booking`

4. **Import database schema**
   ```bash
   mysql -u root -p hotel_booking < database/schema.sql
   ```

5. **Configure web server**
   
   **For Apache:**
   - Point document root to `public/` directory
   - Enable mod_rewrite
   - The `.htaccess` file is already configured

   **For PHP Built-in Server (Development):**
   ```bash
   php -S localhost:8000 -t public
   ```

6. **Set permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 public/uploads
   ```

7. **Access the application**
   - Open browser and go to `http://localhost:8000`
   - Default admin credentials:
     - Email: `admin@hotel.com`
     - Password: `admin123`

## Template Engine Syntax

The custom template engine supports Blade-like syntax:

### Layouts & Sections
```php
@extends('layouts.main')

@section('content')
    <h1>Hello World</h1>
@endsection
```

### Variables
```php
{{ $variable }}           <!-- Escaped output -->
{!! $variable !!}         <!-- Raw output -->
```

### Control Structures
```php
@if($condition)
    ...
@elseif($other)
    ...
@else
    ...
@endif

@foreach($items as $item)
    {{ $item }}
@endforeach

@while($condition)
    ...
@endwhile
```

### Components & Partials
```php
@include('partials.header')
@component('components.card', ['title' => 'Hello'])
```

### Forms
```php
@csrf                    <!-- CSRF token -->
@method('PUT')          <!-- Method spoofing -->
```

## Architecture Layers

### Domain Layer
Contains core business logic and entities:
- **Entities**: Room, Booking, User
- **Repositories**: Interfaces for data access
- **Services**: Domain business logic

### Application Layer
Contains use cases and application logic:
- **UseCases**: CreateBookingUseCase, etc.
- **DTOs**: Data transfer objects
- **Services**: Application-level services

### Infrastructure Layer
Contains technical implementations:
- **Repositories**: Database implementations
- **Database**: Connection management
- **Persistence**: Data storage

### Presentation Layer
Contains UI and controllers:
- **Controllers**: HTTP request handlers
- **Views**: HTML templates
- **Middlewares**: Request/response filters

## Design Patterns Used

1. **Repository Pattern**: Abstract data access
2. **Singleton Pattern**: Database connection
3. **Dependency Injection**: Container-based DI
4. **MVC Pattern**: Separation of concerns
5. **Factory Pattern**: Object creation
6. **Observer Pattern**: Event handling (ready for implementation)
7. **Strategy Pattern**: Validation, payment processing (ready for implementation)

## Database Schema

### Tables
- `users`: User accounts and authentication
- `rooms`: Hotel room information
- `bookings`: Room reservations

### Relationships
- Bookings belong to Users (one-to-many)
- Bookings belong to Rooms (one-to-many)

## API Endpoints (Routes)

### Public Routes
- `GET /` - Home page
- `GET /rooms` - List all rooms
- `GET /rooms/{id}` - Room details
- `GET /rooms/search` - Search rooms
- `GET /login` - Login page
- `POST /login` - Process login
- `GET /register` - Registration page
- `POST /register` - Process registration

### Authenticated Routes
- `GET /dashboard` - User dashboard
- `GET /booking/create` - Booking form
- `POST /booking/store` - Create booking
- `POST /logout` - Logout

### Admin Routes
- `GET /admin` - Admin dashboard
- `GET /admin/rooms` - Manage rooms
- `GET /admin/bookings` - Manage bookings

## Development

### Adding New Routes
Edit `config/routes.php`:
```php
['GET', '/new-route', 'ControllerName@method']
```

### Creating Controllers
```php
namespace App\Presentation\Controllers;

class MyController extends BaseController
{
    public function index(): void
    {
        $this->render('pages.mypage', ['data' => 'value']);
    }
}
```

### Creating Views
Create file in `src/Presentation/Views/pages/mypage.php`:
```php
@extends('layouts.main')

@section('content')
    <h1>My Page</h1>
@endsection
```

## Security Features

- Password hashing with bcrypt
- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS protection (escaped output by default)
- Session security
- Input validation

## Contributing

1. Follow PSR-4 autoloading standards
2. Maintain Clean Architecture principles
3. Write clean, documented code
4. Test your changes

## License

This project is open-source and available for educational purposes.

## Support

For issues or questions, please create an issue in the project repository.

---

**Built with Clean Architecture & DDD principles**

