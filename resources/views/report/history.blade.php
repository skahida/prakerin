@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-3">Cari Data Laporan</h4>
                        <form method="GET">
                            <label>Siswa:</label>
                            <div class="form-group">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="search" class="form-control form-control-lg selectpicker"
                                    data-live-search="true" placeholder="Search by Nama Siswa">
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
                            <div class="form-group">
                                <!-- Select2 Dropdown for Searching Students -->
                                <select name="batch_search" class="form-control form-control-lg selectpicker"
                                    data-live-search="true" placeholder="Search by Gelombang">
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
                            <div class="d-flex flex-column flex-md-row gap-2 mb-3">
                                <!-- d-flex and flex-wrap to handle buttons layout -->
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <!-- Ensure button takes full width on mobile -->
                                    <button class="btn btn-md btn-info btn-fill w-100" type="submit">
                                        <i class="nc-icon nc-zoom-split"></i> Search
                                    </button>
                                </div>
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <!-- Ensure button takes full width on mobile -->
                                    <a href="{{ route('report') }}" class="btn btn-md btn-success btn-fill w-100">
                                        <i class="nc-icon nc-refresh-02"></i> Reload
                                    </a>
                                </div>
                                @if (session('ses_role') == 'admin' || session('ses_role') == 'super-admin')
                                <div class="d-flex flex-column flex-md-row gap-2">
                                    <!-- Tombol Cetak Rekap -->
                                    <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                        <a href="{{ route('rekap.grade') }}?search={{ request('search') }}&batch_search={{ request('batch_search') }}"
                                            class="btn btn-md btn-primary btn-fill w-100">
                                            <i class="fas fa-file-pdf"></i> Cetak Rekap
                                        </a>
                                    </div>

                                    <!-- Tombol Export Excel -->
                                    <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                        <a href="{{ route('export.rekap') }}?search={{ request('search') }}&batch_search={{ request('batch_search') }}"
                                            class="btn btn-md btn-success btn-fill w-100">
                                            <i class="fas fa-file-excel"></i> Export Excel
                                        </a>
                                    </div>
                                </div>
                                @elseif (session('ses_role') == 'mentor')
                                <div class="input-group-append mr-2 mb-2 w-100 w-md-auto">
                                    <!-- Ensure button takes full width on mobile -->
                                    <a href="{{ route('rekap.grade') }}?search={{ request('search') }}&batch_search={{ request('batch_search') }}"
                                        class="btn btn-md btn-primary btn-fill w-100">
                                        <i class="fas fa-print"></i> Cetak Rekap
                                    </a>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <h4 class="card-title">Laporan Mingguan</h4>
                        <p class="card-category">Upload laporan untuk setiap minggu Praktek Kerja Industri (Senin -
                            Sabtu)</p>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>No.</th>
                                <th>Aksi</th>
                                <th>Nama</th>
                                <th>Dudi</th>
                                <th>Progress Upload Laporan</th>
                                <th>Total Nilai</th>
                                @if (session('ses_role') === 'admin' || session('ses_role') === 'super-admin')
                                <th>Guru Pembimbing</th>
                                @endif
                            </thead>
                            <tbody>
                                @foreach ($distinctReportsPaginated as $report)
                                <tr>
                                    <td>
                                        <!-- Menyesuaikan nomor urut berdasarkan pencarian dan pagination -->
                                        {{ ($distinctReportsPaginated->currentPage() - 1) * $distinctReportsPaginated->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <!-- Cek apakah grades sudah ada untuk siswa -->
                                        @if ($report->grades->isEmpty())
                                        <!-- Jika belum ada nilai, tampilkan tombol 'Nilai Laporan' -->
                                        <a class="btn btn-primary btn-fill"
                                            href="{{ route('grade', ['studentId' => $report->student_id]) }}">
                                            <i class="fas fa-pen"></i> Nilai Laporan
                                        </a>
                                        @else
                                        <!-- Jika sudah ada nilai, tampilkan tombol 'Edit Laporan' -->
                                        <a class="btn btn-warning btn-fill"
                                            href="{{ route('grade', ['studentId' => $report->student_id]) }}">
                                            <i class="fas fa-pen"></i> Edit Nilai Laporan
                                        </a>
                                        @endif

                                    </td>
                                    <td>{{ $report->student->name }}</td>
                                    <td>{{ $report->dudi_name }}</td>
                                    <td>
                                        <!-- Progress Bar -->
                                        <div class="progress">
                                            <div class="progress-bar {{ $report->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}"
                                                role="progressbar"
                                                style="width:  {{ round($report->progress_percentage) }}%"
                                                aria-valuenow=" {{ round($report->progress_percentage) }}%"
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ round($report->progress_percentage) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($report->grades->isEmpty())
                                        <span class="badge badge-danger">Belum Diberikan</span>
                                        @else
                                        <!-- Menampilkan rata-rata grade untuk laporan -->
                                        <span
                                            class="badge badge-success">{{ $report->average_grade ?? 'N/A' }}</span>
                                        @endif
                                    </td>
                                    @if (session('ses_role') === 'admin' || session('ses_role') === 'super-admin')
                                    <td>{{ $report->mentor_name }}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination links with Bootstrap 4 styling -->
                        <div class="pagination justify-content-center">
                            <!-- Link pagination dengan pencarian di URL -->
                            {{ $distinctReportsPaginated->links('pagination::bootstrap-4') }}
                            {{-- {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection