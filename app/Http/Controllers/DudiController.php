<?php

namespace App\Http\Controllers;

use App\Models\InternshipPlace;
use App\Models\Mentor;
use Illuminate\Http\Request;

class DudiController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Dudi";

        // Ambil nilai pencarian dari request
        $search = $request->input('search');

        // Use paginate instead of get to ensure you get a Paginator instance
        $dudies = InternshipPlace::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')  // Sort by ID or any other field you want
            ->paginate(10);  // Paginate results, for example 10 items per page

        // Append the search query to the pagination links
        $dudies = $dudies->appends(['search' => $search]);

        $mentors = Mentor::all();

        $check_location_link = "https://www.google.com/maps?q=";

        // return response()->json($users);
        return view('dudi.index', compact('title', 'dudies', 'mentors', 'search', 'check_location_link'));
    }

    public function store(Request $request)
    {
        // Validasi input, termasuk validasi untuk mentor_id yang ada di tabel mentors
        $request->validate([
            'code' => 'required|string|max:10|unique:classes',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'field' => 'required|string|max:255',
            'contact_number' => 'required|string|max:16',
        ], [
            'code.required' => 'Kode dudi tidak boleh kosong',
            'name.required' => 'Nama dudi tidak boleh kosong',
            'address.required' => 'Alamat tidak boleh kosong',
            'field.required' => 'Bidang tidak boleh kosong',
            'contact_number.required' => 'Nomor WhatsApp tidak boleh kosong',
        ]);

        // Persiapkan data untuk membuat data InternshipPlace baru
        $data = $request->only(['code', 'mentor_id', 'name', 'field', 'contact_number']);

        // Membuat data InternshipPlace baru dengan data yang sudah dipersiapkan
        InternshipPlace::create($data);

        // Redirect dengan pesan sukses
        return redirect()->route('dudi')->with('success', 'Data berhasil disimpan.');
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Dudi";

        // Ambil data InternshipPlace yang akan diedit berdasarkan ID
        $dudi = InternshipPlace::findOrFail($id);

        // Ambil nilai pencarian (jika ingin menampilkan daftar atau filter tambahan)
        $search = $request->input('search', '');

        // Jika Anda ingin menampilkan daftar data dengan filter, bisa menggunakan paginate
        // Tetapi pastikan variabel untuk daftar data tidak mengganggu data yang akan diedit.
        $dudies = InternshipPlace::where('name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        $mentors = Mentor::all();

        $check_location_link = "https://www.google.com/maps?q=";

        // Return view, pastikan data yang ingin diedit dikirim dengan nama yang tepat
        return view('dudi.index', compact('title', 'dudi', 'dudies', 'mentors', 'search', 'check_location_link'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'code' => 'required|string|max:10|unique:internship_places,code,' . $id . ',code',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'field' => 'required|string|max:255',
            'contact_number' => 'required|string|max:16',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'code.required' => 'Kode dudi tidak boleh kosong',
            'name.required' => 'Nama dudi tidak boleh kosong',
            'address.required' => 'Alamat tidak boleh kosong',
            'field.required' => 'Bidang tidak boleh kosong',
            'contact_number.required' => 'Nomor WhatsApp tidak boleh kosong',
            'latitude.required' => 'Koordinat latitude wajib diisi.',
            'latitude.numeric' => 'Koordinat latitude harus berupa angka.',
            'longitude.required' => 'Koordinat longitude wajib diisi.',
            'longitude.numeric' => 'Koordinat longitude harus berupa angka.',
        ]);

        // Persiapkan data untuk update, pastikan menyertakan field 'address'
        $data = $request->only(['code', 'mentor_id', 'name', 'address', 'field', 'contact_number', 'latitude', 'longitude']);

        // Ambil data InternshipPlace berdasarkan ID dan update
        $dudi = InternshipPlace::findOrFail($id);
        $dudi->update($data);

        // Redirect dengan pesan sukses
        return redirect()->route('dudi')->with('success', 'Data berhasil diperbarui.');
    }
}
