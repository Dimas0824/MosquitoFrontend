<section id="inference" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden animate-fade-in"
    style="animation-delay: 0.3s;">
    <div class="p-8 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Hasil Inferensi Terbaru</h2>
            <p class="text-sm text-slate-500 italic">Menampilkan inferensi terbaru yang tersimpan di backend.</p>
        </div>
        <form id="adminInferenceFilterForm" method="GET" action="{{ route('admin.dashboard') }}" data-admin-filter-form
            data-date-mode-form class="flex flex-wrap items-center gap-2">
            <select name="inference_device"
                class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Device</option>
                @foreach ($deviceOptions ?? [] as $deviceCode)
                    <option value="{{ $deviceCode }}" @selected(($inferenceFilters['device_code'] ?? '') === $deviceCode)>
                        {{ $deviceCode }}
                    </option>
                @endforeach
            </select>
            <select name="inference_status"
                class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Status</option>
                @foreach ($inferenceStatusOptions ?? [] as $statusOption)
                    <option value="{{ $statusOption }}" @selected(($inferenceFilters['status'] ?? '') === $statusOption)>
                        {{ $statusOption }}
                    </option>
                @endforeach
            </select>
            <select name="inference_date_mode" data-date-mode-select
                class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="range" @selected(($inferenceFilters['date_mode'] ?? 'exact') === 'range')>Rentang Tanggal</option>
                <option value="exact" @selected(($inferenceFilters['date_mode'] ?? 'exact') === 'exact')>Tanggal Spesifik</option>
            </select>
            <div data-date-mode-group="range" class="flex items-center gap-2">
                <input type="date" name="inference_date_from" value="{{ $inferenceFilters['date_from'] ?? '' }}"
                    class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="date" name="inference_date_to" value="{{ $inferenceFilters['date_to'] ?? '' }}"
                    class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div data-date-mode-group="exact" class="hidden items-center">
                <input type="date" name="inference_date" value="{{ $inferenceFilters['date'] ?? '' }}"
                    title="Tanggal spesifik"
                    class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                class="px-3 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition">
                Terapkan
            </button>
            <button type="button"
                class="admin-filter-reset px-3 py-2 text-xs font-semibold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                Reset
            </button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-bold">
                    <th class="px-8 py-4">Timestamp</th>
                    <th class="px-8 py-4">Source Device</th>
                    <th class="px-8 py-4">Detection Status</th>
                    <th class="px-8 py-4 text-center">Score</th>
                    <th class="px-8 py-4 text-right">Total Jentik</th>
                    <th class="px-8 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($inferenceResults as $result)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5 text-xs font-medium text-slate-500">{{ $result['timestamp'] }}</td>
                        <td class="px-8 py-5 font-bold text-slate-700">{{ $result['device_code'] }}</td>
                        <td class="px-8 py-5">
                            <span
                                class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs font-bold border border-indigo-100">{{ $result['label'] }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if (!empty($result['score']))
                                <div class="w-24 bg-slate-100 h-2 rounded-full mx-auto overflow-hidden">
                                    <div class="bg-indigo-500 h-full" style="width: {{ min($result['score'], 100) }}%">
                                    </div>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-indigo-600 mt-1 block tracking-wider">{{ $result['score'] }}%</span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right text-sm font-semibold text-slate-700">
                            {{ $result['total_jentik'] ?? 0 }}</td>
                        <td class="px-8 py-5 text-right">
                            <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100 transition"
                                onclick="openInferenceEditModal({
                                    id: '{{ $result['id'] }}',
                                    timestamp: '{{ $result['timestamp'] }}',
                                    device_code: '{{ $result['device_code'] }}',
                                    label: '{{ $result['label'] }}',
                                    score: '{{ $result['raw_score'] ?? '' }}',
                                    total_jentik: '{{ $result['total_jentik'] ?? 0 }}'
                                })">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-6 text-center text-sm text-slate-500">Belum ada data
                            inferensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-8 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-slate-500">
            Menampilkan {{ $inferenceResults->firstItem() ?? 0 }}-{{ $inferenceResults->lastItem() ?? 0 }} dari
            {{ $inferenceResults->total() }} data inferensi
        </p>
        <div class="admin-pagination text-xs">
            {{ $inferenceResults->onEachSide(1)->links('vendor.pagination.admin') }}
        </div>
    </div>
</section>
