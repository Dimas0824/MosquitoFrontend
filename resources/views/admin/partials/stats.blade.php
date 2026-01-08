<div id="dashboard" class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in" style="animation-delay: 0.1s;">
    <div
        class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:shadow-indigo-50/50 transition-all group overflow-hidden relative">
        <div
            class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 transition-transform group-hover:scale-150">
        </div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="p-4 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                <i data-lucide="cpu" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Device</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['device_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div
        class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:shadow-purple-50/50 transition-all group overflow-hidden relative">
        <div
            class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 transition-transform group-hover:scale-150">
        </div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="p-4 bg-purple-600 text-white rounded-2xl shadow-lg shadow-purple-100">
                <i data-lucide="brain-circuit" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Inferensi</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['inference_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div
        class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:shadow-amber-50/50 transition-all group overflow-hidden relative">
        <div
            class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 transition-transform group-hover:scale-150">
        </div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="p-4 bg-amber-500 text-white rounded-2xl shadow-lg shadow-amber-100">
                <i data-lucide="images" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Media Galeri</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['gallery_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
