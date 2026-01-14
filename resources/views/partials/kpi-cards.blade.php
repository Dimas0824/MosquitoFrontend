{{--
/**
 * ============================================================================
 * KPI CARDS COMPONENT - Key Performance Indicators
 * ============================================================================
 *
 * Menampilkan 2 kartu metrik utama:
 * 1. Jentik Terdeteksi (Sesi Terakhir) - Dengan status waspada
 * 2. Total Deteksi Hari Ini - Dengan indikator sistem aktif
 *
 * @props
 * - $latest_detection_count (int|null) - Jumlah jentik terdeteksi terakhir
 * - $today_detection_total (int|null) - Total deteksi hari ini
 */
--}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- ========== CARD 1: Jentik Terdeteksi (Sesi Terakhir) ========== --}}
    <div class="glass-panel p-5 rounded-2xl flex items-center justify-between">
        <div>
            {{-- Label Metrik --}}
            <p class="text-sm font-medium text-slate-500 mb-1">Jentik Terdeteksi (Sesi Terakhir)</p>

            {{-- Nilai Utama dengan default 5 jika tidak ada data --}}
            <h3 class="text-4xl font-bold text-red-500">
                {{ $latest_detection_count ?? 0 }}
                <span class="text-lg text-slate-400 font-normal">ekor</span>
            </h3>

            {{-- Status Badge - Warna merah untuk waspada --}}
            <p class="text-xs text-red-600 mt-2 font-medium bg-red-50 inline-block px-2 py-1 rounded">
                Status Waspada
            </p>
        </div>

        {{-- Icon Indikator Activity (Grafik Heartbeat) --}}
        <div class="bg-red-50 p-3 rounded-full text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        </div>
    </div>

    {{-- ========== CARD 2: Total Deteksi Hari Ini ========== --}}
    <div class="glass-panel p-5 rounded-2xl flex items-center justify-between">
        <div>
            {{-- Label Metrik --}}
            <p class="text-sm font-medium text-slate-500 mb-1">Total Deteksi Hari Ini</p>

            {{-- Nilai Utama dengan default 24 jika tidak ada data --}}
            <h3 class="text-4xl font-bold text-slate-700">
                {{ $today_detection_total ?? 24 }}
                <span class="text-lg text-slate-400 font-normal">kali</span>
            </h3>

            {{-- Status Badge - Warna hijau untuk sistem aktif --}}
            <p class="text-xs text-green-600 mt-2 font-medium bg-green-50 inline-block px-2 py-1 rounded">
                Sistem Aktif
            </p>
        </div>

        {{-- Icon Indikator Refresh/Sync --}}
        <div class="bg-blue-50 p-3 rounded-full text-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                <path d="M3 3v5h5" />
                <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                <path d="M16 21h5v-5" />
            </svg>
        </div>
    </div>
</div>
