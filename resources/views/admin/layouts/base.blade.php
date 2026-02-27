<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Admin - Data Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        .sidebar-item-active {
            background: linear-gradient(to right, rgba(79, 70, 229, 0.1), transparent);
            border-left: 4px solid #4f46e5;
            color: #4f46e5;
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
            background: #e2e8f0;
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

<body class="bg-[#f8fafc] text-slate-800">
    <div class="flex h-screen overflow-hidden">
        <main class="flex-1 flex flex-col overflow-hidden relative">
            @include('admin.partials.topbar', ['adminEmail' => $admin_email ?? null])

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar space-y-8 pb-20">
                @yield('content')
            </div>
        </main>
    </div>

    @include('admin.partials.device-modal')
    @include('admin.partials.inference-edit-modal')
    @include('admin.partials.confirm-modal')
    @include('admin.partials.scripts')
    @stack('scripts')
</body>

</html>
