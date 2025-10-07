{{-- // TODO Untuk Finalisasi:
TODO TAMBAHIN TABEL BARANG READY DI SISWA, JUST VIEW
TODO ADD JUMLAH BARANG YG MAU DIPINJAM
TODO TAMBAHAIN BARANG YANG TERSEDIA DI SETIAP RUANGAN
TODO SISWA BISA UBAH PASS
TODO CHAT PAK YANDRIK TTG KP, APA AJA YANG PERLU DIBAWA
    TODO 1. Buat LSTM.
    TODO 2. Batasi peminjaman: Peminjaman pada siswa ga bole booking setelah jam 16.00, begitu juga pada pengembalian barang dan peminjaman jalur admin.
    . KETIKA SISWA MENGEMBALIKAN BARANG DALAM KEADAAN RUSAK, TANGGUHKAN AKUN SISWA UNTUK SESAAT(SISWA TIDAK BISA MENGAJUKAN PEMINJAMAN HINGGA ADMIN MEMBATALKAN PENANGGUHAN). TAMBAHKAN FITUR "BATAL PENANGGUHAN" PADA MANAGEMENT SISWA //?--done--.
    5. KETIKA PEMINJAMAN SISWA AKAN BERAKHIR SETENGAH JAM LAGI, SEDIAKAN TOMBOL KIRIM NOTIF KE WA SISWA BERSANGKUTAN --done--
    --}}

<div>
    {{-- Notifikasi --}}
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

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        {{-- Card Total Unit Barang --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Unit Barang</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalUnitBarang }}</p>
            </div>
        </div>
        {{-- Card Sedang Dipinjam --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalDipinjam }}</p>
            </div>
        </div>
        {{-- Card Barang Rusak --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Barang Rusak</p>
                <p class="text-3xl font-bold text-red-500">{{ $totalRusak }}</p>
            </div>
        </div>
        {{-- Card Jatuh Tempo --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Jatuh Tempo</p>
                <p class="text-3xl font-bold text-yellow-500">{{ $jatuhTempo }}</p>
            </div>
        </div>
    </div>

    {{-- Tabel Permintaan Masuk --}}
    @if ($permintaanMasuk->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">PERMINTAAN PEMINJAMAN MASUK</h2>
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Siswa</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Barang</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Waktu Pinjam</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permintaanMasuk as $permintaan)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">{{ $permintaan->siswa->nama }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @foreach ($permintaan->barangs as $barang)
                                        <span class="block">{{ $barang->nama_barang }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($permintaan->waktu_pinjam)->format('d M, H:i') }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    <button wire:click="setujuiPermintaan({{ $permintaan->id }})"
                                        class="font-semibold text-green-600 hover:text-green-900">Setujui</button>
                                    <button wire:click="tolakPermintaan({{ $permintaan->id }})"
                                        class="font-semibold text-red-600 hover:text-red-900 ml-4">Tolak</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Grup Tombol Aksi & Pencarian --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <h2 class="text-xl font-semibold text-gray-700">RIWAYAT PEMINJAMAN</h2>
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            {{-- filter pencarian --}}
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..."
                class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            <div class="flex items-center gap-2 w-full sm:w-auto">

                {{-- cetak laporan --}}
                @can('kelola-pengguna')
                    <button wire:click="openReportModal"
                        class="w-1/2 sm:w-auto flex justify-center items-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-2 px-4 rounded text-sm">
                        Cetak Laporan
                    </button>
                @endcan


                {{-- form peminjaman --}}
                <button wire:click="openTransactionModal"
                    class="w-1/2 sm:w-auto flex justify-center items-center bg-primary hover:bg-purple-800 text-white font-bold py-2 px-4 rounded-lg shadow-md">Tambah
                    Peminjaman</button>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Peminjaman --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto relative">
        <div wire:loading.flex wire:target="search, nextPage, previousPage"
            class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
            <svg wire:loading wire:target="simpanPeminjaman" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>
        <table class="w-full table-auto leading-normal">
            <thead class="bg-primary">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama
                        Barang</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Asal
                        Barang</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama
                        Peminjam</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Ruang
                        Penggunaan</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Waktu
                        Pinjam</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Waktu
                        Kembali</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status
                    </th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $transaksi)
                    @php
                        $waktuKembali = \Carbon\Carbon::parse($transaksi->waktu_kembali);
                        $isJatuhTempo =
                            $transaksi->status == 'dipinjam' &&
                            \Carbon\Carbon::parse($transaksi->waktu_kembali)->isPast();
                        $statusBadgeClass = match ($transaksi->status) {
                            'dipinjam', 'disetujui' => $isJatuhTempo
                                ? 'bg-red-200 text-red-900'
                                : 'bg-yellow-200 text-yellow-900',
                            'dikembalikan' => 'bg-green-200 text-green-900',
                            'ditolak' => 'bg-orange-200 text-orange-900',
                            default => 'bg-gray-200 text-gray-800',
                        };
                        $isMendekatiBatas =
                            ($transaksi->status == 'dipinjam' || $transaksi->status == 'disetujui') &&
                            $waktuKembali->between(now(), now()->addMinutes(30));
                    @endphp
                    <tr class="border-b border-gray-200 {{ $isJatuhTempo ? 'bg-red-100' : 'hover:bg-gray-50' }}">
                        <td class="px-3 py-4 text-sm">
                            @foreach ($transaksi->barangs as $barang)
                                <span class="block">{{ $barang->nama_barang }}</span>
                            @endforeach
                        </td>
                        <td class="px-3 py-4 text-sm">
                            @foreach ($transaksi->barangs as $barang)
                                <span class="block">{{ $barang->ruangan->nama_ruangan }}</span>
                            @endforeach
                        </td>
                        {{-- <td class="px-3 py-4 text-sm">
                             @foreach ($transaksi->barangs as $barang)
                            {{ optional($transaksi->barangs->first()?->ruangan)->nama_ruangan ?? 'N/A' }}</td> --}}
                        <td class="px-3 py-4 text-sm">
                            <button wire:click="showSiswaDetail({{ $transaksi->siswa->id }})"
                                class="font-semibold text-indigo-600 hover:underline">{{ $transaksi->siswa->nama }}</button>
                        </td>
                        <td class="px-3 py-4 text-sm">{{ $transaksi->ruang_pemakaian }}</td>
                        <td class="px-3 py-4 text-sm">
                            {{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d M Y, H:i') }}</td>
                        <td class="px-3 py-4 text-sm">
                            {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d M Y, H:i') }}</td>
                        <td class="px-3 py-4 text-sm">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full {{ $statusBadgeClass }}">
                                {{ $isJatuhTempo ? 'Jatuh Tempo' : ucfirst($transaksi->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap">
                            @if ($isMendekatiBatas)
                                @php
                                    $barangList = $transaksi->barangs->pluck('nama_barang')->join(', ');
                                    $pesan = "PENGINGAT: Waktu peminjaman untuk barang '{$barangList}' akan berakhir dalam kurang dari 30 menit lagi! Mohon segera bersiap untuk mengembalikannya ke gudang. Terima kasih.";
                                @endphp
                                <a href="https://api.whatsapp.com/send?phone={{ $transaksi->siswa->formatted_no_hp }}&text={{ urlencode($pesan) }}"
                                    target="_blank" class="font-semibold text-orange-600 hover:text-orange-900">
                                    Kirim Pengingat WA
                                </a>
                            @endif

                            @if ($transaksi->status == 'disetujui')
                                <button wire:click="konfirmasiAmbil({{ $transaksi->id }})"
                                    class="font-semibold text-blue-600 hover:text-blue-900">Konfirmasi Ambil</button>
                                <button wire:click="batalkanPeminjaman({{ $transaksi->id }})"
                                    wire:confirm="Anda yakin ingin membatalkan booking ini?"
                                    class="font-semibold text-red-600 hover:text-red-900">
                                    Batalkan
                                </button>
                            @endif

                            @if ($transaksi->status == 'dipinjam')
                                <button wire:click="konfirmasiPengembalian({{ $transaksi->id }})"
                                    class="font-semibold text-indigo-600 hover:text-indigo-900 ml-2">Kembalikan</button>
                            @endif
                            @if ($isJatuhTempo)
                                @php
                                    $barangList = $transaksi->barangs->pluck('nama_barang')->join(', ');
                                    $pesan = "Pemberitahuan: Peminjaman barang '{$barangList}' atas nama '{$transaksi->siswa->nama}' telah melewati batas waktu pengembalian. Harap segera dikembalikan ke gudang. Terima kasih.";
                                @endphp
                                <a href="https://api.whatsapp.com/send?phone={{ $transaksi->siswa->formatted_no_hp }}&text={{ urlencode($pesan) }}"
                                    target="_blank"
                                    class="text-green-600 hover:text-green-900 font-semibold ml-2">Chat
                                    WA</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">Belum ada riwayat peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $transaksis->links() }}</div>
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

                <form wire:submit.prevent="simpanPeminjaman">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kolom Kiri: Data Siswa & Keranjang --}}
                        <div class="space-y-4">
                            {{-- Pencarian Siswa --}}
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
                                </div>
                            @elseif(!empty($nis))
                                <div class="bg-red-50 p-4 rounded-md border border-red-200">
                                    <p class="font-bold text-red-700">Siswa tidak ditemukan.</p>
                                </div>
                            @endif

                            {{-- Keranjang Peminjaman --}}
                            @if (!empty($keranjang))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Barang yang Akan
                                        Dipinjam:</label>
                                    <div class="mt-2 space-y-2 border rounded-md p-2 max-h-40 overflow-auto">
                                        @foreach ($keranjang as $index => $item)
                                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                                <span class="text-sm">{{ $item['nama'] }}</span>
                                                <button type="button"
                                                    wire:click="hapusDariKeranjang({{ $index }})"
                                                    class="text-red-500 hover:text-red-700 text-xs font-bold">HAPUS</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @error('keranjang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Kolom Kanan: Data Barang & Peminjaman --}}
                        <div class="space-y-4">
                            {{-- Pencarian Barang --}}
                            <div class="relative">
                                <label for="searchBarang" class="block text-sm font-medium text-gray-700">Cari &
                                    Tambah Barang</label>
                                <input type="text" id="searchBarang" wire:model.live.debounce.300ms="searchBarang"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="Ketik min. 2 huruf..." autocomplete="off">
                                @if (!empty($barangDitemukan))
                                    <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border">
                                        <ul class="max-h-60 overflow-auto">
                                            @forelse($barangDitemukan as $barang)
                                                @if ($barang->jumlah_saat_ini > 0)
                                                    {{-- BARANG TERSEDIA (Bisa Diklik) --}}
                                                    <li wire:click="tambahKeKeranjang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}')"
                                                        class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                                        {{ $barang->nama_barang }} (Stok:
                                                        {{ $barang->jumlah_saat_ini }})
                                                    </li>
                                                @else
                                                    {{-- BARANG HABIS (Tidak Bisa Diklik) --}}
                                                    <li class="px-4 py-2 text-gray-400 cursor-not-allowed">
                                                        {{ $barang->nama_barang }} (Stok habis)
                                                    </li>
                                                @endif
                                            @empty
                                                <li class="px-4 py-2 text-gray-500">Barang tidak ditemukan.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            {{-- Input Ruang & Waktu --}}
                            <div>
                                <label for="ruang_pemakaian" class="block text-sm font-medium text-gray-700">Ruang
                                    Pemakaian</label>
                                <select wire:model="ruang_pemakaian" id="ruang_pemakaian"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruang_pemakaian')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
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
                        <button type="button" wire:click="closeTransactionModal()"
                            class="px-4 py-2 bg-gray-200 rounded mr-2">Batal</button>

                        <button type="submit" wire:loading.attr="disabled" {{-- Tombol dinonaktifkan saat loading --}}
                            class="bg-primary hover:bg-purple-800 text-white font-bold py-2 px-4 rounded flex items-center">

                            {{-- Ikon loading, muncul saat method 'simpanPeminjaman' berjalan --}}
                            <svg wire:loading wire:target="simpanPeminjaman"
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>

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
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Pengembalian</h3>
                <p class="text-sm text-gray-600 mb-4">Peminjam: <strong>{{ $transaksiTerpilih->siswa->nama }}</strong>
                </p>

                <div class="space-y-4">
                    <p class="text-sm font-medium">Beri tanda jika ada barang yang rusak saat dikembalikan:</p>
                    @foreach ($transaksiTerpilih->barangs as $barang)
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                            <span class="text-sm">{{ $barang->nama_barang }} (Dipinjam:
                                {{ $barang->pivot->kuantitas }})</span>
                            <div>
                                <label for="rusak_{{ $barang->id }}" class="text-sm mr-2">Jumlah Rusak:</label>
                                <input type="number" id="rusak_{{ $barang->id }}"
                                    wire:model="kerusakanItems.{{ $barang->id }}"
                                    class="w-20 text-sm border-gray-300 rounded-md" min="0"
                                    max="{{ $barang->pivot->kuantitas }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="$set('transaksiIdUntukDikembalikan', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="prosesPengembalian" class="px-4 py-2 bg-primary text-white rounded">Proses
                        Pengembalian</button>
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

    {{-- Modal Filter Laporan --}}
    @if ($showReportModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
                {{-- Form sekarang ada di dalam modal --}}
                <form action="{{ route('laporan.transaksi') }}" method="GET" target="_blank">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cetak Laporan Transaksi</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih rentang tanggal untuk mencetak laporan. Kosongkan untuk
                        mencetak semua riwayat transaksi.</p>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Dari
                                Tanggal</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" wire:model="tanggal_mulai"
                                class="mt-1 text-sm border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Sampai
                                Tanggal</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" wire:model="tanggal_akhir"
                                class="mt-1 text-sm border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeReportModal"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Alasan Penolakan --}}
    @if ($showTolakModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="prosesPenolakan">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Alasan Penolakan</h3>
                    <div>
                        <label for="alasan_penolakan" class="sr-only">Alasan Penolakan</label>
                        <textarea wire:model="alasan_penolakan" id="alasan_penolakan" rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Contoh: Barang tidak tersedia pada jadwal tersebut."></textarea>
                        @error('alasan_penolakan')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showTolakModal', false)"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Tolak
                            Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
