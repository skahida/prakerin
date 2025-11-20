<?php

namespace App\Http\Controllers;

use App\Models\InternshipBatch;
use App\Models\InternshipPlace;
use App\Models\Mentor;
use Illuminate\Http\Request;

class InternshipBatchController extends Controller
{
    public function index(Request $request)
    {
        $title = "Gelombang";

        $search = $request->input('search');

        $batches = InternshipBatch::where('batch_name', 'like', '%' . $search . '%')
            ->orderByRaw("status_batch = 'active' desc") // Menempatkan 'active' di atas
            ->orderBy('status_batch', 'desc') // Sorting berdasarkan 'status_batch' lainnya jika perlu
            ->paginate(10);

        // Append the search query to the pagination links
        $batches = $batches->appends(['search' => $search]);

        $mentors = Mentor::all();
        $places = InternshipPlace::all();

        // return response()->json($users);
        return view('batch.index', compact('title', 'batches', 'mentors', 'places', 'search'));
    }

    public function show($id)
    {
        $batch = InternshipBatch::find($id); // Menampilkan satu batch berdasarkan ID

        if (!$batch) {
            return response()->json(['message' => 'Batch not found'], 404);
        }

        return response()->json($batch);
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validated = $request->validate([
            'batch_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'academic_year' => 'required|string|max:255',
        ], [
            'batch_name.required' => 'Gelombang tidak boleh kosong',
            'start_date.required' => 'Mulai prakerin tidak boleh kosong',
            'end_date.required' => 'Selesai prakerin tidak boleh kosong',
            'academic_year.required' => 'Tahun pelajaran tidak boleh kosong',
        ]);

        // Menyimpan data baru
        $batch = InternshipBatch::create($validated);

        // Redirect dengan pesan sukses
        return redirect()->route('batch')->with('success', 'Data berhasil disimpan.');
    }

    public function edit(Request $request, $id)
    {
        $title = "Edit Data Gelombang";

        // Ambil data InternshipPlace yang akan diedit berdasarkan ID
        $batch = InternshipBatch::findOrFail($id);

        // Ambil nilai pencarian (jika ingin menampilkan daftar atau filter tambahan)
        $search = $request->input('search', '');

        // Jika Anda ingin menampilkan daftar data dengan filter, bisa menggunakan paginate
        // Tetapi pastikan variabel untuk daftar data tidak mengganggu data yang akan diedit.
        $batches = InternshipBatch::where('batch_name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        // Return view, pastikan data yang ingin diedit dikirim dengan nama yang tepat
        return view('batch.index', compact('title', 'batch', 'batches', 'search'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'batch_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'academic_year' => 'nullable|string',
        ], [
            'batch_name.required' => 'Nama gelombang tidak boleh kosong',
            'batch_name.string' => 'Nama gelombang harus berupa teks',
            'start_date.required' => 'Tanggal mulai tidak boleh kosong',
            'start_date.date' => 'Tanggal mulai tidak valid',
            'end_date.required' => 'Tanggal selesai tidak boleh kosong',
            'end_date.date' => 'Tanggal selesai tidak valid',
            'academic_year.string' => 'Tahun akademik harus berupa angka',
        ]);

        // Persiapkan data untuk update, pastikan menyertakan field 'address'
        $data = $request->only(['batch_name', 'start_date', 'end_date', 'academic_year']);

        // Ambil data InternshipPlace berdasarkan ID dan update

        $batch = InternshipBatch::where('id', $id)->firstOrFail();
        $batch->update($data);

        // Redirect dengan pesan sukses
        return redirect()->route('batch')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Menemukan batch berdasarkan ID dan menghapusnya
        $batch = InternshipBatch::find($id);

        if (!$batch) {
            return response()->json(['message' => 'Batch not found'], 404);
        }

        $batch->delete();

        return response()->json(['message' => 'Batch deleted successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi status
        $request->validate([
            'status_batch' => 'required|in:active,non-active',
        ]);

        // Cari batch berdasarkan ID
        $batch = InternshipBatch::findOrFail($id);

        // Update status batch
        $batch->status_batch = $request->status_batch;
        $batch->save();

        // Jika AJAX request, kembalikan respons JSON
        if ($request->ajax()) {
            return response()->json(['message' => 'Status gelombang berhasil diperbarui']);
        }

        // Redirect jika bukan AJAX
        return redirect()->back()->with('success', 'Status gelombang berhasil diperbarui.');
    }

    public function getInternshipDates()
    {
        // Ambil data internship batch, misalnya batch pertama
        $internshipBatch = InternshipBatch::first(); // Atau kamu bisa menambahkan filter jika perlu

        // Mengembalikan response dengan data start_date dan end_date
        return response()->json([
            'start_date' => $internshipBatch->start_date,
            'end_date' => $internshipBatch->end_date,
        ]);
    }
}
