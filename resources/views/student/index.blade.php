@extends('layouts._app')

@section('title', 'Data Siswa — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">face</span>
                    </span>
                    Siswa Prakerin
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Manajemen Data Siswa Prakerin
                </p>
            </div>
        </div>

        {{-- ===================== MENTOR VIEW ===================== --}}
        @if (auth()->user()->role == 'mentor')
            <div class="space-y-6">
                <div class="bg-white border border-slate-100 rounded-[2rem] shadow-xl shadow-slate-200/40 p-5">
                    <form method="GET" action="{{ route('student') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <span
                                class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input type="text" name="search"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pl-12 pr-4 py-3.5"
                                placeholder="Cari nama siswa..." value="{{ request()->input('search') }}">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-teal-200">
                                <span class="material-icons-round text-base">search</span> Cari
                            </button>
                            <a href="{{ route('student') }}"
                                class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2">
                                <span class="material-icons-round text-base">refresh</span> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
                    @if ($students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-slate-50/50">
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            No</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Nama</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Kelas / Jurusan</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            DUDI</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Kontak</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Gelombang</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Periode</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach ($students as $student)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-slate-400 font-medium">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $student->name }}</div>
                                                <div class="text-xs text-slate-400">
                                                    {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-slate-700">{{ $student->class->name ?? '-' }}
                                                </div>
                                                <div class="text-xs text-slate-400">{{ $student->department->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600">
                                                {{ $student->internshipPlace->name ?? '-' }}</td>
                                            <td class="px-6 py-4">
                                                <div class="text-xs text-slate-600">WA:
                                                    {{ $student->whatsapp_number ?? '-' }}</div>
                                                <div class="text-xs text-slate-400">TG:
                                                    {{ $student->telegram_number ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-slate-700">
                                                    {{ $student->internshipBatch->batch_name ?? '-' }}</div>
                                                <div class="text-xs text-slate-400">
                                                    {{ $student->internshipBatch->academic_year ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($student->internshipBatch->start_date)->locale('id')->format('d M Y') }}
                                                <br>s/d
                                                {{ \Carbon\Carbon::parse($student->internshipBatch->end_date)->locale('id')->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-slate-100">
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-20">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="material-icons-round text-4xl">face</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Belum ada data siswa</h3>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===================== ADMIN / SUPER-ADMIN ===================== --}}
        @else
            {{-- Form --}}
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">
                            {{ isset($student) ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            {{ isset($student) ? 'Perbarui data siswa' : 'Isi data siswa dengan lengkap' }}
                        </p>
                    </div>
                    @if (isset($student))
                        <a href="{{ route('student') }}"
                            class="text-xs font-bold text-slate-400 hover:text-slate-600 flex items-center gap-1">
                            <span class="material-icons-round text-sm">close</span> Batal
                        </a>
                    @endif
                </div>

                <div class="p-8">
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

                    <form action="{{ isset($student) ? route('student.update', $student->id) : route('student.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($student))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama
                                    Lengkap</label>
                                <input type="text" name="name"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                    value="{{ old('name', $student->name ?? '') }}" placeholder="Nama lengkap" autofocus>
                                @error('name')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NIS</label>
                                <input type="text" name="nis"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                    value="{{ old('nis', $student->nis ?? '') }}" placeholder="NIS">
                                @error('nis')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis
                                    Kelamin</label>
                                <select name="gender" class="select2-dropdown w-full">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L"
                                        {{ old('gender', $student->gender ?? '') == 'L' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="P"
                                        {{ old('gender', $student->gender ?? '') == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                                @error('gender')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelas</label>
                                <select name="class_code" class="select2-dropdown w-full">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->code }}"
                                            {{ old('class_code', $student->class_code ?? '') == $class->code ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_code')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jurusan</label>
                                <select name="department_code" class="select2-dropdown w-full">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->code }}"
                                            {{ old('department_code', $student->department_code ?? '') == $department->code ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_code')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">DUDI</label>
                                <select name="internship_place_code" class="select2-dropdown w-full">
                                    <option value="">Pilih DUDI</option>
                                    @foreach ($dudies as $dudi)
                                        <option value="{{ $dudi->code }}"
                                            {{ old('internship_place_code', $student->internship_place_code ?? '') == $dudi->code ? 'selected' : '' }}>
                                            {{ $dudi->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('internship_place_code')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gelombang</label>
                                <select name="internship_batch_id" class="select2-dropdown w-full">
                                    <option value="">Pilih Gelombang</option>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}"
                                            {{ old('internship_batch_id', $student->internship_batch_id ?? '') == $batch->id ? 'selected' : '' }}>
                                            {{ $batch->batch_name }} — {{ $batch->academic_year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('internship_batch_id')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pembimbing</label>
                                <select name="mentor_id" class="select2-dropdown w-full">
                                    <option value="">Pilih Pembimbing</option>
                                    @foreach ($mentors as $mentor)
                                        <option value="{{ $mentor->id }}"
                                            {{ old('mentor_id', $student->mentor_id ?? '') == $mentor->id ? 'selected' : '' }}>
                                            {{ $mentor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mentor_id')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">WhatsApp</label>
                                <input type="text" name="whatsapp_number"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                    value="{{ old('whatsapp_number', $student->whatsapp_number ?? '') }}"
                                    placeholder="08xxxxxxxxxx">
                                @error('whatsapp_number')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Telegram</label>
                                <input type="text" name="telegram_number"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                    value="{{ old('telegram_number', $student->telegram_number ?? '') }}"
                                    placeholder="Chat ID / Nomor">
                                @error('telegram_number')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Username</label>
                                <input type="text" name="username"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                    value="{{ old('username', $student->user->username ?? '') }}"
                                    placeholder="Username login">
                                @error('username')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Password</label>
                                @if (isset($student) && !empty($student->user->password))
                                    <div
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 text-sm text-slate-500 px-4 py-3">
                                        Password sudah ada (tidak dapat diedit)
                                    </div>
                                @else
                                    <input type="text" name="password"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                        value="{{ old('password', 'smkaha') }}" readonly>
                                @endif
                                @error('password')
                                    <p class="text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5 md:col-span-2 lg:col-span-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Foto
                                    Profil</label>
                                <input type="file" name="foto_url"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700">
                                @if (isset($student) && !empty($student->user->foto_url))
                                    <img src="{{ asset('storage/' . $student->user->foto_url) }}" alt="Foto"
                                        class="mt-2 w-20 h-20 rounded-xl object-cover border border-slate-200">
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-xl shadow-teal-200 active:scale-[0.98]">
                                <span class="material-icons-round text-lg">{{ isset($student) ? 'edit' : 'save' }}</span>
                                {{ isset($student) ? 'Perbarui Data' : 'Simpan Siswa' }}
                            </button>
                            @if (isset($student))
                                <a href="{{ route('student') }}"
                                    class="px-8 py-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                                    <span class="material-icons-round text-lg">close</span> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Search + Table --}}
            <div class="space-y-6">
                <div class="bg-white border border-slate-100 rounded-[2rem] shadow-xl shadow-slate-200/40 p-5">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</label>
                            <select name="search" class="select2-dropdown w-full">
                                <option value="">Semua Siswa</option>
                                @foreach ($studentAll as $s)
                                    <option value="{{ $s->name }}"
                                        {{ request()->input('search') == $s->name ? 'selected' : '' }}>
                                        {{ $s->name }} | {{ $s->class_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gelombang</label>
                            <select name="batch_search" class="select2-dropdown w-full">
                                <option value="">Semua Gelombang</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}"
                                        {{ request()->input('batch_search') == $batch->id ? 'selected' : '' }}>
                                        {{ $batch->batch_name }} | {{ $batch->academic_year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2 md:col-span-2">
                            <button type="submit"
                                class="px-5 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest flex items-center gap-2 shadow-lg shadow-teal-200">
                                <span class="material-icons-round text-base">search</span> Cari
                            </button>
                            <a href="{{ route('student') }}"
                                class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                <span class="material-icons-round text-base">refresh</span> Reset
                            </a>
                            <a href="{{ route('print.student') }}?search={{ request()->input('search') }}&batch_search={{ request()->input('batch_search') }}"
                                class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                <span class="material-icons-round text-base">download</span> Cetak
                            </a>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
                    @if ($students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-slate-50/50">
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            No</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Nama</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Kelas</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            DUDI</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Username</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach ($students as $item)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-slate-400 font-medium">
                                                {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $item->name }}</div>
                                                <div class="text-xs text-slate-400">
                                                    {{ $item->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600">{{ $item->class->name ?? '-' }}</td>
                                            <td class="px-6 py-4 text-slate-600">{{ $item->internshipPlace->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                                                    {{ $item->user->username ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('student.edit', $item->id) }}"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all"
                                                        title="Edit">
                                                        <span class="material-icons-round text-base">edit</span>
                                                    </a>
                                                    <button type="button"
                                                        class="archive-btn w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all"
                                                        data-id="{{ $item->user->id }}" title="Arsipkan">
                                                        <span class="material-icons-round text-base">archive</span>
                                                    </button>
                                                    <button type="button"
                                                        class="reset-password-btn w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all"
                                                        data-id="{{ $item->user->id }}" title="Reset Password">
                                                        <span class="material-icons-round text-base">key</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-slate-100">
                            {{ $students->appends(['search' => $search ?? ''])->links() }}
                        </div>
                    @else
                        <div class="text-center py-20">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="material-icons-round text-4xl">face</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Belum ada data siswa</h3>
                            <p class="text-slate-400 text-sm mt-1">Tambahkan siswa baru menggunakan form di atas</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-dropdown').select2({
                placeholder: "Cari / Pilih...",
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
        });
    </script>
@endpush
