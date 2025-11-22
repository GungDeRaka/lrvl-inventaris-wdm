<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanPengadaanController extends Controller
{
    public function cetak(Request $request)
    {
        // 1. Ambil Filter dari URL
        $sumberDanaId = $request->query('sumber_dana');
        $tglMulai = $request->query('tgl_mulai');
        $tglAkhir = $request->query('tgl_akhir');

        // 2. Query Data
        $query = PengadaanBarang::with(['barang', 'sumberDana', 'user']);

        if ($sumberDanaId) {
            $query->where('sumber_dana_id', $sumberDanaId);
        }

        if ($tglMulai) {
            $query->whereDate('tanggal_pengadaan', '>=', $tglMulai);
        }

        if ($tglAkhir) {
            $query->whereDate('tanggal_pengadaan', '<=', $tglAkhir);
        }

        $dataPengadaan = $query->orderBy('tanggal_pengadaan', 'desc')->get();
        $totalBiaya = $dataPengadaan->sum('total_harga');

        // 3. Siapkan Data untuk View
        $data = [
            'pengadaans' => $dataPengadaan,
            'totalBiaya' => $totalBiaya,
            'tglMulai' => $tglMulai ? Carbon::parse($tglMulai)->format('d M Y') : null,
            'tglAkhir' => $tglAkhir ? Carbon::parse($tglAkhir)->format('d M Y') : null,
            'tanggalCetak' => now()->format('d F Y H:i'),
            'userCetak' => Auth::user()->name,
        ];

        // 4. Generate PDF
        $pdf = Pdf::loadView('laporan.pengadaan_pdf', $data);
        $pdf->setPaper('a4', 'landscape'); // Landscape agar tabel muat

        return $pdf->stream('Laporan-Keuangan-Pengadaan.pdf');
    }
}