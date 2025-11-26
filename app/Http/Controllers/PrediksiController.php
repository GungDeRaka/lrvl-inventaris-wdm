<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrediksiController extends Controller
{
    public function index()
    {
        // Tampilkan view halaman prediksi (akan kita buat nanti)
        return view('admin.prediksi.index');
    }

    public function getPrediction()
    {
        try {
            // Panggil API Python di port 5001
            // Perhatikan: Jika menggunakan SAIL, 'localhost' merujuk ke container itu sendiri.
            // Untuk mengakses host (komputer Anda) dari dalam container Sail, gunakan 'host.docker.internal'
            $response = Http::post('http://host.docker.internal:5001/predict');

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghubungi API Python: ' . $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage()
            ], 500);
        }
    }
}