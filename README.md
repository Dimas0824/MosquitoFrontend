# Mosquito Detector Frontend

Dashboard web untuk monitoring perangkat IoT deteksi jentik nyamuk, manajemen data inferensi, dan kontrol operasional admin.

## Ringkasan

Project ini dibangun dengan Laravel 12 dan Tailwind/Vite. Aplikasi menyediakan:

- Login perangkat (`device`) untuk akses dashboard monitoring.
- Panel admin untuk kelola perangkat, edit hasil inferensi, lihat galeri visual.
- Filter admin berbasis server-side (device, status, rentang tanggal).
- Auto cleanup foto lebih dari 30 hari (scheduled task).
- Redis runtime fallback agar aplikasi tetap berjalan saat Redis tidak tersedia.

## Fitur Utama

| Area | Detail |
| --- | --- |
| Dashboard perangkat | KPI deteksi, riwayat, galeri visual, chart, dan kontrol aktuator. |
| API internal | Endpoint riwayat deteksi dan hasil inferensi untuk device yang login. |
| Panel admin | CRUD perangkat, edit data inferensi, dan impersonasi perangkat. |
| Filter data | Devices: pencarian + status. Inference: device + status + rentang tanggal. Gallery: device + rentang tanggal. |
| Retensi foto | Auto cleanup foto lama melalui `photos:prune-old` (default >30 hari). |
| Reliability | Fallback cache/session/queue saat Redis tidak tersedia (`config/redis_fallback.php`). |

## Tech Stack

- Backend: PHP 8.2+, Laravel 12
- Frontend: Blade, Tailwind CSS, Vite
- Database: MySQL (utama)
- Queue: Database queue (default)
- Cache/Session: Redis (dengan fallback ke non-Redis)
- Build tool: Node.js + npm

## Struktur Modul

- `app/Http/Controllers`: alur login device/admin, dashboard, admin management.
- `app/Models`: entitas utama (`Device`, `Image`, `InferenceResult`, dll).
- `resources/views`: UI device dan admin.
- `routes/web.php`: route web + API internal aplikasi.
- `routes/console.php`: command artisan custom + scheduler.
- `config/redis_fallback.php`: pengaturan fallback Redis runtime.

## Arsitektur Singkat

1. Device login menggunakan `device_code + password` dari tabel `devices` dan `device_auth`.
2. Dashboard membaca data dari tabel lokal (`images`, `inference_results`, dll).
3. Admin mengelola data lewat route `/admin/*`.
4. Cleanup foto lama dijalankan via scheduler Laravel.
5. Jika Redis gagal diakses, aplikasi otomatis pindah ke fallback driver.

## Persyaratan

- PHP `^8.2` dengan ekstensi umum Laravel + `pdo_mysql`.
- Composer `^2`.
- Node.js 18+ dan npm.
- MySQL/MariaDB aktif.

## Instalasi Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
```

Atau gunakan script bawaan:

```bash
composer run setup
```

Catatan: script `composer run setup` menjalankan migrasi dengan `--force`.

## Konfigurasi Environment

Contoh variabel penting di `.env`:

```dotenv
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mosquito_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_TIMEOUT=1.0
REDIS_READ_TIMEOUT=1.0
REDIS_READ_WRITE_TIMEOUT=1.0
REDIS_MAX_RETRIES=1

REDIS_FALLBACK_ENABLED=true
REDIS_FALLBACK_CACHE_STORE=failover
REDIS_FALLBACK_SESSION_DRIVER=database
REDIS_FALLBACK_QUEUE_CONNECTION=failover
REDIS_FALLBACK_PROBE_COOLDOWN_SECONDS=15
```

## Menjalankan Aplikasi

Development mode (server + queue + vite):

```bash
composer run dev
```

Manual (jika ingin terpisah):

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Build assets produksi:

```bash
npm run build
```

## Scheduled Task (Auto Delete Foto > 30 Hari)

Command manual:

```bash
php artisan photos:prune-old --days=30
```

Dry run (tanpa menghapus):

```bash
php artisan photos:prune-old --days=30 --dry-run
```

Scheduler sudah didaftarkan di `routes/console.php`:

- `photos:prune-old --days=30`
- Frekuensi: setiap hari `02:00`.

Aktifkan scheduler di server (cron):

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Akun Default Seeder

Jika menjalankan `php artisan migrate --seed`, akun admin default:

- Email: `admin@admin.com`
- Password: `admin123`

Ganti kredensial ini di environment non-lokal.

## Endpoint Penting

- `GET /login` form login device
- `POST /login` autentikasi device
- `GET /dashboard` dashboard device (butuh session device)
- `GET /admin/login` form login admin
- `POST /admin/login` autentikasi admin
- `GET /admin` dashboard admin (butuh session admin)
- `GET /api/detections/history` data riwayat device login
- `GET /api/inference/results` data inference device login

## Testing dan Quality

Jalankan test:

```bash
composer test
```

Format/lint PHP (opsional):

```bash
./vendor/bin/pint
```

## Troubleshooting

| Masalah | Solusi |
| --- | --- |
| `'vite' is not recognized as an internal or external command` | Jalankan `npm install` agar binary `vite` tersedia di `node_modules`. |
| `could not find driver` saat menjalankan Artisan | Aktifkan ekstensi DB PHP yang sesuai, misalnya `pdo_mysql`. |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan optimize:clear`. |
| Redis tiba-tiba down dan request jadi lambat | Gunakan `REDIS_TIMEOUT=1.0`, `REDIS_MAX_RETRIES=1`, dan pastikan fallback aktif (`REDIS_FALLBACK_ENABLED=true`). |
| Scheduler tidak berjalan | Pastikan cron `php artisan schedule:run` aktif setiap menit. |

## Keamanan

- Jangan commit file `.env`.
- Gunakan password admin/device yang kuat di production.
- Rotasi kredensial jika pernah terpapar di repository/log.

## Lisensi

Project ini menggunakan lisensi MIT (mengikuti basis Laravel).
