@extends('layouts.app')

@section('title', 'Dashboard - Monitoring Jentik')

@section('content')
    <!-- Memastikan Alpine.js & Tailwind dimuat untuk preview mandiri -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- State Management dengan Alpine JS -->
    <div x-data="{
        showModal: false,
        isActivating: false,
        successMsg: '',
        selectedImage: null,
    
        confirmActuator() {
            this.isActivating = true;
            // Simulasi Request API menggunakan Fetch/Axios bisa ditaruh di sini
            setTimeout(() => {
                this.isActivating = false;
                this.showModal = false;
                this.successMsg = 'Perintah berhasil dikirim ke perangkat!';
                setTimeout(() => this.successMsg = '', 4000);
            }, 2000);
        }
    }" class="min-h-screen bg-slate-50 pb-20">

        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                        <!-- Icon Bug -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m8 2 1.88 1.88" />
                            <path d="M14.12 3.88 16 2" />
                            <path d="M9 7.13v-1a3.003 3.003 0 1 1 6 0v1" />
                            <path d="M12 20c-3.3 0-6-2.7-6-6v-3a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v3c0 3.3-2.7 6-6 6" />
                            <path d="M12 20v-9" />
                            <path d="M6.53 9C4.6 8.8 3 7.1 3 5" />
                            <path d="M6 13H2" />
                            <path d="M3 21c0-2.1 1.7-3.9 3.8-4" />
                            <path d="M20.97 5c0 2.1-1.6 3.8-3.5 4" />
                            <path d="M22 13h-4" />
                            <path d="M17.2 17c2.1.1 3.8 1.9 3.8 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-800 leading-tight">Dashboard Jentik</h1>
                        <!-- Menampilkan User dari Session Laravel -->
                        <p class="text-xs text-slate-500">{{ session('device_id', 'Guest Device') }}</p>
                    </div>
                </div>

                {{-- <form action="{{ route('logout') }}" method="POST"> --}}
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-2" title="Keluar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" x2="9" y1="12" y2="12" />
                    </svg>
                </button>
                </form>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 py-6 space-y-6">

            <!-- Notifikasi Sukses -->
            <div x-show="successMsg" x-transition
                class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center shadow-sm"
                x-cloak>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span x-text="successMsg"></span>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Card 1: Jentik Terdeteksi -->
                <div class="glass-panel p-5 rounded-2xl flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Jentik Terdeteksi (Sesi Terakhir)</p>
                        <h3 class="text-4xl font-bold text-red-500">{{ $latest_detection_count ?? 5 }} <span
                                class="text-lg text-slate-400 font-normal">ekor</span></h3>
                        <p class="text-xs text-red-600 mt-2 font-medium bg-red-50 inline-block px-2 py-1 rounded"> Status
                            Waspada</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-full text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Total Deteksi -->
                <div class="glass-panel p-5 rounded-2xl flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Deteksi Hari Ini</p>
                        <h3 class="text-4xl font-bold text-slate-700">{{ $today_detection_total ?? 24 }} <span
                                class="text-lg text-slate-400 font-normal">kali</span></h3>
                        <p class="text-xs text-green-600 mt-2 font-medium bg-green-50 inline-block px-2 py-1 rounded">
                            Sistem Aktif</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-full text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                            <path d="M3 3v5h5" />
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                            <path d="M16 21h5v-5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Chart & Actuator Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Chart Section -->
                <div class="glass-panel p-5 rounded-2xl lg:col-span-2">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-slate-700">Tren Mingguan</h3>
                        <span class="text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded-full">7 Hari Terakhir</span>
                    </div>
                    <!-- Canvas untuk Chart.js -->
                    <div class="relative h-48 w-full">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>

                <!-- Actuator Control Section -->
                <div
                    class="glass-panel p-5 rounded-2xl flex flex-col justify-center items-center text-center space-y-4 bg-gradient-to-b from-white to-red-50/30">
                    <div class="bg-white p-4 rounded-full shadow-md text-red-500 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                            <line x1="12" x2="12" y1="2" y2="12" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">Kontrol Manual</h3>
                        <p class="text-sm text-slate-500 px-4">Aktifkan pompa/larvasida secara paksa jika deteksi otomatis
                            gagal.</p>
                    </div>
                    <button @click="showModal = true"
                        class="w-full bg-white border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-sm active:scale-95">
                        Basmi Manual
                    </button>
                </div>
            </div>

            <!-- SECTION 1: PHOTO GALLERY (HORIZONTAL SCROLL) -->
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-slate-700">Galeri Foto Deteksi</h3>
                            <p class="text-xs text-slate-400 mt-1">Hasil tangkapan kamera terkini.</p>
                        </div>
                        <span class="text-slate-400 text-xs flex items-center">
                            Geser <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="p-5 bg-slate-50/30">
                    <div class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar">
                        <!-- Loop Data Dummy (Ganti dengan $images dari Controller) -->
                        @php
                            $dummyImages = [
                                [
                                    'id' => 1,
                                    'time' => '10:30 WIB',
                                    'date' => 'Hari Ini',
                                    'count' => 5,
                                    'status' => 'Bahaya',
                                ],
                                [
                                    'id' => 2,
                                    'time' => '09:00 WIB',
                                    'date' => 'Hari Ini',
                                    'count' => 0,
                                    'status' => 'Aman',
                                ],
                                [
                                    'id' => 3,
                                    'time' => '16:45 WIB',
                                    'date' => 'Kemarin',
                                    'count' => 2,
                                    'status' => 'Waspada',
                                ],
                                [
                                    'id' => 4,
                                    'time' => '12:00 WIB',
                                    'date' => 'Kemarin',
                                    'count' => 0,
                                    'status' => 'Aman',
                                ],
                                [
                                    'id' => 5,
                                    'time' => '10:00 WIB',
                                    'date' => 'Kemarin',
                                    'count' => 8,
                                    'status' => 'Bahaya',
                                ],
                            ];
                        @endphp

                        @foreach ($dummyImages as $img)
                            <div @click="selectedImage = {{ json_encode($img) }}"
                                class="flex-none w-48 group bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-all cursor-pointer relative">
                                <div
                                    class="aspect-square bg-slate-100 flex items-center justify-center text-slate-300 group-hover:bg-slate-200 transition-colors relative">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2"
                                                ry="2" />
                                            <circle cx="9" cy="9" r="2" />
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                        </svg>
                                        <span class="text-xs mt-1 font-medium">Foto #{{ $img['id'] }}</span>
                                    </div>
                                    <!-- Badge -->
                                </div>
                                <div class="p-3">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs text-slate-400">{{ $img['time'] }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium truncate">{{ $img['date'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- SECTION 2: HISTORY TABLE CARD -->
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-slate-700">Log Data Riwayat</h3>
                        <p class="text-xs text-slate-400 mt-1">Catatan tekstual seluruh aktivitas deteksi.</p>
                    </div>
                    <button class="text-xs text-blue-600 font-medium hover:underline">Download CSV</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 font-medium uppercase text-xs">
                            <tr>
                                <th class="px-5 py-3">Waktu</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3 text-center">Jumlah</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($dummyImages as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-slate-800">{{ $item['time'] }}</div>
                                        <div class="text-xs text-slate-400">{{ $item['date'] }}</div>
                                    </td>
                                    <td class="px-5 py-3">{{ session('device_id', 'Guest Device') }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span
                                            class="font-bold {{ $item['count'] > 0 ? 'text-red-600' : 'text-slate-400' }}">
                                            {{ $item['count'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $item['status'] === 'Bahaya' ? 'bg-red-100 text-red-700' : ($item['status'] === 'Waspada' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (Dummy) -->
                <div class="p-4 border-t border-slate-100 flex justify-between items-center bg-white">
                    <button class="text-slate-400 text-sm hover:text-slate-600 disabled:opacity-50" disabled>←
                        Sebelumnya</button>
                    <span class="text-xs text-slate-400">Hal 1 dari 5</span>
                    <button class="text-blue-600 text-sm hover:text-blue-700 font-medium">Selanjutnya →</button>
                </div>
            </div>
        </main>

        <!-- Modal Konfirmasi Actuator -->
        <div x-show="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm"
            x-transition.opacity x-cloak>
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 transform transition-all"
                @click.away="showModal = false">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                            <line x1="12" x2="12" y1="2" y2="12" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-semibold text-gray-900">Konfirmasi Aktivasi</h3>
                    <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin mengaktifkan aktuator? Aksi ini tidak
                        dapat dibatalkan.</p>
                </div>
                <div class="mt-6 flex gap-3">
                    <button @click="showModal = false" :disabled="isActivating"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                        Batal
                    </button>
                    <button @click="confirmActuator()" :disabled="isActivating"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm transition-colors flex justify-center items-center">
                        <template x-if="isActivating">
                            <span
                                class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                        </template>
                        <span x-text="isActivating ? 'Memproses...' : 'Ya, Aktifkan'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Image Modal (Lightbox) -->
        <div x-show="selectedImage"
            class="fixed inset-0 bg-black bg-opacity-90 z-[60] flex items-center justify-center p-4 backdrop-blur-sm"
            x-transition.opacity @click="selectedImage = null" x-cloak>
            <!-- Close Button -->
            <button class="absolute top-4 right-4 text-white hover:text-gray-300 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

            <div class="max-w-4xl w-full flex flex-col items-center" @click.stop>
                <template x-if="selectedImage">
                    <div
                        class="relative w-full aspect-video bg-gray-800 rounded-xl overflow-hidden shadow-2xl flex items-center justify-center">
                        <!-- Simulasi Gambar Placeholder -->
                        <div class="w-full h-full bg-slate-800 flex flex-col items-center justify-center text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                <circle cx="9" cy="9" r="2" />
                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                            </svg>
                            <span class="mt-4 text-sm" x-text="'ID Gambar: ' + selectedImage.id"></span>
                        </div>

                        <!-- Overlay Info -->
                        <div
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-sm opacity-75"
                                        x-text="selectedImage.date + ' • ' + selectedImage.time"></p>

                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <!-- Memuat Chart.js secara manual di sini untuk menghindari ReferenceError di previewer -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('weeklyChart');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Jentik Terdeteksi',
                            data: [12, 19, 3, 5, 2, 3, 10],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#2563eb'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
