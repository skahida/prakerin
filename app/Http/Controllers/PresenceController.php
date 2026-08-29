<?php

namespace App\Http\Controllers;

use App\Models\InternshipBatch;
use Carbon\Carbon;
use App\Models\Presence;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PresenceController extends Controller
{
    public function index()
    {
        $title = "Riwayat Presensi";

        // Jika peran adalah 'student'
        if (session('ses_role') == 'student') {
            $presences = $this->getStudentPresences();
            return view('presence.index', compact('title', 'presences'));
        } elseif (session('ses_role') == 'mentor') {
            // Ambil ID mentor dari session
            $mentorId = session('ses_mentor_id');

            // Pastikan mentor_id valid
            if (!$mentorId) {
                return redirect()->route('home')->with('error', 'Mentor ID tidak ditemukan.');
            }

            // Pagination for today's presences
            $presences = Presence::with([
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
                ->whereDate('check_in', Carbon::today())  // Filter hanya presensi hari ini
                ->orderBy('check_in', 'desc')  // Urutkan berdasarkan check_in
                ->paginate(10); // Limit to 10 records per page

            // dd($presences);

            $historyPresences = Presence::with([
                'student.internshipBatch',  // Eager loading internshipBatch
                'student.internshipPlace',  // Eager loading internshipPlace
                'student.class',            // Eager loading class
                'student.department'        // Eager loading department
            ])
                ->whereHas('student', function ($query) use ($mentorId) {
                    // Filter by mentor_id
                    $query->where('mentor_id', $mentorId);
                })
                ->orderBy('created_at', 'desc')  // Order by created_at (or another field)
                ->paginate(10);  // Pagination with 10 results per page

            // dd($historyPresences);

            return view('presence.index', compact('title', 'presences', 'historyPresences'));
        }
    }

    private function getStudentPresences()
    {
        $studentId = session('ses_student_id');
        $query = Presence::where('student_id', $studentId);

        // Apply search filter if there's a date input in the request
        if ($search = request('search')) {
            $query->whereDate('created_at', $search); // Adjust the column name if needed
        }

        // Paginate results, 10 items per page
        return $query->orderBy('created_at', 'desc')->paginate(5);
    }

    public function history(Request $request)
    {
        $title = "Riwayat Presensi";

        if (session('ses_role') == 'admin' || session('ses_role') == 'super-admin') {

            $search      = $request->input('search', '');
            $batchName   = $request->input('batch_search');
            $startMonth  = $request->input('start_month'); // Format: 'YYYY-MM'
            $endMonth    = $request->input('end_month');   // Format: 'YYYY-MM'
            $startDate   = $request->input('start_date');
            $endDate     = $request->input('end_date');

            $historyPresencesQuery = Presence::with([
                'student.internshipBatch',
                'student.internshipPlace',
                'student.class',
                'student.department'
            ])->orderBy('check_in', 'desc');

            // Filter berdasarkan nama siswa
            if ($search) {
                $historyPresencesQuery->whereHas('student', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            }

            // Filter berdasarkan batch
            if ($batchName) {
                $historyPresencesQuery->whereHas('student.internshipBatch', function ($query) use ($batchName) {
                    $query->where('id', $batchName);
                });
            }

            // Filter berdasarkan rentang bulan
            if ($startMonth && $endMonth) {
                $start = Carbon::parse($startMonth)->startOfMonth();
                $end   = Carbon::parse($endMonth)->endOfMonth();
                $historyPresencesQuery->whereBetween('check_in', [$start, $end]);
            } elseif ($startMonth) {
                $start = Carbon::parse($startMonth)->startOfMonth();
                $historyPresencesQuery->where('check_in', '>=', $start);
            } elseif ($endMonth) {
                $end = Carbon::parse($endMonth)->endOfMonth();
                $historyPresencesQuery->where('check_in', '<=', $end);
            }

            // Filter by date range
            if ($startDate && $endDate) {
                $historyPresencesQuery->whereBetween('check_in', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } elseif ($startDate) {
                $historyPresencesQuery->whereDate('check_in', '>=', Carbon::parse($startDate)->format('Y-m-d'));
            } elseif ($endDate) {
                $historyPresencesQuery->whereDate('check_in', '<=', Carbon::parse($endDate)->format('Y-m-d'));
            }

            $perPage = 5;
            // Pagination + mempertahankan semua query params
            $historyPresences = $historyPresencesQuery->paginate($perPage)
                ->appends([
                    'search'       => $search,
                    'batch_search' => $batchName,
                    'start_month'  => $startMonth,
                    'end_month'    => $endMonth,
                    'start_date'   => $startDate,
                    'end_date'     => $endDate
                ]);


            $batches  = InternshipBatch::orderBy('id', 'desc')->get();
            $students = Student::orderBy('id', 'desc')->get();

            return view('presence.history', compact(
                'title',
                'historyPresences',
                'search',
                'students',
                'batches',
                'batchName',
                'startMonth',
                'endMonth',
                'startDate',
                'endDate'
            ));
        } elseif (session('ses_role') == 'mentor') {
            $mentorId = session('ses_mentor_id');

            if (!$mentorId) {
                return redirect()->route('home')->with('error', 'Mentor ID tidak ditemukan.');
            }

            // Ambil input filter
            $studentId = $request->input('student_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Query presensi
            $query = Presence::with([
                'student.internshipBatch',
                'student.internshipPlace',
                'student.class',
                'student.department'
            ])
                ->whereHas('student', function ($q) use ($mentorId, $studentId) {
                    $q->where('mentor_id', $mentorId)
                        ->whereHas('internshipBatch', function ($subQ) {
                            $subQ->where('status_batch', 'active');
                        });

                    if ($studentId) {
                        $q->where('id', $studentId);
                    }
                });

            // Filter by date range
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } elseif ($startDate) {
                $query->whereDate('created_at', '>=', Carbon::parse($startDate)->format('Y-m-d'));
            } elseif ($endDate) {
                $query->whereDate('created_at', '<=', Carbon::parse($endDate)->format('Y-m-d'));
            }

            // Paginate
            $historyPresences = $query->orderBy('created_at', 'desc')->paginate(10);

            // Ambil list siswa & batch
            $students = Student::where('mentor_id', $mentorId)
                ->whereHas('internshipBatch', fn($q) => $q->where('status_batch', 'active'))
                ->orderBy('id', 'desc')->get();

            $batches = InternshipBatch::orderBy('id', 'desc')->get();

            return view('presence.history', compact('title', 'historyPresences', 'students', 'batches'));
        }

        // elseif (session('ses_role') == 'mentor') {
        //     // Ambil ID mentor dari session
        //     $mentorId = session('ses_mentor_id');

        //     // Pastikan mentor_id valid
        //     if (!$mentorId) {
        //         return redirect()->route('home')->with('error', 'Mentor ID tidak ditemukan.');
        //     }
        //     // Get search term from request
        //     $search = $request->input('search');

        //     // Start the query
        //     $query = Presence::with([
        //         'student.internshipBatch',  // Eager loading internshipBatch
        //         'student.internshipPlace',  // Eager loading internshipPlace
        //         'student.class',            // Eager loading class
        //         'student.department'        // Eager loading department
        //     ])
        //         ->whereHas('student', function ($query) use ($mentorId) {
        //             $query->where('mentor_id', $mentorId);
        //         });

        //     // If search term is provided, filter by date (you can extend this to other fields if needed)
        //     if ($search) {
        //         $query->whereDate('check_in', '=', Carbon::parse($search)->format('Y-m-d'));
        //     }

        //     // Paginate the results
        //     $historyPresences = $query->orderBy('created_at', 'desc')
        //         ->paginate(10);

        //     // Debugging: lihat apakah presensi kosong atau tidak
        //     // dd($presences);


        //     $batches = InternshipBatch::orderBy('id', 'desc')->get();
        //     $students = Student::where('mentor_id', $mentorId)
        //         ->whereHas('internshipBatch', function ($q) {
        //             $q->where('status_batch', 'active');
        //         })
        //         ->orderBy('id', 'desc')
        //         ->get();


        //     return view('presence.history', compact('title', 'historyPresences', 'students', 'batches'));
        // }
    }

    // public function printPresences(Request $request)
    // {
    //     // Capture the filters from the request
    //     $batchName = $request->input('batch_search');
    //     $search = $request->input('search');

    //     // Fetch data for students with the optional filters
    //     $presencesQuery = Presence::with([
    //         'student.internshipBatch',   // Eager loading internshipBatch
    //         'student.internshipPlace',   // Eager loading internshipPlace
    //         'student.class',             // Eager loading class
    //         'student.department'         // Eager loading department
    //     ])
    //         ->join('students', 'students.id', '=', 'presences.student_id')  // Join students table
    //         // ->orderBy('presences.created_at', 'desc')  // Order by created_at in presences
    //         ->orderBy('students.name', 'asc');  // Order by student name

    //     // Apply the search filter if provided
    //     if ($search) {
    //         $presencesQuery->whereHas('student', function ($query) use ($search) {
    //             $query->where('name', 'like', '%' . $search . '%');
    //         });
    //     }

    //     // Apply the batch_name filter if provided
    //     if ($batchName) {
    //         $presencesQuery->whereHas('student.internshipBatch', function ($query) use ($batchName) {
    //             $query->where('id', $batchName);
    //         });
    //     }

    //     // Get the filtered list of students
    //     $historyPresences = $presencesQuery->get();

    //     // Prepare the data to pass to the view
    //     $data = [
    //         'title' => 'Laporan Presensi Siswa',
    //         'date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
    //         'historyPresences' => $historyPresences
    //     ];

    //     // Generate the PDF using SnappyPdf
    //     $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('presence.report', $data)
    //         ->setOption('page-size', 'A4')
    //         ->setOption('orientation', 'Landscape')
    //         ->setOption('margin-top', 20)
    //         ->setOption('margin-left', 15)
    //         ->setOption('margin-right', 15)
    //         ->setOption('margin-bottom', 20);

    //     // Download the generated PDF
    //     return $pdf->download('print_Presence.pdf');
    // }

    // public function print(Request $request)
    // {
    //     $search      = $request->input('search', '');
    //     $batchName   = $request->input('batch_search');
    //     $startMonth  = $request->input('start_month');
    //     $endMonth    = $request->input('end_month');
    //     $startDate   = $request->input('start_date');
    //     $endDate     = $request->input('end_date');

    //     $historyPresencesQuery = Presence::with([
    //         'student.internshipBatch',
    //         'student.internshipPlace',
    //         'student.class',
    //         'student.department'
    //     ])->orderBy('check_in', 'asc'); // ✅ Urut berdasarkan jam masuk paling pagi

    //     // Filter siswa
    //     if ($search) {
    //         $historyPresencesQuery->whereHas('student', function ($query) use ($search) {
    //             $query->where('name', 'like', '%' . $search . '%');
    //         });
    //     }

    //     // Filter gelombang
    //     if ($batchName) {
    //         $historyPresencesQuery->whereHas('student.internshipBatch', function ($query) use ($batchName) {
    //             $query->where('id', $batchName);
    //         });
    //     }

    //     // Filter bulan
    //     if ($startMonth && $endMonth) {
    //         $start = Carbon::parse($startMonth)->startOfMonth();
    //         $end   = Carbon::parse($endMonth)->endOfMonth();
    //         $historyPresencesQuery->whereBetween('created_at', [$start, $end]);
    //     } elseif ($startMonth) {
    //         $start = Carbon::parse($startMonth)->startOfMonth();
    //         $historyPresencesQuery->where('created_at', '>=', $start);
    //     } elseif ($endMonth) {
    //         $end = Carbon::parse($endMonth)->endOfMonth();
    //         $historyPresencesQuery->where('created_at', '<=', $end);
    //     }

    //     // Filter tanggal
    //     if ($startDate && $endDate) {
    //         $historyPresencesQuery->whereBetween('created_at', [
    //             Carbon::parse($startDate)->startOfDay(),
    //             Carbon::parse($endDate)->endOfDay()
    //         ]);
    //     } elseif ($startDate) {
    //         $historyPresencesQuery->whereDate('created_at', '>=', Carbon::parse($startDate)->format('Y-m-d'));
    //     } elseif ($endDate) {
    //         $historyPresencesQuery->whereDate('created_at', '<=', Carbon::parse($endDate)->format('Y-m-d'));
    //     }

    //     $historyPresences = $historyPresencesQuery->get();

    //     return view('presence.print', compact('historyPresences'));
    // }

    public function print(Request $request)
    {
        $search      = $request->input('search', '');
        $batchName   = $request->input('batch_search');
        $startMonth  = $request->input('start_month');
        $endMonth    = $request->input('end_month');
        $startDate   = $request->input('start_date');
        $endDate     = $request->input('end_date');

        $historyPresencesQuery = Presence::with([
            'student.internshipBatch',
            'student.internshipPlace',
            'student.class',
            'student.department'
        ])->orderBy('check_in', 'asc');

        // 🔎 Filter siswa
        if ($search) {
            $historyPresencesQuery->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // 🎓 Filter gelombang
        if ($batchName) {
            $historyPresencesQuery->whereHas('student.internshipBatch', function ($q) use ($batchName) {
                $q->where('id', $batchName);
            });
        }

        // 📆 Filter bulan
        if ($startMonth && $endMonth) {
            $historyPresencesQuery->whereBetween('created_at', [
                \Carbon\Carbon::parse($startMonth)->startOfMonth(),
                \Carbon\Carbon::parse($endMonth)->endOfMonth()
            ]);
        } elseif ($startMonth) {
            $historyPresencesQuery->where(
                'created_at',
                '>=',
                \Carbon\Carbon::parse($startMonth)->startOfMonth()
            );
        } elseif ($endMonth) {
            $historyPresencesQuery->where(
                'created_at',
                '<=',
                \Carbon\Carbon::parse($endMonth)->endOfMonth()
            );
        }

        // 🗓 Filter tanggal
        if ($startDate && $endDate) {
            $historyPresencesQuery->whereBetween('created_at', [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $historyPresencesQuery->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $historyPresencesQuery->whereDate('created_at', '<=', $endDate);
        }

        $historyPresences = $historyPresencesQuery->get();

        // 🧾 Generate PDF (wkhtmltopdf)
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView(
            'presence.print',
            compact('historyPresences')
        )
            ->setPaper('a4')
            ->setOrientation('portrait')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm')
            ->setOption('enable-local-file-access', true);

        return $pdf->inline('laporan-presensi-magang.pdf');
    }





    public function printPresences(Request $request)
    {
        $batchId     = $request->input('batch_search');
        $search      = $request->input('search');
        $classCode   = $request->input('class_code');

        $startMonth  = $request->input('start_month');       // Format: 'YYYY-MM'
        $endMonth    = $request->input('end_month');         // Format: 'YYYY-MM' (optional)
        $startDate   = $request->input('start_date');       // Format: 'YYYY-MM-DD'
        $endDate     = $request->input('end_date');         // Format: 'YYYY-MM-DD' (optional)

        Carbon::setLocale('id');

        // Tentukan range tanggal
        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : $start->copy()->endOfDay();
            $yearResult = $start->translatedFormat('d F Y') . ($endDate ? ' - ' . $end->translatedFormat('d F Y') : '');
        } elseif ($startMonth) {
            $start = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $end   = $endMonth ? Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth() : $start->copy()->endOfMonth();
            $yearResult = $start->translatedFormat('F') . ($endMonth ? ' - ' . $end->translatedFormat('F Y') : ' ' . $start->translatedFormat('Y'));
        } else {
            $start = $end = null;
            $yearResult = '';
        }

        // Query siswa sesuai filter batch, class, search
        $studentsQuery = Student::with(['class', 'internshipBatch'])
            ->whereHas('internshipBatch', fn($q) => $q->where('status_batch', 'active'));

        if ($batchId) {
            $studentsQuery->whereHas('internshipBatch', fn($q) => $q->where('id', $batchId));
            $batch = InternshipBatch::find($batchId);
            $batchName = $batch ? $batch->batch_name : 'Gelombang Tidak Ditemukan';
        } else {
            $batchName = 'Semua Gelombang';
        }

        if ($classCode) {
            $studentsQuery->whereHas('class', fn($q) => $q->where('class_code', $classCode));
        }

        if ($search) {
            $studentsQuery->where('name', 'like', '%' . $search . '%');
        }

        $students = $studentsQuery->orderBy('name', 'asc')->get();

        // Hitung hari efektif
        $hariEfektif = 77;
        // if ($start && $end) {
        //     $hariEfektif = Presence::whereBetween('check_in', [$start, $end])
        //         ->selectRaw('DATE(check_in) as tgl')
        //         ->groupBy('tgl')
        //         ->pluck('tgl')
        //         ->count();
        // }


        $rekap = [];
        foreach ($students as $student) {
            // Hitung kehadiran per status untuk batch aktif
            $presencesQuery = Presence::where('student_id', $student->id)
                ->whereHas('student.internshipBatch', function ($q) {
                    $q->where('status_batch', 'active');
                });

            if ($start && $end) {
                $presencesQuery->whereBetween('created_at', [$start, $end]);
            }

            $presences = $presencesQuery->get();

            // Group by tanggal (hanya tanggal, bukan waktu)
            $presencesByDate = $presences->groupBy(fn($p) => Carbon::parse($p->check_in)->toDateString());

            $masuk = $sakit = $izin = $holiday = $lainnya = 0;

            foreach ($presencesByDate as $date => $entries) {
                // Ambil prioritas status jika ada multiple status di hari yang sama
                // Misal prioritas: present > sick > permission > holiday > lainnya
                if ($entries->contains('status', 'present')) {
                    $masuk++;
                } elseif ($entries->contains('status', 'sick')) {
                    $sakit++;
                } elseif ($entries->contains('status', 'permission')) {
                    $izin++;
                } elseif ($entries->contains('status', 'holiday')) {
                    $holiday++;
                } else {
                    $lainnya++;
                }
            }

            // Hitung alpa otomatis
            $alpa = $hariEfektif - ($masuk + $sakit + $izin + $holiday + $lainnya);
            if ($alpa < 0) $alpa = 0;


            $tgl_sakit = $presences->where('status', 'sick')
                ->pluck('check_in')
                ->map(fn($tgl) => Carbon::parse($tgl)->translatedFormat('d'))
                ->implode(', ');

            $tgl_izin = $presences->where('status', 'permission')
                ->pluck('check_in')
                ->map(fn($tgl) => Carbon::parse($tgl)->translatedFormat('d'))
                ->implode(', ');

            $keterangan = [];
            if ($sakit > 0) $keterangan[] = 'Sakit tanggal ' . $tgl_sakit;
            if ($izin > 0)  $keterangan[] = 'Izin tanggal ' . $tgl_izin;
            if ($alpa > 0)  $keterangan[] = 'Alpa ' . $alpa . ' hari'; // keterangan alpa

            $keteranganStr = !empty($keterangan) ? implode(' | ', $keterangan) : '-';

            $rekap[] = (object)[
                'nama'        => $student->name,
                'kelas'       => $student->class->name ?? '-',
                'hari_efektif' => $hariEfektif,
                'masuk'       => $masuk,
                'sakit'       => $sakit,
                'izin'        => $izin,
                'alpa'        => $alpa,
                'libur'       => $holiday,
                'lainnya'     => $lainnya,
                'keterangan'  => $keteranganStr,
            ];
        }


        $data = [
            'data'      => $rekap,
            'yearResult' => $yearResult,
            'batchName' => $batchName,
            'className' => $classCode,
        ];

        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('presence.report', $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait')
            ->setOption('margin-top', 20)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15)
            ->setOption('margin-bottom', 20)
            ->setOption('enable-local-file-access', true);

        return $pdf->download('Rekap Kehadiran Prakerin ' . ($classCode ?? 'Semua Kelas') . '.pdf');
    }


    // public function checkIn(Request $request)
    // {
    //     // Ambil latitude dan longitude dari request
    //     $latitude = $request->input('check_in_latitude');
    //     $longitude = $request->input('check_in_longitude');
    //     $check_in_location_link = "https://www.google.com/maps?q=";

    //     // Pastikan data diterima
    //     if ($latitude === null || $longitude === null) {
    //         return response()->json(['error' => 'Data geolocation tidak ditemukan'], 400);
    //     }

    //     // Simpan data lat/long ke database
    //     $presence = Presence::create([
    //         'check_in_latitude' => $latitude,
    //         'check_in_longitude' => $longitude,
    //         'check_in_location_link' => $check_in_location_link . $latitude . "," . $longitude,
    //         'student_id' => session('ses_student_id'),  // Asumsi Anda menggunakan autentikasi
    //         'check_in' => now(),
    //     ]);

    //     // Ambil data siswa beserta internshipPlace
    //     $student = $presence->student()->with('internshipPlace', 'mentor')->first();

    //     // Pastikan data siswa ditemukan
    //     if (!$student) {
    //         return response()->json(['error' => 'Data siswa tidak ditemukan'], 400);
    //     }

    //     // Ambil mentor yang mengajar siswa ini
    //     $mentor = $student->mentor;

    //     // Pastikan mentor ditemukan
    //     if (!$mentor) {
    //         return response()->json(['error' => 'Mentor tidak ditemukan untuk siswa ini'], 400);
    //     }

    //     // Kirim notifikasi ke Telegram
    //     $this->sendTelegramNotification($mentor, $student, $presence);
    // }

    public function getStudentLocation($student_id)
    {
        // Ambil data siswa berdasarkan student_id
        $student = Student::with('internshipPlace')->find($student_id);

        // Periksa apakah siswa ditemukan dan memiliki internship_place
        if ($student && $student->internshipPlace) {
            return response()->json([
                'latitude' => $student->internshipPlace->latitude,
                'longitude' => $student->internshipPlace->longitude,
            ]);
        }

        return response()->json(['latitude' => null, 'longitude' => null], 404);
    }

    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'search' => 'required|exists:students,id',
    //         'check_in' => 'required|date',
    //         'check_out' => 'nullable|date|after:check_in',
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //         'status' => 'required|string',
    //         'note' => 'nullable|string',
    //     ], [
    //         'search.required' => 'Nama siswa wajib diisi.',
    //         'search.exists' => 'Nama siswa tidak ditemukan.',
    //         'check_in.required' => 'Waktu presensi masuk wajib diisi.',
    //         'check_in.date' => 'Format waktu presensi masuk tidak valid.',
    //         'check_out.date' => 'Format waktu presensi pulang tidak valid.',
    //         'check_out.after' => 'Waktu presensi pulang harus setelah presensi masuk.',
    //         'latitude.required' => 'Koordinat latitude wajib diisi.',
    //         'latitude.numeric' => 'Koordinat latitude harus berupa angka.',
    //         'longitude.required' => 'Koordinat longitude wajib diisi.',
    //         'longitude.numeric' => 'Koordinat longitude harus berupa angka.',
    //         'status.required' => 'Status wajib diisi.',
    //     ]);

    //     // Cek apakah presensi sudah ada pada tanggal tersebut
    //     $existingPresence = Presence::where('student_id', $request->search)
    //         ->whereDate('check_in', date('Y-m-d', strtotime($request->check_in)))
    //         ->first();

    //     if ($existingPresence) {
    //         $formattedDate = date('d-m-Y', strtotime($existingPresence->check_in));
    //         return redirect()->back()->withInput()->withErrors(['check_in' => "Presensi untuk tanggal $formattedDate sudah ada."]);
    //     }

    //     // Cari siswa berdasarkan nama
    //     $student = Student::where('id', $request->search)->firstOrFail();
    //     $check_location_link = "https://www.google.com/maps?q=";

    //     // Simpan data presensi
    //     Presence::create([
    //         'student_id' => $student->id,
    //         'check_in' => $request->check_in,
    //         'check_out' => $request->check_out,
    //         'check_in_latitude' => $request->latitude,
    //         'check_in_longitude' => $request->longitude,
    //         'check_out_latitude' => $request->latitude,
    //         'check_out_longitude' => $request->longitude,
    //         'check_in_location_link' => $check_location_link . $request->latitude . "," . $request->longitude,
    //         'check_out_location_link' => $check_location_link . $request->latitude . "," . $request->longitude,
    //         'status' => $request->status,
    //         'note' => $request->note,
    //     ]);

    //     return redirect()->route('history.presence')->with('success', 'Presensi berhasil disimpan.');
    // }

    public function store(Request $request)
    {
        if (session('ses_role') == 'mentor') {
            // Validasi input
            $request->validate([
                'search' => 'required|exists:students,id',
                'check_in' => 'required|date',
                'check_in.required' => 'Tanggal wajib diisi.',
                'check_in.date' => 'Format waktu tidak valid.',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'status' => 'required|string',
                'note' => 'nullable|string',
            ], [
                'search.required' => 'Nama siswa wajib diisi.',
                'search.exists' => 'Nama siswa tidak ditemukan.',
                'latitude.required' => 'Koordinat latitude wajib diisi.',
                'latitude.numeric' => 'Koordinat latitude harus berupa angka.',
                'longitude.required' => 'Koordinat longitude wajib diisi.',
                'longitude.numeric' => 'Koordinat longitude harus berupa angka.',
                'status.required' => 'Status wajib diisi.',
            ]);

            // Cari siswa
            $student = Student::findOrFail($request->search);

            // // Cek apakah sudah ada presensi hari ini
            // $today = Carbon::today();
            // $existingPresence = Presence::where('student_id', $student->id)
            //     ->whereDate('check_in', $today)
            //     ->first();

            // if ($existingPresence) {
            //     $formattedDate = $today->format('d-m-Y');
            //     return redirect()->back()->withInput()
            //         ->withErrors(['check_in' => "Presensi untuk tanggal $formattedDate sudah ada."]);
            // }

            // Ambil tanggal dari check_in
            $checkInDate = Carbon::parse($request->check_in)->toDateString();

            // Cek apakah sudah ada presensi di tanggal yang sama (berdasarkan created_at)
            $existingPresence = Presence::where('student_id', $student->id)
                ->whereDate('created_at', $checkInDate)
                ->where('status', $request->status)
                ->first();

            if ($existingPresence) {
                $formattedDate = Carbon::parse($request->check_in)->format('d-m-Y');
                return back()->withInput()
                    ->withErrors(['check_in' => "Presensi untuk tanggal $formattedDate sudah ada dengan status {$existingPresence->status}."]);
            }

            // Lokasi
            $check_location_link = "https://www.google.com/maps?q={$request->latitude},{$request->longitude}";

            // Simpan presensi baru (check-in otomatis sekarang)
            $presence = Presence::create([
                'student_id' => $student->id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_out_latitude' => null,
                'check_out_longitude' => null,
                'check_in_location_link' => $check_location_link,
                'check_out_location_link' => null,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            // $presence->created_at = $request->check_in;
            // $presence->updated_at = $request->check_in;
            $presence->save();

            return redirect()->route('history.presence')->with('success', 'Presensi berhasil disimpan.');
        } elseif (session('ses_role') == 'super-admin') {
            // Validasi input
            $request->validate([
                'search' => 'required|exists:students,id',
                'check_in' => 'required|date',
                'check_out' => 'nullable|date|after:check_in',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'status' => 'required|string',
                'note' => 'nullable|string',
            ], [
                'search.required' => 'Nama siswa wajib diisi.',
                'search.exists' => 'Nama siswa tidak ditemukan.',
                'check_in.required' => 'Waktu presensi masuk wajib diisi.',
                'check_in.date' => 'Format waktu presensi masuk tidak valid.',
                'check_out.date' => 'Format waktu presensi pulang tidak valid.',
                'check_out.after' => 'Waktu presensi pulang harus setelah presensi masuk.',
                'latitude.required' => 'Koordinat latitude wajib diisi.',
                'latitude.numeric' => 'Koordinat latitude harus berupa angka.',
                'longitude.required' => 'Koordinat longitude wajib diisi.',
                'longitude.numeric' => 'Koordinat longitude harus berupa angka.',
                'status.required' => 'Status wajib diisi.',
            ]);

            // Cek apakah presensi sudah ada pada tanggal tersebut
            $existingPresence = Presence::where('student_id', $request->search)
                ->whereDate('check_in', date('Y-m-d', strtotime($request->check_in)))
                ->first();

            if ($existingPresence) {
                $formattedDate = date('d-m-Y', strtotime($existingPresence->check_in));
                return redirect()->back()->withInput()->withErrors(['check_in' => "Presensi untuk tanggal $formattedDate sudah ada."]);
            }

            // Cari siswa berdasarkan nama
            $student = Student::where('id', $request->search)->firstOrFail();
            $check_location_link = "https://www.google.com/maps?q=";

            // Simpan data presensi
            Presence::create([
                'student_id' => $student->id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_out_latitude' => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'check_in_location_link' => $check_location_link . $request->latitude . "," . $request->longitude,
                'check_out_location_link' => $check_location_link . $request->latitude . "," . $request->longitude,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            return redirect()->route('history.presence')->with('success', 'Presensi berhasil disimpan.');
        }
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Presensi";

        // Get the search term from the request, if any
        $search = request('search', '');
        $batchName = $request->input('batch_search');

        // Query the Presence model and eager load the relationships
        $historyPresencesQuery = Presence::with([
            'student.internshipBatch',   // Eager loading internshipBatch
            'student.internshipPlace',   // Eager loading internshipPlace
            'student.class',             // Eager loading class
            'student.department'         // Eager loading department
        ])
            ->orderBy('created_at', 'desc');  // Sort by created_at or another field

        // If there is a search term, filter the records by student name
        if ($search) {
            $historyPresencesQuery->whereHas('student', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        // Apply the batch_name filter if provided
        if ($batchName) {
            $historyPresencesQuery->whereHas('student.internshipBatch', function ($query) use ($batchName) {
                $query->where('id', $batchName);
            });
        }

        // Get the results with pagination (5 results per page)
        $perPage = 5;
        $historyPresences = $historyPresencesQuery->paginate($perPage)
            ->appends(request()->query());

        $batches = InternshipBatch::orderBy('id', 'desc')->get();
        $students = Student::orderBy('id', 'desc')->get();

        $studentEdit = Presence::with([
            'student.internshipBatch',
            'student.internshipPlace',
            'student.class',
            'student.department'
        ])
            ->where('id', $id)
            ->first();  // first() returns a single model

        return view('presence.history', compact('title', 'historyPresences', 'search', 'students', 'studentEdit', 'batches', 'batchName'));
    }

    // public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'search' => 'required|exists:students,id',
    //         'check_in' => 'required|date',
    //         'check_out' => 'nullable|date|after:check_in',
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //         'status' => 'required|string',
    //         'note' => 'nullable|string',
    //     ], [
    //         'search.required' => 'Nama siswa wajib diisi.',
    //         'search.exists' => 'Nama siswa tidak ditemukan.',
    //         'check_in.required' => 'Waktu presensi masuk wajib diisi.',
    //         'check_in.date' => 'Format waktu presensi masuk tidak valid.',
    //         'check_out.date' => 'Format waktu presensi pulang tidak valid.',
    //         'check_out.after' => 'Waktu presensi pulang harus setelah presensi masuk.',
    //         'latitude.required' => 'Koordinat latitude wajib diisi.',
    //         'latitude.numeric' => 'Koordinat latitude harus berupa angka.',
    //         'longitude.required' => 'Koordinat longitude wajib diisi.',
    //         'longitude.numeric' => 'Koordinat longitude harus berupa angka.',
    //         'status.required' => 'Status wajib diisi.',
    //     ]);

    //     // Cari siswa berdasarkan ID
    //     $student = Student::findOrFail($request->search);
    //     $check_location_link = "https://www.google.com/maps?q=";

    //     // Update data presensi
    //     $presence = Presence::findOrFail($id);
    //     $presence->update([
    //         'student_id' => $student->id,
    //         'check_in' => $request->check_in,
    //         'check_out' => $request->check_out,
    //         'check_in_latitude' => $request->latitude,
    //         'check_in_longitude' => $request->longitude,
    //         'check_out_latitude' => $request->latitude,
    //         'check_out_longitude' => $request->longitude,
    //         'check_in_location_link' => $check_location_link . $request->latitude . "," . $request->longitude,
    //         'check_out_location_link' => $check_location_link . $request->latitude . "," . $request->longitude,
    //         'status' => $request->status,
    //         'note' => $request->note,
    //     ]);

    //     return redirect()->route('history.presence')->with('success', 'Presensi berhasil diperbarui.');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'search' => 'required|exists:students,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string',
            'note' => 'nullable|string',
            'check_in' => 'required|date',
            'check_out' => 'nullable|date|after:check_in',
        ], [
            'search.required' => 'Nama siswa wajib diisi.',
            'search.exists' => 'Nama siswa tidak ditemukan.',
            'latitude.required' => 'Koordinat latitude wajib diisi.',
            'latitude.numeric' => 'Koordinat latitude harus berupa angka.',
            'longitude.required' => 'Koordinat longitude wajib diisi.',
            'longitude.numeric' => 'Koordinat longitude harus berupa angka.',
            'status.required' => 'Status wajib diisi.',
        ]);

        // Cari siswa
        $student = Student::findOrFail($request->search);

        $check_location_link = "https://www.google.com/maps?q={$request->latitude},{$request->longitude}";

        // Ambil presensi
        $presence = Presence::findOrFail($id);

        // Update otomatis dengan timestamp
        $presence->update([
            'student_id' => $student->id,
            'check_in' => $presence->check_in ?? Carbon::now(), // kalau belum ada, isi sekarang
            'check_out' => $request->check_out, // waktu update dianggap checkout
            'check_in_latitude' => $presence->check_in_latitude ?? $request->latitude,
            'check_in_longitude' => $presence->check_in_longitude ?? $request->longitude,
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_in_location_link' => $presence->check_in_location_link ?? $check_location_link,
            'check_out_location_link' => $check_location_link,
            'status' => $request->status,
            'note' => $request->note,
        ]);

        return redirect()->route('history.presence')->with('success', 'Presensi berhasil diperbarui.');
    }

    public function checkIn(Request $request)
    {
        $latitude  = $request->input('check_in_latitude');
        $longitude = $request->input('check_in_longitude');
        $photo     = $request->file('proof_photo');

        if ($latitude === null || $longitude === null) {
            return response()->json([
                'success' => false,
                'error'   => 'Data geolocation tidak ditemukan'
            ], 400);
        }

        $student = Student::with('internshipPlace', 'mentor')
            ->where('id', session('ses_student_id'))
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'error'   => 'Data siswa tidak ditemukan'
            ], 400);
        }

        $place = $student->internshipPlace;

        if (!$place) {
            return response()->json([
                'success' => false,
                'error'   => 'Data tempat prakerin (DUDI) tidak ditemukan'
            ], 400);
        }

        // Ambil koordinat DUDI (sesuaikan nama kolom jika berbeda)
        $dudiLat = $place->latitude ?? $place->lat ?? null;
        $dudiLng = $place->longitude ?? $place->long ?? $place->lng ?? null;

        $isOutsideRadius = false;
        $distance = null;

        if ($dudiLat !== null && $dudiLng !== null) {
            // Hitung jarak
            $distance = $this->calculateDistance(
                (float) $latitude,
                (float) $longitude,
                (float) $dudiLat,
                (float) $dudiLng
            );

            $radiusMeter = 200; // toleransi 200 meter

            if ($distance > $radiusMeter) {
                $isOutsideRadius = true;
            }
        } else {
            // Jika koordinat DUDI kosong → anggap di luar radius (wajib foto)
            $isOutsideRadius = true;
        }

        // ===== WAJIB FOTO JIKA DI LUAR RADIUS =====
        if ($isOutsideRadius && !$photo) {
            $message = $distance !== null
                ? 'Lokasi Anda di luar area DUDI (jarak ±' . round($distance) . ' m). Silakan upload foto sebagai bukti.'
                : 'Koordinat DUDI belum diatur / lokasi tidak cocok. Silakan upload foto sebagai bukti presensi.';

            return response()->json([
                'success'       => false,
                'require_photo' => true,
                'message'       => $message,
                'distance'      => $distance !== null ? round($distance) : null,
            ], 422);
        }

        // Simpan foto jika ada
        $photoPath = null;
        if ($photo) {
            $photoPath = $photo->store('presence-photos', 'public');
        }

        $currentTime = Carbon::now();
        $check_in_location_link = "https://www.google.com/maps?q={$latitude},{$longitude}";

        $presence = Presence::create([
            'check_in_latitude'      => $latitude,
            'check_in_longitude'     => $longitude,
            'check_in_location_link' => $check_in_location_link,
            'student_id'             => $student->id,
            'check_in'               => $currentTime,
            'proof_photo'            => $photoPath,
            'status'                 => 'present', // pastikan ada default status
            'note'                   => $isOutsideRadius
                ? ($distance !== null
                    ? 'Presensi di luar radius DUDI (±' . round($distance) . ' m) – dilengkapi foto bukti'
                    : 'Presensi dengan foto bukti (koordinat DUDI tidak tersedia)')
                : null,
        ]);

        // Kirim notifikasi Telegram
        $mentor = $student->mentor;
        if ($mentor) {
            $this->sendTelegramNotification($mentor, $student, $presence);
        }

        return response()->json([
            'success' => true,
            'message' => $isOutsideRadius
                ? 'Presensi masuk berhasil (dengan foto bukti karena di luar radius).'
                : 'Presensi masuk berhasil.',
        ]);
    }

    /**
     * Hitung jarak antara 2 koordinat (meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
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
        // Ambil latitude dan longitude dari request
        $latitude = $request->input('check_out_latitude');
        $longitude = $request->input('check_out_longitude');
        $check_out_location_link = "https://www.google.com/maps?q=";

        // Pastikan data diterima
        if ($latitude === null || $longitude === null) {
            return response()->json(['error' => 'Data geolocation tidak ditemukan'], 400);
        }

        // Ambil student_id dari session
        $student_id = session('ses_student_id');

        // Ambil tanggal hari ini (format YYYY-MM-DD)
        $today = Carbon::today();

        // Cari data presensi berdasarkan student_id, pastikan check-in sudah ada, dan presensi dilakukan hari ini
        $presence = Presence::where('student_id', $student_id)
            ->whereNotNull('check_in')  // Pastikan check-in sudah dilakukan
            ->whereNull('check_out')    // Pastikan check-out belum dilakukan
            ->whereDate('check_in', $today)  // Hanya presensi hari ini
            ->first();

        if (!$presence) {
            return response()->json(['error' => 'Anda belum melakukan presensi masuk atau sudah melakukan presensi pulang'], 400);
        }

        // Update data check-out
        $presence->update([
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_location_link' => $check_out_location_link . $latitude . "," . $longitude,
            'check_out' => now(),  // Menyimpan waktu check-out
        ]);

        // Ambil data siswa beserta internshipPlace
        $student = $presence->student()->with('internshipPlace', 'mentor')->first();

        // Pastikan data siswa ditemukan
        if (!$student) {
            return response()->json(['error' => 'Data siswa tidak ditemukan'], 400);
        }

        // Ambil mentor yang mengajar siswa ini
        $mentor = $student->mentor;

        // Pastikan mentor ditemukan
        if (!$mentor) {
            return response()->json(['error' => 'Mentor tidak ditemukan untuk siswa ini'], 400);
        }

        // Kirim notifikasi ke Telegram untuk check-out
        $this->sendTelegramNotificationCheckOut($mentor, $student, $presence);
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

    public function ChatId()
    {
        // Token API bot Telegram
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Pastikan Anda menyimpan token bot di .env file
        $chatId = 5999517292;
        $message = "tes";
        $response = Http::get("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Gagal mengirim notifikasi Telegram',
                'response' => $response->body()  // Tampilkan response untuk debugging
            ], 500);
        }

        return response()->json(['message' => 'Notifikasi Telegram berhasil dikirim']);
    }
}
