<div id="inferenceEditModal"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 opacity-0 transition">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 transform transition duration-200 scale-95">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-indigo-500">Edit Inferensi</p>
                <h3 class="text-lg font-semibold text-slate-800" id="inferenceEditTitle">Perbarui Data</h3>
                <p class="text-xs text-slate-500" id="inferenceEditSubtitle"></p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeInferenceEditModal()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="inferenceEditForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600">Device</label>
                    <input type="text" id="inferenceDevice"
                        class="mt-2 w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50" readonly>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Status</label>
                    <input type="text" name="status" id="inferenceStatus" required
                        class="mt-2 w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Total Jentik</label>
                        <input type="number" name="total_jentik" id="inferenceTotalJentik" min="0" required
                            class="mt-2 w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Score (%)</label>
                        <input type="number" name="score" id="inferenceScore" min="0" max="100"
                            step="0.1"
                            class="mt-2 w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="0-100">
                    </div>
                </div>
            </div>
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeInferenceEditModal()"
                    class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
