{{--
/**
 * ============================================================================
 * HISTORY TABLE COMPONENT - Detection Log Table
 * ============================================================================
 *
 * Tabel riwayat deteksi dengan fitur:
 * - Sortable columns (waktu, device ID, jumlah, status)
 * - Pagination controls
 * - Export to CSV button
 * - Responsive overflow scroll
 *
 * @props $dummyImages (array) - Data riwayat deteksi (shared dengan gallery)
 * @session device_id - ID perangkat untuk kolom Device ID
 */
--}}

<div class="glass-panel rounded-2xl overflow-hidden">

    {{-- ========== Header dengan Export Button ========== --}}
    <div class="p-5 border-b border-slate-100 flex justify-between items-center">
        <div>
            <h3 class="font-semibold text-slate-700">Riwayat Deteksi</h3>
            <p class="text-xs text-slate-400 mt-1">Log aktivitas deteksi terkini</p>
        </div>

        {{--
            Export CSV Button
            TODO: Implement export functionality via route('detections.export')
        --}}
        <button class="text-xs text-blue-600 font-medium hover:underline">
            Download CSV
        </button>
    </div>

    {{-- ========== Table Container (Responsive Overflow) ========== --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">

            {{-- Table Header --}}
            <thead class="bg-slate-50 text-slate-500 font-medium uppercase text-xs">
                <tr>
                    <th class="px-5 py-3">Waktu</th>
                    <th class="px-5 py-3">Device ID</th>
                    <th class="px-5 py-3 text-center">Jumlah</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>

            {{-- Table Body --}}
            <tbody class="divide-y divide-slate-100 bg-white">
                {{-- PHP: Prepare data - use $images from controller or fallback to dummy data --}}
                @php
                    $detectionHistory = $images ?? [
                        ['id' => 1, 'time' => '10:30 WIB', 'date' => 'Hari Ini', 'count' => 5, 'status' => 'Bahaya'],
                        ['id' => 2, 'time' => '09:00 WIB', 'date' => 'Hari Ini', 'count' => 0, 'status' => 'Aman'],
                        ['id' => 3, 'time' => '16:45 WIB', 'date' => 'Kemarin', 'count' => 2, 'status' => 'Waspada'],
                    ];
                @endphp

                {{-- Loop data riwayat --}}
                @forelse ($detectionHistory as $item)
                    <tr class="hover:bg-slate-50 transition-colors">

                        {{-- Kolom 1: Waktu & Tanggal (Stack vertical) --}}
                        <td class="px-5 py-3">
                            <div class="font-medium text-slate-800">{{ $item['time'] }}</div>
                            <div class="text-xs text-slate-400">{{ $item['date'] }}</div>
                        </td>

                        {{-- Kolom 2: Device ID dari session --}}
                        <td class="px-5 py-3 text-slate-600">
                            {{ session('device_code', 'Guest Device') }}
                        </td>

                        {{-- Kolom 3: Jumlah Jentik (Center aligned, bold jika > 0) --}}
                        <td class="px-5 py-3 text-center">
                            <span class="font-bold {{ $item['count'] > 0 ? 'text-red-600' : 'text-slate-400' }}">
                                {{ $item['count'] }}
                            </span>
                        </td>

                        {{-- Kolom 4: Status Badge (Warna dinamis) --}}
                        <td class="px-5 py-3">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $item['status'] === 'Bahaya'
                                    ? 'bg-red-100 text-red-700'
                                    : ($item['status'] === 'Waspada'
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : 'bg-green-100 text-green-700') }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm">Belum ada data deteksi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ========== Pagination Controls ========== --}}
    {{-- TODO: Implement actual pagination logic di controller --}}
    <div class="p-4 border-t border-slate-100 flex justify-between items-center bg-white">

        {{-- Previous Button (currently disabled) --}}
        <button class="text-slate-400 text-sm hover:text-slate-600 disabled:opacity-50" disabled>
            ← Sebelumnya
        </button>

        {{-- Page Indicator --}}
        <span class="text-xs text-slate-400">Halaman 1 dari 5</span>

        {{-- Next Button --}}
        <button class="text-blue-600 text-sm hover:text-blue-700 font-medium">
            Selanjutnya →
        </button>
    </div>
</div>
