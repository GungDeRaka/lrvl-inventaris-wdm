<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Kita gunakan Carbon untuk manajemen waktu

class CekJamKerja
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Cek jika pengguna adalah 'penjaga_gudang'
        if ($user && $user->peran === 'penjaga_gudang') {
            // Atur zona waktu ke 'Asia/Makassar' (WITA)
            $jamSekarang = Carbon::now('Asia/Makassar');
            $jamMulai = Carbon::createFromTimeString('08:00:00', 'Asia/Makassar');
            $jamSelesai = Carbon::createFromTimeString('16:00:00', 'Asia/Makassar');

            // Cek apakah waktu saat ini di luar jam kerja
            if (!$jamSekarang->between($jamMulai, $jamSelesai)) {
                Auth::logout(); // Logout pengguna
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Kirim pesan error kembali ke halaman login
                return redirect()->route('login')
                    ->with('error', 'Akses ditolak. Penjaga gudang hanya bisa login antara jam 08:00 - 16:00 WITA.');
            }
        }

        // Jika peran adalah 'kepala_gudang' atau jika 'penjaga_gudang' dalam jam kerja, lanjutkan
        return $next($request);
    }
}