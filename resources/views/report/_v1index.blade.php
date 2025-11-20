@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <h4 class="card-title">Laporan Mingguan</h4>
                        <p class="card-category">Upload laporan untuk setiap minggu Praktek Kerja Industri (Senin - Sabtu)</p>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>No.</th>
                                <th>Aksi</th>
                                <th>Minggu</th>
                                <th>Status</th>
                                <th>Link Sosmed <span style="color:red;">*</span></th>
                            </thead>
                            <tbody>
                                @foreach ($weeks as $week)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($week['report'] && $week['report']->report_status == 'Sudah Upload')
                                        <!-- Tombol Edit jika sudah upload -->
                                        <button class="btn btn-warning btn-fill"
                                            onclick="showEditModal({{ $week['minggu'] }}, '{{ $week['db_title'] }}')">
                                            <i class="fas fa-pencil-alt"></i> Edit Laporan
                                        </button>
                                        @else
                                        <!-- Tombol Upload -->
                                        <button class="btn btn-primary btn-fill"
                                            @if (in_array($week['minggu'], $disabledWeeks)) disabled @endif
                                            onclick="showUploadModal({{ $week['minggu'] }}, '{{ $week['db_title'] }}')">
                                            <i class="fas fa-cloud-upload-alt"></i> Upload Laporan
                                        </button>
                                        @endif
                                    </td>
                                    <td>{{ $week['display_title'] }}</td>
                                    <td>
                                        @if ($week['report'] && $week['report']->report_status == 'Sudah Upload')
                                        <span class="badge badge-success">Sudah Upload</span>
                                        @else
                                        <span class="badge badge-danger">Belum Upload</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($week['report'] && $week['report']->report_link1)
                                        <a href="{{ $week['report']->report_link1 }}" target="_blank">Link Sosmed</a>
                                        @else
                                        <span>No Link</span>
                                        @endif
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