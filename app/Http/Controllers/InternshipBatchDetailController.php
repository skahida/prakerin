<?php

namespace App\Http\Controllers;

use App\Models\InternshipBatchDetail;
use App\Models\Mentor;
use Illuminate\Http\Request;

class InternshipBatchDetailController extends Controller
{
    public function bulkStore(Request $request)
    {
        $details = $request->input('details', []);

        if (empty($details)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data untuk disimpan'
            ]);
        }

        foreach ($details as $d) {
            InternshipBatchDetail::create([
                'internship_batch_id' => $d['batch_id'], // sesuai nama kolom
                'mentor_id'           => $d['mentor_id'],
                'place_code'          => $d['place_code'],  // atau ganti key di JS jadi 'place_code'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail gelombang berhasil disimpan!'
        ]);
    }

    public function getDetailsJson($batchId)
    {
        $details = InternshipBatchDetail::with(['mentor', 'place'])
            ->where('internship_batch_id', $batchId)
            ->get();

        return response()->json($details);
    }

    public function destroy($id)
    {
        $detail = InternshipBatchDetail::find($id);
        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $detail->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
