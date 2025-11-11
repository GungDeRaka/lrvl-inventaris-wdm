<div>
    {{-- Notifikasi Sukses --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold mr-3 text-gray-800">Manajemen Data Barang</h1>

            {{-- FILTER BARANG --}}
            <div class="flex items-center space-x-2 w-full md:w-auto">

                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama..."
                    class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm">

                <select wire:model.live="filterKategori" class="block w-full border-fuchsia-500 rounded-md shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- TOMBOL TAMBAH BARANG --}}
        {{-- //TODO KETIKA PENAMBAHAN BARANG, BARANG JANGAN LANGSUNG DISIMPAN. BARANG YANG DITAMBAHKAN , DIMINTA KONFIRMASI DLU KE KEPALA GUDANG. SETELAH ACC, BARANG OTOMATIS TER-INPUT DI SISTEM ✅ --}}
        <button wire:click="openModal()"
            class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
            Tambah Barang
        </button>
    </div>

    {{-- Tabel Manajemen Barang --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-200">
                <tr>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Kode Barang</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama Barang</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Kategori</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Lokasi/Ruangan</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Stok</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $barang)
                    <tr class="hover:bg-gray-300 ">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->kode_barang }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <button wire:click="$set('detailBarangId', {{ $barang->id }})"
                                class="font-semibold text-indigo-600 hover:underline">
                                {{ $barang->nama_barang }}
                            </button>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            {{ $barang->kategori->nama_kategori }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            {{ $barang->ruangan->nama_ruangan }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->jumlah_saat_ini }}
                            /
                            {{ $barang->jumlah_total }}
                            @if ($barang->jumlah_rusak > 0)
                                <span class="text-red-600 block text-xs">(Rusak: {{ $barang->jumlah_rusak }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <button wire:click="edit({{ $barang->id }})"
                                class="text-yellow-600 hover:text-yellow-900 mr-2 font-semibold">Edit</button>
                            <button wire:click="openTambahStokModal({{ $barang->id }})"
                                class="font-semibold text-green-600 hover:text-green-900 ml-2 mr-2">Tambah
                                Stok</button>
                            @can('kelola-pengguna')
                                <button wire:click="openPindahModal({{ $barang->id }})"
                                    class="font-semibold text-blue-600 hover:text-blue-900 mx-2">Pindahkan</button>
                            @endcan
                            <button wire:click="konfirmasiStatusRusak({{ $barang->id }})"
                                class="text-red-600 hover:text-red-900 font-semibold">
                                Tandai Rusak
                            </button>
                            @if ($barang->jumlah_rusak > 0)
                                <button wire:click="konfirmasiPerbaikan({{ $barang->id }})"
                                    class="text-green-600 hover:text-green-900 ml-2 font-semibold">
                                    Perbaiki
                                </button>
                            @endif

                        </td>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($barangs->hasPages())
            <div class="p-4">
                {{ $barangs->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL UNTUK TAMBAH/EDIT BARANG --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            {{-- Wrapper utama modal --}}
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto p-6 relative"
                @click.away="closeModal()">

                <form wire:submit.prevent="simpanBarang">
                    <input type="hidden" wire:model="barang_id">

                    <h3 class="text-lg font-medium text-gray-900 mb-4 sticky top-0 bg-white z-10 py-2 border-b">
                        {{ $barang_id ? 'Edit Data Barang' : 'Tambah Barang Baru' }}
                    </h3>

                    <div class="space-y-4">
                        {{-- kode barang --}}
                        <div>
                            <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode
                                Barang</label>
                            <input type="text" wire:model="kode_barang" id="kode_barang"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('kode_barang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- nama barang --}}
                        <div>
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama
                                Barang</label>
                            <input type="text" wire:model="nama_barang" id="nama_barang"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('nama_barang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- kategori --}}
                        <div>
                            <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select wire:model="kategori_id" id="kategori_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- ruangan --}}
                        @if (!$barang_id)
                            <div>
                                <label for="ruangan_id" class="block text-sm font-medium text-gray-700">Lokasi /
                                    Ruangan</label>
                                <select wire:model="ruangan_id" id="ruangan_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                                @error('ruangan_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        @if (!$barang_id)
                            <hr class="my-4 border-t-2 border-gray-200">
                            <p class="text-sm font-semibold text-gray-700">Detail Pengadaan Awal</p>

                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah
                                    Awal</label>
                                <input type="number" wire:model="jumlah" id="jumlah"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('jumlah')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="harga_satuan" class="block text-sm font-medium text-gray-700">Harga Satuan
                                    (Rp)</label>
                                <input type="number" step="0.01" wire:model="harga_satuan" id="harga_satuan"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('harga_satuan')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="sumber_dana" class="block text-sm font-medium text-gray-700">Sumber
                                    Dana</label>
                                <input type="text" wire:model="sumber_dana" id="sumber_dana"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="Contoh: Dana BOS, Internal Sekolah">
                                @error('sumber_dana')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_pengadaan" class="block text-sm font-medium text-gray-700">Tanggal
                                    Pengadaan</label>
                                <input type="date" wire:model="tanggal_pengadaan" id="tanggal_pengadaan"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('tanggal_pengadaan')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="mt-6 flex justify-end space-x-2 sticky bottom-0 bg-white py-3 border-t z-10">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">
                            @if ($barang_id)
                                Update
                            @else
                                {{ auth()->user()->peran === 'kepala_gudang' ? 'Simpan' : 'Ajukan' }}
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    {{-- tandai barang rusak --}}
    @if ($barangIdToUpdateStatus)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tandai Barang Rusak</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Masukkan jumlah **"{{ $barangNamaForStatus }}"** yang akan ditandai sebagai rusak. Stok total dan
                    tersedia akan dikurangi.
                </p>

                <div>
                    <label for="jumlahYangRusak" class="block text-sm font-medium text-gray-700">Jumlah Rusak</label>
                    <input type="number" wire:model="jumlahYangRusak" id="jumlahYangRusak"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('jumlahYangRusak')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="$set('barangIdToUpdateStatus', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="updateStatusRusak"
                        class="px-4 py-2 bg-red-600 text-white rounded">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL Perbaikan BARANG --}}

    @if ($barangIdToRepair)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Perbaiki Barang Rusak</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Masukkan jumlah **"{{ $barangNamaForRepair }}"** yang telah diperbaiki. Stok akan dikembalikan.
                    (Maks: {{ $maxPerbaikan }})
                </p>

                <div>
                    <label for="jumlahYangDiperbaiki" class="block text-sm font-medium text-gray-700">Jumlah
                        Diperbaiki</label>
                    <input type="number" wire:model="jumlahYangDiperbaiki" id="jumlahYangDiperbaiki"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('jumlahYangDiperbaiki')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="$set('barangIdToRepair', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="prosesPerbaikan" class="px-4 py-2 bg-green-600 text-white rounded">Simpan
                        Perbaikan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error (Tambahkan ini jika belum ada) --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Modal Detail Distribusi Barang --}}
    @if ($detailBarangId && $detailBarang)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30 p-4">
            <div x-data="{ activeTab: 'ringkasan' }"
                class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">Detail Barang:
                        {{ $detailBarang['barang']->nama_barang }}</h3>
                    <button wire:click="closeDetailModal"
                        class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
                </div>

                {{-- Navigasi Tab --}}
                <div class="mb-4 border-b border-gray-200 px-6">
                    <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                        <button @click="activeTab = 'ringkasan'"
                            :class="{ 'border-primary text-primary': activeTab === 'ringkasan', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'ringkasan' }"
                            class="py-3 px-1 border-b-2 font-medium text-sm">
                            Ringkasan Stok
                        </button>
                        <button @click="activeTab = 'distribusi'"
                            :class="{ 'border-primary text-primary': activeTab === 'distribusi', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'distribusi' }"
                            class="py-3 px-1 border-b-2 font-medium text-sm">
                            Distribusi Peminjaman
                        </button>
                        {{-- TAB BARU --}}
                        <button @click="activeTab = 'pengadaan'"
                            :class="{ 'border-primary text-primary': activeTab === 'pengadaan', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'pengadaan' }"
                            class="py-3 px-1 border-b-2 font-medium text-sm">
                            Riwayat Pengadaan
                        </button>
                        <button @click="activeTab = 'pemindahan'"
                            :class="{ 'border-primary text-primary': activeTab === 'pemindahan', '...': activeTab !== 'pemindahan' }"
                            class="py-3 px-1 border-b-2 font-medium text-sm">
                            Riwayat Pemindahan
                        </button>
                    </nav>
                </div>

                {{-- Konten Tab --}}
                <div class="overflow-y-auto p-6 pt-0 flex-1">
                    {{-- Konten untuk Tab Ringkasan Stok --}}
                    <div x-show="activeTab === 'ringkasan'">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <p class="text-sm font-medium text-blue-800">Total Unit</p>
                                <p class="text-2xl font-bold text-blue-900">
                                    {{ $detailBarang['barang']->jumlah_total }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <p class="text-sm font-medium text-green-800">Stok Tersedia</p>
                                <p class="text-2xl font-bold text-green-900">
                                    {{ $detailBarang['barang']->jumlah_saat_ini }}</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <p class="text-sm font-medium text-yellow-800">Sedang Dipinjam</p>
                                <p class="text-2xl font-bold text-yellow-900">
                                    {{ $detailBarang['barang']->jumlah_total - $detailBarang['barang']->jumlah_saat_ini }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Konten untuk Tab Distribusi Peminjaman --}}
                    <div x-show="activeTab === 'distribusi'" style="display: none;">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Peminjam</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Kelas</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Digunakan di Ruang</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Waktu Kembali</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php $adaPinjaman = false; @endphp
                                    @foreach ($detailBarang['distribusi'] as $ruangan => $transaksis)
                                        @foreach ($transaksis as $transaksi)
                                            @php $adaPinjaman = true; @endphp
                                            <tr>
                                                <td class="px-4 py-3 text-sm">{{ $transaksi->siswa->nama }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $transaksi->siswa->kelas }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $transaksi->ruang_pemakaian }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d M Y, H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    @if (!$adaPinjaman)
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada unit
                                                dari barang ini yang sedang dipinjam.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- KONTEN TAB BARU: RIWAYAT PENGADAAN --}}
                    <div x-show="activeTab === 'pengadaan'" style="display: none;">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Tgl. Pengadaan</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Jumlah</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Harga Satuan</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Sumber Dana</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($detailBarang['riwayatPengadaan'] as $pengadaan)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">
                                                {{ \Carbon\Carbon::parse($pengadaan->tanggal_pengadaan)->format('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ $pengadaan->jumlah }} unit</td>
                                            <td class="px-4 py-3 text-sm">Rp
                                                {{ number_format($pengadaan->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $pengadaan->sumber_dana }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">Belum ada
                                                riwayat pengadaan untuk barang ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- KONTEN TAB BARU: RIWAYAT PEMINDAHAN --}}
                    <div x-show="activeTab === 'pemindahan'" style="display: none;">
                        <h4 class="font-semibold text-md text-gray-800 mb-2">Riwayat Barang Keluar (Dipindahkan)</h4>
                        <div class="border rounded-md mb-4">
                            <table class="w-full table-auto text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Tgl.</th>
                                        <th class="px-3 py-2 text-left">Jml</th>
                                        <th class="px-3 py-2 text-left">Ke Barang (Kode)</th>
                                        <th class="px-3 py-2 text-left">Di Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($detailBarang['riwayatPemindahanKeluar'] as $log)
                                        <tr>
                                            <td class="px-3 py-2">{{ $log->created_at->format('d M Y') }}</td>
                                            <td class="px-3 py-2">{{ $log->jumlah_dipindahkan }}</td>
                                            <td class="px-3 py-2">{{ $log->barangTujuan->kode_barang }}</td>
                                            <td class="px-3 py-2">{{ $log->barangTujuan->ruangan->nama_ruangan }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-3 text-gray-500">Belum ada riwayat
                                                pemindahan keluar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <h4 class="font-semibold text-md text-gray-800 mb-2">Riwayat Barang Masuk (Hasil Pindahan)</h4>
                        <div class="border rounded-md">
                            <table class="w-full table-auto text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Tgl.</th>
                                        <th class="px-3 py-2 text-left">Jml</th>
                                        <th class="px-3 py-2 text-left">Dari Barang (Kode)</th>
                                        <th class="px-3 py-2 text-left">Dari Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($detailBarang['riwayatPemindahanMasuk'] as $log)
                                        <tr>
                                            <td class="px-3 py-2">{{ $log->created_at->format('d M Y') }}</td>
                                            <td class="px-3 py-2">{{ $log->jumlah_dipindahkan }}</td>
                                            <td class="px-3 py-2">{{ $log->barangAsal->kode_barang }}</td>
                                            <td class="px-3 py-2">{{ $log->barangAsal->ruangan->nama_ruangan }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-3 text-gray-500">Barang ini bukan
                                                hasil pemindahan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t flex justify-center flex-shrink-0">
                    <button type="button" wire:click="closeDetailModal"
                        class="px-6 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Tambah Stok --}}
    @if ($showTambahStokModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="prosesTambahStok">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Stok: {{ $tambahStokBarangNama }}</h3>

                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        <p class="text-sm font-semibold text-gray-700">Detail Pengadaan Baru</p>
                        <div>
                            <label for="tambah_jumlah" class="block text-sm font-medium text-gray-700">Jumlah
                                Ditambah</label>
                            <input type="number" wire:model="jumlah" id="tambah_jumlah"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('jumlah')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="tambah_harga_satuan" class="block text-sm font-medium text-gray-700">Harga
                                Satuan (Rp)</label>
                            <input type="number" step="0.01" wire:model="harga_satuan" id="tambah_harga_satuan"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('harga_satuan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="tambah_sumber_dana" class="block text-sm font-medium text-gray-700">Sumber
                                Dana</label>
                            <input type="text" wire:model="sumber_dana" id="tambah_sumber_dana"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Contoh: Dana BOS, Internal">
                            @error('sumber_dana')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="tambah_tanggal_pengadaan"
                                class="block text-sm font-medium text-gray-700">Tanggal
                                Pengadaan</label>
                            <input type="date" wire:model="tanggal_pengadaan" id="tambah_tanggal_pengadaan"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('tanggal_pengadaan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">

                        <button type="button" wire:click="closeTambahStokModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">
                            {{ auth()->user()->peran === 'kepala_gudang' ? 'Simpan Penambahan' : 'Ajukan Penambahan' }}
                        </button>
                    </div>
            </div>
            </form>
        </div>
    @endif

    {{-- modal pindahkan barang --}}
    @if ($showPindahModal && $pindahBarang)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300"
            x-data="{ show: true }" x-show="show" x-transition.opacity>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-100"
                x-show="show" x-transition.scale>
                <form wire:submit.prevent="prosesPemindahan" class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-5 border-b pb-3">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            Pindahkan Stok:
                            <span class="text-indigo-600 dark:text-indigo-400">{{ $pindahBarang->nama_barang }}</span>
                        </h3>
                        <button type="button" wire:click="closePindahModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            ✕
                        </button>
                    </div>

                    <!-- Info stok -->
                    <p
                        class="text-sm text-gray-600 dark:text-gray-300 mb-4 bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                        <span class="font-medium">Stok tersedia:</span> {{ $pindahBarang->jumlah_saat_ini }}
                    </p>

                    <!-- Form isi -->
                    <div class="space-y-5 max-h-[65vh] overflow-y-auto pr-2">
                        <!-- Jumlah dan ruangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="jumlahPindah"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Jumlah Pindah*
                                </label>
                                <input type="number" wire:model="jumlahPindah" id="jumlahPindah"
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    max="{{ $pindahBarang->jumlah_saat_ini }}">
                                @error('jumlahPindah')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="ruanganTujuanId"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Ruangan Tujuan*
                                </label>
                                <select wire:model="ruanganTujuanId" id="ruanganTujuanId"
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih ruangan...</option>
                                    @foreach ($ruangans as $ruangan)
                                        @if ($ruangan->id != $pindahBarang->ruangan_id)
                                            <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('ruanganTujuanId')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Detail barang baru -->
                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">
                                Detail Barang Baru di Lokasi Tujuan
                            </p>

                            <div class="space-y-3">
                                <div>
                                    <label for="kodeBaruPindahan"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Kode Barang Baru*
                                    </label>
                                    <input type="text" wire:model="kodeBaruPindahan" id="kodeBaruPindahan"
                                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Harus unik">
                                    @error('kodeBaruPindahan')
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="namaBaruPindahan"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Nama Barang Baru*
                                    </label>
                                    <input type="text" wire:model="namaBaruPindahan" id="namaBaruPindahan"
                                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('namaBaruPindahan')
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closePindahModal()"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-md">
                            Pindahkan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
