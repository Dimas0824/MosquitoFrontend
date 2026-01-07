{{--
/**
 * ============================================================================
 * DASHBOARD - Mosquito Larvae Detection System
 * ============================================================================
 *
 * Halaman dashboard utama untuk monitoring sistem deteksi jentik nyamuk.
 *
 * Fitur Utama:
 * - KPI Cards (Jentik terdeteksi & Total deteksi)
 * - Grafik Tren Mingguan (Chart.js)
 * - Kontrol Manual Aktuator
 * - Galeri Foto Deteksi
 * - Tabel Riwayat Deteksi dengan Pagination
 * - Modal Konfirmasi & Lightbox
 *
 * @layout layouts.app
 * @middleware auth.device
 * @controller DashboardController
 *
 * @props (dari controller):
 * - $latest_detection_count (int|null) - Jumlah jentik sesi terakhir
 * - $today_detection_total (int|null) - Total deteksi hari ini
 * - $images (array|null) - Data foto deteksi
 *
 * @session:
 * - device_id (string) - ID perangkat yang sedang login
 */
--}}

@extends('layouts.app')

@section('title', 'Dashboard - Monitoring Jentik')

{{-- ============================================================================
    CUSTOM STYLES - Glass Panel & Scrollbar
    ============================================================================ --}}
@push('styles')
    <style>
        /* Glass panel effect untuk card components */
        .glass-panel {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Custom horizontal scrollbar untuk gallery */
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

{{-- ============================================================================
    MAIN CONTENT
    ============================================================================ --}}
@section('content')
    {{--
        Alpine.js Root Container
        State Management untuk modal, notifications, dan API calls
    --}}
    <div x-data="{
        // Modal States
        showModal: false,
        isActivating: false,
        selectedImage: null,

        // Notification State
        successMsg: '',

        /**
         * Confirm Actuator Activation
         * Mengirim POST request ke API untuk aktivasi manual pompa/larvasida
         * @returns void
         */
        confirmActuator() {
            this.isActivating = true;

            fetch('{{ route('actuator.activate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    this.isActivating = false;
                    this.showModal = false;
                    this.successMsg = data.message || 'Perintah berhasil dikirim ke perangkat!';
                    setTimeout(() => this.successMsg = '', 4000);
                })
                .catch(error => {
                    this.isActivating = false;
                    this.showModal = false;
                    this.successMsg = 'Gagal mengirim perintah. Silakan coba lagi.';
                    setTimeout(() => this.successMsg = '', 4000);
                });
        }
    }" class="min-h-screen bg-slate-50 pb-20">

        {{-- ========== HEADER NAVIGATION ========== --}}
        @include('partials.header')

        {{-- ========== MAIN CONTENT AREA ========== --}}
        <main class="max-w-5xl mx-auto px-4 py-6 space-y-6">

            {{-- Success Notification Toast (Alpine.js controlled) --}}
            <div x-show="successMsg" x-transition
                class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center shadow-sm"
                x-cloak>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span x-text="successMsg"></span>
            </div>

            {{-- ========== KPI CARDS: Jentik Terdeteksi & Total Deteksi ========== --}}
            @include('partials.kpi-cards')

            {{-- ========== CHART & ACTUATOR: Tren Mingguan + Kontrol Manual ========== --}}
            @include('partials.chart-actuator')

            {{-- ========== PHOTO GALLERY: Horizontal Scroll Gallery ========== --}}
            @include('partials.photo-gallery')

            {{-- ========== HISTORY TABLE: Log Deteksi dengan Pagination ========== --}}
            @include('partials.history-table')

        </main>

        {{-- ========== MODALS: Konfirmasi Aktuator & Lightbox Gambar ========== --}}
        @include('partials.modals')


    </div>
@endsection

{{-- ============================================================================
    CHART.JS INITIALIZATION SCRIPT
    ============================================================================
    Script untuk menginisialisasi grafik tren mingguan menggunakan Chart.js
    Data bisa diganti dengan data real dari controller
--}}
@push('scripts')
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
                            data: [12, 19, 3, 5, 2, 3,
                                10
                            ], // TODO: Replace dengan data real dari backend
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
