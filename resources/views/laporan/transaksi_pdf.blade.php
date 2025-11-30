<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Transaksi</title>
    <style>
        /* --- Konfigurasi Halaman --- */
        @page {
            size: A4 landscape; /* Landscape agar tabel muat banyak kolom */
            margin: 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #1a202c;
        }

        /* --- Kop Surat --- */
        .header-container {
            border-bottom: 3px double #2d3748;
            padding-bottom: 15px;
            margin-bottom: 20px;
            position: relative;
        }
        /* Logo sekolah (Ganti dengan tag img jika ada gambar) */
        .logo-placeholder {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
            height: 70px;
            background-color: #e2e8f0;
            
            text-align: center;
            line-height: 70px;
            font-size: 9px;
            color: #718096;
        }
        .school-info {
            text-align: center;
            margin-left: 80px; /* Memberi ruang untuk logo */
            margin-right: 80px;
        }
        .school-name { font-size: 16pt; font-weight: bold; margin: 0; }
        .school-address { font-size: 10pt; margin-top: 5px; }

        /* --- Judul Laporan --- */
        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .report-period {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 20px;
            color: #4a5568;
        }

        /* --- Tabel Data --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #4a5568;
            padding: 8px;
            vertical-align: top;
        }
        th {
            background-color: #edf2f7;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* --- Area Tanda Tangan (Kiri: Admin, Kanan: Kepala Sekolah) --- */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid; /* Jangan potong tanda tangan ke halaman baru */
        }
        /* Menggunakan tabel borderless untuk layout tanda tangan yang rapi di PDF */
        .signature-table {
            width: 100%;
            border: none;
            margin-top: 30px;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 40%; /* Kiri & Kanan masing-masing 40% */
        }
        .signature-space {
            height: 70px; /* Ruang untuk tanda tangan */
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- 1. Kop Surat --}}
    <div class="header-container">
        <img src="{{ public_path('logo.jpg') }}" class="logo-placeholder">
        
        
        <div class="school-info">
            <div class="school-name">SMK NEGERI CONTOH SISTEM</div>
            <div class="school-address">
                Jl. Pendidikan No. 123, Kota Contoh, Provinsi Sample<br>
                Telp: (021) 1234567 | Email: info@sekolah.sch.id
            </div>
        </div>
    </div>

    {{-- 2. Judul & Periode --}}
    <div class="report-title">LAPORAN RIWAYAT TRANSAKSI PEMINJAMAN</div>
    <div class="report-period">
        @if(isset($tanggal_mulai) && isset($tanggal_akhir))
            Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} 
            s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d M Y') }}
        @else
            Periode: Semua Riwayat
        @endif
    </div>

    {{-- 3. Tabel Data Transaksi --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Peminjam</th>
                <th style="width: 25%">Barang (Jml)</th>
                <th style="width: 15%">Tgl. Pinjam</th>
                <th style="width: 15%">Tgl. Kembali</th>
                <th style="width: 10%">Status</th>
                <th style="width: 15%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $index => $transaksi)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $transaksi->siswa->nama }}</strong><br>
                    <small>{{ $transaksi->siswa->kelas }}</small>
                </td>
                <td>
                    @foreach($transaksi->barangs as $barang)
                        - {{ $barang->nama_barang }} ({{ $barang->pivot->kuantitas }})<br>
                    @endforeach
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                    @if($transaksi->waktu_pengembalian_aktual)
                        {{ \Carbon\Carbon::parse($transaksi->waktu_pengembalian_aktual)->format('d/m/Y H:i') }}
                    @else
                        {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d/m/Y H:i') }}
                    @endif
                </td>
                <td class="text-center" style="text-transform:capitalize;">{{ str_replace('-', ' ', $transaksi->status) }}</td>
                <td>{{ $transaksi->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. Area Tanda Tangan --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                {{-- KIRI: Admin / Petugas --}}
                <td>
                    <p>Mengetahui,<br>Petugas Inventaris</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ Auth::user()->name ?? '.........................' }}</p>
                    <p>NIP/ID: {{ Auth::id() }}</p>
                </td>
                
                {{-- TENGAH: Kosong (Spacer) --}}
                <td style="width: 20%;"></td>

                {{-- KANAN: Kepala Sekolah --}}
                <td>
                    <p>Kota Contoh, {{ now()->format('d F Y') }}<br>Kepala Sekolah</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">( .................................... )</p>
                    <p>NIP: ..............................</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>