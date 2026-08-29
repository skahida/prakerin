<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validasi yang lebih kompleks
        $request->validate([
            'username' => [
                'required',
                'string',
                'exists:users,username',
            ],
            'password' => [
                'required',
                'string',
                'min:5',
            ],
        ], [
            'username.required' => 'Username harus diisi.',
            'username.exists' => 'Username tidak terdaftar.',
            'password.required' => 'Kata sandi harus diisi.',
            'password.min' => 'Kata sandi minimal 5 karakter.',
        ]);

        // Cek apakah email ada dan akun aktif
        $user = User::where('username', $request->username)->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'username' => 'Akun Anda belum aktif. Harap tunggu konfirmasi atau hubungi administrator untuk aktivasi akun.',
            ]);
        }

        // Batasi jumlah percobaan login
        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        // Cek login
        if (Auth::attempt($request->only('username', 'password'))) {

            $this->clearLoginAttempts($request);

            // regenerate session ID agar record session benar-benar dibuat
            $request->session()->regenerate();

            // regenerate token CSRF
            $request->session()->regenerateToken();

            $user = Auth::user();

            // Simpan informasi pengguna
            $this->storeUserSession($user);

            // UPDATE user_id pada session yang baru saja dibuat
            \DB::table('sessions')
                ->where('id', session()->getId())
                ->update([
                    'user_id'       => $user->id,
                    'last_activity' => now()->timestamp
                ]);

            // Password default checking
            if ($user->role == 'student') {
                if (Hash::check('prakerin', $user->password)) {
                    session(['requires_password_update' => true]);
                }
            }

            return redirect()->route('dashboard')->with('success', 'Login berhasil.');
        }


        // Jika gagal
        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }


    //  Fungsi untuk menyimpan informasi pengguna ke sesi
    protected function storeUserSession($user)
    {
        if ($user->role === 'student') {
            $student = $user->student;

            if ($student) {
                $userData['name'] =  $user->student->name;
                $userData['nis'] =  $user->student->nis;
                $userData['ses_student_id'] =  $user->student->id;
                $userData['ses_role'] =  $user->role;
                // $userData['user_id'] = $usrPublic->id;
                // $userData['user_type'] = 'public';
                // $userData['family_card_number'] = $usrPublic->family_card_number;
                // $userData['address'] = $usrPublic->address;
                // $userData['rt'] = $usrPublic->rt;
                // $userData['rw'] = $usrPublic->rw;
                // $userData['residence_id'] = $usrPublic->residence_id;

                // Periksa apakah ada relasi residence
                // if ($usrPublic->residence) {
                //     $userData['residence_name'] = $usrPublic->residence->residence_name;
                // } else {
                //     $userData['residence_name'] = null; // Atau nilai default lain jika tidak ada
                // }
            } else {
                // Redirect ke login
                return redirect()->route('loginpage');
            }
        } elseif ($user->role === 'mentor') {
            $mentor = $user->mentor;
            if ($mentor) {
                $userData['name'] =  $user->mentor->name;
                $userData['ses_role'] =  $user->role;
                $userData['ses_mentor_id'] =  $user->mentor->id;
            } else {
                // Redirect ke login
                return redirect()->route('loginpage');
            }
        } elseif ($user->role === 'admin' || $user->role === 'super-admin') {
            $admin = $user->admin;
            if ($admin) {
                $userData['name'] =  $user->admin->name;
                $userData['ses_role'] =  $user->role;
            } else {
                // Redirect ke landing page
                return redirect()->route('loginpage');
            }
        } else {
            // Redirect ke login
            return redirect()->route('loginpage');
        }

        // Simpan di sesi
        session($userData);
    }

    public function logout()
    {
        // Menghapus cache aplikasi
        Cache::flush();  // Menghapus seluruh cache aplikasi

        // Menghapus cookies tertentu (ganti dengan nama cookie yang sesuai)
        Cookie::queue(Cookie::forget('_token'));  // Ganti 'your_cookie_name' dengan nama cookie yang ingin dihapus

        // Hapus data session
        session()->flush();  // Menghapus seluruh data session

        // Logout pengguna
        Auth::logout();  // Menghapus sesi pengguna

        // Menghapus CSRF token dari session secara eksplisit
        session()->forget('_token');  // Menghapus CSRF token

        // Redirect ke halaman login
        return redirect()->route('loginpage');
    }

    // Fungsi untuk membatasi jumlah percobaan login
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $maxAttempts = 5; // Maksimal 5 kali percobaan
        $decayMinutes = 15; // Blokir selama 15 menit setelah 5 percobaan gagal

        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $maxAttempts,
            $decayMinutes
        );
    }

    // Increment jumlah percobaan login yang gagal
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit($this->throttleKey($request));
    }

    // Reset percobaan login
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    // Fungsi untuk menangani respons jika akun dikunci
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
        ]);
    }

    // Mendapatkan throttle key berdasarkan email dan IP
    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('username')) . '|' . $request->ip();
    }

    // Mendapatkan instance dari rate limiter
    protected function limiter()
    {
        return app(\Illuminate\Cache\RateLimiter::class);
    }
}
