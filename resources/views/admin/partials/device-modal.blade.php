<div id="modalDevice"
    class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 sm:p-6 opacity-0 transition-opacity duration-300">
    <div
        class="bg-white w-full max-w-lg sm:max-w-xl rounded-3xl sm:rounded-[2rem] shadow-2xl border border-white transform scale-95 transition-transform duration-300 flex flex-col max-h-[95vh] sm:max-h-[90vh]">
        <!-- Header -->
        <div
            class="px-5 py-5 sm:p-8 bg-slate-50 border-b border-slate-100 flex justify-between items-start flex-shrink-0">
            <div>
                <h3 class="font-black text-lg sm:text-xl text-slate-900 tracking-tight" id="deviceModalTitle">Tambah
                    Perangkat</h3>
                <p class="text-xs text-slate-500 mt-1 hidden sm:block">Kelola data perangkat IoT.</p>
            </div>
            <button onclick="closeModal('modalDevice')"
                class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-slate-400 hover:text-red-500 transition-all flex-shrink-0">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="deviceForm" class="flex flex-col flex-1 min-h-0" method="POST"
            action="{{ route('admin.devices.store') }}">
            @csrf
            <input type="hidden" name="_method" id="deviceFormMethod" value="POST">
            <input type="hidden" name="device_id" id="deviceId" value="">

            <!-- Scrollable Content -->
            <div class="px-5 sm:px-6 py-5 sm:py-6 space-y-5 sm:space-y-6 overflow-y-auto flex-1">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Device
                        Code</label>
                    <div class="relative">
                        <i data-lucide="hash"
                            class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="device_code" id="deviceCode" placeholder="ESP32-LAB-XXXX"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-sm"
                            required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Password
                        Device</label>
                    <div class="relative">
                        <i data-lucide="lock"
                            class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" name="password" id="devicePassword" placeholder="Minimal 6 karakter"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-sm"
                            required>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Saat edit, isi untuk mengganti password perangkat.</p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Lokasi
                        Instalasi</label>
                    <div class="relative">
                        <i data-lucide="map-pin"
                            class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="location" id="deviceLocation" placeholder="Gedung, Lantai, Area..."
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-sm">
                    </div>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Deskripsi</label>
                    <textarea name="description" id="deviceDescription" rows="3"
                        class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-sm resize-none"
                        placeholder="Catatan atau deskripsi perangkat"></textarea>
                </div>
                <div class="flex items-center gap-3 pb-2">
                    <input type="checkbox" name="is_active" id="deviceIsActive" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500" checked>
                    <label for="deviceIsActive" class="text-sm text-slate-600 font-medium">Aktif</label>
                </div>
            </div>

            <!-- Footer dengan Tombol -->
            <div
                class="px-5 sm:px-6 py-5 sm:py-6 border-t border-slate-100 flex flex-col sm:flex-row gap-3 sm:gap-4 flex-shrink-0 bg-white">
                <button type="button" onclick="closeModal('modalDevice')"
                    class="w-full sm:flex-1 px-5 py-3.5 text-sm font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-2xl transition-all order-2 sm:order-1">Batal</button>
                <button type="submit"
                    class="w-full sm:flex-1 px-5 py-3.5 bg-indigo-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:shadow-indigo-200 transition-all order-1 sm:order-2"
                    id="deviceSubmitButton">Simpan Perangkat</button>
            </div>
        </form>
    </div>
</div>
