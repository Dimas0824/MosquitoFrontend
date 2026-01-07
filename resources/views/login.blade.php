<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Smart Larva Detector</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center p-4" x-data="{
        deviceId: '',
        password: '',
        showPassword: false,
        loading: false,
        error: '',

        submitForm() {
            this.error = '';

            if (!this.deviceId || !this.password) {
                this.error = 'ID Perangkat dan Kata Sandi wajib diisi.';
                return;
            }

            this.loading = true;

            // Submit form via Laravel
            $refs.loginForm.submit();
        }
    }">
        <div class="max-w-md w-full glass-panel rounded-2xl p-8 animate-fade-in">
            <!-- Header -->
            <div class="text-center mb-8">
                <div><img src="LogoArt.jpeg" alt="" class="mx-auto w-20 h-20 object-contain"></div>
                <h1 class="text-2xl font-bold text-slate-800 mb-2">Smart Larva Detector</h1>
                <p class="text-sm text-slate-600">Masuk dengan ID Perangkat Anda</p>
            </div>

            <!-- Error Alert -->
            <div x-show="error" x-transition
                class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-start"
                x-cloak>
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span x-text="error"></span>
            </div>

            <!-- Laravel Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form x-ref="loginForm" action="{{ route('login') }}" method="POST" @submit.prevent="submitForm()"
                class="space-y-5">
                @csrf

                <!-- Device ID Input -->
                <div>
                    <label for="device_id" class="block text-sm font-medium text-slate-700 mb-2">
                        ID Perangkat
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" />
                                <path d="M7 7h.01" />
                                <path d="M17 7h.01" />
                                <path d="M7 17h.01" />
                                <path d="M17 17h.01" />
                                <path d="M12 3v18" />
                                <path d="m3 12 18 0" />
                            </svg>
                        </div>
                        <input type="text" id="device_id" name="device_id" x-model="deviceId"
                            value="{{ old('device_id') }}"
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Contoh: ESP32-JEN-001" required>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                            x-model="password"
                            class="w-full pl-10 pr-12 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Masukkan kata sandi" required>
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" x-cloak>
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                <path
                                    d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                <line x1="2" x2="22" y1="2" y2="22" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="loading"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <template x-if="loading">
                        <span
                            class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
                    </template>
                    <span x-text="loading ? 'Memproses...' : 'Masuk ke Dashboard'"></span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-500">
                    Sistem Deteksi Jentik Otomatis &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</body>

</html>
