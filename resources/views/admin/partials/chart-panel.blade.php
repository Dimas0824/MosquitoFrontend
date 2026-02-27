<section id="chart" class="animate-fade-in" style="animation-delay: 0.15s;">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-linear-to-r from-indigo-50/70 via-white to-cyan-50/50">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Grafik Deteksi Dinamis</h2>
                    <p class="text-sm text-slate-500">Visual realtime untuk memantau pola deteksi jentik.</p>
                </div>
                <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-slate-200 bg-white/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Live via AJAX
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Filter berdasarkan rentang tanggal atau minggu per
                        bulan.</p>
                    <p class="text-xs text-slate-500 mt-1">Week mode menggunakan ISO week (Senin–Minggu).</p>
                </div>

                <form id="adminChartFilterForm" class="flex flex-wrap items-end gap-2">
                    <div>
                        <label for="chartMode"
                            class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-[0.12em]">Mode</label>
                        <select id="chartMode" name="mode"
                            class="bg-white border border-slate-200 text-xs rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="date_range">Rentang Tanggal</option>
                            <option value="week_in_month">Minggu di Bulan</option>
                        </select>
                    </div>

                    <div id="dateRangeFields" class="flex items-end gap-2">
                        <div>
                            <label for="chartDateFrom"
                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-[0.12em]">Dari</label>
                            <input type="date" id="chartDateFrom" name="date_from"
                                class="bg-white border border-slate-200 text-xs rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label for="chartDateTo"
                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-[0.12em]">Sampai</label>
                            <input type="date" id="chartDateTo" name="date_to"
                                class="bg-white border border-slate-200 text-xs rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>

                    <div id="weekFields" class="hidden items-end gap-2">
                        <div>
                            <label for="chartWeek"
                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-[0.12em]">Minggu</label>
                            <select id="chartWeek" name="week"
                                class="bg-white border border-slate-200 text-xs rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}">Minggu {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="chartMonth"
                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-[0.12em]">Bulan</label>
                            <select id="chartMonth" name="month"
                                class="bg-white border border-slate-200 text-xs rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($m === now()->month)>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="chartYear"
                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-[0.12em]">Tahun</label>
                            <input type="number" id="chartYear" name="year" min="2000" max="2100"
                                value="{{ now()->year }}"
                                class="bg-white border border-slate-200 text-xs rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 w-24 transition">
                        </div>
                    </div>

                    <button type="submit"
                        class="px-3.5 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition shadow-sm">
                        Terapkan
                    </button>
                    <button type="button" id="adminChartReset"
                        class="px-3.5 py-2.5 text-xs font-semibold text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                        Reset
                    </button>
                </form>
            </div>

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p id="adminChartTitle" class="text-sm font-semibold text-slate-700">Deteksi Jentik per Hari</p>
                    <p id="adminChartRangeText" class="text-xs text-slate-500 mt-0.5">Memuat data...</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 min-w-28">
                        <p class="text-[10px] uppercase tracking-[0.14em] text-slate-500 font-bold">Total</p>
                        <p id="adminChartTotal" class="text-sm font-extrabold text-slate-800">0</p>
                    </div>
                    <div class="px-3 py-2 rounded-xl bg-indigo-50/70 border border-indigo-100 min-w-28">
                        <p class="text-[10px] uppercase tracking-[0.14em] text-indigo-500 font-bold">Rata-rata</p>
                        <p id="adminChartAverage" class="text-sm font-extrabold text-indigo-700">0.0</p>
                    </div>
                </div>
            </div>

            <div class="relative h-80 rounded-2xl border border-slate-100 bg-linear-to-b from-white to-slate-50/50 p-3">
                <canvas id="adminDetectionsChart"></canvas>
            </div>
        </div>
    </div>
</section>
