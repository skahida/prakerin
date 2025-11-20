<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PresenceController extends Controller
{
    public function todayPresence()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        $today = Carbon::today();

        $presence = \App\Models\Presence::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->first();

        return response()->json([
            'presence' => $presence
        ]);
    }

    public function checkIn(Request $request)
    {
        $latitude = $request->input('check_in_latitude');
        $longitude = $request->input('check_in_longitude');

        if (!$latitude || !$longitude) {
            return response()->json(['error' => 'Data geolocation tidak ditemukan'], 400);
        }

        $currentTime = Carbon::now();

        // if ($currentTime->hour < 7 || $currentTime->hour > 22) {
        //     return response()->json([
        //         'error' => 'Presensi hanya dapat dilakukan antara jam 7 pagi hingga 10 malam'
        //     ], 400);
        // }

        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        $presence = Presence::create([
            'student_id' => $student->id,
            'check_in_latitude' => $latitude,
            'check_in_longitude' => $longitude,
            'check_in_location_link' => "https://www.google.com/maps?q=$latitude,$longitude",
            'check_in' => $currentTime,
        ]);

        // ✅ Panggil notifikasi ke Telegram (jika mentor tersedia)
        $mentor = $student->mentor; // pastikan relasi mentor tersedia

        if ($mentor) {
            $this->sendTelegramNotification($mentor, $student, $presence);
        }

        return response()->json([
            'message' => 'Presensi masuk berhasil',
            'data' => $presence,
        ]);
    }

    private function sendTelegramNotification($mentor, $student, $presence)
    {
        // Token API bot Telegram
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Pastikan Anda menyimpan token bot di .env file
        // Chat ID mentor (misalnya, dapat disimpan di database atau di session)
        $chatId = $mentor->telegram_number;  // Pastikan Anda menyimpan chat_id mentor

        // Cek apakah chat_id ada
        if (!$chatId) {
            return response()->json(['error' => 'Chat ID mentor tidak ditemukan'], 400);
        }

        // Ambil nama internship place jika tersedia
        $internshipPlaceName = $student->internshipPlace ? $student->internshipPlace->name : 'N/A'; // Jika tidak ada, tampilkan 'N/A'

        // Format pesan
        $message = "📌 *Presensi Masuk*\n";
        $message .= "👨‍🎓 *Nama Siswa*: " . $student->name . "\n";
        $message .= "🏷️ *Dudi*: " . $internshipPlaceName . "\n"; // Menambahkan informasi Internship
        $message .= "📅 *Status Presensi*: Masuk\n";
        $message .= "⏰ *Waktu Presensi*: " . $presence->check_in->format('d-m-Y H:i:s') . "\n";
        $message .= "📍 *Lokasi*: [Google Maps](" . $presence->check_in_location_link . ")\n";

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

    public function checkOut(Request $request)
    {
        $latitude = $request->input('check_out_latitude');
        $longitude = $request->input('check_out_longitude');
        $check_out_location_link = "https://www.google.com/maps?q=";

        if ($latitude === null || $longitude === null) {
            return response()->json(['error' => 'Data geolocation tidak ditemukan'], 400);
        }

        // Ambil user yang sedang login (pastikan pakai guard yang benar)
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        $today = Carbon::today();

        $presence = Presence::where('student_id', $student->id)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->whereDate('check_in', $today)
            ->first();

        if (!$presence) {
            return response()->json([
                'error' => 'Anda belum melakukan presensi masuk atau sudah melakukan presensi pulang'
            ], 400);
        }

        // Update checkout
        $presence->update([
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_location_link' => $check_out_location_link . "$latitude,$longitude",
            'check_out' => now(),
        ]);

        // Ambil ulang student dengan relasi
        $studentWithRelations = $student->load('internshipPlace', 'mentor');

        if (!$studentWithRelations->mentor) {
            return response()->json(['error' => 'Mentor tidak ditemukan untuk siswa ini'], 400);
        }

        // ✅ Panggil notifikasi ke Telegram (jika mentor tersedia)
        $mentor = $student->mentor; // pastikan relasi mentor tersedia

        if ($mentor) {
            $this->sendTelegramNotificationCheckOut($mentor, $student, $presence);
        }

        return response()->json([
            'message' => 'Presensi pulang berhasil',
            'data' => $presence
        ]);
    }

    private function sendTelegramNotificationCheckOut($mentor, $student, $presence)
    {
        // Token API bot Telegram
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Pastikan Anda menyimpan token bot di .env file
        // Chat ID mentor (misalnya, dapat disimpan di database atau di session)
        $chatId = $mentor->telegram_number;  // Pastikan Anda menyimpan chat_id mentor

        // Ambil nama internship place jika tersedia
        $internshipPlaceName = $student->internshipPlace ? $student->internshipPlace->name : 'N/A'; // Jika tidak ada, tampilkan 'N/A'

        // Format pesan untuk check-out
        $message = "📌 *Presensi Pulang*\n";
        $message .= "👨‍🎓 *Nama Siswa*: " . $student->name . "\n";
        $message .= "🏷️ *Dudi*: " . $internshipPlaceName . "\n"; // Menambahkan informasi Batch/Internship
        $message .= "📅 *Status Presensi*: Pulang\n";
        $message .= "⏰ *Waktu Presensi*: " . $presence->check_out->format('d-m-Y H:i:s') . "\n";
        $message .= "📍 *Lokasi*: [Google Maps](" . $presence->check_out_location_link . ")\n";

        // Mengirimkan pesan ke Telegram
        Http::get("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',  // Format pesan menggunakan Markdown
        ]);
    }

    public function history(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Presence::where('student_id', $student->id)
            ->orderBy('check_in', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('check_in', 'like', "%{$search}%")
                    ->orWhere('check_out', 'like', "%{$search}%");
            });
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('check_in', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('check_in', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('check_in', '<=', $dateTo);
        }

        $presences = $query->get();

        $result = [];

        foreach ($presences as $item) {
            $tanggal = \Carbon\Carbon::parse($item->check_in)->format('Y-m-d');

            if ($item->check_in) {
                $result[] = [
                    'id' => $item->id . '_in',
                    'tanggal' => $tanggal,
                    'waktu' => \Carbon\Carbon::parse($item->check_in)->format('H:i:s'), // tambahkan detik
                    'tipe' => 'Masuk',
                ];
            }

            if ($item->check_out) {
                $result[] = [
                    'id' => $item->id . '_out',
                    'tanggal' => $tanggal,
                    'waktu' => \Carbon\Carbon::parse($item->check_out)->format('H:i:s'), // tambahkan detik
                    'tipe' => 'Pulang',
                ];
            }
        }


        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function getUploadsByWeeks()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        // Ambil semua laporan milik student, batasi hanya 12 minggu
        $reports = Report::where('student_id', $student->id)
            ->where(function ($query) {
                for ($i = 1; $i <= 12; $i++) {
                    $query->orWhere('report_title', 'like', "Minggu $i%");
                }
            })
            ->get()
            ->mapWithKeys(function ($item) {
                preg_match('/Minggu (\d+)/', $item->report_title, $match);
                $week = isset($match[1]) ? (int)$match[1] : null;
                return $week ? [$week => $item] : [];
            });

        $result = [];

        // Loop 1-12 minggu, isi data kalau ada, kalau gak ada null
        for ($i = 1; $i <= 12; $i++) {
            $report = $reports->get($i);

            $result[] = [
                'minggu' => $i,
                'video_link' => $report ? $report->report_link1 : null,
            ];
        }

        return response()->json($result, 200);
    }



    public function store(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        // Validasi input
        $validated = $request->validate([
            'minggu' => 'required|integer|between:1,12',
            'video_link' => 'required|url',
        ]);

        // Buat report_title berdasarkan minggu
        $reportTitle = "Minggu " . $validated['minggu'];

        // Cari laporan sudah ada untuk student dan minggu itu
        $report = Report::where('student_id', $student->id)
            ->where('report_title', $reportTitle)
            ->first();

        if ($report) {
            // Update link video dan tanggal update
            $report->report_link1 = $validated['video_link'];
            $report->report_date = now();
            $report->report_status = "Sudah Upload";
            $report->save();
        } else {
            // Buat baru
            $report = new Report();
            $report->student_id = $student->id;
            $report->report_title = $reportTitle;
            $report->report_link1 = $validated['video_link'];
            $report->report_date = now();
            $report->report_status = "Sudah Upload";
            $report->save();
        }

        // (Opsional) Kirim notifikasi atau proses lain di sini...

        return response()->json(['success' => 'Link video minggu ke-' . $validated['minggu'] . ' berhasil disimpan'], 200);
    }

    public function lokasi(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan untuk user ini'], 404);
        }

        // Load relasi internshipPlace
        $student->load('internshipPlace');

        if (!$student->internshipPlace) {
            return response()->json([
                'message' => 'Data lokasi tidak ditemukan'
            ], 404);
        }

        $place = $student->internshipPlace;

        // Ambil tanggal dan hari ini
        $today = now()->format('Y-m-d');
        $isSaturday = now()->isSaturday();

        // Jika tanggal 29-30 Okt 2025 atau hari Sabtu
        if (in_array($today, ['2025-10-29', '2025-10-30', '2025-11-05', '2025-11-06']) || $isSaturday) {
            $lat = -6.7641133;
            $lng = 110.8042769;
            $radius = 300;
        } else {
            $lat = $place->latitude;
            $lng = $place->longitude;
            $radius = 300;
        }

        return response()->json([
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
        ]);
    }
}
