@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            {{-- Search Form --}}
            <div class="col-md-12">
                <form method="GET">
                    <div class="input-group mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by Minggu"
                            value="{{ request()->input('search') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <button class="btn btn-md btn-info btn-fill" type="submit">
                                <i class="nc-icon nc-zoom-split"></i> Search
                            </button>
                        </div>
                        <div class="input-group-append ml-2"> <!-- Added margin-left (ml-2) to create space -->
                            <a href="{{ route('report') }}" class="btn btn-md btn-primary btn-fill">
                                <i class="nc-icon nc-refresh-02"></i> Reload
                            </a>
                        </div>
                    </div>

                </form>
            </div>

            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header ">
                        <h4 class="card-title">{{ $title }}</h4>
                        <p class="card-category">Here is a subtitle for this table</p>
                    </div>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th>No.</th>
                                <th>Minggu</th>
                                <th>Tanggal</th>
                                <th>Link (Sosmed) <span style="color:red">*</span></th>
                                <th>Status</th>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $report->report_title }}</td>
                                    <td>{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->timezone('Asia/Jakarta')->format('d-m-Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($report->report_link1)
                                        <a href="{{ $report->report_link1 }}"
                                            class="btn btn-md btn-info btn-fill" target="_blank">Lihat</a>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-warning"><i>{{ $report->report_status }}</i></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($reports->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Previous</span>
                                    </li>
                                    @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $reports->previousPageUrl() }}"
                                            aria-label="Previous">
                                            Previous
                                        </a>
                                    </li>
                                    @endif

                                    {{-- Page Number Links --}}
                                    @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $reports->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($reports->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $reports->nextPageUrl() }}"
                                            aria-label="Next">
                                            Next
                                        </a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                            <h4 class="card-title">Laporan Kegiatan Prakerin</h4>
                            <div id="calendar" class="mb-3"></div>
                        </div>
                    </div>
                </div> --}}
        </div>
    </div>
</div>
@endsection