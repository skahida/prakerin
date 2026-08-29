@extends('layouts._app')

@section('title', 'Laporan Mingguan — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">assignment</span>
                    </span>

                    Laporan Mingguan
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Upload Laporan Kegiatan Prakerin
                </p>
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

        {{-- ===================== INFORMASI ===================== --}}
        <div
            class="p-5 rounded-2xl bg-blue-50 border border-blue-100
                text-blue-700 text-sm flex items-start gap-3">

            <span class="material-icons-round text-blue-500">
                info
            </span>

            <div>
                <p class="font-bold">Informasi Laporan</p>

                <p class="mt-1 text-blue-600">
                    Upload laporan kegiatan Prakerin setiap minggu untuk periode
                    kegiatan hari Senin sampai Sabtu.
                </p>
            </div>
        </div>

        {{-- ===================== TABEL LAPORAN ===================== --}}
        <div
            class="bg-white border border-slate-100 rounded-[2.5rem]
                shadow-2xl shadow-slate-200/50 overflow-hidden">

            <div
                class="px-8 py-6 border-b border-slate-50
                    flex flex-col sm:flex-row sm:items-center
                    justify-between gap-4">

                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        Daftar Laporan Mingguan
                    </h3>

                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Lengkapi laporan sesuai minggu kegiatan
                    </p>
                </div>

                <div
                    class="w-11 h-11 shrink-0 rounded-2xl bg-purple-50
                        text-purple-600 flex items-center justify-center
                        border border-purple-100">

                    <span class="material-icons-round">
                        calendar_month
                    </span>
                </div>
            </div>

            @if (empty($weeks) || count($weeks) === 0)

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
                        Belum Ada Data Minggu
                    </h3>

                    <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                        Daftar laporan mingguan belum tersedia.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/70">
                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest w-20">

                                    No
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest">

                                    Minggu
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest">

                                    Status
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest">

                                    Link Sosial Media
                                    <span class="text-rose-500">*</span>
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest text-right">

                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-50">
                            @foreach ($weeks as $week)
                                @php
                                    $report = $week['report'] ?? null;

                                    $isUploaded = $report && $report->report_status === 'Sudah Upload';

                                    $isDisabled = in_array($week['minggu'], $disabledWeeks ?? []);
                                @endphp

                                <tr class="group hover:bg-slate-50/50 transition-colors">

                                    {{-- Nomor --}}
                                    <td class="px-8 py-6 align-middle">
                                        <span
                                            class="text-sm font-black text-slate-300
                                                group-hover:text-teal-500 transition-colors">

                                            {{ sprintf('%02d', $loop->iteration) }}
                                        </span>
                                    </td>

                                    {{-- Minggu --}}
                                    <td class="px-8 py-6 align-middle min-w-[230px]">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-11 h-11 shrink-0 rounded-xl
                                                    {{ $isUploaded ? 'bg-teal-50 text-teal-600 border-teal-100' : 'bg-slate-50 text-slate-400 border-slate-100' }}
                                                    flex items-center justify-center
                                                    border transition-all">

                                                <span class="material-icons-round">
                                                    {{ $isUploaded ? 'event_available' : 'date_range' }}
                                                </span>
                                            </div>

                                            <div>
                                                <p
                                                    class="font-black text-slate-800 text-sm
                                                        group-hover:text-teal-600 transition-colors">

                                                    {{ $week['display_title'] }}
                                                </p>

                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    Laporan kegiatan Prakerin
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-8 py-6 align-middle whitespace-nowrap">
                                        @if ($isUploaded)
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                    px-3 py-1.5 rounded-xl text-xs font-bold
                                                    border bg-teal-50 text-teal-700
                                                    border-teal-100">

                                                <span class="material-icons-round text-sm">
                                                    check_circle
                                                </span>

                                                Sudah Upload
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                    px-3 py-1.5 rounded-xl text-xs font-bold
                                                    border bg-rose-50 text-rose-700
                                                    border-rose-100">

                                                <span class="material-icons-round text-sm">
                                                    pending_actions
                                                </span>

                                                Belum Upload
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Link Sosial Media --}}
                                    <td class="px-8 py-6 align-middle min-w-[210px]">
                                        @if ($report && $report->report_link1)
                                            <a href="{{ $report->report_link1 }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-2 px-3 py-2
                                                    rounded-xl bg-blue-50 text-blue-700
                                                    hover:bg-blue-100 border border-blue-100
                                                    text-xs font-bold transition-all">

                                                <span class="material-icons-round text-base">
                                                    open_in_new
                                                </span>

                                                Buka Link Sosmed
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                    text-xs text-slate-400 font-medium">

                                                <span class="material-icons-round text-base">
                                                    link_off
                                                </span>

                                                Belum ada link
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-8 py-6 align-middle">
                                        <div class="flex justify-end">
                                            @if ($isUploaded)
                                                <button type="button"
                                                    onclick="showEditModal(
                                                        {{ $week['minggu'] }},
                                                        @js($week['db_title'])
                                                    )"
                                                    class="inline-flex items-center justify-center gap-2
                                                        px-4 py-2.5 rounded-xl bg-amber-50
                                                        text-amber-700 hover:bg-amber-100
                                                        border border-amber-100 text-xs font-black
                                                        uppercase tracking-wider transition-all
                                                        active:scale-[0.98]">

                                                    <span class="material-icons-round text-base">
                                                        edit
                                                    </span>

                                                    Edit Laporan
                                                </button>
                                            @else
                                                <button type="button"
                                                    onclick="showUploadModal(
                                                        {{ $week['minggu'] }},
                                                        @js($week['db_title'])
                                                    )"
                                                    @disabled($isDisabled)
                                                    class="inline-flex items-center justify-center gap-2
                                                        px-4 py-2.5 rounded-xl text-xs font-black
                                                        uppercase tracking-wider transition-all
                                                        {{ $isDisabled
                                                            ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200'
                                                            : 'bg-teal-600 text-white hover:bg-teal-700 shadow-lg shadow-teal-200 active:scale-[0.98]' }}">

                                                    <span class="material-icons-round text-base">
                                                        {{ $isDisabled ? 'lock' : 'cloud_upload' }}
                                                    </span>

                                                    {{ $isDisabled ? 'Belum Tersedia' : 'Upload Laporan' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
    SWEETALERT2
============================================================ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        /*
            |--------------------------------------------------------------------------
            | URL ENDPOINT
            |--------------------------------------------------------------------------
            */
        const REPORT_STORE_URL = @json(route('report.store'));
        const REPORT_GET_URL = @json(route('report.getData'));
        const REPORT_EDIT_URL = @json(route('report.edit'));

        /*
        |--------------------------------------------------------------------------
        | CSRF TOKEN
        |--------------------------------------------------------------------------
        */
        const CSRF_TOKEN = @json(csrf_token());


        /*
        |--------------------------------------------------------------------------
        | AMBIL ERROR DARI RESPONSE LARAVEL
        |--------------------------------------------------------------------------
        */
        function getLaravelError(data) {

            if (data?.errors) {
                const firstKey = Object.keys(data.errors)[0];

                if (
                    firstKey &&
                    data.errors[firstKey] &&
                    data.errors[firstKey].length
                ) {
                    return data.errors[firstKey][0];
                }
            }

            return data?.message || 'Terjadi kesalahan pada server.';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI LINK
        |--------------------------------------------------------------------------
        */
        function isValidHttpUrl(value) {
            try {

                const url = new URL(value);

                return (
                    url.protocol === 'http:' ||
                    url.protocol === 'https:'
                );

            } catch (error) {
                return false;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | POPUP UPLOAD LAPORAN
        |--------------------------------------------------------------------------
        */
        async function showUploadModal(minggu, reportTitle) {

            const result = await Swal.fire({

                title: 'Upload Laporan',

                html: `
                <div style="
                    text-align:left;
                    margin-top:10px;
                ">

                    <div style="
                        background:#f0fdfa;
                        border:1px solid #ccfbf1;
                        border-radius:14px;
                        padding:14px;
                        margin-bottom:18px;
                    ">

                        <div style="
                            font-size:12px;
                            color:#64748b;
                            margin-bottom:4px;
                        ">
                            Laporan Prakerin
                        </div>

                        <div style="
                            font-weight:800;
                            color:#0f172a;
                            font-size:15px;
                        ">
                            Minggu ${minggu}
                        </div>

                    </div>


                    <label
                        for="swal-report-link"
                        style="
                            display:block;
                            font-weight:700;
                            color:#334155;
                            font-size:13px;
                            margin-bottom:7px;
                        "
                    >
                        Link Video / Sosial Media
                        <span style="color:#ef4444">*</span>
                    </label>


                    <input
                        id="swal-report-link"
                        type="url"
                        autocomplete="off"
                        placeholder="https://..."
                        style="
                            width:100%;
                            box-sizing:border-box;
                            border:1px solid #cbd5e1;
                            border-radius:12px;
                            padding:13px 14px;
                            outline:none;
                            font-size:14px;
                            color:#0f172a;
                        "
                    >


                    <div style="
                        display:flex;
                        align-items:flex-start;
                        gap:7px;
                        margin-top:10px;
                        color:#64748b;
                        font-size:11px;
                        line-height:1.5;
                    ">

                        <span
                            class="material-icons-round"
                            style="
                                font-size:16px;
                                color:#0d9488;
                            "
                        >
                            info
                        </span>

                        <span>
                            Pastikan link video dapat dibuka dan tidak bersifat privat.
                        </span>

                    </div>

                </div>
            `,

                icon: 'info',

                showCancelButton: true,

                confirmButtonText: `
                <span style="
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    gap:6px;
                ">
                    Kirim Laporan
                </span>
            `,

                cancelButtonText: 'Batal',

                confirmButtonColor: '#0d9488',

                cancelButtonColor: '#64748b',

                reverseButtons: true,

                focusConfirm: false,

                showLoaderOnConfirm: true,

                allowOutsideClick: () => !Swal.isLoading(),

                didOpen: () => {

                    const input = document.getElementById(
                        'swal-report-link'
                    );

                    if (input) {
                        input.focus();
                    }

                },

                preConfirm: async () => {

                    const input = document.getElementById(
                        'swal-report-link'
                    );

                    const link = input.value.trim();


                    /*
                    |--------------------------------------------------------------------------
                    | CEK KOSONG
                    |--------------------------------------------------------------------------
                    */
                    if (!link) {

                        Swal.showValidationMessage(
                            'Link laporan wajib diisi.'
                        );

                        return false;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CEK URL
                    |--------------------------------------------------------------------------
                    */
                    if (!isValidHttpUrl(link)) {

                        Swal.showValidationMessage(
                            'Masukkan link yang valid. Contoh: https://facebook.com/...'
                        );

                        return false;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | KIRIM KE SERVER
                        |--------------------------------------------------------------------------
                        */
                        const response = await fetch(
                            REPORT_STORE_URL, {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': CSRF_TOKEN,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },

                                body: JSON.stringify({
                                    report_title: reportTitle,
                                    link1: link,
                                }),
                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | AMBIL RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */
                        const data = await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | JIKA ERROR
                        |--------------------------------------------------------------------------
                        */
                        if (!response.ok) {

                            throw new Error(
                                getLaravelError(data)
                            );
                        }


                        return data;

                    } catch (error) {

                        Swal.showValidationMessage(
                            error.message ||
                            'Gagal mengirim laporan.'
                        );

                        return false;
                    }
                }
            });


            /*
            |--------------------------------------------------------------------------
            | JIKA BERHASIL
            |--------------------------------------------------------------------------
            */
            if (result.isConfirmed) {

                await Swal.fire({

                    icon: 'success',

                    title: 'Berhasil!',

                    text: 'Laporan Prakerin berhasil dikirim.',

                    confirmButtonText: 'OK',

                    confirmButtonColor: '#0d9488',

                    allowOutsideClick: false,
                });


                /*
                |--------------------------------------------------------------------------
                | REFRESH HALAMAN
                |--------------------------------------------------------------------------
                */
                window.location.reload();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | POPUP EDIT LAPORAN
        |--------------------------------------------------------------------------
        */
        async function showEditModal(minggu, reportTitle) {

            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN LOADING
            |--------------------------------------------------------------------------
            */
            Swal.fire({

                title: 'Memuat Laporan',

                text: 'Mohon tunggu...',

                allowOutsideClick: false,

                allowEscapeKey: false,

                didOpen: () => {
                    Swal.showLoading();
                }
            });


            try {

                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA LAPORAN
                |--------------------------------------------------------------------------
                */
                const url =
                    REPORT_GET_URL +
                    '?report_title=' +
                    encodeURIComponent(reportTitle);


                const response = await fetch(
                    url, {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    }
                );


                const data = await response.json();


                if (!response.ok) {

                    throw new Error(
                        getLaravelError(data)
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LINK LAMA
                |--------------------------------------------------------------------------
                */
                const oldLink =
                    data?.data?.link1 || '';


                /*
                |--------------------------------------------------------------------------
                | TUTUP LOADING
                |--------------------------------------------------------------------------
                */
                Swal.close();


                /*
                |--------------------------------------------------------------------------
                | TAMPILKAN POPUP EDIT
                |--------------------------------------------------------------------------
                */
                const result = await Swal.fire({

                    title: 'Edit Laporan',

                    html: `
                    <div style="
                        text-align:left;
                        margin-top:10px;
                    ">

                        <div style="
                            background:#fffbeb;
                            border:1px solid #fde68a;
                            border-radius:14px;
                            padding:14px;
                            margin-bottom:18px;
                        ">

                            <div style="
                                font-size:12px;
                                color:#92400e;
                                margin-bottom:4px;
                            ">
                                Edit Laporan Prakerin
                            </div>

                            <div style="
                                font-weight:800;
                                color:#78350f;
                                font-size:15px;
                            ">
                                Minggu ${minggu}
                            </div>

                        </div>


                        <label
                            for="swal-edit-link"
                            style="
                                display:block;
                                font-weight:700;
                                color:#334155;
                                font-size:13px;
                                margin-bottom:7px;
                            "
                        >
                            Link Video / Sosial Media
                            <span style="color:#ef4444">*</span>
                        </label>


                        <input
                            id="swal-edit-link"
                            type="url"
                            value="${escapeHtml(oldLink)}"
                            autocomplete="off"
                            placeholder="https://..."
                            style="
                                width:100%;
                                box-sizing:border-box;
                                border:1px solid #cbd5e1;
                                border-radius:12px;
                                padding:13px 14px;
                                outline:none;
                                font-size:14px;
                                color:#0f172a;
                            "
                        >


                        <div style="
                            display:flex;
                            align-items:flex-start;
                            gap:7px;
                            margin-top:10px;
                            color:#64748b;
                            font-size:11px;
                            line-height:1.5;
                        ">

                            <span
                                class="material-icons-round"
                                style="
                                    font-size:16px;
                                    color:#d97706;
                                "
                            >
                                info
                            </span>

                            <span>
                                Link sebelumnya dapat diganti dengan link baru.
                            </span>

                        </div>

                    </div>
                `,

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonText: 'Simpan Perubahan',

                    cancelButtonText: 'Batal',

                    confirmButtonColor: '#d97706',

                    cancelButtonColor: '#64748b',

                    reverseButtons: true,

                    focusConfirm: false,

                    showLoaderOnConfirm: true,

                    allowOutsideClick: () => !Swal.isLoading(),

                    didOpen: () => {

                        const input = document.getElementById(
                            'swal-edit-link'
                        );

                        if (input) {

                            input.focus();

                            input.setSelectionRange(
                                input.value.length,
                                input.value.length
                            );
                        }
                    },

                    preConfirm: async () => {

                        const input = document.getElementById(
                            'swal-edit-link'
                        );

                        const link = input.value.trim();


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDASI
                        |--------------------------------------------------------------------------
                        */
                        if (!link) {

                            Swal.showValidationMessage(
                                'Link laporan wajib diisi.'
                            );

                            return false;
                        }


                        if (!isValidHttpUrl(link)) {

                            Swal.showValidationMessage(
                                'Masukkan link yang valid.'
                            );

                            return false;
                        }


                        try {

                            /*
                            |--------------------------------------------------------------------------
                            | UPDATE KE SERVER
                            |--------------------------------------------------------------------------
                            */
                            const responseEdit = await fetch(
                                REPORT_EDIT_URL, {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': CSRF_TOKEN,
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },

                                    body: JSON.stringify({
                                        report_title: reportTitle,
                                        link1: link,
                                    }),
                                }
                            );


                            const editData =
                                await responseEdit.json();


                            if (!responseEdit.ok) {

                                throw new Error(
                                    getLaravelError(editData)
                                );
                            }


                            return editData;

                        } catch (error) {

                            Swal.showValidationMessage(
                                error.message ||
                                'Gagal memperbarui laporan.'
                            );

                            return false;
                        }
                    }
                });


                /*
                |--------------------------------------------------------------------------
                | BERHASIL EDIT
                |--------------------------------------------------------------------------
                */
                if (result.isConfirmed) {

                    await Swal.fire({

                        icon: 'success',

                        title: 'Berhasil!',

                        text: 'Link laporan berhasil diperbarui.',

                        confirmButtonText: 'OK',

                        confirmButtonColor: '#0d9488',

                        allowOutsideClick: false,
                    });


                    window.location.reload();
                }


            } catch (error) {

                /*
                |--------------------------------------------------------------------------
                | ERROR AMBIL DATA
                |--------------------------------------------------------------------------
                */
                Swal.fire({

                    icon: 'error',

                    title: 'Gagal',

                    text: error.message ||
                        'Data laporan tidak dapat dimuat.',

                    confirmButtonText: 'OK',

                    confirmButtonColor: '#e11d48',
                });
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        | Untuk mencegah karakter aneh pada value input.
        |--------------------------------------------------------------------------
        */
        function escapeHtml(value) {

            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;');
        }
    </script>
@endsection
