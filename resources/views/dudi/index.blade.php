@extends('layouts._app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Dudi</h4>
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
                            <form action="{{ isset($dudi) ? route('dudi.update', $dudi->code) : route('dudi.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($dudi))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Nama Dudi -->
                                        <div class="form-group">
                                            <label>Nama Dudi</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', isset($dudi) ? $dudi->name : '') }}"
                                                placeholder="Masukan Dudi" autofocus>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <!-- Kode Dudi -->
                                        <div class="form-group">
                                            <label>Kode Dudi</label>
                                            <input type="text" name="code" class="form-control"
                                                value="{{ old('code', isset($dudi) ? $dudi->code : '') }}"
                                                placeholder="Masukan Kode Dudi" {{ isset($dudi) ? 'readonly' : '' }}>
                                            @error('code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <!-- Alamat -->
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <input type="text" name="address" class="form-control"
                                                value="{{ old('address', isset($dudi) ? $dudi->address : '') }}"
                                                placeholder="Masukan Alamat">
                                            @error('address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <!-- Bidang -->
                                        <div class="form-group">
                                            <label>Bidang</label>
                                            <input type="text" name="field" class="form-control"
                                                value="{{ old('field', isset($dudi) ? $dudi->field : '') }}"
                                                placeholder="Masukan Bidang">
                                            @error('field')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <!-- Nomor WhatsApp -->
                                        <div class="form-group">
                                            <label>Nomor WhatsApp</label>
                                            <input type="text" name="contact_number" class="form-control"
                                                value="{{ old('contact_number', isset($dudi) ? $dudi->contact_number : '') }}"
                                                placeholder="Masukan Nomor WhatsApp">
                                            @error('contact_number')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <!-- Latitude -->
                                        <div class="form-group">
                                            <label>Latitude</label>
                                            <input type="text" name="latitude" class="form-control"
                                                value="{{ old('latitude', isset($dudi) ? $dudi->latitude : '') }}"
                                                placeholder="Masukan Latitude">
                                            @error('latitude')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <!-- Longitude -->
                                        <div class="form-group">
                                            <label>Longitude</label>
                                            <input type="text" name="longitude" class="form-control"
                                                value="{{ old('longitude', isset($dudi) ? $dudi->longitude : '') }}"
                                                placeholder="Masukan Longitude">
                                            @error('longitude')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Tambahkan field address, name, field, contact_number sesuai kebutuhan -->
                                <button type="submit" class="btn btn-info btn-fill">
                                    <i class="fa {{ isset($dudi) ? 'fa-edit' : 'fa-save' }}"></i>
                                    {{ isset($dudi) ? 'Ubah' : 'Simpan' }}
                                </button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-3">Cari Data Dudi</h4>
                            <form method="GET">
                                <label>Dudi:</label>
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by Nama Dudi" value="{{ $search }}">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <button class="btn btn-md btn-info btn-fill" type="submit">
                                            <i class="nc-icon nc-zoom-split"></i> Search
                                        </button>
                                    </div>
                                    <div class="input-group-append ml-2"> <!-- Added margin-left (ml-2) to create space -->
                                        <a href="{{ route('dudi') }}" class="btn btn-md btn-primary btn-fill">
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
                            <h4 class="card-title">Data Dudi</h4>
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>ID</th>
                                    <th>Kode Dudi</th>
                                    <th>Dudi</th>
                                    <th>Bidang</th>
                                    <th>Nomor Whatsapp</th>
                                    <th>Maps</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($dudies as $dudi)
                                        <tr>
                                            <td>
                                                <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                                {{ ($dudies->currentPage() - 1) * $dudies->perPage() + $loop->iteration }}
                                            </td>
                                            <td>{{ $dudi->code }}</td>
                                            <td>{{ $dudi->name }}</td>
                                            <td>{{ $dudi->field }}</td>
                                            <td>{{ $dudi->contact_number }}</td>
                                            <td>
                                                @if ($dudi->latitude)
                                                    <iframe width="100" height="100" frameborder="0"
                                                        style="border:0"
                                                        src="{{ $check_location_link . $dudi->latitude . ',' . $dudi->longitude }}&output=embed"
                                                        allowfullscreen>
                                                    </iframe>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('dudi.edit', $dudi->code) }}"
                                                    class="btn btn-warning btn-fill mr-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Pagination links with Bootstrap 4 styling -->
                            <div class="pagination justify-content-center">
                                <!-- Link pagination dengan pencarian di URL -->
                                {{ $dudies->links('pagination::bootstrap-4') }}
                                {{-- {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
