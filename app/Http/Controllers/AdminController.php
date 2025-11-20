<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Admin";
        $users = User::all();
        $admins = Admin::with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1);
            })
            ->OrderBy('id', 'desc')
            ->get();
        // return response()->json($users);
        return view('admin.index', compact('title', 'admins', 'users'));
    }

    public function store(Request $request)
    {
        // Validasi input, termasuk validasi unik untuk letter_code
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'role' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'role.required' => 'Role tidak boleh kosong',
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
            'role' => $request->role,
            'is_active' => true,
        ]);

        // Simpan data 
        Admin::create([
            'name' => $request->name,
            'user_id' => $user->id, // Mengaitkan dengan pengguna yang baru dibuat
        ]);

        // dd($data);

        // Redirect dengan pesan sukses
        return redirect()->route('admin')->with('success', 'Data berhasil disimpan.');
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Data Guru Pembimbing";

        // Ambil data InternshipPlace yang akan diedit berdasarkan ID
        $admin = Admin::findOrFail($id);

        // Ambil nilai pencarian (jika ingin menampilkan daftar atau filter tambahan)
        $search = $request->input('search', '');

        // Jika Anda ingin menampilkan daftar data dengan filter, bisa menggunakan paginate
        // Tetapi pastikan variabel untuk daftar data tidak mengganggu data yang akan diedit.
        $admins = Admin::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        // Return view, pastikan data yang ingin diedit dikirim dengan nama yang tepat
        return view('admin.index', compact('title', 'admin', 'admins', 'search'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
        ]);

        // Ambil data admin
        $admin = Admin::where('id', $id)->firstOrFail();

        // Update data admin (nama saja misalnya)
        $admin->update([
            'name' => $request->name,
        ]);
        // Update data user berdasarkan user_id dari admin
        $user = User::where('id', $admin->user_id)->firstOrFail();
        $user->update([
            'username' => $request->username,
        ]);

        return redirect()->route('admin')->with('success', 'Data berhasil diperbarui.');
    }

    public function archives(Request $request)
    {
        $title = "Arsip Admin";
        if (session('ses_role') == 'admin' || session('ses_role') == 'super-admin') {

            $title = "Admin";
            $users = User::all();

            $search = $request->input('search');

            $admins = Admin::with('user')
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
            $admins = $admins->appends(['search' => $search]);

            $adminAll = Admin::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 0);
                })
                ->get();

            // return response()->json($users);
            return view('admin.archive', compact('title', 'adminAll', 'admins', 'users', 'search'));
        }
    }
}
