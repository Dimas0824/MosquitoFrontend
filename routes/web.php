<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActuatorController;
use App\Http\Controllers\InferenceController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminInferenceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AdminDeviceController;

/*
|--------------------------------------------------------------------------
| Web Routes - Mosquito Detection System
|--------------------------------------------------------------------------
|
| Route untuk sistem monitoring deteksi jentik nyamuk.
| Terdiri dari autentikasi, dashboard, dan kontrol aktuator.
|
*/

// ============================================================================
// GUEST ROUTES - Halaman yang dapat diakses tanpa login
// ============================================================================

/**
 * Route: Login Page (GET)
 * Menampilkan form login untuk device authentication
 */
Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

/**
 * Route: Login Process (POST)
 * Memproses autentikasi device berdasarkan device_id dan password
 */
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

/**
 * Route: Welcome/Landing Page
 * Redirect ke dashboard jika sudah login, ke login jika belum
 */
Route::get('/', function () {
    if (session('device_id')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::post('/devices', [AdminDeviceController::class, 'store'])->name('devices.store');
        Route::patch('/devices/{device}', [AdminDeviceController::class, 'update'])->name('devices.update');
        Route::delete('/devices/{device}', [AdminDeviceController::class, 'destroy'])->name('devices.destroy');
        Route::patch('/inference/{inference}', [AdminInferenceController::class, 'update'])->name('inference.update');
    });
});

// ============================================================================
// AUTHENTICATED ROUTES - Halaman yang memerlukan login
// ============================================================================

Route::middleware(['auth.device'])->group(function () {

    /**
     * Route: Dashboard (GET)
     * Halaman utama monitoring dengan KPI, grafik, galeri foto, dan riwayat
     */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /**
     * Route: Logout (POST)
     * Menghapus session dan mengembalikan ke halaman login
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * Route: Activate Actuator (POST)
     * API endpoint untuk mengaktifkan pompa/larvasida secara manual
     * Response: JSON {success: boolean, message: string}
     */
    Route::post('/actuator/activate', [ActuatorController::class, 'activate'])
        ->name('actuator.activate');

    /**
     * Route: Get Detection History (GET)
     * API endpoint untuk mengambil data riwayat deteksi (AJAX/Fetch)
     * Response: JSON array of detection records
     */
    Route::get('/api/detections/history', [HistoryController::class, 'index'])
        ->name('api.detections.history');

    /**
     * Route: Download CSV (GET)
     * Download riwayat deteksi dalam format CSV
     */
    Route::get('/detections/export', [HistoryController::class, 'export'])
        ->name('detections.export');

    /**
     * Route: Inference Results (GET)
     * API endpoint mengambil data inference langsung dari database (by device)
     */
    Route::get('/api/inference/results', [InferenceController::class, 'index'])
        ->name('api.inference.results');
});

