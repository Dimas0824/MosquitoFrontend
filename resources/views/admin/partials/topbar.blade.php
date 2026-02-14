@php
    $deviceCount = $stats['device_count'] ?? 0;
    $inferenceCount = $stats['inference_count'] ?? 0;
    $galleryCount = $stats['gallery_count'] ?? 0;
@endphp

<header
    class="bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 py-4 sm:px-8 sm:py-5 flex flex-col gap-4 z-10">
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Pusat Data & Inferensi</h1>
                <p class="text-xs text-slate-500">Monitoring dan kontrol IoT</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="hidden sm:flex items-center gap-2 text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Keluar
                    </button>
                    <button type="submit"
                        class="sm:hidden inline-flex items-center gap-2 text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
        <div class="flex flex-col gap-2 text-[11px] font-semibold text-slate-500">
            @if (!empty($adminEmail))
                <span>Masuk sebagai {{ $adminEmail }}</span>
            @endif
            @impersonating
                <div class="flex flex-wrap items-center gap-2 text-amber-600">
                    <span>Menampilkan sebagai {{ auth()->user()->name }}</span>
                    <a href="{{ route('impersonate.leave') }}"
                        class="text-amber-500 underline decoration-dotted hover:text-amber-600">Kembali ke admin</a>
                </div>
            @endImpersonating
        </div>
    </div>
    <div
        class="flex items-center gap-2 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-4 py-2 rounded-full border border-emerald-100 justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="uppercase tracking-widest">System Online</span>
        </div>
        <span class="text-slate-500">Sinkron terakhir {{ now()->format('d M H:i') }}</span>
    </div>
</header>
