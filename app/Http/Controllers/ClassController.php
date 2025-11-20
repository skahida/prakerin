<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Kelas";
        // Get search query from the request
        $search = $request->input('search', '');

        // Perform the search query only if search is provided
        $classes = ClassModel::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Append the search query to the pagination links
        $classes = $classes->appends(['search' => $search]);
        // return response()->json($users);
        return view('class.index', compact('title', 'classes', 'search'));
    }

    public function store(Request $request)
    {
        // Validasi input, termasuk validasi unik untuk letter_code
        $request->validate([
            'code' => 'required|string|max:10|unique:classes',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode kelas tidak boleh kosong',
            'code.unique' => 'Kode kelas sudah ada. Silakan buat kode kelas lain.',
            'name.required' => 'Nama kelas tidak boleh kosong',
        ]);

        // Persiapkan data untuk membuat data Letter baru
        $data = $request->all();

        // Membuat data Letter baru dengan data yang sudah dipersiapkan
        ClassModel::create($data);

        // dd($data);

        // Redirect dengan pesan sukses
        return redirect()->route('class')->with('success', 'Data berhasil disimpan.');
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Kelas";

        // Ambil data InternshipPlace yang akan diedit berdasarkan ID
        $class = ClassModel::findOrFail($id);

        // Ambil nilai pencarian (jika ingin menampilkan daftar atau filter tambahan)
        $search = $request->input('search', '');

        // Jika Anda ingin menampilkan daftar data dengan filter, bisa menggunakan paginate
        // Tetapi pastikan variabel untuk daftar data tidak mengganggu data yang akan diedit.
        $classes = ClassModel::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        // Return view, pastikan data yang ingin diedit dikirim dengan nama yang tepat
        return view('class.index', compact('title', 'class', 'classes', 'search'));
    }

    public function update(Request $request, $code)
    {
        // Validasi input
        $request->validate([
            'code' => 'required|string|max:10|unique:classes,code,' . $code . ',code',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode kelas tidak boleh kosong',
            'name.required' => 'Nama kelas tidak boleh kosong',
        ]);

        // Persiapkan data untuk update, pastikan menyertakan field 'address'
        $data = $request->only(['name']);

        // Ambil data InternshipPlace berdasarkan ID dan update

        $class = ClassModel::where('code', $code)->firstOrFail();
        $class->update($data);

        // Redirect dengan pesan sukses
        return redirect()->route('class')->with('success', 'Data berhasil diperbarui.');
    }
}
