<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Report;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GradeController extends Controller
{
    public function index($studentId)
    {
        $title = "Grade";
        $role = session('ses_role');

        if ($role == 'admin' || $role == 'super-admin') {
            // Mengambil data laporan dengan grades menggunakan relasi yang sudah ada
            $reports = Report::select(
                'reports.*',
                'grades.grade',
                'grades.content',
                // 'grades.audio_visual',
                // 'grades.creativity_innovation',
                // 'grades.social_media_upload',
                // 'grades.adherence_to_guidelines',
                'grades.video_appearance',
                'students.mentor_id', // Mengambil mentor_id dari tabel students
                'mentors.name as mentor_name'
            ) // Mengambil nama mentor dari tabel mentors
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id') // Join dengan tabel students
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id') // Join dengan tabel mentors
                ->where('reports.student_id', $studentId)
                ->get();
        } elseif ($role == 'mentor') {
            $mentorId = session('ses_mentor_id');
            // Mengambil data laporan dengan grades menggunakan relasi yang sudah ada
            $reports = Report::select(
                'reports.*',
                'grades.grade',
                'grades.content',
                // 'grades.audio_visual',
                // 'grades.creativity_innovation',
                // 'grades.social_media_upload',
                // 'grades.adherence_to_guidelines',
                'grades.video_appearance',
                'students.mentor_id', // Mengambil mentor_id dari tabel students
                'mentors.name as mentor_name'
            ) // Mengambil nama mentor dari tabel mentors
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id') // Join dengan tabel students
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id') // Join dengan tabel mentors
                ->where('reports.student_id', $studentId)
                ->where('students.mentor_id', $mentorId) // Filter berdasarkan mentor_id
                ->get();
        }



        // Generate HTML untuk laporan yang diminta
        return view('grade.index', compact('reports', 'title', 'studentId'));
    }

    public function saveOrUpdateGrade(Request $request)
    {
        // Validasi bahwa data grades yang dikirimkan adalah array dan setiap item memiliki properti yang dibutuhkan
        $request->validate([
            'grades' => 'required|array',
            'grades.*.report_id' => 'required|exists:reports,id',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.content' => 'nullable|numeric|min:0|max:100',
            // 'grades.*.audio_visual' => 'nullable|numeric|min:0|max:100',
            // 'grades.*.creativity_innovation' => 'nullable|numeric|min:0|max:100',
            // 'grades.*.social_media_upload' => 'nullable|numeric|min:0|max:100',
            'grades.*.video_appearance' => 'nullable|numeric|min:0|max:100',
        ]);

        // Loop melalui data grades yang dikirimkan
        foreach ($request->grades as $gradeData) {
            // Hitung Grand Total untuk setiap laporan
            $grandTotal = (0.5 * ($gradeData['content'] ?? 0)) +
                // (0.2 * ($gradeData['audio_visual'] ?? 0)) +
                // (0.15 * ($gradeData['creativity_innovation'] ?? 0)) +
                // (0.15 * ($gradeData['social_media_upload'] ?? 0)) +
                (0.5 * ($gradeData['video_appearance'] ?? 0));

            // Update atau buat grade baru berdasarkan student_id dan report_id
            Grade::updateOrCreate(
                ['student_id' => $gradeData['student_id'], 'report_id' => $gradeData['report_id']],
                [
                    'content' => $gradeData['content'],
                    // 'audio_visual' => $gradeData['audio_visual'],
                    // 'creativity_innovation' => $gradeData['creativity_innovation'],
                    // 'social_media_upload' => $gradeData['social_media_upload'],
                    'video_appearance' => $gradeData['video_appearance'],
                    'grade' => $grandTotal, // Simpan grand total nilai
                ]
            );
        }

        return response()->json(['message' => 'Nilai berhasil disimpan atau diperbarui!']);
    }

    public function generatePDF($studentId)
    {
        $role = session('ses_role');

        if ($role == 'admin' || $role == "super-admin") {
            // Mengambil data laporan dengan grades menggunakan relasi yang sudah ada
            $reports = Report::select(
                'reports.*',
                'grades.content',
                // 'grades.audio_visual',
                // 'grades.creativity_innovation',
                // 'grades.social_media_upload',
                // 'grades.adherence_to_guidelines',
                'grades.video_appearance',
                'students.mentor_id', // Mengambil mentor_id dari tabel students
                'mentors.name as mentor_name',
                'students.class_code', // Mengambil mentor_id dari tabel students
                'classes.name as class_name',
                'students.internship_place_code',
                'internship_places.name as dudi_name',
                'students.internship_batch_id',
                'internship_batches.batch_name as batch_name',
                'internship_batches.academic_year as academic_year',
            ) // Mengambil nama mentor dari tabel mentors
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id') // Join dengan tabel students
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id') // Join dengan tabel mentors
                ->leftJoin('classes', 'classes.code', '=', 'students.class_code')
                ->leftJoin('internship_places', 'internship_places.code', '=', 'students.internship_place_code')
                ->leftJoin('internship_batches', 'internship_batches.id', '=', 'students.internship_batch_id')
                ->where('reports.student_id', $studentId)
                ->get();
        } elseif ($role == 'mentor') {
            $mentorId = session('ses_mentor_id');
            // Mengambil data laporan dengan grades menggunakan relasi yang sudah ada
            $reports = Report::select(
                'reports.*',
                'grades.content',
                // 'grades.audio_visual',
                // 'grades.creativity_innovation',
                // 'grades.social_media_upload',
                // 'grades.adherence_to_guidelines',
                'grades.video_appearance',
                'students.mentor_id', // Mengambil mentor_id dari tabel students
                'mentors.name as mentor_name',
                'students.class_code', // Mengambil mentor_id dari tabel students
                'classes.name as class_name',
                'students.internship_place_code',
                'internship_places.name as dudi_name',
                'students.internship_batch_id',
                'internship_batches.batch_name as batch_name',
                'internship_batches.academic_year as academic_year',
            ) // Mengambil nama mentor dari tabel mentors
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id') // Join dengan tabel students
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id') // Join dengan tabel mentors
                ->leftJoin('classes', 'classes.code', '=', 'students.class_code')
                ->leftJoin('internship_places', 'internship_places.code', '=', 'students.internship_place_code')
                ->leftJoin('internship_batches', 'internship_batches.id', '=', 'students.internship_batch_id')
                ->where('reports.student_id', $studentId)
                ->where('students.mentor_id', $mentorId) // Filter berdasarkan mentor_id
                ->get();
        }

        // dd($reports);

        // Data untuk PDF
        $data = [
            'name' => $reports->isNotEmpty() ? $reports->first()->student->name : 'Unknown Siswa Prakerin',
            'mentor' => $reports->isNotEmpty() ? $reports->first()->mentor_name : 'Unknown Guru Pembimbing',
            'class' => $reports->isNotEmpty() ? $reports->first()->class_name : 'Unknown Kelas',
            'dudi' => $reports->isNotEmpty() ? $reports->first()->dudi_name : 'Unknown Dudi',
            'batch' => $reports->isNotEmpty()
                ? preg_replace('/[^0-9]/', '', $reports->first()->batch_name)
                : 'Unknown Batch', // Extract the number from 'Gelombang 3'
            'year' => $reports->isNotEmpty() ? $reports->first()->academic_year : 'Unknown Tahun',
            'date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'), // Tanggal format bahasa Indonesia
            'reports' => $reports, // Ensure you pass the reports data here
        ];

        // dd($data);


        // $footerHtml = view()->make('grade.footer')->render();

        // Setting custom margin for F4 page size
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('grade.grade', $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait')
            ->setOption('margin-top', 20)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15)
            ->setOption('margin-bottom', 20);
        // ->setOption('header-html', $footerHtml);

        // Mengunduh PDF
        return $pdf->download('nilai_' . $data['name'] . '.pdf');




        // // Mengunduh PDF
        // return $pdf->download('invoice.pdf');

        // Setting custom margin for custom page size (F4 dimensions)
        // $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('grade.grade', $data)
        //     ->setOption('page-width', '210mm')  // Set custom width (F4 width)
        //     ->setOption('page-height', '330mm') // Set custom height (F4 height)
        //     ->setOption('orientation', 'Portrait') // Set orientation to portrait
        //     ->setOption('margin-top', 20)    // Top margin in mm
        //     ->setOption('margin-left', 15)   // Left margin in mm
        //     ->setOption('margin-right', 15)  // Right margin in mm
        //     ->setOption('margin-bottom', 20); // Bottom margin in mm
    }

    public function rekapGrade(Request $request)
    {
        $role = session('ses_role');

        // Capture the filters from the request
        $batchName = request('batch_search');
        $search = request('search');

        if ($role == 'admin' || $role == 'super-admin') {

            $reportsQuery = Report::select(
                'reports.*',
                'grades.content',
                // 'grades.audio_visual',
                // 'grades.creativity_innovation',
                // 'grades.social_media_upload',
                // 'grades.adherence_to_guidelines',
                'grades.video_appearance',
                'grades.grade',
                'students.mentor_id',
                'students.id',
                'students.name as student_name',
                'mentors.name as mentor_name',
                'students.internship_batch_id',
                'internship_batches.batch_name as batch_name',
                'internship_batches.academic_year as academic_year'
            )
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id')
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id')
                ->leftJoin('internship_batches', 'internship_batches.id', '=', 'students.internship_batch_id');

            // Apply the search filter if provided
            if ($search) {
                $reportsQuery->whereHas('student', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            }

            // Apply the batch_name filter if provided
            if ($batchName) {
                $reportsQuery->whereHas('student.internshipBatch', function ($query) use ($batchName) {
                    $query->where('id', $batchName);
                });
            }

            $reports = $reportsQuery->get();
        } elseif ($role == 'mentor') {
            $mentorId = session('ses_mentor_id');
            $reports = Report::select(
                'reports.*',
                'grades.content',
                // 'grades.audio_visual',
                // 'grades.creativity_innovation',
                // 'grades.social_media_upload',
                // 'grades.adherence_to_guidelines',
                'grades.video_appearance',
                'students.mentor_id',
                'mentors.name as mentor_name',
                'students.class_code',
                'classes.name as class_name',
                'students.internship_place_code',
                'internship_places.name as dudi_name',
                'students.internship_batch_id',
                'internship_batches.batch_name as batch_name',
                'internship_batches.academic_year as academic_year'
            )
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id')
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id')
                ->leftJoin('classes', 'classes.code', '=', 'students.class_code')
                ->leftJoin('internship_places', 'internship_places.code', '=', 'students.internship_place_code')
                ->leftJoin('internship_batches', 'internship_batches.id', '=', 'students.internship_batch_id')
                ->where('students.mentor_id', $mentorId)
                ->get();
        }

        // Data untuk PDF
        $data = [
            'name' => $reports->isNotEmpty() ? $reports->first()->student->name : 'Unknown Siswa Prakerin',
            'mentor' => $reports->isNotEmpty() ? $reports->first()->mentor_name : 'Unknown Guru Pembimbing',
            'class' => $reports->isNotEmpty() ? $reports->first()->class_name : 'Unknown Kelas',
            'dudi' => $reports->isNotEmpty() ? $reports->first()->dudi_name : 'Unknown Dudi',
            'batch' => $reports->isNotEmpty()
                ? preg_replace('/[^0-9]/', '', $reports->first()->batch_name)
                : 'Unknown Batch',
            'year' => $reports->isNotEmpty() ? $reports->first()->academic_year : 'Unknown Tahun',
            'date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
            'reports' => $reports,
        ];

        // Menghasilkan PDF
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('grade.rekap', $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Landscape')
            ->setOption('margin-top', 20)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15)
            ->setOption('margin-bottom', 20);

        // Mengunduh PDF
        return $pdf->download('rekap_nilai.pdf');
    }

    public function exportRekap(Request $request)
    {
        $role = session('ses_role');

        if ($role == 'admin' || $role == 'super-admin') {

            $search = $request->get('search');
            $batchName = $request->get('batch_search');

            // Query untuk mengambil data laporan dengan relasi yang lebih kompleks
            $reportsQuery = Report::select(
                'reports.*',
                'grades.content',
                'grades.video_appearance',
                'grades.grade',
                'students.mentor_id',
                'students.id',
                'students.name as student_name',
                'mentors.name as mentor_name',
                'students.internship_batch_id',
                'internship_batches.batch_name as batch_name',
                'internship_batches.academic_year as academic_year'
            )
                ->leftJoin('grades', 'grades.report_id', '=', 'reports.id')
                ->leftJoin('students', 'students.id', '=', 'reports.student_id')
                ->leftJoin('mentors', 'mentors.id', '=', 'students.mentor_id')
                ->leftJoin('internship_batches', 'internship_batches.id', '=', 'students.internship_batch_id');

            // Apply the search filter if provided
            if ($search) {
                $reportsQuery->whereHas('student.user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            }

            // Apply the batch_name filter if provided
            if ($batchName) {
                $reportsQuery->whereHas('student.internshipBatch', function ($query) use ($batchName) {
                    $query->where('id', $batchName);
                });
            }

            $reports = $reportsQuery->get();

            // Cek apakah data kosong
            if ($reports->isEmpty()) {
                // Simpan pesan ke dalam session jika tidak ada data
                session()->flash('no_data', 'Tidak ada data dengan filter yang diberikan');
                return redirect()->back();
            }

            // Ambil semua minggu unik
            $weeks = [];
            foreach ($reports as $report) {
                if (preg_match('/Minggu (\d+)/', $report->report_title, $matches)) {
                    $week = (int)$matches[1];
                    if (!in_array($week, $weeks)) {
                        $weeks[] = $week;
                    }
                }
            }
            sort($weeks);

            // Persiapkan spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Menambahkan judul
            $academicYear = $reports->first()->academic_year ?? 'Tahun Akademik Tidak Diketahui'; // Ambil academic_year dari data pertama
            $batchText = $batchName ? (' - ' . $reports->first()->batch_name ?? ' - Semua Gelombang') : ' - Semua Gelombang';
            $sheet->mergeCells('A1:' . chr(70 + count($weeks)) . '1');  // Merge header untuk judul
            $sheet->setCellValue('A1', 'Rekap Nilai Prakerin SMK NU Al Hidayah TP. ' . $academicYear . $batchText);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            // Set baris header setelah judul
            $sheet->setCellValue('A2', 'No');
            $sheet->setCellValue('B2', 'Nama Siswa');
            $col = 'C';
            foreach ($weeks as $week) {
                $sheet->setCellValue($col . '2', 'Minggu ' . $week);
                $col++;
            }
            $sheet->setCellValue($col . '2', 'Rata-rata');

            // Mengatur teks header agar tebal (dari A2 sampai kolom terakhir)
            $lastColumn = chr(65 + count($weeks) + 2); // Memperbaiki perhitungan untuk kolom "Rata-rata"
            $sheet->getStyle('A2:' . $lastColumn . '2')->getFont()->setBold(true);

            // Menambahkan border pada header
            $sheet->getStyle('A2:' . $lastColumn . '2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Isi data per siswa
            $students = $reports->unique('student_id');
            $row = 3;  // Mulai dari baris 3 karena baris 1 adalah judul dan baris 2 adalah header
            $no = 1;

            foreach ($students as $report) {
                $student = $report->student;
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $student->name);

                $col = 'C';
                $total = 0;
                $count = 0;

                foreach ($weeks as $week) {
                    // Ambil laporan untuk minggu tertentu
                    $reportForWeek = $reports->first(function ($r) use ($student, $week) {
                        preg_match('/Minggu (\d+):/', $r->report_title, $matches);
                        $reportWeek = isset($matches[1]) ? (int)$matches[1] : null;
                        return $r->student_id == $student->id && $reportWeek == $week;
                    });

                    $grade = $reportForWeek ? $reportForWeek->grade : 0;
                    $sheet->setCellValue($col . $row, $grade);
                    $total += $grade;
                    $count++;
                    $col++;
                }

                // Hitung rata-rata
                $average = $count > 0 ? round($total / $count, 2) : 0;
                $sheet->setCellValue($col . $row, $average);

                $row++;
            }

            // Menambahkan border pada data siswa
            $lastRow = $row - 1; // Menentukan baris terakhir data siswa
            $lastColumn = chr(65 + count($weeks) + 2); // Kolom terakhir adalah "Rata-rata"
            $sheet->getStyle('A2:' . $lastColumn . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Menyesuaikan lebar kolom agar lebih enak dilihat
            foreach (range('A', chr(65 + count($weeks) + 1)) as $colLetter) {
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }

            // Simpan ke file sementara
            $filename = 'rekap_nilai.xlsx';
            $tempPath = storage_path($filename);
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // Kirim file untuk diunduh
            return response()->download($tempPath)->deleteFileAfterSend(true);
        }
    }
}
