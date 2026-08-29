@extends('layouts._app')

@section('title', 'Data Admin — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">admin_panel_settings</span>
                    </span>
                    Admin
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Manajemen User Admin Sistem
                </p>
            </div>
        </div>

        {{-- ===================== FORM ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        {{ isset($admin) ? 'Edit Admin' : 'Tambah Admin Baru' }}
                    </h3>
                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        {{ isset($admin) ? 'Perbarui data admin' : 'Isi data admin dengan lengkap' }}
                    </p>
                </div>
                @if (isset($admin))
                    <a href="{{ route('admin') }}"
                        class="text-xs font-bold text-slate-400 hover:text-slate-600 flex items-center gap-1 transition-colors">
                        <span class="material-icons-round text-sm">close</span>
                        Batal
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

                <form action="{{ isset($admin) ? route('admin.update', $admin->id) : route('admin.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($admin))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama
                                Lengkap</label>
                            <input type="text" name="name"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('name', $admin->name ?? '') }}" placeholder="Nama lengkap" autofocus>
                            @error('name')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Username</label>
                            <input type="text" name="username"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('username', $admin->user->username ?? '') }}" placeholder="Username login">
                            @error('username')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Password</label>
                            @if (isset($admin) && !empty($admin->user->password))
                                <input type="hidden" name="password" value="{{ $admin->user->password }}">
                                <div
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-100 text-sm text-slate-500 px-4 py-3">
                                    Password sudah ada (tidak dapat diedit)
                                </div>
                            @else
                                <input type="text" name="password"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                    value="{{ old('password') }}" placeholder="Masukan password">
                            @endif
                            @error('password')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Role</label>
                            <select name="role" class="select2-dropdown w-full">
                                <option value="">Pilih Role</option>
                                <option value="super-admin"
                                    {{ old('role', $admin->user->role ?? '') == 'super-admin' ? 'selected' : '' }}>
                                    Super Admin
                                </option>
                                <option value="admin"
                                    {{ old('role', $admin->user->role ?? '') == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                            </select>
                            @error('role')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-xl shadow-teal-200 active:scale-[0.98]">
                            <span class="material-icons-round text-lg">
                                {{ isset($admin) ? 'edit' : 'save' }}
                            </span>
                            {{ isset($admin) ? 'Perbarui Data' : 'Simpan Admin' }}
                        </button>

                        @if (isset($admin))
                            <a href="{{ route('admin') }}"
                                class="px-8 py-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                                <span class="material-icons-round text-lg">close</span>
                                Batal
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== TABLE ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

            @if ($admins->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">
                                    No</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Admin
                                </th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Username</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Role
                                </th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($admins as $i => $item)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <span
                                            class="text-sm font-black text-slate-300 group-hover:text-teal-500 transition-colors">
                                            {{ sprintf('%02d', $loop->iteration) }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-sm border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-all">
                                                {{ substr($item->name, 0, 1) }}
                                            </div>
                                            <div
                                                class="font-black text-slate-800 text-sm group-hover:text-teal-600 transition-colors">
                                                {{ $item->name }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                                            {{ $item->user->username ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-6">
                                        @php
                                            $role = $item->user->role ?? '-';
                                            $roleClass =
                                                $role === 'super-admin'
                                                    ? 'bg-purple-50 text-purple-700 border-purple-100'
                                                    : 'bg-blue-50 text-blue-700 border-blue-100';
                                        @endphp
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border {{ $roleClass }}">
                                            {{ $role === 'super-admin' ? 'Super Admin' : ($role === 'admin' ? 'Admin' : $role) }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.edit', $item->id) }}"
                                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all"
                                                title="Edit">
                                                <span class="material-icons-round text-base">edit</span>
                                            </a>

                                            <button type="button"
                                                class="archive-btn w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all"
                                                data-id="{{ $item->user_id }}" title="Arsipkan">
                                                <span class="material-icons-round text-base">archive</span>
                                            </button>

                                            <button type="button"
                                                class="reset-password-btn w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all"
                                                data-id="{{ $item->user_id }}" title="Reset Password">
                                                <span class="material-icons-round text-base">key</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-24">
                    <div
                        class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
                        <span class="material-icons-round text-6xl">admin_panel_settings</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Database Kosong</h3>
                    <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2">
                        Belum ada data admin yang terdaftar dalam sistem.
                    </p>
                </div>
            @endif
        </div>
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
