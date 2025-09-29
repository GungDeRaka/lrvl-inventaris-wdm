<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Selamat Datang, {{ Auth::user()->name }}!</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Unit Barang</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalUnitBarang }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalDipinjam }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Barang Rusak</p>
                <p class="text-3xl font-bold text-red-500">{{ $totalRusak }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Jatuh Tempo</p>
                <p class="text-3xl font-bold text-yellow-500">{{ $jatuhTempo }}</p>
            </div>
        </div>
    </div>


    {{-- Notifikasi Sukses --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Terjadi Kesalahan!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Harap perbaiki error berikut:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <hr class="bt-4 border-black mb-2 mt-6">

    <div>
        <div class="flex justify-start items-center mb-2 mt-4">
            <h2 class="text-xl font-semibold  mr-4 text-gray-700">HISTORY PEMINJAMAN</h2>


            {{-- tombol cetak laporan --}}
            <a href="{{ route('laporan.transaksi') }}" target="_blank"
                class="border border-primary hover:bg-primary hover:text-white text-primary font-bold py-1 px-4 rounded text-sm">
                Cetak Laporan
            </a>
        </div>
        <div class="flex flex-row justify-between items-center mb-4">
            <div class="w-1/3 ">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama barang atau peminjam..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <button wire:click="openTransactionModal"
                class="bg-primary hover:bg-purple-800 text-white font-bold py-2 px-6 rounded-lg shadow-md">
                Tambah Peminjaman Baru
            </button>

        </div>

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-primary">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama
                            Barang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama
                            Peminjam</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Waktu
                            Pinjam</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Waktu
                            Kembali</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transaksis as $transaksi)
                        @php
                            // Logika untuk mengecek apakah sudah jatuh tempo
                            $isJatuhTempo =
                                $transaksi->status == 'dipinjam' &&
                                \Carbon\Carbon::parse($transaksi->waktu_kembali)->isPast();
                        @endphp

                        {{-- Beri warna latar jika jatuh tempo --}}
                        <tr class="hover:bg-gray-50 border-b border-gray-200 {{ $isJatuhTempo ? 'bg-red-100' : '' }}">

                            {{-- Perhatikan, semua class 'bg-white' dan 'bg-transparent' telah dihapus dari <td> --}}
                            <td class="px-5 py-4 text-sm">{{ $transaksi->barang->nama_barang }}</td>
                            <td class="px-5 py-4 text-sm">
                                <button wire:click="showSiswaDetail({{ $transaksi->siswa->id }})"
                                    class="font-semibold text-indigo-600 hover:underline">
                                    {{ $transaksi->siswa->nama }}
                                </button>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                {{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-4 text-sm">
                                {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d M Y, H:i') }}</td>

                            {{-- Kolom Status --}}
                            <td class="px-5 py-4 text-sm">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight rounded-full 
                    {{ $isJatuhTempo ? 'bg-red-200 text-red-900' : ($transaksi->status == 'dipinjam' ? 'bg-yellow-200 text-yellow-900' : 'bg-green-200 text-green-900') }}">
                                    {{ $isJatuhTempo ? 'Jatuh Tempo' : ucfirst($transaksi->status) }}
                                </span>
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="px-5 py-4 text-sm">
                                @if ($transaksi->status == 'dipinjam')
                                    <button wire:click="konfirmasiPengembalian({{ $transaksi->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Kembalikan
                                    </button>
                                @endif

                                @if ($isJatuhTempo)
                                    @php
                                        $pesan = "Pemberitahuan: Peminjaman barang '{$transaksi->barang->nama_barang}' atas nama Anda telah melewati batas waktu pengembalian. Harap segera dikembalikan ke gudang. Terima kasih.";
                                    @endphp
                                    <a href="https://api.whatsapp.com/send?phone={{ $transaksi->siswa->formatted_no_hp }}&text={{ urlencode($pesan) }}"
                                        target="_blank" class="text-green-600 hover:text-green-900 font-semibold ml-2">
                                        Chat WA
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $transaksis->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Form Peminjaman --}}
    @if ($showTransactionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Form Peminjaman Barang</h3>
                    <button wire:click="closeTransactionModal"
                        class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
                </div>

                {{-- Form Anda sekarang ada di sini --}}

                {{-- !formulir peminjaman --}}
                <form wire:submit.prevent="simpanPeminjaman">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Kolom Kiri: Data Siswa --}}
                        <div class="space-y-4">
                            <div>
                                <label for="nis" class="block text-sm font-medium text-gray-700">Cari NIS
                                    Siswa</label>
                                <input type="text" id="nis" wire:model.live.debounce.300ms="nis"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="Masukkan NIS...">
                            </div>

                            @if ($siswaDitemukan)
                                <div class="bg-gray-50 p-4 rounded-md border">
                                    <p class="font-bold">{{ $siswaDitemukan->nama }}</p>
                                    <p class="text-sm text-gray-600">Kelas: {{ $siswaDitemukan->kelas }}</p>
                                    <p class="text-sm text-gray-600">No. HP: {{ $siswaDitemukan->no_hp }}</p>
                                </div>
                            @elseif(!empty($nis))
                                <div class="bg-red-50 p-4 rounded-md border border-red-200">
                                    <p class="font-bold text-red-700">Siswa tidak ditemukan.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Kolom Kanan: Data Barang & Peminjaman --}}
                        <div class="space-y-4">
                            <div class="relative">
                                <label for="searchBarang" class="block text-sm font-medium text-gray-700">Cari Barang
                                    (Kode/Nama)</label>
                                <input type="text" id="searchBarang" wire:model.live.debounce.300ms="searchBarang"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="Ketik min. 2 huruf...">

                                @if (!empty($barangDitemukan) && !$selectedBarangId)
                                    <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border">
                                        <ul class="max-h-60 overflow-auto">
                                            @forelse($barangDitemukan as $barang)
                                                {{-- Logika untuk membedakan barang yang tersedia dan habis --}}
                                                @if ($barang->jumlah_saat_ini > 0)
                                                    {{-- BARANG TERSEDIA (Bisa Diklik) --}}
                                                    <li wire:click="selectBarang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}')"
                                                        class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                                        {{ $barang->nama_barang }} (Stok:
                                                        {{ $barang->jumlah_saat_ini }})
                                                    </li>
                                                @else
                                                    {{-- BARANG HABIS (Tidak Bisa Diklik) --}}
                                                    <li class="px-4 py-2 text-gray-600 cursor-not-allowed">
                                                        {{ $barang->nama_barang }}
                                                        <span class="text-xs italic">(Semua sedang dipinjam)</span>
                                                    </li>
                                                @endif
                                            @empty
                                                <li class="px-4 py-2 text-gray-500">Barang tidak ditemukan.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            @if ($selectedBarangId)
                                <div class="bg-gray-50 p-4 rounded-md border">
                                    <p class="font-bold">Barang Dipilih: {{ $selectedBarangNama }}</p>
                                </div>
                            @endif

                            <div>
                                <label for="ruang_pemakaian" class="block text-sm font-medium text-gray-700">Ruang
                                    Pemakaian</label>
                                <input type="text" id="ruang_pemakaian" wire:model="ruang_pemakaian"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="waktu_kembali" class="block text-sm font-medium text-gray-700">Waktu
                                    Pengembalian</label>
                                <input type="datetime-local" id="waktu_kembali" wire:model="waktu_kembali"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
                            Simpan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>


@endif

{{-- modal pengembalian --}}
@if ($transaksiIdUntukDikembalikan)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Pengembalian</h3>
            <p class="text-sm text-gray-600 mb-4">
                Anda yakin ingin menandai barang <strong>"{{ $transaksiTerpilih->barang->nama_barang }}"</strong>
                yang dipinjam
                oleh <strong>"{{ $transaksiTerpilih->siswa->nama }}" </strong>telah dikembalikan?
            </p>
            <div class="mt-4 flex justify-end space-x-2">
                <button wire:click="$set('transaksiIdUntukDikembalikan', null)"
                    class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button wire:click="prosesPengembalian" class="px-4 py-2 bg-purple-700 text-white rounded">Ya,
                    Sudah
                    Kembali</button>
            </div>
        </div>
    </div>
@endif

{{-- MODAL TAMPILKAN DETAIL SISWA --}}
@if ($siswaDetail)
    {{-- Latar belakang gelap, klik di sini akan menutup modal --}}
    <div wire:click="closeModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30 cursor-pointer">

        {{-- Kotak modal putih. @click.stop mencegah klik di sini ikut menutup modal --}}
        <div @click.stop class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md cursor-default">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Peminjam</h3>

                {{-- Tombol 'x' sekarang memanggil method closeModal --}}
                <button wire:click="closeModal"
                    class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
            </div>
            <div>
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">NIS</td>
                            <td class="py-2">{{ $siswaDetail->nis }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Nama</td>
                            <td class="py-2">{{ $siswaDetail->nama }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Kelas</td>
                            <td class="py-2">{{ $siswaDetail->kelas }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold">No. HP</td>
                            <td class="py-2">{{ $siswaDetail->no_hp }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
</div>
