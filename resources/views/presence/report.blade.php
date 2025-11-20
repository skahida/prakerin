<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Kehadiran Siswa Prakerin</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            margin: 10px;
        }

        h2,
        h3 {
            text-align: center;
            margin: 2px;
        }

        .subtitle {
            text-align: left;
            margin-bottom: 5px;
            margin-top: 5px;
            font-weight: bold;
        }

        .info {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th {
            text-align: center;
            background-color: #f0f0f0;
            vertical-align: middle;
        }

        td {
            text-align: center;
            padding: 10px;
        }

        td.left {
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer .tanggal {
            float: right;
            text-align: center;
        }

        .ttd {
            margin-top: 80px;
            text-align: center;
        }

        .ttd img {
            width: 100px;
        }

        .stamp {
            width: 130px;
        }
    </style>
</head>

<body>
    <h2>REKAPITULASI KEHADIRAN SISWA PRAKERIN</h2>
    <h3>SMK NU AL HIDAYAH TAHUN PELAJARAN 2024/2025</h3>
    <hr>
    <div class="subtitle">{{ $batchName }}</div>
    <div class="subtitle">Bulan : {{ $yearResult }}</div>
    <br>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA</th>
                <th rowspan="2">KELAS</th>
                <th rowspan="2">HARI EFEKTIF</th>
                <th rowspan="2">MASUK</th>
                <th colspan="4">TIDAK MASUK</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th>S</th>
                <th>I</th>
                <th>A</th>
                <th>LAINNYA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="left">{{ $item->nama }}</td>
                    <td class="left">{{ $item->kelas }}</td>
                    <td>{{ $item->hari_efektif }}</td>
                    <td>{{ $item->masuk }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alpa }}</td>
                    <td>{{ $item->lainnya }}</td>
                    <td class="left">{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="tanggal">
            Kudus, 1 April 2025<br>
            Kepala SMK NU Al Hidayah
            <div class="ttd">
                {{-- <img class="stamp" src="{{ public_path('img/stempel.png') }}" alt="Stempel">
                <br> --}}
                <strong>Khaerudin, S.Pd.I, S.Kom, M.M.</strong>
            </div>
        </div>
    </div>
</body>

</html>
