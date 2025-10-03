<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Import Carbon

class LaporanController extends Controller
{
    public function cetakTransaksi(Request $request)
    {
        // Mulai query
        $query = Transaksi::with(['siswa', 'barangs', 'user']);

        // Ambil tanggal dari request
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Terapkan filter jika tanggal diisi
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('created_at', [
                Carbon::parse($tanggalMulai)->startOfDay(),
                Carbon::parse($tanggalAkhir)->endOfDay()
            ]);
        }

        $transaksis = $query->get();

        // Data tambahan untuk laporan
        $data = [
            'tanggal' => date('d F Y'),
            'transaksis' => $transaksis,
            'periodeMulai' => $tanggalMulai ? Carbon::parse($tanggalMulai)->format('d M Y') : null,
            'periodeAkhir' => $tanggalAkhir ? Carbon::parse($tanggalAkhir)->format('d M Y') : null,
        ];

        $pdf = Pdf::loadView('laporan.transaksi_pdf', $data);
        return $pdf->download('laporan-transaksi-inventaris.pdf');
    }
}