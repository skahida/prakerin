@extends('layouts._app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'super-admin')
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-2">Cari Data Siswa</h4>
                                <form method="GET">
                                    <label>Siswa:</label>
                                    <div class="form-group">
                                        <!-- Select2 Dropdown for Searching Students -->
                                        <select name="search" class="form-control form-control-lg selectpicker"
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
                                        <select name="batch_search" class="form-control form-control-lg selectpicker"
                                            placeholder="Search by Gelombang">
                                            <option value="">Select Gelombang</option>
                                            <!-- Dynamically populate options with student names -->
                                            @foreach ($batches as $batch)
                                                <option value="{{ $batch->batch_name }}"
                                                    {{ request()->input('batch_search') == $batch->batch_name ? 'selected' : '' }}>
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
                                            <a href="{{ route('student.archive') }}"
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
                                                    <a href="#"
                                                        class="btn btn-success btn-fill mr-2 archive-active-btn"
                                                        data-id="{{ $student->user->id }}">
                                                        <i class="fas fa-undo"></i> Aktifkan Kembali
                                                    </a>
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
