{{--
/**
 * ============================================================================
 * PHOTO GALLERY COMPONENT - Horizontal Scrollable Image Gallery
 * ============================================================================
 *
 * Galeri foto horizontal dengan fitur:
 * - Horizontal scroll (custom scrollbar)
 * - Card preview dengan metadata (waktu, status, jumlah jentik)
 * - Klik untuk melihat detail di lightbox modal
 *
 * @props $gallery (array|null) - Array data foto deteksi
 * @alpine-data selectedImage - State untuk lightbox modal
 *
 * Data Structure:
 * [
 *   'id' => int,
 *   'time' => string (e.g., "10:30 WIB"),
 *   'date' => string (e.g., "Hari Ini"),
 *   'count' => int (jumlah jentik),
 *   'status' => string ('Bahaya'|'Waspada'|'Aman')
 * ]
 */
--}}

<div class="glass-panel rounded-2xl overflow-hidden">

    {{-- ========== Header Section ========== --}}
    <div class="p-5 border-b border-slate-100">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-slate-700">Galeri Foto Deteksi</h3>
                <p class="text-xs text-slate-400 mt-1">Hasil tangkapan kamera terkini</p>
            </div>
            {{-- Hint untuk scroll horizontal --}}
            <span class="text-slate-400 text-xs flex items-center gap-1">
                Geser
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </span>
        </div>
    </div>

    {{-- ========== Gallery Container ========== --}}
    <div class="p-5 bg-slate-50/30">
        {{--
            Horizontal Scroll Container
            - flex: membuat layout horizontal
            - overflow-x-auto: enable horizontal scroll
            - custom-scrollbar: class untuk styling scrollbar (didefinisikan di @push('styles'))
        --}}
        <div class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar">

            {{-- Loop semua gambar --}}
            @forelse ($gallery ?? [] as $img)
                {{--
                    Card Item
                    @click="selectedImage = ..." - Set data untuk lightbox modal
                    flex-none: prevent flex shrink
                    w-48: fixed width untuk consistency
                --}}
                <div @click="selectedImage = {{ json_encode($img) }}"
                    class="flex-none w-48 group bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-all cursor-pointer">

                    {{-- Image Placeholder (aspect-square untuk rasio 1:1) --}}
                    <div
                        class="aspect-square bg-slate-100 flex items-center justify-center text-slate-300 group-hover:bg-slate-200 transition-colors relative overflow-hidden">
                        @if (!empty($img['image_src']))
                            <img src="{{ $img['image_src'] }}" alt="Foto {{ $img['id'] }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                {{-- Icon Gambar --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                </svg>
                                <span class="text-xs mt-2 font-medium">Foto #{{ $img['id'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Metadata Footer --}}
                    <div class="p-3 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-1">
                            {{-- Waktu --}}
                            <span class="text-xs text-slate-600 font-medium">{{ $img['time'] }}</span>

                            {{-- Status Badge (dinamis berdasarkan status) --}}
                            <span
                                class="text-xs px-2 py-0.5 rounded
                                {{ $img['status'] === 'Bahaya'
                                    ? 'bg-red-50 text-red-600'
                                    : ($img['status'] === 'Waspada'
                                        ? 'bg-yellow-50 text-yellow-600'
                                        : 'bg-green-50 text-green-600') }}">
                                {{ $img['status'] }}
                            </span>
                        </div>

                        {{-- Tanggal --}}
                        <p class="text-xs text-slate-500">{{ $img['date'] }}</p>

                        {{-- Jumlah Jentik --}}
                        <p class="text-xs text-slate-700 font-semibold mt-1">{{ $img['count'] }} Jentik</p>
                    </div>
                </div>
            @empty
                <div class="text-slate-400 text-sm">Belum ada foto deteksi.</div>
            @endforelse
        </div>
    </div>
</div>
