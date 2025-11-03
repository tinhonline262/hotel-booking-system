# RoomType API Documentation

## Overview

This API provides endpoints to manage room types in the hotel booking system. It allows creating, reading, updating, and deleting room types, as well as filtering them by capacity and price range.

## Base URL

```
http://localhost:8000/api/room-types
```

## Endpoints

### 1. List All Room Types

Retrieves a list of all room types.

**Endpoint:** `GET /api/room-types`

**Response Example:**
```json
{
    "success": true,
    "message": "Room types retrieved successfully",
    "timestamp": "2025-11-01 08:22:25",
    "data": [
        {
            "id": 1,
            "name": "Standard",
            "description": "Comfortable room with basic amenities",
            "capacity": 2,
            "pricePerNight": 500000,
            "amenities": [
                "WiFi",
                "Air Conditioning",
                "TV",
                "Mini Fridge"
            ],
            "createdAt": "2025-10-28 16:32:47",
            "updatedAt": "2025-10-28 16:32:47"
        }
    ]
}
```

### 2. Get Single Room Type

Retrieves details of a specific room type.

**Endpoint:** `GET /api/room-types/{id}`

**Parameters:**
- `id` (path parameter) - The ID of the room type

**Response Example:**
```json
{
    "success": true,
    "message": "Room type retrieved successfully",
    "timestamp": "2025-11-01 08:22:25",
    "data": {
        "id": 2,
        "name": "Deluxe Plus",
        "description": "Updated description",
        "capacity": 3,
        "pricePerNight": 150,
        "amenities": [
            "WiFi",
            "TV",
            "Minibar",
            "AC"
        ],
        "createdAt": "2025-10-28 16:32:47",
        "updatedAt": "2025-10-31 03:45:07"
    }
}
```

### 3. Create Room Type

Creates a new room type.

**Endpoint:** `POST /api/room-types`

**Request Body:**
```json
{
  "name": "Standard",
  "description": "Amenities not an array",
  "capacity": 2,
  "pricePerNight": 60.00,
  "amenities": "WiFi,TV"
}

```

**Validation Rules:**
- `name`: Required, string
- `description`: Required, string
- `capacity`: Required, integer between 1 and 10
- `pricePerNight`: Required, positive number
- `amenities`: Required, array of strings

**Response Example:**
```json
{
  "success": true,
  "message": "Room type created successfully",
  "timestamp": "2025-11-01 08:25:59",
  "data": {
    "success": true
  }
}
```

### 4. Update Room Type

Updates an existing room type.

**Endpoint:** `PUT /api/room-types/{id}`

**Parameters:**
- `id` (path parameter) - The ID of the room type to update

**Request Body:**
```json
{
  "name": "Deluxe Plus",
  "description": "Updated description",
  "capacity": 3,
  "pricePerNight": 160,
  "amenities": "TV"
}
```

**Validation Rules:**
Same as Create Room Type

**Response Example:**
```json
{
  "success": true,
  "message": "Room type updated successfully",
  "timestamp": "2025-11-01 08:28:32",
  "data": {
    "success": true
  }
}
```

### 5. Delete Room Type

Deletes a room type.

**Endpoint:** `DELETE /api/room-types/{id}`

**Parameters:**
- `id` (path parameter) - The ID of the room type to delete

**Response Example:**
```json
{
    "success": true,
    "message": "Room type deleted successfully",
    "timestamp": "2025-11-01 08:22:25"
}
```

## Error Responses

### 400 Bad Request
```json
{
    "success": false,
    "message": "Validation failed",
    "timestamp": "2025-11-01 08:22:25",
    "errors": {
        "name": "The name field is required",
        "capacity": "The capacity must be between 1 and 10"
    }
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Room type not found",
    "timestamp": "2025-11-01 08:22:25"
}
```

### 500 Server Error
```json
{
    "success": false,
    "message": "Internal server error occurred",
    "timestamp": "2025-11-01 08:22:25"
}
```

## Notes

1. All timestamps are in the format "YYYY-MM-DD HH:MM:SS"
2. Price values are in VND (Vietnamese Dong)
3. Response format consistently includes:
   - success: boolean
   - message: string
   - timestamp: string
   - data: object/array (for successful responses)
   - errors: object (for validation errors)

## Changelog

- **2025-10-28**: Initial API release
- **2025-10-31**: Added validation for amenities array
- **2025-11-01**: Updated response format to include timestamps
