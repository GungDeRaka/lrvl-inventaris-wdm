<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Peminjaman #{{ $transaksi->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }

        .container {
            width: 100%;
        }

        h2 {
            text-align: center;
            margin: 0 0 10px 0;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
        }

        .items-table {
            border-collapse: collapse;
            margin-top: 15px;
        }

        .items-table th,
        .items-table td {
            padding: 5px 0;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
        }

        .items-table .qty {
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>BUKTI PEMINJAMAN<br>SMK WIDIATMIKA</h2>
        <hr>
        <table class="info-table">
            <tr>
                <td>ID Transaksi</td>
                <td>: #{{ $transaksi->id }}</td>
            </tr>
            <tr>
                <td>Admin</td>
                <td>: {{ $transaksi->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Peminjam</td>
                <td>: {{ $transaksi->siswa->nama }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $transaksi->siswa->kelas }}</td>
            </tr>
            <tr>
                <td>Waktu Pinjam</td>
                <td>: {{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td>Waktu Kembali</td>
                <td>: {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ ucfirst($transaksi->status) }}</td>
            </tr>
        </table>
        <hr>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th class="qty">Jml</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi->barangs as $barang)
                    <tr>
                        <td>{{ $barang->nama_barang }}</td>
                        <td class="qty">{{ $barang->pivot->kuantitas }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <p class="footer">
            Dicetak pada: {{ $tanggal_cetak }}<br>
            Harap kembalikan barang tepat waktu.
        </p>
    </div>
</body>

</html>
