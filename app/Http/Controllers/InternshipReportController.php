<?php

namespace App\Http\Controllers;

use App\Models\InternshipBatch;
use App\Models\Student;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InternshipReportController extends Controller
{
    /**
     * Display a listing of the reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Laporan Kegiatan Prakerin";

        $query = $request->input('search');
        $studentId = session('ses_student_id'); // Get the student ID from the request

        // Query reports based on the search term, student ID, and order by report_title in descending order
        $reports = Report::when($query, function ($queryBuilder) use ($query) {
            return $queryBuilder->where('report_title', 'like', '%' . $query . '%');
        })
            ->when($studentId, function ($queryBuilder) use ($studentId) {
                return $queryBuilder->where('student_id', $studentId);  // Add where condition for ses_student_id
            })
            ->orderBy('report_title', 'desc')  // Order by report_title in descending order
            ->paginate(5);  // Pagination

        return view('report.index', compact('title', 'reports'));
    }

    public function v1index(Request $request)
    {
        $title = "Laporan Kegiatan Prakerin";

        if (session('ses_role') == 'student') {

            $studentId = session('ses_student_id');
            $student = Student::find($studentId);
            $internshipBatch = $student->internshipBatch;

            $startDate = Carbon::parse($internshipBatch->start_date);
            $endDate = Carbon::parse($internshipBatch->end_date);

            $reports = Report::where('student_id', $studentId)
                ->whereNotNull('report_link1')
                ->orderBy('created_at', 'asc')
                ->get()
                ->keyBy('report_title'); // key by report_title

            $weeks = [];
            $currentDate = $startDate;
            $maxWeeks = 16;

            for ($i = 0; $i < $maxWeeks; $i++) {
                $dbTitle = 'Minggu ' . ($i + 1) . ': Upload Laporan'; // harus sama persis dengan DB
                $report = $reports->get($dbTitle);

                $weeks[] = [
                    'db_title' => $dbTitle,
                    'display_title' => $dbTitle, // bisa sama, atau tambahkan tambahan teks jika mau
                    'minggu' => $i + 1,
                    'tanggal_mulai' => $currentDate->format('d-m-Y'),
                    'tanggal_akhir' => $currentDate->copy()->addDays(6)->format('d-m-Y'),
                    'report' => $report,
                ];

                $currentDate->addWeek();
            }


            // Tentukan disabled weeks
            $disabledWeeks = [];
            foreach ($weeks as $index => $week) {
                if ($index > 0 && $weeks[$index - 1]['report'] === null) {
                    $disabledWeeks[] = $week['minggu'];
                }
            }

            return view('report._v1index', compact('title', 'weeks', 'reports', 'disabledWeeks'));
        } elseif (session('ses_role') == 'mentor') {

            $mentorId = session('ses_mentor_id');

            // Get the search term from the request, if present
            $search = request('search', '');
            $batchSearch = request('batch_search');

            // Fetch the reports with the relationship to the student and their grades
            $reportsQuery = Report::with(['student.grades']) // Eager load the grades
                ->whereHas('student', function ($query) use ($mentorId) {
                    $query->where('mentor_id', $mentorId);
                })
                ->orderBy('id', 'desc');

            // If there's a search term, filter the reports by student name
            if ($search) {
                $reportsQuery->whereHas('student', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            }

            // If batch_search is provided, filter by internship batch name
            if ($batchSearch) {
                $reportsQuery->whereHas('student.internshipBatch', function ($query) use ($batchSearch) {
                    $query->where('id',   $batchSearch);
                });
            }

            $reports = $reportsQuery->get(); // Get the filtered reports

            // Kelompokkan laporan berdasarkan student_id dan ambil laporan pertama untuk setiap siswa
            $distinctReports = $reports->groupBy('student_id')->map(function ($group) {
                return $group->first(); // Ambil laporan pertama di setiap grup
            });

            // Convert the grouped collection into a regular collection
            $distinctReportsCollection = collect($distinctReports->values());

            // Paginate the collection (manually, since it's no longer a DB query)
            $perPage = 5;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $distinctReportsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            // Create a LengthAwarePaginator instance
            $distinctReportsPaginated = new LengthAwarePaginator(
                $currentItems,
                $distinctReportsCollection->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            foreach ($distinctReportsPaginated as $report) {
                // Menyimpan grades yang sudah diambil melalui eager loading
                $grades = $report->student->grades; // Mengambil grades melalui eager loading

                // Hitung jumlah laporan yang ada untuk siswa ini
                $totalReports = $report->student->reports->count(); // Hitung jumlah reports untuk student
                $requiredReports = 12; // Asumsikan total laporan yang diperlukan adalah 12

                // Hitung persentase kemajuan berdasarkan jumlah reports
                $percentage = ($totalReports / $requiredReports) * 100;
                $report->progress_percentage = min($percentage, 100); // Pastikan tidak lebih dari 100%

                // Hitung rata-rata nilai dari kolom 'grade' jika grades tidak kosong
                if ($grades->isNotEmpty()) {
                    $gradeCount = $grades->count(); // Menghitung jumlah grades

                    // Jika ada grades, hitung rata-rata
                    $averageGrade = $grades->avg('grade'); // Menggunakan Eloquent avg untuk menghitung rata-rata
                    $report->average_grade = round($averageGrade, 2); // Rata-rata nilai yang dibulatkan
                } else {
                    $report->average_grade = null; // Jika tidak ada grade, set ke null
                }

                // Set nama Dudi jika ada, jika tidak, set 'No Dudi'
                $report->dudi_name = $report->student->internshipPlace ? $report->student->internshipPlace->name : 'No Dudi';
            }

            $batches = InternshipBatch::orderBy('id', 'desc')->get();
            $students = Student::orderBy('id', 'desc')->get();

            return view('report.history', compact('title', 'distinctReportsPaginated', 'reports', 'search', 'students', 'batchSearch', 'batches'));
        } elseif (session('ses_role') === 'admin' || session('ses_role') === 'super-admin') {
            // Get the search term from the request, if present
            $search = request('search', '');
            $batchSearch = request('batch_search');
            // Query the reports and eager load the relationships
            $reportsQuery = Report::with(['student.grades', 'student.mentor', 'student.internshipPlace', 'student.internshipBatch'])
                ->orderBy('id', 'desc');

            // If there's a search term, filter the reports by student name
            if ($search) {
                $reportsQuery->whereHas('student', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            }

            // If batch_search is provided, filter by internship batch name
            if ($batchSearch) {
                $reportsQuery->whereHas('student.internshipBatch', function ($query) use ($batchSearch) {
                    $query->where('id',   $batchSearch);
                });
            }

            $reports = $reportsQuery->get(); // Get the filtered reports

            // Kelompokkan laporan berdasarkan student_id dan ambil laporan pertama untuk setiap siswa
            $distinctReports = $reports->groupBy('student_id')->map(function ($group) {
                return $group->first(); // Ambil laporan pertama di setiap grup
            });

            // Convert the grouped collection into a regular collection
            $distinctReportsCollection = collect($distinctReports->values());

            // Paginate the collection (manually, since it's no longer a DB query)
            $perPage = 5;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $distinctReportsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            // Create a LengthAwarePaginator instance
            $distinctReportsPaginated = new LengthAwarePaginator(
                $currentItems,
                $distinctReportsCollection->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            foreach ($distinctReportsPaginated as $report) {
                // Menyimpan grades yang sudah diambil melalui eager loading
                $grades = $report->student->grades; // Mengambil grades melalui eager loading

                // Hitung jumlah laporan yang ada untuk siswa ini
                $totalReports = $report->student->reports->count(); // Hitung jumlah reports untuk student
                $requiredReports = 12; // Asumsikan total laporan yang diperlukan adalah 12

                // Hitung persentase kemajuan berdasarkan jumlah reports
                $percentage = ($totalReports / $requiredReports) * 100;
                $report->progress_percentage = min($percentage, 100); // Pastikan tidak lebih dari 100%

                // Hitung rata-rata nilai dari kolom 'grade' jika grades tidak kosong
                if ($grades->isNotEmpty()) {
                    $gradeCount = $grades->count(); // Menghitung jumlah grades

                    // Jika ada grades, hitung rata-rata
                    $averageGrade = $grades->avg('grade'); // Menggunakan Eloquent avg untuk menghitung rata-rata
                    $report->average_grade = round($averageGrade, 2); // Rata-rata nilai yang dibulatkan
                } else {
                    $report->average_grade = null; // Jika tidak ada grade, set ke null
                }

                // Store grades in the report object
                $report->grades = $grades;

                // Store mentor's name in the report object
                $report->mentor_name = $report->student->mentor ? $report->student->mentor->name : 'No Mentor';
                $report->dudi_name = $report->student->internshipPlace ? $report->student->internshipPlace->name : 'No Dudi';
            }

            $batches = InternshipBatch::orderBy('id', 'desc')->get();
            $students = Student::orderBy('id', 'desc')->get();

            return view('report.history', compact('title', 'distinctReportsPaginated', 'reports', 'search', 'students', 'batchSearch', 'batches'));
        }
    }
}
