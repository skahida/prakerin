<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MentorController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Guru Pembimbing";

        // Ambil nilai pencarian dari request
        $search = $request->input('search');

        // Use paginate instead of get to ensure you get a Paginator instance
        $mentors = Mentor::with('user')
            ->where('name', 'like', '%' . $search . '%')
            // Pastikan hanya mengambil students yang memiliki user dengan is_active = 1
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1);  // Menyaring user dengan is_active = 1
            })
            ->orderBy('created_at', 'desc')  // Sort by ID or any other field you want
            ->paginate(10);  // Paginate results, for example 10 items per page

        // Append the search query to the pagination links
        $mentors = $mentors->appends(['search' => $search]);

        // return response()->json($users);
        return view('mentor.index', compact('title', 'mentors', 'search'));
    }

    public function store(Request $request)
    {
        // Validasi input, termasuk validasi unik untuk letter_code
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:16',
            'telegram_number' => 'nullable|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'gender.required' => 'Jenis kelamin tidak boleh kosong',
            'whatsapp_number.required' => 'Nomor WhatsApp tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ]);

        // Persiapkan data untuk membuat data Letter baru
        // $data = $request->all();

        // // Membuat data Letter baru dengan data yang sudah dipersiapkan
        // Mentor::create($data);

        // Simpan data
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => "mentor",
            'is_active' => true,
        ]);

        // Simpan data 
        Mentor::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'whatsapp_number' => $request->whatsapp_number,
            'telegram_number' => $request->telegram_number,
            'user_id' => $user->id, // Mengaitkan dengan pengguna yang baru dibuat
        ]);

        // dd($data);

        // Redirect dengan pesan sukses
        return redirect()->route('mentor')->with('success', 'Data berhasil disimpan.');
    }

    public function updateChatId(Request $request)
    {
        $mentorId = session('ses_mentor_id');  // Ambil mentor_id dari session

        // Validasi Chat ID
        $request->validate([
            'chat_id' => 'required|string|min:5',  // Pastikan chat_id tidak kosong dan memiliki panjang minimal
        ]);

        // Cari mentor berdasarkan mentor_id
        $mentor = Mentor::find($mentorId);

        // Cek apakah mentor ada
        if ($mentor) {
            // Update kolom telegram_number dengan chat_id yang baru
            $mentor->telegram_number = $request->input('chat_id');
            $mentor->save();

            // Ambil nama mentor
            $name = $mentor->name;
            $chatId = $mentor->telegram_number;

            // Kirim pesan ke Telegram menggunakan bot
            $message = "Chat ID *$name* sudah aktif dan sudah bisa mendapatkan notifikasi presensi dari siswa.";

            $this->sendMessage($chatId, $message);

            // Kembalikan response sukses
            return response()->json(['message' => 'Chat ID berhasil diperbarui dan pesan sudah dikirim ke Telegram.']);
        } else {
            // Jika mentor tidak ditemukan, kirim error
            return response()->json(['message' => 'Mentor tidak ditemukan.'], 404);
        }
    }

    private function sendMessage($chatId, $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Pastikan token bot ada di .env
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // Menggunakan POST request untuk mengirim pesan
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',  // Format pesan menggunakan Markdown
        ]);

        // Cek apakah pengiriman pesan berhasil
        if ($response->failed()) {
            Log::error('Failed to send message: ' . $response->body());
        } else {
            Log::info('Message sent successfully to chat_id: ' . $chatId);
        }
    }

    public function report()
    {
        $title = "Guru Pembimbing Prakerin";

        // Ambil data siswa 
        $mentors = Mentor::with([
            'user'       // Eager loading department
        ])
            ->orderBy('id', 'desc')  // Atur urutan berdasarkan nama siswa atau field lainnya
            ->get();

        return view('mentor.report', compact('title', 'mentors'));
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Data Guru Pembimbing";

        // Ambil data InternshipPlace yang akan diedit berdasarkan ID
        $mentor = Mentor::findOrFail($id);

        // Ambil nilai pencarian (jika ingin menampilkan daftar atau filter tambahan)
        $search = $request->input('search', '');

        // Jika Anda ingin menampilkan daftar data dengan filter, bisa menggunakan paginate
        // Tetapi pastikan variabel untuk daftar data tidak mengganggu data yang akan diedit.
        $mentors = Mentor::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        // Return view, pastikan data yang ingin diedit dikirim dengan nama yang tepat
        return view('mentor.index', compact('title', 'mentor', 'mentors', 'search'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:16',
            'telegram_number' => 'nullable|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'gender.required' => 'Jenis kelamin tidak boleh kosong',
            'whatsapp_number.required' => 'Nomor WhatsApp tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ]);

        // Persiapkan data untuk update, pastikan menyertakan field 'address'
        $data = $request->only(['name', 'gender', 'whatsapp_number', 'telegram_number', 'username']);

        // Ambil data InternshipPlace berdasarkan ID dan update

        $mentor = Mentor::where('id', $id)->firstOrFail();

        // Update data admin (nama saja misalnya)
        $mentor->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'whatsapp_number' => $request->whatsapp_number,
            'telegram_number' => $request->telegram_number,
            'gender' => $request->gender,
        ]);

        // Update data user berdasarkan user_id dari admin
        $user = User::where('id', $mentor->user_id)->firstOrFail();
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('mentor')->with('success', 'Data berhasil diperbarui.');
    }

    public function archives(Request $request)
    {
        $title = "Arsip Guru Pembimbing";
        if (session('ses_role') == 'admin' || session('ses_role') == 'super-admin') {

            $users = User::all();

            $search = $request->input('search');

            $mentors = Mentor::with('user')
                ->where(function ($query) use ($search) {
                    // If search is provided, filter by student name (first_name or last_name)
                    if ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
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
            $mentors = $mentors->appends(['search' => $search]);

            $mentorAll = Mentor::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 0);
                })
                ->get();

            // return response()->json($users);
            return view('mentor.archive', compact('title', 'mentorAll', 'mentors', 'users', 'search'));
        }
    }
}
