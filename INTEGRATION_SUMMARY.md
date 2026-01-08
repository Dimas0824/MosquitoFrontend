# ✅ Integrasi Laravel Frontend dengan FastAPI Backend - SELESAI

## Ringkasan Perubahan

Sistem telah dikonfigurasi untuk menggunakan **FastAPI backend** sebagai sumber data utama dan **shared MySQL database** untuk akses data deteksi.

## Arsitektur Final

```
ESP32 Camera
    ↓ POST /api/upload
FastAPI Backend (localhost:8080)
    ↓ writes to
MySQL Database (mosquito_db) ← SHARED
    ↑ reads from
Laravel Frontend (localhost:8000)
```

## File yang Diubah/Dibuat

### 1. **Models** (Akses Database Shared)

- ✅ `app/Models/Device.php` - Model untuk devices table
- ✅ `app/Models/Image.php` - Model untuk images table  
- ✅ `app/Models/InferenceResult.php` - Model untuk inference_results table
- ✅ `app/Models/Alert.php` - Model untuk alerts table (user created)
- ✅ `app/Models/DeviceControl.php` - Model untuk device_controls (user created)

### 2. **Services** (FastAPI Integration)

- ✅ `app/Services/MosquitoApiService.php`
  - `getDeviceInfo()` - Login validation via FastAPI
  - `activateServo()` - Control actuator via FastAPI
  - `getDetectionHistory()` - Read dari shared database
  - `getActiveAlerts()` - Read dari shared database
  - `checkHealth()` - FastAPI health check

### 3. **Controllers** (Updated untuk FastAPI)

- ✅ `app/Http/Controllers/AuthController.php`
  - Login menggunakan FastAPI `/api/device/info`
  - Menyimpan credentials di session untuk subsequent calls
  
- ✅ `app/Http/Controllers/DashboardController.php`
  - **REMOVED:** Direct database queries
  - **USING:** `MosquitoApiService->getDetectionHistory()`
  - Membaca dari shared database (bukan via API endpoint)
  
- ✅ `app/Http/Controllers/ActuatorController.php`
  - Control servo via FastAPI `POST /api/device/{code}/activate_servo`

### 4. **Middleware**

- ✅ `app/Http/Middleware/DeviceAuthenticated.php` - Session guard
- ✅ Registered di `bootstrap/app.php` sebagai `auth.device`

### 5. **Views** (Fixed)

- ✅ `resources/views/partials/history-table.blade.php`
  - **FIXED:** Undefined variable `$dummyImages`
  - **NOW:** Menggunakan `$images` dari controller dengan fallback

### 6. **Database Seeders**

- ✅ `database/seeders/DeviceSeeder.php`
  - Seed devices dari backend database
  - Test devices: `test`, `ESP32_test_01`

### 7. **Configuration**

- ✅ `.env` - Added `MOSQUITO_API_URL=https://mosquitobackend-production.up.railway.app`
- ✅ `bootstrap/app.php` - Registered middleware alias

## Cara Kerja Sistem

### Login Flow

```php
1. User input: device_code + password
2. AuthController validates via FastAPI:
   GET /api/device/info (HTTP Basic Auth)
3. FastAPI returns device info
4. Laravel stores in session:
   - device_id
   - device_code
   - device_location
   - api_credentials (for future calls)
5. Redirect to dashboard
```

### Dashboard Data Flow

```php
1. DashboardController->index()
2. Calls: MosquitoApiService->getDetectionHistory()
3. Service queries SHARED MySQL database:
   - Table: images (WHERE device_code = session('device_code'))
   - Join: inference_results
4. Transform data for view
5. Pass to dashboard2.blade.php as $images
```

### Actuator Control Flow

```php
1. User clicks "Activate" button
2. AJAX POST to /actuator/activate
3. ActuatorController calls FastAPI:
   POST /api/device/{code}/activate_servo
4. FastAPI controls device servo
5. Return success/failure to frontend
```

## Credentials untuk Testing

### Device dari Backend Database

| Device Code | Password Location |
|-------------|------------------|
| `test` | Check `device_auth` table |
| `ESP32_test_01` | Check `device_auth` table |

**Note:** Password di-hash dengan bcrypt di backend. Untuk testing, gunakan credentials yang valid dari backend.

## Testing Steps

### 1. Start FastAPI Backend

```bash
# Di terminal backend
python main.py
# Running di https://mosquitobackend-production.up.railway.app
```

### 2. Verify Backend Health

```bash
curl https://mosquitobackend-production.up.railway.app/api/health
# Response: {"status": "healthy", ...}
```

### 3. Start Laravel Frontend

```bash
php artisan serve
# Running di http://localhost:8000
```

### 4. Test Login

1. Buka: `http://localhost:8000/login`
2. Input: Device Code `test` (atau sesuai backend)
3. Input: Password dari backend
4. Klik login

### 5. Verify Dashboard

- ✅ Detection history muncul (dari database)
- ✅ Photo gallery muncul
- ✅ Device info displayed
- ✅ Manual control button works

## Database yang Digunakan

Frontend dan Backend menggunakan **database yang sama**:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mosquito_db
DB_USERNAME=root
DB_PASSWORD=root123
```

### Tables yang Diakses

- `devices` - Device information
- `device_auth` - Authentication (managed by FastAPI)
- `images` - Uploaded images (original & preprocessed)
- `inference_results` - Detection results from Roboflow
- `alerts` - Active alerts
- `device_controls` - Control commands history

## API Endpoints FastAPI yang Digunakan

| Method | Endpoint | Used By | Purpose |
|--------|----------|---------|---------|
| GET | `/api/health` | MosquitoApiService | Health check |
| GET | `/api/device/info` | AuthController | Login validation |
| POST | `/api/device/{code}/activate_servo` | ActuatorController | Control servo |
| GET | `/api/device/{code}/control` | (Optional) | Get control status |

## Troubleshooting

### Error: "Undefined variable $dummyImages"

**Status:** ✅ FIXED
**Solution:** Updated history-table.blade.php to use `$images` with fallback

### Error: "Device ID atau kata sandi tidak valid"

**Solutions:**

1. Pastikan FastAPI backend running
2. Cek credentials di database: `SELECT * FROM device_auth;`
3. Test FastAPI endpoint:

  ```bash
  curl -u "test:password" https://mosquitobackend-production.up.railway.app/api/device/info
  ```

### Dashboard kosong / No detection data

**Solutions:**

1. Cek ada data di database:

   ```sql
   SELECT COUNT(*) FROM images WHERE device_code = 'test';
   SELECT COUNT(*) FROM inference_results;
   ```

1. Upload test image via ESP32 atau backend test script
1. Verify database connection di `.env`

## Migration Status

User sudah membuat migrations untuk:

- ✅ `devices` table
- ✅ `device_auth` table  
- ✅ `images` table
- ✅ `inference_results` table
- ✅ `alerts` table
- ✅ `device_controls` table

**Note:** Migration opsional karena database sudah dikelola FastAPI backend.

## Security Checklist

- ✅ HTTP Basic Auth ke FastAPI
- ✅ Session-based auth untuk dashboard
- ✅ CSRF protection enabled
- ✅ Password tidak disimpan plain text
- ✅ Credentials stored encrypted di session
- ⚠️ Production: Gunakan HTTPS
- ⚠️ Production: Rate limiting

## Next Development

Fitur yang bisa ditambahkan:

- [ ] Real-time dashboard updates (WebSocket)
- [ ] Advanced search & filter
- [ ] Export to PDF/Excel
- [ ] Email/WhatsApp notifications
- [ ] User roles & permissions
- [ ] API rate limiting
- [ ] Image lightbox/zoom
- [ ] Statistics charts (Chart.js)

---

## Summary

✅ **Frontend Laravel terintegrasi penuh dengan FastAPI backend**  
✅ **Menggunakan shared MySQL database untuk data deteksi**  
✅ **Autentikasi via FastAPI endpoint**  
✅ **Control device via FastAPI**  
✅ **Error `$dummyImages` fixed**  
✅ **Seeder untuk test devices dibuat**  
✅ **Documentation lengkap**  

**Status:** READY FOR TESTING 🚀

Test dengan:

1. Start FastAPI backend di port 8080
2. Start Laravel di port 8000  
3. Login dengan device credentials dari backend
4. Dashboard akan menampilkan data dari shared database
