@extends('layouts._app')

@section('title', 'Detail Jurnal — Tera Prakerin')

@section('content')
    @php
        // Bisa tanda tangan selama status masih submitted
        // (siswa menyerahkan perangkat ke Pembimbing DUDI untuk ttd)
        $canSign = $jurnal->status === 'submitted';
    @endphp

    <div class="max-w-4xl mx-auto space-y-10">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">menu_book</span>
                    </span>
                    Detail Jurnal Harian
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    {{ $jurnal->date?->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>

            <a href="{{ route('jurnal.index') }}"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200
                      text-slate-600 text-xs font-black uppercase tracking-widest transition-all">
                <span class="material-icons-round text-lg">arrow_back</span>
                Kembali
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

        {{-- INFO JURNAL --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Informasi Jurnal</h3>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Siswa</p>
                        <p class="font-bold text-slate-800">{{ $jurnal->student?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kelas</p>
                        <p class="font-bold text-slate-800">
                            {{ $jurnal->student?->class?->name ?? ($jurnal->student?->class_code ?? '-') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tempat Prakerin
                            (DUDI)</p>
                        <p class="font-bold text-slate-800">{{ $jurnal->student?->internshipPlace?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pembimbing DUDI</p>
                        <p class="font-bold text-slate-800">{{ $jurnal->dudi_supervisor_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                        @php
                            $statusData = match ($jurnal->status) {
                                'signed' => [
                                    'label' => 'Signed',
                                    'class' => 'bg-teal-50 text-teal-700 border-teal-100',
                                ],
                                'submitted' => [
                                    'label' => 'Submitted',
                                    'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                ],
                                default => [
                                    'label' => 'Draft',
                                    'class' => 'bg-slate-50 text-slate-600 border-slate-100',
                                ],
                            };
                        @endphp
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border {{ $statusData['class'] }}">
                            {{ $statusData['label'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal</p>
                        <p class="font-bold text-slate-800">{{ $jurnal->date?->format('d-m-Y') }}</p>
                    </div>
                </div>

                {{-- Kegiatan --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kegiatan</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($jurnal->activities ?? [] as $act)
                            <span
                                class="inline-flex px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 text-xs font-bold border border-teal-100">
                                {{ str_replace('_', ' ', ucfirst($act)) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Deskripsi --}}
                @if ($jurnal->description)
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Deskripsi</p>
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $jurnal->description }}
                        </p>
                    </div>
                @endif

                {{-- Foto --}}
                @if ($jurnal->photo)
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Foto Kegiatan</p>
                        <img src="{{ asset('storage/' . $jurnal->photo) }}" alt="Foto Jurnal"
                            class="rounded-2xl max-h-72 object-cover border border-slate-100">
                    </div>
                @endif
            </div>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Tanda Tangan Pembimbing DUDI</h3>
            </div>

            <div class="p-8">
                @if ($jurnal->dudi_supervisor_signature)
                    {{-- Sudah ada tanda tangan --}}
                    <div class="space-y-3">
                        <p class="text-sm text-slate-500">
                            Ditandatangani pada:
                            <span class="font-bold text-slate-700">
                                {{ $jurnal->signed_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}
                            </span>
                        </p>
                        <img src="{{ asset('storage/' . $jurnal->dudi_supervisor_signature) }}" alt="Tanda Tangan"
                            class="max-w-xs border border-slate-200 rounded-xl bg-white p-2">
                    </div>
                @elseif ($canSign)
                    {{-- Form tanda tangan digital --}}
                    <div>
                        <p class="text-sm text-slate-500 mb-4">
                            Silakan bubuhkan tanda tangan digital Pembimbing DUDI di bawah ini.
                        </p>

                        <canvas id="signature-pad"
                            class="border-2 border-dashed border-slate-200 rounded-2xl w-full h-48 bg-slate-50 touch-none"></canvas>

                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="button" id="clear-signature"
                                class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600
                                           text-xs font-black uppercase tracking-widest transition-all">
                                Hapus
                            </button>
                            <button type="button" id="save-signature"
                                class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white
                                           text-xs font-black uppercase tracking-widest shadow-lg shadow-teal-200 transition-all">
                                Simpan Tanda Tangan
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Belum ada tanda tangan.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($canSign)
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
        <script>
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(248, 250, 252)',
                penColor: 'rgb(15, 23, 42)'
            });

            // Sesuaikan ukuran canvas agar tajam di layar retina
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Tombol Hapus
            document.getElementById('clear-signature').addEventListener('click', () => {
                signaturePad.clear();
            });

            // Tombol Simpan Tanda Tangan
            document.getElementById('save-signature').addEventListener('click', () => {
                if (signaturePad.isEmpty()) {
                    Swal.fire('Peringatan', 'Tanda tangan belum diisi.', 'warning');
                    return;
                }

                const dataURL = signaturePad.toDataURL('image/png');

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`{{ route('jurnal.sign', $jurnal) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            signature: dataURL
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan tanda tangan.', 'error');
                    });
            });
        </script>
    @endif
@endpush
