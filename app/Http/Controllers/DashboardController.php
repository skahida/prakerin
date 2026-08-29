<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\ClassModel;
use App\Models\Department;
use App\Models\InternshipBatch;
use App\Models\InternshipPlace;
use App\Models\Mentor;
use App\Models\Presence;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */

    public function userSession()
    {
        if (Auth::check()) {
            return response()->json([
                'user_name' => Auth::user()->name
            ]);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }

    public function index(Request $request)
    {
        $title = "Dashboard";
        $role = session('ses_role');

        // Ambil data siswa yang memiliki mentor_id yang sesuai
        $studentCount = Student::with([
            'internshipBatch',  // Eager loading internshipBatch
            'internshipPlace',  // Eager loading internshipPlace
            'class',            // Eager loading class
            'department'        // Eager loading department
        ])
            ->orderBy('name', 'asc')  // Atur urutan berdasarkan nama siswa atau field lainnya
            ->count();

        // Ambil total mentor
        $mentorCount = Mentor::count();

        // Ambil total admin
        $adminCount = Admin::count();

        // Ambil total dudi
        $dudiCount = InternshipPlace::count();

        // Ambil total gelombang
        $batchCount = InternshipBatch::where('status_batch', 'active')
            ->count();


        $reports = Report::with(['student']) // Eager load the related student
            ->get()
            ->groupBy('student_id');

        // Count distinct student_ids
        $reportsCount = $reports->count();

        $historyPresencesCount = Presence::with([
            'student.internshipBatch',   // Eager loading internshipBatch
            'student.internshipPlace',   // Eager loading internshipPlace
            'student.class',             // Eager loading class
            'student.department'         // Eager loading department
        ])
            ->count();  // Sort by created_at or another field

        // dd($distinctStudentCount);

        // Jika role adalah 'admin', tampilkan dashboard admin
        if ($role === 'admin' || $role === 'super-admin') {
            return $this->renderAdminDashboard($title, $studentCount, $mentorCount, $adminCount, $dudiCount, $reportsCount, $historyPresencesCount, $batchCount);
        }

        // Jika role adalah 'student', tampilkan dashboard untuk student
        elseif ($role === "student") {
            $studentId = session('ses_student_id');
            // Ambil data student yang sesuai
            $student = $this->getStudentData();
            // Ambil semua presensi dari student
            $presences = $this->getStudentPresences();

            // Cek status presensi hari ini
            $hasCheckedIn = $this->hasCheckedInToday();
            $hasCheckedOut = $this->hasCheckedOutToday();

            $reportsCount = Report::where('student_id', $studentId)
                ->count();

            $presenceCount = Presence::where('student_id', $studentId)
                ->count();

            return view('dashboard.index', [
                'title'           => $title,
                'student'         => $student,
                'presences'       => $presences,
                'hasCheckedIn'    => $hasCheckedIn,
                'hasCheckedOut'   => $hasCheckedOut,
                'noPresence'      => $presences->isEmpty(),
                'users'           => User::all(),
                'classes'         => ClassModel::all(),
                'departments'     => Department::all(),
                'internshipPlaces' => InternshipPlace::all(),
                'batches'         => InternshipBatch::all(),
                'mentors'         => Mentor::all(),
                'presencesCount'  => $presences->count(), // Jumlah presensi untuk student
                'reportsCount'    => $reportsCount,
                'presenceCount'    => $presenceCount
            ]);
        } elseif ($role === 'mentor') {
            // Ambil ID mentor dari session
            $mentorId = session('ses_mentor_id');

            // Cek apakah mentor dengan mentor_id tersebut memiliki chat ID
            $mentor = Mentor::find($mentorId);  // Ambil data mentor berdasarkan mentor_id

            if ($mentor && !$mentor->telegram_number) {
                // Jika mentor tidak memiliki chat_id, set session untuk menampilkan modal
                session(['requires_chat_id_update' => true]);
            }

            // Ambil jumlah presensi siswa yang terdaftar pada mentor ini dan terdaftar di internship batch aktif
            $presencesCount = Presence::with([
                'student.internshipBatch',  // Eager loading internshipBatch
                'student.internshipPlace',  // Eager loading internshipPlace
                'student.class',            // Eager loading class
                'student.department'        // Eager loading department
            ])
                ->whereHas('student', function ($query) use ($mentorId) {
                    // Filter siswa berdasarkan mentor_id dan internshipBatch yang aktif
                    $query->where('mentor_id', $mentorId);
                    // ->whereHas('internshipBatch', function ($query) {
                    //     $query->where('status_batch', 'active');
                    // });
                })
                ->whereDate('check_in', Carbon::today())  // Menambahkan filter hanya untuk presensi hari ini
                ->orderBy('check_in', 'desc')  // Urutkan berdasarkan check_in
                ->count(); // Menghitung jumlah presensi untuk siswa yang di-mentor oleh mentor ini

            // // Ambil data siswa yang memiliki mentor_id yang sesuai
            // $studentsCount = Student::with([
            //     'internshipBatch',  // Eager loading internshipBatch
            //     'internshipPlace',  // Eager loading internshipPlace
            //     'class',            // Eager loading class
            //     'department'        // Eager loading department
            // ])
            //     ->where('mentor_id', $mentorId)  // Filter siswa berdasarkan mentor_id
            //     ->orderBy('name', 'asc')  // Atur urutan berdasarkan nama siswa atau field lainnya
            //     ->count();

            $studentsCount = Student::with([
                'internshipBatch',  // Eager loading internshipBatch
                'internshipPlace',  // Eager loading internshipPlace
                'class',            // Eager loading class
                'department'        // Eager loading department
            ])
                ->where('mentor_id', $mentorId) // filter by mentor
                ->whereHas('internshipBatch', function ($q) {
                    $q->where('status_batch', 'active');
                })
                ->orderBy('name', 'asc')
                ->count();


            $reports = Report::with(['student.mentor']) // Eager load the related student and mentor
                ->whereHas('student', function ($query) use ($mentorId) {
                    $query->where('mentor_id', $mentorId); // Filter by mentor_id
                })
                ->get()
                ->groupBy('student_id'); // Group by student_id

            // Count distinct student_ids
            $reportCount = $reports->count();

            // // Ambil jumlah presensi siswa yang terdaftar pada mentor ini dan terdaftar di internship batch aktif
            // $presenceCount = Presence::with([
            //     'student.internshipBatch',  // Eager loading internshipBatch
            //     'student.internshipPlace',  // Eager loading internshipPlace
            //     'student.class',            // Eager loading class
            //     'student.department'        // Eager loading department
            // ])
            //     ->whereHas('student', function ($query) use ($mentorId) {
            //         // Filter siswa berdasarkan mentor_id dan internshipBatch yang aktif
            //         $query->where('mentor_id', $mentorId);
            //         // ->whereHas('internshipBatch', function ($query) {
            //         //     $query->where('status_batch', 'active');
            //         // });
            //     })
            //     ->orderBy('check_in', 'desc')  // Urutkan berdasarkan check_in
            //     ->count();

            $presenceCount = Presence::with([
                'student.internshipBatch',  // Eager loading internshipBatch
                'student.internshipPlace',  // Eager loading internshipPlace
                'student.class',            // Eager loading class
                'student.department'        // Eager loading department
            ])
                ->whereHas('student', function ($query) use ($mentorId) {
                    // Filter siswa berdasarkan mentor_id
                    $query->where('mentor_id', $mentorId)
                        ->whereHas('internshipBatch', function ($q) {
                            $q->where('status_batch', 'active');
                        });
                })
                ->orderBy('check_in', 'desc') // urutkan berdasarkan check_in
                ->count();


            // dd(date_default_timezone_get());


            // Pastikan untuk mengirim data yang diperlukan ke view
            return view('dashboard.index', [
                'title'           => $title,
                'student'         => null,  // Tidak relevan untuk mentor
                'presences'       => null,  // Tidak relevan untuk mentor
                'hasCheckedIn'    => null,  // Tidak relevan untuk mentor
                'hasCheckedOut'   => null,  // Tidak relevan untuk mentor
                'noPresence'      => null,  // Tidak relevan untuk mentor
                'users'           => User::all(),
                'classes'         => ClassModel::all(),
                'departments'     => Department::all(),
                'internshipPlaces' => InternshipPlace::all(),
                'batches'         => InternshipBatch::all(),
                'mentor'         => $mentor,
                'presencesCount'  => $presencesCount,
                'studentsCount'  => $studentsCount,
                'reportsCount'  => $reportCount,
                'presenceCount'  => $presenceCount,
            ]);
        }

        // Jika role tidak dikenali
        abort(403, 'Unauthorized action.');
    }


    /**
     * Render Admin Dashboard
     */
    private function renderAdminDashboard($title, $studentCount, $mentorCount, $adminCount, $dudiCount, $reportsCount, $historyPresencesCount, $batchCount)
    {
        return view('dashboard.index', compact('title', 'studentCount', 'mentorCount', 'adminCount', 'dudiCount', 'reportsCount', 'historyPresencesCount', 'batchCount'));
    }

    /**
     * Get Student Data Based on NIS from Session
     */
    private function getStudentData()
    {
        $nis = session('nis', null);
        if ($nis) {
            return Student::with([
                'user',
                'class',
                'department',
                'internshipPlace',
                'internshipBatch',
                'mentor'
            ])->where('nis', $nis)->first();
        }

        return null;  // Jika tidak ada NIS di session
    }

    /**
     * Get Student Presences from Session
     */
    private function getStudentPresences()
    {
        $studentId = session('ses_student_id');
        if ($studentId) {
            // Ambil tanggal hari ini
            $today = Carbon::today();

            // Ambil presensi dengan filter hanya hari ini
            return Presence::where('student_id', $studentId)
                ->whereDate('check_in', $today)  // Menambahkan filter untuk tanggal hari ini
                ->orderBy('check_in', 'desc')
                ->get();
        }

        return collect();  // Jika tidak ada session student_id
    }

    /**
     * Check if Student Has Checked In Today
     */
    private function hasCheckedInToday()
    {
        $studentId = session('ses_student_id');
        $today = Carbon::today();
        return Presence::where('student_id', $studentId)
            ->whereDate('check_in', $today)
            ->exists();
    }

    /**
     * Check if Student Has Checked Out Today
     */
    private function hasCheckedOutToday()
    {
        $studentId = session('ses_student_id');
        $today = Carbon::today();
        $presence = Presence::where('student_id', $studentId)
            ->whereDate('check_in', $today)
            ->first();

        return $presence && $presence->check_out !== null;
    }



    public function updatePassword(Request $request)
    {
        // Validasi input password
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\|-]).{6,}$/', // Password harus kombinasi huruf, angka, simbol
            ],
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            // Kembalikan error
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400); // Status 400 untuk error validasi
        }

        // Mencari pengguna berdasarkan username (dalam session)
        $user = User::where('username', session('nis'))->first();

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna dengan username tersebut tidak ditemukan.',
            ], 404); // Kode status 404 untuk data tidak ditemukan
        }

        // Perbarui password
        $user->password = Hash::make($request->password);
        $user->save();

        // Kirim respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diperbarui. Silakan login ulang untuk melanjutkan.',
        ], 200); // Status 200 untuk sukses
    }

    public function calendar(Request $request)
    {
        $title = "Calendar";

        return view('dashboard.calendar', compact('title'));
    }

    public function lks(Request $request)
    {
        $title = "Lks";

        return view('dashboard.lks', compact('title'));
    }

    public function getOnlineUsers()
    {
        // Menggunakan Eloquent untuk mendapatkan data pengguna yang online berdasarkan sesi aktif
        $onlineUsers = User::whereHas('sessions', function ($query) {
            $query->where('last_activity', '>', time() - 300); // 300 detik = 5 menit
        })
            ->with(['sessions' => function ($query) {
                $query->select('user_id', 'last_activity'); // Mengambil kolom user_id dan last_activity dari tabel sessions
            }])
            ->get(['id', 'name']); // Ambil id dan name dari users

        // Ambil 'last_activity' dari session pertama yang ditemukan untuk setiap user
        $onlineUsers = $onlineUsers->map(function ($user) {
            // Pastikan user memiliki sesi yang ditemukan
            $user->last_activity = $user->sessions->first()->last_activity ?? null;
            return $user;
        });

        return response()->json($onlineUsers);
    }

    // public function getAttendanceData(Request $request)
    // {
    //     // Ambil data absensi dengan filter bulan jika ada
    //     $historyPresences = Presence::query()->with('student.internshipBatch');

    //     // FILTER BULAN
    //     if ($request->filled('start_month')) {
    //         $start = Carbon::parse($request->start_month)->startOfMonth(); // Mulai dari awal bulan
    //         $end = $request->filled('end_month')
    //             ? Carbon::parse($request->end_month)->endOfMonth() // Jika end_month diisi, gunakan akhir bulan
    //             : Carbon::parse($request->start_month)->endOfMonth(); // Jika end_month kosong, ambil akhir bulan start_month

    //         // Terapkan filter berdasarkan rentang bulan
    //         $historyPresences->whereBetween('check_in', [$start, $end]);
    //     }

    //     // FILTER SISWA
    //     if ($request->filled('student_name')) {
    //         $historyPresences->whereHas('student', function ($q) use ($request) {
    //             $q->where('name', $request->student_name);
    //         });
    //     }

    //     // FILTER BATCH
    //     if ($request->filled('batch_name')) {
    //         // Mengambil nilai 'batch_name' dari request
    //         $batchName = $request->input('batch_name'); // atau $request->query('batch_name')

    //         // Menambahkan kondisi filter pada query historyPresences
    //         $historyPresences->whereHas('student.internshipBatch', function ($q) use ($batchName) {
    //             // Memfilter berdasarkan id batch_name yang diambil dari request
    //             $q->where('id', $batchName);
    //         });

    //         // Mendapatkan nama Gelombang berdasarkan batch_id
    //         $batch = InternshipBatch::find($batchName);
    //         if ($batch) {
    //             $batchName = $batch->batch_name; // Mengambil nama batch (misalnya "Gelombang 3")
    //         } else {
    //             $batchName = 'Gelombang Tidak Ditemukan';
    //         }
    //     } else {
    //         $batchName = 'Semua Gelombang';
    //     }


    //     // Ambil data presensi
    //     $historyPresences = $historyPresences->get();

    //     // Kelompokkan berdasarkan ID siswa
    //     $students = $historyPresences->groupBy(function ($item) {
    //         return $item->student->id;
    //     });

    //     // Menentukan format untuk hasil output tahun dan bulan
    //     Carbon::setLocale('id');

    //     // Menentukan format untuk hasil output tahun dan bulan
    //     $yearResult = '';
    //     if ($request->filled('start_month')) {
    //         // Mengambil nilai 'start_month' dari request dan membuat Carbon object
    //         $carbonStartDate = Carbon::createFromFormat('Y-m', $request->input('start_month')); // Perbaikan di sini

    //         if ($request->filled('end_month')) {
    //             // Jika ada 'end_month', gunakan range bulan
    //             $carbonEndDate = Carbon::createFromFormat('Y-m', $request->input('end_month')); // Perbaikan di sini

    //             // Format yearResult sebagai range bulan
    //             $yearResult = $carbonStartDate->translatedFormat('F') . ' - ' . $carbonEndDate->translatedFormat('F Y'); // Output: "Januari - April 2025"
    //         } else {
    //             // Jika hanya start_month yang ada, format tahun dan bulan
    //             $yearResult = $carbonStartDate->translatedFormat('F Y'); // Output: "Januari 2025"
    //         }
    //     } else {
    //         $yearResult = "Belum filter bulan";
    //     }

    //     $chartData = [];
    //     $rekapTable = [];
    //     $presenceTable = [];

    //     // Buat presenceTable
    //     $presenceTable = $historyPresences->map(function ($presence, $index) {
    //         return [
    //             'id' => $presence->id,
    //             'no' => $index + 1,
    //             'siswa' => $presence->student->name ?? '-',
    //             'kelas' => $presence->student->class->name ?? '-',
    //             'dudi' => $presence->student->internshipPlace->name ?? '-',
    //             'gelombang' => $presence->student->internshipBatch->batch_name ?? '-',
    //             'tahun_pelajaran' => $presence->student->internshipBatch->academic_year ?? '-',
    //             'hari' => $presence->check_in ? Carbon::parse($presence->check_in)->locale('id')->isoFormat('dddd') : '-',
    //             'tanggal' => $presence->check_in ? Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('d-m-Y') : '-',
    //             'masuk' => $presence->check_in ? Carbon::parse($presence->check_in)->timezone('Asia/Jakarta')->format('H:i:s') : '-',
    //             'pulang' => $presence->check_out ? Carbon::parse($presence->check_out)->timezone('Asia/Jakarta')->format('H:i:s') : '-',
    //             'lokasi_masuk' => $presence->check_in_location_link ?? null,
    //             'lokasi_pulang' => $presence->check_out_location_link ?? null,
    //             'status' => $presence->status == 'present' ? 'Masuk' : ($presence->status == 'premission' ? 'Izin' : ($presence->status == 'sick' ? 'Sakit' : 'Alpa')),
    //             'note' => $presence->note ?? "-",
    //             'aksi_link' => route('historyPresence.edit', $presence->id),
    //         ];
    //     });

    //     // Loop untuk generate chart dan tabel rekap
    //     foreach ($students as $studentId => $presences) {
    //         $student = $presences->first()->student;

    //         // Hitung jumlah hadir & tidak hadir
    //         $present = $presences->filter(function ($presence) {
    //             return $presence->check_in !== null;
    //         })->count();

    //         $absent = $presences->count() - $present;

    //         // Data untuk chart
    //         $chartData[] = [
    //             'label' => $student->name,
    //             'data' => [$present, $absent],
    //             'present' => $present, // <-- Tambahkan ini untuk sorting
    //             'batch_id' => $student->internshipBatch ? $student->internshipBatch->id : null,
    //         ];

    //         // Hitung data rekap (Masuk, Sakit, Izin, Alpa, Lainnya)
    //         $masuk = $presences->where('status', 'present')->count();
    //         $sakit = $presences->where('status', 'sick')->count();
    //         $izin = $presences->where('status', 'permission')->count();
    //         // $alpa = $presences->where('status', 'absent')->count();
    //         $lainnya = $presences->whereNotIn('status', ['present', 'sick', 'permission', 'absent'])->count();

    //         // Keterangan (sakit)
    //         $keterangan = $presences
    //             ->where('status', 'sick')
    //             ->pluck('check_in')
    //             ->map(fn($tgl) => Carbon::parse($tgl)->translatedFormat('d'))
    //             ->implode(', ');

    //         // Hitung hari efektif dari bulan yang dipilih
    //         $hariEfektif = 0;
    //         if ($request->filled('start_month')) {
    //             $startDate = Carbon::parse($request->start_month);
    //             $endDate = $request->filled('end_month')
    //                 ? Carbon::parse($request->end_month)
    //                 : $startDate;

    //             for ($date = $startDate->copy()->startOfMonth(); $date <= $endDate->copy()->endOfMonth(); $date->addDay()) {
    //                 if (!$date->isSunday()) {
    //                     $hariEfektif++;
    //                 }
    //             }
    //         }

    //         $alpa = max($hariEfektif - $present - $izin - $sakit - $lainnya, 0);

    //         // Tambahkan data ke rekapTable
    //         $rekapTable[] = [
    //             'nama' => $student->name,
    //             'kelas' => $student->class->name ?? '-',
    //             'dudi' => $student->internshipPlace->name ?? '-',
    //             'pembimbing' => $student->mentor->name ?? '-',
    //             'hari_efektif' => $hariEfektif,
    //             'masuk' => $masuk,
    //             'sakit' => $sakit,
    //             'izin' => $izin,
    //             'alpa' => $alpa,
    //             'lainnya' => $lainnya,
    //             'keterangan' => $sakit > 0 ? 'Sakit tanggal ' . $keterangan : '-',
    //         ];
    //     }

    //     // Urutkan chart berdasarkan jumlah hadir (present) terbanyak
    //     $chartData = collect($chartData)
    //         ->sortByDesc('present') // Urut dari terbanyak ke terkecil
    //         ->map(function ($item) {
    //             // Hapus key 'present' biar clean (optional)
    //             return [
    //                 'label' => $item['label'],
    //                 'data' => $item['data'],
    //                 'batch_id' => $item['batch_id'],
    //             ];
    //         })
    //         ->values()
    //         ->all();

    //     // <-- TARUH DI SINI
    //     $rekapTable = collect($rekapTable)->sortByDesc('masuk')->values()->all();

    //     // Dropdown filter siswa & gelombang
    //     $studentsForFilter = Student::all();
    //     $batchesForFilter = InternshipBatch::all();

    //     return response()->json([
    //         'presenceTable' => $presenceTable,
    //         'yearResult' => $yearResult,
    //         'batchNameIdentity' => $batchName,
    //         'attendanceData' => $chartData,
    //         'rekapTable' => $rekapTable,
    //         'students' => $studentsForFilter,
    //         'batches' => $batchesForFilter,
    //     ]);
    // }

    public function getAttendanceData(Request $request)
    {
        $role = session('ses_role');

        // Hanya role yang diperbolehkan
        if (!in_array($role, ['super-admin', 'mentor'])) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // =========================================================
        // MENTOR ID
        // =========================================================
        $mentorId = null;

        if ($role === 'mentor') {
            $mentorId = Auth::user()->mentor->id ?? null;

            if (!$mentorId) {
                return response()->json([
                    'message' => 'Data mentor tidak ditemukan'
                ], 403);
            }
        }

        // =========================================================
        // QUERY PRESENSI
        // =========================================================
        $historyPresences = Presence::query()
            ->with([
                'student',
                'student.class',
                'student.internshipPlace',
                'student.internshipBatch',
                'student.mentor',
            ]);

        // =========================================================
        // FILTER BERDASARKAN ROLE
        // =========================================================
        if ($role === 'super-admin') {

            // Super admin hanya melihat batch aktif
            $historyPresences->whereHas('student.internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            });
        } elseif ($role === 'mentor') {

            // Mentor hanya melihat siswa bimbingannya + batch aktif
            $historyPresences->whereHas('student', function ($q) use ($mentorId) {
                $q->where('mentor_id', $mentorId)
                    ->whereHas('internshipBatch', function ($q2) {
                        $q2->where('status_batch', 'active');
                    });
            });
        }

        // =========================================================
        // FILTER TANGGAL
        // start_date lebih utama dibanding start_month
        // =========================================================
        if ($request->filled('start_date')) {

            $start = Carbon::parse($request->start_date)
                ->startOfDay();

            $end = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::parse($request->start_date)->endOfDay();

            $historyPresences->whereBetween('created_at', [
                $start,
                $end
            ]);
        } elseif ($request->filled('start_month')) {

            $start = Carbon::parse($request->start_month)
                ->startOfMonth();

            $end = $request->filled('end_month')
                ? Carbon::parse($request->end_month)->endOfMonth()
                : Carbon::parse($request->start_month)->endOfMonth();

            $historyPresences->whereBetween('created_at', [
                $start,
                $end
            ]);
        }

        // =========================================================
        // FILTER SISWA
        // =========================================================
        if ($request->filled('student_name')) {

            $studentName = $request->input('student_name');

            $historyPresences->whereHas('student', function ($q) use ($studentName) {
                $q->where('name', $studentName);
            });
        }

        // =========================================================
        // FILTER BATCH
        // =========================================================
        if ($request->filled('batch_name')) {

            $batchId = $request->input('batch_name');

            $historyPresences->whereHas(
                'student.internshipBatch',
                function ($q) use ($batchId) {
                    $q->where('id', $batchId);
                }
            );

            $batch = InternshipBatch::find($batchId);

            $batchName = $batch
                ? $batch->batch_name
                : 'Gelombang Tidak Ditemukan';
        } else {

            $batchName = 'Semua Gelombang';
        }

        // =========================================================
        // FILTER KELAS
        // =========================================================
        if ($request->filled('class_code')) {

            $classCode = $request->input('class_code');

            $historyPresences->whereHas('student', function ($q) use ($classCode) {
                $q->where('class_code', $classCode);
            });

            $class = ClassModel::where('code', $classCode)->first();

            $className = $class
                ? $class->name
                : 'Kelas Tidak Ditemukan';
        } else {

            $className = 'Semua Kelas';
        }

        // =========================================================
        // AMBIL DATA PRESENSI
        // =========================================================
        $historyPresences = $historyPresences
            ->orderBy('created_at', 'desc')
            ->get();

        // =========================================================
        // GROUP BERDASARKAN SISWA
        // =========================================================
        $students = $historyPresences
            ->filter(function ($item) {
                return $item->student;
            })
            ->groupBy(function ($item) {
                return $item->student->id;
            });

        Carbon::setLocale('id');

        // =========================================================
        // IDENTITAS BULAN / TAHUN
        // =========================================================
        if ($request->filled('start_month')) {

            $carbonStartDate = Carbon::createFromFormat(
                'Y-m',
                $request->input('start_month')
            );

            if ($request->filled('end_month')) {

                $carbonEndDate = Carbon::createFromFormat(
                    'Y-m',
                    $request->input('end_month')
                );

                $yearResult =
                    $carbonStartDate->translatedFormat('F Y')
                    . ' - '
                    . $carbonEndDate->translatedFormat('F Y');
            } else {

                $yearResult =
                    $carbonStartDate->translatedFormat('F Y');
            }
        } elseif ($request->filled('start_date')) {

            $carbonStartDate = Carbon::parse(
                $request->start_date
            );

            if ($request->filled('end_date')) {

                $carbonEndDate = Carbon::parse(
                    $request->end_date
                );

                $yearResult =
                    $carbonStartDate->translatedFormat('d F Y')
                    . ' - '
                    . $carbonEndDate->translatedFormat('d F Y');
            } else {

                $yearResult =
                    $carbonStartDate->translatedFormat('d F Y');
            }
        } else {

            $yearResult = 'Belum filter tanggal/bulan';
        }

        // =========================================================
        // PRESENCE TABLE
        // =========================================================
        $presenceTable = $historyPresences
            ->values()
            ->map(function ($presence, $index) {

                return [

                    'id' => $presence->id,

                    'no' => $index + 1,

                    'siswa' =>
                    $presence->student->name ?? '-',

                    'kelas' =>
                    $presence->student->class->name ?? '-',

                    'dudi' =>
                    $presence->student->internshipPlace->name ?? '-',

                    'gelombang' =>
                    $presence->student->internshipBatch->batch_name ?? '-',

                    'tahun_pelajaran' =>
                    $presence->student->internshipBatch->academic_year ?? '-',

                    'hari' =>
                    $presence->check_in
                        ? Carbon::parse($presence->check_in)
                        ->locale('id')
                        ->isoFormat('dddd')
                        : Carbon::parse($presence->created_at)
                        ->locale('id')
                        ->isoFormat('dddd'),

                    'tanggal' =>
                    $presence->check_in
                        ? Carbon::parse($presence->check_in)
                        ->timezone('Asia/Jakarta')
                        ->format('d-m-Y')
                        : Carbon::parse($presence->created_at)
                        ->timezone('Asia/Jakarta')
                        ->format('d-m-Y'),

                    'masuk' =>
                    $presence->check_in
                        ? Carbon::parse($presence->check_in)
                        ->timezone('Asia/Jakarta')
                        ->format('H:i:s')
                        : '-',

                    'pulang' =>
                    $presence->check_out
                        ? Carbon::parse($presence->check_out)
                        ->timezone('Asia/Jakarta')
                        ->format('H:i:s')
                        : '-',

                    'lokasi_masuk' =>
                    $presence->check_in_location_link ?? null,

                    'lokasi_pulang' =>
                    $presence->check_out_location_link ?? null,

                    // =============================================
                    // STATUS TERMASUK HOLIDAY
                    // =============================================
                    'status' => match ($presence->status) {

                        'present' =>
                        'Masuk',

                        'permission' =>
                        'Izin',

                        'sick' =>
                        'Sakit',

                        'absent' =>
                        'Alpa',

                        'holiday' =>
                        'Libur',

                        default =>
                        'Lainnya',
                    },

                    'status_raw' => $presence->status,

                    'note' =>
                    $presence->note ?? '-',

                    'aksi_link' =>
                    route(
                        'historyPresence.edit',
                        $presence->id
                    ),
                ];
            });

        // =========================================================
        // CHART & REKAP
        // =========================================================
        $chartData = [];
        $rekapTable = [];

        foreach ($students as $studentId => $presences) {

            $student = $presences
                ->first()
                ->student;

            if (!$student) {
                continue;
            }

            // =====================================================
            // HITUNG STATUS
            // =====================================================
            $masuk = $presences
                ->where('status', 'present')
                ->count();

            $sakit = $presences
                ->where('status', 'sick')
                ->count();

            $izin = $presences
                ->where('status', 'permission')
                ->count();

            $alpa = $presences
                ->where('status', 'absent')
                ->count();

            $holiday = $presences
                ->where('status', 'holiday')
                ->count();

            $lainnya = $presences
                ->whereNotIn('status', [
                    'present',
                    'absent',
                    'sick',
                    'permission',
                    'holiday',
                ])
                ->count();

            // =====================================================
            // CHART
            //
            // Urutan:
            // 0 = Masuk
            // 1 = Alpa
            // 2 = Izin
            // 3 = Sakit
            // 4 = Libur
            // =====================================================
            $chartData[] = [

                'label' =>
                $student->name,

                'data' => [
                    $masuk,
                    $alpa,
                    $izin,
                    $sakit,
                    $holiday,
                ],

                // dibuat juga supaya gampang sorting
                'present' =>
                $masuk,

                'absent' =>
                $alpa,

                'permission' =>
                $izin,

                'sick' =>
                $sakit,

                'holiday' =>
                $holiday,

                'batch_id' =>
                $student->internshipBatch
                    ? $student->internshipBatch->id
                    : null,
            ];

            // =====================================================
            // TANGGAL SAKIT
            // =====================================================
            $tanggalSakit = $presences
                ->where('status', 'sick')
                ->map(function ($presence) {

                    $tanggal = $presence->check_in
                        ?? $presence->created_at;

                    return Carbon::parse($tanggal)
                        ->locale('id')
                        ->translatedFormat('d');
                })
                ->implode(', ');

            // =====================================================
            // TANGGAL IZIN
            // =====================================================
            $tanggalIzin = $presences
                ->where('status', 'permission')
                ->map(function ($presence) {

                    $tanggal = $presence->check_in
                        ?? $presence->created_at;

                    return Carbon::parse($tanggal)
                        ->locale('id')
                        ->translatedFormat('d');
                })
                ->implode(', ');

            // =====================================================
            // TANGGAL LIBUR
            // =====================================================
            $tanggalLibur = $presences
                ->where('status', 'holiday')
                ->map(function ($presence) {

                    $tanggal = $presence->check_in
                        ?? $presence->created_at;

                    return Carbon::parse($tanggal)
                        ->locale('id')
                        ->translatedFormat('d');
                })
                ->implode(', ');

            // =====================================================
            // KETERANGAN
            // =====================================================
            $keterangan = collect();

            if ($tanggalSakit) {
                $keterangan->push(
                    'Sakit tanggal ' . $tanggalSakit
                );
            }

            if ($tanggalIzin) {
                $keterangan->push(
                    'Izin tanggal ' . $tanggalIzin
                );
            }

            if ($tanggalLibur) {
                $keterangan->push(
                    'Libur tanggal ' . $tanggalLibur
                );
            }

            $keterangan =
                $keterangan->implode('; ');

            // =====================================================
            // HITUNG HARI EFEKTIF
            //
            // Minggu tidak dihitung.
            // Holiday juga tidak dihitung sebagai hari efektif.
            // =====================================================
            $hariEfektif = 0;

            $periodeStart = null;
            $periodeEnd = null;

            // Filter tanggal
            if ($request->filled('start_date')) {

                $periodeStart = Carbon::parse(
                    $request->start_date
                )->startOfDay();

                $periodeEnd = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : Carbon::parse($request->start_date)->endOfDay();
            }
            // Filter bulan
            elseif ($request->filled('start_month')) {

                $periodeStart = Carbon::parse(
                    $request->start_month
                )->startOfMonth();

                $periodeEnd = $request->filled('end_month')
                    ? Carbon::parse($request->end_month)->endOfMonth()
                    : Carbon::parse($request->start_month)->endOfMonth();
            }

            // Hitung Senin-Sabtu
            if ($periodeStart && $periodeEnd) {

                for (
                    $date = $periodeStart->copy()->startOfDay();
                    $date->lte($periodeEnd->copy()->startOfDay());
                    $date->addDay()
                ) {

                    if (!$date->isSunday()) {
                        $hariEfektif++;
                    }
                }

                // ================================================
                // Kurangi hari libur
                //
                // Gunakan UNIQUE DATE agar jika ada data holiday
                // ganda pada tanggal yang sama tidak terhitung 2x.
                // ================================================
                $jumlahHariLibur = $presences
                    ->where('status', 'holiday')
                    ->map(function ($presence) {

                        $tanggal = $presence->check_in
                            ?? $presence->created_at;

                        return Carbon::parse($tanggal)
                            ->timezone('Asia/Jakarta')
                            ->format('Y-m-d');
                    })
                    ->unique()
                    ->count();

                $hariEfektif =
                    max(
                        0,
                        $hariEfektif - $jumlahHariLibur
                    );
            }

            // =====================================================
            // REKAP TABLE
            // =====================================================
            $rekapTable[] = [

                'nama' =>
                $student->name,

                'kelas' =>
                $student->class->name ?? '-',

                'dudi' =>
                $student->internshipPlace->name ?? '-',

                'pembimbing' =>
                $student->mentor->name ?? '-',

                'hari_efektif' =>
                $hariEfektif,

                'masuk' =>
                $masuk,

                'sakit' =>
                $sakit,

                'izin' =>
                $izin,

                'alpa' =>
                $alpa,

                // STATUS LIBUR
                'holiday' =>
                $holiday,

                'libur' =>
                $holiday,

                'lainnya' =>
                $lainnya,

                'keterangan' =>
                $keterangan ?: '-',
            ];
        }

        // =========================================================
        // SORTING CHART
        // Siswa dengan kehadiran terbanyak di atas
        // =========================================================
        $chartData = collect($chartData)
            ->sortByDesc('present')
            ->map(function ($item) {

                return [

                    'label' =>
                    $item['label'],

                    'data' =>
                    $item['data'],

                    'batch_id' =>
                    $item['batch_id'],

                    'present' =>
                    $item['present'],

                    'absent' =>
                    $item['absent'],

                    'permission' =>
                    $item['permission'],

                    'sick' =>
                    $item['sick'],

                    'holiday' =>
                    $item['holiday'],
                ];
            })
            ->values()
            ->all();

        // =========================================================
        // SORTING REKAP
        // =========================================================
        $rekapTable = collect($rekapTable)
            ->sortByDesc('masuk')
            ->values()
            ->all();

        // =========================================================
        // DROPDOWN FILTER
        // =========================================================

        if ($role === 'super-admin') {

            // Siswa batch aktif
            $studentsForFilter = Student::whereHas(
                'internshipBatch',
                function ($q) {
                    $q->where(
                        'status_batch',
                        'active'
                    );
                }
            )
                ->orderBy('name')
                ->get();

            // Batch aktif
            $batchesForFilter = InternshipBatch::where(
                'status_batch',
                'active'
            )
                ->orderBy('batch_name')
                ->get();

            // Semua kelas
            $classesForFilter = ClassModel::orderBy(
                'name'
            )->get();
        } else {

            // =====================================================
            // FILTER DROPDOWN MENTOR
            // =====================================================

            $studentsForFilter = Student::where(
                'mentor_id',
                $mentorId
            )
                ->whereHas(
                    'internshipBatch',
                    function ($q) {
                        $q->where(
                            'status_batch',
                            'active'
                        );
                    }
                )
                ->orderBy('name')
                ->get();

            $batchesForFilter = InternshipBatch::whereHas(
                'students',
                function ($q) use ($mentorId) {

                    $q->where(
                        'mentor_id',
                        $mentorId
                    );
                }
            )
                ->where(
                    'status_batch',
                    'active'
                )
                ->orderBy('batch_name')
                ->get();

            $classesForFilter = ClassModel::whereHas(
                'students',
                function ($q) use ($mentorId) {

                    $q->where(
                        'mentor_id',
                        $mentorId
                    )
                        ->whereHas(
                            'internshipBatch',
                            function ($q2) {

                                $q2->where(
                                    'status_batch',
                                    'active'
                                );
                            }
                        );
                }
            )
                ->orderBy('name')
                ->get();
        }

        // =========================================================
        // RESPONSE JSON
        // =========================================================
        return response()->json([

            'presenceTable' =>
            $presenceTable,

            'yearResult' =>
            $yearResult,

            'batchNameIdentity' =>
            $batchName,

            'classNameIdentity' =>
            $className,

            'attendanceData' =>
            $chartData,

            'rekapTable' =>
            $rekapTable,

            'students' =>
            $studentsForFilter,

            'batches' =>
            $batchesForFilter,

            'classes' =>
            $classesForFilter,
        ]);
    }

    // app/Http/Controllers/DashboardController.php
    public function delete($id)
    {
        try {
            // Misal modelnya Presence
            $presence = Presence::findOrFail($id);
            $presence->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }
}
