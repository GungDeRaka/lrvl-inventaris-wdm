<div>
    {{-- Notifikasi Sukses --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Data Barang</h1>
        <button wire:click="openModal()" class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
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
                        Stok</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $barang)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->kode_barang }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->nama_barang }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            {{ $barang->kategori->nama_kategori }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->jumlah_saat_ini }} /
                            {{ $barang->jumlah_total }}
                            @if ($barang->jumlah_rusak > 0)
                                <span class="text-red-600 block text-xs">(Rusak: {{ $barang->jumlah_rusak }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <button wire:click="edit({{ $barang->id }})"
                                class="text-yellow-600 hover:text-yellow-900 mr-2 font-semibold">Edit</button>
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
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.away="closeModal()">
                <form wire:submit.prevent="simpanBarang">
                    <input type="hidden" wire:model="barang_id">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $barang_id ? 'Edit Data Barang' : 'Tambah Barang Baru' }}
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                            <input type="text" wire:model="kode_barang" id="kode_barang"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('kode_barang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <input type="text" wire:model="nama_barang" id="nama_barang"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('nama_barang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
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
                        <div>
                            <label for="jumlah_total" class="block text-sm font-medium text-gray-700">Jumlah
                                Total</label>
                            <input type="number" wire:model="jumlah_total" id="jumlah_total"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('jumlah_total')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded">
                            {{ $barang_id ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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

        @if($barangIdToRepair)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Perbaiki Barang Rusak</h3>
        <p class="text-sm text-gray-600 mb-4">
            Masukkan jumlah **"{{ $barangNamaForRepair }}"** yang telah diperbaiki. Stok akan dikembalikan. (Maks: {{ $maxPerbaikan }})
        </p>
        
        <div>
            <label for="jumlahYangDiperbaiki" class="block text-sm font-medium text-gray-700">Jumlah Diperbaiki</label>
            <input type="number" wire:model="jumlahYangDiperbaiki" id="jumlahYangDiperbaiki" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('jumlahYangDiperbaiki') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <button wire:click="$set('barangIdToRepair', null)" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
            <button wire:click="prosesPerbaikan" class="px-4 py-2 bg-green-600 text-white rounded">Simpan Perbaikan</button>
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
</div>
