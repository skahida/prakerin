@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Guru Pembimbing</h4>
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
                            action="{{ isset($mentor) ? route('mentor.update', $mentor->id) : route('mentor.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($mentor))
                            @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', isset($mentor) ? $mentor->name : '') }}"
                                            placeholder="Masukan Nama Lengkap" autofocus>
                                        @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select name="gender" id="gender" class="form-control selectpicker">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L"
                                                {{ old('gender', isset($mentor) ? $mentor->gender : '') == 'L' ? 'selected' : '' }}>
                                                Laki-Laki</option>
                                            <option value="P"
                                                {{ old('gender', isset($mentor) ? $mentor->gender : '') == 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                        @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor WhatsApp</label>
                                        <input type="text" name="whatsapp_number" class="form-control"
                                            value="{{ old('whatsapp_number', isset($mentor) ? $mentor->whatsapp_number : '') }}"
                                            placeholder="Masukan Nomor WhatsApp">
                                        @error('whatsapp_number')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Telegram</label>
                                        <input type="text" name="telegram_number" class="form-control"
                                            value="{{ old('telegram_number', isset($mentor) ? $mentor->telegram_number : '') }}"
                                            placeholder="Masukan Nomor Telegram">
                                        @error('telegram_number')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control"
                                            value="{{ old('username', isset($mentor) ? $mentor->user->username : '') }}"
                                            placeholder="Masukan Username">
                                        @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        @if (isset($mentor) && !empty($mentor->user->password))
                                        <!-- Jika password ada, sembunyikan inputan dan tampilkan pesan -->
                                        <input type="hidden" name="password"
                                            value="{{ $mentor->user->password }}">
                                        <div class="password-info">
                                            <p>Password sudah ada (Tidak dapat diedit)</p>
                                        </div>
                                        @else
                                        <label>Password</label>
                                        <!-- Jika password kosong, tampilkan input password -->
                                        <input type="text" name="password" class="form-control"
                                            value="{{ old('password', isset($mentor) ? 'prakerintracer' : 'prakerintracer') }}"
                                            placeholder="Masukan Password" readonly>
                                        @endif
                                        @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-fill">
                                <i class="fa {{ isset($mentor) ? 'fa-edit' : 'fa-save' }}"></i>
                                {{ isset($mentor) ? 'Ubah' : 'Simpan' }}
                            </button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-3">Cari Data Guru Pembimbing</h4>
                        <form method="GET">
                            <label>Pembimbing:</label>
                            <div class="form-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by Nama Guru Pembimbing" value="{{ $search }}">
                            </div>
                            <div class="d-flex flex-wrap mb-3">
                                <!-- d-flex and flex-wrap to handle buttons layout -->
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <!-- Ensure button takes full width on mobile -->
                                    <button class="btn btn-md btn-info btn-fill w-100" type="submit">
                                        <i class="nc-icon nc-zoom-split"></i> Search
                                    </button>
                                </div>
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <!-- Ensure button takes full width on mobile -->
                                    <a href="{{ route('student') }}" class="btn btn-md btn-primary btn-fill w-100">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                                <!-- Print Button: Pass the search and batch_search to the print route -->
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <a href="{{ route('print.student') }}?search={{ request()->input('search') }}&batch_search={{ request()->input('batch_search') }}"
                                        class="btn btn-secondary btn-fill w-100">
                                        <i class="nc-icon nc-cloud-download-93"></i> Cetak
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header ">
                        <h4 class="card-title">Data Guru Pembimbing</h4>
                        <p class="card-category">Here is a subtitle for this table</p>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>ID</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Jenis Kelamin</th>
                                <th>Nomor WhatsApp</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($mentors as $mentor)
                                <tr>
                                    <td>
                                        <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                        {{ ($mentors->currentPage() - 1) * $mentors->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $mentor->name }}</td>
                                    <td>{{ $mentor->user->username }}</td>
                                    <td>{{ $mentor->gender }}</td>
                                    <td>{{ $mentor->whatsapp_number }}</td>
                                    <td>
                                        <a href="{{ route('mentor.edit', $mentor->id) }}"
                                            class="btn btn-warning btn-fill mr-2">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-danger btn-fill mr-2 archive-btn"
                                            data-id="{{ $mentor->user_id }}">
                                            <i class="fas fa-archive"></i>
                                        </a>
                                        <a href="#" class="btn btn-secondary btn-fill reset-password-btn"
                                            data-id="{{ $mentor->user_id }}"><i class="fas fa-key"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            <!-- Link pagination dengan pencarian di URL -->
                            {{ $mentors->links('pagination::bootstrap-4') }}
                            {{-- {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection