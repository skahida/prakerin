@extends('layouts._app')

@section('title', 'Manajemen Gelombang — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">waves</span>
                    </span>
                    Gelombang
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Manajemen Data Gelombang Prakerin
                </p>
            </div>
        </div>

        {{-- ===================== FORM ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        {{ isset($batch) ? 'Edit Gelombang' : 'Tambah Gelombang Baru' }}
                    </h3>
                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        {{ isset($batch) ? 'Perbarui data gelombang yang dipilih' : 'Isi data gelombang dengan lengkap' }}
                    </p>
                </div>
                @if (isset($batch))
                    <a href="{{ route('batch') }}"
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

                <form action="{{ isset($batch) ? route('batch.update', $batch->id) : route('batch.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($batch))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama
                                Gelombang</label>
                            <input type="text" name="batch_name"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-5 py-3.5"
                                value="{{ old('batch_name', isset($batch) ? $batch->batch_name : '') }}"
                                placeholder="Contoh: Gelombang 1" autofocus>
                            @error('batch_name')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahun
                                Pelajaran</label>
                            <input type="text" name="academic_year"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-5 py-3.5"
                                value="{{ old('academic_year', isset($batch) ? $batch->academic_year : '') }}"
                                placeholder="Contoh: 2024/2025">
                            @error('academic_year')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mulai
                                Prakerin</label>
                            <input type="date" name="start_date"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-5 py-3.5"
                                value="{{ old('start_date', isset($batch) ? $batch->start_date : '') }}">
                            @error('start_date')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Selesai
                                Prakerin</label>
                            <input type="date" name="end_date"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all px-5 py-3.5"
                                value="{{ old('end_date', isset($batch) ? $batch->end_date : '') }}">
                            @error('end_date')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-xl shadow-teal-200 active:scale-[0.98]">
                            <span class="material-icons-round text-lg">
                                {{ isset($batch) ? 'edit' : 'save' }}
                            </span>
                            {{ isset($batch) ? 'Perbarui Data' : 'Simpan Gelombang' }}
                        </button>

                        @if (isset($batch))
                            <a href="{{ route('batch') }}"
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
                            placeholder="Cari nama gelombang..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-teal-200">
                            <span class="material-icons-round text-base">search</span>
                            Cari
                        </button>
                        <a href="{{ route('batch') }}"
                            class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2">
                            <span class="material-icons-round text-base">refresh</span>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Card --}}
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                @if ($batches->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">
                                        No</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Gelombang</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Periode</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Tahun Pelajaran</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($batches as $i => $item)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-5">
                                            <span
                                                class="text-sm font-black text-slate-300 group-hover:text-teal-500 transition-colors">
                                                {{ sprintf('%02d', $loop->iteration) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-sm border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-all">
                                                    {{ substr($item->batch_name, 0, 1) }}
                                                </div>
                                                <div
                                                    class="font-black text-slate-800 text-sm group-hover:text-teal-600 transition-colors">
                                                    {{ $item->batch_name }}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="text-sm text-slate-600">
                                                <div class="font-medium">
                                                    {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</div>
                                                <div class="text-xs text-slate-400">s/d
                                                    {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                                                {{ $item->academic_year }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <select
                                                class="status_batch text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 py-2 px-3"
                                                data-id="{{ $item->id }}">
                                                <option value="active"
                                                    {{ $item->status_batch == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="non-active"
                                                    {{ $item->status_batch == 'non-active' ? 'selected' : '' }}>Non-Active
                                                </option>
                                            </select>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('batch.edit', $item->id) }}"
                                                    class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-amber-50 hover:text-amber-600 transition-all"
                                                    title="Edit">
                                                    <span class="material-icons-round text-base">edit</span>
                                                </a>

                                                <button type="button"
                                                    class="archive-btn w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-all"
                                                    data-id="{{ $item->id }}" title="Arsipkan">
                                                    <span class="material-icons-round text-base">archive</span>
                                                </button>

                                                <button type="button"
                                                    class="add-detail-btn w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-teal-50 hover:text-teal-600 transition-all"
                                                    data-id="{{ $item->id }}" title="Detail Mentor & DUDI">
                                                    <span class="material-icons-round text-base">visibility</span>
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
                            <span class="material-icons-round text-6xl">waves</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Database Kosong</h3>
                        <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2">
                            Belum ada data gelombang yang terdaftar dalam sistem.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Status batch change (sudah ada di app.blade, tapi jaga-jaga)
            document.querySelectorAll('.status_batch').forEach(select => {
                select.addEventListener('change', function() {
                    // logic sudah di-handle di app.blade.php
                });
            });

            document.querySelectorAll(".add-detail-btn").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();

                    let batchId = this.dataset.id;
                    let tempDetails = [];

                    Swal.fire({
                        title: 'Detail Gelombang',
                        width: 800,
                        html: `
                    <form id="detail-form" class="text-left">
                        <input type="hidden" name="batch_id" value="${batchId}">
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Pilih Mentor</label>
                            <select id="mentor_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm px-4 py-2.5">
                                <option value="">-- Pilih Mentor --</option>
                                @foreach ($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Pilih DUDI</label>
                            <select id="place_code" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm px-4 py-2.5">
                                <option value="">-- Pilih DUDI --</option>
                                @foreach ($places as $place)
                                    <option value="{{ $place->code }}">{{ $place->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" id="addTempBtn" class="px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-xl hover:bg-teal-700 transition-all">
                            + Tambah ke Tabel
                        </button>

                        <hr class="my-5 border-slate-100">
                        <h5 class="text-sm font-bold text-slate-700 mb-3">Data Sementara</h5>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-sm" id="tempTable">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-500">Mentor</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-500">DUDI</th>
                                        <th class="px-4 py-2.5 text-center text-xs font-bold text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </form>
                `,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan ke Database',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'px-6 py-2.5 rounded-xl bg-teal-600 text-white text-xs font-bold',
                            cancelButton: 'px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold'
                        },
                        didOpen: () => {
                            let savedDetails = [];

                            fetch(`/batch-details/${batchId}/json`)
                                .then(res => res.json())
                                .then(data => {
                                    savedDetails = data.map(d => ({
                                        mentor_name: d.mentor.name,
                                        place_name: d.place.name,
                                        mentor_id: d.mentor_id,
                                        place_code: d.place_code,
                                        batch_id: d.internship_batch_id,
                                        id: d.id
                                    }));
                                    renderTempTable();
                                });

                            document.getElementById("addTempBtn").addEventListener(
                                "click",
                                function() {
                                    let mentorId = document.getElementById(
                                        "mentor_id").value;
                                    let mentorName = document.getElementById(
                                        "mentor_id").selectedOptions[0].text;
                                    let placeCode = document.getElementById(
                                        "place_code").value;
                                    let placeName = document.getElementById(
                                        "place_code").selectedOptions[0].text;

                                    if (!mentorId || !placeCode) {
                                        Swal.showValidationMessage(
                                            "Mentor dan DUDI wajib dipilih");
                                        return;
                                    }

                                    tempDetails.push({
                                        batch_id: batchId,
                                        mentor_id: mentorId,
                                        mentor_name: mentorName,
                                        place_code: placeCode,
                                        place_name: placeName
                                    });

                                    renderTempTable();
                                });

                            function renderTempTable() {
                                let tbody = document.querySelector("#tempTable tbody");
                                tbody.innerHTML = "";

                                let allDetails = [...savedDetails, ...tempDetails];

                                allDetails.forEach((d, index) => {
                                    tbody.innerHTML += `
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-2.5">${d.mentor_name}</td>
                                    <td class="px-4 py-2.5">${d.place_name}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <button type="button" class="removeBtn px-2.5 py-1 bg-rose-500 hover:bg-rose-600 text-white text-xs rounded-lg" data-index="${index}">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            `;
                                });

                                document.querySelectorAll(".removeBtn").forEach(btn => {
                                    btn.addEventListener("click", function() {
                                        let i = this.dataset.index;

                                        if (i >= savedDetails.length) {
                                            tempDetails.splice(i -
                                                savedDetails.length,
                                                1);
                                            renderTempTable();
                                        } else {
                                            let detailId = savedDetails[
                                                i].id;
                                            Swal.fire({
                                                title: 'Hapus Data?',
                                                text: "Data ini akan dihapus permanen!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Ya, hapus',
                                                cancelButtonText: 'Batal',
                                                customClass: {
                                                    popup: 'rounded-2xl',
                                                    confirmButton: 'px-5 py-2 rounded-xl bg-rose-500 text-white text-xs font-bold',
                                                    cancelButton: 'px-5 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold'
                                                }
                                            }).then((result) => {
                                                if (result
                                                    .isConfirmed
                                                ) {
                                                    fetch(`/batch-details/${detailId}`, {
                                                            method: 'DELETE',
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Accept': 'application/json',
                                                                'Content-Type': 'application/json'
                                                            },
                                                        })
                                                        .then(
                                                            res => {
                                                                if (!
                                                                    res
                                                                    .ok
                                                                )
                                                                    throw new Error(
                                                                        `HTTP error! status: ${res.status}`
                                                                    );
                                                                return res
                                                                    .json();
                                                            })
                                                        .then(
                                                            data => {
                                                                if (data
                                                                    .success
                                                                ) {
                                                                    Swal.fire(
                                                                            'Berhasil',
                                                                            data
                                                                            .message,
                                                                            'success'
                                                                        )
                                                                        .then(
                                                                            () => {
                                                                                savedDetails
                                                                                    .splice(
                                                                                        i,
                                                                                        1
                                                                                    );
                                                                                const
                                                                                    tbody =
                                                                                    document
                                                                                    .querySelector(
                                                                                        "#tempTable tbody"
                                                                                    );
                                                                                if (
                                                                                    tbody
                                                                                    )
                                                                                    renderTempTable();
                                                                            }
                                                                        );
                                                                } else {
                                                                    Swal.fire(
                                                                        'Gagal',
                                                                        data
                                                                        .message,
                                                                        'error'
                                                                    );
                                                                }
                                                            })
                                                        .catch(
                                                            err => {
                                                                console
                                                                    .error(
                                                                        err
                                                                    );
                                                                Swal.fire(
                                                                    'Error',
                                                                    'Terjadi kesalahan server: ' +
                                                                    err
                                                                    .message,
                                                                    'error'
                                                                );
                                                            });
                                                }
                                            });
                                        }
                                    });
                                });
                            }
                        },
                        preConfirm: () => {
                            if (tempDetails.length === 0) {
                                Swal.showValidationMessage(
                                    "Minimal 1 detail harus ditambahkan");
                                return false;
                            }
                            return tempDetails;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("{{ route('batch-details.bulk-store') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        details: result.value
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Berhasil!', data.message, 'success')
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire('Gagal!', data.message, 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error!', 'Terjadi kesalahan server',
                                        'error');
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush
