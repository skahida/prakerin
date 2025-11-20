<table class="table table-hover table-striped">
    <thead>
        <th>No.</th>
        <th>Nama</th>
        <th>Minggu</th>
        <th>Status</th>
        <th>Facebook Link</th>
        <th>Instagram Link</th>
        <th>TikTok Link</th>
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
                        <a href="{{ $report->report_link1 }}" target="_blank">Facebook</a>
                    @else
                        <span>No Link</span>
                    @endif
                </td>
                <td>
                    @if ($report->report_link2)
                        <a href="{{ $report->report_link2 }}" target="_blank">Instagram</a>
                    @else
                        <span>No Link</span>
                    @endif
                </td>
                <td>
                    @if ($report->report_link3)
                        <a href="{{ $report->report_link3 }}" target="_blank">TikTok</a>
                    @else
                        <span>No Link</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
