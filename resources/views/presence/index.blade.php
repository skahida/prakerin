@extends('layouts._app')

@section('title', 'Presensi — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">fact_check</span>
                    </span>
                    Presensi
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    @if (auth()->user()->role == 'mentor')
                        Presensi Siswa Bimbingan Hari Ini
                    @elseif (auth()->user()->role == 'student')
                        Riwayat Kehadiran Prakerin
                    @else
                        Manajemen Data Presensi
                    @endif
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

        {{-- ========================================================= --}}
        {{-- ADMIN / SUPER ADMIN --}}
        {{-- ========================================================= --}}
        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin')

            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Tambah Presensi
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Isi data dengan lengkap
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100">
                        <span class="material-icons-round">person_add</span>
                    </div>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Nama --}}
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Nama Lengkap
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                                        person
                                    </span>

                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            pl-12 pr-4 py-3"
                                        placeholder="Masukkan nama lengkap">
                                </div>

                                @error('name')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Username --}}
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Username
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                                        alternate_email
                                    </span>

                                    <input type="text" name="username" value="{{ old('username') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            pl-12 pr-4 py-3"
                                        placeholder="Masukkan username">
                                </div>

                                @error('username')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Password
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                                        lock
                                    </span>

                                    <input type="text" name="password" value="{{ old('password') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            pl-12 pr-4 py-3"
                                        placeholder="Masukkan password">
                                </div>

                                @error('password')
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

                                <span class="material-icons-round text-lg">save</span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- MENTOR --}}
            {{-- ========================================================= --}}
        @elseif (auth()->user()->role == 'mentor')
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Presensi Siswa Hari Ini
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Data kehadiran siswa bimbingan
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <span class="material-icons-round">groups</span>
                    </div>
                </div>

                @if ($presences->isEmpty())
                    <div class="text-center py-24 px-6">
                        <div
                            class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem]
                                flex items-center justify-center mx-auto mb-6">

                            <span class="material-icons-round text-6xl">
                                event_busy
                            </span>
                        </div>

                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                            Belum Ada Presensi
                        </h3>

                        <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                            Tidak ada data presensi yang ditemukan untuk siswa bimbingan Anda hari ini.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/70">
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        No
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Siswa
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Kelas
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        DUDI
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Gelombang
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Tahun Pelajaran
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Hari
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Masuk
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Pulang
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Lokasi Masuk
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Lokasi Pulang
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Status
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Keterangan
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-50">
                                @foreach ($presences as $presence)
                                    @php
                                        $statusData = match ($presence->status) {
                                            'present' => [
                                                'label' => 'Masuk',
                                                'class' => 'bg-teal-50 text-teal-700 border-teal-100',
                                                'icon' => 'check_circle',
                                            ],
                                            'premission' => [
                                                'label' => 'Izin',
                                                'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                'icon' => 'event_note',
                                            ],
                                            'sick' => [
                                                'label' => 'Sakit',
                                                'class' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'icon' => 'medical_services',
                                            ],
                                            'holiday' => [
                                                'label' => 'Libur',
                                                'class' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                'icon' => 'beach_access',
                                            ],
                                            default => [
                                                'label' => 'Alpa',
                                                'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                'icon' => 'cancel',
                                            ],
                                        };
                                    @endphp

                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-6 align-top">
                                            <span class="text-sm font-black text-slate-300 group-hover:text-teal-500">
                                                {{ sprintf('%02d', $loop->iteration) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top min-w-[220px]">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 shrink-0 rounded-xl bg-teal-50 text-teal-600
                                                        flex items-center justify-center font-black text-sm
                                                        border border-teal-100 group-hover:bg-teal-600
                                                        group-hover:text-white transition-all">

                                                    {{ strtoupper(substr($presence->student->name ?? 'S', 0, 1)) }}
                                                </div>

                                                <div>
                                                    <p class="font-black text-slate-800 text-sm">
                                                        {{ $presence->student->name ?? '-' }}
                                                    </p>

                                                    <p class="text-xs text-slate-400 mt-0.5">
                                                        Siswa Prakerin
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100
                                                    text-slate-700 text-xs font-bold">

                                                {{ $presence->student->class->name ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top min-w-[180px]">
                                            <p class="text-sm font-bold text-slate-700">
                                                {{ $presence->student->internshipPlace->name ?? '-' }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <span class="text-sm font-medium text-slate-600">
                                                {{ $presence->student->internshipBatch->batch_name ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <span class="text-sm font-medium text-slate-600">
                                                {{ $presence->student->internshipBatch->academic_year ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <span class="text-sm font-bold text-slate-700">
                                                {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->locale('id')->isoFormat('dddd') : '-' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            @if ($presence->check_in)
                                                <p class="text-sm font-bold text-slate-700">
                                                    {{ \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('H:i:s') }}
                                                </p>

                                                <p class="text-[10px] text-slate-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y') }}
                                                </p>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Belum masuk
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            @if ($presence->check_out)
                                                <p class="text-sm font-bold text-slate-700">
                                                    {{ \Carbon\Carbon::parse($presence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') }}
                                                </p>

                                                <p class="text-[10px] text-slate-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($presence->check_out)->timezone('Asia/Jakarta')->format('d-m-Y') }}
                                                </p>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Belum pulang
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top">
                                            @if ($presence->check_in_location_link)
                                                <div
                                                    class="w-52 h-28 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                                    <iframe class="w-full h-full border-0"
                                                        src="{{ $presence->check_in_location_link }}&output=embed"
                                                        loading="lazy" allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top">
                                            @if ($presence->check_out_location_link)
                                                <div
                                                    class="w-52 h-28 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                                    <iframe class="w-full h-full border-0"
                                                        src="{{ $presence->check_out_location_link }}&output=embed"
                                                        loading="lazy" allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                    rounded-xl text-xs font-bold border {{ $statusData['class'] }}">

                                                <span class="material-icons-round text-sm">
                                                    {{ $statusData['icon'] }}
                                                </span>

                                                {{ $statusData['label'] }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top min-w-[180px]">
                                            <p class="text-sm text-slate-600">
                                                {{ $presence->note ?: '-' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ========================================================= --}}
            {{-- SISWA --}}
            {{-- ========================================================= --}}
        @elseif (auth()->user()->role == 'student')
            {{-- FILTER --}}
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Cari Data Presensi
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Filter riwayat berdasarkan tanggal
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <span class="material-icons-round">manage_search</span>
                    </div>
                </div>

                <div class="p-8">
                    <form method="GET">
                        <div class="flex flex-col lg:flex-row gap-4">
                            <div class="relative flex-1">
                                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    calendar_month
                                </span>

                                <input type="date" name="search" value="{{ request()->input('search') }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                        focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                        pl-12 pr-4 py-3">
                            </div>

                            <button type="submit"
                                class="px-7 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700
                                    text-white text-xs font-black uppercase tracking-widest
                                    transition-all flex items-center justify-center gap-2
                                    shadow-lg shadow-teal-200 active:scale-[0.98]">

                                <span class="material-icons-round text-lg">search</span>
                                Cari
                            </button>

                            <a href="{{ route('presence') }}"
                                class="px-7 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200
                                    text-slate-600 text-xs font-black uppercase tracking-widest
                                    transition-all flex items-center justify-center gap-2">

                                <span class="material-icons-round text-lg">refresh</span>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RIWAYAT --}}
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Data Riwayat Presensi
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Daftar kehadiran selama kegiatan prakerin
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <span class="material-icons-round">history</span>
                    </div>
                </div>

                @if ($presences->isEmpty())
                    <div class="text-center py-24 px-6">
                        <div
                            class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem]
                                flex items-center justify-center mx-auto mb-6">

                            <span class="material-icons-round text-6xl">
                                event_busy
                            </span>
                        </div>

                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                            Data Tidak Ditemukan
                        </h3>

                        <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                            Belum ada riwayat presensi pada tanggal yang dipilih.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/70">
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        No
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Hari
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Tanggal
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Masuk
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Pulang
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Lokasi Masuk
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Lokasi Pulang
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Status
                                    </th>

                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Keterangan
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-50">
                                @foreach ($presences as $presence)
                                    @php
                                        $statusData = match ($presence->status) {
                                            'present' => [
                                                'label' => 'Masuk',
                                                'class' => 'bg-teal-50 text-teal-700 border-teal-100',
                                                'icon' => 'check_circle',
                                            ],
                                            'premission' => [
                                                'label' => 'Izin',
                                                'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                'icon' => 'event_note',
                                            ],
                                            'sick' => [
                                                'label' => 'Sakit',
                                                'class' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'icon' => 'medical_services',
                                            ],
                                            'holiday' => [
                                                'label' => 'Libur',
                                                'class' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                'icon' => 'beach_access',
                                            ],
                                            default => [
                                                'label' => 'Alpa',
                                                'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                'icon' => 'cancel',
                                            ],
                                        };
                                    @endphp

                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-6 align-top">
                                            <span class="text-sm font-black text-slate-300 group-hover:text-teal-500">
                                                {{ sprintf('%02d', ($presences->currentPage() - 1) * $presences->perPage() + $loop->iteration) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <p class="text-sm font-black text-slate-800">
                                                {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->locale('id')->isoFormat('dddd') : '-' }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <p class="text-sm font-bold text-slate-700">
                                                {{ $presence->check_in
                                                    ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y')
                                                    : '-' }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            @if ($presence->check_in)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg
                                                        bg-teal-50 text-teal-700 text-xs font-bold">

                                                    <span class="material-icons-round text-sm">
                                                        login
                                                    </span>

                                                    {{ \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('H:i:s') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Belum masuk
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            @if ($presence->check_out)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg
                                                        bg-blue-50 text-blue-700 text-xs font-bold">

                                                    <span class="material-icons-round text-sm">
                                                        logout
                                                    </span>

                                                    {{ \Carbon\Carbon::parse($presence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Belum pulang
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top">
                                            @if ($presence->check_in_location_link)
                                                <div
                                                    class="w-44 h-28 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                                    <iframe class="w-full h-full border-0"
                                                        src="{{ $presence->check_in_location_link }}&output=embed"
                                                        loading="lazy" allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top">
                                            @if ($presence->check_out_location_link)
                                                <div
                                                    class="w-44 h-28 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                                    <iframe class="w-full h-full border-0"
                                                        src="{{ $presence->check_out_location_link }}&output=embed"
                                                        loading="lazy" allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-6 align-top whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                    rounded-xl text-xs font-bold border {{ $statusData['class'] }}">

                                                <span class="material-icons-round text-sm">
                                                    {{ $statusData['icon'] }}
                                                </span>

                                                {{ $statusData['label'] }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-6 align-top min-w-[180px]">
                                            <p class="text-sm text-slate-600">
                                                {{ $presence->note ?: '-' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if ($presences->hasPages())
                        <div class="px-8 py-6 border-t border-slate-50">
                            {{ $presences->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>
@endsection
