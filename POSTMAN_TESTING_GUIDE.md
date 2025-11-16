# Room Image Upload API - Postman Testing Guide

## Base URL
```
http://localhost
```

---

## 📤 1. Upload Images

### Request
- **Method:** `POST`
- **URL:** `http://localhost/api/rooms/images/upload`
- **Body Type:** `form-data`

### Body (form-data):
| Key | Value | Type |
|-----|-------|------|
| `room_id` | `1` | Text |
| `images[]` | Select image file(s) | File |
| `storage_type` | `local` (optional) | Text |

### Example Response (201 Created):
```json
{
  "success": true,
  "message": "Images uploaded successfully",
  "data": [
    {
      "id": 1,
      "roomId": 1,
      "imageUrl": "/uploads/rooms/img_123456.jpg",
      "storageType": "local",
      "fileSize": 204800,
      "mimeType": "image/jpeg",
      "displayOrder": 1,
      "isPrimary": true
    }
  ]
}
```

---

## ⭐ 2. Set Primary Image

### Request
- **Method:** `PUT`
- **URL:** `http://localhost/api/rooms/images/set-primary`
- **Headers:** `Content-Type: application/json`
- **Body Type:** `raw (JSON)`

### Body:
```json
{
  "image_id": 1,
  "room_id": 1
}
```

### Example Response (200 OK):
```json
{
  "success": true,
  "message": "Primary image set successfully"
}
```

---

## 🔢 3. Update Display Order

### Request
- **Method:** `PUT`
- **URL:** `http://localhost/api/rooms/images/order`
- **Headers:** `Content-Type: application/json`
- **Body Type:** `raw (JSON)`

### Body:
```json
{
  "orders": {
    "1": 1,
    "2": 2,
    "3": 3
  }
}
```

### Example Response (200 OK):
```json
{
  "success": true,
  "message": "Display order updated successfully"
}
```

---

## 🗑️ 4. Delete Image

### Request (Option 1: URL Parameter - Recommended)
- **Method:** `DELETE`
- **URL:** `http://localhost/api/rooms/images/5`
  - Replace `5` with the actual image ID

### Request (Option 2: Request Body - Backward Compatible)
- **Method:** `DELETE`
- **URL:** `http://localhost/api/rooms/images/0`
- **Headers:** `Content-Type: application/json`
- **Body Type:** `raw (JSON)`

### Body (Option 2):
```json
{
  "image_id": 5
}
```

### Example Response (200 OK):
```json
{
  "success": true,
  "message": "Image deleted successfully"
}
```

### Example Response (404 Not Found):
```json
{
  "success": false,
  "message": "Image not found"
}
```

---

## 🏥 5. Health Check

### Request
- **Method:** `GET`
- **URL:** `http://localhost/api/storage/health`
- **Query Params (optional):**
  - `storage_type`: `local` or `cloudinary`

### Example URLs:
- All storage types: `http://localhost/api/storage/health`
- Local only: `http://localhost/api/storage/health?storage_type=local`
- Cloudinary only: `http://localhost/api/storage/health?storage_type=cloudinary`

### Example Response (200 OK):
```json
{
  "success": true,
  "data": {
    "local": {
      "storage_type": "local",
      "status": "healthy",
      "writable": true,
      "disk_free_space": 50000000000,
      "base_path": "public/uploads/rooms"
    },
    "cloudinary": {
      "storage_type": "cloudinary",
      "status": "error",
      "configured": false
    }
  }
}
```

---

## 📊 6. Get Storage Info

### Request
- **Method:** `GET`
- **URL:** `http://localhost/api/storage/info`
- **Query Params (optional):**
  - `storage_type`: `local` or `cloudinary`

### Example URLs:
- All storage types: `http://localhost/api/storage/info`
- Local only: `http://localhost/api/storage/info?storage_type=local`

### Example Response (200 OK):
```json
{
  "success": true,
  "data": {
    "local": {
      "storage_type": "local",
      "current_usage": 10485760,
      "storage_limit": 1073741824,
      "available_space": 1063256064,
      "usage_percentage": 0.98,
      "base_path": "public/uploads/rooms"
    }
  }
}
```

---

## 🔄 7. Switch Storage Provider

### Request
- **Method:** `PUT`
- **URL:** `http://localhost/api/storage/provider`
- **Headers:** `Content-Type: application/json`
- **Body Type:** `raw (JSON)`

### Body:
```json
{
  "provider": "cloudinary"
}
```

### Valid providers:
- `local`
- `cloudinary`

### Example Response (200 OK):
```json
{
  "success": true,
  "message": "Storage provider switched to cloudinary"
}
```

### Example Response (400 Bad Request):
```json
{
  "success": false,
  "message": "Invalid storage provider"
}
```

---

## 🧪 Testing Steps

### Step 1: Upload Images
1. Set method to `POST`
2. URL: `http://localhost/api/rooms/images/upload`
3. Select `Body` tab → `form-data`
4. Add fields:
   - `room_id`: `1`
   - `images[]`: Select image files (can select multiple)
   - `storage_type`: `local`
5. Click `Send`

### Step 2: Set Primary Image
1. Copy an `id` from the upload response
2. Set method to `PUT`
3. URL: `http://localhost/api/rooms/images/set-primary`
4. Select `Body` tab → `raw` → `JSON`
5. Paste the JSON body with the copied ID
6. Click `Send`

### Step 3: Update Display Order
1. Set method to `PUT`
2. URL: `http://localhost/api/rooms/images/order`
3. Select `Body` tab → `raw` → `JSON`
4. Paste the JSON body with image IDs and orders
5. Click `Send`

### Step 4: Delete Image
1. Set method to `DELETE`
2. URL: `http://localhost/api/rooms/images/5` (replace 5 with actual ID)
3. Click `Send`

### Step 5: Health Check
1. Set method to `GET`
2. URL: `http://localhost/api/storage/health`
3. Click `Send`

### Step 6: Get Storage Info
1. Set method to `GET`
2. URL: `http://localhost/api/storage/info`
3. Click `Send`

### Step 7: Switch Provider
1. Set method to `PUT`
2. URL: `http://localhost/api/storage/provider`
3. Select `Body` tab → `raw` → `JSON`
4. Paste the JSON body with provider name
5. Click `Send`

---

## ⚠️ Common Errors

### 400 Bad Request
- Missing required fields
- Invalid data format
- Invalid storage provider

### 404 Not Found
- Image ID doesn't exist
- Room ID doesn't exist

### 500 Internal Server Error
- Database connection issues
- Storage permission issues
- Missing configuration

---

## 💡 Tips

1. **Always check response status codes** - 200/201 = success, 400 = bad request, 404 = not found, 500 = server error
2. **Use Postman Collections** - Save all requests in a collection for easy re-testing
3. **Environment Variables** - Create a Postman environment with `base_url` variable
4. **Test in order** - Upload → Set Primary → Update Order → Delete
5. **Check database** - Verify data is actually saved in the `room_images` table

---

## 📝 Notes

- All responses are in JSON format
- File uploads use `multipart/form-data`
- Other requests use `application/json`
- The `delete` method accepts both URL parameters and request body for flexibility
- Storage type is optional and defaults to the configured default storage

---

Happy Testing! 🚀

