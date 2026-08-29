<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $query = Jurnal::with([
            'student.class',
            'student.internshipPlace',
            'student.internshipBatch'
        ])
            // Hanya jurnal siswa yang gelombangnya masih active
            ->whereHas('student.internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            });

        // Filter berdasarkan role
        if ($role === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                $query->where('student_id', $student->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($role === 'mentor') {
            $mentor = \App\Models\Mentor::where('user_id', $user->id)->first();

            if ($mentor) {
                $query->whereHas('student', function ($q) use ($mentor) {
                    $q->where('mentor_id', $mentor->id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // admin & super-admin bisa lihat semua (yang active)

        // Filter tambahan dari form
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jurnals = $query->latest('date')->paginate(15)->withQueryString();

        // Data untuk filter dropdown siswa (hanya yang gelombang active)
        $students = collect();

        if ($role === 'mentor') {
            $mentor = \App\Models\Mentor::where('user_id', $user->id)->first();
            if ($mentor) {
                $students = Student::where('mentor_id', $mentor->id)
                    ->whereHas('internshipBatch', function ($q) {
                        $q->where('status_batch', 'active');
                    })
                    ->orderBy('name')
                    ->get();
            }
        } elseif (in_array($role, ['admin', 'super-admin'])) {
            $students = Student::whereHas('internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
                ->orderBy('name')
                ->get();
        }

        return view('jurnal.index', compact('jurnals', 'students', 'role'));
    }

    public function create()
    {
        $user = Auth::user();
        $role = $user->role;

        $students = collect();
        $studentId = null;

        if ($role === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            $studentId = $student?->id;
        } elseif ($role === 'mentor') {
            $mentor = \App\Models\Mentor::where('user_id', $user->id)->first();
            if ($mentor) {
                $students = Student::where('mentor_id', $mentor->id)
                    ->whereHas('internshipBatch', function ($q) {
                        $q->where('status_batch', 'active');
                    })
                    ->orderBy('name')
                    ->get();
            }
        } elseif (in_array($role, ['admin', 'super-admin'])) {
            $students = Student::whereHas('internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
                ->orderBy('name')
                ->get();
        }

        return view('jurnal.create', compact('students', 'studentId', 'role'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $rules = [
            'date'                   => 'required|date',
            'activities'             => 'required|array|min:1',
            'activities.*'           => 'string',
            'description'            => 'nullable|string',
            'photo'                  => 'nullable|image|max:2048',
            'dudi_supervisor_name'   => 'required|string|max:100',
        ];

        if ($role === 'student') {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            $studentId = $student->id;
        } else {
            $rules['student_id'] = 'required|exists:students,id';
            $request->validate($rules);
            $studentId = $request->student_id;
        }

        $request->validate($rules);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('jurnal-photos', 'public');
        }

        Jurnal::create([
            'student_id'             => $studentId,
            'date'                   => $request->date,
            'activities'             => $request->activities,
            'description'            => $request->description,
            'photo'                  => $photoPath,
            'status'                 => 'submitted',
            'dudi_supervisor_name'   => $request->dudi_supervisor_name,
        ]);

        return redirect()->route('jurnal.index')->with('success', 'Jurnal harian berhasil disimpan.');
    }

    public function show(Jurnal $jurnal)
    {
        $this->authorizeAccess($jurnal);

        $jurnal->load(['student.class', 'student.internshipPlace', 'student.internshipBatch']);

        return view('jurnal.show', compact('jurnal'));
    }

    public function edit(Jurnal $jurnal)
    {
        $this->authorizeAccess($jurnal);

        if ($jurnal->status === 'signed') {
            return redirect()->route('jurnal.show', $jurnal)
                ->with('error', 'Jurnal yang sudah ditandatangani tidak dapat diedit.');
        }

        $user = Auth::user();
        $role = $user->role;

        $students = collect();
        if (in_array($role, ['admin', 'super-admin'])) {
            $students = Student::whereHas('internshipBatch', function ($q) {
                $q->where('status_batch', 'active');
            })
                ->orderBy('name')
                ->get();
        } elseif ($role === 'mentor') {
            $mentor = \App\Models\Mentor::where('user_id', $user->id)->first();
            if ($mentor) {
                $students = Student::where('mentor_id', $mentor->id)
                    ->whereHas('internshipBatch', function ($q) {
                        $q->where('status_batch', 'active');
                    })
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('jurnal.edit', compact('jurnal', 'students', 'role'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $this->authorizeAccess($jurnal);

        if ($jurnal->status === 'signed') {
            return redirect()->route('jurnal.show', $jurnal)->with('error', 'Jurnal yang sudah ditandatangani tidak dapat diubah.');
        }

        $request->validate([
            'date'                   => 'required|date',
            'activities'             => 'required|array|min:1',
            'activities.*'           => 'string',
            'description'            => 'nullable|string',
            'photo'                  => 'nullable|image|max:2048',
            'dudi_supervisor_name'   => 'required|string|max:100',
        ]);

        $data = [
            'date'                 => $request->date,
            'activities'           => $request->activities,
            'description'          => $request->description,
            'dudi_supervisor_name' => $request->dudi_supervisor_name,
        ];

        if ($request->hasFile('photo')) {
            if ($jurnal->photo) {
                Storage::disk('public')->delete($jurnal->photo);
            }
            $data['photo'] = $request->file('photo')->store('jurnal-photos', 'public');
        }

        $jurnal->update($data);

        return redirect()->route('jurnal.show', $jurnal)->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy(Jurnal $jurnal)
    {
        $this->authorizeAccess($jurnal);

        if ($jurnal->photo) {
            Storage::disk('public')->delete($jurnal->photo);
        }
        if ($jurnal->dudi_supervisor_signature) {
            Storage::disk('public')->delete($jurnal->dudi_supervisor_signature);
        }

        $jurnal->delete();

        return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
    }

    /**
     * Simpan tanda tangan digital Pembimbing DUDI
     */
    public function sign(Request $request, Jurnal $jurnal)
    {
        $this->authorizeAccess($jurnal);

        $request->validate([
            'signature' => 'required|string',
        ]);

        // Signature datang sebagai base64
        $signatureData = $request->signature;

        if (preg_match('/^data:image\/(\w+);base64,/', $signatureData, $matches)) {
            $extension = $matches[1];
            $signatureData = substr($signatureData, strpos($signatureData, ',') + 1);
            $signatureData = base64_decode($signatureData);

            $filename = 'signatures/jurnal_' . $jurnal->id . '_' . time() . '.' . $extension;
            Storage::disk('public')->put($filename, $signatureData);

            $jurnal->update([
                'dudi_supervisor_signature' => $filename,
                'signed_at'                 => now(),
                'status'                    => 'signed',
            ]);

            return response()->json(['success' => true, 'message' => 'Tanda tangan berhasil disimpan.']);
        }

        return response()->json(['success' => false, 'message' => 'Format tanda tangan tidak valid.'], 422);
    }

    /**
     * Cek akses berdasarkan role
     */
    private function authorizeAccess(Jurnal $jurnal)
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student || $jurnal->student_id !== $student->id) {
                abort(403);
            }
        } elseif ($role === 'mentor') {
            $mentor = \App\Models\Mentor::where('user_id', $user->id)->first();

            if (!$mentor || $jurnal->student->mentor_id !== $mentor->id) {
                abort(403);
            }
        }
        // admin & super-admin boleh akses semua
    }
}
