<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
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

    public function cetakStruk($id)
{
    $transaksi = Transaksi::with(['siswa', 'barangs', 'user'])->findOrFail($id);

    // KEAMANAN: Cek siapa yang login
    $isAdmin = Auth::guard('web')->check();
    
    // Cek apakah yang login adalah siswa DAN pemilik transaksi tersebut
    $isSiswaPemilik = Auth::guard('siswa')->check() && Auth::guard('siswa')->id() == $transaksi->siswa_id;

    // Jika bukan Admin DAN bukan Siswa Pemilik, tolak akses
    if (!$isAdmin && !$isSiswaPemilik) {
        abort(403, 'ANDA TIDAK BERHAK MENGAKSES STRUK INI.');
    }

    // ... (sisa kode PDF sama seperti sebelumnya)
    $data = [
        'transaksi' => $transaksi,
        'tanggal_cetak' => now()->format('d M Y, H:i')
    ];
    
    $pdf = Pdf::loadView('laporan.struk_transaksi_pdf', $data);
    $customPaper = [0, 0, 220, 350];
    $pdf->setPaper($customPaper, 'portrait');

    return $pdf->stream('struk-peminjaman-'.$transaksi->id.'.pdf');
}
}
