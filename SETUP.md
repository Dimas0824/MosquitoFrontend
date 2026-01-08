# Mosquito Detector Frontend - Laravel

Frontend dashboard untuk sistem deteksi jentik nyamuk menggunakan Laravel.

## Prerequisites

- PHP 8.2+
- Composer
- MySQL/MariaDB
- Backend API berjalan di `https://mosquitobackend-production.up.railway.app`

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Configuration

File `.env` sudah dikonfigurasi dengan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mosquito_db
DB_USERNAME=root
DB_PASSWORD=root123

MOSQUITO_API_URL=https://mosquitobackend-production.up.railway.app
```

Sesuaikan kredensial database dan URL backend API jika perlu.

### 3. Database Migration

Jalankan migration untuk membuat tabel `devices`:

```bash
php artisan migrate
```

### 4. Seed Sample Data (Optional)

Untuk testing, seed device credentials:

```bash
php artisan db:seed --class=DeviceSeeder
```

Sample devices yang akan dibuat:

- **Device Code:** `ESP32_TEST_01` | **Password:** `password123`
- **Device Code:** `ESP32-JEN-001` | **Password:** `mosquito2026`

### 5. Start Development Server

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## Testing

### 1. Test Login

Buka browser dan akses: `http://localhost:8000/login`

Gunakan credentials:

- **ID Perangkat:** `ESP32_TEST_01`
- **Kata Sandi:** `password123`

### 2. Test API Connection

Pastikan backend API berjalan di `https://mosquitobackend-production.up.railway.app`. Frontend akan:

1. Validasi credentials dengan endpoint `GET /api/device/info`
2. Menyimpan session device
3. Redirect ke dashboard

### 3. Dashboard Features

Setelah login berhasil, Anda akan diarahkan ke dashboard yang menampilkan:

- Device information
- Detection statistics (to be integrated)
- Image gallery (to be integrated)
- Detection history (to be integrated)

## Architecture

### Authentication Flow

```
User Login Form
    ↓
AuthController::login
    ↓
MosquitoApiService::getDeviceInfo (HTTP Basic Auth)
    ↓
Backend API validates credentials
    ↓
Store device info in session
    ↓
Redirect to Dashboard
```

### File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       # Login/logout logic
│   │   ├── DashboardController.php  # Dashboard display
│   │   └── ActuatorController.php   # Manual actuator control
│   └── Middleware/
│       └── DeviceAuthenticated.php  # Session-based auth
├── Models/
│   └── Device.php                   # Device model
└── Services/
    └── MosquitoApiService.php       # Backend API client

database/
├── migrations/
│   └── 2026_01_07_000001_create_devices_table.php
└── seeders/
    └── DeviceSeeder.php

resources/
└── views/
    ├── login.blade.php              # Login form
    └── dashboard2.blade.php         # Dashboard view
```

## API Integration

### MosquitoApiService Methods

```php
// Validate device credentials
$apiService->validateCredentials($deviceCode, $password);

// Get device info from backend
$deviceInfo = $apiService->getDeviceInfo($deviceCode, $password);

// Check backend API health
$isHealthy = $apiService->checkHealth();
```

### Backend API Endpoints Used

- `GET /api/device/info` - Get device details (dengan Basic Auth)
- `GET /api/health` - Health check

## Session Management

Session menyimpan:

- `device_id` - UUID device dari database local
- `device_code` - Kode device (ESP32_TEST_01, dll)
- `device_location` - Lokasi device
- `device_info` - Full device info dari backend API
- `api_credentials` - Credentials untuk subsequent API calls

## Security

- HTTP Basic Authentication dengan backend API
- Session-based authentication untuk web dashboard
- Password hashing menggunakan Laravel's Hash
- Middleware `auth.device` untuk protect routes

## Development

### Adding New Features

1. Tambahkan route di `routes/web.php`
2. Buat controller method
3. Update view jika perlu
4. Test dengan backend API

### Environment Variables

```env
MOSQUITO_API_URL=https://mosquitobackend-production.up.railway.app  # Backend API URL
DB_DATABASE=mosquito_db                 # Database name
```

## Troubleshooting

### Login gagal dengan "Device ID atau kata sandi tidak valid"

1. Pastikan backend API berjalan di `https://mosquitobackend-production.up.railway.app`
2. Test backend health: `curl https://mosquitobackend-production.up.railway.app/api/health`
3. Cek credentials di backend database
4. Lihat log Laravel: `storage/logs/laravel.log`

### Session tidak tersimpan

1. Pastikan `SESSION_DRIVER=database` di `.env`
2. Jalankan migration: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`

### Database connection error

1. Cek MySQL/MariaDB berjalan
2. Verifikasi credentials di `.env`
3. Create database: `CREATE DATABASE mosquito_db;`

## Next Steps

- [ ] Integrate detection history dari backend API
- [ ] Image gallery dari inference results
- [ ] Manual actuator control
- [ ] Export CSV functionality

## License

Proprietary - Smart Larva Detector System
