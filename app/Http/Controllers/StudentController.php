<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Department;
use App\Models\InternshipBatch;
use App\Models\InternshipPlace;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\User;
use App\Models\Presence;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Siswa Prakerin";
        if (session('ses_role') == 'admin' || session('ses_role') == 'super-admin') {

            $users = User::all();

            // Get search and batch_search values from the request
            $search = $request->input('search');
            $batchSearch = $request->input('batch_search');  // Filter for internship batch

            $students = Student::with([
                'user',              // Eager loading user
                'internshipBatch',   // Eager loading internshipBatch
                'internshipPlace',   // Eager loading internshipPlace
                'class',             // Eager loading class
                'department'         // Eager loading department
            ])
                ->where(function ($query) use ($search, $batchSearch) {
                    // If search is provided, filter by student name (first_name or last_name)
                    if ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                    }

                    // Apply the batch_name filter if provided
                    if ($batchSearch) {
                        $query->whereHas('internshipBatch', function ($query) use ($batchSearch) {
                            $query->where('id',  $batchSearch);
                        });
                    }
                })
                // Pastikan hanya mengambil students yang memiliki user dengan is_active = 1
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 1);  // Menyaring user dengan is_active = 1
                })
                ->orderBy('id', 'desc')  // Sorting by ID (can be changed to another field if needed)
                ->paginate(10);  // Paginate results with 10 per page

            // Add search and batch_search parameters to pagination links
            $students = $students->appends(['search' => $search, 'batch_search' => $batchSearch]);

            // Additional data to populate form options
            $studentAll = Student::with(['user'])  // Memuat relasi user
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 1);  // Menambahkan kondisi is_active pada tabel users
                })
                ->orderBy('id', 'desc')  // Mengurutkan berdasarkan id
                ->get();

            $batches = InternshipBatch::orderBy('id', 'desc')->get();

            $classes = ClassModel::all();
            $departments = Department::all();
            $dudies = InternshipPlace::all();
            $mentors = Mentor::all();

            // Return view with all necessary data
            return view('student.index', compact(
                'title',
                'users',
                'classes',
                'departments',
                'dudies',
                'batches',
                'mentors',
                'students',
                'search',
                'batchSearch',
                'studentAll'
            ));
        } elseif (session('ses_role') == 'mentor') {
            // Ambil ID mentor dari session
            $mentorId = session('ses_mentor_id');
            if (!$mentorId) {
                return redirect()->route('home')->with('error', 'Mentor ID tidak ditemukan.');
            }

            // Ambil input pencarian dari request
            $search = $request->input('search');

            // Mulai query untuk mengambil data siswa
            $query = Student::with([
                'internshipBatch',  // Eager loading internshipBatch
                'internshipPlace',  // Eager loading internshipPlace
                'class',            // Eager loading class
                'department'        // Eager loading department
            ])
                ->where('mentor_id', $mentorId) // Filter berdasarkan mentor_id
                ->whereHas('internshipBatch', function ($q) {
                    $q->where('status_batch', 'active'); // Hanya batch aktif
                });

            // Jika ada input pencarian, filter berdasarkan nama siswa atau kelas
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('class', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Paginate dengan 5 hasil per halaman
            $students = $query->orderBy('name', 'asc')->paginate(5);

            // Return view dengan data siswa dan pencarian
            return view('student.index', [
                'title'    => $title,
                'students' => $students,
                'search'   => $search,  // Pass search value to view for repopulating search input
            ]);
        }
    }
    //     elseif (session('ses_role') == 'mentor') {
    //         // Ambil ID mentor dari session
    //         $mentorId = session('ses_mentor_id');
    //         if (!$mentorId) {
    //             return redirect()->route('home')->with('error', 'Mentor ID tidak ditemukan.');
    //         }

    //         // Ambil input pencarian dari request
    //         $search = $request->input('search');

    //         // Mulai query untuk mengambil data siswa
    //         $query = Student::with([
    //             'internshipBatch',  // Eager loading internshipBatch
    //             'internshipPlace',  // Eager loading internshipPlace
    //             'class',            // Eager loading class
    //             'department'        // Eager loading department
    //         ])
    //             ->where('mentor_id', $mentorId);  // Filter berdasarkan mentor_id

    //         // Jika ada input pencarian, filter berdasarkan nama siswa atau kelas
    //         if ($search) {
    //             $query->where(function ($q) use ($search) {
    //                 $q->where('name', 'like', "%{$search}%")
    //                     ->orWhereHas('class', function ($q) use ($search) {
    //                         $q->where('name', 'like', "%{$search}%");
    //                     });
    //             });
    //         }

    //         // Paginate dengan 10 hasil per halaman
    //         $students = $query->orderBy('name', 'asc')->paginate(5);

    //         // Return view dengan data siswa dan pencarian
    //         return view('student.index', [
    //             'title'    => $title,
    //             'students' => $students,
    //             'search'   => $search,  // Pass search value to view for repopulating search input
    //         ]);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     // Validasi input, termasuk validasi unik untuk letter_code
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'nis' => 'required|string|max:10|unique:students',
    //         'gender' => 'required|string|max:255',
    //         'whatsapp_number' => 'required|string|max:16',
    //         'telegram_number' => 'required|string|max:255',
    //         'class_code' => 'required|string|max:255',
    //         'department_code' => 'required|string|max:255',
    //         'internship_place_code' => 'required|string|max:255',
    //         'internship_batch_id' => 'required|string|max:255',
    //         'username' => 'required|string|max:255',
    //         'password' => 'required|string|max:255',
    //     ], [
    //         'name.required' => 'Nama tidak boleh kosong',
    //         'nis.required' => 'NIS tidak boleh kosong',
    //         'gender.required' => 'Jenis kelamin tidak boleh kosong',
    //         'whatsapp_number.required' => 'Nomor WhatsApp tidak boleh kosong',
    //         'telegram_number.required' => 'Nomor Telegram tidak boleh kosong',
    //         'class_code.required' => 'Kelas tidak boleh kosong',
    //         'department_code.required' => 'Jurusan tidak boleh kosong',
    //         'internship_place_code.required' => 'Dudi tidak boleh kosong',
    //         'internship_batch_id.required' => 'Gelombang tidak boleh kosong',
    //         'username.required' => 'Username tidak boleh kosong',
    //         'password.required' => 'Password tidak boleh kosong',
    //     ]);

    //     // Persiapkan data untuk membuat data Letter baru
    //     // $data = $request->all();

    //     // // Membuat data Letter baru dengan data yang sudah dipersiapkan
    //     // Mentor::create($data);

    //     // Simpan data
    //     $user = User::create([
    //         'name' => $request->name,
    //         'username' => $request->username,
    //         'password' => bcrypt($request->password),
    //         'role' => "student",
    //         'is_active' => true,
    //     ]);

    //     // Simpan data 
    //     Student::create([
    //         'name' => $request->name,
    //         'nis' => $request->nis,
    //         'gender' => $request->gender,
    //         'whatsapp_number' => $request->whatsapp_number,
    //         'telegram_number' => $request->telegram_number,
    //         'class_code' => $request->class_code,
    //         'department_code' => $request->department_code,
    //         'internship_place_code' => $request->internship_place_code,
    //         'internship_batch_id' => $request->internship_batch_id,
    //         'mentor_id' => $request->mentor_id,
    //         'user_id' => $user->id, // Mengaitkan dengan pengguna yang baru dibuat
    //     ]);

    //     // dd($data);

    //     // Redirect dengan pesan sukses
    //     return redirect()->route('student')->with('success', 'Data berhasil disimpan.');
    // }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:10|unique:students',
            'gender' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:16',
            'telegram_number' => 'required|string|max:255',
            'class_code' => 'required|string|max:255',
            'department_code' => 'required|string|max:255',
            'internship_place_code' => 'required|string|max:255',
            'internship_batch_id' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|max:255',
            'foto_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // validasi foto
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'nis.required' => 'NIS tidak boleh kosong',
            'gender.required' => 'Jenis kelamin tidak boleh kosong',
            'whatsapp_number.required' => 'Nomor WhatsApp tidak boleh kosong',
            'telegram_number.required' => 'Nomor Telegram tidak boleh kosong',
            'class_code.required' => 'Kelas tidak boleh kosong',
            'department_code.required' => 'Jurusan tidak boleh kosong',
            'internship_place_code.required' => 'Dudi tidak boleh kosong',
            'internship_batch_id.required' => 'Gelombang tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'username.unique' => 'Username sudah dipakai',
            'password.required' => 'Password tidak boleh kosong',
            'foto_url.image' => 'File harus berupa gambar (jpg, jpeg, png)',
            'foto_url.max' => 'Ukuran foto maksimal 2MB',
        ]);

        // Simpan user baru
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->role = "student";
        $user->is_active = true;

        // Upload foto jika ada
        if ($request->hasFile('foto_url')) {
            $path = $request->file('foto_url')->store('uploads/foto', 'public');
            $user->foto_url = $path;
        }

        $user->save();

        // Simpan data student
        Student::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'gender' => $request->gender,
            'whatsapp_number' => $request->whatsapp_number,
            'telegram_number' => $request->telegram_number,
            'class_code' => $request->class_code,
            'department_code' => $request->department_code,
            'internship_place_code' => $request->internship_place_code,
            'internship_batch_id' => $request->internship_batch_id,
            'mentor_id' => $request->mentor_id,
            'user_id' => $user->id, // kaitkan ke user
        ]);

        return redirect()->route('student')->with('success', 'Data berhasil disimpan.');
    }


    public function archives(Request $request)
    {
        $title = "Siswa Prakerin";
        if (session('ses_role') == 'admin' || session('ses_role') == 'super-admin') {

            $users = User::all();

            // Get search and batch_search values from the request
            $search = $request->input('search');
            $batchSearch = $request->input('batch_search');  // Filter for internship batch

            $students = Student::with([
                'user',              // Eager loading user
                'internshipBatch',   // Eager loading internshipBatch
                'internshipPlace',   // Eager loading internshipPlace
                'class',             // Eager loading class
                'department'         // Eager loading department
            ])
                ->where(function ($query) use ($search, $batchSearch) {
                    // If search is provided, filter by student name (first_name or last_name)
                    if ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                    }

                    // If batch_search is provided, filter by internship batch name
                    if ($batchSearch) {
                        $query->whereHas('internshipBatch', function ($query) use ($batchSearch) {
                            $query->where('batch_name', 'like', '%' . $batchSearch . '%');
                        });
                    }
                })
                // Pastikan hanya mengambil students yang memiliki user dengan is_active = 1
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 0);  // Menyaring user dengan is_active = 1
                })
                ->orderBy('id', 'desc')  // Sorting by ID (can be changed to another field if needed)
                ->paginate(10);  // Paginate results with 10 per page

            // Add search and batch_search parameters to pagination links
            $students = $students->appends(['search' => $search, 'batch_search' => $batchSearch]);

            // Additional data to populate form options
            $studentAll = Student::with(['user'])  // Memuat relasi user
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 0);  // Menambahkan kondisi is_active pada tabel users
                })
                ->orderBy('id', 'desc')  // Mengurutkan berdasarkan id
                ->get();

            $batches = InternshipBatch::orderBy('id', 'desc')->get();

            $classes = ClassModel::all();
            $departments = Department::all();
            $dudies = InternshipPlace::all();
            $mentors = Mentor::all();

            // Return view with all necessary data
            return view('student.archive', compact(
                'title',
                'users',
                'classes',
                'departments',
                'dudies',
                'batches',
                'mentors',
                'students',
                'search',
                'batchSearch',
                'studentAll'
            ));
        }
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Siswa Prakerin";

        // Ambil data student berdasarkan ID dengan eager loading untuk relasi yang diperlukan
        $student = Student::with([
            'user',
            'internshipBatch',  // Eager loading internshipBatch
            'internshipPlace',  // Eager loading internshipPlace
            'class',            // Eager loading class
            'department',       // Eager loading department
            'mentor'            // Eager loading mentor
        ])->findOrFail($id);

        // Ambil data pencarian dari request jika ada
        $search = $request->input('search');
        $batchSearch = $request->input('batch_search'); // Filter untuk gelombang internship

        // Ambil data untuk kelas, jurusan, dudi, mentor
        $classes = ClassModel::all();
        $departments = Department::all();
        $dudies = InternshipPlace::all();
        $batches = InternshipBatch::all();
        $mentors = Mentor::all();

        // Ambil data semua mahasiswa dengan pencarian berdasarkan nama atau batch
        $students = Student::with([
            'user',
            'internshipBatch',  // Eager loading internshipBatch
            'internshipPlace',  // Eager loading internshipPlace
            'class',            // Eager loading class
            'department'        // Eager loading department
        ])
            ->where(function ($query) use ($search, $batchSearch) {
                // Jika ada pencarian berdasarkan nama
                if ($search) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
                }

                // Jika ada pencarian berdasarkan nama batch internship
                if ($batchSearch) {
                    $query->whereHas('internshipBatch', function ($query) use ($batchSearch) {
                        $query->where('batch_name', 'like', '%' . $batchSearch . '%');
                    });
                }
            })
            ->orderBy('id', 'desc') // Sorting berdasarkan ID (bisa diubah sesuai kebutuhan)
            ->paginate(10); // Paginate hasil pencarian 10 per halaman

        // Tambahkan parameter pencarian ke pagination link
        $students = $students->appends(['search' => $search, 'batch_search' => $batchSearch]);

        // Ambil semua data student untuk variabel studentAll
        $studentAll = Student::orderBy('id', 'desc')->get();

        // Kembalikan view dengan semua data yang diperlukan
        return view('student.index', compact(
            'title',       // Data untuk title yang akan diedit
            'student',       // Data untuk mahasiswa yang akan diedit
            'students',      // Data mahasiswa dengan pencarian
            'studentAll',    // Semua data mahasiswa
            'classes',       // Data kelas
            'departments',   // Data jurusan
            'dudies',        // Data dudi
            'batches',       // Data batch internship
            'mentors',       // Data mentor
            'search',        // Pencarian nama mahasiswa
            'batchSearch'    // Pencarian nama batch internship
        ));
    }


    // public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'nis' => 'required|string|max:10|unique:students,nis,' . $id,  // Pastikan NIS hanya unik untuk data yang lain
    //         'gender' => 'required|string|max:255',
    //         'whatsapp_number' => 'required|string|max:16',
    //         'telegram_number' => 'required|string|max:255',
    //         'class_code' => 'required|string|max:255',
    //         'department_code' => 'required|string|max:255',
    //         'internship_place_code' => 'required|string|max:255',
    //         'internship_batch_id' => 'required|string|max:255',
    //         'username' => 'required|string|max:255',
    //         'password' => 'nullable|string|max:255',  // Password hanya perlu diubah jika diisi
    //     ], [
    //         'name.required' => 'Nama tidak boleh kosong',
    //         'nis.required' => 'NIS tidak boleh kosong',
    //         'gender.required' => 'Jenis kelamin tidak boleh kosong',
    //         'whatsapp_number.required' => 'Nomor WhatsApp tidak boleh kosong',
    //         'telegram_number.required' => 'Nomor Telegram tidak boleh kosong',
    //         'class_code.required' => 'Kelas tidak boleh kosong',
    //         'department_code.required' => 'Jurusan tidak boleh kosong',
    //         'internship_place_code.required' => 'Dudi tidak boleh kosong',
    //         'internship_batch_id.required' => 'Gelombang tidak boleh kosong',
    //         'username.required' => 'Username tidak boleh kosong',
    //         'password.required' => 'Password tidak boleh kosong',
    //     ]);

    //     // Ambil data mahasiswa yang ingin diupdate
    //     $student = Student::findOrFail($id);
    //     $user = User::findOrFail($student->user_id);  // Ambil user yang terkait dengan student

    //     // Perbarui data user
    //     $user->name = $request->name;
    //     $user->username = $request->username;

    //     // Jika password diubah, hash ulang password
    //     if ($request->filled('password')) {
    //         $user->password = bcrypt($request->password);
    //     }

    //     $user->save();  // Simpan perubahan user

    //     // Perbarui data student
    //     $student->name = $request->name;
    //     $student->nis = $request->nis;
    //     $student->gender = $request->gender;
    //     $student->whatsapp_number = $request->whatsapp_number;
    //     $student->telegram_number = $request->telegram_number;
    //     $student->class_code = $request->class_code;
    //     $student->department_code = $request->department_code;
    //     $student->internship_place_code = $request->internship_place_code;
    //     $student->internship_batch_id = $request->internship_batch_id;
    //     $student->mentor_id = $request->mentor_id;  // Pembimbing
    //     $student->save();  // Simpan perubahan student

    //     // Redirect dengan pesan sukses
    //     return redirect()->route('student')->with('success', 'Data berhasil diperbarui.');
    // }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:10|unique:students,nis,' . $id,
            'gender' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:16',
            'telegram_number' => 'required|string|max:255',
            'class_code' => 'required|string|max:255',
            'department_code' => 'required|string|max:255',
            'internship_place_code' => 'required|string|max:255',
            'internship_batch_id' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'foto_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // validasi foto
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'nis.required' => 'NIS tidak boleh kosong',
            'gender.required' => 'Jenis kelamin tidak boleh kosong',
            'whatsapp_number.required' => 'Nomor WhatsApp tidak boleh kosong',
            'telegram_number.required' => 'Nomor Telegram tidak boleh kosong',
            'class_code.required' => 'Kelas tidak boleh kosong',
            'department_code.required' => 'Jurusan tidak boleh kosong',
            'internship_place_code.required' => 'Dudi tidak boleh kosong',
            'internship_batch_id.required' => 'Gelombang tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'foto_url.image' => 'File harus berupa gambar (jpg, jpeg, png)',
            'foto_url.max' => 'Ukuran foto maksimal 2MB',
        ]);

        // Ambil data mahasiswa
        $student = Student::findOrFail($id);
        $user = User::findOrFail($student->user_id);

        // Update data user
        $user->name = $request->name;
        $user->username = $request->username;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Upload foto baru jika ada
        if ($request->hasFile('foto_url')) {
            // Hapus foto lama jika ada
            if ($user->foto_url && \Storage::disk('public')->exists($user->foto_url)) {
                \Storage::disk('public')->delete($user->foto_url);
            }

            // Simpan foto baru
            $path = $request->file('foto_url')->store('uploads/foto', 'public');
            $user->foto_url = $path;
        }

        $user->save();

        // Update data student
        $student->name = $request->name;
        $student->nis = $request->nis;
        $student->gender = $request->gender;
        $student->whatsapp_number = $request->whatsapp_number;
        $student->telegram_number = $request->telegram_number;
        $student->class_code = $request->class_code;
        $student->department_code = $request->department_code;
        $student->internship_place_code = $request->internship_place_code;
        $student->internship_batch_id = $request->internship_batch_id;
        $student->mentor_id = $request->mentor_id;
        $student->save();

        return redirect()->route('student')->with('success', 'Data berhasil diperbarui.');
    }


    public function activate($id)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::findOrFail($id);

        // Ubah status is_active menjadi 1 (aktifkan kembali)
        $user->is_active = 1;
        $user->save();

        // Return response JSON untuk memberikan feedback ke pengguna
        return response()->json(['success' => true]);
    }

    public function archive($id)
    {
        // Temukan user berdasarkan ID (ID berasal dari tabel users, bukan tabel students)
        $user = User::findOrFail($id);

        // Ubah status is_active menjadi 0 (mengarsipkan)
        $user->is_active = 0;
        $user->save();

        // Redirect kembali ke halaman daftar student dengan pesan sukses
        return redirect()->route('student')->with('success', 'Data berhasil diarsipkan.');
    }

    public function report()
    {
        $title = "Siswa Prakerin";

        // Ambil data siswa 
        $students = Student::with([
            'user',
            'internshipBatch',  // Eager loading internshipBatch
            'internshipPlace',  // Eager loading internshipPlace
            'class',            // Eager loading class
            'department'        // Eager loading department
        ])
            ->orderBy('id', 'desc')  // Atur urutan berdasarkan nama siswa atau field lainnya
            ->get();

        return view('student.report', compact('title', 'students'));
    }

    public function printStudents(Request $request)
    {
        // Capture the filters from the request
        $batchName = $request->input('batch_search');
        $search = $request->input('search');

        // Fetch data for students with the optional filters
        $studentsQuery = Student::with([
            'user',
            'internshipBatch',
            'internshipPlace',
            'class',
            'department',
            'mentor'
        ]);

        // Apply the search filter if provided
        if ($search) {
            $studentsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        // Apply the batch_name filter if provided
        if ($batchName) {
            $studentsQuery->whereHas('internshipBatch', function ($query) use ($batchName) {
                $query->where('id',  $batchName);
            });
        }

        // Get the filtered list of students
        $students = $studentsQuery->get();

        // Prepare the data to pass to the view
        $data = [
            'title' => 'Laporan Akun Siswa',
            'date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
            'students' => $students
        ];

        // dd($data);

        // Generate the PDF using SnappyPdf
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('student.report', $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait')
            ->setOption('margin-top', 20)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15)
            ->setOption('margin-bottom', 20);

        // Download the generated PDF
        return $pdf->download('print_Student.pdf');
    }
}
