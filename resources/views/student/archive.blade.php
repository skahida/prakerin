```blade
@extends('layouts._app')

@section('title', 'Arsip Siswa — Tera Prakerin')

@section('content')
    @php
        $isAdmin = in_array(auth()->user()->role, ['admin', 'super-admin']);
    @endphp

    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">
                            inventory_2
                        </span>
                    </span>

                    Arsip Siswa
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Data Siswa Prakerin yang Dinonaktifkan
                </p>
            </div>
        </div>

        @if ($isAdmin)

            {{-- ===================== NOTIFIKASI ===================== --}}
            @if (session('success'))
                <div
                    class="p-4 rounded-2xl bg-teal-50 border border-teal-100
                        text-teal-700 text-sm flex items-start gap-3">

                    <span class="material-icons-round text-teal-500">
                        check_circle
                    </span>

                    <div>
                        <p class="font-bold">Berhasil</p>
                        <p class="mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="p-4 rounded-2xl bg-rose-50 border border-rose-100
                        text-rose-700 text-sm">

                    <div class="flex items-start gap-3">
                        <span class="material-icons-round text-rose-500">
                            error
                        </span>

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

            {{-- ===================== FILTER ===================== --}}
            <div
                class="bg-white border border-slate-100 rounded-[2.5rem]
                    shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div
                    class="px-8 py-6 border-b border-slate-50
                        flex flex-col sm:flex-row sm:items-center
                        justify-between gap-4">

                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Cari Data Siswa
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Filter berdasarkan nama siswa dan gelombang
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 shrink-0 rounded-2xl
                            bg-blue-50 text-blue-600 flex items-center
                            justify-center border border-blue-100">

                        <span class="material-icons-round">
                            manage_search
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    <form method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Siswa --}}
                            <div class="space-y-1.5">
                                <label for="search"
                                    class="text-[10px] font-black text-slate-400
                                        uppercase tracking-widest">

                                    Siswa
                                </label>

                                <select name="search" id="search" class="select2-dropdown w-full">

                                    <option value="">Semua siswa</option>

                                    @foreach ($studentAll as $student)
                                        <option value="{{ $student->name }}"
                                            {{ request('search') == $student->name ? 'selected' : '' }}>

                                            {{ $student->name }} | {{ $student->class_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Gelombang --}}
                            <div class="space-y-1.5">
                                <label for="batch_search"
                                    class="text-[10px] font-black text-slate-400
                                        uppercase tracking-widest">

                                    Gelombang
                                </label>

                                <select name="batch_search" id="batch_search" class="select2-dropdown w-full">

                                    <option value="">Semua gelombang</option>

                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->batch_name }}"
                                            {{ request('batch_search') == $batch->batch_name ? 'selected' : '' }}>

                                            {{ $batch->batch_name }} | {{ $batch->academic_year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                class="px-7 py-3 rounded-2xl bg-teal-600
                                    hover:bg-teal-700 text-white text-xs
                                    font-black uppercase tracking-widest
                                    transition-all flex items-center justify-center
                                    gap-2 shadow-lg shadow-teal-200
                                    active:scale-[0.98]">

                                <span class="material-icons-round text-lg">
                                    search
                                </span>

                                Cari
                            </button>

                            <a href="{{ route('student.archive') }}"
                                class="px-7 py-3 rounded-2xl bg-slate-100
                                    hover:bg-slate-200 text-slate-600 text-xs
                                    font-black uppercase tracking-widest
                                    transition-all flex items-center
                                    justify-center gap-2">

                                <span class="material-icons-round text-lg">
                                    refresh
                                </span>

                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===================== TABEL ARSIP ===================== --}}
            <div
                class="bg-white border border-slate-100 rounded-[2.5rem]
                    shadow-2xl shadow-slate-200/50 overflow-hidden">

                <div
                    class="px-8 py-6 border-b border-slate-50
                        flex flex-col sm:flex-row sm:items-center
                        justify-between gap-4">

                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            Data Siswa Prakerin
                        </h3>

                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Daftar akun siswa yang telah diarsipkan
                        </p>
                    </div>

                    <div
                        class="w-11 h-11 shrink-0 rounded-2xl
                            bg-amber-50 text-amber-600 flex items-center
                            justify-center border border-amber-100">

                        <span class="material-icons-round">
                            archive
                        </span>
                    </div>
                </div>

                @if ($students->isEmpty())

                    {{-- Empty State --}}
                    <div class="text-center py-24 px-6">
                        <div
                            class="w-24 h-24 bg-slate-50 text-slate-200
                                rounded-[2.5rem] flex items-center justify-center
                                mx-auto mb-6">

                            <span class="material-icons-round text-6xl">
                                inventory_2
                            </span>
                        </div>

                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                            Arsip Kosong
                        </h3>

                        <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                            Tidak ada siswa yang sedang dinonaktifkan atau sesuai
                            dengan filter yang dipilih.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/70">
                                    <th class="table-heading w-20">
                                        No
                                    </th>

                                    <th class="table-heading">
                                        Nama Lengkap
                                    </th>

                                    <th class="table-heading">
                                        Jenis Kelamin
                                    </th>

                                    <th class="table-heading">
                                        Kelas
                                    </th>

                                    <th class="table-heading">
                                        DUDI
                                    </th>

                                    <th class="table-heading">
                                        Username/NIS
                                    </th>

                                    <th class="table-heading text-right">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-50">
                                @foreach ($students as $student)
                                    @php
                                        $studentName = $student->name ?? '-';

                                        $initial = mb_strtoupper(mb_substr($studentName, 0, 1));

                                        $genderLabel = match (strtolower($student->gender ?? '')) {
                                            'l', 'laki-laki', 'male' => 'Laki-laki',
                                            'p', 'perempuan', 'female' => 'Perempuan',
                                            default => $student->gender ?? '-',
                                        };

                                        $genderClass = match ($genderLabel) {
                                            'Laki-laki' => 'bg-blue-50 text-blue-700 border-blue-100',

                                            'Perempuan' => 'bg-pink-50 text-pink-700 border-pink-100',

                                            default => 'bg-slate-50 text-slate-700 border-slate-100',
                                        };
                                    @endphp

                                    <tr class="group hover:bg-slate-50/50 transition-colors">

                                        {{-- Nomor --}}
                                        <td class="table-cell align-middle">
                                            <span
                                                class="text-sm font-black text-slate-300
                                                    group-hover:text-teal-500 transition-colors">

                                                {{ sprintf('%02d', ($students->currentPage() - 1) * $students->perPage() + $loop->iteration) }}
                                            </span>
                                        </td>

                                        {{-- Nama --}}
                                        <td class="table-cell align-middle min-w-[240px]">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-11 h-11 shrink-0 rounded-xl
                                                        bg-teal-50 text-teal-600
                                                        flex items-center justify-center
                                                        font-black text-sm border
                                                        border-teal-100
                                                        group-hover:bg-teal-600
                                                        group-hover:text-white
                                                        transition-all">

                                                    {{ $initial }}
                                                </div>

                                                <div>
                                                    <p
                                                        class="font-black text-slate-800 text-sm
                                                            group-hover:text-teal-600
                                                            transition-colors">

                                                        {{ $studentName }}
                                                    </p>

                                                    <p class="text-xs text-slate-400 mt-0.5">
                                                        Siswa Prakerin
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Jenis Kelamin --}}
                                        <td class="table-cell align-middle whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                    px-3 py-1.5 rounded-xl text-xs
                                                    font-bold border {{ $genderClass }}">

                                                <span class="material-icons-round text-sm">
                                                    person
                                                </span>

                                                {{ $genderLabel }}
                                            </span>
                                        </td>

                                        {{-- Kelas --}}
                                        <td class="table-cell align-middle whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-lg
                                                    bg-slate-100 text-slate-700
                                                    text-xs font-bold">

                                                {{ $student->class?->name ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- DUDI --}}
                                        <td class="table-cell align-middle min-w-[220px]">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="material-icons-round
                                                        text-slate-300 text-lg">

                                                    business
                                                </span>

                                                <p class="text-sm font-bold text-slate-700">
                                                    {{ $student->internshipPlace?->name ?? '-' }}
                                                </p>
                                            </div>
                                        </td>

                                        {{-- Username --}}
                                        <td class="table-cell align-middle whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                    px-3 py-1.5 rounded-xl
                                                    bg-purple-50 text-purple-700
                                                    border border-purple-100
                                                    text-xs font-bold">

                                                <span class="material-icons-round text-sm">
                                                    badge
                                                </span>

                                                {{ $student->user?->username ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="table-cell align-middle">
                                            <div class="flex justify-end">
                                                @if ($student->user)
                                                    <button type="button"
                                                        class="archive-active-btn
                                                            inline-flex items-center
                                                            justify-center gap-2
                                                            px-4 py-2.5 rounded-xl
                                                            bg-teal-50 text-teal-700
                                                            hover:bg-teal-100
                                                            border border-teal-100
                                                            text-xs font-black uppercase
                                                            tracking-wider transition-all
                                                            active:scale-[0.98]"
                                                        data-id="{{ $student->user->id }}">

                                                        <span class="material-icons-round text-base">

                                                            restore
                                                        </span>

                                                        Aktifkan Kembali
                                                    </button>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5
                                                            px-3 py-1.5 rounded-xl
                                                            bg-rose-50 text-rose-700
                                                            border border-rose-100
                                                            text-xs font-bold">

                                                        <span class="material-icons-round text-sm">

                                                            error
                                                        </span>

                                                        Akun tidak ditemukan
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($students->hasPages())
                        <div class="px-8 py-6 border-t border-slate-50">
                            {{ $students->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @else
            {{-- Akses Ditolak --}}
            <div
                class="bg-white border border-slate-100 rounded-[2.5rem]
                    shadow-2xl shadow-slate-200/50 text-center py-24 px-6">

                <div
                    class="w-24 h-24 bg-rose-50 text-rose-300
                        rounded-[2.5rem] flex items-center justify-center
                        mx-auto mb-6">

                    <span class="material-icons-round text-6xl">
                        lock
                    </span>
                </div>

                <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                    Akses Ditolak
                </h3>

                <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                    Halaman arsip siswa hanya dapat diakses oleh admin.
                </p>
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
```
