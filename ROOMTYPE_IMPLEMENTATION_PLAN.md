# 📋 KẾ HOẠCH IMPLEMENTATION - ROOMTYPE MODULE

**Dự án:** Hotel Booking System  
**Module:** RoomType (Loại phòng)  
**Kiến trúc:** Clean Architecture + DDD  
**Ngày:** 31/10/2025

---

## 🎯 TỔNG QUAN

Module RoomType quản lý các loại phòng trong hệ thống khách sạn, bao gồm thông tin về tên, mô tả, sức chứa, giá, và tiện nghi.

### Database Schema
```sql
room_types (
  id INT PRIMARY KEY,
  name VARCHAR(100),
  description TEXT,
  capacity INT,
  price_per_night DECIMAL(10,2),
  amenities TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
)
```

---

## 📂 CẤU TRÚC HIỆN TẠI

```
✅ Domain Layer
   └── Entities/RoomType.php
   └── Interfaces/Repositories/RoomTypeRepositoryInterface.php
   
✅ Application Layer
   └── UseCases/
       ├── GetAllRoomTypesUseCase.php
       ├── GetRoomTypeUseCase.php
       ├── CreateRoomTypeUseCase.php
       ├── UpdateRoomTypeUseCase.php
       ├── DeleteRoomTypeUseCase.php
       ├── FilterRoomTypesByCapacityUseCase.php
       └── FilterRoomTypesByPriceRangeUseCase.php
   └── Services/RoomTypeService.php
   └── DTOs/RoomTypeDTO.php
   └── Validators/RoomTypeValidator.php

✅ Infrastructure Layer
   └── Persistence/Repositories/RoomTypeRepository.php

✅ Presentation Layer
   └── Controllers/Api/RoomTypeController.php
   └── Controllers/Api/BaseRestController.php

✅ DI Container
   └── DIContainer/RepositoryProvider.php (partial)
   └── DIContainer/UseCaseProvider.php (partial)
   └── DIContainer/ServiceProvider.php (partial)
   └── DIContainer/ControllerProvider.php (partial)
```

---

## 🚀 CÁC BƯỚC IMPLEMENTATION

### **BƯỚC 1: XÁC NHẬN & KIỂM TRA FILE HIỆN CÓ**
**Mục đích:** Đảm bảo tất cả file cần thiết đã tồn tại và hoàn chỉnh

#### Checklist:
- [ ] **Domain Layer**
  - [ ] `RoomType.php` - Entity với đầy đủ properties & methods
  - [ ] `RoomTypeRepositoryInterface.php` - Interface định nghĩa contracts
  - [ ] `Exceptions/RoomTypeNotFoundException.php`
  - [ ] `Exceptions/InvalidRoomTypeDataException.php`

- [ ] **Application Layer**
  - [ ] `RoomTypeDTO.php` - Data Transfer Object
  - [ ] `RoomTypeValidator.php` - Validation logic
  - [ ] `RoomTypeService.php` - Application service
  - [ ] Tất cả 7 Use Cases

- [ ] **Infrastructure Layer**
  - [ ] `RoomTypeRepository.php` - Concrete implementation

- [ ] **Presentation Layer**
  - [ ] `RoomTypeController.php` - REST API Controller
  - [ ] `BaseRestController.php` - Base với response helpers

**Action:** Đọc từng file, kiểm tra logic, tìm bugs hoặc thiếu sót

---

### **BƯỚC 2: BỔ SUNG/SỬA CHỮA CODE (Nếu cần)**

#### 2.1. Domain Layer
**File cần kiểm tra:**
- `RoomType.php`: 
  - ✅ Constructor validation
  - ✅ Immutability (hoặc setters nếu cần)
  - ✅ Business logic methods
  - ✅ `toArray()` method

- `RoomTypeRepositoryInterface.php`:
  ```php
  interface RoomTypeRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?RoomType;
    public function save(RoomType $roomType): RoomType;
    public function update(RoomType $roomType): bool;
    public function delete(int $id): bool;
    public function findByCapacity(int $capacity): array;
    public function findByPriceRange(float $min, float $max): array;
  }
  ```

#### 2.2. Application Layer
**RoomTypeValidator.php:**
- Validate name (required, max 100 chars)
- Validate capacity (min 1, max 20)
- Validate price (min 0)
- Validate amenities format

**RoomTypeService.php:**
- Orchestrate use cases
- Handle exceptions
- Business logic coordination

#### 2.3. Infrastructure Layer
**RoomTypeRepository.php:**
- Implement tất cả methods từ interface
- PDO queries với prepared statements
- Error handling
- Data mapping từ DB → Entity

#### 2.4. Presentation Layer
**RoomTypeController.php:**
- REST API endpoints:
  - `GET /api/room-types` - List all
  - `GET /api/room-types/{id}` - Get one
  - `POST /api/room-types` - Create
  - `PUT /api/room-types/{id}` - Update
  - `DELETE /api/room-types/{id}` - Delete
  - `GET /api/room-types/filter/capacity/{capacity}` - Filter
  - `GET /api/room-types/filter/price?min=X&max=Y` - Filter
- Request validation
- Response formatting (JSON)
- HTTP status codes

---

### **BƯỚC 3: CẤU HÌNH DEPENDENCY INJECTION**

#### 3.1. RepositoryProvider.php
```php
class RepositoryProvider {
    public static function register(Container $container): void {
        // RoomType Repository
        $container->singleton(
            RoomTypeRepositoryInterface::class,
            function($container) {
                $db = $container->make(Database::class);
                return new RoomTypeRepository($db);
            }
        );
        
        // Các repositories khác...
    }
}
```

#### 3.2. UseCaseProvider.php
```php
class UseCaseProvider {
    public static function register(Container $container): void {
        // GetAllRoomTypesUseCase
        $container->bind(GetAllRoomTypesUseCase::class);
        
        // GetRoomTypeUseCase
        $container->bind(GetRoomTypeUseCase::class);
        
        // CreateRoomTypeUseCase
        $container->bind(CreateRoomTypeUseCase::class);
        
        // UpdateRoomTypeUseCase
        $container->bind(UpdateRoomTypeUseCase::class);
        
        // DeleteRoomTypeUseCase
        $container->bind(DeleteRoomTypeUseCase::class);
        
        // FilterRoomTypesByCapacityUseCase
        $container->bind(FilterRoomTypesByCapacityUseCase::class);
        
        // FilterRoomTypesByPriceRangeUseCase
        $container->bind(FilterRoomTypesByPriceRangeUseCase::class);
    }
}
```

#### 3.3. ServiceProvider.php
```php
class ServiceProvider {
    public static function register(Container $container): void {
        // RoomType Service
        $container->singleton(RoomTypeService::class);
        
        // Các services khác...
    }
}
```

#### 3.4. ControllerProvider.php
```php
class ControllerProvider {
    public static function register(Container $container): void {
        // RoomType Controller
        $container->bind(RoomTypeController::class);
        
        // Các controllers khác...
    }
}
```

#### 3.5. ValidatorProvider.php
```php
class ValidatorProvider {
    public static function register(Container $container): void {
        // RoomType Validator
        $container->singleton(RoomTypeValidator::class);
        
        // Các validators khác...
    }
}
```

---

### **BƯỚC 4: CẤU HÌNH ROUTES**

**File:** `config/routes.php`

```php
// RoomType Routes
$router->get('/api/room-types', [RoomTypeController::class, 'index']);
$router->get('/api/room-types/{id}', [RoomTypeController::class, 'show']);
$router->post('/api/room-types', [RoomTypeController::class, 'store']);
$router->put('/api/room-types/{id}', [RoomTypeController::class, 'update']);
$router->delete('/api/room-types/{id}', [RoomTypeController::class, 'destroy']);

// Filter routes
$router->get('/api/room-types/filter/capacity/{capacity}', [RoomTypeController::class, 'filterByCapacity']);
$router->get('/api/room-types/filter/price', [RoomTypeController::class, 'filterByPrice']);
```

---

### **BƯỚC 5: TESTING**

#### 5.1. Unit Tests
```
tests/Unit/
├── Domain/
│   └── Entities/RoomTypeTest.php
├── Application/
│   ├── UseCases/GetAllRoomTypesUseCaseTest.php
│   ├── Validators/RoomTypeValidatorTest.php
│   └── Services/RoomTypeServiceTest.php
└── Infrastructure/
    └── Repositories/RoomTypeRepositoryTest.php
```

#### 5.2. Integration Tests
```
tests/Integration/
└── Api/RoomTypeControllerTest.php
```

#### 5.3. Manual Testing (Postman/cURL)
- Test tất cả endpoints
- Test validation errors
- Test edge cases
- Test error handling

---

### **BƯỚC 6: DOCUMENTATION**

#### 6.1. API Documentation
**File:** `docs/API/RoomType.md`
- Endpoint descriptions
- Request/Response examples
- Error codes
- Authentication (if required)

#### 6.2. Code Documentation
- PHPDoc cho tất cả classes & methods
- Inline comments cho logic phức tạp
- README cho module

#### 6.3. Architecture Documentation
**File:** `docs/Architecture/RoomTypeModule.md`
- Layer explanation
- Data flow diagram
- Dependency graph
- Design decisions

---

## 📝 CHI TIẾT TỪNG FILE CẦN TẠO/SỬA

### 1. Domain Layer

#### `src/Domain/Interfaces/Repositories/RoomTypeRepositoryInterface.php`
```php
<?php
namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\RoomType;

interface RoomTypeRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?RoomType;
    public function save(RoomType $roomType): RoomType;
    public function update(RoomType $roomType): bool;
    public function delete(int $id): bool;
    public function findByCapacity(int $capacity): array;
    public function findByPriceRange(float $min, float $max): array;
}
```

#### `src/Domain/Exceptions/RoomTypeNotFoundException.php`
```php
<?php
namespace App\Domain\Exceptions;

class RoomTypeNotFoundException extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Room type with ID {$id} not found");
    }
}
```

#### `src/Domain/Exceptions/InvalidRoomTypeDataException.php`
```php
<?php
namespace App\Domain\Exceptions;

class InvalidRoomTypeDataException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct("Invalid room type data: {$message}");
    }
}
```

---

### 2. Application Layer

#### `src/Application/DTOs/RoomTypeDTO.php`
```php
<?php
namespace App\Application\DTOs;

class RoomTypeDTO
{
    public ?int $id;
    public string $name;
    public string $description;
    public int $capacity;
    public float $pricePerNight;
    public array $amenities;
    
    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? '';
        $dto->description = $data['description'] ?? '';
        $dto->capacity = (int)($data['capacity'] ?? 0);
        $dto->pricePerNight = (float)($data['pricePerNight'] ?? 0);
        $dto->amenities = $data['amenities'] ?? [];
        return $dto;
    }
    
    public function toEntity(): RoomType
    {
        return new RoomType(
            $this->id,
            $this->name,
            $this->description,
            $this->capacity,
            $this->pricePerNight,
            $this->amenities
        );
    }
}
```

#### `src/Application/Validators/RoomTypeValidator.php`
```php
<?php
namespace App\Application\Validators;

use App\Domain\Exceptions\InvalidRoomTypeDataException;

class RoomTypeValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        
        // Name validation
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Name must not exceed 100 characters';
        }
        
        // Capacity validation
        if (!isset($data['capacity']) || $data['capacity'] < 1) {
            $errors['capacity'] = 'Capacity must be at least 1';
        } elseif ($data['capacity'] > 20) {
            $errors['capacity'] = 'Capacity must not exceed 20';
        }
        
        // Price validation
        if (!isset($data['pricePerNight']) || $data['pricePerNight'] < 0) {
            $errors['pricePerNight'] = 'Price must be a positive number';
        }
        
        // Amenities validation
        if (isset($data['amenities']) && !is_array($data['amenities'])) {
            $errors['amenities'] = 'Amenities must be an array';
        }
        
        if (!empty($errors)) {
            throw new InvalidRoomTypeDataException(json_encode($errors));
        }
        
        return $data;
    }
}
```

#### `src/Application/Services/RoomTypeService.php`
```php
<?php
namespace App\Application\Services;

use App\Application\UseCases\*;
use App\Application\DTOs\RoomTypeDTO;
use App\Application\Validators\RoomTypeValidator;

class RoomTypeService
{
    private GetAllRoomTypesUseCase $getAllUseCase;
    private GetRoomTypeUseCase $getUseCase;
    private CreateRoomTypeUseCase $createUseCase;
    private UpdateRoomTypeUseCase $updateUseCase;
    private DeleteRoomTypeUseCase $deleteUseCase;
    private FilterRoomTypesByCapacityUseCase $filterByCapacityUseCase;
    private FilterRoomTypesByPriceRangeUseCase $filterByPriceUseCase;
    private RoomTypeValidator $validator;
    
    public function __construct(...) { /* Inject all use cases & validator */ }
    
    public function getAllRoomTypes(): array
    {
        return $this->getAllUseCase->execute();
    }
    
    public function getRoomType(int $id): RoomType
    {
        return $this->getUseCase->execute($id);
    }
    
    public function createRoomType(RoomTypeDTO $dto): RoomType
    {
        $this->validator->validate($dto->toArray());
        return $this->createUseCase->execute($dto);
    }
    
    // ... other methods
}
```

---

### 3. Infrastructure Layer

#### `src/Infrastructure/Persistence/Repositories/RoomTypeRepository.php`
```php
<?php
namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Entities\RoomType;
use App\Core\Database\Database;

class RoomTypeRepository implements RoomTypeRepositoryInterface
{
    private Database $db;
    
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
    
    public function findAll(): array
    {
        $sql = "SELECT * FROM room_types ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        
        return array_map([$this, 'mapToEntity'], $rows);
    }
    
    public function findById(int $id): ?RoomType
    {
        $sql = "SELECT * FROM room_types WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        $row = $stmt->fetch();
        
        return $row ? $this->mapToEntity($row) : null;
    }
    
    public function save(RoomType $roomType): RoomType
    {
        $sql = "INSERT INTO room_types (name, description, capacity, price_per_night, amenities) 
                VALUES (:name, :description, :capacity, :price, :amenities)";
        
        $this->db->query($sql, [
            'name' => $roomType->getName(),
            'description' => $roomType->getDescription(),
            'capacity' => $roomType->getCapacity(),
            'price' => $roomType->getPricePerNight(),
            'amenities' => json_encode($roomType->getAmenities())
        ]);
        
        $id = (int)$this->db->getConnection()->lastInsertId();
        return $this->findById($id);
    }
    
    private function mapToEntity(array $row): RoomType
    {
        return new RoomType(
            (int)$row['id'],
            $row['name'],
            $row['description'],
            (int)$row['capacity'],
            (float)$row['price_per_night'],
            json_decode($row['amenities'], true) ?? [],
            $row['created_at'],
            $row['updated_at']
        );
    }
    
    // ... other methods
}
```

---

### 4. Presentation Layer

#### `src/Presentation/Controllers/Api/RoomTypeController.php`
```php
<?php
namespace App\Presentation\Controllers\Api;

use App\Application\Services\RoomTypeService;
use App\Application\DTOs\RoomTypeDTO;
use App\Domain\Exceptions\*;

class RoomTypeController extends BaseRestController
{
    private RoomTypeService $service;
    
    public function __construct(RoomTypeService $service)
    {
        $this->service = $service;
    }
    
    public function index(): void
    {
        try {
            $roomTypes = $this->service->getAllRoomTypes();
            $this->success(array_map(fn($rt) => $rt->toArray(), $roomTypes));
        } catch (\Exception $e) {
            $this->serverError($e->getMessage());
        }
    }
    
    public function show(int $id): void
    {
        try {
            $roomType = $this->service->getRoomType($id);
            $this->success($roomType->toArray());
        } catch (RoomTypeNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError($e->getMessage());
        }
    }
    
    public function store(): void
    {
        try {
            $data = $this->getJsonInput();
            $dto = RoomTypeDTO::fromRequest($data);
            $roomType = $this->service->createRoomType($dto);
            $this->created($roomType->toArray(), 'Room type created successfully');
        } catch (InvalidRoomTypeDataException $e) {
            $this->badRequest($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError($e->getMessage());
        }
    }
    
    // ... other methods
}
```

---

## ✅ CHECKLIST HOÀN THÀNH

### Domain Layer
- [ ] RoomType Entity hoàn chỉnh
- [ ] RoomTypeRepositoryInterface đầy đủ methods
- [ ] Exceptions tạo đủ
- [ ] Business logic validated

### Application Layer
- [ ] RoomTypeDTO với fromRequest() & toEntity()
- [ ] RoomTypeValidator với validation rules đầy đủ
- [ ] RoomTypeService orchestrate use cases
- [ ] 7 Use Cases hoàn chỉnh

### Infrastructure Layer
- [ ] RoomTypeRepository implement interface
- [ ] PDO queries với prepared statements
- [ ] Error handling đầy đủ
- [ ] mapToEntity() chính xác

### Presentation Layer
- [ ] RoomTypeController với 7 endpoints
- [ ] Request validation
- [ ] Response formatting (JSON)
- [ ] HTTP status codes chuẩn

### DI Container
- [ ] RepositoryProvider register RoomTypeRepository
- [ ] UseCaseProvider register 7 use cases
- [ ] ServiceProvider register RoomTypeService
- [ ] ControllerProvider register RoomTypeController
- [ ] ValidatorProvider register RoomTypeValidator

### Routes
- [ ] 7 routes đã config trong routes.php
- [ ] Route parameters đúng
- [ ] HTTP methods đúng

### Testing
- [ ] Unit tests cho Entity
- [ ] Unit tests cho Repository
- [ ] Unit tests cho Use Cases
- [ ] Unit tests cho Validator
- [ ] Integration tests cho Controller
- [ ] Manual testing với Postman/cURL

### Documentation
- [ ] API Documentation (endpoints, examples)
- [ ] Code Documentation (PHPDoc)
- [ ] Architecture Documentation
- [ ] README cho module

---

## 🚦 TIÊU CHÍ CHẤP NHẬN

### Functional Requirements
✅ Có thể tạo, đọc, cập nhật, xóa room types  
✅ Có thể filter theo capacity  
✅ Có thể filter theo price range  
✅ Validation đầy đủ cho input data  
✅ Error handling chuẩn  

### Non-Functional Requirements
✅ Clean Architecture layers rõ ràng  
✅ Dependency Injection hoạt động  
✅ Code dễ đọc, dễ maintain  
✅ Performance tốt (queries optimized)  
✅ Security (SQL injection prevention)  

### Code Quality
✅ PSR-12 coding standard  
✅ Type hints đầy đủ  
✅ PHPDoc comments  
✅ No code duplication  
✅ SOLID principles  

---

## 📊 THỜI GIAN ƯỚC TÍNH

| Bước | Thời gian | Ghi chú |
|------|-----------|---------|
| Bước 1: Kiểm tra files | 30 phút | Review code hiện có |
| Bước 2: Sửa/bổ sung code | 2 giờ | Fix bugs, improve logic |
| Bước 3: Config DI | 1 giờ | Setup providers |
| Bước 4: Config routes | 15 phút | Add routes |
| Bước 5: Testing | 2 giờ | Write & run tests |
| Bước 6: Documentation | 1 giờ | Write docs |
| **TỔNG** | **~6-7 giờ** | Cho 1 developer |

---

## 🔄 QUY TRÌNH LÀM VIỆC

1. **Bạn review plan này** → Approve hoặc yêu cầu điều chỉnh
2. **Tôi bắt đầu từ Bước 1** → Kiểm tra từng file
3. **Báo cáo sau mỗi bước** → Bạn xác nhận OK
4. **Chuyển sang bước tiếp theo** → Lặp lại cho đến hết
5. **Testing tổng thể** → Đảm bảo mọi thứ hoạt động
6. **Viết documentation** → Hoàn tất

---

## ❓ CÂU HỎI TRƯỚC KHI BẮT ĐẦU

1. **Plan này có OK không?** Cần điều chỉnh gì không?
2. **Bắt đầu từ bước nào?** (Recommend: Bước 1)
3. **Có module nào khác cần làm trước?** (VD: Auth, Middleware)
4. **API cần authentication không?** (JWT, Session, etc.)
5. **Response format?** (Đã có BaseRestController - OK?)

---

**👉 Vui lòng xác nhận để tôi bắt đầu implementation!**

