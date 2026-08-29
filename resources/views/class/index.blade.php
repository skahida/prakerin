@extends('layouts._app')

@section('title', 'Manajemen Kelas — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">meeting_room</span>
                    </span>
                    Kelas
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Manajemen Data Kelas Prakerin
                </p>
            </div>
        </div>

        {{-- ===================== FORM (Full Width) ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        {{ isset($class) ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
                    </h3>
                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        {{ isset($class) ? 'Perbarui data kelas yang dipilih' : 'Isi data kelas dengan lengkap' }}
                    </p>
                </div>
                @if (isset($class))
                    <a href="{{ route('class') }}"
                        class="text-xs font-bold text-slate-400 hover:text-slate-600 flex items-center gap-1 transition-colors">
                        <span class="material-icons-round text-sm">close</span>
                        Batal
                    </a>
                @endif
            </div>

            <div class="p-8">
                {{-- Success --}}
                @if (session('success'))
                    <div
                        class="mb-6 p-4 rounded-2xl bg-teal-50 border border-teal-100 text-teal-700 text-sm flex items-start gap-3">
                        <span class="material-icons-round text-teal-500">check_circle</span>
                        <div>
                            <p class="font-bold">Berhasil</p>
                            <p class="mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 text-sm">
                        <div class="flex items-start gap-3">
                            <span class="material-icons-round text-rose-500">error</span>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ isset($class) ? route('class.update', $class->code) : route('class.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($class))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode
                                Kelas</label>
                            <input type="text" name="code"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-5 py-3.5 {{ isset($class) ? 'bg-slate-100 cursor-not-allowed opacity-70' : '' }}"
                                value="{{ old('code', isset($class) ? $class->code : '') }}" placeholder="Contoh: XI-RPL-1"
                                autofocus {{ isset($class) ? 'readonly' : '' }}>
                            @error('code')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama
                                Kelas</label>
                            <input type="text" name="name"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-5 py-3.5"
                                value="{{ old('name', isset($class) ? $class->name : '') }}"
                                placeholder="Contoh: XI Rekayasa Perangkat Lunak 1">
                            @error('name')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-xl shadow-teal-200 active:scale-[0.98]">
                            <span class="material-icons-round text-lg">
                                {{ isset($class) ? 'edit' : 'save' }}
                            </span>
                            {{ isset($class) ? 'Perbarui Data' : 'Simpan Kelas' }}
                        </button>

                        @if (isset($class))
                            <a href="{{ route('class') }}"
                                class="px-8 py-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                                <span class="material-icons-round text-lg">close</span>
                                Batal
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== SEARCH + TABLE ===================== --}}
        <div class="space-y-6">

            {{-- Search --}}
            <div class="bg-white border border-slate-100 rounded-[2rem] shadow-xl shadow-slate-200/40 p-5">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <span
                            class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input type="text" name="search"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pl-12 pr-4 py-3.5"
                            placeholder="Cari nama atau kode kelas..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-teal-200">
                            <span class="material-icons-round text-base">search</span>
                            Cari
                        </button>
                        <a href="{{ route('class') }}"
                            class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2">
                            <span class="material-icons-round text-base">refresh</span>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Card --}}
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                @if ($classes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">
                                        No</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Informasi Kelas</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($classes as $i => $item)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-sm font-black text-slate-300 group-hover:text-teal-500 transition-colors">
                                                {{ sprintf('%02d', $classes->firstItem() + $i) }}
                                            </span>
                                        </td>

                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-lg shadow-sm border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-all duration-500">
                                                    {{ substr($item->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-black text-slate-800 uppercase tracking-tight text-base group-hover:text-teal-600 transition-colors">
                                                        {{ $item->name }}
                                                    </div>
                                                    <div class="flex items-center gap-1.5 mt-1 text-slate-400">
                                                        <span class="material-icons-round text-xs">tag</span>
                                                        <span class="text-[10px] font-bold uppercase tracking-tighter">
                                                            {{ $item->code }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('class.edit', $item->code) }}"
                                                    class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-amber-50 hover:text-amber-600 border border-transparent hover:border-amber-100 transition-all shadow-sm"
                                                    title="Edit">
                                                    <span class="material-icons-round text-sm">edit</span>
                                                </a>

                                                <button type="button"
                                                    class="archive-btn w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-600 border border-transparent hover:border-rose-100 transition-all shadow-sm"
                                                    data-id="{{ $item->id }}" title="Arsipkan">
                                                    <span class="material-icons-round text-sm">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($classes->hasPages())
                        <div
                            class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                Menampilkan
                                <span class="text-slate-800">{{ $classes->firstItem() }}</span> -
                                <span class="text-slate-800">{{ $classes->lastItem() }}</span> dari
                                <span class="text-slate-800">{{ $classes->total() }}</span> data
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ $classes->previousPageUrl() ?? '#' }}"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-teal-600 hover:border-teal-600 transition-all shadow-sm {{ $classes->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                                    <span class="material-icons-round text-sm">west</span>
                                </a>

                                <div
                                    class="px-4 py-2 bg-teal-600 text-white rounded-xl font-black text-xs shadow-lg shadow-teal-200">
                                    {{ $classes->currentPage() }}
                                </div>

                                <a href="{{ $classes->nextPageUrl() ?? '#' }}"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-teal-600 hover:border-teal-600 transition-all shadow-sm {{ !$classes->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                                    <span class="material-icons-round text-sm">east</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-24">
                        <div
                            class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
                            <span class="material-icons-round text-6xl">meeting_room</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Database Kosong</h3>
                        <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2">
                            Belum ada data kelas yang terdaftar dalam sistem.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
