@extends('layouts._app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tambah User Admin</h4>
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
                            <form action="{{ isset($admin) ? route('admin.update', $admin->id) : route('admin.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($admin))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', isset($admin) ? $admin->name : '') }}"
                                                placeholder="Masukan Nama Lengkap" autofocus>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control"
                                                value="{{ old('username', isset($admin) ? $admin->user->username : '') }}"
                                                placeholder="Masukan Username">
                                            @error('username')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            @if (isset($admin) && !empty($admin->user->password))
                                                <!-- Jika password ada, sembunyikan inputan dan tampilkan pesan -->
                                                <input type="hidden" name="password" value="{{ $admin->user->password }}">
                                                <div class="password-info">
                                                    <p>Password sudah ada (Tidak dapat diedit)</p>
                                                </div>
                                            @else
                                                <!-- Jika password kosong, tampilkan input password -->
                                                <input type="text" name="password" class="form-control"
                                                    value="{{ old('password') }}" placeholder="Masukan Password">
                                            @endif
                                            @error('password')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select name="role" id="role" class="form-control selectpicker">
                                                <option value="">Pilih Role</option>
                                                <option value="super-admin"
                                                    {{ old('role', isset($admin) ? $admin->user->role : '') == 'super-admin' ? 'selected' : '' }}>
                                                    Super Admin</option>
                                                <option value="admin"
                                                    {{ old('role', isset($admin) ? $admin->user->role : '') == 'admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                            </select>
                                            @error('role')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
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
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                            <h4 class="card-title">Data Admin</h4>
                            <p class="card-category">Here is a subtitle for this table</p>
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($admins as $admin)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->user->username }}</td>
                                            <td>
                                                <a href="{{ route('admin.edit', $admin->id) }}"
                                                    class="btn btn-warning btn-fill mr-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-danger btn-fill mr-2 archive-btn"
                                                    data-id="{{ $admin->user_id }}">
                                                    <i class="fas fa-archive"></i>
                                                </a>
                                                <a href="#" class="btn btn-secondary btn-fill reset-password-btn"
                                                    data-id="{{ $admin->user_id }}"><i class="fas fa-key"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
