@canImpersonate
<section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden animate-fade-in"
    style="animation-delay: 0.25s;">
    <div class="p-6 border-b border-slate-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Mode Impersonasi</h3>
            <p class="text-xs text-slate-500">Masuk sebagai pengguna lain untuk memeriksa dashboard tanpa mengganggu akun
                mereka.</p>
        </div>
        @impersonating
            <div class="flex items-center gap-2 text-amber-500 text-xs font-semibold">
                <span class="uppercase tracking-wider">sedang menyamar</span>
                <span class="text-slate-400">→</span>
                <span class="text-amber-600">{{ auth()->user()->name }}</span>
                <a href="{{ route('impersonate.leave') }}" class="text-amber-400 underline decoration-dotted">Kembali</a>
            </div>
        @endImpersonating
    </div>

    <div class="p-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($users as $user)
            @canBeImpersonated($user)
            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4 flex flex-col justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ $user->name }}</p>
                    <p class="text-[11px] text-slate-500 uppercase tracking-[0.2em]">{{ $user->email }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-slate-400">ID {{ $user->id }}</span>
                    <a href="{{ route('impersonate', $user->id) }}"
                        class="inline-flex items-center gap-2 text-xs font-bold text-slate-50 bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-xl shadow-lg shadow-indigo-100">Impersonate</a>
                </div>
            </div>
            @endCanBeImpersonated
        @empty
            <p class="text-xs text-slate-500">Belum ada pengguna yang dapat di-impersonate.</p>
        @endforelse
    </div>
</section>
@endCanImpersonate
