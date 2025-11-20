<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{

    public function store(Request $request)
    {
        $studentId = session('ses_student_id');

        // Validasi input
        $validated = $request->validate([
            'report_title' => 'required|min:5',
            'link1' => 'required|url', // Facebook link
            // 'link2' => 'nullable|url', // Instagram link
            // 'link3' => 'nullable|url', // TikTok link
        ]);

        // Simpan data laporan ke database
        $report = new Report();
        $report->student_id = $studentId;
        $report->report_title = $validated['report_title'];
        $report->report_link1 = $validated['link1'];
        // $report->report_link2 = $validated['link2'];
        // $report->report_link3 = $validated['link3'];
        $report->report_date = date('Y-m-d H:i:s');
        $report->report_status = "Sudah Upload";
        $report->save();

        // Ambil data mahasiswa
        $student = Student::find($studentId);

        // Ambil data mentor berdasarkan student
        $mentor = $student->mentor;  // Asumsikan ada relasi antara Student dan Mentor

        // Kirim notifikasi Telegram
        $this->sendTelegramNotification($mentor, $student, $report, 'create');

        // Kembalikan response sukses
        return response()->json(['success' => 'Berhasil'], 200);
    }

    public function getReportData(Request $request)
    {
        // Fetch report data based on the provided report title
        $reportTitle = $request->input('report_title');
        $studentId = session('ses_student_id');

        // Retrieve the report for the student with the matching report title
        $report = Report::where('report_title', $reportTitle)
            ->where('student_id', $studentId)
            ->first();

        // Check if the report exists
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        // Return the report data as JSON for editing
        return response()->json([
            'data' => [
                'link1' => $report->report_link1 ?? "",
                // 'link2' => $report->report_link2 ?? "",
                // 'link3' => $report->report_link3 ?? "",
            ]
        ], 200);
    }

    public function edit(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'report_title' => 'required|min:5',
            'link1' => 'required|url',  // Facebook link
            // 'link2' => 'nullable|url',  // Instagram link
            // 'link3' => 'nullable|url',  // TikTok link
        ]);

        // Retrieve the report data based on the report_title and student_id
        $studentId = session('ses_student_id');
        $report = Report::where('report_title', $request->input('report_title'))
            ->where('student_id', $studentId)
            ->first();

        // Check if the report exists
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        // Update the report with the new links
        $report->report_title = $validated['report_title'];
        $report->report_link1 = $validated['link1'];
        // $report->report_link2 = $validated['link2'];
        // $report->report_link3 = $validated['link3'];
        $report->report_date = date('Y-m-d H:i:s');
        $report->report_status = 'Sudah Upload';  // Update the status to 'Sudah Upload'
        $report->save();

        // Ambil data mahasiswa
        $student = Student::find($studentId);

        // Ambil data mentor berdasarkan student
        $mentor = $student->mentor;  // Asumsikan ada relasi antara Student dan Mentor

        // Kirim notifikasi Telegram
        $this->sendTelegramNotification($mentor, $student, $report, 'update');

        // Return a success response
        return response()->json(['success' => 'Report successfully updated'], 200);
    }

    private function sendTelegramNotification($mentor, $student, $report, $type = 'create')
    {
        // Token API bot Telegram
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Pastikan Anda menyimpan token bot di .env file
        // Chat ID mentor
        $chatId = $mentor->telegram_number;  // Pastikan Anda menyimpan chat_id mentor

        // Cek apakah chat_id ada
        if (!$chatId) {
            return response()->json(['error' => 'Chat ID mentor tidak ditemukan'], 400);
        }

        // Ambil nama internship place jika tersedia
        $internshipPlaceName = $student->internshipPlace ? $student->internshipPlace->name : 'N/A';

        // Format pesan
        if ($type == 'create') {
            $message = "📌 *Laporan Baru Diupload*\n";
        } else {
            $message = "✏️ *Laporan Diedit*\n";
        }

        $message .= "👨‍🎓 *Nama Siswa*: " . $student->name . "\n";
        $message .= "🏷️ *Dudi*: " . $internshipPlaceName . "\n"; // Menambahkan informasi Internship
        $message .= "📄 *Judul Laporan*: " . $report->report_title . "\n";
        $message .= "📅 *Tanggal Laporan*: " . $report->updated_at->format('d-m-Y H:i:s') . "\n";
        $message .= "🔗 *Link Laporan (Sosmed)*: " . $report->report_link1 . "\n";

        // Mengirimkan pesan ke Telegram
        $response = Http::get("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',  // Format pesan menggunakan Markdown
        ]);

        // Cek apakah pengiriman pesan berhasil
        if ($response->failed()) {
            return response()->json(['error' => 'Gagal mengirim notifikasi Telegram'], 500);
        }

        return response()->json(['message' => 'Notifikasi Telegram berhasil dikirim']);
    }

    // Method to update an existing report
    public function update(Request $request, $id)
    {
        // Find the report by ID
        $report = Report::find($id);

        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan.'], 404);
        }

        // Validate the incoming data
        $validatedData = $request->validate([
            'title' => 'required|string|min:5',
            'link1' => 'required|url|regex:/^https:\/\/(www\.)?facebook\.com\/(.*)$/',
            'link2' => 'nullable|url|regex:/^https:\/\/www\.instagram\.com\/p\/[A-Za-z0-9_-]+$/',
            'link3' => 'nullable|url|regex:/^https:\/\/www\.tiktok\.com\/@([A-Za-z0-9_-]+)\/video\/\d+$/',
            'date' => 'required|date',
        ]);

        // Update the report
        $report->report_title = $validatedData['title'];
        $report->report_link1 = $validatedData['link1'];
        $report->report_link2 = $validatedData['link2'];
        $report->report_link3 = $validatedData['link3'];
        $report->report_date = $validatedData['date'];
        $report->save();

        // Respond with success
        return response()->json(['message' => 'Laporan berhasil diperbarui.'], 200);
    }

    public function getReportDetails($studentId)
    {
        // Ambil laporan untuk student_id tertentu
        $reports = Report::with(['student.mentor'])
            ->where('student_id', $studentId)
            ->orderBy('id', 'asc')
            ->get();

        // Generate HTML tabel untuk laporan yang diminta
        $html = view('report.report_table', compact('reports'))->render();

        return response()->json(['html' => $html]);
    }

    public function checkReport($date)
    {
        // Ambil student_id dari session
        $studentId = session('ses_student_id');

        // Validasi student_id
        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Student ID tidak ditemukan dalam session.',
            ], 400); // Bad Request
        }

        // Cari laporan berdasarkan tanggal dan student_id
        $report = Report::whereDate('report_date', $date)
            ->where('student_id', $studentId)
            ->first();

        if ($report) {
            // Jika laporan ditemukan, kembalikan data laporan
            return response()->json([
                'success' => true,
                'report' => [
                    'report_id' => $report->id,
                    'report_date' => $report->report_date,
                    'report_title' => $report->report_title,
                    'report_link1' => $report->report_link1,
                    'report_link2' => $report->report_link2 ?? "",
                    'report_link3' => $report->report_link3 ?? "",
                    'report_status' => $report->report_status, // Pastikan kolom status ada di tabel
                ],
            ]);
        }
    }
}
