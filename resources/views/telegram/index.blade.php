@extends('layouts._app')

@section('title', 'Telegram — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">send</span>
                    </span>
                    Telegram
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Konfigurasi Bot dan Webhook Telegram
                </p>
            </div>
        </div>

        {{-- ===================== NOTIFIKASI ===================== --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-100 text-teal-700 text-sm flex items-start gap-3">
                <span class="material-icons-round text-teal-500">check_circle</span>

                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 text-sm">
                <div class="flex items-start gap-3">
                    <span class="material-icons-round text-rose-500">error</span>

                    <div>
                        <p class="font-bold mb-1">Terjadi Kesalahan</p>

                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===================== KONFIGURASI BOT ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        Konfigurasi Bot Telegram
                    </h3>

                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Masukkan token dan informasi bot Telegram
                    </p>
                </div>

                <div
                    class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100">
                    <span class="material-icons-round">smart_toy</span>
                </div>
            </div>

            <div class="p-8">
                <form id="telegramForm" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Bot Token --}}
                        <div class="space-y-1.5">
                            <label for="botToken" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Bot Token
                            </label>

                            <div class="relative">
                                <span
                                    class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                                    key
                                </span>

                                <input type="text" id="botToken" name="botToken"
                                    value="{{ old('botToken', $telegram->bot_token ?? '') }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                        focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                        pl-12 pr-4 py-3"
                                    placeholder="Masukkan Bot Token">
                            </div>

                            @error('botToken')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Username --}}
                        <div class="space-y-1.5">
                            <label for="username" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Username
                            </label>

                            <div class="relative">
                                <span
                                    class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                                    alternate_email
                                </span>

                                <input type="text" id="username" name="username"
                                    value="{{ old('username', $telegram->username ?? '') }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                        focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                        pl-12 pr-4 py-3"
                                    placeholder="Masukkan Username">
                            </div>

                            @error('username')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="message" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Message
                            </label>

                            <textarea id="message" name="message" rows="5"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                    focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                    px-4 py-3 resize-y"
                                placeholder="Masukkan pesan untuk melakukan pengecekan bot">{{ old('message', $telegram->message ?? '') }}</textarea>

                            @error('message')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700
                                text-white text-xs font-black uppercase tracking-widest
                                transition-all flex items-center justify-center gap-2
                                shadow-xl shadow-teal-200 active:scale-[0.98]">

                            <span class="material-icons-round text-lg">
                                search
                            </span>

                            Check
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== WEBHOOK TELEGRAM ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        Webhook Telegram
                    </h3>

                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Hubungkan bot Telegram dengan aplikasi
                    </p>
                </div>

                <div
                    class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                    <span class="material-icons-round">webhook</span>
                </div>
            </div>

            <div class="p-8">

                {{-- Informasi --}}
                <div
                    class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 text-sm flex items-start gap-3">
                    <span class="material-icons-round text-blue-500">info</span>

                    <div>
                        <p class="font-bold">Informasi Webhook</p>
                        <p class="mt-0.5">
                            Pastikan pengecekan bot berhasil sebelum mengaktifkan webhook Telegram.
                        </p>
                    </div>
                </div>

                <form id="telegramSetWebhookForm" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-5">

                        {{-- Bot Token --}}
                        <div class="space-y-1.5">
                            <label for="webhookBotToken"
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Bot Token
                            </label>

                            <div class="relative">
                                <span
                                    class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                                    key
                                </span>

                                <input type="text" id="webhookBotToken" name="botToken"
                                    value="{{ old('botToken', $telegram->bot_token ?? '') }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-100
                                        text-slate-500 text-sm pl-12 pr-4 py-3 cursor-not-allowed"
                                    placeholder="Bot Token belum tersedia" readonly>
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="space-y-1.5">
                            <label for="webhookMessage"
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Message
                            </label>

                            <textarea id="webhookMessage" name="message" rows="4"
                                class="w-full rounded-2xl border-slate-200 bg-slate-100
                                    text-slate-500 text-sm px-4 py-3 resize-none cursor-not-allowed"
                                placeholder="Pesan belum tersedia" readonly>{{ old('message', $telegram->message ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-slate-800 hover:bg-slate-900
                                text-white text-xs font-black uppercase tracking-widest
                                transition-all flex items-center justify-center gap-2
                                shadow-xl shadow-slate-200 active:scale-[0.98]">

                            <span class="material-icons-round text-lg">
                                webhook
                            </span>

                            Set Webhook
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
