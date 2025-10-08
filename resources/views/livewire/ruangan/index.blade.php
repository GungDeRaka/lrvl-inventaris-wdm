<div>
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex items-center justify-between space-x-2 w-full md:w-auto mb-6">
        <div class="flex justify-around items-center space-x-2">

            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Ruangan</h1>
            {{-- KOTAK PENCARIAN --}}
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama ruangan..."
                class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm">
        </div>
        <button wire:click="openModal()" class="bg-primary hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
            Tambah Ruangan Baru
        </button>
    </div>

    {{-- Tabel Ruangan --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Ruangan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangans as $ruangan)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-5 py-4 text-sm">
                            {{-- Buat nama ruangan menjadi tombol --}}
                            <button wire:click="showDetail({{ $ruangan->id }})"
                                class="font-semibold text-indigo-600 hover:underline">
                                {{ $ruangan->nama_ruangan }}
                            </button>
                        </td>
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            <button wire:click="edit({{ $ruangan->id }})"
                                class="font-semibold text-yellow-600 hover:text-yellow-900">Edit</button>
                            <button wire:click="konfirmasiHapus({{ $ruangan->id }})"
                                class="font-semibold text-red-600 hover:text-red-900 ml-4">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-4">Belum ada data ruangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah/Edit Ruangan --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="simpanRuangan">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $ruangan_id ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}</h3>
                    <div>
                        <label for="nama_ruangan" class="block text-sm font-medium">Nama Ruangan</label>
                        <input type="text" wire:model="nama_ruangan" id="nama_ruangan"
                            class="mt-1 block w-full border-gray-300 rounded-md" autofocus>
                        @error('nama_ruangan')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded">{{ $ruangan_id ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($ruanganIdToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <p>Anda yakin ingin menghapus ruangan ini?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('ruanganIdToDelete', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="hapusRuangan" class="px-4 py-2 bg-red-600 text-white rounded">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Detail Ruangan --}}
    @if ($detailRuangan)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            {{-- Inisialisasi Alpine.js untuk state tab --}}
            <div x-data="{ activeTab: 'asal' }"
                class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[80vh] flex flex-col">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-medium text-gray-900">Detail Ruangan:
                        {{ $detailRuangan['ruangan']->nama_ruangan }}</h3>
                    <button wire:click="closeDetailModal"
                        class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
                </div>

                {{-- Navigasi Tab --}}
                <div class="mb-4 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                        <button @click="activeTab = 'asal'"
                            :class="{ 'border-primary text-primary': activeTab === 'asal', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'asal' }"
                            class="py-3 px-1 border-b-2 font-medium text-sm">
                            Barang Asli di Ruangan Ini
                        </button>
                        <button @click="activeTab = 'pinjaman'"
                            :class="{ 'border-primary text-primary': activeTab === 'pinjaman', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pinjaman' }"
                            class="py-3 px-1 border-b-2 font-medium text-sm">
                            Barang Pinjaman di Ruangan Ini
                        </button>
                    </nav>
                </div>

                {{-- Konten Tab --}}
                <div class="overflow-y-auto">
                    {{-- Konten untuk Tab Barang Asli --}}
                    <div x-show="activeTab === 'asal'">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Nama Barang</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Kategori</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($detailRuangan['barangAsal'] as $barang)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ $barang->nama_barang }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $barang->kategori->nama_kategori }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $barang->jumlah_saat_ini }} /
                                                {{ $barang->jumlah_total }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-gray-500">Tidak ada barang
                                                asli di ruangan ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Konten untuk Tab Barang Pinjaman --}}
                    <div x-show="activeTab === 'pinjaman'" style="display: none;">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Nama Barang</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Asal Barang</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Peminjam</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-primary uppercase">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($detailRuangan['barangPinjamanMasuk'] as $transaksi)
                                        @foreach ($transaksi->barangs as $barang)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">{{ $barang->nama_barang }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $barang->ruangan->nama_ruangan }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $transaksi->siswa->nama }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-yellow-200 text-yellow-900">{{ ucfirst($transaksi->status) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada barang
                                                pinjaman di ruangan ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
