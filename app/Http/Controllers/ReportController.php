<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * SIMPAN LAPORAN BARU
     */
    public function store(Request $request)
    {
        $studentId = session('ses_student_id');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Session siswa tidak ditemukan. Silakan login kembali.'
            ], 401);
        }

        $validated = $request->validate([
            'report_title' => 'required|string|min:5|max:255',
            'link1' => 'required|url|max:1000',
        ], [
            'report_title.required' => 'Judul laporan tidak ditemukan.',
            'report_title.min' => 'Judul laporan minimal 5 karakter.',
            'link1.required' => 'Link laporan wajib diisi.',
            'link1.url' => 'Link laporan harus berupa URL yang valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CEK APAKAH LAPORAN SUDAH PERNAH DIUPLOAD
        |--------------------------------------------------------------------------
        */
        $existingReport = Report::where('student_id', $studentId)
            ->where('report_title', $validated['report_title'])
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan minggu ini sudah pernah diupload.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN LAPORAN
        |--------------------------------------------------------------------------
        */
        $report = new Report();

        $report->student_id = $studentId;
        $report->report_title = $validated['report_title'];
        $report->report_link1 = $validated['link1'];
        $report->report_date = now();
        $report->report_status = 'Sudah Upload';

        $report->save();

        /*
        |--------------------------------------------------------------------------
        | DATA SISWA
        |--------------------------------------------------------------------------
        */
        $student = Student::find($studentId);

        /*
        |--------------------------------------------------------------------------
        | KIRIM TELEGRAM
        |--------------------------------------------------------------------------
        */
        if ($student) {
            $mentor = $student->mentor;

            if ($mentor) {
                $this->sendTelegramNotification(
                    $mentor,
                    $student,
                    $report,
                    'create'
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim.',
            'data' => [
                'id' => $report->id,
                'report_title' => $report->report_title,
                'report_link1' => $report->report_link1,
                'report_status' => $report->report_status,
            ]
        ], 200);
    }


    /**
     * AMBIL LAPORAN UNTUK EDIT
     */
    public function getReportData(Request $request)
    {
        $studentId = session('ses_student_id');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Session siswa tidak ditemukan.'
            ], 401);
        }

        $request->validate([
            'report_title' => 'required|string',
        ]);

        $reportTitle = $request->input('report_title');

        $report = Report::where('report_title', $reportTitle)
            ->where('student_id', $studentId)
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,

            'data' => [
                'id' => $report->id,
                'report_title' => $report->report_title,
                'link1' => $report->report_link1 ?? '',
            ]
        ], 200);
    }


    /**
     * EDIT LAPORAN
     */
    public function edit(Request $request)
    {
        $studentId = session('ses_student_id');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Session siswa tidak ditemukan. Silakan login kembali.'
            ], 401);
        }

        $validated = $request->validate([
            'report_title' => 'required|string|min:5|max:255',
            'link1' => 'required|url|max:1000',
        ], [
            'report_title.required' => 'Judul laporan tidak ditemukan.',
            'link1.required' => 'Link laporan wajib diisi.',
            'link1.url' => 'Link laporan harus berupa URL yang valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CARI LAPORAN
        |--------------------------------------------------------------------------
        */
        $report = Report::where('report_title', $validated['report_title'])
            ->where('student_id', $studentId)
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */
        $report->report_link1 = $validated['link1'];
        $report->report_date = now();
        $report->report_status = 'Sudah Upload';

        $report->save();

        /*
        |--------------------------------------------------------------------------
        | DATA SISWA
        |--------------------------------------------------------------------------
        */
        $student = Student::find($studentId);

        /*
        |--------------------------------------------------------------------------
        | KIRIM TELEGRAM
        |--------------------------------------------------------------------------
        */
        if ($student) {
            $mentor = $student->mentor;

            if ($mentor) {
                $this->sendTelegramNotification(
                    $mentor,
                    $student,
                    $report,
                    'update'
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui.',
            'data' => [
                'id' => $report->id,
                'report_title' => $report->report_title,
                'report_link1' => $report->report_link1,
            ]
        ], 200);
    }


    /**
     * KIRIM NOTIFIKASI TELEGRAM
     */
    private function sendTelegramNotification(
        $mentor,
        $student,
        $report,
        $type = 'create'
    ) {
        try {

            $botToken = env('TELEGRAM_BOT_TOKEN');

            if (!$botToken) {
                Log::warning('TELEGRAM_BOT_TOKEN belum diatur.');
                return;
            }

            $chatId = $mentor->telegram_number ?? null;

            /*
            |--------------------------------------------------------------------------
            | JIKA MENTOR BELUM PUNYA TELEGRAM
            |--------------------------------------------------------------------------
            */
            if (!$chatId) {
                Log::warning(
                    'Telegram mentor tidak ditemukan untuk mentor ID: '
                        . ($mentor->id ?? '-')
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | TEMPAT PRAKERIN
            |--------------------------------------------------------------------------
            */
            $internshipPlaceName =
                $student->internshipPlace
                ? $student->internshipPlace->name
                : 'N/A';

            /*
            |--------------------------------------------------------------------------
            | JUDUL PESAN
            |--------------------------------------------------------------------------
            */
            if ($type === 'create') {
                $message = "📌 *Laporan Baru Diupload*\n\n";
            } else {
                $message = "✏️ *Laporan Diedit*\n\n";
            }

            /*
            |--------------------------------------------------------------------------
            | ISI PESAN
            |--------------------------------------------------------------------------
            */
            $message .= "👨‍🎓 *Nama Siswa:* {$student->name}\n";
            $message .= "🏢 *DUDI:* {$internshipPlaceName}\n";
            $message .= "📄 *Laporan:* {$report->report_title}\n";
            $message .= "📅 *Tanggal:* "
                . $report->updated_at->format('d-m-Y H:i:s')
                . "\n\n";

            $message .= "🔗 *Link Laporan:*\n";
            $message .= $report->report_link1;


            /*
            |--------------------------------------------------------------------------
            | KIRIM TELEGRAM
            |--------------------------------------------------------------------------
            */
            $response = Http::timeout(10)->get(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | LOG JIKA GAGAL
            |--------------------------------------------------------------------------
            */
            if ($response->failed()) {
                Log::error(
                    'Gagal mengirim Telegram',
                    [
                        'response' => $response->body()
                    ]
                );
            }
        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | TELEGRAM ERROR TIDAK BOLEH MEMBUAT UPLOAD LAPORAN GAGAL
            |--------------------------------------------------------------------------
            */
            Log::error(
                'Telegram Notification Error: ' . $e->getMessage()
            );
        }
    }


    /**
     * DETAIL LAPORAN SISWA
     */
    public function getReportDetails($studentId)
    {
        $reports = Report::with(['student.mentor'])
            ->where('student_id', $studentId)
            ->orderBy('id', 'asc')
            ->get();

        $html = view(
            'report.report_table',
            compact('reports')
        )->render();

        return response()->json([
            'html' => $html
        ]);
    }


    /**
     * CEK LAPORAN BERDASARKAN TANGGAL
     */
    public function checkReport($date)
    {
        $studentId = session('ses_student_id');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Student ID tidak ditemukan dalam session.',
            ], 400);
        }

        $report = Report::whereDate('report_date', $date)
            ->where('student_id', $studentId)
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,

            'report' => [
                'report_id' => $report->id,
                'report_date' => $report->report_date,
                'report_title' => $report->report_title,
                'report_link1' => $report->report_link1,
                'report_link2' => $report->report_link2 ?? '',
                'report_link3' => $report->report_link3 ?? '',
                'report_status' => $report->report_status,
            ],
        ]);
    }
}
