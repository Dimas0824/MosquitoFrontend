<section id="devices" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden animate-fade-in"
    style="animation-delay: 0.2s;">
    <div class="p-8 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Daftar Perangkat IoT</h2>
            <p class="text-sm text-slate-500">Kelola endpoint dan monitoring status hardware.</p>
        </div>
        <div class="flex flex-wrap items-center justify-end gap-3">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="devices_search" placeholder="Cari device/lokasi..."
                    value="{{ $deviceFilters['search'] ?? '' }}"
                    class="w-44 bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                <select name="devices_status"
                    class="bg-white border border-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(($deviceFilters['status'] ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($deviceFilters['status'] ?? '') === 'inactive')>Non-aktif</option>
                </select>
                <button type="submit"
                    class="px-3 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.dashboard') }}#devices"
                    class="px-3 py-2 text-xs font-semibold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    Reset
                </a>
            </form>
            <button onclick="openDeviceModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 text-sm font-bold shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Device
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-bold">
                    <th class="px-8 py-4">Device Code</th>
                    <th class="px-8 py-4">Lokasi Instalasi</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($devices as $device)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                    <i data-lucide="hard-drive"
                                        class="w-4 h-4 text-slate-400 group-hover:text-indigo-600"></i>
                                </div>
                                <span class="font-bold text-slate-900">{{ $device->device_code ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-sm font-medium text-slate-600">{{ $device->location ?? '-' }}</td>
                        <td class="px-8 py-5">
                            @if ($device->is_active)
                                <span
                                    class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-full border border-emerald-200">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> AKTIF
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 text-[10px] font-bold px-3 py-1 rounded-full border border-slate-200">
                                    <span class="w-1.5 h-1.5 bg-slate-500 rounded-full"></span> NON-AKTIF
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.devices.impersonate', $device) }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-100 hover:text-purple-700 transition-all"
                                    title="View as Device">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <button type="button"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all"
                                    title="Edit" data-device-id="{{ $device->id }}"
                                    data-device-code="{{ $device->device_code }}"
                                    data-device-location="{{ $device->location }}"
                                    data-device-description="{{ $device->description }}"
                                    data-device-active="{{ $device->is_active ? '1' : '0' }}"
                                    data-update-action="{{ route('admin.devices.update', $device) }}"
                                    onclick="openDeviceModal(this.dataset)">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.devices.destroy', $device) }}"
                                    class="confirm-delete" data-device-code="{{ $device->device_code }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all"
                                        title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-6 text-center text-sm text-slate-500">Belum ada perangkat
                            terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
