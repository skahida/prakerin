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
        REKAP JURNAL PENILAIAN <br> LAPORAN PER-MINGGU PRAKERIN GEL.{{ $batch }} <br> SMK NU AL HIDAYAH
        KUDUS TP {{ $year }}</h2>

    <!-- Table for Reports -->
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th rowspan="2" style="font-size: 12px;">No.</th> <!-- Added column for serial number -->
                <th rowspan="2" style="font-size: 12px;">Nama Siswa</th>
                @php
                    $weeks = []; // Array to track unique weeks
                @endphp

                @foreach ($reports as $report)
                    @php
                        // Extracting the week number from the report_title using regex
                        preg_match('/Minggu (\d+)/', $report->report_title, $matches);
                        $week = isset($matches[1]) ? (int) $matches[1] : 0;
                        if (!in_array($week, $weeks)) {
                            $weeks[] = $week;
                        }
                    @endphp
                @endforeach

                @foreach ($weeks as $week)
                    <th style="font-size: 12px;">Minggu {{ $week }}</th>
                @endforeach
                <th rowspan="2" style="font-size: 12px;">Total Nilai</th>
            </tr>
        </thead>


        <tbody>
            @php
                $displayedStudents = []; // Initialize an array to track students that have been shown
                $counter = 1; // Initialize counter for serial numbers
            @endphp

            @foreach ($reports->unique('student_id') as $report)
                @php
                    $student = $report->student; // Access the related student
                    $totalGrade = 0;
                    $weekCount = 0; // Initialize week count for averaging
                @endphp
                <tr>
                    <td style="font-size: 12px;">{{ $counter++ }}</td> <!-- Display serial number -->

                    @if (!in_array($student->id, $displayedStudents))
                        <td style="font-size: 12px; text-align: left;">{{ $student->name }}</td>
                        @php
                            // Add the student's ID to the array to prevent future duplicate displays
                            $displayedStudents[] = $student->id;
                        @endphp
                    @else
                        <td></td> <!-- Empty cell for students already displayed -->
                    @endif

                    @foreach ($weeks as $week)
                        @php
                            // Find the report for the current week using regex to match "Minggu X"
                            $reportForWeek = $reports->first(function ($r) use ($student, $week) {
                                // Extract the week number from report_title using regex
                                preg_match('/Minggu (\d+):/', $r->report_title, $matches);
                                $reportWeek = isset($matches[1]) ? (int) $matches[1] : null;

                                // Match report based on student_id and week number
                                return $r->student_id == $student->id && $reportWeek == $week;
                            });

                            // If no report is found for this week, set grade to 0
                            $grade = $reportForWeek ? $reportForWeek->grade : 0;
                            $totalGrade += $grade;
                            $weekCount++;
                        @endphp
                        <td>
                            <strong>{{ $grade }}</strong> <!-- Display grade or 0 if null -->
                        </td>
                    @endforeach

                    @php
                        // Calculate the average grade (if there are any weeks)
                        $averageGrade = $weekCount > 0 ? $totalGrade / $weekCount : 0;
                    @endphp
                    <td style="font-size: 12px; font-weight: bold;">{{ number_format($averageGrade, 2) }}</td>
                    <!-- Display the average grade with 2 decimal places -->
                </tr>
            @endforeach
        </tbody>



    </table>

    <div style="margin-top: 30px; font-size: 12px;">
        <h3>Keterangan Kriteria Penilaian:</h3>
        <ul>
            <li><strong>Isi Konten</strong> (Adanya judul kegiatan, Identitas Sekolah dan Pekerjaan yang dilakukan).
                Bobot (50%)</li>
            {{-- <li><strong>Audio Visual</strong> (Pengambilan gambar baik dan suara jelas serta audio sesuai dengan visual
                yang ditampilkan). Bobot (20%)</li>
            <li><strong>Kreativitas dan Inovasi</strong> (Meliputi Transisi, Animasi dan Efek Tambahan). Bobot (15%)
            </li> --}}
            <li><strong>Tampilan Video</strong> (Video sesuai dengan ketentuan yang diberikan). Bobot (50%)
            </li>
            {{-- <li><strong>Unggah Medsos</strong> (Siswa mengunggah di FB, IG, TikTok dll). Bobot (20%)</li> --}}
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
                &nbsp;
                <p>Kepala SMK NU Al Hidayah</p>
                <br><br>
                <p><strong><u>{{ 'Khaerudin, S.Pd.I., S.Kom., M.M.' }}</u></strong></p>
            </td>
            <td style="text-align: left; border: none; font-size: 12px; width: 10%;">
                Kudus, {{ $date }}
                <p>Ketua Program Keahlian TJKT</p>
                <br><br>
                <p><strong><u>{{ 'Aris Mulyono, S.Pd.I' }}</u></strong></p>
            </td>
        </tr>
    </table>

</body>

</html>
