<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\InternshipBatch; // Tambahkan ini
use App\Models\InternshipPlace; // Tambahkan ini
use Illuminate\Http\Request;

class MapMonitoringController extends Controller
{
    public function index()
    {
        $title = "Peta Monitoring Global (Semua Riwayat)";

        // 1. Ambil SEMUA data presensi yang punya lokasi
        // Tanpa ->limit(500), ini akan mengambil semua data yang cocok
        $presences = Presence::with([
            'student:id,name,class_code,internship_batch_id,internship_place_code,mentor_id',
            'student.internshipPlace:code,name',
            'student.mentor:id,name'
        ])
            ->whereNotNull(['check_in_latitude', 'check_in_longitude'])
            ->latest('check_in')
            ->get(); // Hapus ->limit(500) di sini

        // 2. Ambil master data (tetap cepat karena query terpisah)
        $batches = InternshipBatch::select('id', 'batch_name', 'academic_year')->get();
        $places = InternshipPlace::select('code', 'name')->get();

        // 3. Ambil Kelas unik
        $classes = $presences->pluck('student')->filter()->unique('class_code')->map(fn($s) => [
            'class_code' => $s->class_code,
            'batch_id'   => $s->internship_batch_id ?? 0
        ])->values();

        return view('presence.map_monitoring', compact('title', 'presences', 'batches', 'classes', 'places'));
    }
}
