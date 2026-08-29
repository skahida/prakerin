<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tera Prakerin | Login Autentikasi</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url("{{ asset('assets/img/banner/smk.jpeg') }}") no-repeat center center fixed;
            background-size: cover;
        }

        .overlay {
            background: radial-gradient(circle at center, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.8) 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4 sm:p-6 relative overflow-x-hidden">

    <div class="absolute inset-0 overlay z-0"></div>

    <div class="relative w-full max-w-[420px] animate-[fadeIn_0.6s_ease-out] z-10">

        <div class="glass-card rounded-[2rem] md:rounded-[2.5rem] shadow-2xl overflow-hidden">

            <div class="pt-8 pb-5 px-6 text-center bg-white/50 border-b border-gray-100/50">
                <div
                    class="inline-flex p-3 bg-white rounded-2xl shadow-lg mb-3 transform -rotate-2 hover:rotate-0 transition-transform duration-500">
                    <img src="{{ asset('assets/img/logo/logo-removebg-preview.png') }}" alt="Logo"
                        class="w-12 h-12 md:w-16 md:h-16 object-contain">
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Tera Prakerin</h1>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">
                    Sistem Presensi dan Monitoring
                </p>
            </div>

            <div class="p-6 md:p-10">

                @if (session('error') || session('success') || session('status'))
                    @php
                        $successMessage = session('success') ?? session('status');
                    @endphp

                    <div x-data="{ show: true }" x-show="show" x-transition.opacity
                        class="flex items-center gap-3 p-4 rounded-xl mb-6 text-[11px] md:text-sm font-bold border {{ session('error') ? 'bg-red-50 text-red-600 border-red-100' : 'bg-green-50 text-green-600 border-green-100' }}">

                        <span class="material-icons-round text-lg md:text-xl shrink-0 flex items-center justify-center">
                            {{ session('error') ? 'error' : 'check_circle' }}
                        </span>

                        <span class="leading-tight">
                            {{ session('error') ?? $successMessage }}
                        </span>
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="flex items-start gap-3 p-4 rounded-xl mb-6 text-[11px] md:text-sm font-bold border bg-red-50 text-red-600 border-red-100">
                        <span
                            class="material-icons-round text-lg md:text-xl shrink-0 flex items-center justify-center">error</span>

                        <ul class="space-y-1 leading-tight">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4 md:space-y-5">
                    @csrf

                    <div class="space-y-1.5 group">
                        <label for="username"
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 transition-colors group-focus-within:text-green-600">
                            Username / NIS
                        </label>
                        <div class="relative">
                            <span
                                class="material-icons-round absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-green-600 transition-all duration-300">
                                person
                            </span>
                            <input id="username" type="text" name="username" value="{{ old('username') }}"
                                class="w-full bg-gray-50 border border-gray-100 focus:border-green-500 focus:ring-4 focus:ring-green-500/10 py-4 pl-14 pr-6 rounded-2xl text-sm font-semibold outline-none transition-all"
                                placeholder="Masukkan Username" autocomplete="username" autofocus required>
                        </div>
                    </div>

                    <div class="space-y-1.5 group" x-data="{ show: false }">
                        <label for="password"
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 transition-colors group-focus-within:text-green-600">
                            Password
                        </label>
                        <div class="relative">
                            <span
                                class="material-icons-round absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-green-600 transition-all duration-300 text-[20px]">
                                lock
                            </span>
                            <input id="password" :type="show ? 'text' : 'password'" name="password"
                                class="w-full bg-gray-50 border border-gray-100 focus:border-green-500 focus:ring-4 focus:ring-green-500/10 py-4 pl-14 pr-14 rounded-2xl text-sm font-semibold outline-none transition-all"
                                placeholder="••••••••" autocomplete="current-password" required>
                            <button type="button" @click="show = !show" tabindex="-1"
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors active:scale-90">
                                <span class="material-icons-round text-[20px]"
                                    x-text="show ? 'visibility' : 'visibility_off'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button id="loginBtn" type="submit"
                            class="w-full bg-gray-900 hover:bg-green-700 active:bg-green-800 text-white py-4 rounded-xl md:rounded-2xl font-bold shadow-lg shadow-gray-200 flex items-center justify-center gap-3 transition-all active:scale-[0.97]">
                            <span class="material-icons-round text-lg">login</span>
                            <span>Masuk Dashboard</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50/50 p-5 text-center border-t border-gray-100">
                <p class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-loose">
                    &copy; 2026 Tera Prakerin &bull; SMK AHA Kudus<br>
                    <span class="text-gray-300">TeraDev Stable Edition</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        form.addEventListener('submit', () => {
            if (!form.checkValidity()) {
                return;
            }

            loginBtn.disabled = true;
            loginBtn.classList.add('opacity-90', 'cursor-not-allowed');
            loginBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span class="font-bold">Menghubungkan...</span>
            `;
        });
    </script>
</body>

</html>
