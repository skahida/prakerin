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
            background-color: #d9edf7; /* header biru muda */
            vertical-align: middle;
            padding: 5px;
        }

        td {
            text-align: center;
            padding: 8px;
        }

        td.left {
            text-align: left;
        }

        /* Warna kolom data */
        td.effective { background-color: #cfe2f3; }   /* biru muda */
        td.present   { background-color: #d9f2d9; }   /* hijau muda */
        td.sick      { background-color: #fff3b0; }   /* kuning */
        td.permission{ background-color: #ffd699; }   /* oranye muda */
        td.absent    { background-color: #f5b0b0; }   /* merah muda */
        td.others    { background-color: #e0e0e0; }   /* abu-abu muda */

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

        .note {
            font-weight: normal;
            font-size: 10pt;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <h2>REKAPITULASI KEHADIRAN SISWA PRAKERIN</h2>
    <h3>SMK NU AL HIDAYAH TAHUN PELAJARAN 2025/2026</h3>
    <hr>
    <br>
    <div class="subtitle">{{ $batchName }}</div>
    <div class="subtitle">Kelas: {{ $className ?? '-' }}</div>  
    <div class="subtitle">Periode: {{ $yearResult }}</div>
    <br>

    <div class="note" style="font-weight: normal; font-size: 10pt; margin-top:20px;">
        <strong>Catatan Penting:</strong>  
        Dokumen ini merupakan rekapitulasi kehadiran siswa Prakerin yang diunduh resmi melalui 
        <a href="https://prakerin.skahida.my.id" target="_blank"><strong>Aplikasi Prakerin Tracer</strong></a>.  
    </div>
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
                    <td class="effective">{{ $item->hari_efektif }}</td>
                    <td class="present">{{ $item->masuk }}</td>
                    <td class="sick">{{ $item->sakit }}</td>
                    <td class="permission">{{ $item->izin }}</td>
                    <td class="absent">{{ $item->alpa }}</td>
                    <td class="others">{{ $item->lainnya }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="tanggal">
            Kudus, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            Kepala SMK NU Al Hidayah
            <div class="ttd">
                <br>
                <strong>Khaerudin, S.Pd.I, S.Kom, M.M.</strong>
            </div>
        </div>
    </div>
</body>

</html>
