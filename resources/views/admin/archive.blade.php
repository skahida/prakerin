@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            @if (auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-2">Cari Data Admin</h4>
                        <form method="GET">
                            <label>Admin:</label>
                            <div class="form-group">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="search" class="form-control form-control-lg selectpicker"
                                    placeholder="Search by Nama Admin">
                                    <option value="">Select Nama Admin</option>
                                    <!-- Dynamically populate options with student names -->
                                    @foreach ($adminAll as $admin)
                                    <option value="{{ $admin->name }}"
                                        {{ request()->input('search') == $admin->name ? 'selected' : '' }}>
                                        {{ $admin->name }}
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
                                    <a href="{{ route('admin.archive') }}"
                                        class="btn btn-md btn-primary btn-fill w-100">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header ">
                        <h4 class="card-title">Data Admin</h4>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($admins as $admin)
                                <tr>
                                    <td>
                                        <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                        {{ ($admins->currentPage() - 1) * $admins->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->user->username }}</td>
                                    <td>
                                        <a href="#"
                                            class="btn btn-success btn-fill mr-2 archive-active-btn"
                                            data-id="{{ $admin->user->id }}">
                                            <i class="fas fa-undo"></i> Aktifkan Kembali
                                        </a>
                                        </a>
                                        <a href="#" class="btn btn-danger btn-fill"
                                            data-id="{{ $admin->user->id }}"><i class="fas fa-remove"></i>
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            <!-- Link pagination dengan pencarian di URL -->
                            {{ $admins->appends(['search' => $search])->links('pagination::bootstrap-4') }}
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