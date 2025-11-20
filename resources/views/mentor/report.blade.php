<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report Mentor | {{ $title }}</title>
    <!-- Link to Google Fonts for better font styling -->
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
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4A90E2;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        td {
            background-color: #ffffff;
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
                border: none;
                box-shadow: none;
            }

            th,
            td {
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body>
    <h1>Mentor Report - {{ $title }}</h1>
    <table class="table table-hover table-striped">
        <thead>
            <th>ID</th>
            <th>Nama Lengkap</th>
            <th>Jenis Kelamin</th>
            <th>Nomor WhatsApp</th>
            <th>Username</th>
            <th>Password</th>
            <th>Created at</th>
            <th>Created at</th>
        </thead>
        <tbody>
            @foreach ($mentors as $mentor)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mentor->name }}</td>
                    <td>{{ $mentor->gender }}</td>
                    <td>{{ $mentor->whatsapp_number }}</td>
                    <td>{{ $mentor->user->username }}</td>
                    <td>{{ 'prakerintracer' }}</td>
                    <td>{{ $mentor->created_at }}</td>
                    <td>{{ $mentor->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Automatic Print on Load -->
    <script>
        window.print();
    </script>
</body>

</html>
