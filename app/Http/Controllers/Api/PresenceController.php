<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Report;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

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
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

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

    public function outsideUpload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
            'latitude' => 'required',
            'longitude' => 'required',
            'timestamp' => 'required',
        ]);

        $currentTime = Carbon::now();

        $student = Auth::user()->student;

        if (!$student) {
            return response()->json([
                'error' => 'Data siswa tidak ditemukan untuk user ini'
            ], 404);
        }

        // 🔥 CARI PRESENSI HARI INI
        $presence = Presence::where('student_id', $student->id)
            ->whereDate('check_in', Carbon::today())
            ->first();

        // 🔥 UPLOAD FILE
        $path = $request->file('photo')->store('presensi', 'public');

        // ===========================
        // CASE 1: BELUM CHECK-IN → AUTO CREATE
        // ===========================
        if (!$presence) {

            $presence = Presence::create([
                'student_id' => $student->id,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in_location_link' => "https://www.google.com/maps?q={$request->latitude},{$request->longitude}",
                'check_in' => $currentTime,

                // langsung simpan bukti
                'proof_photo' => $path,
                'note' => 'outside_radius',
            ]);
        }
        // ===========================
        // CASE 2: SUDAH ADA → UPDATE
        // ===========================
        else {

            $presence->update([
                'check_out_latitude' => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'check_out_location_link' => "https://www.google.com/maps?q={$request->latitude},{$request->longitude}",
                'check_out' => $currentTime,
                'proof_photo' => $path,
                'note' => 'outside_radius',
            ]);
        }

        // optional telegram notif
        $mentor = $student->mentor ?? null;
        if ($mentor) {
            $this->sendTelegramNotification($mentor, $student, $presence);
        }

        return response()->json([
            'message' => 'Bukti presensi berhasil dikirim',
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
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
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
            'minggu' => 'required|integer|between:1,12', // bisa ubah sesuai jumlah minggu
            'video_link' => 'required|url',
        ]);

        // Buat report_title otomatis
        $reportTitle = "Minggu " . $validated['minggu'] . ": Upload Laporan";

        // Cek apakah laporan sudah ada
        $report = Report::firstOrNew([
            'student_id' => $student->id,
            'report_title' => $reportTitle,
        ]);

        // Set/update data laporan
        $report->report_link1 = $validated['video_link'];
        $report->report_date = now();
        $report->report_status = "Sudah Upload";
        $report->save();

        return response()->json([
            'success' => "Link video {$reportTitle} berhasil disimpan"
        ], 200);
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
        // if (in_array($today, ['2025-10-29', '2025-10-30', '2025-11-05', '2025-11-06']) || $isSaturday) {
        //     $lat = -6.7641133;
        //     $lng = 110.8042769;
        //     $radius = 300;
        // } else {
        $lat = $place->latitude;
        $lng = $place->longitude;
        $radius = 300;
        // }

        return response()->json([
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
        ]);
    }

    public function checkStatus(Request $request)
    {
        $today = date('Y-m-d');

        $studentId = Student::where('user_id', $request->user()->id)
            ->value('id');

        $presence = Presence::where('student_id', $studentId)
            ->whereDate('created_at', $today)
            ->first();

        if (!$presence) {
            return response()->json([
                'status' => 'Masuk'
            ]);
        }

        if ($presence->check_in && !$presence->check_out) {
            return response()->json([
                'status' => 'Pulang'
            ]);
        }

        if ($presence->check_in && $presence->check_out) {
            return response()->json([
                'status' => 'Selesai'
            ]);
        }

        return response()->json([
            'status' => 'Masuk'
        ]);
    }

    // public function historyAll(Request $request)
    // {
    //     $search = $request->input('search');
    //     $dateFrom = $request->input('date_from');
    //     $dateTo = $request->input('date_to');

    //     // Query untuk mengambil semua presensi dari semua siswa
    //     $query = Presence::query()
    //         ->with('student:id,name') // Mengambil relasi nama siswa agar informatif
    //         ->orderBy('check_in', 'desc');

    //     // Filter berdasarkan nama siswa (via relasi) atau waktu
    //     if ($search) {
    //         $query->whereHas('student', function ($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%");
    //         });
    //     }

    //     if ($dateFrom && $dateTo) {
    //         $query->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
    //     } elseif ($dateFrom) {
    //         $query->where('check_in', '>=', $dateFrom . ' 00:00:00');
    //     } elseif ($dateTo) {
    //         $query->where('check_in', '<=', $dateTo . ' 23:59:59');
    //     }

    //     $presences = $query->get();

    //     $result = $presences->flatMap(function ($item) {
    //         $list = [];
    //         $tanggal = \Carbon\Carbon::parse($item->check_in)->format('Y-m-d');
    //         $namaSiswa = $item->student ? $item->student->name : 'Unknown';

    //         if ($item->check_in) {
    //             $list[] = [
    //                 'id'         => $item->id . '_in',
    //                 'siswa'      => $namaSiswa,
    //                 'date'       => $tanggal,
    //                 'time'       => \Carbon\Carbon::parse($item->check_in)->format('H:i:s'),
    //                 'type'       => 'Masuk',
    //             ];
    //         }

    //         if ($item->check_out) {
    //             $list[] = [
    //                 'id'         => $item->id . '_out',
    //                 'siswa'      => $namaSiswa,
    //                 'date'       => $tanggal,
    //                 'time'       => \Carbon\Carbon::parse($item->check_out)->format('H:i:s'),
    //                 'type'       => 'Pulang',
    //             ];
    //         }

    //         return $list;
    //     })->values();

    //     return response()->json([
    //         'status'  => 'success',
    //         'message' => 'Data riwayat seluruh siswa berhasil diambil',
    //         'data'    => $result,
    //     ]);
    // }

    public function historyAll(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Query utama mengambil presensi beserta relasinya
        $query = Presence::query()
            ->with(['student.internshipPlace', 'student.internshipBatch'])
            ->whereHas('student.internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
            ->orderBy('check_in', 'desc');

        // Filter berdasarkan pencarian nama siswa jika ada
        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter Rentang Tanggal
        if ($dateFrom && $dateTo) {
            $query->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        } elseif ($dateFrom) {
            $query->where('check_in', '>=', $dateFrom . ' 00:00:00');
        } elseif ($dateTo) {
            $query->where('check_in', '<=', $dateTo . ' 23:59:59');
        }

        $presences = $query->get();

        $result = $presences->flatMap(function ($item) {
            $list = [];
            $tanggal = \Carbon\Carbon::parse($item->check_in)->format('Y-m-d');
            $namaSiswa = $item->student ? $item->student->name : 'Unknown';

            // Mengambil nama instansi berdasarkan kolom 'name' di tabel internship_places
            $dudi = ($item->student && $item->student->internshipPlace)
                ? $item->student->internshipPlace->name
                : 'Tidak Ada DU/DI';

            if ($item->check_in) {
                $list[] = [
                    'id'         => $item->id . '_in',
                    'siswa'      => $namaSiswa,
                    'dudi'       => $dudi, // Berhasil terisi nama (contoh: "Atique Design", "AISAH KOMPUTER")
                    'date'       => $tanggal,
                    'time'       => \Carbon\Carbon::parse($item->check_in)->format('H:i:s'),
                    'type'       => 'Masuk',
                ];
            }

            if ($item->check_out) {
                $list[] = [
                    'id'         => $item->id . '_out',
                    'siswa'      => $namaSiswa,
                    'dudi'       => $dudi,
                    'date'       => $tanggal,
                    'time'       => \Carbon\Carbon::parse($item->check_out)->format('H:i:s'),
                    'type'       => 'Pulang',
                ];
            }

            return $list;
        })->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data riwayat seluruh siswa berhasil diambil',
            'data'    => $result,
        ]);
    }

    public function presenceToday(Request $request)
    {
        $search = $request->input('search');
        $today = \Carbon\Carbon::today();

        $query = Presence::query()
            ->with([
                'student' => function ($q) {
                    $q->select('id', 'user_id', 'name', 'class_code', 'internship_place_code', 'internship_batch_id');
                },
                'student.user:id,foto_url', // Eager load user untuk ambil foto
                'student.class',
                'student.internshipPlace',
                'student.internshipBatch'
            ])
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'desc');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $presences = $query->get();

        $result = $presences->flatMap(function ($item) {
            $list = [];
            $siswa = $item->student;

            // Ambil foto dari relasi user
            $fotoUrl = ($siswa && $siswa->user && $siswa->user->foto_url)
                ? asset('storage/' . $siswa->user->foto_url)
                : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->name ?? 'Unknown');

            // Persiapkan data dasar untuk setiap log
            $commonData = [
                'siswa' => $siswa->name ?? 'Unknown',
                'fotoUrl' => $fotoUrl,
                'kelas' => $siswa->class->name ?? $siswa->class_code ?? '-',
                'dudi'  => $siswa->internshipPlace->name ?? '-',
                'batch' => $siswa->internshipBatch->batch_name ?? '-',
            ];

            // Log Masuk
            if ($item->check_in) {
                $list[] = array_merge($commonData, [
                    'id'            => $item->id . '_in',
                    'time'          => \Carbon\Carbon::parse($item->check_in)->format('H:i:s'),
                    'type'          => 'Masuk',
                    'latitude'      => $item->check_in_latitude,
                    'longitude'     => $item->check_in_longitude,
                    'location_link' => $item->check_in_location_link,
                ]);
            }

            // Log Pulang
            if ($item->check_out) {
                $list[] = array_merge($commonData, [
                    'id'            => $item->id . '_out',
                    'time'          => \Carbon\Carbon::parse($item->check_out)->format('H:i:s'),
                    'type'          => 'Pulang',
                    'latitude'      => $item->check_out_latitude,
                    'longitude'     => $item->check_out_longitude,
                    'location_link' => $item->check_out_location_link,
                ]);
            }

            return $list;
        })->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data riwayat hari ini berhasil diambil',
            'data'    => $result,
        ]);
    }

    public function storePresensiManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'keterangan' => 'required|in:present,sick,permission,absent,holiday',
            'tanggal'    => 'required|date',
            'note'       => 'nullable|string|max:255', // Validasi catatan
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
        }

        try {
            // 1. Ambil data siswa untuk mengetahui tempat magangnya
            $student = \App\Models\Student::find($request->student_id);

            // Asumsi: siswa memiliki relasi atau kolom 'internship_place_code'
            // Sesuaikan dengan nama kolom di database Anda
            $place = \App\Models\InternshipPlace::where('code', $student->internship_place_code)->first();

            // 2. Cek duplikasi presensi
            $tanggalInput = Carbon::parse($request->tanggal)->format('Y-m-d');
            $exists = Presence::where('student_id', $request->student_id)
                ->whereDate('check_in', $tanggalInput)
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Siswa sudah melakukan presensi hari ini.'], 409);
            }

            // 3. Simpan dengan koordinat dari DB
            $waktu = $tanggalInput . ' ' . date('H:i:s');

            // Gabungkan catatan input dengan keterangan tambahan
            $finalNote = $request->note ? "Manual: " . $request->note : "Input manual oleh mentor";

            Presence::create([
                'student_id'          => $request->student_id,
                'status'              => $request->keterangan,
                'check_in'            => $waktu,
                'check_out'           => $waktu,
                'check_in_latitude'   => $place ? $place->latitude : null,  // Ambil dari DB
                'check_in_longitude'  => $place ? $place->longitude : null, // Ambil dari DB
                'note'                => $finalNote, // Simpan catatan
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            return response()->json(['message' => 'Presensi manual berhasil disimpan.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan.', 'error' => $e->getMessage()], 500);
        }
    }
}
