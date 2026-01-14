<section id="gallery" class="animate-fade-in" style="animation-delay: 0.4s;">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Galeri Visual Deteksi</h2>
            <p class="text-sm text-slate-500">Hasil visual yang dikirim oleh modul kamera.</p>
        </div>
        @php
            $deviceOptions = collect($galleryImages ?? [])
                ->pluck('device_code')
                ->filter()
                ->unique();
        @endphp
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-slate-400">Filter by:</span>
            <select
                class="bg-white border border-slate-200 text-xs font-bold rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-indigo-500">
                @if ($deviceOptions->isNotEmpty())
                    <option value="">Semua Device</option>
                    @foreach ($deviceOptions as $deviceCode)
                        <option value="{{ $deviceCode }}">{{ $deviceCode }}</option>
                    @endforeach
                @else
                    <option disabled>Belum ada device</option>
                @endif
            </select>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse ($galleryImages as $img)
            <div
                class="group relative bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-200 aspect-square cursor-pointer transition-all hover:shadow-2xl hover:-translate-y-2">
                @if (!empty($img['image_url']))
                    <img src="{{ $img['image_url'] }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        alt="Deteksi dari {{ $img['device_code'] }}">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                        <i data-lucide="image" class="w-8 h-8"></i>
                    </div>
                @endif
                <div
                    class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-6">
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
                class="bg-slate-100 rounded-[2rem] aspect-square flex items-center justify-center border border-dashed border-slate-300 text-slate-400">
                <span class="text-sm">Belum ada foto deteksi.</span>
            </div>
        @endforelse
    </div>
</section>
