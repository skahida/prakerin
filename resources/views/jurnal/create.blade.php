@extends('layouts._app')

@section('title', 'Tambah Jurnal Harian — Tera Prakerin')

@section('content')
    @php
        $userRole = auth()->user()->role;
        $isStudent = $userRole === 'student';
    @endphp

    <div class="max-w-4xl mx-auto space-y-10">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">add</span>
                    </span>
                    Tambah Jurnal Harian
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Isi catatan kegiatan prakerin hari ini
                </p>
            </div>
        </div>

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

        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Form Jurnal Harian</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Lengkapi data kegiatan dengan benar
                    </p>
                </div>
                <div
                    class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100">
                    <span class="material-icons-round">edit_note</span>
                </div>
            </div>

            <div class="p-8">
                <form action="{{ route('jurnal.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Siswa (jika bukan student) --}}
                        @unless ($isStudent)
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama
                                    Siswa</label>
                                <select name="student_id" class="select2-dropdown w-full" required>
                                    <option value="">Pilih siswa</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} | {{ $student->class_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endunless

                        {{-- Tanggal --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                          focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                required>
                        </div>

                        {{-- Nama Pembimbing DUDI --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pembimbing
                                DUDI</label>
                            <input type="text" name="dudi_supervisor_name" value="{{ old('dudi_supervisor_name') }}"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                          focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                placeholder="Contoh: Bapak Ahmad" required>
                        </div>

                        {{-- Kegiatan --}}
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">
                                Kegiatan (pilih minimal 1)
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @php
                                    $activityOptions = [
                                        'routine_service' => 'Service Rutin',
                                        'oil_change' => 'Ganti Oli',
                                        'troubleshooting' => 'Troubleshooting',
                                        'parts_replacement' => 'Ganti Sparepart',
                                        'inspection' => 'Pemeriksaan',
                                        'cleaning' => 'Pembersihan',
                                        'other' => 'Lainnya',
                                    ];
                                @endphp
                                @foreach ($activityOptions as $value => $label)
                                    <label
                                        class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-teal-300 transition-colors">
                                        <input type="checkbox" name="activities[]" value="{{ $value }}"
                                            class="rounded text-teal-600 focus:ring-teal-500"
                                            {{ in_array($value, old('activities', [])) ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi
                                Kegiatan</label>
                            <textarea name="description" rows="4"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                             focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                placeholder="Jelaskan kegiatan yang dilakukan hari ini...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Foto --}}
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Foto Kegiatan
                                (opsional)</label>
                            <input type="file" name="photo" accept="image/*"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                          focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3">
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest
                                       transition-all flex items-center justify-center gap-2 shadow-xl shadow-teal-200 active:scale-[0.98]">
                            <span class="material-icons-round text-lg">save</span>
                            Simpan Jurnal
                        </button>
                        <a href="{{ route('jurnal.index') }}"
                            class="px-8 py-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest
                                  transition-all flex items-center justify-center gap-2">
                            <span class="material-icons-round text-lg">close</span>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
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
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 10px !important;
        }

        .select2-dropdown {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1) !important;
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
                width: '100%'
            });
        });
    </script>
@endpush
