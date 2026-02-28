<div id="confirmDeletionModal"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div
        class="bg-white rounded-[2rem] w-full max-w-sm shadow-2xl border border-slate-200 transform scale-95 opacity-0 transition-all duration-200">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-lg font-black text-slate-900">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-slate-500 mt-1">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-6 py-5 space-y-3">
            <p id="confirmDeletionMessage" class="text-sm text-slate-700 font-medium">Apakah Anda yakin ingin menghapus
                data ini?</p>
            <div class="text-xs text-slate-500">Pastikan Anda memilih perangkat atau entri yang benar sebelum
                melanjutkan.</div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex gap-3">
            <button type="button" onclick="closeDeletionModal()"
                class="flex-1 px-4 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-2xl hover:bg-slate-200 transition">Batal</button>
            <button type="button" id="confirmDeletionButton"
                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-2xl hover:bg-red-500 transition">Hapus
                Sekarang</button>
        </div>
    </div>
</div>
