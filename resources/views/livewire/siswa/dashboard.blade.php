<div>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Hai {{ auth()->guard('siswa')->user()->nama }}, ada barang yang mau dipinjam?
    </h1>

    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">{{ session('error') }}
        </div>
    @endif

    {{-- Tombol Aksi Utama --}}
    <div class="mb-6">
        <button wire:click="$set('showRequestModal', true)"
            {{ auth()->guard('siswa')->user()->is_ditangguhkan ? 'disabled' : '' }}
            class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            Buat Permintaan Peminjaman
        </button>
    </div>

    {{-- Navigasi Tab --}}
    <div class="mb-4 border-b border-gray-200">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button wire:click="setActiveTab('ketersediaan')"
                class="py-3 px-1 border-b-2 font-medium text-sm {{ $activeTab == 'ketersediaan' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Ketersediaan Barang
            </button>
            <button wire:click="setActiveTab('riwayat')"
                class="py-3 px-1 border-b-2 font-medium text-sm {{ $activeTab == 'riwayat' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Riwayat Permintaan Anda
            </button>
        </nav>
    </div>

    <hr class="mb-2 border-t-4 border-primary">

    {{-- Konten Tab --}}
    <div>
        {{-- Konten untuk Tab Riwayat --}}
        @if ($activeTab == 'riwayat')
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-primary">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Barang yang
                                Dipinjam</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Jadwal Booking
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($riwayat as $item)
                            @php
                                // Tentukan class untuk badge status
                                $statusBadgeClass = match ($item->status) {
                                    'disetujui' => 'bg-cyan-100 text-cyan-800',
                                    'dipinjam' => 'bg-blue-100 text-blue-800',
                                    'dikembalikan' => 'bg-green-100 text-green-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                    'menunggu-konfirmasi' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800', // Untuk 'diajukan'
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    @foreach ($item->barangs as $barang)
                                        <span class="block">{{ $barang->nama_barang }} ({{ $barang->pivot->kuantitas }}
                                            unit)</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->waktu_pinjam)->format('d M, H:i') }} -
                                    {{ \Carbon\Carbon::parse($item->waktu_kembali)->format('d M, H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusBadgeClass }}">
                                        {{ $item->status == 'menunggu-konfirmasi' ? 'Menunggu Konfirmasi' : ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($item->status == 'diajukan' || $item->status == 'disetujui')
                                        <button wire:click="batalkanPermintaan({{ $item->id }})"
                                            wire:confirm="Anda yakin?"
                                            class="font-semibold text-red-600 hover:underline">Batalkan</button>
                                    @endif
                                    @if ($item->status == 'dipinjam')
                                        <button wire:click="bukaModalPengembalian({{ $item->id }})"
                                            class="font-semibold text-indigo-600 hover:underline">Ajukan
                                            Pengembalian</button>
                                    @endif
                                </td>
                            </tr>
                            @if ($item->status == 'ditolak' && $item->alasan_penolakan)
                                <tr>
                                    <td colspan="4" class="px-4 pb-3 -pt-2 bg-red-50">
                                        <p class="text-xs text-red-700"><strong class="font-semibold">Alasan:</strong>
                                            {{ $item->alasan_penolakan }}</p>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500">Anda belum memiliki riwayat
                                    permintaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Konten untuk Tab Ketersediaan Barang --}}
        @if ($activeTab == 'ketersediaan')
            {{-- Header dengan Filter Dropdown --}}
            <div class="flex items-center gap-2 mb-4">
                <h2 class="text-lg font-semibold text-gray-700 whitespace-nowrap">Daftar barang tersedia di:</h2>

                <div class="relative">
                    <select wire:model.live="filterRuangan"
                        class="block appearance-none w-full bg-transparent border-none text-gray-700 font-semibold py-2 pr-8 rounded leading-tight focus:outline-none focus:bg-transparent focus:border-none">
                        <option value="">Semua Ruangan</option>
                        @foreach ($semuaRuangan as $ruangan)
                            <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                        @endforeach
                    </select>
                    {{-- <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div> --}}
                </div>
            </div>

            {{-- Tabel Ketersediaan Barang Terpadu --}}
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-primary">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Nama Barang
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Stok Tersedia
                            </th>
                            {{-- Tampilkan kolom Ruangan hanya jika tidak difilter --}}
                            @if (!$filterRuangan)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Ruangan
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($semuaBarangTersedia as $barang)
                            <tr class="block sm:table-row">
                                <td class="px-4 py-3 text-sm font-semibold sm:font-normal" data-label="Nama Barang">
                                    {{ $barang->nama_barang }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600" data-label="Kategori">
                                    {{ $barang->kategori->nama_kategori }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600" data-label="Stok Tersedia">
                                    {{ $barang->jumlah_saat_ini }}</td>
                                @if (!$filterRuangan)
                                    <td class="px-4 py-3 text-sm text-gray-600" data-label="Ruangan">
                                        {{ $barang->ruangan->nama_ruangan }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500">Tidak ada barang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>
    {{-- Modal Form Permintaan --}}
    @if ($showRequestModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
                <form wire:submit.prevent="ajukanPeminjaman">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Permintaan Peminjaman</h3>

                    <div class="space-y-4">
                        {{-- Pencarian Barang --}}
                        <div class="relative">
                            <label for="searchBarangSiswa" class="block text-sm font-medium text-gray-700">Cari & Tambah
                                Barang</label>
                            <input type="text" id="searchBarangSiswa" wire:model.live.debounce.300ms="searchBarang"
                                placeholder="Ketik nama barang..." autocomplete="off"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                            @if (!empty($barangDitemukan))
                                <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border">
                                    <ul class="max-h-60 overflow-auto">
                                        @forelse($barangDitemukan as $barang)
                                            @if ($barang->jumlah_saat_ini > 0)
                                                {{-- BARANG TERSEDIA (Bisa Diklik) --}}
                                                <li wire:click="tambahKeKeranjang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}', '{{ $barang->ruangan->nama_ruangan }}')"
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                                    {{ $barang->nama_barang }} (Stok: {{ $barang->jumlah_saat_ini }})
                                                    <small class="block text-xs text-gray-500">Asal:
                                                        {{ $barang->ruangan->nama_ruangan }}</small>
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

                        {{-- KERANJANG PEMINJAMAN --}}
                        @if (!empty($keranjang))
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Barang yang Akan
                                    Dipinjam:</label>
                                <div class="mt-2 space-y-2 border rounded-md p-2 max-h-40 overflow-auto">
                                    @foreach ($keranjang as $index => $item)
                                        <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                            <div>
                                                <span class="text-sm font-semibold">{{ $item['nama'] }}</span>
                                                <small class="block text-xs text-gray-500">Asal:
                                                    {{ $item['asal'] }}</small>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                {{-- Tombol Kuantitas --}}
                                                <button type="button"
                                                    wire:click="decrementKuantitas({{ $index }})"
                                                    class="font-bold">-</button>
                                                <span
                                                    class="text-sm w-8 text-center border rounded">{{ $item['kuantitas'] }}</span>
                                                <button type="button"
                                                    wire:click="incrementKuantitas({{ $index }})"
                                                    class="font-bold">+</button>
                                                {{-- Tombol Hapus --}}
                                                <button type="button"
                                                    wire:click="hapusDariKeranjang({{ $index }})"
                                                    class="text-red-500 hover:text-red-700 text-xs font-bold ml-2">HAPUS</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @error('keranjang')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror

                        {{-- Input lainnya --}}
                        <div>
                            <label for="ruang_pemakaian_siswa" class="block text-sm font-medium text-gray-700">Rencana
                                Ruang Penggunaan</label>
                            <select wire:model="ruang_pemakaian" id="ruang_pemakaian_siswa"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Ruangan</option>
                                @foreach ($ruangans as $ruangan)
                                    @php
                                        $isAsalRuangan = isset($this->asalRuangan[$ruangan->nama_ruangan]);
                                    @endphp
                                    <option value="{{ $ruangan->nama_ruangan }}"
                                        {{ $isAsalRuangan ? 'disabled' : '' }}
                                        class="{{ $isAsalRuangan ? 'text-gray-400 cursor-not-allowed' : '' }}">
                                        {{ $ruangan->nama_ruangan }}
                                        @if ($isAsalRuangan)
                                            (Ruangan Asal:
                                            {{ $this->asalRuangan[$ruangan->nama_ruangan]->pluck('nama_barang')->join(', ') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('ruang_pemakaian')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="waktu_pinjam" class="block text-sm font-medium text-gray-700">Waktu
                                    Pinjam</label>
                                <input type="datetime-local" wire:model="waktu_pinjam" id="waktu_pinjam"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('waktu_pinjam')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="waktu_kembali" class="block text-sm font-medium text-gray-700">Waktu
                                    Kembali</label>
                                <input type="datetime-local" wire:model="waktu_kembali" id="waktu_kembali"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('waktu_kembali')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showRequestModal', false)"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Ajukan
                            Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Pengembalian Mandiri --}}
    @if ($showReturnModal && $returnTransaksi)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Form Pengembalian Barang</h3>
                <p class="text-sm text-gray-600 mb-4">Laporkan jika ada barang yang rusak saat dikembalikan. Harap isi
                    dengan jujur.</p>

                <div class="space-y-4 max-h-60 overflow-auto">
                    @foreach ($returnTransaksi->barangs as $barang)
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                            <span class="text-sm">{{ $barang->nama_barang }} (Dipinjam:
                                {{ $barang->pivot->kuantitas }})</span>
                            <div>
                                <label for="rusak_{{ $barang->id }}" class="text-sm mr-2">Jumlah Rusak:</label>
                                <input type="number" id="rusak_{{ $barang->id }}"
                                    wire:model="kerusakanDilaporkan.{{ $barang->id }}"
                                    class="w-20 text-sm border-gray-300 rounded-md" min="0"
                                    max="{{ $barang->pivot->kuantitas }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" wire:click="$set('showReturnModal', false)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="ajukanPengembalian"
                        wire:confirm="Anda yakin data kerusakan yang diisi sudah benar?"
                        class="px-4 py-2 bg-primary text-white rounded">Ajukan Pengembalian</button>
                </div>
            </div>
        </div>
    @endif
</div>
