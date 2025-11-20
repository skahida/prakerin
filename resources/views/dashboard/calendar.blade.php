<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Upload Mingguan</title>

    <!-- FullCalendar CSS (v3.x) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />

    <!-- Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Moment.js (Required by FullCalendar) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>

    <style>
        .fc-day-sunday {
            background-color: #f2dede;
            /* Highlight Sundays as holidays */
        }

        .fc-event {
            background-color: #5bc0de;
            /* Event background color */
            border-color: #46b8da;
            /* Border color */
            color: #fff;
            /* Text color */
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center">Kalender Upload Data Mingguan (14 Minggu)</h2>
        <div id="calendar"></div>
    </div>

    <script>
        $(document).ready(function() {
            var startDate = moment('2025-01-08'); // Tanggal mulai (Rabu)
            var weeks = [];

            // Generate 14 weeks events with 6 working days (Monday-Saturday)
            for (var i = 0; i < 14; i++) {
                var weekStart = startDate.clone().add(i * 7, 'days'); // Mulai minggu (Senin)
                var weekEnd = weekStart.clone().add(5, 'days'); // Akhir minggu (Sabtu)

                weeks.push({
                    title: 'Minggu ' + (i + 1) + ': Upload Laporan',
                    start: weekEnd.format('YYYY-MM-DD'), // Event ditampilkan pada hari Sabtu
                    rendering: 'block',
                    description: 'Minggu ' + (i + 1) + ', upload laporan!',
                    allDay: true
                });
            }

            // Initialize the calendar
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultView: 'agendaWeek',
                weekNumbers: true,
                events: weeks,
                // Disable event modification, allow interaction with the calendar only
                editable: false,
                droppable: false
            });
        });
    </script>

</body>

</html>
