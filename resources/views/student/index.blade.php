@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            @if (auth()->user()->role == 'mentor')
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header ">
                        <h4 class="card-title">Data Siswa Prakerin</h4>
                        <p class="card-category">Here is a subtitle for this table</p>
                        <form method="GET" action="{{ route('student') }}">
                            <div class="input-group mb-3">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by Nama Siswa" value="{{ request()->input('search') }}">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <button class="btn btn-md btn-info btn-fill" type="submit">
                                        <i class="nc-icon nc-zoom-split"></i> Search
                                    </button>
                                </div>
                                <div class="input-group-append ml-2">
                                    <!-- Added margin-left (ml-2) to create space -->
                                    <a href="{{ route('student') }}" class="btn btn-md btn-primary btn-fill">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>ID</th>
                                <th>Nama Lengkap</th>
                                <th>Jenis Kelamin</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Dudi</th>
                                <th>Nomor WhatsApp</th>
                                <th>Nomor Telegram</th>
                                <th>Gelombang</th>
                                <th>Tahun Pelajaran</th>
                                <th>Mulai Prakerin</th>
                                <th>Selesai Prakerin</th>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->gender }}</td>
                                    <td>{{ $student->class->name }}</td>
                                    <td>{{ $student->department->name }}</td>
                                    <td>{{ $student->internshipPlace->name }}</td>
                                    <td>{{ $student->whatsapp_number }}</td>
                                    <td>{{ $student->telegram_number }}</td>
                                    <td>{{ $student->internshipBatch->batch_name }}</td>
                                    <td>{{ $student->internshipBatch->academic_year }}</td>
                                    <td>{{ \Carbon\Carbon::parse($student->internshipBatch->start_date)->locale('id')->format('d F Y') }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($student->internshipBatch->end_date)->locale('id')->format('d F Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
            @elseif (auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Siswa Prakerin</h4>
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
                            action="{{ isset($student) ? route('student.update', $student->id) : route('student.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($student))
                            @method('PUT') <!-- Metode PUT untuk update -->
                            @endif

                            <div class="row">
                                <div class="col-12">
                                    <!-- Nama Lengkap -->
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control" data-live-search="true"
                                            value="{{ old('name', isset($student) ? $student->name : '') }}"
                                            placeholder="Masukan Nama Lengkap" autofocus>
                                        @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- NIS -->
                                    <div class="form-group">
                                        <label>NIS</label>
                                        <input type="text" name="nis" class="form-control"
                                            value="{{ old('nis', isset($student) ? $student->nis : '') }}"
                                            placeholder="Masukan NIS">
                                        @error('nis')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Jenis Kelamin -->
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select name="gender" id="gender" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L"
                                                {{ old('gender', isset($student) ? $student->gender : '') == 'L' ? 'selected' : '' }}>
                                                Laki-Laki</option>
                                            <option value="P"
                                                {{ old('gender', isset($student) ? $student->gender : '') == 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                        @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Kelas -->
                                    <div class="form-group">
                                        <label>Kelas</label>
                                        <select name="class_code" id="class_code" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($classes as $class)
                                            <option value="{{ $class->code }}"
                                                {{ old('class_code', isset($student) ? $student->class_code : '') == $class->code ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('class_code')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Jurusan -->
                                    <div class="form-group">
                                        <label>Jurusan</label>
                                        <select name="department_code" id="department_code"
                                            class="form-control selectpicker" data-live-search="true">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach ($departments as $department)
                                            <option value="{{ $department->code }}"
                                                {{ old('department_code', isset($student) ? $student->department_code : '') == $department->code ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('department_code')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Dudi -->
                                    <div class="form-group">
                                        <label>Dudi</label>
                                        <select name="internship_place_code" id="internship_place_code"
                                            class="form-control selectpicker" data-live-search="true">
                                            <option value="">Pilih Dudi</option>
                                            @foreach ($dudies as $dudi)
                                            <option value="{{ $dudi->code }}"
                                                {{ old('internship_place_code', isset($student) ? $student->internship_place_code : '') == $dudi->code ? 'selected' : '' }}>
                                                {{ $dudi->name . ' - ' . $dudi->code }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('internship_place_code')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Gelombang -->
                                    <div class="form-group">
                                        <label>Gelombang</label>
                                        <select name="internship_batch_id" id="internship_batch_id"
                                            class="form-control selectpicker" data-live-search="true">
                                            <option value="">Pilih Gelombang</option>
                                            @foreach ($batches as $batch)
                                            <option value="{{ $batch->id }}"
                                                {{ old('internship_batch_id', isset($student) ? $student->internship_batch_id : '') == $batch->id ? 'selected' : '' }}>
                                                {{ $batch->batch_name . ' - ' . $batch->academic_year }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('internship_batch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Pembimbing -->
                                    <div class="form-group">
                                        <label>Pembimbing</label>
                                        <select name="mentor_id" id="mentor_id" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Pilih Pembimbing</option>
                                            @foreach ($mentors as $mentor)
                                            <option value="{{ $mentor->id }}"
                                                {{ old('mentor_id', isset($student) ? $student->mentor_id : '') == $mentor->id ? 'selected' : '' }}>
                                                {{ $mentor->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('mentor_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Nomor WhatsApp -->
                                    <div class="form-group">
                                        <label>Nomor WhatsApp</label>
                                        <input type="text" name="whatsapp_number" class="form-control"
                                            value="{{ old('whatsapp_number', isset($student) ? $student->whatsapp_number : '') }}"
                                            placeholder="Masukan Nomor WhatsApp">
                                        @error('whatsapp_number')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Nomor Telegram -->
                                    <div class="form-group">
                                        <label>Nomor Telegram</label>
                                        <input type="text" name="telegram_number" class="form-control"
                                            value="{{ old('telegram_number', isset($student) ? $student->telegram_number : '') }}"
                                            placeholder="Masukan Nomor Telegram">
                                        @error('telegram_number')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Username -->
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control"
                                            value="{{ old('username', isset($student) ? $student->user->username : '') }}"
                                            placeholder="Masukan Username">
                                        @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group">
                                        <label>Password</label>
                                        @if (isset($student) && !empty($student->user->password))
                                        <!-- Jika password ada, sembunyikan inputan dan tampilkan pesan -->
                                        <div class="password-info">
                                            <p>Password sudah ada (tidak dapat diedit)</p>
                                        </div>
                                        @else
                                        <!-- Jika password kosong, tampilkan input password -->
                                        <input type="text" name="password" class="form-control"
                                            value="{{ old('password', isset($student) ? 'skahida' : 'skahida') }}"
                                            placeholder="Masukan Password" readonly>
                                        @endif
                                        @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Foto Profil -->
                                    <div class="form-group">
                                        <label>Foto Profil</label>
                                        <input type="file" name="foto_url" class="form-control"> <br>
                                        @if (isset($student) && !empty($student->user->foto_url))
                                        <img src="{{ asset('storage/' . optional($student->user)->foto_url) }}"
                                            alt="Foto Profil" width="120" class="mb-2"><br>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <button type="submit" class="btn btn-info btn-fill">
                                <i class="fa {{ isset($student) ? 'fa-edit' : 'fa-save' }}"></i>
                                {{ isset($student) ? 'Ubah' : 'Simpan' }}
                            </button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-2">Cari Data Siswa</h4>
                        <form method="GET">
                            <label>Siswa:</label>
                            <div class="form-group">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="search" class="form-control form-control-lg selectpicker" data-live-search="true"
                                    placeholder="Search by Nama Siswa">
                                    <option value="">Select Nama Siswa</option>
                                    <!-- Dynamically populate options with student names -->
                                    @foreach ($studentAll as $student)
                                    <option value="{{ $student->name }}"
                                        {{ request()->input('search') == $student->name ? 'selected' : '' }}>
                                        {{ $student->name . ' | ' . $student->class_code }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <label>Gelombang:</label>
                            <div class="form-group">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="batch_search" class="form-control form-control-lg selectpicker" data-live-search="true"
                                    placeholder="Search by Gelombang">
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
                                    <a href="{{ route('student') }}"
                                        class="btn btn-md btn-primary btn-fill w-100">
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
                        <h4 class="card-title">Data Siswa Prakerin</h4>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>No.</th>
                                <th>Nama Lengkap</th>
                                <th>Jenis Kelamin</th>
                                <th>Kelas</th>
                                <th>Dudi</th>
                                <th>Username/NIS</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                <tr>
                                    <td>
                                        <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                        {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->gender }}</td>
                                    <td>{{ $student->class->name }}</td>
                                    <td>{{ $student->internshipPlace->name }}</td>
                                    <td>{{ $student->user->username }}</td>
                                    <td>
                                        <a href="{{ route('student.edit', $student->id) }}"
                                            class="btn btn-warning btn-fill mr-2">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-danger btn-fill mr-2 archive-btn"
                                            data-id="{{ $student->user->id }}">
                                            <i class="fas fa-archive"></i>
                                        </a>
                                        <a href="#"
                                            class="btn btn-secondary btn-fill reset-password-btn"
                                            data-id="{{ $student->user->id }}"><i class="fas fa-key"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            <!-- Link pagination dengan pencarian di URL -->
                            {{ $students->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                            {{-- {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection