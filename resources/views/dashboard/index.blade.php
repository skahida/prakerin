@extends('layouts._app')

@section('title', 'Dashboard')

@section('content')
    {{-- ============================================================
         CSS langsung di Blade agar tidak bergantung pada @stack styles
         ============================================================ --}}
    @once
        {{-- Select2 --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <style>
            /*
                                                                                                    |--------------------------------------------------------------------------
                                                                                                    | Google Maps GPS pada header kartu siswa
                                                                                                    |--------------------------------------------------------------------------
                                                                                                    */
            .student-map-header {
                position: relative;
                display: block;
                width: 100%;
                height: 190px;
                min-height: 190px;
                overflow: hidden;
                background: #e2e8f0;
            }

            #student-card-map {
                display: block;
                width: 100%;
                height: 190px;
                border: 0;
                margin: 0;
                padding: 0;
                background: #e2e8f0;
            }

            .student-map-loading {
                position: absolute;
                inset: 0;
                z-index: 10;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: #f1f5f9;
                color: #94a3b8;
                transition: opacity 220ms ease;
            }

            .student-map-loading.is-hidden {
                opacity: 0;
                pointer-events: none;
            }

            .student-map-label {
                position: absolute;
                left: 14px;
                bottom: 14px;
                z-index: 20;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                max-width: calc(100% - 28px);
                padding: 7px 10px;
                border-radius: 999px;
                background: rgb(15 23 42 / 0.78);
                color: #ffffff;
                font-size: 10px;
                font-weight: 700;
                line-height: 1;
                backdrop-filter: blur(8px);
                box-shadow: 0 8px 18px rgb(15 23 42 / 0.16);
                pointer-events: none;
            }

            .student-map-label-dot {
                width: 7px;
                height: 7px;
                flex: none;
                border-radius: 999px;
                background: #2dd4bf;
                box-shadow: 0 0 0 4px rgb(45 212 191 / 0.18);
            }

            .student-map-label.is-fallback .student-map-label-dot {
                background: #fbbf24;
                box-shadow: 0 0 0 4px rgb(251 191 36 / 0.18);
            }

            .student-profile-content {
                position: relative;
                z-index: 30;
                background: #ffffff;
            }

            /* Select2 modern style */
            .select2-container--default .select2-selection--single {
                height: 46px !important;
                border-radius: 16px !important;
                border: 1px solid #e2e8f0 !important;
                background-color: #f8fafc !important;
                padding: 8px 12px !important;
                display: flex;
                align-items: center;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #334155 !important;
                font-size: 0.875rem !important;
                line-height: 1.5 !important;
                padding-left: 0 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 44px !important;
                right: 10px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__placeholder {
                color: #94a3b8 !important;
            }

            .select2-dropdown {
                border-radius: 16px !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1) !important;
                overflow: hidden;
            }

            .select2-search--dropdown .select2-search__field {
                border-radius: 12px !important;
                border: 1px solid #e2e8f0 !important;
                padding: 8px 12px !important;
            }

            .select2-results__option {
                padding: 10px 14px !important;
                font-size: 0.875rem !important;
            }

            .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
                background-color: #0d9488 !important;
            }

            .select2-container {
                width: 100% !important;
            }
        </style>
    @endonce

    @php
        $dashboardRole = auth()->user()->role;

        $dashboardSubtitle = match ($dashboardRole) {
            'student' => 'Ringkasan Aktivitas Prakerin Siswa',
            'mentor' => 'Monitoring Siswa Bimbingan Prakerin',
            'admin' => 'Ringkasan Pengelolaan Prakerin',
            'super-admin' => 'Ringkasan dan Analisis Sistem Prakerin',
            default => 'Ringkasan Aktivitas Sistem',
        };

        /*
        |--------------------------------------------------------------------------
        | Koordinat peta DUDI siswa
        |--------------------------------------------------------------------------
        | Mendukung beberapa kemungkinan nama kolom:
        | - lat / long
        | - latitude / longitude
        | - lng
        */
        $studentInternshipPlace = $dashboardRole === 'student' ? $student?->internshipPlace ?? null : null;

        $studentMapLatitude = $studentInternshipPlace?->latitude ?? ($studentInternshipPlace?->lat ?? null);

        $studentMapLongitude =
            $studentInternshipPlace?->longitude ??
            ($studentInternshipPlace?->long ?? ($studentInternshipPlace?->lng ?? null));

        $studentMapPlaceName = $studentInternshipPlace?->name ?? 'Lokasi DUDI';

        /*
        |--------------------------------------------------------------------------
        | Status presensi hari ini langsung dari data $presences
        |--------------------------------------------------------------------------
        | Tidak lagi bergantung pada $hasCheckedIn / $hasCheckedOut dari
        | controller karena variabel tersebut bisa tidak sinkron.
        */
        $todayPresence = collect($presences ?? [])->first(function ($presence) {
            if (empty($presence->check_in)) {
                return false;
            }

            return \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->isToday();
        });

        $hasCheckedInToday = !empty($todayPresence?->check_in);
        $hasCheckedOutToday = !empty($todayPresence?->check_out);
    @endphp

    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">dashboard</span>
                    </span>

                    Dashboard
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    {{ $dashboardSubtitle }}
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white
                border border-slate-100 shadow-lg shadow-slate-200/40 text-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-bold text-slate-600">
                    {{ ucfirst(str_replace('-', ' ', $dashboardRole)) }}
                </span>
            </div>
        </div>

        {{-- ===================== STUDENT ===================== --}}
        @if (auth()->user()->role === 'student')
            {{-- Quick Menu --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <a href="#presensi"
                    class="group relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-teal-500 to-teal-600 p-6 text-white shadow-xl shadow-teal-500/20 hover:shadow-2xl hover:shadow-teal-500/30 transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                            <span class="material-icons-round text-2xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium opacity-90">Presensi</p>
                            <p class="text-2xl font-bold">{{ $presencesCount }}</p>
                            <p class="text-xs opacity-75 mt-0.5">hari ini</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('presence') }}"
                    class="group relative overflow-hidden rounded-[2rem] bg-white border border-slate-100 p-6 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:border-teal-100 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-all">
                            <span class="material-icons-round text-2xl">history</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Riwayat</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $presenceCount }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">total presensi</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('report') }}"
                    class="group relative overflow-hidden rounded-[2rem] bg-white border border-slate-100 p-6 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:border-amber-100 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all">
                            <span class="material-icons-round text-2xl">cloud_upload</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Laporan</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $reportsCount }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">laporan dikirim</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Profile Card --}}
                <div class="lg:col-span-4">
                    <div
                        class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                        {{-- Google Maps GPS pada bagian atas kartu --}}
                        <div class="student-map-header">
                            <iframe id="student-card-map" title="Lokasi GPS siswa" loading="eager"
                                referrerpolicy="no-referrer-when-downgrade" allowfullscreen
                                data-latitude="{{ $studentMapLatitude }}" data-longitude="{{ $studentMapLongitude }}"
                                data-place-name="{{ $studentMapPlaceName }}">
                            </iframe>

                            <div id="student-map-loading" class="student-map-loading">
                                <span class="material-icons-round text-3xl mb-1.5">
                                    gps_fixed
                                </span>
                                <p class="text-[10px] font-bold">
                                    Mengambil lokasi GPS...
                                </p>
                            </div>

                            <div id="student-map-label" class="student-map-label hidden">
                                <span class="student-map-label-dot"></span>
                                <span id="student-map-label-text">
                                    Menunggu lokasi
                                </span>
                            </div>
                        </div>

                        <div class="student-profile-content px-6 pb-6 text-center">
                            @if ($student)
                                <div class="relative inline-block -mt-12">
                                    <img class="w-24 h-24 rounded-full border-4 border-white shadow-xl object-cover"
                                        src="{{ $student->user->foto_url ? asset('storage/' . $student->user->foto_url) : asset('assets/img/faces/face-0.jpg') }}"
                                        alt="Foto Profil">
                                    <div
                                        class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full">
                                    </div>
                                </div>

                                <h3 class="mt-3 text-xl font-bold text-slate-800">Hallo, {{ $student->name }}</h3>
                                <p class="text-sm text-teal-600 font-medium mt-1">Semangat Prakerin ya!</p>

                                <div class="mt-6 space-y-3 text-left">
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center">
                                            <span class="material-icons-round text-lg">business</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">DUDI
                                            </p>
                                            <p class="text-sm font-semibold text-slate-700 truncate">
                                                {{ $student->internshipPlace->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                            <span class="material-icons-round text-lg">meeting_room</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Kelas
                                            </p>
                                            <p class="text-sm font-semibold text-slate-700">
                                                {{ $student->class->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                                            <span class="material-icons-round text-lg">supervisor_account</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
                                                Pembimbing</p>
                                            <p class="text-sm font-semibold text-slate-700 truncate">
                                                {{ $student->mentor->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($student->internshipBatch->status_batch === 'active')
                                    @if ($hasCheckedOutToday)
                                        {{-- Semua tombol disembunyikan karena presensi selesai --}}
                                        <div class="mt-6">
                                            <div
                                                class="w-full py-3.5 px-4 rounded-2xl
                                                    bg-emerald-50 text-emerald-700
                                                    border border-emerald-200
                                                    text-sm font-black
                                                    flex items-center justify-center gap-2">

                                                <span class="material-icons-round text-xl">
                                                    task_alt
                                                </span>

                                                Presensi Selesai
                                            </div>

                                            <p class="mt-2 text-xs font-medium text-emerald-600">
                                                Presensi masuk dan pulang hari ini sudah tercatat.
                                            </p>
                                        </div>
                                    @elseif ($hasCheckedInToday)
                                        {{-- Tombol masuk disembunyikan, hanya tombol pulang --}}
                                        <div class="mt-6">
                                            <button type="button" id="checkOutPresence"
                                                onclick="window.handleCheckOutPresence(event)"
                                                class="relative z-50 pointer-events-auto
                                                    w-full py-3.5 px-4 rounded-2xl
                                                    bg-slate-800 text-white text-sm font-bold
                                                    hover:bg-slate-900 active:scale-95
                                                    transition-all
                                                    flex items-center justify-center gap-2
                                                    shadow-lg shadow-slate-800/25"
                                                data-checked-out="false">

                                                <span class="material-icons-round text-lg">
                                                    logout
                                                </span>

                                                Presensi Pulang
                                            </button>

                                            <p class="mt-2 text-xs font-medium text-slate-400">
                                                Presensi masuk sudah tercatat.
                                            </p>
                                        </div>
                                    @else
                                        {{-- Tombol pulang disembunyikan, hanya tombol masuk --}}
                                        <div class="mt-6">
                                            <button type="button" id="checkInPresence"
                                                onclick="window.handleCheckInPresence(event)"
                                                class="relative z-50 pointer-events-auto
                                                    w-full py-3.5 px-4 rounded-2xl
                                                    bg-teal-500 text-white text-sm font-bold
                                                    hover:bg-teal-600 active:scale-95
                                                    transition-all
                                                    flex items-center justify-center gap-2
                                                    shadow-lg shadow-teal-500/25"
                                                data-checked-in="false">

                                                <span class="material-icons-round text-lg">
                                                    login
                                                </span>

                                                Presensi Masuk
                                            </button>

                                            <p class="mt-2 text-xs font-medium text-slate-400">
                                                Lakukan presensi masuk terlebih dahulu.
                                            </p>
                                        </div>
                                    @endif
                                @elseif ($student->internshipBatch->status_batch === 'non-active')
                                    <div
                                        class="mt-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-sm text-rose-600 text-left">
                                        ⚠️ {{ $student->internshipBatch->batch_name }} sudah tidak aktif.
                                    </div>
                                @endif
                            @else
                                <div class="p-4 bg-rose-50 text-rose-600 rounded-2xl text-sm">
                                    Data siswa tidak ditemukan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Riwayat Hari Ini --}}
                <div class="lg:col-span-8">
                    <div
                        class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden h-full">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-bold text-slate-800">Riwayat Presensi Hari Ini</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Data real-time kehadiran</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50/80 text-slate-500 text-[11px] uppercase tracking-wider">
                                        <th class="px-5 py-3.5 text-left font-semibold">No</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Hari</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Tanggal</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Masuk</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Pulang</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Lokasi</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Status</th>
                                        <th class="px-5 py-3.5 text-left font-semibold">Ket</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($presences as $presence)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-5 py-4 text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="px-5 py-4 font-medium text-slate-700">
                                                {{ \Carbon\Carbon::parse($presence->check_in)->locale('id')->isoFormat('dddd') ?? '-' }}
                                            </td>
                                            <td class="px-5 py-4 text-slate-600">
                                                {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center gap-1.5 font-semibold text-teal-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                                    {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('H:i') : '-' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-slate-600">
                                                {{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->timezone('Asia/Jakarta')->format('H:i') : '-' }}
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex gap-2">
                                                    @if ($presence->check_in_location_link)
                                                        <iframe width="70" height="50"
                                                            class="rounded-lg border border-slate-200"
                                                            src="{{ $presence->check_in_location_link }}&output=embed"></iframe>
                                                    @endif
                                                    @if ($presence->check_out_location_link)
                                                        <iframe width="70" height="50"
                                                            class="rounded-lg border border-slate-200"
                                                            src="{{ $presence->check_out_location_link }}&output=embed"></iframe>
                                                    @endif
                                                    @if (!$presence->check_in_location_link && !$presence->check_out_location_link)
                                                        <span class="text-slate-300">—</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                @php
                                                    $statusLabel = match ($presence->status) {
                                                        'present' => 'Masuk',
                                                        'premission', 'permission' => 'Izin',
                                                        'sick' => 'Sakit',
                                                        'holiday' => 'Libur',
                                                        default => 'Alpa',
                                                    };
                                                    $statusColor = match ($presence->status) {
                                                        'present' => 'bg-teal-50 text-teal-700 ring-1 ring-teal-200',
                                                        'premission',
                                                        'permission'
                                                            => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
                                                        'sick' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                                                        'holiday'
                                                            => 'bg-purple-50 text-purple-700 ring-1 ring-purple-200',
                                                        default => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
                                                    };
                                                @endphp
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-slate-500 max-w-[120px] truncate">
                                                {{ $presence->note ?: '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-5 py-16 text-center">
                                                <div class="flex flex-col items-center gap-3">
                                                    <div
                                                        class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center">
                                                        <span
                                                            class="material-icons-round text-3xl text-slate-300">event_busy</span>
                                                    </div>
                                                    <p class="text-slate-400 font-medium">Belum ada presensi hari ini</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SUPER-ADMIN / ADMIN / MENTOR ===================== --}}
        @else
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
                @if ($dashboardRole === 'super-admin')
                    @php
                        $stats = [
                            [
                                'label' => 'Laporan',
                                'value' => $reportsCount,
                                'route' => route('report'),
                                'icon' => 'description',
                                'hover_border' => 'hover:border-teal-100',
                                'orb' => 'bg-teal-50',
                                'icon_class' =>
                                    'bg-teal-50 text-teal-600 group-hover:bg-teal-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Siswa',
                                'value' => $studentCount,
                                'route' => route('student'),
                                'icon' => 'face',
                                'hover_border' => 'hover:border-blue-100',
                                'orb' => 'bg-blue-50',
                                'icon_class' =>
                                    'bg-blue-50 text-blue-600 group-hover:bg-blue-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Pembimbing',
                                'value' => $mentorCount,
                                'route' => route('mentor'),
                                'icon' => 'supervisor_account',
                                'hover_border' => 'hover:border-purple-100',
                                'orb' => 'bg-purple-50',
                                'icon_class' =>
                                    'bg-purple-50 text-purple-600 group-hover:bg-purple-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'DUDI',
                                'value' => $dudiCount,
                                'route' => route('dudi'),
                                'icon' => 'business',
                                'hover_border' => 'hover:border-amber-100',
                                'orb' => 'bg-amber-50',
                                'icon_class' =>
                                    'bg-amber-50 text-amber-600 group-hover:bg-amber-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Gelombang',
                                'value' => $batchCount,
                                'route' => route('batch'),
                                'icon' => 'waves',
                                'hover_border' => 'hover:border-rose-100',
                                'orb' => 'bg-rose-50',
                                'icon_class' =>
                                    'bg-rose-50 text-rose-600 group-hover:bg-rose-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Admin',
                                'value' => $adminCount,
                                'route' => route('admin'),
                                'icon' => 'admin_panel_settings',
                                'hover_border' => 'hover:border-slate-200',
                                'orb' => 'bg-slate-100',
                                'icon_class' =>
                                    'bg-slate-100 text-slate-600 group-hover:bg-slate-700 group-hover:text-white',
                            ],
                        ];
                    @endphp
                @elseif ($dashboardRole === 'admin')
                    @php
                        $stats = [
                            [
                                'label' => 'Laporan',
                                'value' => $reportsCount,
                                'route' => route('report'),
                                'icon' => 'description',
                                'hover_border' => 'hover:border-teal-100',
                                'orb' => 'bg-teal-50',
                                'icon_class' =>
                                    'bg-teal-50 text-teal-600 group-hover:bg-teal-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Siswa',
                                'value' => $studentCount,
                                'route' => route('student'),
                                'icon' => 'face',
                                'hover_border' => 'hover:border-blue-100',
                                'orb' => 'bg-blue-50',
                                'icon_class' =>
                                    'bg-blue-50 text-blue-600 group-hover:bg-blue-500 group-hover:text-white',
                            ],
                        ];
                    @endphp
                @else
                    @php
                        $stats = [
                            [
                                'label' => 'Presensi Hari Ini',
                                'value' => $presencesCount,
                                'route' => route('presence'),
                                'icon' => 'today',
                                'hover_border' => 'hover:border-teal-100',
                                'orb' => 'bg-teal-50',
                                'icon_class' =>
                                    'bg-teal-50 text-teal-600 group-hover:bg-teal-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Riwayat',
                                'value' => $presenceCount,
                                'route' => route('history.presence'),
                                'icon' => 'history',
                                'hover_border' => 'hover:border-blue-100',
                                'orb' => 'bg-blue-50',
                                'icon_class' =>
                                    'bg-blue-50 text-blue-600 group-hover:bg-blue-500 group-hover:text-white',
                            ],
                            [
                                'label' => 'Siswa',
                                'value' => $studentsCount,
                                'route' => route('student'),
                                'icon' => 'face',
                                'hover_border' => 'hover:border-purple-100',
                                'orb' => 'bg-purple-50',
                                'icon_class' =>
                                    'bg-purple-50 text-purple-600 group-hover:bg-purple-500 group-hover:text-white',
                            ],
                        ];
                    @endphp
                @endif

                @foreach ($stats as $stat)
                    <a href="{{ $stat['route'] }}"
                        class="group relative bg-white rounded-[2rem] border border-slate-100 p-5
                            shadow-xl shadow-slate-200/40 hover:shadow-2xl
                            {{ $stat['hover_border'] }} transition-all duration-300 overflow-hidden">

                        <div
                            class="absolute -right-6 -top-6 w-20 h-20 {{ $stat['orb'] }}
                                rounded-full opacity-60 group-hover:scale-150
                                transition-transform duration-500">
                        </div>

                        <div class="relative">
                            <div
                                class="w-11 h-11 rounded-2xl {{ $stat['icon_class'] }}
                                    flex items-center justify-center mb-4 transition-all">

                                <span class="material-icons-round">
                                    {{ $stat['icon'] }}
                                </span>
                            </div>

                            <p class="text-2xl font-black text-slate-800 tracking-tight">
                                {{ $stat['value'] }}
                            </p>

                            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">
                                {{ $stat['label'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Mentor Card --}}
            @if (auth()->user()->role === 'mentor')
                <div
                    class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-teal-500 via-teal-600 to-emerald-600 p-1 shadow-2xl shadow-teal-500/20">
                    <div class="bg-white/10 backdrop-blur-sm rounded-[22px] p-6 md:p-8 text-white">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="relative">
                                <img class="w-20 h-20 rounded-full border-4 border-white/30 object-cover shadow-lg"
                                    src="{{ asset('assets/img/faces/face-0.jpg') }}" alt="Avatar">
                                <div
                                    class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-400 border-2 border-white rounded-full flex items-center justify-center">
                                    <span class="material-icons-round text-xs text-white">check</span>
                                </div>
                            </div>
                            <div class="flex-1 text-center md:text-left">
                                <h3 class="text-xl font-bold">Hallo, {{ $mentor->name }}</h3>
                                <p class="text-teal-100 text-sm mt-1">Pembimbing Prakerin</p>

                                @if ($mentor->telegram_number)
                                    <div
                                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/15 rounded-full text-sm">
                                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                        Chat ID Telegram aktif
                                    </div>
                                @else
                                    <div class="mt-4 p-3 bg-white/10 rounded-2xl text-sm">
                                        ❗ Chat ID belum terdaftar.
                                        <a href="https://t.me/PrakerinTracerBot" target="_blank"
                                            class="underline font-semibold">Daftarkan sekarang</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Chart + Table Section --}}
            @if (in_array(auth()->user()->role, ['super-admin', 'admin', 'mentor']))
                <div
                    class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div
                        class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h4 class="text-lg font-black text-slate-800 tracking-tight">Grafik & Rekap Presensi</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Filter dan analisis kehadiran siswa</p>
                        </div>
                        <div id="printButton" class="hidden">
                            <a id="printButtonLink" href="#"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-2xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                                <span class="material-icons-round text-base">download</span>
                                Cetak Rekap
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-8">
                        {{-- Chart --}}
                        <div
                            class="w-full h-80 rounded-2xl bg-gradient-to-b from-slate-50 to-white p-4 border border-slate-100">
                            <canvas id="attendanceChart"></canvas>
                        </div>

                        {{-- Filters (Searchable) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Siswa</label>
                                <select id="studentFilter" class="select2-search w-full">
                                    <option value="">Semua Siswa</option>
                                </select>
                            </div>
                            @if (auth()->user()->role !== 'admin')
                                <div class="space-y-1.5">
                                    <label
                                        class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label>
                                    <select id="classFilter" class="select2-search w-full">
                                        <option value="">Semua Kelas</option>
                                    </select>
                                </div>
                            @endif
                            <div class="space-y-1.5">
                                <label
                                    class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gelombang</label>
                                <select id="batchFilter" class="select2-search w-full">
                                    <option value="">Semua Gelombang</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bulan
                                    Mulai</label>
                                <input type="month" id="startMonthFilter"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-4 py-2.5">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bulan
                                    Akhir</label>
                                <input type="month" id="endMonthFilter"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-4 py-2.5">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal
                                    Mulai</label>
                                <input type="date" id="startDateFilter"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-4 py-2.5">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal
                                    Akhir</label>
                                <input type="date" id="endDateFilter"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-4 py-2.5">
                            </div>
                        </div>

                        {{-- Periode Info --}}
                        <div class="flex items-center gap-3 text-sm">
                            <span id="batchName" class="font-bold text-slate-800"></span>
                            <span class="text-slate-300">•</span>
                            <span id="yearResult" class="text-slate-500"></span>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table id="filteredTable" class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                                        <th class="px-4 py-3.5 text-left font-semibold" rowspan="2">No</th>
                                        <th class="px-4 py-3.5 text-left font-semibold" rowspan="2">Nama</th>
                                        <th class="px-4 py-3.5 text-left font-semibold" rowspan="2">Kelas</th>
                                        <th class="px-4 py-3.5 text-left font-semibold" rowspan="2">DUDI</th>
                                        <th class="px-4 py-3.5 text-left font-semibold" rowspan="2">Pembimbing</th>
                                        <th class="px-4 py-3.5 text-center font-semibold bg-amber-50 text-amber-700"
                                            rowspan="2">Hari Efektif</th>
                                        <th class="px-4 py-3.5 text-center font-semibold bg-teal-50 text-teal-700"
                                            rowspan="2">Masuk</th>
                                        <th class="px-4 py-3.5 text-center font-semibold bg-rose-50 text-rose-700"
                                            colspan="5">Tidak Masuk</th>
                                        <th class="px-4 py-3.5 text-left font-semibold" rowspan="2">Keterangan</th>
                                        <th class="px-4 py-3.5 text-center font-semibold" rowspan="2">Aksi</th>
                                    </tr>
                                    <tr class="bg-slate-50 text-[11px] uppercase tracking-wider">
                                        <th class="px-2 py-2.5 text-center font-semibold bg-amber-50/70 text-amber-600">S
                                        </th>
                                        <th class="px-2 py-2.5 text-center font-semibold bg-blue-50/70 text-blue-600">I
                                        </th>
                                        <th class="px-2 py-2.5 text-center font-semibold bg-rose-50/70 text-rose-600">A
                                        </th>
                                        <th class="px-2 py-2.5 text-center font-semibold bg-purple-50/70 text-purple-600">L
                                        </th>
                                        <th class="px-2 py-2.5 text-center font-semibold bg-slate-100 text-slate-500">Lain
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    {{-- filled by JS --}}
                                </tbody>
                            </table>
                        </div>
                        <div id="paginationControls" class="flex justify-center gap-1.5 pt-2"></div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- ============================================================
         JAVASCRIPT langsung di Blade agar pasti ikut dirender
         Tidak memuat public/assets/js/index.js lagi.
         ============================================================ --}}
    @once
        {{-- Select2 --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            /**
             * Menampilkan Google Maps berdasarkan GPS perangkat.
             * Koordinat DUDI digunakan sebagai fallback.
             */
            function initializeStudentMap() {
                const mapFrame =
                    document.getElementById('student-card-map');

                const loadingElement =
                    document.getElementById('student-map-loading');

                const labelElement =
                    document.getElementById('student-map-label');

                const labelTextElement =
                    document.getElementById('student-map-label-text');

                if (!mapFrame) {
                    return;
                }

                const fallbackLatitude =
                    Number.parseFloat(mapFrame.dataset.latitude);

                const fallbackLongitude =
                    Number.parseFloat(mapFrame.dataset.longitude);

                const placeName =
                    mapFrame.dataset.placeName || 'Lokasi DUDI';

                const hasFallback =
                    Number.isFinite(fallbackLatitude) &&
                    Number.isFinite(fallbackLongitude) &&
                    fallbackLatitude >= -90 &&
                    fallbackLatitude <= 90 &&
                    fallbackLongitude >= -180 &&
                    fallbackLongitude <= 180;

                const hideLoading = function() {
                    if (!loadingElement) {
                        return;
                    }

                    loadingElement.classList.add('is-hidden');

                    window.setTimeout(function() {
                        loadingElement.classList.add('hidden');
                    }, 230);
                };

                const showLabel = function(
                    message,
                    isFallback = false
                ) {
                    if (labelTextElement) {
                        labelTextElement.textContent = message;
                    }

                    if (labelElement) {
                        labelElement.classList.remove('hidden');
                        labelElement.classList.toggle(
                            'is-fallback',
                            isFallback
                        );
                    }
                };

                const setMapLocation = function(
                    latitude,
                    longitude,
                    label,
                    isFallback = false
                ) {
                    const safeLatitude = Number(latitude);
                    const safeLongitude = Number(longitude);

                    if (
                        !Number.isFinite(safeLatitude) ||
                        !Number.isFinite(safeLongitude)
                    ) {
                        return;
                    }

                    const query =
                        `${safeLatitude},${safeLongitude}`;

                    mapFrame.src =
                        'https://maps.google.com/maps' +
                        `?q=${encodeURIComponent(query)}` +
                        '&z=17&output=embed';

                    showLabel(label, isFallback);

                    mapFrame.addEventListener(
                        'load',
                        hideLoading, {
                            once: true
                        }
                    );

                    window.setTimeout(hideLoading, 2500);
                };

                /*
                 * Tampilkan koordinat DUDI dahulu agar area map tidak kosong
                 * saat browser masih meminta izin GPS.
                 */
                if (hasFallback) {
                    setMapLocation(
                        fallbackLatitude,
                        fallbackLongitude,
                        placeName,
                        true
                    );
                }

                if (!navigator.geolocation) {
                    if (!hasFallback) {
                        hideLoading();
                        showLabel(
                            'GPS tidak didukung browser',
                            true
                        );
                    }

                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude =
                            position.coords.latitude;

                        const longitude =
                            position.coords.longitude;

                        const accuracy = Math.round(
                            position.coords.accuracy || 0
                        );

                        setMapLocation(
                            latitude,
                            longitude,
                            `Lokasi GPS · ±${accuracy} m`
                        );
                    },
                    function(error) {
                        let message =
                            'Lokasi DUDI · GPS belum tersedia';

                        if (error.code === 1) {
                            message =
                                'Lokasi DUDI · izin GPS ditolak';
                        } else if (error.code === 2) {
                            message =
                                'Lokasi DUDI · sinyal GPS belum ditemukan';
                        } else if (error.code === 3) {
                            message =
                                'Lokasi DUDI · GPS belum merespons';
                        }

                        if (hasFallback) {
                            showLabel(message, true);
                            hideLoading();
                        } else {
                            hideLoading();
                            showLabel(
                                'Aktifkan izin lokasi browser',
                                true
                            );
                        }
                    }, {
                        enableHighAccuracy: true,
                        timeout: 20000,
                        maximumAge: 0
                    }
                );
            }

            /**
             * Mengamankan teks HTML.
             */
            function escapeHtml(value) {
                const element = document.createElement('div');
                element.textContent = value ?? '';
                return element.innerHTML;
            }

            var attendanceChartInstance = null;
            var allPresenceTable = [];
            let currentPage = 1;
            const rowsPerPage = 5;

            // Init Select2
            function initSelect2() {
                $('.select2-search').select2({
                    placeholder: "Cari...",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Tidak ditemukan";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    }
                });
            }

            function renderChart(labels, presentData, alphaData, izinData, sakitData, liburData) {
                var ctx = document.getElementById('attendanceChart');
                if (!ctx) return;
                ctx = ctx.getContext('2d');

                if (attendanceChartInstance) attendanceChartInstance.destroy();

                // Gradient helper
                const createGradient = (color1, color2) => {
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, color1);
                    gradient.addColorStop(1, color2);
                    return gradient;
                };

                attendanceChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Masuk',
                                data: presentData,
                                backgroundColor: createGradient('rgba(13, 148, 136, 0.95)',
                                    'rgba(13, 148, 136, 0.55)'),
                                borderRadius: 10,
                                borderSkipped: false,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Alpha',
                                data: alphaData,
                                backgroundColor: createGradient('rgba(244, 63, 94, 0.9)', 'rgba(244, 63, 94, 0.5)'),
                                borderRadius: 10,
                                borderSkipped: false,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Izin',
                                data: izinData,
                                backgroundColor: createGradient('rgba(14, 165, 233, 0.9)',
                                    'rgba(14, 165, 233, 0.5)'),
                                borderRadius: 10,
                                borderSkipped: false,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Sakit',
                                data: sakitData,
                                backgroundColor: createGradient('rgba(245, 158, 11, 0.9)',
                                    'rgba(245, 158, 11, 0.5)'),
                                borderRadius: 10,
                                borderSkipped: false,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Libur',
                                data: liburData,
                                backgroundColor: createGradient('rgba(139, 92, 246, 0.9)',
                                    'rgba(139, 92, 246, 0.5)'),
                                borderRadius: 10,
                                borderSkipped: false,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20,
                                    font: {
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    },
                                    color: '#64748b'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: {
                                    size: 13,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 12,
                                cornerRadius: 12,
                                displayColors: true,
                                boxPadding: 6
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.15)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#94a3b8',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 900,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            function fetchAttendanceData() {
                currentPage = 1;

                const tableBody = document.querySelector('#filteredTable tbody');
                if (tableBody) {
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="14" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <span class="w-8 h-8 border-2 border-slate-200 border-t-teal-500 rounded-full animate-spin"></span>
                                <span class="text-sm font-medium">Memuat data presensi...</span>
                            </div>
                        </td>
                    </tr>
                `;
                }
                var studentName = $('#studentFilter').val() || '';
                var classCode = $('#classFilter').val() || '';
                var batchName = $('#batchFilter').val() || '';
                var startMonth = $('#startMonthFilter').val() || '';
                var endMonth = $('#endMonthFilter').val() || '';
                var startDate = $('#startDateFilter').val() || '';
                var endDate = $('#endDateFilter').val() || '';

                $.ajax({
                    url: "{{ route('attendance.data') }}",
                    method: 'GET',
                    data: {
                        student_name: studentName,
                        class_code: classCode,
                        batch_name: batchName,
                        start_month: startMonth,
                        end_month: endMonth,
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function(response) {
                        var chartData = response.attendanceData || [];
                        var classes = response.classes || [];
                        var students = response.students || [];
                        var batches = response.batches || [];
                        var yearResult = response.yearResult || '';
                        var batchNameIdentity = response.batchNameIdentity || '';
                        allPresenceTable = response.presenceTable || [];

                        var labels = chartData.map(item => item.label);
                        var presentData = chartData.map(item => item.data[0] || 0);
                        var alphaData = chartData.map(item => item.data[1] || 0);
                        var izinData = chartData.map(item => item.data[2] || 0);
                        var sakitData = chartData.map(item => item.data[3] || 0);
                        var liburData = chartData.map(item => item.data[4] || 0);

                        renderChart(labels, presentData, alphaData, izinData, sakitData, liburData);
                        populateFilterOptions(students, studentName);
                        populateBatchFilter(batches, batchName);
                        populateClassFilter(classes, classCode);

                        // Re-init Select2 after options updated
                        $('.select2-search').select2('destroy');
                        initSelect2();

                        if (response.rekapTable) {
                            $('#batchName').text(batchNameIdentity);
                            $('#yearResult').text("Bulan : " + yearResult);
                            populateRekapTable(response.rekapTable);
                        }

                        if (response.rekapTable && response.rekapTable.length > 0) {
                            $('#printButton').removeClass('hidden');
                            var printUrl = "{{ route('print.presence') }}" +
                                '?student=' + encodeURIComponent(studentName || '') +
                                '&class_code=' + encodeURIComponent(classCode || '') +
                                '&batch=' + encodeURIComponent(batchName || '') +
                                '&start_month=' + encodeURIComponent(startMonth || '') +
                                '&end_month=' + encodeURIComponent(endMonth || '') +
                                '&start_date=' + encodeURIComponent(startDate || '') +
                                '&end_date=' + encodeURIComponent(endDate || '');
                            $('#printButtonLink').attr('href', printUrl);
                        } else {
                            $('#printButton').addClass('hidden');
                        }
                    },
                    error: function(err) {
                        console.error("Error fetching data", err);

                        const tableBody = document.querySelector('#filteredTable tbody');
                        if (tableBody) {
                            tableBody.innerHTML = `
                            <tr>
                                <td colspan="14" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-icons-round text-4xl text-rose-300">cloud_off</span>
                                        <p class="text-sm font-bold text-slate-600">Data gagal dimuat</p>
                                        <p class="text-xs text-slate-400">Periksa koneksi atau muat ulang halaman.</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        }
                    }
                });
            }

            function populateFilterOptions(students, selectedName) {
                var el = $('#studentFilter');
                if (!el.length) return;
                el.empty().append('<option value="">Semua Siswa</option>');
                students.forEach(s => {
                    el.append(
                        `<option value="${s.name}" ${s.name === selectedName ? 'selected' : ''}>${s.name} | ${s.class_code}</option>`
                    );
                });
            }

            function populateBatchFilter(batches, selectedBatch) {
                var el = $('#batchFilter');
                if (!el.length) return;
                el.empty().append('<option value="">Semua Gelombang</option>');
                batches.forEach(b => {
                    el.append(
                        `<option value="${b.id}" ${b.id == selectedBatch ? 'selected' : ''}>${b.batch_name} | TP.${b.academic_year}</option>`
                    );
                });
            }

            function populateClassFilter(classes, selectedClass) {
                var el = $('#classFilter');
                if (!el.length) return;
                el.empty().append('<option value="">Semua Kelas</option>');
                classes.forEach(c => {
                    el.append(
                        `<option value="${c.code}" ${c.code === selectedClass ? 'selected' : ''}>${c.name}</option>`
                    );
                });
            }

            function paginateData(data) {
                const start = (currentPage - 1) * rowsPerPage;
                return data.slice(start, start + rowsPerPage);
            }

            function renderPagination(data) {
                const totalPages = Math.ceil(data.length / rowsPerPage);
                const container = document.getElementById('paginationControls');
                if (!container) return;
                container.innerHTML = '';

                const makeBtn = (label, disabled, onClick, active = false) => {
                    const btn = document.createElement('button');
                    btn.innerHTML = label;
                    btn.disabled = disabled;
                    btn.className = `min-w-[36px] h-9 px-3 rounded-xl text-sm font-medium transition-all ${
                active 
                    ? 'bg-teal-500 text-white shadow-md shadow-teal-500/30' 
                    : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-40'
            }`;
                    btn.addEventListener('click', onClick);
                    return btn;
                };

                container.appendChild(makeBtn('‹', currentPage === 1, () => {
                    if (currentPage > 1) {
                        currentPage--;
                        populateRekapTable(data);
                    }
                }));

                for (let i = 1; i <= totalPages; i++) {
                    container.appendChild(makeBtn(i, false, () => {
                        currentPage = i;
                        populateRekapTable(data);
                    }, i === currentPage));
                }

                container.appendChild(makeBtn('›', currentPage === totalPages || totalPages === 0, () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        populateRekapTable(data);
                    }
                }));
            }

            function populateRekapTable(data) {
                const tbody = document.querySelector("#filteredTable tbody");
                if (!tbody) return;
                tbody.innerHTML = "";

                if (!data || data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="14" class="px-5 py-16 text-center text-slate-400">Tidak ada data</td></tr>`;
                    return;
                }

                paginateData(data).forEach((item, index) => {
                    const tr = document.createElement("tr");
                    tr.className = "hover:bg-slate-50/70 transition-colors";
                    tr.innerHTML = `
                <td class="px-4 py-3.5 text-slate-400">${(currentPage - 1) * rowsPerPage + index + 1}</td>
                <td class="px-4 py-3.5 font-semibold text-slate-800">${item.nama || '-'}</td>
                <td class="px-4 py-3.5 text-slate-600">${item.kelas || '-'}</td>
                <td class="px-4 py-3.5 text-slate-600">${item.dudi || '-'}</td>
                <td class="px-4 py-3.5 text-slate-600">${item.pembimbing || '-'}</td>
                <td class="px-4 py-3.5 text-center font-bold text-amber-600">${item.hari_efektif || 0}</td>
                <td class="px-4 py-3.5 text-center font-bold text-teal-600">${item.masuk || 0}</td>
                <td class="px-2 py-3.5 text-center text-slate-600">${item.sakit || 0}</td>
                <td class="px-2 py-3.5 text-center text-slate-600">${item.izin || 0}</td>
                <td class="px-2 py-3.5 text-center text-slate-600">${item.alpa || 0}</td>
                <td class="px-2 py-3.5 text-center text-slate-600">${item.libur || 0}</td>
                <td class="px-2 py-3.5 text-center text-slate-600">${item.lainnya || 0}</td>
                <td class="px-4 py-3.5 text-slate-500 text-xs">${item.keterangan || '—'}</td>
                <td class="px-4 py-3.5 text-center">
                    <button class="view-detail-btn w-9 h-9 rounded-xl bg-teal-50 text-teal-600 hover:bg-teal-500 hover:text-white transition-all inline-flex items-center justify-center"
                        data-nama="${item.nama || ''}">
                        <span class="material-icons-round text-lg">visibility</span>
                    </button>
                </td>
            `;
                    tbody.appendChild(tr);
                });

                renderPagination(data);
            }

            // Detail handler
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.view-detail-btn');
                if (!btn) return;

                const nama = btn.dataset.nama;
                const filtered = allPresenceTable.filter(item => item.siswa === nama);

                if (!filtered.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data tidak ditemukan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }

                let html = `
            <div class="max-h-[420px] overflow-y-auto text-left">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600">
                            <th class="border p-2">No</th>
                            <th class="border p-2">Siswa</th>
                            <th class="border p-2">Kelas</th>
                            <th class="border p-2">DUDI</th>
                            <th class="border p-2">Gelombang</th>
                            <th class="border p-2">TP</th>
                            <th class="border p-2">Hari</th>
                            <th class="border p-2">Tanggal</th>
                            <th class="border p-2">Masuk</th>
                            <th class="border p-2">Pulang</th>
                            <th class="border p-2">Lokasi</th>
                            <th class="border p-2">Status</th>
                            <th class="border p-2">Catatan</th>
                            <th class="border p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                filtered.forEach((item, i) => {
                    html += `
                <tr>
                    <td class="border p-2 text-center">${i + 1}</td>
                    <td class="border p-2">${item.siswa || '-'}</td>
                    <td class="border p-2">${item.kelas || '-'}</td>
                    <td class="border p-2">${item.dudi || '-'}</td>
                    <td class="border p-2">${item.gelombang || '-'}</td>
                    <td class="border p-2">${item.tahun_pelajaran || '-'}</td>
                    <td class="border p-2">${item.hari || '-'}</td>
                    <td class="border p-2">${item.tanggal || '-'}</td>
                    <td class="border p-2">${item.masuk || '-'}</td>
                    <td class="border p-2">${item.pulang || '-'}</td>
                    <td class="border p-2">
                        <div class="flex gap-1">
                            ${item.lokasi_masuk ? `<iframe width="90" height="60" class="rounded border" src="${item.lokasi_masuk}&output=embed"></iframe>` : ''}
                            ${item.lokasi_pulang ? `<iframe width="90" height="60" class="rounded border" src="${item.lokasi_pulang}&output=embed"></iframe>` : ''}
                        </div>
                    </td>
                    <td class="border p-2">${item.status || '-'}</td>
                    <td class="border p-2">${item.note || '-'}</td>
                    <td class="border p-2 text-center">
                        <button class="btn-hapus px-2.5 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-xs" data-id="${item.id}">Hapus</button>
                    </td>
                </tr>
            `;
                });

                html += `</tbody></table></div>`;

                Swal.fire({
                    title: `Presensi — ${nama}`,
                    html: html,
                    width: '95%',
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-3xl'
                    },
                    didOpen: async () => {
                        const video = document.getElementById('swal-camera');
                        const canvas = document.getElementById('swal-canvas');
                        const btnCapture = document.getElementById('btn-capture');
                        const btnRetake = document.getElementById('btn-retake');
                        const statusEl = document.getElementById('capture-status');
                        const faceGuide = document.getElementById('face-guide');
                        const confirmBtn = Swal.getConfirmButton();

                        confirmBtn.disabled = true;
                        confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');

                        // ===== Overlay watermark live (langsung terlihat di kamera) =====
                        const overlay = document.createElement('div');
                        overlay.id = 'live-watermark';
                        overlay.style.cssText = `
        position: absolute;
        left: 12px;
        bottom: 12px;
        z-index: 30;
        background: rgba(15, 23, 42, 0.75);
        color: #fff;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-family: Inter, Arial, sans-serif;
        line-height: 1.4;
        pointer-events: none;
        backdrop-filter: blur(4px);
    `;
                        overlay.innerHTML = `
        <div style="font-weight:700;">Lat: ${currentLat.toFixed(6)} | Lng: ${currentLng.toFixed(6)}</div>
        <div style="color:#94a3b8; font-size:10px;" id="wm-time"></div>
    `;

                        const cameraWrapper = video.parentElement;
                        cameraWrapper.style.position = 'relative';
                        cameraWrapper.appendChild(overlay);

                        // Update jam setiap detik
                        const timeEl = overlay.querySelector('#wm-time');
                        const updateTime = () => {
                            const now = new Date();
                            timeEl.textContent = now.toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                        };
                        updateTime();
                        const timeInterval = setInterval(updateTime, 1000);

                        // Simpan interval supaya bisa di-clear
                        video.dataset.timeInterval = timeInterval;

                        try {
                            stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: 'user',
                                    width: {
                                        ideal: 720
                                    },
                                    height: {
                                        ideal: 960
                                    }
                                },
                                audio: false
                            });
                            video.srcObject = stream;
                        } catch (err) {
                            statusEl.textContent =
                                'Tidak bisa mengakses kamera. Izinkan akses kamera di browser.';
                            statusEl.classList.add('text-rose-500');
                            return;
                        }

                        // ===== Tombol Ambil Foto =====
                        btnCapture.addEventListener('click', () => {
                            const w = video.videoWidth || 720;
                            const h = video.videoHeight || 960;
                            canvas.width = w;
                            canvas.height = h;

                            const ctx = canvas.getContext('2d');

                            // 1. Gambar video
                            ctx.drawImage(video, 0, 0, w, h);

                            // 2. Gambar watermark ke foto (supaya ikut tersimpan)
                            const now = new Date();
                            const timeStr = now.toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            const line1 =
                                `Lat: ${currentLat.toFixed(6)}  |  Lng: ${currentLng.toFixed(6)}`;
                            const line2 = timeStr;

                            const padding = Math.max(12, Math.floor(w * 0.02));
                            const fontSize = Math.max(16, Math.floor(w * 0.032));

                            ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                            const textW = Math.max(ctx.measureText(line1).width, ctx.measureText(
                                line2).width);
                            const boxW = textW + padding * 2;
                            const boxH = fontSize * 2.5 + padding;
                            const boxX = padding;
                            const boxY = h - boxH - padding;

                            ctx.fillStyle = 'rgba(15, 23, 42, 0.75)';
                            ctx.fillRect(boxX, boxY, boxW, boxH);

                            ctx.fillStyle = '#ffffff';
                            ctx.textBaseline = 'top';
                            ctx.fillText(line1, boxX + padding, boxY + padding * 0.7);

                            ctx.font = `${Math.floor(fontSize * 0.9)}px Arial, sans-serif`;
                            ctx.fillStyle = '#94a3b8';
                            ctx.fillText(line2, boxX + padding, boxY + padding * 0.7 + fontSize *
                                1.3);

                            // 3. Jadiin file
                            canvas.toBlob((blob) => {
                                if (!blob) {
                                    statusEl.textContent = 'Gagal mengambil foto.';
                                    return;
                                }

                                capturedBlob = blob;
                                const url = URL.createObjectURL(blob);

                                // Stop kamera
                                if (stream) {
                                    stream.getTracks().forEach(t => t.stop());
                                    stream = null;
                                }
                                clearInterval(Number(video.dataset.timeInterval));

                                video.srcObject = null;
                                video.poster = url;
                                video.pause();

                                // Sembunyikan guide & overlay live (sudah ada di foto)
                                if (faceGuide) faceGuide.style.display = 'none';
                                overlay.style.display = 'none';

                                btnCapture.classList.add('hidden');
                                btnRetake.classList.remove('hidden');
                                statusEl.textContent =
                                    'Foto berhasil diambil (dengan koordinat). Klik "Kirim Presensi".';
                                statusEl.classList.remove('text-slate-400');
                                statusEl.classList.add('text-teal-600', 'font-semibold');

                                confirmBtn.disabled = false;
                                confirmBtn.classList.remove('opacity-50',
                                    'cursor-not-allowed');
                            }, 'image/jpeg', 0.9);
                        });

                        // ===== Tombol Ulangi =====
                        btnRetake.addEventListener('click', async () => {
                            capturedBlob = null;
                            if (faceGuide) faceGuide.style.display = 'block';
                            overlay.style.display = 'block';
                            btnCapture.classList.remove('hidden');
                            btnRetake.classList.add('hidden');
                            statusEl.textContent =
                                'Pastikan wajah terlihat jelas di dalam kotak';
                            statusEl.classList.remove('text-teal-600', 'font-semibold');
                            statusEl.classList.add('text-slate-400');

                            confirmBtn.disabled = true;
                            confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');

                            try {
                                stream = await navigator.mediaDevices.getUserMedia({
                                    video: {
                                        facingMode: 'user'
                                    },
                                    audio: false
                                });
                                video.srcObject = stream;
                                video.poster = '';
                                await video.play();

                                updateTime();
                                const newInterval = setInterval(updateTime, 1000);
                                video.dataset.timeInterval = newInterval;
                            } catch (err) {
                                statusEl.textContent = 'Gagal mengaktifkan kamera kembali.';
                            }
                        });
                    },
                    willClose: () => {
                        if (stream) {
                            stream.getTracks().forEach(t => t.stop());
                            stream = null;
                        }
                        const video = document.getElementById('swal-camera');
                        if (video?.dataset?.timeInterval) {
                            clearInterval(Number(video.dataset.timeInterval));
                        }
                    },
                });
            });

            $(document).ready(function() {
                initSelect2();
                initializeStudentMap();

                if (document.getElementById('attendanceChart')) {
                    fetchAttendanceData();

                    // Listen change on Select2
                    $('#studentFilter, #batchFilter, #classFilter').on('change', function() {
                        fetchAttendanceData();
                    });

                    $('#startMonthFilter, #endMonthFilter, #startDateFilter, #endDateFilter').on('change', function() {
                        fetchAttendanceData();
                    });
                }
            });
        </script>

        {{-- ============================================================
         PRESENSI SISWA LANGSUNG DARI BLADE
         Tombol memakai onclick langsung, tidak menunggu DOMContentLoaded.
         ============================================================ --}}
        @if ($dashboardRole === 'student')
            <script>
                window.checkInUrl = @json(route('presence.checkIn'));
                window.checkOutUrl = @json(route('presence.checkOut'));
                window.presenceCsrfToken = @json(csrf_token());
                window.hasCheckedInToday = @json($hasCheckedInToday);
                window.hasCheckedOutToday = @json($hasCheckedOutToday);

                /**
                 * Menampilkan SweetAlert atau alert biasa.
                 */
                window.showPresenceMessage = async function(options) {
                    if (typeof window.Swal !== 'undefined') {
                        return window.Swal.fire(options);
                    }
                    alert(options.text || options.title || 'Terjadi kesalahan.');
                };

                /**
                 * Mengambil lokasi perangkat dengan akurasi tinggi.
                 */
                window.getPresenceLocation = function() {
                    return new Promise(function(resolve, reject) {
                        if (!navigator.geolocation) {
                            reject(new Error('Browser tidak mendukung pengambilan lokasi.'));
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                resolve({
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    accuracy: position.coords.accuracy
                                });
                            },
                            function(error) {
                                let message = 'Lokasi tidak dapat diakses. Pastikan GPS dan izin lokasi aktif.';

                                if (error.code === 1) {
                                    message = 'Izin lokasi ditolak. Aktifkan izin lokasi pada browser.';
                                } else if (error.code === 2) {
                                    message = 'Lokasi perangkat tidak dapat ditemukan.';
                                } else if (error.code === 3) {
                                    message = 'Pengambilan lokasi terlalu lama. Silakan coba lagi.';
                                }

                                reject(new Error(message));
                            }, {
                                enableHighAccuracy: true,
                                timeout: 20000,
                                maximumAge: 0
                            }
                        );
                    });
                };

                /**
                 * Membaca respons JSON atau teks.
                 */
                window.parsePresenceResponse = async function(response) {
                    const contentType = response.headers.get('content-type') || '';

                    if (contentType.includes('application/json')) {
                        return await response.json();
                    }

                    const text = await response.text();
                    return {
                        message: text || 'Server tidak memberikan respons.'
                    };
                };

                /**
                 * Mengirim data presensi (bisa dengan foto bukti).
                 */
                window.sendPresenceRequest = async function(config) {
                    const button = config.button;

                    if (!button) {
                        alert('Tombol presensi tidak ditemukan.');
                        return;
                    }

                    if (!config.url) {
                        await window.showPresenceMessage({
                            icon: 'error',
                            title: 'Route Tidak Ditemukan',
                            text: 'URL presensi belum tersedia.'
                        });
                        return;
                    }

                    if (!navigator.onLine) {
                        await window.showPresenceMessage({
                            icon: 'error',
                            title: 'Tidak Ada Koneksi',
                            text: 'Periksa koneksi internet terlebih dahulu.'
                        });
                        return;
                    }

                    const csrfToken =
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        window.presenceCsrfToken;

                    const originalHtml = button.innerHTML;

                    button.disabled = true;
                    button.classList.add('opacity-70', 'cursor-wait');
                    button.innerHTML = `
            <span class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
            Memproses...
        `;

                    if (typeof window.Swal !== 'undefined') {
                        window.Swal.fire({
                            title: 'Mengambil Lokasi',
                            text: 'Pastikan GPS dan izin lokasi sudah aktif.',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: function() {
                                window.Swal.showLoading();
                            }
                        });
                    }

                    try {
                        const location = await window.getPresenceLocation();

                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append(config.latitudeField, String(location.latitude));
                        formData.append(config.longitudeField, String(location.longitude));

                        // Jika ada foto (dari retry setelah di luar radius)
                        if (config.photoFile) {
                            formData.append('proof_photo', config.photoFile);
                        }

                        const response = await fetch(config.url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const result = await window.parsePresenceResponse(response);

                        // ===== Jika server minta foto karena di luar radius =====
                        if (result.require_photo === true) {
                            if (typeof window.Swal !== 'undefined') {
                                window.Swal.close();
                            }

                            // Ambil koordinat yang sudah didapat sebelumnya
                            const currentLat = location.latitude;
                            const currentLng = location.longitude;

                            let stream = null;
                            let capturedBlob = null;

                            const {
                                value: photoFile
                            } = await Swal.fire({
                                title: 'Upload Foto Bukti Presensi',
                                html: `
            <div class="text-left space-y-3">
                <p class="text-sm text-slate-600">
                    ${result.message || 'Lokasi di luar area DUDI. Ambil foto sebagai bukti.'}
                </p>

                <div class="text-xs text-slate-500 bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-icons-round text-sm text-teal-600">my_location</span>
                        <span class="font-bold text-slate-700">Koordinat saat ini</span>
                    </div>
                    <div>Latitude: <strong id="swal-lat">${currentLat.toFixed(6)}</strong></div>
                    <div>Longitude: <strong id="swal-lng">${currentLng.toFixed(6)}</strong></div>
                </div>

                <!-- Camera Preview -->
                <div class="relative w-full rounded-2xl overflow-hidden bg-slate-900" style="aspect-ratio: 3/4; max-height: 360px;">
                    <video id="swal-camera" autoplay playsinline muted
                           class="w-full h-full object-cover"></video>

                    <!-- Kotak guide deteksi wajah -->
                    <div id="face-guide"
                         class="absolute border-2 border-teal-400 rounded-2xl pointer-events-none"
                         style="
                            width: 55%;
                            height: 45%;
                            top: 18%;
                            left: 22.5%;
                            box-shadow: 0 0 0 9999px rgba(0,0,0,0.35);
                         ">
                        <div class="absolute -top-6 left-0 right-0 text-center">
                            <span class="text-[10px] font-bold text-teal-300 uppercase tracking-wider bg-slate-900/70 px-2 py-0.5 rounded">
                                Posisikan wajah di sini
                            </span>
                        </div>
                    </div>

                    <!-- Canvas untuk capture (hidden) -->
                    <canvas id="swal-canvas" class="hidden"></canvas>
                </div>

                <div class="flex gap-2">
                    <button type="button" id="btn-capture"
                            class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all">
                        Ambil Foto
                    </button>
                    <button type="button" id="btn-retake"
                            class="flex-1 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-black uppercase tracking-widest transition-all hidden">
                        Ulangi
                    </button>
                </div>

                <p id="capture-status" class="text-xs text-center text-slate-400">
                    Pastikan wajah terlihat jelas di dalam kotak
                </p>
            </div>
        `,
                                showCancelButton: true,
                                confirmButtonText: 'Kirim Presensi',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#0d9488',
                                reverseButtons: true,
                                allowOutsideClick: false,
                                didOpen: async () => {
                                    const video = document.getElementById('swal-camera');
                                    const canvas = document.getElementById('swal-canvas');
                                    const btnCapture = document.getElementById('btn-capture');
                                    const btnRetake = document.getElementById('btn-retake');
                                    const statusEl = document.getElementById('capture-status');
                                    const faceGuide = document.getElementById('face-guide');

                                    // Matikan tombol confirm dulu sampai foto diambil
                                    const confirmBtn = Swal.getConfirmButton();
                                    confirmBtn.disabled = true;
                                    confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');

                                    try {
                                        stream = await navigator.mediaDevices.getUserMedia({
                                            video: {
                                                facingMode: 'user', // kamera depan
                                                width: {
                                                    ideal: 720
                                                },
                                                height: {
                                                    ideal: 960
                                                }
                                            },
                                            audio: false
                                        });
                                        video.srcObject = stream;
                                    } catch (err) {
                                        statusEl.textContent =
                                            'Tidak bisa mengakses kamera. Izinkan akses kamera di browser.';
                                        statusEl.classList.add('text-rose-500');
                                        return;
                                    }

                                    // Tombol Ambil Foto
                                    // Tombol Ambil Foto
                                    btnCapture.addEventListener('click', () => {
                                        const w = video.videoWidth;
                                        const h = video.videoHeight;
                                        canvas.width = w;
                                        canvas.height = h;

                                        const ctx = canvas.getContext('2d');

                                        // 1. Gambar frame kamera
                                        ctx.drawImage(video, 0, 0, w, h);

                                        // 2. Watermark koordinat + waktu
                                        const now = new Date();
                                        const timeStr = now.toLocaleString('id-ID', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit'
                                        });

                                        const latText = `Lat: ${currentLat.toFixed(6)}`;
                                        const lngText = `Lng: ${currentLng.toFixed(6)}`;
                                        const line1 = `${latText}  |  ${lngText}`;
                                        const line2 = timeStr;

                                        // Background semi-transparan di bawah
                                        const padding = 12;
                                        const fontSize = Math.max(14, Math.floor(w * 0.035));
                                        ctx.font = `bold ${fontSize}px Inter, Arial, sans-serif`;

                                        const textWidth = Math.max(
                                            ctx.measureText(line1).width,
                                            ctx.measureText(line2).width
                                        );

                                        const boxWidth = textWidth + padding * 2;
                                        const boxHeight = fontSize * 2.6 + padding;
                                        const boxX = padding;
                                        const boxY = h - boxHeight - padding;

                                        // Kotak hitam transparan
                                        ctx.fillStyle = 'rgba(15, 23, 42, 0.72)';
                                        ctx.beginPath();
                                        ctx.roundRect(boxX, boxY, boxWidth, boxHeight, 10);
                                        ctx.fill();

                                        // Teks putih
                                        ctx.fillStyle = '#ffffff';
                                        ctx.textBaseline = 'top';
                                        ctx.fillText(line1, boxX + padding, boxY + padding);
                                        ctx.font = `${fontSize * 0.9}px Inter, Arial, sans-serif`;
                                        ctx.fillStyle = '#94a3b8';
                                        ctx.fillText(line2, boxX + padding, boxY + padding + fontSize *
                                            1.35);

                                        // 3. Jadikan blob
                                        canvas.toBlob((blob) => {
                                            capturedBlob = blob;
                                            const url = URL.createObjectURL(blob);

                                            video.srcObject = null;
                                            video.poster = url;
                                            video.pause();

                                            // Stop kamera
                                            if (stream) {
                                                stream.getTracks().forEach(t => t.stop());
                                                stream = null;
                                            }

                                            faceGuide.style.display = 'none';
                                            btnCapture.classList.add('hidden');
                                            btnRetake.classList.remove('hidden');
                                            statusEl.textContent =
                                                'Foto berhasil diambil. Klik "Kirim Presensi".';
                                            statusEl.classList.remove('text-slate-400');
                                            statusEl.classList.add('text-teal-600',
                                                'font-semibold');

                                            confirmBtn.disabled = false;
                                            confirmBtn.classList.remove('opacity-50',
                                                'cursor-not-allowed');
                                        }, 'image/jpeg', 0.88);
                                    });

                                    // Tombol Ulangi
                                    btnRetake.addEventListener('click', async () => {
                                        capturedBlob = null;
                                        faceGuide.style.display = 'block';
                                        btnCapture.classList.remove('hidden');
                                        btnRetake.classList.add('hidden');
                                        statusEl.textContent =
                                            'Pastikan wajah terlihat jelas di dalam kotak';
                                        statusEl.classList.remove('text-teal-600', 'font-semibold');
                                        statusEl.classList.add('text-slate-400');

                                        confirmBtn.disabled = true;
                                        confirmBtn.classList.add('opacity-50',
                                            'cursor-not-allowed');

                                        try {
                                            stream = await navigator.mediaDevices.getUserMedia({
                                                video: {
                                                    facingMode: 'user'
                                                },
                                                audio: false
                                            });
                                            video.srcObject = stream;
                                            video.poster = '';
                                            video.play();
                                        } catch (err) {
                                            statusEl.textContent =
                                                'Gagal mengaktifkan kamera kembali.';
                                        }
                                    });
                                },
                                willClose: () => {
                                    // Pastikan kamera dimatikan saat modal ditutup
                                    if (stream) {
                                        stream.getTracks().forEach(t => t.stop());
                                        stream = null;
                                    }
                                },
                                preConfirm: () => {
                                    if (!capturedBlob) {
                                        Swal.showValidationMessage('Ambil foto terlebih dahulu');
                                        return false;
                                    }
                                    // Ubah blob jadi File agar bisa dikirim sebagai proof_photo
                                    return new File([capturedBlob], 'presence-proof.jpg', {
                                        type: 'image/jpeg'
                                    });
                                }
                            });

                            // User membatalkan
                            if (!photoFile) {
                                button.disabled = false;
                                button.classList.remove('opacity-70', 'cursor-wait');
                                button.innerHTML = originalHtml;
                                return;
                            }

                            // Kirim ulang dengan foto
                            return window.sendPresenceRequest({
                                ...config,
                                photoFile: photoFile
                            });
                        }

                        // ===== Gagal biasa =====
                        if (!response.ok || result.success === false || result.status === 'error') {
                            throw new Error(
                                result.error ||
                                result.message ||
                                `Request gagal dengan status ${response.status}.`
                            );
                        }

                        // ===== Sukses =====
                        await window.showPresenceMessage({
                            icon: 'success',
                            title: config.successTitle,
                            text: result.message || 'Presensi berhasil disimpan.',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        window.location.reload();

                    } catch (error) {
                        console.error('Presensi gagal:', error);

                        if (typeof window.Swal !== 'undefined') {
                            window.Swal.close();
                        }

                        await window.showPresenceMessage({
                            icon: 'error',
                            title: 'Presensi Gagal',
                            text: error.message || 'Terjadi kesalahan saat mengirim presensi.'
                        });

                        button.disabled = false;
                        button.classList.remove('opacity-70', 'cursor-wait');
                        button.innerHTML = originalHtml;
                    }
                };

                /**
                 * Dipanggil langsung dari onclick tombol Masuk.
                 */
                window.handleCheckInPresence = async function(event) {
                    event?.preventDefault();
                    event?.stopPropagation();

                    if (window.hasCheckedOutToday) {
                        await window.showPresenceMessage({
                            icon: 'info',
                            title: 'Presensi Selesai',
                            text: 'Presensi masuk dan pulang hari ini sudah tercatat.'
                        });
                        return;
                    }

                    if (window.hasCheckedInToday) {
                        await window.showPresenceMessage({
                            icon: 'info',
                            title: 'Sudah Presensi Masuk',
                            text: 'Anda sudah melakukan presensi masuk hari ini.'
                        });
                        return;
                    }

                    window.sendPresenceRequest({
                        button: document.getElementById('checkInPresence'),
                        url: window.checkInUrl,
                        latitudeField: 'check_in_latitude',
                        longitudeField: 'check_in_longitude',
                        successTitle: 'Presensi Masuk Berhasil'
                    });
                };

                /**
                 * Dipanggil langsung dari onclick tombol Pulang.
                 */
                window.handleCheckOutPresence = async function(event) {
                    event?.preventDefault();
                    event?.stopPropagation();

                    if (window.hasCheckedOutToday) {
                        await window.showPresenceMessage({
                            icon: 'info',
                            title: 'Presensi Selesai',
                            text: 'Anda sudah melakukan presensi pulang hari ini.'
                        });
                        return;
                    }

                    if (!window.hasCheckedInToday) {
                        await window.showPresenceMessage({
                            icon: 'warning',
                            title: 'Belum Presensi Masuk',
                            text: 'Lakukan presensi masuk terlebih dahulu.'
                        });
                        return;
                    }

                    window.sendPresenceRequest({
                        button: document.getElementById('checkOutPresence'),
                        url: window.checkOutUrl,
                        latitudeField: 'check_out_latitude',
                        longitudeField: 'check_out_longitude',
                        successTitle: 'Presensi Pulang Berhasil'
                    });
                };

                console.log('Handler presensi siap.', {
                    checkInUrl: window.checkInUrl,
                    checkOutUrl: window.checkOutUrl
                });
            </script>
        @endif
    @endonce
@endsection
