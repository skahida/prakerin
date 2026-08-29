<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\MentorController;
use App\Http\Controllers\Api\MonitoringApiController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/acheck-status', [PresenceController::class, 'checkStatus']);


// Route::get('/presence/atoday', [PresenceController::class, 'presenceToday']);

Route::get('/live-prakerin', [App\Http\Controllers\Api\MonitoringApiController::class, 'getStats']);
Route::get('/riwayat/presensi', [PresenceController::class, 'historyAll']);

Route::get(
    '/video',
    [MonitoringApiController::class, 'getVideos']
);

Route::get(
    '/video/{report}/thumbnail',
    [MonitoringApiController::class, 'thumbnail']
)
    ->whereNumber('report');

Route::middleware('auth.token')->get('/user-info', function (Request $request) {
    $user = $request->user();

    // ==== ROLE STUDENT ====
    if ($user->role === 'student') {
        $student = $user->student()->with([
            'class',
            'internshipPlace',
            'internshipBatch',
            'mentor',
        ])->first();

        if (!$student) {
            return response()->json(['message' => 'Data student tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Berhasil akses profile (student)',
            'role' => 'student',
            'user' => [
                'id' => $student->id,
                'nama' => $student->name,
                'jekel' => $student->gender,
                'kelas' => optional($student->class)->name,
                'perusahaan' => optional($student->internshipPlace)->name,
                'gelombang' => optional($student->internshipBatch)->name,
                'pembimbing' => optional($student->mentor)->name,
                'fotoUrl' => $user->foto_url ? asset('storage/' . $user->foto_url) : null,
                'status_batch' => optional($student->internshipBatch)->status_batch,
                'batch_name' => optional($student->internshipBatch)->batch_name,
                'academic_year' => optional($student->internshipBatch)->academic_year,
            ]
        ]);
    }

    // ==== ROLE MENTOR ====
    if ($user->role === 'mentor') {
        $mentor = $user->mentor()->with([
            'students.class',
            'students.internshipPlace',
            'students.internshipBatch'
        ])->first();

        if (!$mentor) {
            return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Berhasil akses profile (mentor)',
            'role' => 'mentor',
            'user' => [
                'id' => $mentor->id,
                'nama' => $mentor->name,
                'no_hp' => $mentor->phone,
                'jabatan' => $mentor->position,
                'fotoUrl' => $user->foto_url ? asset('storage/' . $user->foto_url) : null,

                // daftar siswa yang dibimbing
                'students' => $mentor->students->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->name,
                        'kelas' => optional($s->class)->name,
                        'perusahaan' => optional($s->internshipPlace)->name,
                        'gelombang' => optional($s->internshipBatch)->name,
                    ];
                }),
            ]
        ]);
    }

    // ==== ROLE LAIN (opsional) ====
    return response()->json([
        'message' => 'Role tidak dikenali atau tidak memiliki data relasi.',
        'role' => $user->role
    ], 403);
});

Route::middleware('auth.token')->get('/mentor/students', [MentorController::class, 'getStudents']);
Route::middleware('auth.token')->post('/mentor/presensi-manual', [PresenceController::class, 'storePresensiManual']);

Route::middleware('auth.token')->post('/presence-outside', [PresenceController::class, 'outsideUpload']);
Route::middleware('auth.token')->get('/check-status', [PresenceController::class, 'checkStatus']);
Route::middleware('auth.token')->post('/check-in', [PresenceController::class, 'checkIn']);
Route::middleware('auth.token')->post('/check-out', [PresenceController::class, 'checkOut']);
Route::middleware('auth.token')->get('/history', [PresenceController::class, 'history']);
Route::middleware('auth.token')->get('/uploads', [PresenceController::class, 'getUploadsByWeeks']);
Route::middleware('auth.token')->post('/uploads', [PresenceController::class, 'store']);
Route::middleware('auth.token')->get('/lokasi', [PresenceController::class, 'lokasi']);
Route::middleware('auth.token')->get('/check-token', [AuthController::class, 'checkToken']);
Route::middleware('auth.token')->get('/mentor/students-uploads', [MentorController::class, 'getStudentsUploads']);

Route::middleware('auth.token')->get('/presence/history', [PresenceController::class, 'historyAll']);
Route::middleware('auth.token')->get('/presence/today', [PresenceController::class, 'presenceToday']);

Route::middleware('auth.token')->get('/mentor/presence-history', [MentorController::class, 'getPresenceHistory']);
// ===== Monitoring =====
Route::middleware('auth.token')->get('/mentor/monitoring', [MentorController::class, 'index']);
Route::middleware('auth.token')->post('/mentor/monitoring', [MentorController::class, 'store']);


// 🔽 TAMBAHAN INI
Route::middleware('auth.token')->get(
    '/mentor/monitoring/list',
    [MentorController::class, 'list']
);

Route::middleware('auth.token')->get('/presences/today', [PresenceController::class, 'todayPresence']);

Route::post('/logout', [AuthController::class, 'logout']);



Route::post('/telegram-webhook', [TelegramController::class, 'handleWebhook']);
