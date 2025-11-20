<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Report | {{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4A90E2;
            font-size: 24px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
            color: #333;
        }

        th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }

        td {
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #f1f1f1;
        }

        /* Print Styling */
        @media print {
            body {
                padding: 0;
                background-color: #fff;
            }

            h1 {
                font-size: 22px;
                margin-bottom: 10px;
            }

            table {
                border: 1px solid #ddd;
                box-shadow: none;
            }

            th,
            td {
                border: 1px solid #ddd;
            }

            .table-container {
                overflow: initial;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h1>Student Report - {{ $title }}</h1>

    <!-- Tanggal print dan download -->
    <p style="font-size: 12px;">
        Laporan ini dicetak pada: <b>{{ $date }}</b><br>
        Laporan ini didownload melalui platform: <a href="https://prakerin.skahida.sch.id/"
            target="_blank">https://prakerin.skahida.sch.id/</a>
    </p>

    <div class="table-container">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Kelas</th>
                    <th>Dudi</th>
                    <th>Pembimbing</th>
                    <th>Username/NIS</th>
                    <th>Password Default</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->gender }}</td>
                        <td>{{ $student->class->name }}</td>
                        <td>{{ $student->internshipPlace->name }}</td>
                        <td>{{ $student->mentor->name }}</td>
                        <td>{{ $student->user->username }}</td>
                        <td>{{ 'prakerintracer' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
