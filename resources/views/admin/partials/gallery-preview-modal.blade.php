<div id="galleryPreviewModal"
    class="fixed inset-0 z-[70] hidden items-center justify-center p-4 sm:p-8 bg-slate-950/80 backdrop-blur-sm opacity-0 transition-opacity duration-200"
    aria-hidden="true">
    <div data-gallery-preview-dialog
        class="relative w-full max-w-6xl bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl scale-95 transition-transform duration-200">
        <button type="button" id="galleryPreviewClose"
            class="absolute top-3 right-3 z-10 p-2 rounded-full bg-slate-900/80 text-slate-200 hover:text-white hover:bg-slate-800 transition"
            aria-label="Tutup preview">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>

        <div class="grid lg:grid-cols-[1fr_280px]">
            <div class="bg-slate-950 max-h-[78vh] min-h-[320px] flex items-center justify-center">
                <img id="galleryPreviewImage" src="" alt="Preview galeri"
                    class="max-h-[78vh] w-full object-contain select-none">
            </div>
            <aside class="bg-slate-900/80 border-t lg:border-t-0 lg:border-l border-slate-800 p-5 space-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-indigo-300 font-black">Device</p>
                    <p id="galleryPreviewDevice" class="text-sm text-white font-bold mt-1">-</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-indigo-300 font-black">Deteksi</p>
                    <p id="galleryPreviewLabel" class="text-sm text-slate-100 mt-1">-</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-indigo-300 font-black">Waktu</p>
                    <p id="galleryPreviewCapturedAt" class="text-sm text-slate-200 mt-1">-</p>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Klik area gelap di luar gambar atau tekan <span class="font-semibold text-slate-200">Esc</span> untuk menutup.
                </p>
            </aside>
        </div>
    </div>
</div>
