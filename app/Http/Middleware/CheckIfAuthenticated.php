<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login
        if (Auth::check()) {
            // Cek apakah sesi sudah kedaluwarsa atau CSRF token invalid
            if (Session::has('expired') || !$request->has('_token')) {
                // Redirect ke halaman login jika sesi expired
                return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
            }

            // Jika pengguna sudah login dan mencoba mengakses halaman login atau homepage, redirect ke dashboard
            if ($request->is('login') || $request->is('/')) {
                return redirect()->route('dashboard'); // Redirect ke dashboard jika sudah login
            }

            // Cek apakah pengguna memiliki role yang valid dan mencoba mengakses login page
            if (in_array(Auth::user()->role, ['student', 'mentor', 'admin'])) {
                if ($request->is('login')) {
                    return redirect()->route('dashboard'); // Redirect ke dashboard
                }
            }
        }

        // Jika pengguna belum login atau tidak perlu di-redirect, lanjutkan request
        return $next($request);
    }
}
