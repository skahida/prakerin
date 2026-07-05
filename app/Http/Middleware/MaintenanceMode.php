<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Waktu sekarang WIB
        $now = Carbon::now('Asia/Jakarta');

        // Target selesai maintenance: jam 06:00 WIB hari ini
        $maintenanceEnd = Carbon::now('Asia/Jakarta')->startOfDay()->setTime(6, 0, 0);

        // Maintenance aktif jika sekarang MASIH sebelum jam 06:00
        $isMaintenance = $now->lt($maintenanceEnd);

        if ($isMaintenance) {

            // Jika API
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'maintenance',
                    'message' => 'Aplikasi Prakerin Tracer sedang maintenance & migrasi server. Silakan coba lagi nanti.',
                ], 503);
            }

            // Jika Web
            return response()->view('maintenance');
        }

        return $next($request);
    }
}
