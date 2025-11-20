<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Dashboard";

        // Ambil nilai pencarian dari request
        $search = $request->input('search');

        // Use paginate instead of get to ensure you get a Paginator instance
        $departments = Department::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')  // Sort by ID or any other field you want
            ->paginate(10);  // Paginate results, for example 10 items per page

        // Append the search query to the pagination links
        $departments = $departments->appends(['search' => $search]);

        // return response()->json($users);
        return view('department.index', compact('title', 'departments', 'search'));
    }

    public function store(Request $request)
    {
        // Validasi input, termasuk validasi unik untuk letter_code
        $request->validate([
            'code' => 'required|string|max:10|unique:classes',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode jurusan tidak boleh kosong',
            'code.unique' => 'Kode jurusan sudah ada. Silakan buat kode kelas lain.',
            'name.required' => 'Nama jurusan tidak boleh kosong',
        ]);

        // Persiapkan data untuk membuat data Letter baru
        $data = $request->all();

        // Membuat data Letter baru dengan data yang sudah dipersiapkan
        Department::create($data);

        // dd($data);

        // Redirect dengan pesan sukses
        return redirect()->route('department')->with('success', 'Data berhasil disimpan.');
    }

    public function edit(Request $request, $code)
    {
        $title = "Edit Jurusan";

        // Ambil data InternshipPlace yang akan diedit berdasarkan ID
        $department = Department::findOrFail($code);

        // Ambil nilai pencarian (jika ingin menampilkan daftar atau filter tambahan)
        $search = $request->input('search', '');

        // Jika Anda ingin menampilkan daftar data dengan filter, bisa menggunakan paginate
        // Tetapi pastikan variabel untuk daftar data tidak mengganggu data yang akan diedit.
        $departments = Department::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        // Return view, pastikan data yang ingin diedit dikirim dengan nama yang tepat
        return view('department.index', compact('title', 'department', 'departments', 'search'));
    }

    public function update(Request $request, $code)
    {
        // Validasi input
        $request->validate([
            'code' => 'required|string|max:10|unique:departments,code,' . $code . ',code',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode kelas tidak boleh kosong',
            'name.required' => 'Nama kelas tidak boleh kosong',
        ]);

        // Persiapkan data untuk update, pastikan menyertakan field 'address'
        $data = $request->only(['name']);

        // Ambil data InternshipPlace berdasarkan ID dan update

        $department = Department::where('code', $code)->firstOrFail();
        $department->update($data);

        // Redirect dengan pesan sukses
        return redirect()->route('department')->with('success', 'Data berhasil diperbarui.');
    }
}
