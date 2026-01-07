{{--
/**
 * ============================================================================
 * HEADER COMPONENT - Dashboard Navigation Bar
 * ============================================================================
 *
 * Komponen header sticky yang menampilkan:
 * - Logo dan branding aplikasi
 * - Device ID dari session
 * - Tombol logout
 *
 * @props None (menggunakan session global)
 * @session device_id - ID perangkat yang sedang login
 */
--}}

<header class="bg-white shadow-sm sticky top-0 z-30">
    <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">

        {{-- Brand Section: Logo + Device Info --}}
        <div class="flex items-center gap-3">
            {{-- Icon Container dengan background biru --}}
            <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                {{-- SVG Icon: Bug/Mosquito Larvae --}}
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

            {{-- Title & Device ID --}}
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Dashboard Monitoring Jentik</h1>
                {{-- Menampilkan Device ID dari session, default 'Guest Device' jika tidak ada --}}
                <p class="text-xs text-slate-500">{{ session('device_id', 'Guest Device') }}</p>
            </div>
        </div>

        {{-- Logout Button Section --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf {{-- CSRF Token untuk keamanan --}}
            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-2" title="Keluar">
                {{-- SVG Icon: Logout --}}
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
