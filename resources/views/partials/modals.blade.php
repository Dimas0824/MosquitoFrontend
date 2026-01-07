{{--
/**
 * ============================================================================
 * MODALS COMPONENT - Confirmation & Lightbox Modals
 * ============================================================================
 *
 * Berisi 2 modal overlay:
 * 1. Modal Konfirmasi Aktuator - Untuk validasi sebelum aktivasi manual
 * 2. Modal Lightbox Gambar - Untuk preview foto deteksi dalam ukuran besar
 *
 * @alpine-data State yang digunakan:
 * - showModal (boolean) - Toggle modal konfirmasi
 * - isActivating (boolean) - Loading state saat proses aktivasi
 * - selectedImage (object|null) - Data gambar untuk lightbox
 * - confirmActuator() (function) - Handler submit aktivasi aktuator
 */
--}}

{{-- ============================================================================
    MODAL 1: KONFIRMASI AKTIVASI AKTUATOR
    ============================================================================ --}}
<div x-show="showModal" x-transition.opacity
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm" x-cloak>

    {{--
        Modal Card
        @click.away="showModal = false" - Tutup modal jika klik di luar card
    --}}
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 transform transition-all"
        @click.away="showModal = false">

        {{-- Modal Content Center Aligned --}}
        <div class="text-center">

            {{-- Icon Circle - Red Power Icon --}}
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                    <line x1="12" x2="12" y1="2" y2="12" />
                </svg>
            </div>

            {{-- Title --}}
            <h3 class="text-lg leading-6 font-semibold text-gray-900">Konfirmasi Aktivasi</h3>

            {{-- Description/Warning --}}
            <p class="text-sm text-gray-500 mt-2">
                Apakah Anda yakin ingin mengaktifkan aktuator?
                Aksi ini akan menjalankan pompa/larvasida secara manual.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 flex gap-3">

            {{-- Cancel Button --}}
            <button @click="showModal = false" :disabled="isActivating"
                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors disabled:opacity-50">
                Batal
            </button>

            {{--
                Confirm Button
                @click="confirmActuator()" - Trigger fungsi Alpine.js untuk API call
                :disabled="isActivating" - Disable saat loading
            --}}
            <button @click="confirmActuator()" :disabled="isActivating"
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm transition-colors flex justify-center items-center disabled:opacity-50">

                {{-- Loading Spinner (conditional render) --}}
                <template x-if="isActivating">
                    <span
                        class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                </template>

                {{-- Button Text (dinamis berdasarkan state) --}}
                <span x-text="isActivating ? 'Memproses...' : 'Ya, Aktifkan'"></span>
            </button>
        </div>
    </div>
</div>

{{-- ============================================================================
    MODAL 2: LIGHTBOX GAMBAR
    ============================================================================ --}}
<div x-show="selectedImage" x-transition.opacity
    class="fixed inset-0 bg-black bg-opacity-90 z-[60] flex items-center justify-center p-4 backdrop-blur-sm"
    @click="selectedImage = null" x-cloak>

    {{-- Close Button (Top Right) --}}
    <button @click="selectedImage = null"
        class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
        </svg>
    </button>

    {{--
        Lightbox Container
        @click.stop - Prevent close saat klik image
    --}}
    <div class="max-w-4xl w-full flex flex-col items-center" @click.stop>

        {{-- Conditional render jika ada selectedImage --}}
        <template x-if="selectedImage">
            <div
                class="relative w-full aspect-video bg-gray-800 rounded-xl overflow-hidden shadow-2xl flex items-center justify-center">

                {{-- Image Placeholder --}}
                <div class="w-full h-full bg-slate-800 flex flex-col items-center justify-center text-slate-500">
                    {{-- Icon Gambar Besar --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                        <circle cx="9" cy="9" r="2" />
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                    </svg>
                    <span class="mt-4 text-sm" x-text="'Foto ID: ' + selectedImage.id"></span>
                </div>

                {{--
                    Info Overlay (Bottom Gradient)
                    Menampilkan metadata foto: tanggal, waktu, jumlah jentik, status
                --}}
                <div
                    class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                    <div class="flex justify-between items-end">

                        {{-- Left: Tanggal & Jumlah Jentik --}}
                        <div>
                            <p class="text-sm opacity-75" x-text="selectedImage.date + ' • ' + selectedImage.time"></p>
                            <p class="text-lg font-semibold mt-1" x-text="selectedImage.count + ' Jentik Terdeteksi'">
                            </p>
                        </div>

                        {{-- Right: Status Badge (Warna dinamis) --}}
                        <span
                            :class="{
                                'bg-red-500': selectedImage.status === 'Bahaya',
                                'bg-yellow-500': selectedImage.status === 'Waspada',
                                'bg-green-500': selectedImage.status === 'Aman'
                            }"
                            class="px-3 py-1 rounded-full text-xs font-medium" x-text="selectedImage.status">
                        </span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
