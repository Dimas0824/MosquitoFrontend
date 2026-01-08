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
            {{-- Image Icon: Bug/Mosquito Larvae --}}
            <div><img src="LogoArt.jpeg" alt="" class="mx-auto w-10 h-10 object-contain"></div>

            {{-- Title & Device ID --}}
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Dashboard Smart Larva Detector</h1>
                {{-- Menampilkan Device ID dari session, default 'Guest Device' jika tidak ada --}}
                <p class="text-xs text-slate-500">{{ session('device_code', 'Guest Device') }}</p>
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
