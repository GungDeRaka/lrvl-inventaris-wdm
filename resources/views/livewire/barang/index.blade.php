<div class="relative min-h-screen">
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center"
            role="alert">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
            </svg>
            <p>{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center"
            role="alert">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Header & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Data Barang</h1>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm transition duration-150 ease-in-out">
            </div>

            {{-- Filter Kategori --}}
            <div class="w-full md:w-48">
                <select wire:model.live="filterKategori"
                    class="w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Aksi Header --}}
            <div class="flex gap-2">
                <button wire:click="openRiwayatPindahModal"
                    class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition"
                    title="Riwayat Pemindahan">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="text-sm font-medium">Pemindahan</span>
                </button>
                <button wire:click="openRiwayatPengadaanModal"
                    class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition"
                    title="Riwayat Pengadaan">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="text-sm font-medium">Pengadaan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Barang --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Barang</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lokasi</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($barangs as $barang)
                        @php $isStokMenipis = $barang->jumlah_saat_ini <= $barang->stok_minimum; @endphp
                        <tr class="hover:bg-gray-50 transition duration-150 {{ $isStokMenipis ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $barang->kode_barang }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button wire:click="$set('detailBarangId', {{ $barang->id }})"
                                    class="font-semibold text-purple-700 hover:text-purple-900 hover:underline focus:outline-none">
                                    {{ $barang->nama_barang }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $barang->kategori->nama_kategori }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $barang->ruangan->nama_ruangan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isStokMenipis ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $barang->jumlah_saat_ini }} / {{ $barang->jumlah_total }}
                                </span>
                                @if ($barang->jumlah_rusak > 0)
                                    <small class="block text-red-600 mt-1 font-semibold">(Rusak:
                                        {{ $barang->jumlah_rusak }})</small>
                                @endif

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center space-x-2">
                                    <button wire:click="edit({{ $barang->id }})"
                                        class="text-yellow-600 hover:text-yellow-900 border border-yellow-200 bg-yellow-50 hover:bg-yellow-100 px-2 py-1 rounded flex items-center gap-1 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg> Edit
                                    </button>
                                    <button wire:click="openTambahStokModal({{ $barang->id }})"
                                        class="text-green-600 hover:text-green-900 border border-green-200 bg-green-50 hover:bg-green-100 px-2 py-1 rounded flex items-center gap-1 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg> Stok
                                    </button>
                                    @can('kelola-pengguna')
                                        <button wire:click="openPindahModal({{ $barang->id }})"
                                            class="text-blue-600 hover:text-blue-900 border border-blue-200 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded flex items-center gap-1 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg> Pindah
                                        </button>
                                        <button wire:click="konfirmasiStatusRusak({{ $barang->id }})"
                                            class="text-red-600 hover:text-red-900 border border-red-200 bg-red-50 hover:bg-red-100 px-2 py-1 rounded flex items-center gap-1 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg> Rusak
                                        </button>
                                        {{-- Tombol Perbaiki (jika ada barang rusak) --}}
                                        @if ($barang->jumlah_rusak > 0)
                                            <button wire:click="konfirmasiPerbaikan({{ $barang->id }})"
                                                class="inline-flex items-center gap-1 border border-emerald-400 text-emerald-600 hover:bg-emerald-50 text-sm font-medium px-3 py-1.5 rounded-md transition duration-150 ease-in-out"
                                                title="Perbaiki Barang Rusak">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                                    class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 16l5 5L20 7M17 7l3 3-9 9-3-3 9-9z" />
                                                </svg>
                                                <span>Perbaiki</span>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada barang ditemukan.</p>
                                    <p class="text-sm text-gray-400">Coba kata kunci lain atau tambahkan barang baru.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($barangs->hasPages())
        <div class="bg-gray-50 px-6 py-4 border items-start border-gray-300 shadow-lg w-1/3 rounded-md">
            <span class="items-start">{{ $barangs->links() }}</span>
        </div>
    @endif

    {{-- FAB: Tambah Barang --}}
    <button wire:click="openModal"
        class="fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-purple-800 transition transform hover:scale-110 focus:outline-none z-50 flex items-center justify-center"
        title="Buat Pengajuan RAB Baru">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="mx-2 text-sm font-semibold">Tambah Barang</span>
    </button>

    {{-- Modal Tambah/Edit Barang --}}
    @if ($showModal)
        {{-- ... (Gunakan kode modal tambah/edit yang sudah kita sempurnakan sebelumnya) ... --}}
        {{-- (Saya singkat di sini agar tidak terlalu panjang, pastikan Anda menyalin kode modal Anda yang terakhir) --}}
        @if (session()->has('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center"
                role="alert">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-transition>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md flex flex-col max-h-[85vh]">
                <form wire:submit.prevent="simpanBarang" class="flex flex-col flex-1 min-h-0">
                    <div class="p-6 border-b flex-shrink-0">
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $barang_id ? 'Edit Data Barang' : 'Tambah Barang Baru' }}</h3>
                    </div>
                    <div class="space-y-4 overflow-y-auto p-6 flex-1">
                        {{-- ... Form fields (kode, nama, kategori, dll) ... --}}
                        <div>
                            <label for="kode_barang" class="block text-sm font-medium text-gray-700 mb-1">Kode
                                Barang</label>
                            <input type="text" wire:model="kode_barang"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 {{ $barang_id ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}"
                                {{ $barang_id ? 'disabled' : '' }}>
                            @error('kode_barang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                            @if ($barang_id)
                                <span class="text-xs text-gray-500">Kode barang tidak dapat diubah.</span>
                            @endif
                        </div>

                        <div>
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama
                                Barang</label>
                            <input type="text" wire:model="nama_barang"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            @error('nama_barang')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="kategori_id"
                                class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select wire:model="kategori_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$barang_id)
                            <div>
                                <label for="ruangan_id" class="block text-sm font-medium text-gray-700 mb-1">Lokasi
                                    Awal</label>
                                <select wire:model="ruangan_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
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
                                <label for="stok_minimum" class="block text-sm font-medium text-gray-700">Stok
                                    Minimum</label>
                                <small class="text-xxs text-amber-300 mb-1">Tentukan stok minimum untuk memberi
                                    peringatan ketika stok barang hampir habis</small>
                                <input type="number" wire:model="stok_minimum"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                @error('stok_minimum')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr class="my-2">
                            <p class="text-sm font-bold text-gray-700 mb-2">Detail Pengadaan Awal</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Jumlah</label>
                                    <input type="number" wire:model="jumlah"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    @error('jumlah')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Harga Satuan</label>
                                    <input type="number" wire:model="harga_satuan"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    @error('harga_satuan')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-600 mb-1">Sumber Dana</label>
                                {{-- Dropdown Sumber Dana (Sama seperti sebelumnya) --}}
                                @if ($isAddingSumberDana)
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="sumberDanaBaru" placeholder="Nama Sumber"
                                            class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <button type="button" wire:click="simpanSumberDanaBaru"
                                            class="bg-green-600 text-white px-2 rounded text-xs">Save</button>
                                        <button type="button" wire:click="toggleSumberDanaBaru"
                                            class="bg-gray-300 px-2 rounded text-xs">X</button>
                                    </div>
                                @else
                                    <select wire:model="sumber_dana_id"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="">Pilih Sumber</option>
                                        @foreach (\App\Models\SumberDana::all() as $sumber)
                                            <option value="{{ $sumber->id }}">{{ $sumber->nama_sumber }}</option>
                                        @endforeach
                                    </select>
                                    {{-- Tombol Kecil "Tambah Baru" --}}
                                    <button type="button" wire:click="toggleSumberDanaBaru"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 flex items-center font-semibold focus:outline-none">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Sumber Dana Baru
                                    </button>
                                @endif
                                @error('sumber_dana_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-600 mb-1">Tanggal</label>
                                <input type="date" wire:model="tanggal_pengadaan"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @error('tanggal_pengadaan')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="p-6 border-t flex justify-end space-x-3 bg-gray-50 flex-shrink-0 rounded-b-lg">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm hover:bg-gray-50 font-medium">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-700 text-white rounded-md shadow-sm hover:bg-purple-800 font-medium">
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

    {{-- Modal Riwayat Pemindahan (Gunakan kode sebelumnya) --}}
    @if ($showRiwayatPindahModal)
        {{-- ... (Kode modal riwayat pemindahan yang sudah Anda miliki, hanya sesuaikan class styling jika perlu) ... --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">Riwayat Pemindahan Barang</h3>
                    <button wire:click="closeRiwayatPindahModal"
                        class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
                </div>
                <div class="overflow-auto p-0 flex-1">
                    <table class="w-full table-auto text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b sticky top-0 z-10">
                            <tr>
                                <th class="py-3 px-4 bg-gray-50">Tanggal</th>
                                <th class="py-3 px-4 bg-gray-50">Barang</th>
                                <th class="py-3 px-4 bg-gray-50">Dari</th>
                                <th class="py-3 px-4 bg-gray-50 text-center">Jml</th>
                                <th class="py-3 px-4 bg-gray-50">Ke</th>
                                <th class="py-3 px-4 bg-gray-50">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayatPemindahan as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 whitespace-nowrap">{{ $log->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-3 px-4 font-medium text-gray-900">
                                        {{ $log->barangAsal->nama_barang ?? '-' }}</td>
                                    <td class="py-3 px-4 text-gray-500">
                                        {{ $log->barangAsal->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-blue-600">
                                        {{ $log->jumlah_dipindahkan }}</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $log->barangTujuan->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-500">{{ $log->user->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-400">Belum ada riwayat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (count($riwayatPemindahan))
                    <div class="p-4 border-t bg-gray-50 flex-shrink-0">{{ $riwayatPemindahan->links() }}</div>
                @endif
                <div class="p-4 border-t flex justify-end flex-shrink-0">
                    <button wire:click="closeRiwayatPindahModal"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Riwayat Pengadaan (BARU) --}}
    @if ($showRiwayatPengadaanModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-transition>
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">

                {{-- Header Modal --}}
                <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white flex-shrink-0">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Riwayat Pengadaan Barang</h3>
                        <p class="text-sm text-gray-500 mt-1">Laporan detail barang masuk dan sumber pendanaan.</p>
                    </div>
                    <button wire:click="closeRiwayatPengadaanModal"
                        class="text-gray-400 hover:text-gray-600 transition p-2 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Filter & Action Toolbar --}}
                <div
                    class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row justify-between items-center gap-4 flex-shrink-0">

                    {{-- Tombol Cetak (Kiri) --}}
                    <div>
                        {{-- Gunakan target="_blank" pada tag <a> pembungkus atau gunakan Javascript untuk membuka di tab baru jika method di livewire melakukan redirect --}}
                        {{-- Di sini saya gunakan wire:click biasa, nanti di controller PDF Anda bisa mengatur headers untuk download/view --}}
                        <button wire:click="cetakLaporanPengadaan" wire:loading.attr="disabled"
                            class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-md shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8-4V3a1 1 0 00-1-1H8a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1zM7 10a1 1 0 011-1h8a1 1 0 011 1v10H7V10z" />
                            </svg>
                            <span>Cetak Laporan</span>
                            {{-- Loading Indicator kecil saat proses cetak --}}
                            <svg wire:loading target="cetakLaporanPengadaan"
                                class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>

                    {{-- Filter Inputs (Kanan) --}}
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full lg:w-auto justify-end">
                        <div class="flex items-center gap-2 bg-white p-1 rounded-md border shadow-sm">
                            <span class="text-sm text-gray-500 pl-2">Periode:</span>
                            <input type="date" wire:model.live="filterTanggalMulai"
                                class="border-0 text-sm focus:ring-0 p-1 text-gray-700">
                            <span class="text-gray-400">-</span>
                            <input type="date" wire:model.live="filterTanggalAkhir"
                                class="border-0 text-sm focus:ring-0 p-1 text-gray-700">
                        </div>

                        <div class="w-full md:w-56">
                            <select wire:model.live="filterSumberDana"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-purple-500 focus:border-purple-500 py-2">
                                <option value="">Semua Sumber Dana</option>
                                @foreach ($sumberDanas as $sumber)
                                    <option value="{{ $sumber->id }}">{{ $sumber->nama_sumber }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Tabel Pengadaan (UI UPGRADE) --}}
                <div class="overflow-auto p-0 flex-1">
                    <table class="w-full table-auto text-sm text-left border-collapse">
                        {{-- Header dengan warna --}}
                        <thead
                            class="bg-purple-100 text-purple-900 font-semibold border-b-2 border-purple-200 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="py-4 px-4 whitespace-nowrap">Tgl. Masuk</th>
                                <th class="py-4 px-4">Barang</th>
                                <th class="py-4 px-4">Ruangan Awal</th>
                                <th class="py-4 px-4 text-center">Jumlah</th>
                                <th class="py-4 px-4 text-right">Harga Satuan</th>
                                <th class="py-4 px-4 text-right">Total Harga</th>
                                <th class="py-4 px-4">Sumber Dana</th>
                                <th class="py-4 px-4">Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($riwayatPengadaan as $log)
                                {{-- Zebra Striping (baris genap warna beda) & Hover Effect --}}
                                <tr
                                    class="transition-colors duration-200 hover:bg-purple-50 @if ($loop->even) bg-gray-50 @endif">
                                    <td class="py-3 px-4 whitespace-nowrap text-gray-600 font-medium">
                                        {{ \Carbon\Carbon::parse($log->tanggal_pengadaan)->format('d M Y') }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-800">
                                        {{ $log->barang->nama_barang ?? '-' }}</td>
                                    <td class="py-3 px-4 text-gray-600">
                                        {{ $log->barang->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-green-600 bg-green-50 rounded-md">
                                        +{{ $log->jumlah }}</td>
                                    <td class="py-3 px-4 text-right text-gray-600">Rp
                                        {{ number_format($log->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-gray-900">Rp
                                        {{ number_format($log->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">
                                        {{-- Badge Sumber Dana yang lebih menarik --}}
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200 shadow-sm">
                                            {{ $log->sumberDana->nama_sumber ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-gray-500">{{ $log->user->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-gray-400 bg-gray-50">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <span class="block font-medium">Belum ada data pengadaan yang sesuai
                                            filter.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        {{-- Footer dengan warna --}}
                        <tfoot
                            class="bg-purple-50 font-bold text-purple-900 border-t-2 border-purple-200 sticky bottom-0 shadow-[0_-2px_4px_rgba(0,0,0,0.06)]">
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-right uppercase text-xs tracking-wider">
                                    Total Biaya Keseluruhan (Sesuai Filter):
                                </td>
                                <td class="py-4 px-4 text-right text-lg">
                                    Rp {{ number_format($totalBiayaPengadaan, 0, ',', '.') }}
                                </td>
                                <td colspan="2" class="bg-purple-50"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Pagination --}}
                @if (count($riwayatPengadaan))
                    <div class="p-4 border-t border-gray-200 bg-white flex-shrink-0 w-1/4 mb-20 md:mb-24">
                        {{ $riwayatPengadaan->links() }}
                    </div>
                @endif
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

    @if ($detailBarangId && $detailBarang)
        {{-- ... (Kode modal detail barang dengan 3 tab yang sudah ada) ... --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            {{-- ... Isi modal detail ... --}}
            <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[85vh] flex flex-col"
                x-data="{ activeTab: 'ringkasan' }">
                <div class="flex justify-between items-center p-6 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">Detail: {{ $detailBarang['barang']->nama_barang }}
                    </h3>
                    <button wire:click="closeDetailModal" class="text-gray-500 text-3xl">&times;</button>
                </div>
                <div class="px-6 border-b flex space-x-6">
                    <button @click="activeTab = 'ringkasan'"
                        :class="activeTab === 'ringkasan' ? 'border-purple-600 text-purple-600' :
                            'border-transparent text-gray-500'"
                        class="py-3 border-b-2 font-medium text-sm transition">Ringkasan</button>
                    <button @click="activeTab = 'distribusi'"
                        :class="activeTab === 'distribusi' ? 'border-purple-600 text-purple-600' :
                            'border-transparent text-gray-500'"
                        class="py-3 border-b-2 font-medium text-sm transition"> Distribusi Peminjaman</button>
                    <button @click="activeTab = 'pengadaan'"
                        :class="activeTab === 'pengadaan' ? 'border-purple-600 text-purple-600' :
                            'border-transparent text-gray-500'"
                        class="py-3 border-b-2 font-medium text-sm transition">Riwayat Pengadaan</button>
                    <button @click="activeTab = 'pemindahan'"
                        :class="activeTab === 'pemindahan' ? 'border-purple-600 text-purple-600' :
                            'border-transparent text-gray-500'"
                        class="py-3 border-b-2 font-medium text-sm transition">Riwayat Pemindahan</button>
                </div>
                <div class="p-6 overflow-auto flex-1">
                    {{-- /! Konten Ringkasan  --}}
                    <div x-show="activeTab === 'ringkasan'">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <p class="text-sm font-medium text-purple-800">Total Pengadaan</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $detailBarang['totalPengadaan'] }}
                                </p>
                                <p class="text-xs text-purple-600 mt-1">Jumlah asli dari pembelian</p>
                            </div>

                            {{-- Kartu Total Unit (Stok Saat Ini di Ruangan Ini) --}}
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <p class="text-sm font-medium text-blue-800">Stok Saat Ini</p>
                                <p class="text-2xl font-bold text-blue-900">
                                    {{ $detailBarang['barang']->jumlah_total }}</p>
                                <p class="text-xs text-blue-600 mt-1">Di
                                    {{ $detailBarang['barang']->ruangan->nama_ruangan }}</p>
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

                    {{-- Konten Distribusi Peminjaman --}}
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
                    {{-- Koten Riwayat Pengadaan --}}
                    <div x-show="activeTab === 'pengadaan'" style="display: none;">
                        <div x-show="activeTab === 'pengadaan'" style="display: none;">
                            <div class="overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th
                                                class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Tgl. Pengadaan</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Jumlah</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Harga Satuan</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Sumber Dana</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">
                                                Lokasi Saat Diadakan</th>
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
                                                <td class="px-4 py-3 text-sm">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $pengadaan->sumberDana->nama_sumber ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $pengadaan->barang->ruangan->nama_ruangan ?? 'Data Lama' }}</td>
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
                    </div>
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
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Sumber Dana</label>

                            @if ($isAddingSumberDana)
                                {{-- Input Text untuk Sumber Dana Baru --}}
                                <div class="flex gap-2 mt-1">
                                    <input type="text" wire:model="sumberDanaBaru"
                                        placeholder="Nama Sumber Dana Baru"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                                    <button type="button" wire:click="simpanSumberDanaBaru"
                                        class="bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 text-xs font-bold">Simpan</button>
                                    <button type="button" wire:click="toggleSumberDanaBaru"
                                        class="bg-gray-300 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-400 text-xs font-bold">Batal</button>
                                </div>
                                @error('sumberDanaBaru')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            @else
                                {{-- Dropdown Sumber Dana --}}
                                <select wire:model="sumber_dana_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                                    <option value="">Pilih Sumber Dana</option>
                                    @foreach (\App\Models\SumberDana::all() as $sumber)
                                        <option value="{{ $sumber->id }}">{{ $sumber->nama_sumber }}</option>
                                    @endforeach

                                </select>

                                {{-- Tombol Kecil "Tambah Baru" --}}
                                <button type="button" wire:click="toggleSumberDanaBaru"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 flex items-center font-semibold focus:outline-none">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Sumber Dana Baru
                                </button>
                            @endif
                            @error('sumber_dana_id')
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

    {{-- Modal Pindahkan Barang --}}
    @if ($showPindahModal && $pindahBarang)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <form wire:submit.prevent="prosesPemindahan">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-bold text-gray-900">Pindahkan Barang</h3>
                        <p class="text-sm text-gray-500">Memindahkan stok <strong>{{ $pindahBarang->kode_barang }} -
                                {{ $pindahBarang->nama_barang }}</strong></p>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Info Asal --}}
                        <div class="bg-blue-50 p-3 rounded border border-blue-100">
                            <p class="text-xs text-blue-600 font-bold uppercase">Lokasi Asal</p>
                            <p class="text-sm font-medium">{{ $pindahBarang->ruangan->nama_ruangan }}</p>
                            <p class="text-xs text-gray-600 mt-1">Stok Tersedia:
                                <strong>{{ $pindahBarang->jumlah_saat_ini }}</strong>
                            </p>
                        </div>

                        {{-- Input Jumlah --}}
                        <div>
                            <label for="jumlahPindah" class="block text-sm font-medium text-gray-700">Jumlah yang
                                Dipindahkan</label>
                            <input type="number" wire:model="jumlahPindah" id="jumlahPindah"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                min="1" max="{{ $pindahBarang->jumlah_saat_ini }}">
                            @error('jumlahPindah')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Input Tujuan --}}
                        <div>
                            <label for="ruanganTujuanId" class="block text-sm font-medium text-gray-700">Pindah ke
                                Ruangan</label>
                            <select wire:model="ruanganTujuanId" id="ruanganTujuanId"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                                <option value="">Pilih ruangan tujuan...</option>
                                @foreach ($ruangans as $ruangan)
                                    {{-- Jangan tampilkan ruangan asal di opsi --}}
                                    @if ($ruangan->id != $pindahBarang->ruangan_id)
                                        <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('ruanganTujuanId')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded">
                            <p><strong>Catatan:</strong> Jika barang dengan kode yang sama sudah ada di ruangan tujuan,
                                stok akan digabungkan.</p>
                        </div>
                    </div>

                    <div class="p-6 border-t flex justify-end space-x-2 bg-gray-50 rounded-b-lg">
                        <button type="button" wire:click="closePindahModal"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 font-bold">Pindahkan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
