@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('report') }}" class="btn-secondary btn btn-fill mb-2"><i class="fas fa-arrow-left"></i>
                    Kembali</a>
                <a href="{{ route('generate.pdf', ['studentId' => $studentId]) }}"
                    class="btn-primary btn btn-fill mb-2"><i class="fas fa-print"></i> Cetak</a>
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <h4 class="card-title"><strong>Penilaian Laporan Praktek Kerja Industri</strong></h4>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead style="text-align: center;">
                                <tr style="border: 2px solid #636e72;">
                                    <th rowspan="2"
                                        style="vertical-align: middle; border: 2px solid #636e72; font-weight:bold;">No.
                                    </th>
                                    <th rowspan="2"
                                        style="vertical-align: middle; border: 2px solid #636e72; font-weight:bold;">
                                        Nama
                                    </th>
                                    <th rowspan="2"
                                        style="vertical-align: middle; border: 2px solid #636e72; font-weight:bold; ">
                                        Minggu
                                    </th>
                                    <th rowspan="2"
                                        style="vertical-align: middle; border: 2px solid #636e72; font-weight:bold;">
                                        Status
                                    </th>
                                    <th colspan="1"
                                        style="text-align: center; border: 2px solid #636e72; font-weight:bold;">Social
                                        Media
                                        Links</th>
                                    <th colspan="2"
                                        style="text-align: center; border: 2px solid #636e72; font-weight:bold;">
                                        Penilaian
                                    </th>
                                    <th rowspan="2"
                                        style="vertical-align: middle; border: 1px solid #636e72; font-weight:bold;">
                                        Total
                                    </th>
                                </tr>
                                <tr style="border: 2px solid #636e72;">
                                    <th style="border: 2px solid #636e72; font-weight:bold;">Link Sosmed</th>
                                    <th style="border: 2px solid #636e72; font-weight:bold;">Isi Konten (50%)</th>
                                    <th style="border: 2px solid #636e72; font-weight:bold;">Tampilan Video
                                        (50%)</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($reports as $report)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $report->student->name }}</td>
                                    <td>{{ $report->report_title }}</td>
                                    <td>
                                        @if ($report->report_status == 'Sudah Upload')
                                        <span class="badge badge-success">{{ $report->report_status }}</span>
                                        @else
                                        <span class="badge badge-danger">{{ $report->report_status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->report_link1)
                                        <a href="{{ $report->report_link1 }}" target="_blank">Link</a>
                                        @else
                                        <span>No Link</span>
                                        @endif
                                    </td>

                                    <!-- Input fields for grading -->
                                    <td>
                                        <input type="number" class="form-control"
                                            name="content_{{ $report->id }}"
                                            data-report-id="{{ $report->id }}"
                                            data-student-id="{{ $report->student_id }}"
                                            value="{{ $report->content ?? '0' }}" min="0" max="100"
                                            placeholder="Isi Konten" style="width: 120%;" />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control"
                                            name="video_appearance_{{ $report->id }}"
                                            data-report-id="{{ $report->id }}"
                                            data-student-id="{{ $report->student_id }}"
                                            value="{{ $report->video_appearance ?? '0' }}" min="0"
                                            max="100" placeholder="Kesesuaian dengan Panduan" />
                                    </td>

                                    <!-- Total nilai per laporan -->
                                    <td>
                                        <strong>
                                            {{ (0.5 * $report->content ?? 0) + (0.5 * $report->video_appearance ?? 0) }}
                                        </strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Grand Total (Rata-rata) Row -->
                        <div class="row">
                            <div class="col-md-12 text-right pr-5">
                                <strong style="color:red;">Grand Average:</strong>
                                <span style="font-weight: bold;">
                                    {{ $reports->isEmpty()
                                            ? 0
                                            : $reports->sum(function ($report) {
                                                    return $report->grade ?? 0;
                                                }) / $reports->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 d-flex justify-content-center">
                                <button id="saveButton" class="btn btn-success btn-fill mx-3">
                                    <i class="fas fa-save"></i> Simpan Nilai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection