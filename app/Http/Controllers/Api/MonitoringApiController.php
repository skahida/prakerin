<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitoring;
use App\Models\InternshipPlace;
use Illuminate\Support\Facades\Response;

class MonitoringApiController extends Controller
{
    public function getStats()
    {
        try {
        // Menarik semua data tanpa batas (Limitless)
        $monitorings = Monitoring::with(['mentor', 'internshipPlace'])
            ->inRandomOrder() // 🔥 Data akan diacak langsung dari database
            ->get(); // 🔥 Diacak setelah data terbaru diambil
        $formattedData = $monitorings->map(function ($m) {
            return [
                'id' => $m->id,
                'mentor' => $m->mentor->name ?? 'Tenaga Ahli',
                'place' => $m->internshipPlace->name ?? 'Mitra Industri',
                'place_code' => $m->internshipPlace->place_code ?? null, // Penting untuk hitung jumlah mitra unik
                'photo' => $m->photo ? asset('storage/' . $m->photo) : null,
                'status' => $m->status,
                'notes' => $m->notes,
                'time_ago' => $m->created_at->diffForHumans(),
                'date_full' => $m->created_at->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil seluruh data monitoring',
            'total_data' => $formattedData->count(),
            'total_mitra' => $formattedData->unique('place')->count(), // Hitung otomatis jumlah industri unik
            'data' => $formattedData
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'error' => 'Gagal mengambil data: ' . $e->getMessage()
        ], 500);
    }
    }
}