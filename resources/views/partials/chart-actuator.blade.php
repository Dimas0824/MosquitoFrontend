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
        class="glass-panel p-5 rounded-2xl flex flex-col justify-center items-center text-center space-y-4
        {{ $servo_status['is_active'] ? 'bg-gradient-to-b from-white to-green-50/30' : 'bg-gradient-to-b from-white to-red-50/30' }}">

        {{-- Icon Power Button dengan shadow --}}
        <div
            class="bg-white p-4 rounded-full shadow-md
            {{ $servo_status['is_active'] ? 'text-green-500' : 'text-red-500' }} mb-2">
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
                @if ($servo_status['is_active'])
                    Pompa sedang menunggu ESP untuk diaktifkan
                @else
                    Aktifkan pompa/larvasida.
                @endif
            </p>
        </div>

        {{-- Status Information --}}
        @if ($servo_status['last_activation'])
            <div class="text-xs text-slate-400 px-4">
                @if ($servo_status['is_active'])
                    <div class="flex items-center justify-center gap-1 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span>Menunggu eksekusi sejak
                            {{ $servo_status['last_activation']->diffForHumans() }}</span>
                    </div>
                @else
                    <div class="flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span>Terakhir:
                            {{ $servo_status['last_activation']->format('d M Y, H:i') }}
                            WIB</span>
                    </div>
                @endif
            </div>
        @endif

        {{--
            Action Button - Trigger Modal Konfirmasi
            @click="showModal = true" - Alpine.js event untuk membuka modal
        --}}
        <button @click="showModal = true"
            class="w-full font-semibold py-2 px-4 rounded-xl transition-all shadow-sm active:scale-95
            {{ $servo_status['is_active']
                ? 'bg-white border-2 border-green-500 text-green-600 hover:bg-green-500 hover:text-white'
                : 'bg-white border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white' }}{{ $servo_status['is_active'] ? ' cursor-not-allowed opacity-70' : '' }}"
            @if ($servo_status['is_active']) disabled @endif>
            @if ($servo_status['is_active'])
                <div class="flex items-center justify-center gap-2">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                    </svg>
                    <span>Sedang Menunggu ESP</span>
                </div>
            @else
                Basmi Manual
            @endif
        </button>
    </div>
</div>
