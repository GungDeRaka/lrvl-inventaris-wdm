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
                                class="font-semibold text-indigo-600 hover:underline flex items-center"></button>
                                {{ $ruangan->nama_ruangan }}
                            </button>
                        </td>
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            <button wire:click="edit({{ $ruangan->id }})"
                                 class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit
                            </button>
                            <button wire:click="konfirmasiHapus({{ $ruangan->id }})"
                                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.033-2.134H8.033c-1.12 0-2.033.954-2.033 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Hapus
                            </button>
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
