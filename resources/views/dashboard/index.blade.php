@extends('layouts._app')

@section('content')
    <!-- End Navbar -->
    <div class="content">
        <div class="container-fluid">
            @if (auth()->user()->role === 'student')
                <div class="custom-menu">
                    <a href="#presensi" class="menu-item active">
                        <i class="fas fa-check-circle"></i>
                        <span>Presensi</span>
                        <span class="badge">{{ $presencesCount }}</span>
                    </a>
                    <a href="{{ route('presence') }}" class="menu-item">
                        <i class="fas fa-history"></i>
                        <span>Riwayat</span>
                        <span class="badge">{{ $presenceCount }}</span>
                    </a>
                    <a href="{{ route('report') }}" class="menu-item">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Laporan</span>
                        <span class="badge">{{ $reportsCount }}</span>
                    </a>
                </div>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card card-user">
                                    <div class="card-image" style="height: 170px;">
                                        <div id="map" style="width: 100%; height: 200px;"></div>
                                    </div>
                                    <div class="card-body">
                                        @if ($student)
                                            <div class="author">
                                                <a href="#">
                                                    <img class="avatar border-gray mx-auto"
                                                        src="{{ $student->user->foto_url
                                                            ? asset('storage/' . $student->user->foto_url)
                                                            : asset('../assets/img/faces/face-0.jpg') }}"
                                                        alt="Foto Profil">
                                                    <h3 class="title text-center">Hallo, {{ $student->name }}</h3>
                                                </a>


                                                <h4 class="title">
                                                    <i class="nc-icon nc-square-pin"></i>
                                                    {{ $student->internshipPlace->name ?? 'N/A' }}
                                                </h4>
                                                <h4 class="title">
                                                    <i class="nc-icon nc-atom"></i>
                                                    {{ $student->class->name ?? 'N/A' }}
                                                </h4>
                                                <h4 class="title">
                                                    <i class="nc-icon nc-circle-09"></i>
                                                    {{ $student->mentor->name ?? 'N/A' }} <!-- Mengambil mentor pertama -->
                                                </h4>
                                            </div>
                                            <h4 class="title text-center">
                                                Semangat Prakerin ya!
                                            </h4>
                                        @else
                                            <div class="alert alert-danger">
                                                Data siswa dengan NIS tersebut tidak ditemukan.
                                            </div>
                                        @endif
                                    </div>
                                    <hr>
                                    @if ($student->internshipBatch->status_batch === 'active')
                                        <div class="button-container mr-auto ml-auto mt-1 mb-2">
                                            <!-- Tombol Presensi Masuk -->
                                            <button type="button" id="checkInPresence" class="btn btn-success btn-fill"
                                                data-checked-in="{{ $hasCheckedIn ? 'true' : 'false' }}">
                                                <i class="nc-icon nc-check-2"></i> Presensi Masuk
                                            </button>

                                            <!-- Tombol Presensi Pulang -->
                                            <button type="button" id="checkOutPresence" class="btn btn-success btn-fill"
                                                data-checked-out="{{ $hasCheckedOut ? 'true' : 'false' }}">
                                                <i class="nc-icon nc-check-2"></i> Presensi Pulang
                                            </button>
                                        </div>
                                    @elseif ($student->internshipBatch->status_batch === 'non-active')
                                        <div class="button-container mr-auto ml-auto mt-1 mb-2">
                                            <h4 class="title text-center">
                                                <div class="alert alert-danger">
                                                    <span>⚠️ Peringatan: {{ $student->internshipBatch->batch_name }}
                                                        presensi telah
                                                        selesai atau tidak aktif. Terima
                                                        kasih atas perhatian Anda!</span>
                                                </div>
                                            </h4>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card strpied-tabled-with-hover">
                                    <div class="card-header ">
                                        <h4 class="card-title">Riwayat Presensi Hari Ini</h4>
                                    </div>
                                    <div class="card-body table-full-width  table-responsive">

                                        {{-- @if ($noPresence)
                                    <div class="alert alert-danger alert-with-icon" data-notify="container">
                                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                            <i class="nc-icon nc-simple-remove"></i>
                                        </button>
                                        <span data-notify="icon" class="nc-icon nc-bell-55"></span>
                                        <span data-notify="message">Belum Presensi Masuk!</span>
                                    </div>
                                @else --}}
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <th>ID</th>
                                                <th>Hari</th>
                                                <th>Tanggal</th>
                                                <th>Masuk</th>
                                                <th>Pulang</th>
                                                <th>Lokasi Masuk</th>
                                                <th>Lokasi Pulang</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($presences as $presence)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <!-- Kolom Hari -->
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($presence->check_in)->locale('id')->isoFormat('dddd') ?? 'Belum Presensi Masuk' }}
                                                        </td>
                                                        <td>
                                                            {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y') : 'Belum Presensi Masuk' }}
                                                        </td>
                                                        <td>
                                                            {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('H:i:s') : 'Belum Presensi Masuk' }}
                                                        </td>
                                                        <td>
                                                            {{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') : '-' }}
                                                        </td>

                                                        <!-- Menampilkan peta jika check_in_location_link ada -->
                                                        <td>
                                                            @if ($presence->check_in_location_link)
                                                                <iframe width="35" height="100" frameborder="0"
                                                                    style="border:0"
                                                                    src="{{ $presence->check_in_location_link }}&output=embed"
                                                                    allowfullscreen>
                                                                </iframe>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>

                                                        <!-- Menampilkan peta untuk check_out_location_link -->
                                                        <td>
                                                            @if ($presence->check_out_location_link)
                                                                <iframe width="35" height="100" frameborder="0"
                                                                    style="border:0"
                                                                    src="{{ $presence->check_out_location_link }}&output=embed"
                                                                    allowfullscreen>
                                                                </iframe>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $presence->status == 'present' ? 'Masuk' : ($presence->status == 'premission' ? 'Izin' : ($presence->status == 'sick' ? 'Sakit' : 'Alpa')) }}
                                                        </td>
                                                        <td>{{ $presence->note ? $presence->note : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                {{-- @endif --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (auth()->user()->role == 'super-admin')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Grafik Presensi</h4>
                            </div>
                            <div class="card-body">
                                <!-- Membungkus canvas dengan div yang memiliki class untuk responsivitas -->
                                <div class="chart-container">

                                    <canvas id="attendanceChart"></canvas>
                                    <br>

                                    <label>Siswa:</label>
                                    <div class="form-group">
                                        <!-- Add a select dropdown for filtering student names -->
                                        <select id="studentFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Siswa</option>
                                            <!-- Student options will be populated here -->
                                        </select>
                                    </div>
                                    <label>Kelas:</label>
                                    <div class="form-group">
                                        <!-- Dropdown untuk memilih nama batch -->
                                        <select id="classFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Kelas</option>
                                            <!-- Opsi batch akan diisi oleh data yang diterima dari backend -->
                                        </select>
                                    </div>
                                    <label>Gelombang:</label>
                                    <div class="form-group">
                                        <!-- Dropdown untuk memilih nama batch -->
                                        <select id="batchFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Gelombang</option>
                                            <!-- Opsi batch akan diisi oleh data yang diterima dari backend -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Bulan Mulai:</label>
                                        <input type="month" id="startMonthFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Bulan Akhir (opsional):</label>
                                        <input type="month" id="endMonthFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Mulai:</label>
                                        <input type="date" id="startDateFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Akhir (opsional):</label>
                                        <input type="date" id="endDateFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>

                                    <div class="input-group-append mr-2 mb-2 w-100 w-md-auto" id="printButton"
                                        style="display: none;">
                                        <a id="printButtonLink" href="#" class="btn btn-secondary btn-fill w-100">
                                            <i class="nc-icon nc-cloud-download-93"></i> Cetak
                                        </a>
                                    </div>
                                    <div class="table-responsive mt-4">
                                        <!-- Subtitle Menampilkan Bulan -->
                                        <div class="subtitle"
                                            style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">
                                            <span id="batchName"></span><br>
                                            <span id="yearResult"></span>
                                        </div>

                                        <table id="filteredTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">NO</th>
                                                    <th rowspan="2">NAMA</th>
                                                    <th rowspan="2">KELAS</th>
                                                    <th rowspan="2">DUDI</th>
                                                    <th rowspan="2">PEMBIMBING</th>
                                                    <th rowspan="2" style="background-color:#856404;color:white;">HARI
                                                        EFEKTIF</th>
                                                    <th rowspan="2" style="background-color:#28a745;color:white;">MASUK
                                                    </th>
                                                    <th colspan="5" style="background-color:#dc3343;color:white;">TIDAK
                                                        MASUK</th>
                                                    <th rowspan="2">KETERANGAN</th>
                                                    <th rowspan="2">AKSI</th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color:#ffc007;color:white;">S</th>
                                                    <th style="background-color:#14a2b8;color:white;">I</th>
                                                    <th style="background-color:#dc3444;color:white;">A</th>
                                                    <th style="background-color:#ba5fea;color:white;">L</th>
                                                    <th style="background-color:#858e96;color:white;">LAINNYA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data akan diisi via JavaScript -->
                                            </tbody>
                                        </table>
                                        <!-- Data akan diisi via JavaScript -->
                                        <div id="paginationControls" style="text-align:center; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Modal Detail --}}
                    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog"> <!-- Ubah jadi xl kalau datanya panjang -->
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title mb-0" id="detailModalLabel">
                                        <i class="fas fa-user-graduate mr-2"></i> Detail Kehadiran Harian
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table id="presenceTable"
                                            class="table table-hover table-striped text-center align-middle">
                                            <thead>
                                                <tr>
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
                                                </tr>
                                            </thead>
                                            <tbody id="modalPresenceTable">
                                                <!-- Akan diisi oleh JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Laporan Kegiatan</h4>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $reportsCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('report') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Siswa</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $studentCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('student') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Guru Pembimbing</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $mentorCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('mentor') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                    {{-- <button type="submit" class="btn btn-success btn-fill pull-right">Lihat</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">DUDI</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $dudiCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('dudi') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                    {{-- <button type="submit" class="btn btn-success btn-fill pull-right">Lihat</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Gelombang Aktif</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $batchCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('batch') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                    {{-- <button type="submit" class="btn btn-success btn-fill pull-right">Lihat</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Admin</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $adminCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('admin') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                    {{-- <button type="submit" class="btn btn-success btn-fill pull-right">Lihat</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (auth()->user()->role == 'admin')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Grafik Presensi</h4>
                            </div>
                            <div class="card-body">
                                <!-- Membungkus canvas dengan div yang memiliki class untuk responsivitas -->
                                <div class="chart-container">
                                    <canvas id="attendanceChart"></canvas>
                                    <br>

                                    <label>Siswa:</label>
                                    <div class="form-group">
                                        <!-- Add a select dropdown for filtering student names -->
                                        <select id="studentFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Siswa</option>
                                            <!-- Student options will be populated here -->
                                        </select>
                                    </div>
                                    <label>Gelombang:</label>
                                    <div class="form-group">
                                        <!-- Dropdown untuk memilih nama batch -->
                                        <select id="batchFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Gelombang</option>
                                            <!-- Opsi batch akan diisi oleh data yang diterima dari backend -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Bulan Mulai:</label>
                                        <input type="month" id="startMonthFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Bulan Akhir (opsional):</label>
                                        <input type="month" id="endMonthFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Mulai:</label>
                                        <input type="date" id="startDateFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Akhir (opsional):</label>
                                        <input type="date" id="endDateFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>

                                    <div class="input-group-append mr-2 mb-2 w-100 w-md-auto" id="printButton"
                                        style="display: none;">
                                        <a id="printButtonLink" href="#" class="btn btn-secondary btn-fill w-100">
                                            <i class="nc-icon nc-cloud-download-93"></i> Cetak
                                        </a>
                                    </div>
                                    <div class="table-responsive mt-4">
                                        <!-- Subtitle Menampilkan Bulan -->
                                        <div class="subtitle"
                                            style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">
                                            <span id="batchName"></span><br>
                                            <span id="yearResult"></span>
                                        </div>
                                        <table id="filteredTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">NO</th>
                                                    <th rowspan="2">NAMA</th>
                                                    <th rowspan="2">KELAS</th>
                                                    <th rowspan="2">DUDI</th>
                                                    <th rowspan="2">PEMBIMBING</th>
                                                    <th rowspan="2" style="background-color:#856404;color:white;">HARI
                                                        EFEKTIF</th>
                                                    <th rowspan="2" style="background-color:#28a745;color:white;">MASUK
                                                    </th>
                                                    <th colspan="4" style="background-color:#dc3343;color:white;">TIDAK
                                                        MASUK</th>
                                                    <th rowspan="2">KETERANGAN</th>
                                                    <th rowspan="2">AKSI</th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color:#ffc007;color:white;">S</th>
                                                    <th style="background-color:#14a2b8;color:white;">I</th>
                                                    <th style="background-color:#dc3444;color:white;">A</th>
                                                    <th style="background-color:#858e96;color:white;">LAINNYA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data akan diisi via JavaScript -->
                                            </tbody>
                                        </table>
                                        <!-- Data akan diisi via JavaScript -->
                                        <div id="paginationControls" style="text-align:center; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Custom Modal -->
                    <div id="customModal" class="custom-modal">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <h5><i class="fas fa-user-graduate"></i> Detail Kehadiran Harian</h5>
                                <span class="custom-close" onclick="closeModal()">&times;</span>
                            </div>
                            <div class="custom-modal-body">
                                <div class="table-responsive">
                                    <table class="table-custom">
                                        <thead>
                                            <tr>
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
                                            </tr>
                                        </thead>
                                        <tbody id="modalPresenceTable">
                                            <!-- Diisi pakai JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Laporan Kegiatan</h4>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $reportsCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('report') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Siswa</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $studentCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('student') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (auth()->user()->role == 'mentor')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Grafik Presensi</h4>
                            </div>
                            <div class="card-body">
                                <!-- Membungkus canvas dengan div yang memiliki class untuk responsivitas -->
                                <div class="chart-container">
                                    <canvas id="attendanceChart"></canvas>
                                    <br>

                                    <label>Siswa:</label>
                                    <div class="form-group">
                                        <!-- Add a select dropdown for filtering student names -->
                                        <select id="studentFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Siswa</option>
                                            <!-- Student options will be populated here -->
                                        </select>
                                    </div>
                                    <label>Kelas:</label>
                                    <div class="form-group">
                                        <!-- Dropdown untuk memilih nama batch -->
                                        <select id="classFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Kelas</option>
                                            <!-- Opsi batch akan diisi oleh data yang diterima dari backend -->
                                        </select>
                                    </div>
                                    <label>Gelombang:</label>
                                    <div class="form-group">
                                        <!-- Dropdown untuk memilih nama batch -->
                                        <select id="batchFilter" class="form-control form-control-lg selectpicker"
                                            data-live-search="true">
                                            <option value="">Semua Gelombang</option>
                                            <!-- Opsi batch akan diisi oleh data yang diterima dari backend -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Bulan Mulai:</label>
                                        <input type="month" id="startMonthFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Bulan Akhir (opsional):</label>
                                        <input type="month" id="endMonthFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Mulai:</label>
                                        <input type="date" id="startDateFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Akhir (opsional):</label>
                                        <input type="date" id="endDateFilter"
                                            class="form-control form-control-lg selectpicker" data-live-search="true">
                                    </div>

                                    {{-- <div class="input-group-append mr-2 mb-2 w-100 w-md-auto" id="printButton"
                                style="display: none;">
                                <a id="printButtonLink" href="#" class="btn btn-secondary btn-fill w-100">
                                    <i class="nc-icon nc-cloud-download-93"></i> Cetak
                                </a>
                            </div> --}}
                                    <div class="table-responsive mt-4">
                                        <!-- Subtitle Menampilkan Bulan -->
                                        <div class="subtitle"
                                            style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">
                                            <span id="batchName"></span><br>
                                            <span id="yearResult"></span>
                                        </div>
                                        <table id="filteredTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">NO</th>
                                                    <th rowspan="2">NAMA</th>
                                                    <th rowspan="2">KELAS</th>
                                                    <th rowspan="2">DUDI</th>
                                                    <th rowspan="2">PEMBIMBING</th>
                                                    <th rowspan="2" style="background-color:#856404;color:white;">HARI
                                                        EFEKTIF</th>
                                                    <th rowspan="2" style="background-color:#28a745;color:white;">MASUK
                                                    </th>
                                                    <th colspan="5" style="background-color:#dc3343;color:white;">TIDAK
                                                        MASUK</th>
                                                    <th rowspan="2">KETERANGAN</th>
                                                    <th rowspan="2">AKSI</th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color:#ffc007;color:white;">S</th>
                                                    <th style="background-color:#14a2b8;color:white;">I</th>
                                                    <th style="background-color:#dc3444;color:white;">A</th>
                                                    <th style="background-color:#ba5fea;color:white;">L</th>
                                                    <th style="background-color:#858e96;color:white;">LAINNYA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data akan diisi via JavaScript -->
                                            </tbody>
                                        </table>
                                        <!-- Data akan diisi via JavaScript -->
                                        <div id="paginationControls" style="text-align:center; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Modal Detail --}}
                    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog"> <!-- Ubah jadi xl kalau datanya panjang -->
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title mb-0" id="detailModalLabel">
                                        <i class="fas fa-user-graduate mr-2"></i> Detail Kehadiran Harian
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table id="presenceTable"
                                            class="table table-hover table-striped text-center align-middle">
                                            <thead>
                                                <tr>
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
                                                </tr>
                                            </thead>
                                            <tbody id="modalPresenceTable">
                                                <!-- Akan diisi oleh JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-user">
                            <div class="card-image" style="height: 170px;">
                                <div class="cover"></div>
                            </div>
                            <div class="card-body">
                                <div class="author">
                                    <a href="#">
                                        <!-- Misalnya gambar avatar ada di field user -->
                                        <img class="avatar border-gray" src="../assets/img/faces/face-0.jpg"
                                            alt="...">
                                        <h3 class="title">Hallo, {{ $mentor->name }}</h3>
                                    </a>
                                </div>
                                @if ($mentor->telegram_number)
                                    <h4 class="title text-center">
                                        <div class="alert alert-primary">
                                            <span>✅ Notifikasi: Chat ID Telegram Anda telah terdaftar dan siap menerima
                                                pemberitahuan terkait presensi siswa. Anda akan mendapatkan update otomatis
                                                ketika presensi sudah aktif. Terima kasih!</span>
                                        </div>
                                    </h4>
                                    <h4 class="title text-center">
                                        <p class="card-category">❗ Jika Anda tidak menerima notifikasi, coba interaksi
                                            dengan
                                            <a href="{{ 'https://t.me/PrakerinTracerBot' }}">bot Telegram</a> dengan ketik
                                            perintah /start untuk memastikan
                                            koneksi aktif.
                                        </p>
                                    </h4>
                                @else
                                    <h4 class="title text-center">
                                        <div class="alert alert-danger">
                                            <span>❗ Notifikasi: Chat ID Telegram Anda belum terdaftar dan tidak akan
                                                menerima pemberitahuan terkait presensi siswa.</span>
                                            <a href="{{ 'https://t.me/PrakerinTracerBot' }}">bot Telegram</a> dengan ketik
                                            perintah /start untuk memastikan
                                            koneksi aktif.
                                        </div>
                                    </h4>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Presensi Siswa Hari Ini</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $presencesCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('presence') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Riwayat Presensi</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $presenceCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('history.presence') }}"
                                        class="btn btn-success btn-fill pull-right"><i class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                                                        <div class="card ">
                                                            <div class="card-header ">
                                                                <h4 class="card-title">Laporan Kegiatan</h4>
                                                                <p class="card-category"></p>
                                                            </div>
                                                            <div class="card-body text-center">
                                                                <span>
                                                                    <h3><b>{{ $reportsCount }}</b></h3>
                                                                </span>
                                                                <hr>
                                                                <div class="stats">
                                                                    <a href="{{ route('report') }}" class="btn btn-success btn-fill pull-right"><i
                                                                            class="fas fa-eye"></i> Lihat</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->
                    <div class="col-md-6">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">Siswa</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body text-center">
                                <span>
                                    <h3><b>{{ $studentsCount }}</b></h3>
                                </span>
                                <hr>
                                <div class="stats">
                                    <a href="{{ route('student') }}" class="btn btn-success btn-fill pull-right"><i
                                            class="fas fa-eye"></i> Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
