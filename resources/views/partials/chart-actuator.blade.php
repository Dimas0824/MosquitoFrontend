{{--
/**
 * ============================================================================
 * CHART & ACTUATOR COMPONENT - Analytics dan Kontrol Manual
 * ============================================================================
 *
 * Grid 2 kolom berisi:
 * 1. Chart Tren Mingguan (col-span-2) - Grafik line chart 7 hari terakhir
 * 2. Actuator Control Panel - Tombol aktivasi manual pompa/larvasida
 *
 * @dependency Chart.js - Library untuk rendering grafik
 * @alpine-data showModal - State untuk kontrol modal konfirmasi
 */
--}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ========== SECTION 1: Weekly Trend Chart (2/3 lebar) ========== --}}
    <div class="glass-panel p-5 rounded-2xl lg:col-span-2">
        {{-- Header Chart dengan Badge --}}
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-semibold text-slate-700">Tren Mingguan</h3>
            <span class="text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded-full">
                7 Hari Terakhir
            </span>
        </div>

        {{-- Canvas untuk Chart.js --}}
        {{-- Script initialization ada di @push('scripts') di dashboard utama --}}
        <div class="relative h-48 w-full">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>

    {{-- ========== SECTION 2: Manual Actuator Control (1/3 lebar) ========== --}}
    <div
        class="glass-panel p-5 rounded-2xl flex flex-col justify-center items-center text-center space-y-4 bg-gradient-to-b from-white to-red-50/30">

        {{-- Icon Power Button dengan shadow --}}
        <div class="bg-white p-4 rounded-full shadow-md text-red-500 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                <line x1="12" x2="12" y1="2" y2="12" />
            </svg>
        </div>

        {{-- Judul dan Deskripsi --}}
        <div>
            <h3 class="font-semibold text-slate-800">Kontrol Manual</h3>
            <p class="text-sm text-slate-500 px-4">
                Aktifkan pompa/larvasida secara paksa jika deteksi otomatis gagal.
            </p>
        </div>

        {{--
            Action Button - Trigger Modal Konfirmasi
            @click="showModal = true" - Alpine.js event untuk membuka modal
        --}}
        <button @click="showModal = true"
            class="w-full bg-white border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-sm active:scale-95">
            Basmi Manual
        </button>
    </div>
</div>
