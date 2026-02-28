<section id="gallery" class="animate-fade-in" style="animation-delay: 0.4s;">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Galeri Visual Deteksi</h2>
            <p class="text-sm text-slate-500">Hasil visual yang dikirim oleh modul kamera.</p>
        </div>
        <form id="adminGalleryFilterForm" method="GET" action="{{ route('admin.dashboard') }}" data-admin-filter-form
            data-date-mode-form class="flex flex-wrap items-center gap-2">
            <select name="gallery_device"
                class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Device</option>
                @foreach ($deviceOptions ?? [] as $deviceCode)
                    <option value="{{ $deviceCode }}" @selected(($galleryFilters['device_code'] ?? '') === $deviceCode)>
                        {{ $deviceCode }}
                    </option>
                @endforeach
            </select>
            <select name="gallery_date_mode" data-date-mode-select
                class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="range" @selected(($galleryFilters['date_mode'] ?? 'exact') === 'range')>Rentang Tanggal</option>
                <option value="exact" @selected(($galleryFilters['date_mode'] ?? 'exact') === 'exact')>Tanggal Spesifik</option>
            </select>
            <div data-date-mode-group="range" class="flex items-center gap-2">
                <input type="date" name="gallery_date_from" value="{{ $galleryFilters['date_from'] ?? '' }}"
                    class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="date" name="gallery_date_to" value="{{ $galleryFilters['date_to'] ?? '' }}"
                    class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div data-date-mode-group="exact" class="hidden items-center">
                <input type="date" name="gallery_date" value="{{ $galleryFilters['date'] ?? '' }}"
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
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse ($galleryImages as $img)
            @php
                $isZoomable = !empty($img['image_url']);
            @endphp
            <div
                class="group relative bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200/80 aspect-square transition-all duration-300 {{ $isZoomable ? 'cursor-pointer hover:shadow-2xl hover:shadow-indigo-100/70 hover:-translate-y-1.5 hover:border-indigo-200/70 focus:outline-none focus:ring-2 focus:ring-indigo-400/70' : 'cursor-default' }}"
                @if ($isZoomable)
                    role="button" tabindex="0" data-gallery-item data-gallery-device="{{ $img['device_code'] }}"
                    data-gallery-label="{{ $img['label'] }}" data-gallery-score="{{ $img['score'] ?? '' }}"
                    data-gallery-captured-at="{{ $img['captured_at'] ?? '-' }}"
                @endif>
                @if (!empty($img['image_url']))
                    <img src="{{ $img['image_url'] }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Deteksi dari {{ $img['device_code'] }}">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                        <i data-lucide="image" class="w-8 h-8"></i>
                    </div>
                @endif
                @if ($isZoomable)
                    <div
                        class="absolute top-3 right-3 px-2 py-1 rounded-full bg-slate-900/80 text-white text-[10px] font-bold tracking-wide inline-flex items-center gap-1 opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                        <i data-lucide="zoom-in" class="w-3 h-3"></i>
                        Zoom
                    </div>
                @endif
                <div
                    class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/35 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-5">
                    <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-300 space-y-1">
                        <p class="text-[10px] text-indigo-400 font-black uppercase tracking-[0.2em]">
                            {{ $img['device_code'] }}</p>
                        <p class="text-sm text-white font-bold leading-tight">
                            {{ $img['label'] }}{{ $img['score'] ? ' (' . $img['score'] . '%)' : '' }}</p>
                        <p class="text-xs text-slate-200">{{ $img['captured_at'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="bg-slate-100 rounded-4xl aspect-square flex items-center justify-center border border-dashed border-slate-300 text-slate-400">
                <span class="text-sm">Belum ada foto deteksi.</span>
            </div>
        @endforelse
    </div>

    <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-slate-500">
            Menampilkan {{ $galleryImages->firstItem() ?? 0 }}-{{ $galleryImages->lastItem() ?? 0 }} dari
            {{ $galleryImages->total() }} foto
        </p>
        <div class="admin-pagination text-xs">
            {{ $galleryImages->onEachSide(1)->links('vendor.pagination.admin') }}
        </div>
    </div>
</section>
