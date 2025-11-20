<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PresenceController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth.token')->get('/user-info', function (Request $request) {
    $user = $request->user();

    // Eager load relasi biar hemat query
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
        'message' => 'Berhasil akses profile dengan token!',
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
});

Route::middleware('auth.token')->post('/check-in', [PresenceController::class, 'checkIn']);
Route::middleware('auth.token')->post('/check-out', [PresenceController::class, 'checkOut']);
Route::middleware('auth.token')->get('/history', [PresenceController::class, 'history']);
Route::middleware('auth.token')->get('/uploads', [PresenceController::class, 'getUploadsByWeeks']);
Route::middleware('auth.token')->post('/uploads', [PresenceController::class, 'store']);
Route::middleware('auth.token')->get('/lokasi', [PresenceController::class, 'lokasi']);





Route::middleware('auth.token')->get('/presences/today', [PresenceController::class, 'todayPresence']);

Route::post('/logout', [AuthController::class, 'logout']);



Route::post('/telegram-webhook', [TelegramController::class, 'handleWebhook']);
