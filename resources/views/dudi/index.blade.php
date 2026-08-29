@extends('layouts._app')

@section('title', 'Data DUDI — Tera Prakerin')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-600 text-white p-3 rounded-2xl shadow-xl shadow-teal-200">
                        <span class="material-icons-round block">business</span>
                    </span>
                    DUDI
                </h2>
                <p class="text-slate-500 font-medium mt-2 uppercase text-xs tracking-[0.2em]">
                    Manajemen Data Dunia Usaha & Dunia Industri
                </p>
            </div>
        </div>

        {{-- ===================== FORM ===================== --}}
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">
                        {{ isset($dudi) ? 'Edit DUDI' : 'Tambah DUDI Baru' }}
                    </h3>
                    <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                        {{ isset($dudi) ? 'Perbarui data DUDI' : 'Isi data DUDI dengan lengkap' }}
                    </p>
                </div>
                @if (isset($dudi))
                    <a href="{{ route('dudi') }}"
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

                <form action="{{ isset($dudi) ? route('dudi.update', $dudi->code) : route('dudi.store') }}" method="POST">
                    @csrf
                    @if (isset($dudi))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama DUDI</label>
                            <input type="text" name="name"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('name', $dudi->name ?? '') }}" placeholder="Nama tempat magang" autofocus>
                            @error('name')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode DUDI</label>
                            <input type="text" name="code"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3 {{ isset($dudi) ? 'bg-slate-100 cursor-not-allowed opacity-70' : '' }}"
                                value="{{ old('code', $dudi->code ?? '') }}" placeholder="Contoh: DUDI-001"
                                {{ isset($dudi) ? 'readonly' : '' }}>
                            @error('code')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bidang</label>
                            <input type="text" name="field"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('field', $dudi->field ?? '') }}" placeholder="Contoh: Teknologi Informasi">
                            @error('field')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat</label>
                            <input type="text" name="address"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('address', $dudi->address ?? '') }}" placeholder="Alamat lengkap">
                            @error('address')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor
                                WhatsApp</label>
                            <input type="text" name="contact_number"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('contact_number', $dudi->contact_number ?? '') }}"
                                placeholder="08xxxxxxxxxx">
                            @error('contact_number')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cari Lokasi --}}
                        <div class="space-y-1.5 md:col-span-2 lg:col-span-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cari Lokasi
                                (Maps)</label>
                            <div class="relative">
                                <span
                                    class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">place</span>
                                <input type="text" id="placeSearch"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pl-12 pr-4 py-3"
                                    placeholder="Ketik nama tempat, contoh: Kantor Kecamatan Bae" autocomplete="off">
                                <div id="placeResults"
                                    class="absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden hidden max-h-64 overflow-y-auto">
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Ketik nama tempat lalu pilih dari daftar. Koordinat
                                akan terisi otomatis.</p>
                        </div>

                        {{-- Latitude & Longitude (tetap bisa diedit manual) --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Latitude</label>
                            <input type="text" name="latitude" id="latitude"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('latitude', $dudi->latitude ?? '') }}" placeholder="-6.xxxxxx">
                            @error('latitude')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Longitude</label>
                            <input type="text" name="longitude" id="longitude"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 px-4 py-3"
                                value="{{ old('longitude', $dudi->longitude ?? '') }}" placeholder="110.xxxxxx">
                            @error('longitude')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Preview Map (opsional) --}}
                        <div id="mapPreview" class="md:col-span-2 lg:col-span-3 hidden">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Preview
                                Lokasi</label>
                            <iframe id="mapFrame" class="w-full h-48 rounded-2xl border border-slate-200" frameborder="0"
                                allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-xl shadow-teal-200 active:scale-[0.98]">
                            <span class="material-icons-round text-lg">
                                {{ isset($dudi) ? 'edit' : 'save' }}
                            </span>
                            {{ isset($dudi) ? 'Perbarui Data' : 'Simpan DUDI' }}
                        </button>

                        @if (isset($dudi))
                            <a href="{{ route('dudi') }}"
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
                            placeholder="Cari nama DUDI..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-teal-200">
                            <span class="material-icons-round text-base">search</span>
                            Cari
                        </button>
                        <a href="{{ route('dudi') }}"
                            class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2">
                            <span class="material-icons-round text-base">refresh</span>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden">

                @if ($dudies->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">
                                        No</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        DUDI</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Bidang</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Kontak</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Lokasi</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($dudies as $i => $item)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-5">
                                            <span
                                                class="text-sm font-black text-slate-300 group-hover:text-teal-500 transition-colors">
                                                {{ sprintf('%02d', ($dudies->currentPage() - 1) * $dudies->perPage() + $loop->iteration) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-sm border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-all">
                                                    {{ substr($item->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-black text-slate-800 text-sm group-hover:text-teal-600 transition-colors">
                                                        {{ $item->name }}
                                                    </div>
                                                    <div class="flex items-center gap-1 mt-0.5 text-slate-400">
                                                        <span class="material-icons-round text-xs">tag</span>
                                                        <span class="text-[10px] font-bold uppercase tracking-tighter">
                                                            {{ $item->code }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 text-sm text-slate-600">
                                            {{ $item->field ?? '-' }}
                                        </td>

                                        <td class="px-6 py-5 text-sm text-slate-600">
                                            {{ $item->contact_number ?? '-' }}
                                        </td>

                                        <td class="px-6 py-5">
                                            @if ($item->latitude && $item->longitude)
                                                <iframe width="100" height="70"
                                                    class="rounded-xl border border-slate-200"
                                                    src="{{ ($check_location_link ?? 'https://maps.google.com/maps?q=') . $item->latitude . ',' . $item->longitude }}&output=embed"
                                                    allowfullscreen>
                                                </iframe>
                                            @else
                                                <span class="text-slate-300 text-xs">—</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex justify-end">
                                                <a href="{{ route('dudi.edit', $item->code) }}"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all"
                                                    title="Edit">
                                                    <span class="material-icons-round text-base">edit</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($dudies->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100">
                            {{ $dudies->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-24">
                        <div
                            class="w-24 h-24 bg-slate-50 text-slate-200 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
                            <span class="material-icons-round text-6xl">business</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Database Kosong</h3>
                        <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2">
                            Belum ada data DUDI yang terdaftar dalam sistem.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const searchInput = document.getElementById('placeSearch');
                const resultsBox = document.getElementById('placeResults');
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const mapPreview = document.getElementById('mapPreview');
                const mapFrame = document.getElementById('mapFrame');

                let debounceTimer = null;

                searchInput?.addEventListener('input', function() {
                    const q = this.value.trim();
                    clearTimeout(debounceTimer);

                    if (q.length < 3) {
                        resultsBox.classList.add('hidden');
                        resultsBox.innerHTML = '';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        // Nominatim OpenStreetMap (gratis)
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=6&addressdetails=1&countrycodes=id`, {
                                headers: {
                                    'Accept': 'application/json',
                                    // Nominatim minta User-Agent yang jelas
                                    'User-Agent': 'TeraPrakerin/1.0 (contact@example.com)'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (!data || data.length === 0) {
                                    resultsBox.innerHTML =
                                        `<div class="px-4 py-3 text-sm text-slate-400">Tidak ditemukan</div>`;
                                    resultsBox.classList.remove('hidden');
                                    return;
                                }

                                resultsBox.innerHTML = data.map(item => `
                    <button type="button"
                        class="w-full text-left px-4 py-3 hover:bg-teal-50 transition-colors border-b border-slate-50 last:border-0 place-item"
                        data-lat="${item.lat}"
                        data-lon="${item.lon}"
                        data-name="${item.display_name.replace(/"/g, '&quot;')}">
                        <div class="text-sm font-semibold text-slate-800 truncate">${item.display_name.split(',')[0]}</div>
                        <div class="text-xs text-slate-400 truncate mt-0.5">${item.display_name}</div>
                    </button>
                `).join('');

                                resultsBox.classList.remove('hidden');

                                // Klik hasil
                                resultsBox.querySelectorAll('.place-item').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const lat = this.dataset.lat;
                                        const lon = this.dataset.lon;
                                        const name = this.dataset.name;

                                        latInput.value = parseFloat(lat).toFixed(7);
                                        lngInput.value = parseFloat(lon).toFixed(7);
                                        searchInput.value = name.split(',')[0];

                                        // Preview map
                                        mapFrame.src =
                                            `https://maps.google.com/maps?q=${lat},${lon}&z=16&output=embed`;
                                        mapPreview.classList.remove('hidden');

                                        resultsBox.classList.add('hidden');
                                    });
                                });
                            })
                            .catch(err => {
                                console.error(err);
                                resultsBox.innerHTML =
                                    `<div class="px-4 py-3 text-sm text-rose-500">Gagal mencari lokasi</div>`;
                                resultsBox.classList.remove('hidden');
                            });
                    }, 400); // debounce 400ms
                });

                // Tutup dropdown kalau klik di luar
                document.addEventListener('click', function(e) {
                    if (!searchInput?.contains(e.target) && !resultsBox?.contains(e.target)) {
                        resultsBox?.classList.add('hidden');
                    }
                });

                // Kalau lat/lng sudah ada (mode edit), tampilkan preview
                if (latInput?.value && lngInput?.value) {
                    mapFrame.src = `https://maps.google.com/maps?q=${latInput.value},${lngInput.value}&z=16&output=embed`;
                    mapPreview.classList.remove('hidden');
                }
            })();
        </script>
    @endpush
@endsection
