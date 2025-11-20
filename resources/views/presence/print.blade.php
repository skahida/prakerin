<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Siswa</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: #222;
            font-size: 11.5px;
            background: #fff;
        }

        h2,
        h3 {
            margin: 0;
            text-align: center;
            line-height: 1.3;
        }

        h2 {
            font-size: 15px;
            text-transform: uppercase;
        }

        h3 {
            font-size: 13px;
            color: #444;
            margin-bottom: 6px;
        }

        .meta {
            width: 100%;
            margin: 8px 0 12px;
            font-size: 11px;
        }

        .meta td {
            padding: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: center;
        }

        th {
            background: #2563eb;
            color: #fff;
            text-transform: uppercase;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10.5px;
        }

        .signature {
            margin-top: 35px;
            text-align: right;
        }

        .no-print {
            position: fixed;
            bottom: 15px;
            right: 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .no-print:hover {
            background: #1e3a8a;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h2>SMK NU AL HIDAYAH</h2>
    <h3>Laporan Presensi Siswa</h3>
    <p style="text-align:center; margin-bottom:10px;">{{ now()->translatedFormat('d F Y') }}</p>

    <table class="meta">
        <tr>
            <td><strong>Dicetak oleh:</strong> {{ auth()->user()->name }}</td>
            <td><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        @if(request()->filled('search') || request()->filled('batch_search') || request()->filled('start_date') || request()->filled('start_month'))
        <tr>
            <td colspan="2">
                <strong>Filter:</strong>
                {{ request()->input('search') ? 'Siswa: ' . request()->input('search') . '; ' : '' }}
                {{ request()->input('batch_search') ? 'Gelombang: ' . request()->input('batch_search') . '; ' : '' }}
                {{ request()->input('start_month') ? 'Mulai Bulan: ' . request()->input('start_month') . '; ' : '' }}
                {{ request()->input('end_month') ? 'Sampai Bulan: ' . request()->input('end_month') . '; ' : '' }}
                {{ request()->input('start_date') ? 'Tanggal Awal: ' . request()->input('start_date') . '; ' : '' }}
                {{ request()->input('end_date') ? 'Tanggal Akhir: ' . request()->input('end_date') : '' }}
            </td>
        </tr>
        @endif
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Dudi</th>
                <th>Gel.</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historyPresences as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->student->name }}</td>
                <td>{{ $item->student->class->name ?? '-' }}</td>
                <td>{{ $item->student->internshipPlace->name ?? '-' }}</td>
                <td>{{ $item->student->internshipBatch->batch_name ?? '-' }}</td>
                <td>{{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('H:i') : '-' }}</td>
                <td>{{ $item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('H:i') : '-' }}</td>
                <td>
                    @switch($item->status)
                    @case('present') Masuk @break
                    @case('permission') Izin @break
                    @case('sick') Sakit @break
                    @case('holiday') Libur @break
                    @default Alpa
                    @endswitch
                </td>
                <td>{{ $item->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10">Tidak ada data presensi ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh sistem — {{ 'Aplikasi Prakerin Tracer' }}</p>
        <div class="signature">
            <p><strong>Super Admin</strong></p>
            <br><br>
            <p>(__________________________)</p>
        </div>
    </div>

    <button class="no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
</body>

</html>