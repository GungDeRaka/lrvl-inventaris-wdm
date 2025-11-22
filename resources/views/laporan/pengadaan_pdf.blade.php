<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Pengadaan Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header h2 { margin: 5px 0; font-size: 14px; font-weight: normal; }
        .meta { margin-bottom: 15px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px 8px; }
        th { background-color: #f0f0f0; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN PENGADAAN BARANG</h1>
        <h2>SMK WIDIATMIKA</h2>
        @if($tglMulai || $tglAkhir)
            <p>Periode: {{ $tglMulai ?? 'Awal' }} s/d {{ $tglAkhir ?? 'Sekarang' }}</p>
        @else
            <p>Periode: Semua Riwayat</p>
        @endif
    </div>

    <div class="meta">
        Dicetak pada: {{ $tanggalCetak }}<br>
        Oleh: {{ $userCetak }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 12%">Tanggal</th>
                <th>Nama Barang</th>
                <th>Sumber Dana</th>
                <th style="width: 8%">Jml</th>
                <th style="width: 15%">Harga Satuan</th>
                <th style="width: 15%">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengadaans as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pengadaan)->format('d/m/Y') }}</td>
                    <td>
                        {{ $item->barang->nama_barang ?? 'Barang Dihapus' }}
                        <br><small style="color: #666">({{ $item->barang->kode_barang ?? '-' }})</small>
                    </td>
                    <td class="text-center">{{ $item->sumberDana->nama_sumber ?? '-' }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data pengadaan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PENGELUARAN:</td>
                <td class="text-right">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>