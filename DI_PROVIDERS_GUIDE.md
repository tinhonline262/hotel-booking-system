# Dependency Injection Container - Organized Provider Structure

## 📋 Tổng quan

Hệ thống DI Container được tổ chức theo module với các Provider riêng biệt, dễ quản lý và mở rộng.

## 🏗️ Cấu trúc Provider

```
src/Infrastructure/Providers/
├── AppServiceProvider.php          # Main provider - Entry point
├── SystemProvider.php              # Core system dependencies
├── ValidatorProvider.php           # All validators
├── RepositoryProvider.php          # All repositories
├── UseCaseProvider.php             # All use cases
├── ServiceProvider.php             # All services
├── ControllerProvider.php          # All controllers
└── RoomTypeServiceProvider.php     # Legacy (backward compatibility)
```

## 📦 Chi tiết từng Provider

### 1. **AppServiceProvider** - Main Provider
```php
AppServiceProvider::register($container);
```
- Entry point cho toàn bộ hệ thống DI
- Tự động load tất cả sub-providers theo thứ tự dependency
- Có method `boot()` cho post-registration logic

**Thứ tự đăng ký:**
1. SystemProvider (Database, Cache, Logger)
2. ValidatorProvider (All validators)
3. RepositoryProvider (All repositories)
4. UseCaseProvider (All use cases)
5. ServiceProvider (All services)
6. ControllerProvider (All controllers)

### 2. **SystemProvider** - Core System
```php
SystemProvider::register($container);
```
**Nhiệm vụ:** Đăng ký các dependency cốt lõi của hệ thống
- Database connection
- Cache system
- Logger
- File system
- Configuration

**Ví dụ:**
```php
$container->singleton(Database::class, function () {
    $config = require __DIR__ . '/../../../config/database.php';
    return Database::getInstance($config);
});
```

### 3. **ValidatorProvider** - Validators
```php
ValidatorProvider::register($container);
```
**Nhiệm vụ:** Đăng ký tất cả validators
- RoomTypeValidator
- UserValidator
- BookingValidator
- PaymentValidator

**Ví dụ:**
```php
$container->singleton(RoomTypeValidator::class, function () {
    return new RoomTypeValidator();
});
```

### 4. **RepositoryProvider** - Repositories
```php
RepositoryProvider::register($container);
```
**Nhiệm vụ:** Bind interface với implementation
- RoomTypeRepositoryInterface → RoomTypeRepository
- UserRepositoryInterface → UserRepository
- BookingRepositoryInterface → BookingRepository

**Ví dụ:**
```php
$container->singleton(RoomTypeRepositoryInterface::class, function (Container $c) {
    return new RoomTypeRepository($c->make(Database::class));
});
```

### 5. **UseCaseProvider** - Use Cases
```php
UseCaseProvider::register($container);
```
**Nhiệm vụ:** Đăng ký tất cả use cases với dependencies
- Organized by module (RoomType, User, Booking, etc.)
- Each module has private method để tổ chức code

**Ví dụ:**
```php
private static function registerRoomTypeUseCases(Container $container): void
{
    $container->bind(CreateRoomTypeUseCase::class, function (Container $c) {
        return new CreateRoomTypeUseCase(
            $c->make(RoomTypeRepositoryInterface::class),
            $c->make(RoomTypeValidator::class)
        );
    });
}
```

### 6. **ServiceProvider** - Application Services
```php
ServiceProvider::register($container);
```
**Nhiệm vụ:** Đăng ký application services
- RoomTypeService
- UserService
- BookingService
- Bind service interfaces

**Ví dụ:**
```php
$container->singleton(RoomTypeService::class, function (Container $c) {
    return new RoomTypeService(
        $c->make(CreateRoomTypeUseCase::class),
        $c->make(GetRoomTypeUseCase::class),
        // ... other use cases
    );
});
```

### 7. **ControllerProvider** - Controllers
```php
ControllerProvider::register($container);
```
**Nhiệm vụ:** Đăng ký tất cả controllers
- RoomTypeController
- UserController
- BookingController
- AuthController

**Ví dụ:**
```php
$container->bind(RoomTypeController::class, function (Container $c) {
    return new RoomTypeController(
        $c->make(RoomTypeService::class)
    );
});
```

## 🚀 Cách sử dụng

### Trong `public/index.php`

```php
use App\Core\Container\Container;use App\Infrastructure\DIContainer\AppServiceProvider;

$container = Container::getInstance();

// Đăng ký tất cả providers
AppServiceProvider::register($container);

// Boot (optional)
AppServiceProvider::boot($container);
```

### Trong Tests

```php
use App\Application\Services\RoomTypeService;use App\Core\Container\Container;use App\Infrastructure\DIContainer\AppServiceProvider;

$container = Container::getInstance();
AppServiceProvider::register($container);

$service = $container->make(RoomTypeService::class);
```

### Resolve Dependencies
```php
// Lấy service
$roomTypeService = $container->make(RoomTypeService::class);

// Lấy repository
$repository = $container->make(RoomTypeRepositoryInterface::class);

// Lấy use case
$createUseCase = $container->make(CreateRoomTypeUseCase::class);
```

## ✨ Ưu điểm của cấu trúc này

### 1. **Separation of Concerns**
- Mỗi provider có một nhiệm vụ riêng biệt
- Dễ tìm và sửa đổi

### 2. **Scalability**
- Thêm module mới chỉ cần thêm vào provider tương ứng
- Không cần tạo provider riêng cho mỗi module

### 3. **Maintainability**
- Code ngắn gọn, rõ ràng
- Dễ debug và test

### 4. **Reusability**
- Có thể register từng provider riêng lẻ nếu cần
- Flexible cho nhiều use case khác nhau

### 5. **Order Management**
- AppServiceProvider quản lý thứ tự load
- Đảm bảo dependencies được resolve đúng

## 📝 Hướng dẫn mở rộng

### Thêm module mới (ví dụ: User)

#### 1. Tạo các file cần thiết
- Domain: UserEntity, UserRepositoryInterface
- Application: UserDTO, UserValidator, UserUseCases, UserService
- Infrastructure: UserRepository
- Presentation: UserController

#### 2. Thêm vào ValidatorProvider
```php
$container->singleton(UserValidator::class, function () {
    return new UserValidator();
});
```

#### 3. Thêm vào RepositoryProvider
```php
$container->singleton(UserRepositoryInterface::class, function (Container $c) {
    return new UserRepository($c->make(Database::class));
});
```

#### 4. Thêm vào UseCaseProvider
```php
private static function registerUserUseCases(Container $container): void
{
    // Register các use cases
}

// Gọi trong register()
self::registerUserUseCases($container);
```

#### 5. Thêm vào ServiceProvider
```php
private static function registerUserService(Container $container): void
{
    // Register service
}
```

#### 6. Thêm vào ControllerProvider
```php
$container->bind(UserController::class, function (Container $c) {
    return new UserController($c->make(UserService::class));
});
```

## 🔄 Migration từ cũ sang mới

### Code cũ (RoomTypeServiceProvider)
```php
RoomTypeServiceProvider::register($container);
```

### Code mới (Recommended)
```php
AppServiceProvider::register($container);
```

**Note:** RoomTypeServiceProvider vẫn hoạt động (backward compatibility) nhưng nên chuyển sang AppServiceProvider.

## 🎯 Best Practices

1. **Singleton cho stateless services:** Database, Validators, Services
2. **Bind cho stateful objects:** UseCases, Controllers
3. **Private methods cho module groups:** Giữ code organized
4. **Document dependencies:** Comment rõ ràng dependencies của mỗi class
5. **Test isolation:** Mỗi provider có thể test độc lập

## 📊 Dependency Flow

```
Request → Router → Container
           ↓
    AppServiceProvider
           ↓
    ┌──────┴──────┬────────┬──────────┬─────────┬────────────┐
    │             │        │          │         │            │
System        Validator Repository UseCase  Service    Controller
Provider      Provider  Provider   Provider  Provider   Provider
    │             │        │          │         │            │
    └─────────────┴────────┴──────────┴─────────┴────────────┘
                            ↓
                     Resolved Instance
```

## 🛠️ Troubleshooting

### Lỗi "Class not found"
```bash
php composer.phar dump-autoload
```

### Lỗi "Circular dependency"
- Kiểm tra thứ tự trong AppServiceProvider
- Đảm bảo SystemProvider được load đầu tiên

### Lỗi "Binding not found"
- Kiểm tra provider đã được register chưa
- Verify class name và namespace

## 📚 Tài liệu tham khảo

- [PSR-11: Container Interface](https://www.php-fig.org/psr/psr-11/)
- [Dependency Injection Principles](https://en.wikipedia.org/wiki/Dependency_injection)
- [Service Provider Pattern](https://laravel.com/docs/providers)

