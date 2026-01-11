<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman #{{ $transaksi->id }}</title>
    <style>
        /* --- Konfigurasi Ukuran Kertas F4 (Folio) --- */
        @page {
            size: 210mm 330mm; /* Ukuran F4 standar */
            margin: 2cm;       /* Margin halaman */
        }

        /* --- Styling Dasar --- */
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1a202c; /* Gray-900 */
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .clearfix::after { content: ""; clear: both; display: table; }

        /* --- Header / Kop Surat --- */
        .header-container {
            border-bottom: 3px double #2d3748;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo-placeholder {
            float: left;
            width: 80px;
            height: 80px;
            background-color: #e2e8f0; /* Warna abu-abu pengganti logo */
            /* border-radius: 50%; */
            text-align: center;
            line-height: 80px;
            font-size: 10px;
            color: #718096;
            margin-right: 20px;
        }
        .school-info {
            overflow: hidden; /* Agar teks tidak menabrak float logo */
        }
        .school-name { font-size: 16pt; font-weight: bold; margin: 0; color: #2d3748; }
        .school-address { margin: 0; font-size: 10pt; }

        /* --- Judul Dokumen --- */
        .document-title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 30px;
            text-decoration: underline;
            color: #2d3748;
        }

        /* --- Info Section (2 Kolom menggunakan Tabel borderless) --- */
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 2px 5px; }
        .info-label { font-weight: bold; width: 120px; }

        /* --- Tabel Barang --- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #cbd5e0;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #edf2f7; /* Gray-100 */
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }

        /* --- Catatan Kaki --- */
        .notes-section {
            margin-top: 30px;
            font-size: 10pt;
            border: 1px solid #e2e8f0;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 5px;
        }
        .notes-section ol { margin: 5px 0 0 20px; padding: 0; }

        /* --- Area Tanda Tangan (Sesuai Request) --- */
        .signature-section {
            /* margin-top: 60px; */
            width: 100%;
            page-break-inside: avoid; /* Mencegah terpotong antar halaman */
        }
        .signature-box {
            width: 40%; /* Lebar masing-masing area tanda tangan */
            text-align: center;
        }
        /* Garis untuk tanda tangan */
        .signature-line {
            border-bottom: 1px solid #1a202c;
            margin-top: 80px; /* Ruang untuk tanda tangan basah */
            margin-bottom: 10px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

    {{-- 1. Header / Kop Surat --}}
    <div class="header-container clearfix">
        {{-- Ganti src ini dengan path logo sekolah Anda yang sebenarnya --}}
        {{-- <img src="{{ public_path('images/logo-sekolah.png') }}" class="logo-placeholder" alt="Logo"> --}}
        
        {{-- Placeholder Logo (Hapus div ini jika sudah pakai gambar) --}}
        <img src="/public/logo.jpg" alt="" class="logo-placeholder">
        
        <div class="school-info">
            <h1 class="school-name">SMK WIDIATMIKA</h1>
            <p class="school-address">
                Jl. Raya Kampus Unud No.8, Jimbaran, Kec. Kuta Sel., Kabupaten Badung, Bali.<br>
                Telp: (0361) 4465612 | Email:  smk@widiatmika.sch.id | Web: widiatmika.sch.id
            </p>
        </div>
    </div>

    {{-- 2. Judul Dokumen --}}
    <div class="document-title">BUKTI PEMINJAMAN BARANG</div>

    {{-- 3. Informasi Transaksi & Peminjam (Layout 2 Kolom) --}}
    <table class="info-table">
        <tr>
            {{-- Kolom Kiri: Detail Transaksi --}}
            <td style="width: 50%; padding-right: 20px;">
                <h3 style="margin-top:0; border-bottom: 2px solid #edf2f7; padding-bottom: 5px;">Detail Transaksi</h3>
                <table style="width: 100%;">
                    <tr><td class="info-label">No. Dokumen</td><td>: #TRX-{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
                    <tr><td class="info-label">Tanggal Pinjam</td><td>: {{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d M Y, H:i') }}</td></tr>
                    <tr><td class="info-label">Rencana Kembali</td><td>: {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d M Y, H:i') }}</td></tr>
                    <tr><td class="info-label">Status Saat Ini</td><td>: <span class="uppercase">{{ $transaksi->status }}</span></td></tr>
                    <tr><td class="info-label">Lokasi Pakai</td><td>: {{ $transaksi->ruang_pemakaian }}</td></tr>
                </table>
            </td>
            {{-- Kolom Kanan: Data Peminjam --}}
            <td style="width: 50%; padding-left: 20px; border-left: 2px solid #edf2f7;">
                <h3 style="margin-top:0; border-bottom: 2px solid #edf2f7; padding-bottom: 5px;">Data Peminjam (Siswa)</h3>
                <table style="width: 100%;">
                    <tr><td class="info-label">Nama Lengkap</td><td>: <strong>{{ $transaksi->siswa->nama }}</strong></td></tr>
                    <tr><td class="info-label">NIS / NISN</td><td>: {{ $transaksi->siswa->nis }} / {{ $transaksi->siswa->nisn }}</td></tr>
                    <tr><td class="info-label">Kelas/Jurusan</td><td>: {{ $transaksi->siswa->kelas }} - {{ $transaksi->siswa->jurusan }}</td></tr>
                    <tr><td class="info-label">No. Telepon</td><td>: {{ $transaksi->siswa->no_hp ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 4. Tabel Daftar Barang --}}
    <h3 style="border-bottom: 2px solid #edf2f7; padding-bottom: 5px; margin-bottom: 10px;">Rincian Barang Yang Dipinjam</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%">No</th>
                <th style="width: 40%">Nama Barang / Alat</th>
                <th style="width: 20%">Kategori</th>
                <th style="width: 20%">Asal Ruangan</th>
                <th class="text-center" style="width: 15%">Kuantitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi->barangs as $index => $barang)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span class="font-bold">{{ $barang->nama_barang }}</span><br>
                    <small style="color: #718096;">Kode: {{ $barang->kode_barang }}</small>
                </td>
                <td>{{ $barang->kategori->nama_kategori }}</td>
                <td>{{ $barang->ruangan->nama_ruangan }}</td>
                <td class="text-center font-bold">{{ $barang->pivot->kuantitas }} Unit</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f7fafc;">
                <td colspan="4" class="text-right font-bold uppercase">Total Unit Dipinjam:</td>
                <td class="text-center font-bold" style="font-size: 12pt;">{{ $transaksi->barangs->sum('pivot.kuantitas') }} Unit</td>
            </tr>
        </tfoot>
    </table>

    {{-- 5. Catatan & Ketentuan --}}
    <div class="notes-section">
        <strong>PERNYATAAN & KETENTUAN PEMINJAMAN:</strong>
        <ol>
            <li>Peminjam menyatakan telah menerima barang-barang tersebut di atas dalam kondisi baik dan lengkap.</li>
            <li>Peminjam bertanggung jawab penuh atas keamanan dan keutuhan barang selama masa peminjaman.</li>
            <li>Apabila terjadi kerusakan atau kehilangan, peminjam wajib mengganti atau memperbaiki sesuai ketentuan sekolah.</li>
            <li>Barang wajib dikembalikan tepat waktu sesuai tanggal "Rencana Kembali" di atas.</li>
        </ol>
    </div>

    {{-- 6. Area Tanda Tangan (Sesuai Referensi Gambar) --}}
    <div class="signature-section clearfix">
        {{-- Kiri: Petugas --}}
        <div class="signature-box" style="float: left;">
            <p style="margin-bottom: 5px;">Mengetahui / Menyetujui,</p>
            <p class="font-bold">Petugas Inventaris / Admin</p>
            
            {{-- Garis Tanda Tangan --}}
            <div class="signature-line"></div>
            
            {{-- Nama Terang Admin (Jika ada datanya) --}}
            <p class="font-bold uppercase">
                {{ $transaksi->user->name ?? '( ................................. )' }}
            </p>
            <p style="font-size: 9pt;">NIP/ID: {{ $transaksi->user->id ?? '-' }}</p>
        </div>

        {{-- Kanan: Peminjam --}}
        <div class="signature-box" style="float: right;">
            {{-- Tanggal Cetak Dinamis --}}
            <p style="margin-bottom: 5px;">Badung, Bali, {{ \Carbon\Carbon::parse($tanggal_cetak)->format('d F Y') }}</p>
            <p class="font-bold">Peminjam (Siswa)</p>

            {{-- Garis Tanda Tangan --}}
            <div class="signature-line"></div>

            {{-- Nama Terang Siswa --}}
            <p class="font-bold uppercase">{{ $transaksi->siswa->nama }}</p>
            <p style="font-size: 9pt;">NIS: {{ $transaksi->siswa->nis }}</p>
        </div>
    </div>

</body>
</html>