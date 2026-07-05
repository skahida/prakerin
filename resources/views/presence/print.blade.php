<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Siswa</title>

    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            color: #111;
        }

        /* ===== HEADER DOKUMEN ===== */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .header h3 {
            font-size: 15px;
            margin: 4px 0 2px;
            font-weight: normal;
        }

        .header p {
            font-size: 12px;
            color: #444;
            margin: 0;
        }

        hr {
            border: none;
            border-top: 3px solid #3b82f6;
            margin: 10px 0 14px;
        }

        /* ===== INFO / META ===== */
        .meta {
            width: 100%;
            margin-bottom: 14px;
            font-size: 12px;
        }

        .meta td {
            padding: 5px 6px;
            vertical-align: top;
        }

        .meta strong {
            display: inline-block;
            width: 120px;
        }

        .filter {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 6px 10px;
            font-size: 12px;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #3b82f6;        /* BIRU CERAH */
            color: #ffffff;
            font-size: 12.5px;
            padding: 9px 6px;
            text-align: center;
            border: 1px solid #1e3a8a;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            font-size: 12px;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 22px;
            font-size: 12px;
            width: 100%;
        }

        .signature {
            width: 260px;
            float: right;
            text-align: center;
            margin-top: 30px;
        }

        .signature p {
            margin: 4px 0;
        }

        /* wkhtmltopdf safe */
        .no-print {
            display: none;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <h2>SMK NU AL HIDAYAH</h2>
        <h3>Laporan Presensi Siswa Prakerin</h3>
        <p>{{ now()->translatedFormat('d F Y') }}</p>
    </div>

    <hr>

    <!-- META -->
    <table class="meta">
        <tr>
            <td>
                <strong>Dicetak Oleh</strong>: {{ auth()->user()->name }}
            </td>
            <td>
                <strong>Tanggal Cetak</strong>: {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>

        @if(request()->filled('search') || request()->filled('batch_search') || request()->filled('start_date') || request()->filled('start_month'))
        <tr>
            <td colspan="2" class="filter">
                <strong>Filter:</strong>
                {{ request('search') ? 'Siswa: ' . request('search') . ' | ' : '' }}
                {{ request('batch_search') ? 'Gelombang: ' . request('batch_search') . ' | ' : '' }}
                {{ request('start_month') ? 'Bulan: ' . request('start_month') . ' s/d ' . request('end_month') . ' | ' : '' }}
                {{ request('start_date') ? 'Tanggal: ' . request('start_date') . ' s/d ' . request('end_date') : '' }}
            </td>
        </tr>
        @endif
    </table>

    <!-- TABLE DATA -->
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="18%">Nama</th>
                <th width="10%">Kelas</th>
                <th width="16%">DUDI</th>
                <th width="7%">Gel.</th>
                <th width="10%">Tanggal</th>
                <th width="7%">Masuk</th>
                <th width="7%">Pulang</th>
                <th width="7%">Status</th>
                <th width="14%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historyPresences as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-left">{{ $item->student->name }}</td>
                <td class="text-center">{{ $item->student->class->name ?? '-' }}</td>
                <td class="text-left">{{ $item->student->internshipPlace->name ?? '-' }}</td>
                <td class="text-center">{{ $item->student->internshipBatch->batch_name ?? '-' }}</td>
                <td class="text-center">
                    {{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('d/m/Y') : '-' }}
                </td>
                <td class="text-center">
                    {{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('H:i') : '-' }}
                </td>
                <td class="text-center">
                    {{ $item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('H:i') : '-' }}
                </td>
                <td class="text-center">
                    @switch($item->status)
                        @case('present') Masuk @break
                        @case('permission') Izin @break
                        @case('sick') Sakit @break
                        @case('holiday') Libur @break
                        @default Alpa
                    @endswitch
                </td>
                <td class="text-left">{{ $item->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">
                    Tidak ada data presensi ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <p><strong>Super Admin</strong></p>
            <br><br>
            <p><strong>( ________________________ )</strong></p>
        </div>
    </div>

</body>
</html>
