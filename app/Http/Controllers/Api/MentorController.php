<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\InternshipPlace;
use App\Models\Monitoring;
use App\Models\InternshipBatchDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MentorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 401);
        }

        // Relasi users.id ke mentors.user_id
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Data mentor tidak ditemukan',
                'user_id' => $user->id
            ], 404);
        }

        $details = InternshipBatchDetail::with([
            'place:code,name'
        ])
            ->where('mentor_id', $mentor->id)
            ->whereHas('batch', function ($query) {
                $query->where('status_batch', 'active');
            })
            ->get();

        $places = $details
            ->pluck('place')
            ->filter()
            ->unique('code')
            ->values();

        if ($places->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tempat magang dengan batch aktif',
                'user_id' => $user->id,
                'mentor_id' => $mentor->id
            ], 404);
        }

        return response()->json([
            'success' => true,
            'places' => $places
        ]);
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor tidak ditemukan'
            ], 403);
        }

        $mentorId = $user->mentor->id;

        // Menggunakan whereHas untuk memfilter berdasarkan batch aktif
        $monitorings = Monitoring::query()
            ->where('mentor_id', $mentorId)
            ->whereHas('internshipPlace.students', function ($query) {
                $query->whereHas('internshipBatch', function ($q) {
                    $q->where('status_batch', 'active');
                });
            })
            ->join('internship_places', 'internship_places.code', '=', 'monitorings.place_code')
            ->select(
                'monitorings.id',
                'monitorings.place_code',
                'internship_places.name as place_name',
                'monitorings.status',
                'monitorings.photo',
                'monitorings.created_at'
            )
            ->orderBy('monitorings.created_at', 'desc')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'place_code' => $s->place_code,
                    'place_name' => $s->place_name,
                    'status' => $s->status,
                    'fotoUrl' => $s->photo ? asset('storage/' . $s->photo) : null,
                    'created_at' => $s->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $monitorings
        ]);
    }



    /**
     * POST /api/mentor/monitoring
     * Upload monitoring mentor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'place_code' => 'required|exists:internship_places,code',
            'monitor_photo' => 'required|image|mimes:jpeg,png,jpg,heic|max:2048',
            'status' => 'required|in:Penerjunan,Monitoring 1,Monitoring 2,Monitoring 3,Penarikan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (!$request->hasFile('monitor_photo')) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak terbaca',
            ], 400);
        }

        $path = $request->file('monitor_photo')
            ->store('monitoring', 'public');

        Monitoring::create([
            'place_code' => $request->place_code,
            'mentor_id'  => Auth::user()->mentor->id ?? null,
            'photo'      => $path,
            'status'     => $request->status,
            'check_latitude' => $request->check_latitude,
            'check_longitude' => $request->check_longitude,
            'check_location_link' => $request->check_location_link,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti monitoring berhasil diupload',
        ]);
    }


    // public function getStudents(Request $request)
    // {
    //     $user = $request->user();

    //     // --- VALIDASI ROLE ----------------------------------------------------
    //     if ($user->role !== 'mentor') {
    //         return response()->json([
    //             'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
    //         ], 403);
    //     }

    //     // --- VALIDASI RELASI MENTOR ------------------------------------------
    //     $mentor = $user->mentor;

    //     if (!$mentor) {
    //         return response()->json([
    //             'message' => 'Data mentor tidak ditemukan.'
    //         ], 404);
    //     }

    //     // --- AMBIL SISWA BERDASARKAN MENTOR ----------------------------------
    //     $students = $mentor->students()
    //         ->whereHas('internshipBatch', function ($q) {
    //             $q->where('status_batch', 'active'); // hanya batch aktif
    //         })
    //         ->with([
    //             'class',
    //             'internshipPlace',
    //             'internshipBatch',
    //             'presence' => function ($q) {
    //                 $q->whereDate('created_at', now()->toDateString()) // presensi hari ini
    //                     ->latest()
    //                     ->limit(1);
    //             }
    //         ])
    //         ->get()
    //         ->map(function ($s) {

    //             $lastPresence = $s->presence->first();

    //             return [
    //                 'id'        => $s->id,
    //                 'nama'      => $s->name,
    //                 'kelas'     => $s->class->name ?? "-",
    //                 'perusahaan'=> $s->internshipPlace->name ?? "-",
    //                 'gelombang' => $s->internshipBatch->batch_name ?? "-",

    //                 // KIRIM LAT & LONG KE MAP
    //                 'lat'       => $lastPresence?->check_in_latitude,
    //                 'long'      => $lastPresence?->check_in_longitude,

    //                 // KETERANGAN PRESENSI
    //                 'waktu_presensi' => $lastPresence?->created_at
    //                     ? $lastPresence->created_at->format("Y-m-d H:i:s")
    //                     : null,
    //             ];
    //         })
    //         ->values(); // reset index agar lebih rapi di JSON


    //     return response()->json([
    //         'message' => 'Data siswa berhasil diambil.',
    //         'students' => $students,
    //     ], 200);
    // }

    // public function getStudents(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->role !== 'mentor') {
    //         return response()->json([
    //             'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
    //         ], 403);
    //     }

    //     $mentor = $user->mentor;

    //     if (!$mentor) {
    //         return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
    //     }

    //     // ===============================
    //     //  Ambil semua siswa milik mentor
    //     // ===============================
    //     $students = $mentor->students()
    //         ->whereHas('internshipBatch', function ($q) {
    //             $q->where('status_batch', 'active');
    //         })
    //         ->with([
    //             'class',
    //             'internshipPlace',
    //             'internshipBatch',
    //             // ==> Ambil PRESENSI TERBARU (bukan hanya today)
    //             'presence' => function ($q) {
    //                 $q->latest()->limit(1);
    //             }
    //         ])
    //         ->get()
    //         ->map(function ($s) {

    //             $lastPresence = $s->presence->first();

    //             // Cek apakah presensi itu HARI INI
    //             $isToday = $lastPresence &&
    //                     $lastPresence->created_at->isToday();

    //             return [
    //                 'id' => $s->id,
    //                 'nama' => $s->name,
    //                 'kelas' => $s->class->name ?? "-",
    //                 'perusahaan' => $s->internshipPlace->name ?? "-",
    //                 'gelombang' => $s->internshipBatch->batch_name ?? "-",

    //                 // Jika tidak presensi hari ini → lat/long = null
    //                 'lat' => $isToday ? $lastPresence->check_in_latitude : null,
    //                 'long' => $isToday ? $lastPresence->check_in_longitude : null,

    //                 'waktu_presensi' => $lastPresence?->created_at?->format("Y-m-d H:i:s"),

    //                 // tambahan untuk styling di map
    //                 'presensi_hari_ini' => $isToday,
    //             ];
    //         })
    //         ->values();

    //     return response()->json([
    //         'message' => 'Data siswa berhasil diambil.',
    //         'students' => $students,
    //     ]);
    // }

    // public function getStudents(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->role !== 'mentor') {
    //         return response()->json([
    //             'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
    //         ], 403);
    //     }

    //     $mentor = $user->mentor;

    //     if (!$mentor) {
    //         return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
    //     }

    //     // Ambil siswa + presensi terakhir
    //     $students = $mentor->students()
    //         ->whereHas('internshipBatch', function ($q) {
    //             $q->where('status_batch', 'active');
    //         })
    //         ->with([
    //             'class',
    //             'internshipPlace',
    //             'internshipBatch',
    //             'presence' => function ($q) {
    //                 $q->latest()->limit(1);
    //             }
    //         ])
    //         ->get()
    //         ->map(function ($s) {

    //             $lastPresence = $s->presence->first();

    //             return [
    //                 'id' => $s->id,
    //                 'nama' => $s->name,
    //                 'kelas' => $s->class->name ?? "-",
    //                 'perusahaan' => $s->internshipPlace->name ?? "-",
    //                 'gelombang' => $s->internshipBatch->batch_name ?? "-",

    //                 // Ambil lat-long dari presensi terakhir SAJA
    //                 'lat' => $lastPresence?->check_in_latitude,
    //                 'long' => $lastPresence?->check_in_longitude,

    //                 'keterangan' => $lastPresence?->status,

    //                 'waktu_presensi' => $lastPresence?->check_in
    //                     ? Carbon::parse($lastPresence->check_in)->format("Y-m-d H:i:s")
    //                     : null,
    //                 'waktu_pulang' => $lastPresence?->check_out
    //                     ? Carbon::parse($lastPresence->check_out)->format("Y-m-d H:i:s")
    //                     : null,
    //                 'punya_presensi' => $lastPresence ? true : false,
    //                 'fotoUrl' => $s->user->foto_url ? asset('storage/' . $s->user->foto_url) : null,
    //             ];
    //         })
    //         ->values();

    //     return response()->json([
    //         'message' => 'Data siswa berhasil diambil.',
    //         'students' => $students,
    //     ]);
    // }

    // public function getStudents(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->role !== 'mentor') {
    //         return response()->json([
    //             'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
    //         ], 403);
    //     }

    //     $mentor = $user->mentor;

    //     if (!$mentor) {
    //         return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
    //     }

    //     $today = Carbon::today()->toDateString(); // Mengambil tanggal hari ini (YYYY-MM-DD)

    //     $students = $mentor->students()
    //         ->whereHas('internshipBatch', function ($q) {
    //             $q->where('status_batch', 'active');
    //         })
    //         ->with([
    //             'class',
    //             'internshipPlace',
    //             'internshipBatch',
    //             'user', // Pastikan merelasikan user untuk mengambil foto_url tanpa N+1 query
    //             'presence' => function ($q) use ($today) {
    //                 // Filter presensi yang dibuat hari ini saja
    //                 $q->whereDate('created_at', $today)->latest();
    //             }
    //         ])
    //         ->get()
    //         ->map(function ($s) {
    //             // Karena sudah difilter per hari ini di atas, 
    //             // kita tinggal ambil baris pertama yang paling baru
    //             $todayPresence = $s->presence->first();

    //             return [
    //                 'id' => $s->id,
    //                 'nama' => $s->name,
    //                 'kelas' => $s->class->name ?? "-",
    //                 'perusahaan' => $s->internshipPlace->name ?? "-",
    //                 'gelombang' => $s->internshipBatch->batch_name ?? "-",

    //                 // Data presensi hari ini
    //                 'lat' => $todayPresence?->check_in_latitude,
    //                 'long' => $todayPresence?->check_in_longitude,
    //                 'keterangan' => $todayPresence?->status ?? "Belum Presensi", // Default jika belum absen

    //                 'waktu_presensi' => $todayPresence?->check_in
    //                     ? Carbon::parse($todayPresence->check_in)->format("Y-m-d H:i:s")
    //                     : null,
    //                 'waktu_pulang' => $todayPresence?->check_out
    //                     ? Carbon::parse($todayPresence->check_out)->format("Y-m-d H:i:s")
    //                     : null,
    //                 'punya_presensi' => $todayPresence ? true : false,
    //                 'fotoUrl' => $s->user?->foto_url ? asset('storage/' . $s->user->foto_url) : null,
    //             ];
    //         })
    //         ->values();

    //     return response()->json([
    //         'message' => 'Data siswa hari ini berhasil diambil.',
    //         'students' => $students,
    //     ]);
    // }

    public function getStudents(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mentor') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
            ], 403);
        }

        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
        }

        // Gunakan timezone Asia/Jakarta agar tanggalnya sinkron dengan waktu lokal indonesia
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        $students = $mentor->students()
            ->whereHas('internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
            ->with([
                'class',
                'internshipPlace',
                'internshipBatch',
                'user',
                'presence' => function ($q) use ($today) {
                    // SESUAIKAN: ganti 'check_in' dengan 'created_at' jika kamu pakai created_at
                    $q->whereDate('check_in', $today)->latest();
                }
            ])
            ->get()
            ->map(function ($s) {
                $todayPresence = $s->presence->first();

                return [
                    'id' => $s->id,
                    'nama' => $s->name,
                    'kelas' => $s->class->name ?? "-",
                    'perusahaan' => $s->internshipPlace->name ?? "-",
                    'gelombang' => $s->internshipBatch->batch_name ?? "-",

                    // Jika hari ini tidak ada presensi, nilainya akan otomatis null / "Belum Presensi"
                    'lat' => $todayPresence?->check_in_latitude ?? null,
                    'long' => $todayPresence?->check_in_longitude ?? null,
                    'keterangan' => $todayPresence?->status ?? "Belum Presensi",

                    'waktu_presensi' => $todayPresence?->check_in
                        ? Carbon::parse($todayPresence->check_in)->format("Y-m-d H:i:s")
                        : null,
                    'waktu_pulang' => $todayPresence?->check_out
                        ? Carbon::parse($todayPresence->check_out)->format("Y-m-d H:i:s")
                        : null,
                    'punya_presensi' => $todayPresence ? true : false,
                    'fotoUrl' => $s->user?->foto_url ? asset('storage/' . $s->user->foto_url) : null,
                ];
            })
            ->values();

        return response()->json([
            'message' => 'Data siswa hari ini berhasil diambil.',
            'students' => $students,
        ]);
    }

    public function getStudentsUploads(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mentor') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
            ], 403);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
        }

        $students = $mentor->students()
            ->whereHas('internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
            ->with(['class', 'internshipPlace', 'reports']) // ambil relasi internshipPlace langsung dari student
            ->get()
            ->map(function ($s) {

                // Pastikan 12 minggu tersedia
                $fullReports = collect(range(1, 52))->map(function ($minggu) use ($s) {
                    $report = $s->reports->firstWhere('report_title', "Minggu {$minggu}: Upload Laporan");
                    return [
                        'minggu' => $minggu,
                        'videoLink' => $report?->report_link1 ?? null,
                        'status' => $report?->report_status ?? 'Belum Upload',
                    ];
                });

                return [
                    'id' => $s->id,
                    'nama' => $s->name,
                    'kelas' => $s->class->name ?? '-',
                    'gelombang' => $s->internshipBatch->batch_name ?? '-',
                    'dudi' => $s->internshipPlace->name ?? '-', // ambil nama DUDI dari student
                    'uploads' => $fullReports,
                ];
            })
            ->values();


        return response()->json([
            'message' => 'Data siswa berhasil diambil.',
            'students' => $students,
        ]);
    }

    public function getPresenceHistory(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'mentor') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya mentor yang bisa mengakses data ini.'
            ], 403);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
        }

        // Ambil parameter filter tanggal dari query
        $from = $request->query('from'); // format: Y-m-d
        $to   = $request->query('to');   // format: Y-m-d

        // Ambil semua siswa mentor dengan batch aktif
        $students = $mentor->students()
            ->whereHas('internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
            ->with([
                'presence' => function ($q) use ($from, $to) {
                    if ($from && $to) {
                        $q->whereDate('check_in', '>=', $from)
                            ->whereDate('check_in', '<=', $to);
                    }
                    $q->orderBy('check_in', 'asc');
                },
                'class',
                'internshipPlace'
            ])
            ->get()
            ->map(function ($s) {
                $hadir = $s->presence->where('status', 'present')->count();
                $izin  = $s->presence->where('status', 'permission')->count();
                $sakit = $s->presence->where('status', 'sick')->count();
                $alpha = $s->presence->where('status', 'absent')->count();
                $libur = $s->presence->where('status', 'Libur')->count();

                $details = $s->presence->map(function ($p) {
                    return [
                        'tanggal' => Carbon::parse($p->check_in)->format('Y-m-d'), // tanggal
                        'check_in' => Carbon::parse($p->check_in)->format('H:i'),  // jam masuk
                        'check_out' => $p->check_out
                            ? Carbon::parse($p->check_out)->format('H:i')
                            : null, // jam pulang, bisa null kalau belum ada
                        'status' => $p->status,
                        'timezone' => 'WIB', // tambahan label WIB
                    ];
                });

                return [
                    'studentId' => $s->id,
                    'nama' => $s->name,
                    'kelas' => $s->class->name ?? '-',
                    'dudi' => $s->internshipPlace->name ?? '-',
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpha' => $alpha,
                    'libur' => $libur,
                    'details' => $details,
                ];
            })
            ->values();

        return response()->json([
            'message' => 'Data histori presensi berhasil diambil.',
            'students' => $students,
        ]);
    }
}
