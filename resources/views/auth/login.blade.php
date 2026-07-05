<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | Prakerin Tracer</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #090d16 url("{{ asset('../assets/img/banner/smk.jpeg') }}") no-repeat center center fixed;
            background-size: cover;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes blinker {
            50% {
                opacity: 0.3;
            }
        }

        .animate-blink {
            animation: blinker 1s linear infinite;
        }

        @keyframes smoothFadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-core {
            animation: smoothFadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>

<body x-data="themeEngine()" x-init="initEngine()"
    class="flex items-center justify-center min-h-screen p-4 relative overflow-x-hidden antialiased select-none transition-colors duration-300"
    :class="darkMode ? 'bg-[#090d16] text-slate-200' : 'bg-slate-100 text-slate-800'">

    <div class="absolute inset-0 z-0 transition-opacity duration-300"
        :class="darkMode ? 'bg-slate-950/92' : 'bg-slate-900/40'"></div>

    <div class="absolute top-4 right-4 z-20">
        <button @click="toggleTheme()"
            class="flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-semibold shadow-sm transition-all duration-200 active:scale-95"
            :class="darkMode ? 'bg-[#0d1527] border-[#1e293b] text-amber-400 hover:text-amber-300' :
                'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
            <span class="material-icons-round text-sm" x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
        </button>
    </div>

    <div class="relative w-full max-w-[450px] animate-core z-10 mx-auto" x-cloak>

        <div class="rounded-[28px] overflow-hidden border transition-all duration-300"
            :class="darkMode ? 'bg-[#0d1527] border-[#1e293b] shadow-[0_35px_70px_-15px_rgba(0,0,0,0.95)]' :
                'bg-white border-slate-200 shadow-[0_35px_70px_-15px_rgba(15,23,42,0.12)]'">

            <div class="pt-10 pb-6 px-10 text-center relative border-b transition-colors duration-300"
                :class="darkMode ? 'border-[#1e293b] bg-[#090d16]' : 'border-slate-100 bg-slate-50'">

                <div class="inline-flex p-3 rounded-2xl border transition-colors duration-300 mb-3.5"
                    :class="darkMode ? 'border-[#1e293b] bg-[#050a12]' : 'border-slate-200 bg-white'">
                    <img src="{{ asset('../assets/img/logo/logo-removebg-preview.png') }}" alt="Logo"
                        class="w-11 h-11 object-contain">
                </div>

                <h1 class="text-xl font-bold tracking-tight uppercase transition-colors duration-300"
                    :class="darkMode ? 'text-slate-100' : 'text-slate-900'">TERA PRAKERIN</h1>

                <div class="text-[10px] font-bold uppercase tracking-[0.15em] mt-2 flex flex-col items-center gap-1.5"
                    :class="darkMode ? 'text-emerald-500' : 'text-emerald-600'">
                    <div>SISTEM PRESENSI DAN MONITORING</div>
                    <div class="flex items-center gap-1.5 opacity-95 font-semibold tracking-[0.02em] mt-0.5 px-3 py-1 rounded-md border transition-colors duration-300"
                        :class="darkMode ? 'bg-[#050a12] border-[#1e293b]' : 'bg-white border-slate-200 shadow-sm'">
                        <span class="material-icons-round text-[11px] animate-pulse mr-0.5">watch_later</span>

                        <span x-text="liveHour">00</span>
                        <span class="animate-blink">:</span>
                        <span x-text="liveMinute">00</span>
                        <span class="animate-blink">:</span>
                        <span x-text="liveSecond" class="opacity-75 text-[9px]">00</span>

                        <span class="mx-1 opacity-40">&bull;</span>
                        <span class="tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">LIVE</span>
                    </div>
                </div>
            </div>

            <div class="p-8 transition-colors duration-300" :class="darkMode ? 'bg-[#0d1527]' : 'bg-white'">

                @if ($errors->any())
                    <div class="flex items-start gap-2.5 p-3.5 rounded-xl mb-5 text-xs font-medium border text-left shadow-sm"
                        :class="darkMode ? 'bg-red-950/20 text-red-400 border-red-900/50' :
                            'bg-red-50 text-red-700 border-red-200'">
                        <span class="material-icons-round text-base shrink-0"
                            :class="darkMode ? 'text-red-500' : 'text-red-600'">error_outline</span>
                        <ul class="list-none space-y-0.5 leading-relaxed">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-5" novalidate>
                    @csrf

                    <div class="space-y-2 group">
                        <label
                            class="block text-[10px] font-bold uppercase tracking-wider ml-0.5 transition-colors duration-300"
                            :class="darkMode ? 'text-slate-400 group-focus-within:text-emerald-400' :
                                'text-slate-500 group-focus-within:text-emerald-600'">
                            Identity Account
                        </label>
                        <div class="relative">
                            <span
                                class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-[19px] transition-colors duration-300"
                                :class="darkMode ? 'text-slate-500 group-focus-within:text-emerald-400' :
                                    'text-slate-400 group-focus-within:text-emerald-600'">
                                alternate_email
                            </span>
                            <input type="text" name="username"
                                class="w-full border py-3 pl-11 pr-4 rounded-xl text-sm shadow-sm outline-none transition-all duration-300"
                                :class="darkMode ?
                                    'bg-[#050a12] border-[#1e293b] focus:border-emerald-500/50 focus:bg-[#050a12] text-slate-200 placeholder-slate-600 focus:ring-0' :
                                    'bg-slate-50 border-slate-200 focus:border-emerald-500 focus:bg-white text-slate-900 placeholder-slate-400 focus:ring-0'"
                                placeholder="Masukkan username atau NIS" value="{{ old('username') }}" autofocus
                                required>
                        </div>
                    </div>

                    <div class="space-y-2 group" x-data="{ show: false }">
                        <label
                            class="block text-[10px] font-bold uppercase tracking-wider ml-0.5 transition-colors duration-300"
                            :class="darkMode ? 'text-slate-400 group-focus-within:text-emerald-400' :
                                'text-slate-500 group-focus-within:text-emerald-600'">
                            Security Password
                        </label>
                        <div class="relative flex items-center">
                            <span
                                class="material-icons-round absolute left-4 text-[19px] transition-colors duration-300"
                                :class="darkMode ? 'text-slate-500 group-focus-within:text-emerald-400' :
                                    'text-slate-400 group-focus-within:text-emerald-600'">
                                vpn_key
                            </span>

                            <input :type="show ? 'text' : 'password'" name="password" id="passwordField"
                                class="w-full border py-3 pl-11 pr-12 rounded-xl text-sm shadow-sm outline-none transition-all duration-300"
                                :class="darkMode ?
                                    'bg-[#050a12] border-[#1e293b] focus:border-emerald-500/50 focus:bg-[#050a12] text-slate-200 placeholder-slate-600 focus:ring-0' :
                                    'bg-slate-50 border-slate-200 focus:border-emerald-500 focus:bg-white text-slate-900 placeholder-slate-400 focus:ring-0'"
                                placeholder="••••••••" required>

                            <button type="button" @click="show = !show" tabindex="-1"
                                class="absolute right-0 top-0 h-full w-12 flex items-center justify-center focus:outline-none transition-colors duration-200"
                                :class="darkMode ? 'text-slate-500 hover:text-emerald-400' :
                                    'text-slate-400 hover:text-emerald-600'">
                                <span class="material-icons-round text-[19px]"
                                    x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button id="loginBtn" type="submit"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white py-3 rounded-xl font-semibold text-sm shadow-lg flex items-center justify-center gap-2 active:scale-[0.99] transition-all duration-150 tracking-wide">

                            <span id="btnContent"
                                class="flex items-center justify-center gap-1.5 opacity-100 transition-opacity duration-150">
                                <span class="material-icons-round text-base">verified_user</span>
                                <span>Masuk Sistem</span>
                            </span>

                            <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white absolute"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-100" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <div class="py-4 px-8 text-center border-t transition-colors duration-300"
                :class="darkMode ? 'border-[#1e293b] bg-[#090d16] text-slate-500' :
                    'border-slate-100 bg-slate-50 text-slate-400'">
                <p class="text-[9px] font-semibold uppercase tracking-widest leading-relaxed">
                    &copy; 2026 TeraDev &bull; SMK AHA Kudus
                </p>
            </div>
        </div>
    </div>

    <script>
        function themeEngine() {
            return {
                darkMode: false,
                liveHour: '00',
                liveMinute: '00',
                liveSecond: '00',

                initEngine() {
                    if (localStorage.getItem('prakerin-theme') === 'dark') {
                        this.darkMode = true;
                    }
                    this.updateClock();
                    setInterval(() => {
                        this.updateClock();
                    }, 1000);
                },

                updateClock() {
                    const now = new Date();
                    this.liveHour = String(now.getHours()).padStart(2, '0');
                    this.liveMinute = String(now.getMinutes()).padStart(2, '0');
                    this.liveSecond = String(now.getSeconds()).padStart(2, '0');
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('prakerin-theme', this.darkMode ? 'dark' : 'light');
                }
            }
        }

        // Submitting Form Loader Protection
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnContent = document.getElementById('btnContent');
        const btnSpinner = document.getElementById('btnSpinner');

        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) return;

            loginBtn.disabled = true;
            btnContent.classList.remove('opacity-100');
            btnContent.classList.add('opacity-0');
            btnSpinner.classList.remove('hidden');
            loginBtn.classList.add('cursor-not-allowed', 'opacity-85');
        });
    </script>
</body>

</html>
