@extends('layouts._app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Jurusan</h4>
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
                                action="{{ isset($department) ? route('department.update', $department->code) : route('department.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($department))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Kode Jurusan</label>
                                            <input type="text" name="code" class="form-control"
                                                value="{{ old('code', isset($department) ? $department->code : '') }}"
                                                placeholder="Masukan Kode Jurusan" autofocus
                                                {{ isset($department) ? 'readonly' : '' }}>
                                            @error('code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Jurusan</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', isset($department) ? $department->name : '') }}"
                                                placeholder="Masukan Nama Jurusan">
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info btn-fill">
                                    <i class="fa {{ isset($department) ? 'fa-edit' : 'fa-save' }}"></i>
                                    {{ isset($department) ? 'Ubah' : 'Simpan' }}
                                </button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-3">Cari Data Jurusan</h4>
                            <form method="GET">
                                <label>Jurusan:</label>
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by Nama Jurusan" value="{{ $search }}">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <button class="btn btn-md btn-info btn-fill" type="submit">
                                            <i class="nc-icon nc-zoom-split"></i> Search
                                        </button>
                                    </div>
                                    <div class="input-group-append ml-2"> <!-- Added margin-left (ml-2) to create space -->
                                        <a href="{{ route('department') }}" class="btn btn-md btn-primary btn-fill">
                                            <i class="nc-icon nc-refresh-02"></i> Reload
                                        </a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                            <h4 class="card-title">Data Jurusan</h4>
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>ID</th>
                                    <th>Kode Jurusan</th>
                                    <th>Nama Jurusan</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($departments as $department)
                                        <tr>
                                            <td>
                                                <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                                {{ ($departments->currentPage() - 1) * $departments->perPage() + $loop->iteration }}
                                            </td>
                                            <td>{{ $department->code }}</td>
                                            <td>{{ $department->name }}</td>
                                            <td>
                                                <a href="{{ route('department.edit', $department->code) }}"
                                                    class="btn btn-warning btn-fill mr-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-danger btn-fill mr-2 archive-btn"
                                                    data-id="{{ $department->code }}">
                                                    <i class="fas fa-archive"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Pagination links with Bootstrap 4 styling -->
                            <div class="pagination justify-content-center">
                                <!-- Link pagination dengan pencarian di URL -->
                                {{ $departments->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                                {{-- {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
