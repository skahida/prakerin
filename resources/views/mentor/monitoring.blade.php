@extends('layouts._app')

@section('title', 'Monitoring Prakerin — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- ===================== HEADER ===================== --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">add_a_photo</span>
                    </span>

                    Monitoring Prakerin
                </h2>

                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Dokumentasi Kunjungan Guru Pembimbing
                </p>
            </div>
        </div>

        {{-- ===================== NOTIFIKASI ===================== --}}
        @if (session('success_monitor'))
            <div
                class="p-4 rounded-2xl bg-teal-50 border border-teal-100
                    text-teal-700 text-sm flex items-start gap-3">

                <span class="material-icons-round text-teal-500">
                    check_circle
                </span>

                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="mt-0.5">{{ session('success_monitor') }}</p>
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

        {{-- ===================== FORM MONITORING ===================== --}}
        <div
            class="bg-white border border-slate-100 rounded-[2.5rem]
                shadow-2xl shadow-slate-200/50 overflow-hidden">

            <div
                class="px-8 py-6 border-b border-slate-50
                    flex flex-col sm:flex-row sm:items-center
                    justify-between gap-4">

                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        Upload Bukti Monitoring
                    </h3>

                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Lengkapi data kunjungan dan unggah foto dokumentasi
                    </p>
                </div>

                <div
                    class="w-11 h-11 shrink-0 rounded-2xl bg-teal-50
                        text-teal-600 flex items-center justify-center
                        border border-teal-100">

                    <span class="material-icons-round">
                        photo_camera
                    </span>
                </div>
            </div>

            <div class="p-8">
                <form id="monitoring-form" action="{{ route('monitoring.store') }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- DUDI --}}
                        <div class="space-y-1.5">
                            <label for="place_code"
                                class="text-[10px] font-black text-slate-400
                                    uppercase tracking-widest">

                                Pilih DUDI
                            </label>

                            <select name="place_code" id="place_code" class="select2-dropdown w-full" required>

                                <option value="">Pilih DUDI</option>

                                @foreach ($places as $place)
                                    <option value="{{ $place->code }}"
                                        {{ old('place_code') == $place->code ? 'selected' : '' }}>

                                        {{ $place->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('place_code')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Monitoring --}}
                        <div class="space-y-1.5">
                            <label for="status"
                                class="text-[10px] font-black text-slate-400
                                    uppercase tracking-widest">

                                Status Monitoring
                            </label>

                            <select name="status" id="status" class="select2-dropdown w-full" required>

                                <option value="">Pilih status</option>

                                <option value="Penerjunan" {{ old('status') === 'Penerjunan' ? 'selected' : '' }}>

                                    Penerjunan
                                </option>

                                <option value="Monitoring 1" {{ old('status') === 'Monitoring 1' ? 'selected' : '' }}>

                                    Monitoring 1
                                </option>

                                <option value="Monitoring 2" {{ old('status') === 'Monitoring 2' ? 'selected' : '' }}>

                                    Monitoring 2
                                </option>

                                <option value="Monitoring 3" {{ old('status') === 'Monitoring 3' ? 'selected' : '' }}>

                                    Monitoring 3
                                </option>

                                <option value="Penarikan" {{ old('status') === 'Penarikan' ? 'selected' : '' }}>

                                    Penarikan
                                </option>
                            </select>

                            @error('status')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="space-y-1.5">
                            <label for="date"
                                class="text-[10px] font-black text-slate-400
                                    uppercase tracking-widest">

                                Tanggal Monitoring
                            </label>

                            <div class="relative">
                                <span
                                    class="material-icons-round absolute left-4 top-1/2
                                        -translate-y-1/2 text-slate-400 text-lg">

                                    calendar_month
                                </span>

                                <input type="date" name="date" id="date"
                                    value="{{ old('date', now()->format('Y-m-d')) }}"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50
                                        text-sm focus:ring-2 focus:ring-teal-500/20
                                        focus:border-teal-500 pl-12 pr-4 py-3"
                                    required>
                            </div>

                            @error('date')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Foto --}}
                        <div class="space-y-1.5">
                            <label for="monitor_photo"
                                class="text-[10px] font-black text-slate-400
                                    uppercase tracking-widest">

                                Bukti Foto
                            </label>

                            <label for="monitor_photo"
                                class="flex items-center gap-3 w-full rounded-2xl
                                    border border-dashed border-slate-300 bg-slate-50
                                    hover:bg-teal-50 hover:border-teal-300
                                    px-4 py-3 cursor-pointer transition-all">

                                <span
                                    class="w-10 h-10 shrink-0 rounded-xl bg-white
                                        text-teal-600 border border-slate-200
                                        flex items-center justify-center">

                                    <span class="material-icons-round">
                                        add_photo_alternate
                                    </span>
                                </span>

                                <div class="min-w-0">
                                    <p id="file-name" class="text-sm font-bold text-slate-700 truncate">

                                        Pilih foto monitoring
                                    </p>

                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Gunakan kamera atau pilih dari galeri
                                    </p>
                                </div>
                            </label>

                            <input type="file" name="monitor_photo" id="monitor_photo" class="hidden" accept="image/*"
                                onchange="previewImage(event)" required>

                            @error('monitor_photo')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ===================== PREVIEW FOTO ===================== --}}
                    <div id="preview-container" class="hidden mt-6">

                        <div class="p-5 rounded-3xl bg-slate-50
                                border border-slate-100">

                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons-round text-teal-500">
                                        image
                                    </span>

                                    <p class="text-sm font-black text-slate-700">
                                        Preview Foto
                                    </p>
                                </div>

                                <button type="button" onclick="removePreview()"
                                    class="w-9 h-9 rounded-xl bg-white text-slate-400
                                        hover:bg-rose-50 hover:text-rose-600
                                        border border-slate-200 flex items-center
                                        justify-center transition-all"
                                    title="Hapus foto">

                                    <span class="material-icons-round text-base">
                                        close
                                    </span>
                                </button>
                            </div>

                            <div
                                class="max-w-xl overflow-hidden rounded-2xl
                                    border border-slate-200 bg-white">

                                <img id="preview-image" src="" alt="Preview Foto Monitoring"
                                    class="w-full max-h-96 object-contain">
                            </div>
                        </div>
                    </div>

                    {{-- Hidden Location --}}
                    <input type="hidden" name="check_latitude" id="check_latitude">

                    <input type="hidden" name="check_longitude" id="check_longitude">

                    <input type="hidden" name="check_location_link" id="check_location_link">

                    {{-- Status Lokasi --}}
                    <div id="location-status"
                        class="mt-6 p-4 rounded-2xl bg-amber-50
                            border border-amber-100 text-amber-700
                            text-sm flex items-start gap-3">

                        <span id="location-status-icon" class="material-icons-round text-amber-500">

                            location_searching
                        </span>

                        <div>
                            <p id="location-status-title" class="font-bold">

                                Mengambil lokasi
                            </p>

                            <p id="location-status-text" class="mt-0.5">

                                Izinkan akses lokasi browser untuk melanjutkan.
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Upload --}}
                    <div class="mt-8">
                        <button type="submit" id="submit-btn" disabled
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700
                                disabled:bg-slate-200 disabled:text-slate-400
                                disabled:shadow-none disabled:cursor-not-allowed
                                text-white text-xs font-black uppercase tracking-widest
                                transition-all flex items-center justify-center gap-2
                                shadow-xl shadow-teal-200 active:scale-[0.98]">

                            <span id="btn-icon" class="material-icons-round text-lg">
                                cloud_upload
                            </span>

                            <span id="btn-text">
                                Upload Foto
                            </span>

                            <span id="btn-spinner"
                                class="hidden w-4 h-4 border-2 border-white/30
                                    border-t-white rounded-full animate-spin">
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== RIWAYAT MONITORING ===================== --}}
        <div
            class="bg-white border border-slate-100 rounded-[2.5rem]
                shadow-2xl shadow-slate-200/50 overflow-hidden">

            <div
                class="px-8 py-6 border-b border-slate-50
                    flex flex-col sm:flex-row sm:items-center
                    justify-between gap-4">

                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        Riwayat Monitoring
                    </h3>

                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        Dokumentasi kunjungan yang telah diunggah
                    </p>
                </div>

                <div
                    class="w-11 h-11 shrink-0 rounded-2xl bg-purple-50
                        text-purple-600 flex items-center justify-center
                        border border-purple-100">

                    <span class="material-icons-round">
                        photo_library
                    </span>
                </div>
            </div>

            @if ($monitorings->isEmpty())

                {{-- Empty State --}}
                <div class="text-center py-24 px-6">
                    <div
                        class="w-24 h-24 bg-slate-50 text-slate-200
                            rounded-[2.5rem] flex items-center
                            justify-center mx-auto mb-6">

                        <span class="material-icons-round text-6xl">
                            no_photography
                        </span>
                    </div>

                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                        Belum Ada Monitoring
                    </h3>

                    <p class="text-slate-400 font-medium max-w-md mx-auto mt-2">
                        Dokumentasi monitoring yang telah diunggah akan tampil di sini.
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

                                    Tanggal
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest">

                                    DUDI
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest">

                                    Status
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest">

                                    Foto
                                </th>

                                <th
                                    class="px-8 py-5 text-[10px] font-black
                                        text-slate-400 uppercase tracking-widest text-right">

                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-50">
                            @foreach ($monitorings as $monitor)
                                @php
                                    $photoUrl = asset('storage/monitoring/' . basename($monitor->photo));

                                    $statusClass = match ($monitor->status) {
                                        'Penerjunan' => 'bg-blue-50 text-blue-700 border-blue-100',

                                        'Monitoring 1',
                                        'Monitoring 2',
                                        'Monitoring 3'
                                            => 'bg-amber-50 text-amber-700 border-amber-100',

                                        'Penarikan' => 'bg-teal-50 text-teal-700 border-teal-100',

                                        default => 'bg-slate-50 text-slate-700 border-slate-100',
                                    };
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

                                    {{-- Tanggal --}}
                                    <td class="px-8 py-6 align-middle whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-xl
                                                    bg-slate-50 text-slate-500
                                                    flex items-center justify-center
                                                    border border-slate-100">

                                                <span class="material-icons-round text-lg">
                                                    calendar_today
                                                </span>
                                            </div>

                                            <div>
                                                <p class="text-sm font-black text-slate-700">
                                                    {{ $monitor->created_at->format('d-m-Y') }}
                                                </p>

                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    {{ $monitor->created_at->format('H:i') }} WIB
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- DUDI --}}
                                    <td class="px-8 py-6 align-middle min-w-[230px]">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-xl
                                                    bg-teal-50 text-teal-600
                                                    flex items-center justify-center
                                                    border border-teal-100">

                                                <span class="material-icons-round text-lg">
                                                    business
                                                </span>
                                            </div>

                                            <p class="text-sm font-black text-slate-700">
                                                {{ $monitor->internshipPlace->name ?? '-' }}
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-8 py-6 align-middle whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                rounded-xl text-xs font-bold border
                                                {{ $statusClass }}">

                                            <span class="material-icons-round text-sm">
                                                task_alt
                                            </span>

                                            {{ $monitor->status ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Foto --}}
                                    <td class="px-8 py-6 align-middle">
                                        <button type="button" onclick="showImage(@js($photoUrl))"
                                            class="relative block w-28 h-20 overflow-hidden
                                                rounded-2xl border border-slate-200
                                                bg-slate-50 group/photo">

                                            <img src="{{ $photoUrl }}" alt="Foto Monitoring"
                                                class="w-full h-full object-cover
                                                    group-hover/photo:scale-110
                                                    transition-transform duration-300">

                                            <span
                                                class="absolute inset-0 bg-slate-900/0
                                                    group-hover/photo:bg-slate-900/30
                                                    flex items-center justify-center
                                                    transition-all">

                                                <span
                                                    class="material-icons-round text-white
                                                        opacity-0 group-hover/photo:opacity-100
                                                        transition-opacity">

                                                    zoom_in
                                                </span>
                                            </span>
                                        </button>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-8 py-6 align-middle">
                                        <div class="flex justify-end">
                                            <form id="delete-form-{{ $monitor->id }}"
                                                action="{{ route('monitoring.destroy', $monitor->id) }}" method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="button" onclick="confirmDelete({{ $monitor->id }})"
                                                    class="w-10 h-10 flex items-center justify-center
                                                        rounded-xl bg-slate-50 text-slate-400
                                                        hover:bg-rose-50 hover:text-rose-600
                                                        transition-all"
                                                    title="Hapus">

                                                    <span class="material-icons-round text-base">
                                                        delete
                                                    </span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (method_exists($monitorings, 'hasPages') && $monitorings->hasPages())
                    <div class="px-8 py-6 border-t border-slate-50">
                        {{ $monitorings->withQueryString()->links() }}
                    </div>
                @endif
            @endif
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
            line-height: 1.5 !important;
            padding-left: 0 !important;
            padding-right: 25px !important;
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
            outline: none !important;
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
                width: '100%',
                language: {
                    noResults: function() {
                        return 'Tidak ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });
        });

        /**
         * Preview foto sebelum diunggah.
         */
        function previewImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');
            const fileName = document.getElementById('file-name');

            if (!file) {
                removePreview();
                return;
            }

            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Tidak Valid',
                    text: 'Silakan pilih file berupa gambar.'
                });

                removePreview();
                return;
            }

            fileName.textContent = file.name;

            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        }

        /**
         * Menghapus foto yang sudah dipilih.
         */
        function removePreview() {
            const fileInput = document.getElementById('monitor_photo');
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');
            const fileName = document.getElementById('file-name');

            fileInput.value = '';
            previewImage.src = '';
            fileName.textContent = 'Pilih foto monitoring';
            previewContainer.classList.add('hidden');
        }

        /**
         * Menampilkan foto ukuran besar.
         */
        function showImage(url) {
            Swal.fire({
                imageUrl: url,
                imageAlt: 'Foto Monitoring',
                showCloseButton: true,
                showConfirmButton: false,
                width: 'auto',
                background: '#ffffff',
                customClass: {
                    image: 'rounded-xl'
                }
            });
        }

        /**
         * Mengubah tampilan status lokasi.
         */
        function updateLocationStatus(type, title, message, icon) {
            const container = document.getElementById('location-status');
            const statusIcon = document.getElementById('location-status-icon');
            const statusTitle = document.getElementById('location-status-title');
            const statusText = document.getElementById('location-status-text');

            const styles = {
                success: [
                    'bg-teal-50',
                    'border-teal-100',
                    'text-teal-700'
                ],
                error: [
                    'bg-rose-50',
                    'border-rose-100',
                    'text-rose-700'
                ],
                loading: [
                    'bg-amber-50',
                    'border-amber-100',
                    'text-amber-700'
                ]
            };

            container.className =
                'mt-6 p-4 rounded-2xl border text-sm flex items-start gap-3 ' +
                styles[type].join(' ');

            statusIcon.textContent = icon;
            statusTitle.textContent = title;
            statusText.textContent = message;
        }

        /**
         * Mengambil lokasi perangkat.
         */
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('check_latitude');
            const lngInput = document.getElementById('check_longitude');
            const linkInput = document.getElementById('check_location_link');
            const submitButton = document.getElementById('submit-btn');

            if (!navigator.geolocation) {
                updateLocationStatus(
                    'error',
                    'Lokasi Tidak Didukung',
                    'Browser Anda tidak mendukung pengambilan lokasi otomatis.',
                    'location_off'
                );

                submitButton.disabled = true;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    latInput.value = latitude;
                    lngInput.value = longitude;
                    linkInput.value =
                        `https://www.google.com/maps?q=${latitude},${longitude}`;

                    submitButton.disabled = false;

                    updateLocationStatus(
                        'success',
                        'Lokasi Berhasil Diambil',
                        `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`,
                        'location_on'
                    );
                },
                function(error) {
                    let message = 'Harap izinkan akses lokasi untuk melanjutkan.';

                    if (error.code === error.TIMEOUT) {
                        message = 'Pengambilan lokasi terlalu lama. Silakan muat ulang halaman.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        message = 'Lokasi perangkat tidak dapat ditemukan.';
                    }

                    submitButton.disabled = true;

                    updateLocationStatus(
                        'error',
                        'Lokasi Diperlukan',
                        message,
                        'location_off'
                    );

                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Diperlukan',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        });

        /**
         * Menampilkan loading ketika form dikirim.
         */
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('monitoring-form');

            if (!form) {
                return;
            }

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const submitButton = document.getElementById('submit-btn');
                const buttonText = document.getElementById('btn-text');
                const buttonIcon = document.getElementById('btn-icon');
                const buttonSpinner = document.getElementById('btn-spinner');

                submitButton.disabled = true;
                buttonText.textContent = 'Mengunggah...';
                buttonIcon.classList.add('hidden');
                buttonSpinner.classList.remove('hidden');

                Swal.fire({
                    title: 'Mengunggah Foto',
                    html: 'Mohon tunggu, foto monitoring sedang dikirim ke server.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });

                setTimeout(function() {
                    form.submit();
                }, 500);
            });
        });

        /**
         * Konfirmasi hapus data monitoring.
         */
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Monitoring?',
                text: 'Data dan foto monitoring akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    const deleteForm = document.getElementById(`delete-form-${id}`);

                    if (deleteForm) {
                        deleteForm.submit();
                    }
                }
            });
        }
    </script>
@endpush
