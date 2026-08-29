@extends('layouts._app')

@section('title', 'Penilaian Laporan — Tera Prakerin')

@section('content')
    @php
        $studentName = $reports->first()?->student?->name ?? 'Siswa';

        $grandAverage = $reports->isEmpty()
            ? 0
            : $reports->sum(function ($report) {
                    return $report->grade ?? 0;
                }) / $reports->count();
    @endphp

    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">rate_review</span>
                    </span>

                    Penilaian Laporan
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Penilaian Laporan Praktik Kerja Industri
                </p>
            </div>

            {{-- Tombol Header --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('report') }}"
                    class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200
                        text-slate-600 text-xs font-black uppercase tracking-widest
                        transition-all flex items-center justify-center gap-2">

                    <span class="material-icons-round text-lg">
                        arrow_back
                    </span>

                    Kembali
                </a>

                <a href="{{ route('generate.pdf', ['studentId' => $studentId]) }}" target="_blank"
                    class="px-6 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900
                        text-white text-xs font-black uppercase tracking-widest
                        transition-all flex items-center justify-center gap-2
                        shadow-xl shadow-slate-200 active:scale-[0.98]">

                    <span class="material-icons-round text-lg">
                        picture_as_pdf
                    </span>

                    Cetak PDF
                </a>
            </div>
        </div>

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
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100
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

        {{-- ===================== INFORMASI SISWA ===================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            {{-- Nama Siswa --}}
            <div
                class="bg-white border border-slate-100 rounded-[2rem]
                    shadow-xl shadow-slate-200/40 p-6">

                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 shrink-0 rounded-2xl bg-teal-50
                            text-teal-600 flex items-center justify-center
                            border border-teal-100">

                        <span class="material-icons-round">
                            person
                        </span>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Nama Siswa
                        </p>

                        <p class="font-black text-slate-800 mt-1 truncate">
                            {{ $studentName }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Jumlah Laporan --}}
            <div
                class="bg-white border border-slate-100 rounded-[2rem]
                    shadow-xl shadow-slate-200/40 p-6">

                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 shrink-0 rounded-2xl bg-blue-50
                            text-blue-600 flex items-center justify-center
                            border border-blue-100">

                        <span class="material-icons-round">
                            assignment
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Jumlah Laporan
                        </p>

                        <p class="font-black text-slate-800 mt-1">
                            {{ $reports->count() }} laporan
                        </p>
                    </div>
                </div>
            </div>

            {{-- Grand Average --}}
            <div
                class="bg-white border border-slate-100 rounded-[2rem]
                    shadow-xl shadow-slate-200/40 p-6">

                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 shrink-0 rounded-2xl bg-purple-50
                            text-purple-600 flex items-center justify-center
                            border border-purple-100">

                        <span class="material-icons-round">
                            analytics
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Rata-rata Nilai
                        </p>

                        <p class="font-black text-slate-800 mt-1">
                            {{ number_format($grandAverage, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== KARTU PENILAIAN ===================== --}}
        <div
            class="bg-white border border-slate-100 rounded-[2.5rem]
                shadow-2xl shadow-slate-200/50 overflow-hidden">

            <div
                class="px-8 py-6 border-b border-slate-50
                    flex flex-col sm:flex-row sm:items-center
                    justify-between gap-4">

                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        Penilaian Laporan Praktik Kerja Industri
                    </h3>

                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Isi konten 50% dan tampilan video 50%
                    </p>
                </div>

                <div
                    class="w-11 h-11 shrink-0 rounded-2xl bg-amber-50
                        text-amber-600 flex items-center justify-center
                        border border-amber-100">

                    <span class="material-icons-round">
                        grading
                    </span>
                </div>
            </div>

            @if ($reports->isEmpty())

                {{-- Empty State --}}
                <div class="text-center py-24 px-6">
                    <div
                        class="w-24 h-24 bg-slate-50 text-slate-200
                            rounded-[2.5rem] flex items-center justify-center
                            mx-auto mb-6">

                        <span class="material-icons-round text-6xl">
                            assignment_late
                        </span>
                    </div>

                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                        Laporan Belum Tersedia
                    </h3>

                    <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                        Belum ada laporan mingguan siswa yang dapat dinilai.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/70">
                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest w-20">

                                    No
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest">

                                    Siswa
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest">

                                    Minggu
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest">

                                    Status
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest">

                                    Link Sosial Media
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest min-w-[180px]">

                                    Isi Konten
                                    <span class="text-teal-600">(50%)</span>
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest min-w-[180px]">

                                    Tampilan Video
                                    <span class="text-teal-600">(50%)</span>
                                </th>

                                <th
                                    class="px-6 py-5 text-[10px] font-black text-slate-400
                                        uppercase tracking-widest text-center">

                                    Total
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-50">
                            @foreach ($reports as $report)
                                @php
                                    $contentValue = $report->content ?? 0;
                                    $videoValue = $report->video_appearance ?? 0;

                                    $totalValue = 0.5 * $contentValue + 0.5 * $videoValue;

                                    $isUploaded = $report->report_status === 'Sudah Upload';

                                    $reportStudentName = $report->student?->name ?? '-';

                                    $initial = mb_strtoupper(mb_substr($reportStudentName, 0, 1));
                                @endphp

                                <tr class="report-row group hover:bg-slate-50/50 transition-colors"
                                    data-report-id="{{ $report->id }}">

                                    {{-- Nomor --}}
                                    <td class="px-6 py-6 align-middle">
                                        <span
                                            class="text-sm font-black text-slate-300
                                                group-hover:text-teal-500 transition-colors">

                                            {{ sprintf('%02d', $loop->iteration) }}
                                        </span>
                                    </td>

                                    {{-- Nama Siswa --}}
                                    <td class="px-6 py-6 align-middle min-w-[230px]">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-xl bg-teal-50
                                                    text-teal-600 flex items-center justify-center
                                                    font-black text-sm border border-teal-100
                                                    group-hover:bg-teal-600 group-hover:text-white
                                                    transition-all">

                                                {{ $initial }}
                                            </div>

                                            <div>
                                                <p class="font-black text-slate-800 text-sm">
                                                    {{ $reportStudentName }}
                                                </p>

                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    Siswa Prakerin
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Minggu --}}
                                    <td class="px-6 py-6 align-middle min-w-[160px]">
                                        <div class="flex items-center gap-2">
                                            <span class="material-icons-round text-purple-400 text-lg">

                                                calendar_month
                                            </span>

                                            <span class="text-sm font-bold text-slate-700">
                                                {{ $report->report_title }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-6 align-middle whitespace-nowrap">
                                        @if ($isUploaded)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                    rounded-xl text-xs font-bold border
                                                    bg-teal-50 text-teal-700 border-teal-100">

                                                <span class="material-icons-round text-sm">
                                                    check_circle
                                                </span>

                                                {{ $report->report_status }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                    rounded-xl text-xs font-bold border
                                                    bg-rose-50 text-rose-700 border-rose-100">

                                                <span class="material-icons-round text-sm">
                                                    pending_actions
                                                </span>

                                                {{ $report->report_status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Link Sosial Media --}}
                                    <td class="px-6 py-6 align-middle min-w-[180px]">
                                        @if ($report->report_link1)
                                            <a href="{{ $report->report_link1 }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-2 px-3 py-2
                                                    rounded-xl bg-blue-50 text-blue-700
                                                    hover:bg-blue-100 border border-blue-100
                                                    text-xs font-bold transition-all">

                                                <span class="material-icons-round text-base">
                                                    open_in_new
                                                </span>

                                                Buka Link
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                    text-xs text-slate-400 font-medium">

                                                <span class="material-icons-round text-base">
                                                    link_off
                                                </span>

                                                Tidak ada link
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Isi Konten --}}
                                    <td class="px-6 py-6 align-middle">
                                        <div class="space-y-1.5">
                                            <div class="relative">
                                                <span
                                                    class="material-icons-round absolute left-3
                                                        top-1/2 -translate-y-1/2
                                                        text-slate-400 text-base">

                                                    article
                                                </span>

                                                <input type="number" name="content_{{ $report->id }}"
                                                    data-report-id="{{ $report->id }}"
                                                    data-student-id="{{ $report->student_id }}"
                                                    value="{{ $contentValue }}" min="0" max="100"
                                                    placeholder="0–100"
                                                    class="content-input w-full rounded-xl
                                                        border-slate-200 bg-slate-50 text-sm
                                                        focus:ring-2 focus:ring-teal-500/20
                                                        focus:border-teal-500 pl-10 pr-4 py-3">
                                            </div>

                                            <p class="text-[10px] text-slate-400 font-medium">
                                                Nilai maksimal 100
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Tampilan Video --}}
                                    <td class="px-6 py-6 align-middle">
                                        <div class="space-y-1.5">
                                            <div class="relative">
                                                <span
                                                    class="material-icons-round absolute left-3
                                                        top-1/2 -translate-y-1/2
                                                        text-slate-400 text-base">

                                                    videocam
                                                </span>

                                                <input type="number" name="video_appearance_{{ $report->id }}"
                                                    data-report-id="{{ $report->id }}"
                                                    data-student-id="{{ $report->student_id }}"
                                                    value="{{ $videoValue }}" min="0" max="100"
                                                    placeholder="0–100"
                                                    class="video-appearance-input w-full rounded-xl
                                                        border-slate-200 bg-slate-50 text-sm
                                                        focus:ring-2 focus:ring-teal-500/20
                                                        focus:border-teal-500 pl-10 pr-4 py-3">
                                            </div>

                                            <p class="text-[10px] text-slate-400 font-medium">
                                                Nilai maksimal 100
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-6 align-middle text-center">
                                        <div
                                            class="total-grade w-14 h-14 rounded-2xl
                                                bg-teal-50 text-teal-700 border border-teal-100
                                                flex items-center justify-center mx-auto
                                                font-black text-base">

                                            {{ number_format($totalValue, 1) }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================== FOOTER NILAI ===================== --}}
                <div
                    class="px-8 py-6 border-t border-slate-100 bg-slate-50/50
                        flex flex-col lg:flex-row lg:items-center
                        justify-between gap-6">

                    {{-- Grand Average --}}
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600
                                flex items-center justify-center border border-purple-100">

                            <span class="material-icons-round">
                                analytics
                            </span>
                        </div>

                        <div>
                            <p
                                class="text-[10px] font-black text-slate-400
                                    uppercase tracking-widest">

                                Grand Average
                            </p>

                            <p id="grandAverage" class="text-2xl font-black text-slate-800 mt-1">

                                {{ number_format($grandAverage, 2) }}
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <button type="button" id="saveButton"
                        class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700
                            text-white text-xs font-black uppercase tracking-widest
                            transition-all flex items-center justify-center gap-2
                            shadow-xl shadow-teal-200 active:scale-[0.98]">

                        <span class="material-icons-round text-lg">
                            save
                        </span>

                        Simpan Nilai
                    </button>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportRows = document.querySelectorAll('.report-row');

            reportRows.forEach(function(row) {
                const contentInput = row.querySelector('.content-input');
                const videoInput = row.querySelector('.video-appearance-input');
                const totalElement = row.querySelector('.total-grade');

                if (!contentInput || !videoInput || !totalElement) {
                    return;
                }

                const updateTotal = function() {
                    let content = parseFloat(contentInput.value) || 0;
                    let video = parseFloat(videoInput.value) || 0;

                    content = Math.min(100, Math.max(0, content));
                    video = Math.min(100, Math.max(0, video));

                    const total = (content * 0.5) + (video * 0.5);

                    totalElement.textContent = total.toFixed(1);
                };

                contentInput.addEventListener('input', updateTotal);
                videoInput.addEventListener('input', updateTotal);
            });
        });
    </script>
@endpush
