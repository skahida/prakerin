<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\InternshipPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    // Menampilkan form & daftar monitoring
    // public function index()
    // {
    //     $title = "Monitoring";

    //     // Ambil mentor_id dari relasi user -> mentor
    //     $mentorId = null;
    //     if (Auth::user()->role === 'mentor') {
    //         $mentorId = Auth::user()->mentor->id ?? null; // pakai null kalau tidak ada
    //     }

    //     // Ambil monitoring milik mentor yang login
    //     $monitorings = Monitoring::with('internshipPlace')
    //         ->when($mentorId, function ($q) use ($mentorId) {
    //             $q->where('mentor_id', $mentorId);
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     if (Auth::user()->role === 'mentor') {
    //         $places = InternshipPlace::whereHas('batchDetails', function ($q) use ($mentorId) {
    //             $q->where('mentor_id', $mentorId);
    //         })
    //             ->orderBy('name')
    //             ->get();
    //     } else {
    //         // role admin atau yang lain → ambil semua tempat
    //         $places = InternshipPlace::orderBy('name')->get();
    //     }

    //     return view('mentor.monitoring', compact("title", "monitorings", "places"));
    // }

    // Menampilkan form & daftar monitoring
    public function index()
    {
        $title = "Monitoring";

        // Ambil mentor_id dari relasi user -> mentor
        $mentorId = null;
        if (Auth::user()->role === 'mentor') {
            $mentorId = Auth::user()->mentor->id ?? null; // pakai null kalau tidak ada
        }

        // Ambil monitoring milik mentor yang login + include nama mentor
        $monitorings = Monitoring::with(['internshipPlace', 'mentor'])
            ->when($mentorId, function ($q) use ($mentorId) {
                $q->where('mentor_id', $mentorId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if (Auth::user()->role === 'mentor') {
            $places = InternshipPlace::whereHas('batchDetails', function ($q) use ($mentorId) {
                $q->where('mentor_id', $mentorId);
            })
                ->orderBy('name')
                ->get();
        } else {
            // role admin atau yang lain → ambil semua tempat
            $places = InternshipPlace::orderBy('name')->get();
        }

        return view('mentor.monitoring', compact("title", "monitorings", "places"));
    }



    // Simpan bukti monitoring
    public function store(Request $request)
    {
        $request->validate([
            'place_code' => 'required|exists:internship_places,code',
            'monitor_photo' => 'required|image|mimes:jpeg,png,jpg,heic',
            'status' => 'required|in:Penerjunan,Monitoring 1,Monitoring 2,Monitoring 3,Penarikan',
        ]);

        // simpan foto
        $path = $request->file('monitor_photo')->store('monitoring', 'public');

        Monitoring::create([
            'place_code' => $request->place_code,
            'mentor_id'  => Auth::user()->mentor->id ?? null,
            'photo'      => $path,
            'status'     => $request->status, // atau bisa $request->status
            'check_latitude' => $request->check_latitude,
            'check_longitude' => $request->check_longitude,
            'check_location_link' => $request->check_location_link,
        ]);

        return redirect()->route('monitoring')
            ->with('success_monitor', 'Bukti monitoring berhasil diupload.');
    }

    public function destroy($id)
    {
        $monitor = Monitoring::findOrFail($id);

        // hapus file foto di storage/public
        if ($monitor->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($monitor->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($monitor->photo);
        }

        $monitor->delete();

        return back()->with('success_monitor', 'Data monitoring berhasil dihapus.');
    }
}
