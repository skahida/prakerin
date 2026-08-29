@extends('layouts._app')

@section('title', 'Riwayat Presensi — Tera Prakerin')

@section('content')
    @php
        $userRole = auth()->user()->role;

        $isMentor = $userRole === 'mentor';
        $isAdmin = in_array($userRole, ['admin', 'super-admin']);

        $checkInValue = old(
            'check_in',
            isset($studentEdit) && $studentEdit->check_in
                ? \Carbon\Carbon::parse($studentEdit->check_in)->format('Y-m-d\TH:i')
                : '',
        );

        $checkOutValue = old(
            'check_out',
            isset($studentEdit) && $studentEdit->check_out
                ? \Carbon\Carbon::parse($studentEdit->check_out)->format('Y-m-d\TH:i')
                : '',
        );
    @endphp

    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">history</span>
                    </span>

                    Riwayat Presensi
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    @if ($isMentor)
                        Kelola Presensi Siswa Bimbingan
                    @else
                        Manajemen Presensi Siswa Prakerin
                    @endif
                </p>
            </div>
        </div>

        {{-- ===================== ALERT ===================== --}}
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

        @if ($isMentor || $isAdmin)

            {{-- ===================== FORM PRESENSI ===================== --}}
            <div
                class="bg-white border border-slate-100 rounded-[2.5rem]
                    shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            {{ isset($studentEdit) ? $title ?? 'Edit Presensi' : 'Tambah Presensi' }}
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            {{ isset($studentEdit) ? 'Perbarui data presensi siswa' : 'Isi data presensi dengan lengkap' }}
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600
                            flex items-center justify-center border border-teal-100">

                        <span class="material-icons-round">
                            {{ isset($studentEdit) ? 'edit_calendar' : 'person_add' }}
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    <form
                        action="{{ isset($studentEdit) ? route('presence.update', $studentEdit->id) : route('presence.store') }}"
                        method="POST" enctype="multipart/form-data">

                        @csrf

                        @if (isset($studentEdit))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Nama Siswa --}}
                            <div class="space-y-1.5 md:col-span-2">
                                <label for="student_select"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                    Nama Siswa
                                </label>

                                <select name="search" id="student_select" class="select2-dropdown w-full">

                                    <option value="">Pilih nama siswa</option>

                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ old('search', isset($studentEdit) ? $studentEdit->student_id : '') == $student->id ? 'selected' : '' }}>

                                            {{ $student->name }} | {{ $student->class_code }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('search')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Presensi Masuk --}}
                            <div class="space-y-1.5">
                                <label for="check_in"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                    Presensi Masuk
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2
                                            -translate-y-1/2 text-slate-400 text-lg">

                                        login
                                    </span>

                                    <input type="datetime-local" name="check_in" id="check_in" value="{{ $checkInValue }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            pl-12 pr-4 py-3">
                                </div>

                                @error('check_in')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Presensi Pulang khusus Admin --}}
                            @if ($isAdmin)
                                <div class="space-y-1.5">
                                    <label for="check_out"
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Presensi Pulang
                                    </label>

                                    <div class="relative">
                                        <span
                                            class="material-icons-round absolute left-4 top-1/2
                                                -translate-y-1/2 text-slate-400 text-lg">

                                            logout
                                        </span>

                                        <input type="datetime-local" name="check_out" id="check_out"
                                            value="{{ $checkOutValue }}"
                                            class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                                focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                                pl-12 pr-4 py-3">
                                    </div>

                                    @error('check_out')
                                        <p class="text-xs text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Latitude --}}
                            <div class="space-y-1.5">
                                <label for="latitude"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                    Koordinat Latitude
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2
                                            -translate-y-1/2 text-slate-400 text-lg">

                                        location_on
                                    </span>

                                    <input type="text" name="latitude" id="latitude"
                                        value="{{ old('latitude', isset($studentEdit) ? $studentEdit->check_in_latitude : '') }}"
                                        class="w-full rounded-2xl border-slate-200
                                            {{ $isMentor ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-slate-50' }}
                                            text-sm focus:ring-2 focus:ring-teal-500/20
                                            focus:border-teal-500 pl-12 pr-4 py-3"
                                        placeholder="Masukkan latitude" @if ($isMentor) readonly @endif>
                                </div>

                                @error('latitude')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Longitude --}}
                            <div class="space-y-1.5">
                                <label for="longitude"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                    Koordinat Longitude
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2
                                            -translate-y-1/2 text-slate-400 text-lg">

                                        location_on
                                    </span>

                                    <input type="text" name="longitude" id="longitude"
                                        value="{{ old('longitude', isset($studentEdit) ? $studentEdit->check_in_longitude : '') }}"
                                        class="w-full rounded-2xl border-slate-200
                                            {{ $isMentor ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-slate-50' }}
                                            text-sm focus:ring-2 focus:ring-teal-500/20
                                            focus:border-teal-500 pl-12 pr-4 py-3"
                                        placeholder="Masukkan longitude" @if ($isMentor) readonly @endif>
                                </div>

                                @error('longitude')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="space-y-1.5">
                                <label for="status"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                    Status
                                </label>

                                <select name="status" id="status" class="select2-dropdown w-full">

                                    <option value="">Pilih status</option>

                                    <option value="present"
                                        {{ old('status', isset($studentEdit) ? $studentEdit->status : '') === 'present' ? 'selected' : '' }}>

                                        Masuk
                                    </option>

                                    <option value="absent"
                                        {{ old('status', isset($studentEdit) ? $studentEdit->status : '') === 'absent' ? 'selected' : '' }}>

                                        Alpa
                                    </option>

                                    <option value="sick"
                                        {{ old('status', isset($studentEdit) ? $studentEdit->status : '') === 'sick' ? 'selected' : '' }}>

                                        Sakit
                                    </option>

                                    <option value="permission"
                                        {{ old('status', isset($studentEdit) ? $studentEdit->status : '') === 'permission' ? 'selected' : '' }}>

                                        Izin
                                    </option>

                                    <option value="holiday"
                                        {{ old('status', isset($studentEdit) ? $studentEdit->status : '') === 'holiday' ? 'selected' : '' }}>

                                        Libur
                                    </option>
                                </select>

                                @error('status')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="space-y-1.5">
                                <label for="note"
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                    Keterangan
                                </label>

                                <div class="relative">
                                    <span
                                        class="material-icons-round absolute left-4 top-1/2
                                            -translate-y-1/2 text-slate-400 text-lg">

                                        notes
                                    </span>

                                    <input type="text" name="note" id="note"
                                        value="{{ old('note', isset($studentEdit) ? $studentEdit->note : '') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            pl-12 pr-4 py-3"
                                        placeholder="Masukkan keterangan">
                                </div>

                                @error('note')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700
                                    text-white text-xs font-black uppercase tracking-widest
                                    transition-all flex items-center justify-center gap-2
                                    shadow-xl shadow-teal-200 active:scale-[0.98]">

                                <span class="material-icons-round text-lg">
                                    {{ isset($studentEdit) ? 'edit' : 'save' }}
                                </span>

                                {{ isset($studentEdit) ? 'Perbarui Data' : 'Simpan Presensi' }}
                            </button>

                            @if (isset($studentEdit))
                                <a href="{{ route('history.presence') }}"
                                    class="px-8 py-4 rounded-2xl bg-slate-100 hover:bg-slate-200
                                        text-slate-600 text-xs font-black uppercase tracking-widest
                                        transition-all flex items-center justify-center gap-2">

                                    <span class="material-icons-round text-lg">close</span>
                                    Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===================== FILTER MENTOR ===================== --}}
            @if ($isMentor)
                <div
                    class="bg-white border border-slate-100 rounded-[2.5rem]
                        shadow-2xl shadow-slate-200/50 overflow-hidden">

                    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">
                                Filter Riwayat
                            </h3>

                            <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                                Cari presensi siswa bimbingan
                            </p>
                        </div>

                        <div
                            class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600
                                flex items-center justify-center border border-blue-100">

                            <span class="material-icons-round">filter_alt</span>
                        </div>
                    </div>

                    <div class="p-8">
                        <form method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Pilih Siswa
                                    </label>

                                    <select name="student_id" class="select2-dropdown w-full">

                                        <option value="">Semua siswa</option>

                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ request('student_id') == $student->id ? 'selected' : '' }}>

                                                {{ $student->name }} | {{ $student->class_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Tanggal Awal
                                    </label>

                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            px-4 py-3">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Tanggal Akhir
                                    </label>

                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            px-4 py-3">
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                                <button type="submit"
                                    class="px-7 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700
                                        text-white text-xs font-black uppercase tracking-widest
                                        transition-all flex items-center justify-center gap-2
                                        shadow-lg shadow-teal-200">

                                    <span class="material-icons-round text-lg">search</span>
                                    Cari
                                </button>

                                <a href="{{ route('history.presence') }}"
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
            @endif

            {{-- ===================== FILTER ADMIN ===================== --}}
            @if ($isAdmin)
                <div
                    class="bg-white border border-slate-100 rounded-[2.5rem]
                        shadow-2xl shadow-slate-200/50 overflow-hidden">

                    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">
                                Cari Data Presensi
                            </h3>

                            <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                                Filter berdasarkan siswa, gelombang, bulan, atau tanggal
                            </p>
                        </div>

                        <div
                            class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600
                                flex items-center justify-center border border-blue-100">

                            <span class="material-icons-round">manage_search</span>
                        </div>
                    </div>

                    <div class="p-8">
                        <form method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                                {{-- Siswa --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Siswa
                                    </label>

                                    <select name="search" class="select2-dropdown w-full">

                                        <option value="">Semua siswa</option>

                                        @foreach ($students as $student)
                                            <option value="{{ $student->name }}"
                                                {{ request('search') == $student->name ? 'selected' : '' }}>

                                                {{ $student->name }} | {{ $student->class_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Gelombang --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Gelombang
                                    </label>

                                    <select name="batch_search" class="select2-dropdown w-full">

                                        <option value="">Semua gelombang</option>

                                        @foreach ($batches as $batch)
                                            <option value="{{ $batch->id }}"
                                                {{ request('batch_search') == $batch->id ? 'selected' : '' }}>

                                                {{ $batch->batch_name }} | {{ $batch->academic_year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Bulan Mulai --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Bulan Mulai
                                    </label>

                                    <input type="month" name="start_month" value="{{ request('start_month') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            px-4 py-3">
                                </div>

                                {{-- Bulan Akhir --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Bulan Akhir
                                    </label>

                                    <input type="month" name="end_month" value="{{ request('end_month') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            px-4 py-3">
                                </div>

                                {{-- Tanggal Awal --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Tanggal Awal
                                    </label>

                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            px-4 py-3">
                                </div>

                                {{-- Tanggal Akhir --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">

                                        Tanggal Akhir
                                    </label>

                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                            focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500
                                            px-4 py-3">
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row flex-wrap gap-3">
                                <button type="submit"
                                    class="px-7 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700
                                        text-white text-xs font-black uppercase tracking-widest
                                        transition-all flex items-center justify-center gap-2
                                        shadow-lg shadow-teal-200">

                                    <span class="material-icons-round text-lg">search</span>
                                    Cari
                                </button>

                                <a href="{{ route('history.presence') }}"
                                    class="px-7 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200
                                        text-slate-600 text-xs font-black uppercase tracking-widest
                                        transition-all flex items-center justify-center gap-2">

                                    <span class="material-icons-round text-lg">refresh</span>
                                    Reset
                                </a>

                                <a href="{{ route('print.presence', [
                                    'search' => request('search'),
                                    'batch_search' => request('batch_search'),
                                    'start_month' => request('start_month'),
                                    'end_month' => request('end_month'),
                                    'start_date' => request('start_date'),
                                    'end_date' => request('end_date'),
                                ]) }}"
                                    class="px-7 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900
                                        text-white text-xs font-black uppercase tracking-widest
                                        transition-all flex items-center justify-center gap-2
                                        shadow-lg shadow-slate-200">

                                    <span class="material-icons-round text-lg">print</span>
                                    Cetak
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- ===================== TABEL RIWAYAT ===================== --}}
            <div
                class="bg-white border border-slate-100 rounded-[2.5rem]
                    shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Riwayat Presensi Siswa
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Data kehadiran kegiatan prakerin
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600
                            flex items-center justify-center border border-purple-100">

                        <span class="material-icons-round">history</span>
                    </div>
                </div>

                @if ($historyPresences->isEmpty())
                    <div class="text-center py-24 px-6">
                        <div
                            class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem]
                                flex items-center justify-center mx-auto mb-6">

                            <span class="material-icons-round text-6xl">event_busy</span>
                        </div>

                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                            Data Tidak Ditemukan
                        </h3>

                        <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                            @if ($isMentor)
                                Tidak ada data presensi siswa yang ditemukan untuk guru pembimbing ini.
                            @else
                                Tidak ada data presensi siswa berdasarkan filter yang dipilih.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/70">
                                    <th class="table-heading">No</th>
                                    <th class="table-heading">Siswa</th>
                                    <th class="table-heading">Kelas</th>
                                    <th class="table-heading">DUDI</th>
                                    <th class="table-heading">Gelombang</th>
                                    <th class="table-heading">Tahun Pelajaran</th>
                                    <th class="table-heading">Hari</th>
                                    <th class="table-heading">Tanggal</th>
                                    <th class="table-heading">Masuk</th>
                                    <th class="table-heading">Pulang</th>
                                    <th class="table-heading">Lokasi Masuk</th>
                                    <th class="table-heading">Lokasi Pulang</th>
                                    <th class="table-heading">Status</th>
                                    <th class="table-heading">Keterangan</th>
                                    <th class="table-heading text-right">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-50">
                                @foreach ($historyPresences as $historyPresence)
                                    @php
                                        $statusData = match ($historyPresence->status) {
                                            'present' => [
                                                'label' => 'Masuk',
                                                'icon' => 'check_circle',
                                                'class' => 'bg-teal-50 text-teal-700 border-teal-100',
                                            ],
                                            'permission', 'premission' => [
                                                'label' => 'Izin',
                                                'icon' => 'event_note',
                                                'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            ],
                                            'sick' => [
                                                'label' => 'Sakit',
                                                'icon' => 'medical_services',
                                                'class' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            ],
                                            'holiday' => [
                                                'label' => 'Libur',
                                                'icon' => 'beach_access',
                                                'class' => 'bg-purple-50 text-purple-700 border-purple-100',
                                            ],
                                            default => [
                                                'label' => 'Alpa',
                                                'icon' => 'cancel',
                                                'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            ],
                                        };

                                        /*
                                         * File lama menampilkan created_at sebagai jam masuk
                                         * khusus pada tabel mentor. Perilaku tersebut dipertahankan.
                                         */
                                        $displayCheckInTime = $isMentor
                                            ? $historyPresence->created_at
                                            : $historyPresence->check_in;
                                    @endphp

                                    <tr class="group hover:bg-slate-50/50 transition-colors">

                                        {{-- No --}}
                                        <td class="table-cell align-top">
                                            <span
                                                class="text-sm font-black text-slate-300
                                                    group-hover:text-teal-500 transition-colors">

                                                {{ sprintf('%02d', ($historyPresences->currentPage() - 1) * $historyPresences->perPage() + $loop->iteration) }}
                                            </span>
                                        </td>

                                        {{-- Siswa --}}
                                        <td class="table-cell align-top min-w-[220px]">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 shrink-0 rounded-xl bg-teal-50
                                                        text-teal-600 flex items-center justify-center
                                                        font-black text-sm border border-teal-100
                                                        group-hover:bg-teal-600 group-hover:text-white
                                                        transition-all">

                                                    {{ strtoupper(mb_substr($historyPresence->student?->name ?? 'S', 0, 1)) }}
                                                </div>

                                                <div>
                                                    <p class="font-black text-slate-800 text-sm">
                                                        {{ $historyPresence->student?->name ?? '-' }}
                                                    </p>

                                                    <p class="text-xs text-slate-400 mt-0.5">
                                                        Siswa Prakerin
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Kelas --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-lg
                                                    bg-slate-100 text-slate-700 text-xs font-bold">

                                                {{ $historyPresence->student?->class?->name ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- DUDI --}}
                                        <td class="table-cell align-top min-w-[180px]">
                                            <p class="text-sm font-bold text-slate-700">
                                                {{ $historyPresence->student?->internshipPlace?->name ?? '-' }}
                                            </p>
                                        </td>

                                        {{-- Gelombang --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span class="text-sm text-slate-600 font-medium">
                                                {{ $historyPresence->student?->internshipBatch?->batch_name ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Tahun Pelajaran --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span class="text-sm text-slate-600 font-medium">
                                                {{ $historyPresence->student?->internshipBatch?->academic_year ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Hari --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span class="text-sm font-bold text-slate-700">
                                                {{ $historyPresence->check_in
                                                    ? \Carbon\Carbon::parse($historyPresence->check_in)->locale('id')->isoFormat('dddd')
                                                    : '-' }}
                                            </span>
                                        </td>

                                        {{-- Tanggal --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span class="text-sm font-bold text-slate-700">
                                                {{ $historyPresence->check_in
                                                    ? \Carbon\Carbon::parse($historyPresence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y')
                                                    : '-' }}
                                            </span>
                                        </td>

                                        {{-- Masuk --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            @if ($displayCheckInTime)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1
                                                        rounded-lg bg-teal-50 text-teal-700
                                                        text-xs font-bold">

                                                    <span class="material-icons-round text-sm">login</span>

                                                    {{ \Carbon\Carbon::parse($displayCheckInTime)->timezone('Asia/Jakarta')->format('H:i:s') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Belum masuk
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Pulang --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            @if ($historyPresence->check_out)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1
                                                        rounded-lg bg-blue-50 text-blue-700
                                                        text-xs font-bold">

                                                    <span class="material-icons-round text-sm">logout</span>

                                                    {{ \Carbon\Carbon::parse($historyPresence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Belum pulang
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Lokasi Masuk --}}
                                        <td class="table-cell align-top">
                                            @if ($historyPresence->check_in_location_link)
                                                <div
                                                    class="w-44 h-28 overflow-hidden rounded-2xl
                                                        border border-slate-200 bg-slate-50">

                                                    <iframe class="w-full h-full border-0"
                                                        src="{{ $historyPresence->check_in_location_link }}&output=embed"
                                                        loading="lazy" allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>

                                        {{-- Lokasi Pulang --}}
                                        <td class="table-cell align-top">
                                            @if ($historyPresence->check_out_location_link)
                                                <div
                                                    class="w-44 h-28 overflow-hidden rounded-2xl
                                                        border border-slate-200 bg-slate-50">

                                                    <iframe class="w-full h-full border-0"
                                                        src="{{ $historyPresence->check_out_location_link }}&output=embed"
                                                        loading="lazy" allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                    rounded-xl text-xs font-bold border
                                                    {{ $statusData['class'] }}">

                                                <span class="material-icons-round text-sm">
                                                    {{ $statusData['icon'] }}
                                                </span>

                                                {{ $statusData['label'] }}
                                            </span>
                                        </td>

                                        {{-- Keterangan --}}
                                        <td class="table-cell align-top min-w-[180px]">
                                            <p class="text-sm text-slate-600">
                                                {{ $historyPresence->note ?: '-' }}
                                            </p>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="table-cell align-top">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('historyPresence.edit', $historyPresence->id) }}"
                                                    class="w-9 h-9 flex items-center justify-center
                                                        rounded-xl bg-slate-50 text-slate-400
                                                        hover:bg-amber-50 hover:text-amber-600
                                                        transition-all"
                                                    title="Edit">

                                                    <span class="material-icons-round text-base">
                                                        edit
                                                    </span>
                                                </a>

                                                <button type="button"
                                                    class="delete-btn w-9 h-9 flex items-center
                                                        justify-center rounded-xl bg-slate-50
                                                        text-slate-400 hover:bg-rose-50
                                                        hover:text-rose-600 transition-all"
                                                    data-id="{{ $historyPresence->id }}" title="Hapus">

                                                    <span class="material-icons-round text-base">
                                                        delete
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($historyPresences->hasPages())
                        <div class="px-8 py-6 border-t border-slate-50">
                            {{ $historyPresences->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <style>
        .table-heading {
            padding: 1.25rem 1.5rem;
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            white-space: nowrap;
        }

        .table-cell {
            padding: 1.5rem;
        }

        .select2-container {
            width: 100% !important;
        }

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
            padding-right: 25px !important;
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
            outline: none !important;
        }

        .select2-results__option {
            padding: 10px 14px !important;
            font-size: 0.875rem !important;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #0d9488 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-dropdown').select2({
                placeholder: 'Cari / Pilih...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return 'Tidak ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });
        });
    </script>
@endpush
