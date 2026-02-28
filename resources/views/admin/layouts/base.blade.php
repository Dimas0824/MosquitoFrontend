<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Admin - Data Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        .sidebar-item-active {
            background: linear-gradient(to right, rgba(79, 70, 229, 0.12), transparent);
            border-left: 4px solid rgb(79, 70, 229);
            color: rgb(79, 70, 229);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgb(226, 232, 240);
            border-radius: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800">
    <div class="flex h-screen overflow-hidden">
        <main class="flex-1 flex flex-col overflow-hidden relative">
            <div id="adminFilterLoadingBadge" aria-live="polite" aria-hidden="true"
                class="hidden items-center gap-2 absolute top-6 right-8 z-40 px-3 py-1.5 bg-white/95 border border-indigo-100 rounded-full shadow-lg shadow-indigo-100 text-xs font-semibold text-indigo-700 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <svg class="w-3.5 h-3.5 animate-spin text-indigo-600" viewBox="0 0 24 24" fill="none"
                    aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="3"></circle>
                    <path class="opacity-90" fill="currentColor"
                        d="M12 2a10 10 0 0 1 10 10h-3a7 7 0 0 0-7-7V2z"></path>
                </svg>
                <span>Memuat filter...</span>
            </div>

            @include('admin.partials.topbar', ['adminEmail' => $admin_email ?? null])

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar space-y-8 pb-20">
                @yield('content')
            </div>
        </main>
    </div>

    @include('admin.partials.device-modal')
    @include('admin.partials.inference-edit-modal')
    @include('admin.partials.gallery-preview-modal')
    @include('admin.partials.confirm-modal')
    @include('admin.partials.scripts')
    @stack('scripts')
</body>

</html>
