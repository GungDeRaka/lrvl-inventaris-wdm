<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Inventaris</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 30px; }
        .date { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Transaksi Peminjaman Barang</h1>
        <h2>SMK Widiatmika</h2>
        <hr>
        <p class="date">Tanggal Cetak: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Peminjam</th>
                <th>Kelas</th>
                <th>Waktu Pinjam</th>
                <th>Waktu Kembali</th>
                <th>Status</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $index => $transaksi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $transaksi->barang->nama_barang }}</td>
                    <td>{{ $transaksi->siswa->nama }}</td>
                    <td>{{ $transaksi->siswa->kelas }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d-m-Y H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d-m-Y H:i') }}</td>
                    <td>{{ ucfirst($transaksi->status) }}</td>
                    <td>{{ $transaksi->user->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>