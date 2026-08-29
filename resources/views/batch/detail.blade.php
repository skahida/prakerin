@extends('layouts.app')

@section('title', 'Detail Gelombang — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">list_alt</span>
                    </span>
                    Detail Gelombang
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Daftar Mentor & Tempat Magang per Gelombang
                </p>
            </div>

            <a href="{{ route('batch') }}"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all">
                <span class="material-icons-round text-base">arrow_back</span>
                Kembali
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

            @if ($details->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">
                                    No</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Gelombang</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Mentor
                                </th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tempat
                                    Magang</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($details as $i => $detail)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <span
                                            class="text-sm font-black text-slate-300 group-hover:text-teal-500 transition-colors">
                                            {{ sprintf('%02d', $loop->iteration) }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div
                                            class="font-black text-slate-800 text-sm group-hover:text-teal-600 transition-colors">
                                            {{ $detail->batch->batch_name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                                {{ substr($detail->mentor->name ?? 'M', 0, 1) }}
                                            </div>
                                            <span class="font-semibold text-slate-700 text-sm">
                                                {{ $detail->mentor->name ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs border border-amber-100">
                                                {{ substr($detail->internshipPlace->name ?? 'D', 0, 1) }}
                                            </div>
                                            <span class="font-semibold text-slate-700 text-sm">
                                                {{ $detail->internshipPlace->name ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex justify-end">
                                            <form action="{{ route('internship_batch_details.destroy', $detail->id) }}"
                                                method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="delete-btn w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-600 border border-transparent hover:border-rose-100 transition-all shadow-sm"
                                                    data-name="{{ $detail->mentor->name ?? 'data ini' }}">
                                                    <span class="material-icons-round text-sm">delete_outline</span>
                                                </button>
                                            </form>
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
                        <span class="material-icons-round text-6xl">list_alt</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Belum Ada Data</h3>
                    <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2">
                        Belum ada detail mentor & tempat magang yang terdaftar.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                const name = this.dataset.name;

                Swal.fire({
                    title: 'Hapus Data?',
                    text: `Yakin ingin menghapus data "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#f1f5f9',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-bold',
                        cancelButton: 'px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600'
                    }
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
