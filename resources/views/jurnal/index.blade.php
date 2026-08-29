@extends('layouts._app')

@section('title', 'Jurnal Harian — Tera Prakerin')

@section('content')
    @php
        $userRole = auth()->user()->role;
        $isStudent = $userRole === 'student';
        $isMentor = $userRole === 'mentor';
        $isAdmin = in_array($userRole, ['admin', 'super-admin']);
    @endphp

    <div class="max-w-7xl mx-auto space-y-10">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">menu_book</span>
                    </span>
                    Jurnal Harian
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    @if ($isStudent)
                        Catatan Kegiatan Prakerin Harian
                    @elseif ($isMentor)
                        Monitoring Jurnal Siswa Bimbingan
                    @else
                        Manajemen Jurnal Harian Siswa
                    @endif
                </p>
            </div>

            <a href="{{ route('jurnal.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700
                      text-white text-xs font-black uppercase tracking-widest shadow-xl shadow-teal-200
                      transition-all active:scale-[0.98]">
                <span class="material-icons-round text-lg">add</span>
                Tambah Jurnal
            </a>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-100 text-teal-700 text-sm flex items-start gap-3">
                <span class="material-icons-round text-teal-500">check_circle</span>
                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 text-sm flex items-start gap-3">
                <span class="material-icons-round text-rose-500">error</span>
                <div>
                    <p class="font-bold">Gagal</p>
                    <p class="mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- FILTER (Mentor & Admin) --}}
        @if ($isMentor || $isAdmin)
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Filter Jurnal</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                            Cari berdasarkan siswa, tanggal, atau status
                        </p>
                    </div>
                    <div
                        class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <span class="material-icons-round">filter_alt</span>
                    </div>
                </div>

                <div class="p-8">
                    <form method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</label>
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
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal
                                    Awal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                              focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal
                                    Akhir</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm
                                              focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3">
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</label>
                                <select name="status" class="select2-dropdown w-full">
                                    <option value="">Semua status</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>
                                        Submitted</option>
                                    <option value="signed" {{ request('status') === 'signed' ? 'selected' : '' }}>Signed
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                class="px-7 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest
                                           transition-all flex items-center justify-center gap-2 shadow-lg shadow-teal-200">
                                <span class="material-icons-round text-lg">search</span>
                                Cari
                            </button>
                            <a href="{{ route('jurnal.index') }}"
                                class="px-7 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest
                                      transition-all flex items-center justify-center gap-2">
                                <span class="material-icons-round text-lg">refresh</span>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- TABEL --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Daftar Jurnal Harian</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Catatan kegiatan siswa di tempat prakerin
                    </p>
                </div>
                <div
                    class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                    <span class="material-icons-round">menu_book</span>
                </div>
            </div>

            @if ($jurnals->isEmpty())
                <div class="text-center py-24 px-6">
                    <div
                        class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
                        <span class="material-icons-round text-6xl">menu_book</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Belum Ada Jurnal</h3>
                    <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                        Belum ada data jurnal harian yang tersedia.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/70">
                                <th class="table-heading">No</th>
                                @unless ($isStudent)
                                    <th class="table-heading">Siswa</th>
                                    <th class="table-heading">Kelas</th>
                                    <th class="table-heading">DUDI</th>
                                @endunless
                                <th class="table-heading">Tanggal</th>
                                <th class="table-heading">Kegiatan</th>
                                <th class="table-heading">Pembimbing DUDI</th>
                                <th class="table-heading">Status</th>
                                <th class="table-heading text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($jurnals as $jurnal)
                                @php
                                    $statusData = match ($jurnal->status) {
                                        'signed' => [
                                            'label' => 'Signed',
                                            'icon' => 'verified',
                                            'class' => 'bg-teal-50 text-teal-700 border-teal-100',
                                        ],
                                        'submitted' => [
                                            'label' => 'Submitted',
                                            'icon' => 'send',
                                            'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        ],
                                        default => [
                                            'label' => 'Draft',
                                            'icon' => 'edit_note',
                                            'class' => 'bg-slate-50 text-slate-600 border-slate-100',
                                        ],
                                    };
                                @endphp
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="table-cell align-top">
                                        <span
                                            class="text-sm font-black text-slate-300 group-hover:text-teal-500 transition-colors">
                                            {{ sprintf('%02d', ($jurnals->currentPage() - 1) * $jurnals->perPage() + $loop->iteration) }}
                                        </span>
                                    </td>

                                    @unless ($isStudent)
                                        <td class="table-cell align-top min-w-[200px]">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 shrink-0 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center
                                                            font-black text-sm border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-all">
                                                    {{ strtoupper(mb_substr($jurnal->student?->name ?? 'S', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-slate-800 text-sm">
                                                        {{ $jurnal->student?->name ?? '-' }}</p>
                                                    <p class="text-xs text-slate-400 mt-0.5">Siswa Prakerin</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-cell align-top whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                                                {{ $jurnal->student?->class?->name ?? ($jurnal->student?->class_code ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="table-cell align-top min-w-[160px]">
                                            <p class="text-sm font-bold text-slate-700">
                                                {{ $jurnal->student?->internshipPlace?->name ?? '-' }}
                                            </p>
                                        </td>
                                    @endunless

                                    <td class="table-cell align-top whitespace-nowrap">
                                        <span class="text-sm font-bold text-slate-700">
                                            {{ $jurnal->date ? $jurnal->date->locale('id')->isoFormat('dddd, D MMM YYYY') : '-' }}
                                        </span>
                                    </td>

                                    <td class="table-cell align-top min-w-[180px]">
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($jurnal->activities ?? [] as $act)
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">
                                                    {{ str_replace('_', ' ', $act) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="table-cell align-top whitespace-nowrap">
                                        <span class="text-sm font-medium text-slate-700">
                                            {{ $jurnal->dudi_supervisor_name ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="table-cell align-top whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border {{ $statusData['class'] }}">
                                            <span class="material-icons-round text-sm">{{ $statusData['icon'] }}</span>
                                            {{ $statusData['label'] }}
                                        </span>
                                    </td>

                                    <td class="table-cell align-top">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('jurnal.show', $jurnal) }}"
                                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400
                                                      hover:bg-teal-50 hover:text-teal-600 transition-all"
                                                title="Detail">
                                                <span class="material-icons-round text-base">visibility</span>
                                            </a>

                                            @if ($jurnal->status !== 'signed')
                                                <a href="{{ route('jurnal.edit', $jurnal) }}"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400
                                                          hover:bg-amber-50 hover:text-amber-600 transition-all"
                                                    title="Edit">
                                                    <span class="material-icons-round text-base">edit</span>
                                                </a>
                                            @endif

                                            <button type="button"
                                                class="delete-jurnal-btn w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50
                                                           text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all"
                                                data-id="{{ $jurnal->id }}" title="Hapus">
                                                <span class="material-icons-round text-base">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($jurnals->hasPages())
                    <div class="px-8 py-6 border-t border-slate-50">
                        {{ $jurnals->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
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

        .select2-dropdown {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1) !important;
            overflow: hidden;
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

            // Hapus jurnal
            $('.delete-jurnal-btn').on('click', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Jurnal?',
                    text: 'Data jurnal ini akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/jurnal/${id}`;
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
