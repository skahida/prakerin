<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    // Endpoint untuk mengecek token
    public function checkToken(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            return response()->json(['message' => 'Token tidak ditemukan'], 401);
        }

        $token = $matches[1];

        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }

        // Token valid
        return response()->json([
            'message' => 'Token valid',
            'user' => $user->only(['id', 'name', 'email', 'role'])
        ]);
    }

    // public function login(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'username' => 'required|alpha_num|min:3|max:20',
    //             'password' => 'required',
    //         ]);

    //         $user = User::where('username', $request->username)->first();

    //         if (!$user || !Hash::check($request->password, $user->password)) {
    //             return response()->json(['message' => 'Username atau kata sandi salah.'], 401);
    //         }

    //         if ($user->role !== 'student') {
    //             return response()->json([
    //                 'message' => 'Akun belum bisa akses aplikasi. Login lewat website.',
    //                 'login_url' => 'https://prakerin.skahida.my.id/'
    //             ], 403);
    //         }

    //         $token = Str::random(60);
    //         $user->remember_token = $token;
    //         $user->save();

    //         return response()->json([
    //             'message' => 'Login berhasil',
    //             'token' => $token,
    //             'user' => $user->only(['id', 'name', 'email']),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Terjadi kesalahan server, silakan coba lagi nanti.'
    //         ], 500);
    //     }
    // }

    public function login(Request $request)
    {
        try {
            $request->validate([
                // Mengizinkan huruf, angka, titik, strip, dan underscore
                'username' => 'required|regex:/^[a-zA-Z0-9._-]+$/|min:3|max:20',
                'password' => 'required',
            ]);

            $user = User::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Username atau kata sandi salah.'], 401);
            }

            // Semua role boleh login → cek role dihapus
            $token = Str::random(60);
            $user->remember_token = $token;
            $user->save();

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $user->only(['id', 'name', 'email', 'role']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan server, silakan coba lagi nanti.'
            ], 500);
        }
    }




    public function logout(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            return response()->json(['message' => 'Token tidak ditemukan'], 401);
        }

        $token = $matches[1];

        $user = \App\Models\User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }

        // Hapus token (logout)
        $user->remember_token = null;
        $user->save();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
