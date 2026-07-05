@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            @if (auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Presensi</h4>
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
                        <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name') }}" placeholder="Masukan Nama Lengkap">
                                        @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control"
                                            value="{{ old('username') }}" placeholder="Masukan Username">
                                        @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="text" name="password" class="form-control"
                                            value="{{ old('password') }}" placeholder="Masukan Password">
                                        @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-fill">Simpan</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            @elseif (auth()->user()->role == 'mentor')
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header ">
                        <h4 class="card-title">Presensi Siswa Hari Ini</h4>
                        <p class="card-category">Here is a subtitle for this table</p>
                    </div>
                    <div class="card-body table-responsive">
                        @if ($presences->isEmpty())
                        <p>Tidak ada data presensi yang ditemukan untuk mentor ini.</p>
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
                                    <td>{{ $presence->student->name }}</td>
                                    <td>{{ $presence->student->class->name }}</td>
                                    <td>{{ $presence->student->internshipPlace->name }}</td>
                                    <td>{{ $presence->student->internshipBatch->batch_name }}</td>
                                    <td>{{ $presence->student->internshipBatch->academic_year }}</td>
                                    <!-- Kolom Hari -->
                                    <td>{{ \Carbon\Carbon::parse($presence->check_in)->locale('id')->isoFormat('dddd') ?? 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'Belum Presensi Masuk' }}
                                    </td>
                                    <td>
                                        {{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : '-' }}
                                    </td>

                                    <!-- Menampilkan peta jika check_in_location_link ada -->
                                    <td>
                                        @if ($presence->check_in_location_link)
                                        <iframe width="200" height="100" frameborder="0"
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
                                        <iframe width="200" height="100" frameborder="0"
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
                        @endif
                    </div>
                </div>
            </div>
            @elseif (auth()->user()->role == 'student')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ">
                        <h4 class="card-title">Cari Data Presensi</h4>
                    </div>
                    <!-- <p class="card-category">Here is a subtitle for this table</p> -->
                    <div class="card-body">
                        <form method="GET">
                            <div class="form-group">
                                <input type="date" name="search" class="form-control selectpicker"
                                    placeholder="Search by Tanggal" value="{{ request()->input('search') }}">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <button class="btn btn-md btn-info btn-fill" type="submit">
                                        <i class="nc-icon nc-zoom-split"></i> Search
                                    </button>
                                </div>
                                <div class="input-group-append ml-2">
                                    <!-- Added margin-left (ml-2) to create space -->
                                    <a href="{{ route('presence') }}" class="btn btn-md btn-primary btn-fill">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header ">
                        <h4 class="card-title">Data Riwayat Presensi</h4>
                    </div>
                    <div class="card-body table-full-width table-responsive">
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
                                    <td>{{ ($presences->currentPage() - 1) * $presences->perPage() + $loop->iteration }}</td>
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
                                        <iframe width="50" height="100" frameborder="0"
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
                                        <iframe width="50" height="100" frameborder="0"
                                            style="border:0"
                                            src="{{ $presence->check_out_location_link }}&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                       {{ $presence->status == 'present' ? 'Masuk' 
                                            : ($presence->status == 'premission' ? 'Izin' 
                                            : ($presence->status == 'sick' ? 'Sakit' 
                                            : ($presence->status == 'holiday' ? 'Libur' 
                                            : 'Alpa'))) }}

                                    </td>
                                    <td>{{ $presence->note ? $presence->note : '-' }}</td>
                                </tr>
                                @endforeach
                                {{-- @endif --}}
                            </tbody>
                        </table>

                        <div class="pagination justify-content-center">
                            {{ $presences->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection