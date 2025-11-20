@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            @if (auth()->user()->role == 'mentor')
            {{-- Search Form --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ isset($studentEdit) ? $title : 'Tambah Presensi' }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Pesan sukses -->
                        @if (session('success'))
                        <div class="alert alert-primary">
                            <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                <i class="nc-icon nc-simple-remove"></i>
                            </button>
                            <span>
                                <b> Sukses - </b> {{ session('success') }}
                        </div>
                        @endif

                        <!-- Pesan error untuk seluruh form -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form
                            action="{{ isset($studentEdit) ? route('presence.update', $studentEdit->id) : route('presence.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($studentEdit))
                            @method('PUT') <!-- Metode PUT untuk update -->
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <select name="search" id="student_select"
                                            class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Select Nama Siswa</option>
                                            @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ old('search', isset($studentEdit) ? $studentEdit->student_id : '') == $student->id ? 'selected' : '' }}>
                                                {{ $student->name . ' | ' . $student->class_code }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('search')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="datetime-local" name="check_in" class="form-control"
                                            value="{{ old('check_in', isset($studentEdit) ? $studentEdit->check_in : '') }}">
                                        @error('check_in')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <!-- <div class="form-group">
                                        <label>Presensi Pulang</label>
                                        <input type="datetime-local" name="check_out" class="form-control"
                                            value="{{ old('check_out', isset($studentEdit) ? $studentEdit->check_out : '') }}">
                                        @error('check_out')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div> -->
                                    <div class="form-group">
                                        <label>Koordinat Latitude</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control"
                                            value="{{ old('latitude', isset($studentEdit) ? $studentEdit->check_in_latitude : '') }}"
                                            placeholder="Masukkan Latitude" readonly>
                                        @error('latitude')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Koordinat Longitude</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control"
                                            value="{{ old('longitude', isset($studentEdit) ? $studentEdit->check_in_longitude : '') }}"
                                            placeholder="Masukkan Longitude" readonly>
                                        @error('longitude')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" id="status" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Pilih Status</option>
                                            <option value="present"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'present' ? 'selected' : '' }}>
                                                Masuk</option>
                                            <option value="absent"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'absent' ? 'selected' : '' }}>
                                                Alpa</option>
                                            <option value="sick"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'sick' ? 'selected' : '' }}>
                                                Sakit</option>
                                            <option value="permission"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'permission' ? 'selected' : '' }}>
                                                Izin</option>
                                            <option value="holiday"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'holiday' ? 'selected' : '' }}>
                                                Libur</option>
                                        </select>
                                        @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <input type="text" name="note" id="note" class="form-control"
                                            value="{{ old('note', isset($studentEdit) ? $studentEdit->note : '') }}"
                                            placeholder="Masukkan Keterangan">
                                        @error('note')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-fill">
                                <i class="fa {{ isset($studentEdit) ? 'fa-edit' : 'fa-save' }}"></i>
                                {{ isset($studentEdit) ? 'Ubah' : 'Simpan' }}
                            </button>
                            <div class="clearfix"></div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <form method="GET">
                            <div class="form-group">
                                <label>Pilih Siswa</label>
                                <select name="student_id" class="form-control selectpicker" data-live-search="true">
                                    <option value="">Semua Siswa</option>
                                    @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ request()->input('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name . ' | ' . $student->class_code }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Awal</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request()->input('start_date') }}">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request()->input('end_date') }}">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <button class="btn btn-md btn-info btn-fill" type="submit">
                                        <i class="nc-icon nc-zoom-split"></i> Search
                                    </button>
                                </div>
                                <div class="input-group-append ml-2">
                                    <!-- Added margin-left (ml-2) to create space -->
                                    <a href="{{ route('history.presence') }}"
                                        class="btn btn-md btn-primary btn-fill">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                            </div>
                        </form>
                        <h4 class="card-title">Riwayat Presensi</h4>
                    </div>
                    <div class="card-body table-responsive">
                        @if ($historyPresences->isEmpty())
                        <p>Tidak ada data presensi siswa yang ditemukan untuk guru pembimbing ini.</p>
                        @else
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>ID</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Dudi</th>
                                <th>Gelombang</th>
                                <th>Tahun Pelajaran</th>
                                <th>Hari</th>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Lokasi Masuk</th>
                                <th>Lokasi Pulang</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($historyPresences as $historyPresence)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $historyPresence->student->name }}</td>
                                    <td>{{ $historyPresence->student->class->name }}</td>
                                    <td>{{ $historyPresence->student->internshipPlace->name }}</td>
                                    <td>{{ $historyPresence->student->internshipBatch->batch_name }}</td>
                                    <td>{{ $historyPresence->student->internshipBatch->academic_year }}
                                    </td>
                                    <!-- Kolom Hari -->
                                    <td>{{ \Carbon\Carbon::parse($historyPresence->created_at)->locale('id')->isoFormat('dddd') ?? 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $historyPresence->created_at ? \Carbon\Carbon::parse($historyPresence->created_at)->timezone('Asia/Jakarta')->format('d-m-Y') : 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $historyPresence->created_at ? \Carbon\Carbon::parse($historyPresence->created_at)->timezone('Asia/Jakarta')->format('H:i:s') : 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $historyPresence->check_out ? \Carbon\Carbon::parse($historyPresence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') : '-' }}
                                    </td>

                                    <!-- Menampilkan peta jika check_in_location_link ada -->
                                    <td>
                                        @if ($historyPresence->check_in_location_link)
                                        <iframe width="100" height="100" frameborder="0"
                                            style="border:0"
                                            src="{{ $historyPresence->check_in_location_link }}&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <!-- Menampilkan peta untuk check_out_location_link -->
                                    <td>
                                        @if ($historyPresence->check_out_location_link)
                                        <iframe width="100" height="100" frameborder="0"
                                            style="border:0"
                                            src="{{ $historyPresence->check_out_location_link }}&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $historyPresence->status == 'present' ? 'Masuk' : ($historyPresence->status == 'permission' ? 'Izin' : ($historyPresence->status == 'sick' ? 'Sakit' : 'Alpa')) }}
                                    </td>
                                    <td>{{ $historyPresence->note ? $historyPresence->note : '-' }}</td>
                                    <td>
                                    <td>
                                        <a href="{{ route('historyPresence.edit', $historyPresence->id) }}"
                                            class="btn btn-warning btn-fill mr-2">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus dengan Swal -->
                                        <button type="button" class="btn btn-danger btn-fill delete-btn" data-id="{{ $historyPresence->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            {{ $historyPresences->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @elseif (auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ isset($studentEdit) ? $title : 'Tambah Presensi' }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Pesan sukses -->
                        @if (session('success'))
                        <div class="alert alert-primary">
                            <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                <i class="nc-icon nc-simple-remove"></i>
                            </button>
                            <span>
                                <b> Sukses - </b> {{ session('success') }}
                        </div>
                        @endif

                        <!-- Pesan error untuk seluruh form -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form
                            action="{{ isset($studentEdit) ? route('presence.update', $studentEdit->id) : route('presence.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($studentEdit))
                            @method('PUT') <!-- Metode PUT untuk update -->
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <select name="search" id="student_select"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                            <option value="">Select Nama Siswa</option>
                                            @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ old('search', isset($studentEdit) ? $studentEdit->student_id : '') == $student->id ? 'selected' : '' }}>
                                                {{ $student->name . ' | ' . $student->class_code }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('search')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Presensi Masuk</label>
                                        <input type="datetime-local" name="check_in" class="form-control"
                                            value="{{ old('check_in', isset($studentEdit) ? $studentEdit->check_in : '') }}">
                                        @error('check_in')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Presensi Pulang</label>
                                        <input type="datetime-local" name="check_out" class="form-control"
                                            value="{{ old('check_out', isset($studentEdit) ? $studentEdit->check_out : '') }}">
                                        @error('check_out')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Koordinat Latitude</label>
                                        <input type="text" name="latitude" id="latitude"
                                            class="form-control"
                                            value="{{ old('latitude', isset($studentEdit) ? $studentEdit->check_in_latitude : '') }}"
                                            placeholder="Masukkan Latitude">
                                        @error('latitude')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Koordinat Longitude</label>
                                        <input type="text" name="longitude" id="longitude"
                                            class="form-control"
                                            value="{{ old('longitude', isset($studentEdit) ? $studentEdit->check_in_longitude : '') }}"
                                            placeholder="Masukkan Longitude">
                                        @error('longitude')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" id="status" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Pilih Status</option>
                                            <option value="present"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'present' ? 'selected' : '' }}>
                                                Masuk</option>
                                            <option value="absent"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'absent' ? 'selected' : '' }}>
                                                Alpa</option>
                                            <option value="sick"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'sick' ? 'selected' : '' }}>
                                                Sakit</option>
                                            <option value="permission"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'permission' ? 'selected' : '' }}>
                                                Izin</option>
                                            <option value="holiday"
                                                {{ old('status', isset($studentEdit) ? $studentEdit->status : '') == 'holiday' ? 'selected' : '' }}>
                                                Libur</option>
                                        </select>
                                        @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <input type="text" name="note" id="note" class="form-control"
                                            value="{{ old('note', isset($studentEdit) ? $studentEdit->note : '') }}"
                                            placeholder="Masukkan Keterangan">
                                        @error('note')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-fill">
                                <i class="fa {{ isset($studentEdit) ? 'fa-edit' : 'fa-save' }}"></i>
                                {{ isset($studentEdit) ? 'Ubah' : 'Simpan' }}
                            </button>
                            <div class="clearfix"></div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cari Data Presensi</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <label>Siswa:</label>
                            <div class="input-group mb-3">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="search" class="form-control form-control-lg selectpicker"
                                    placeholder="Search by Nama Siswa" data-live-search="true">
                                    <option value="">Select Nama Siswa</option>
                                    <!-- Dynamically populate options with student names -->
                                    @foreach ($students as $student)
                                    <option value="{{ $student->name }}"
                                        {{ request()->input('search') == $student->name ? 'selected' : '' }}>
                                        {{ $student->name . ' | ' . $student->class_code }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <label>Gelombang:</label>
                            <div class="input-group mb-3">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="batch_search" class="form-control form-control-lg selectpicker"
                                    placeholder="Search by Gelombang" data-live-search="true">
                                    <option value="">Select Gelombang</option>
                                    <!-- Dynamically populate options with student names -->
                                    @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}"
                                        {{ request()->input('batch_search') == $batch->id ? 'selected' : '' }}>
                                        {{ $batch->batch_name . ' | ' . $batch->academic_year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Bulan Mulai:</label>
                                <input type="month" name="start_month"
                                    value="{{ request()->input('start_month') }}"
                                    class="form-control form-control-lg selectpicker">
                            </div>
                            <div class="form-group">
                                <label>Bulan Akhir (opsional):</label>
                                <input type="month" name="end_month"
                                    value="{{ request()->input('end_month') }}"
                                    class="form-control form-control-lg selectpicker">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Awal</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request()->input('start_date') }}">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request()->input('end_date') }}">
                            </div>
                            <div class="d-flex flex-wrap mb-3">
                                <!-- d-flex and flex-wrap to handle buttons layout -->
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <button class="btn btn-md btn-info btn-fill" type="submit">
                                        <i class="nc-icon nc-zoom-split"></i> Search
                                    </button>
                                </div>
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <a href="{{ route('history.presence') }}"
                                        class="btn btn-md btn-primary btn-fill">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                                <!-- @if (request()->filled('start_month'))
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <a href="{{ route('print.presence') }}?search={{ request()->input('search') }}&batch_search={{ request()->input('batch_search') }}&start_month={{ request()->input('start_month') }}&end_month={{ request()->input('end_month') }}"
                                        class="btn btn-secondary btn-fill w-100">
                                        <i class="nc-icon nc-cloud-download-93"></i> Cetak
                                    </a>
                                </div>
                                @endif -->
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <a href="{{ route('print.presence', [
                                            'search'       => request()->input('search'),
                                            'batch_search' => request()->input('batch_search'),
                                            'start_month'  => request()->input('start_month'),
                                            'end_month'    => request()->input('end_month'),
                                            'start_date'   => request()->input('start_date'),
                                            'end_date'     => request()->input('end_date'),
                                        ]) }}"
                                        class="btn btn-secondary btn-fill w-100">
                                        <i class="nc-icon nc-cloud-download-93"></i> Cetak
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <h4 class="card-title">Riwayat Presensi Siswa</h4>
                    </div>
                    <div class="card-body table-responsive">
                        @if ($historyPresences->isEmpty())
                        <p>Tidak ada data presensi siswa yang ditemukan.</p>
                        @else
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>ID</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Dudi</th>
                                <th>Gelombang</th>
                                <th>Tahun Pelajaran</th>
                                <th>Hari</th>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Lokasi Masuk</th>
                                <th>Lokasi Pulang</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($historyPresences as $historyPresence)
                                <tr>
                                    <td>
                                        <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                        {{ ($historyPresences->currentPage() - 1) * $historyPresences->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $historyPresence->student->name }}</td>
                                    <td>{{ $historyPresence->student->class->name }}</td>
                                    <td>{{ $historyPresence->student->internshipPlace->name }}</td>
                                    <td>{{ $historyPresence->student->internshipBatch->batch_name }}</td>
                                    <td>{{ $historyPresence->student->internshipBatch->academic_year }}
                                    </td>
                                    <!-- Kolom Hari -->
                                    <td>{{ \Carbon\Carbon::parse($historyPresence->check_in)->locale('id')->isoFormat('dddd') ?? 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $historyPresence->created_at ? \Carbon\Carbon::parse($historyPresence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y') : 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $historyPresence->created_at ? \Carbon\Carbon::parse($historyPresence->check_in)->timezone('Asia/Jakarta')->format('H:i:s') : 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $historyPresence->check_out ? \Carbon\Carbon::parse($historyPresence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') : '-' }}
                                    </td>

                                    <!-- Menampilkan peta jika check_in_location_link ada -->
                                    <td>
                                        @if ($historyPresence->check_in_location_link)
                                        <iframe width="100" height="100" frameborder="0"
                                            style="border:0"
                                            src="{{ $historyPresence->check_in_location_link }}&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <!-- Menampilkan peta untuk check_out_location_link -->
                                    <td>
                                        @if ($historyPresence->check_out_location_link)
                                        <iframe width="100" height="100" frameborder="0"
                                            style="border:0"
                                            src="{{ $historyPresence->check_out_location_link }}&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $historyPresence->status == 'present' ? 'Masuk' : ($historyPresence->status == 'permission' ? 'Izin' : ($historyPresence->status == 'sick' ? 'Sakit' : 'Alpa')) }}
                                    </td>
                                    <td>{{ $historyPresence->note ? $historyPresence->note : '-' }}</td>
                                    <td>
                                        <a href="{{ route('historyPresence.edit', $historyPresence->id) }}"
                                            class="btn btn-warning btn-fill mr-2">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus dengan Swal -->
                                        <button type="button" class="btn btn-danger btn-fill delete-btn" data-id="{{ $historyPresence->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                {{-- @endif --}}
                            </tbody>
                        </table>
                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            <!-- Link pagination dengan pencarian di URL -->
                            {{ $historyPresences->links('pagination::bootstrap-4') }}
                            {{-- {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection