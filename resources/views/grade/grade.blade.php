<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 2px solid #636e72;
            padding: 8px 12px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        thead th {
            border-top: 2px solid #636e72;
            border-left: 2px solid #636e72;
            border-right: 2px solid #636e72;
            border-bottom: 2px solid #636e72;
        }

        td {
            font-size: 12px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            font-size: 12px;
            border: 1px solid #ccc;
        }

        .total-row {
            font-weight: bold;
            font-size: 12px;
            text-align: right;
        }

        .grand-average {
            color: red;
        }

        .info-table {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: left;
            border-collapse: collapse;
        }

        .info-table td {
            width: 50%;
            padding: 8px 12px;
            text-align: left;
        }

        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
            border: none;
        }

        .signature-table td {
            width: 50%;
            padding: 20px;
        }

        .signature-table .signature-line {
            border-top: 1px solid #000;
            width: 100%;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2 style="text-align: center; font-size: 18px;">
        JURNAL PENILAIAN <br> LAPORAN PER-MINGGU PRAKERIN GEL.{{ $batch }} <br> SMK NU AL HIDAYAH
        KUDUS TP {{ $year }}</h2>

    <!-- Student Info Table -->
    <table style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td style="padding-bottom: 0px; text-align: left; border: none;"><strong>NAMA:</strong>
                {{ $name }}
            </td>
            <td style="padding-bottom: 0px; text-align: left; border: none;"><strong>DUDI:</strong>
                {{ $dudi }}</td>
        </tr>
        <tr>
            <td style="padding-bottom: 0px; text-align: left; border: none;"><strong>KELAS:</strong>
                {{ $class }}</td>
            <td style="padding-bottom: 0px; text-align: left; border: none;"><strong>GURU PEMBIMBING:</strong>
                {{ $mentor }}</td>
        </tr>
    </table>

    <!-- Table for Reports -->
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th rowspan="2" style="font-size: 12px;">No.</th>
                <th rowspan="2" style="font-size: 12px;">Minggu</th>
                <th colspan="2" style="font-size: 12px;">Penilaian</th>
                <th rowspan="2" style="font-size: 12px;">Total</th>
            </tr>
            <tr>
                <th style="font-size: 12px;">Isi Konten (50%)</th>
                <th style="font-size: 12px;">Tampilan Video (50%)</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ str_replace(': Upload Laporan', '', $report->report_title) }}</td>
                    <td>{{ $report->content ?? '0' }}</td>
                    <td>{{ $report->video_appearance ?? '0' }}</td>
                    <td><strong>{{ (0.5 * $report->content ?? 0) + (0.5 * $report->video_appearance ?? 0) }}</strong>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Grand Average:</td>
                <td colspan="2">
                    <strong>
                        {{ $reports->isEmpty()
                            ? 0
                            : $reports->sum(function ($report) {
                                    return (0.5 * $report->content ?? 0) + (0.5 * $report->video_appearance ?? 0);
                                }) / $reports->count() }}
                    </strong>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 12px;">
        <h3>Keterangan Kriteria Penilaian:</h3>
        <ul>
            <li><strong>Isi Konten</strong> (Adanya judul kegiatan, Identitas Sekolah dan Pekerjaan yang dilakukan)</li>
            <li><strong>Tampilan Video</strong> (Pengambilan gambar baik dan suara jelas serta audio sesuai dengan
                visual yang ditampilkan)</li>
            {{-- <li><strong>Kreativitas dan Inovasi</strong> (Meliputi Transisi, Animasi dan Efek Tambahan)</li>
            <li><strong>Kesesuaian Dengan Panduan</strong> (Video sesuai dengan ketentuan yang diberikan)</li>
            <li><strong>Unggah Medsos</strong> (Siswa mengunggah di FB, IG, TikTok dll)</li> --}}
        </ul>
    </div>

    <!-- Tanggal print dan download -->
    <p style="font-size: 12px;">
        Laporan ini dicetak pada: <b>{{ $date }}</b><br>
        Laporan ini didownload melalui platform: <a href="https://prakerin.skahida.sch.id/"
            target="_blank">https://prakerin.skahida.sch.id/</a>
    </p>

    <!-- Signature Section -->
    <table style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td colspan="2" style="text-align: center; border: none; font-size: 12px; width: 20%;">
                Mengetahui,
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border: none; font-size: 12px; width: 20%;">
                <p>Ketua Program Keahlian TJKT</p>
                <br><br>
                <p><strong><u>Aris Mulyono, S.Pd.I</u></strong></p>
            </td>
            <td style="text-align: left; border: none; font-size: 12px; width: 5%;">
                Kudus, {{ $date }}
                <p>Pembimbing</p>
                <br><br>
                <p><strong><u>{{ $mentor }}</u></strong></p>
            </td>
        </tr>
    </table>

</body>

</html>
