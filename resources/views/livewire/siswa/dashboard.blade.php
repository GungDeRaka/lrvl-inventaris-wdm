<div>
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
            <button wire:click="setActiveTab('riwayat')"
                class="py-3 px-1 border-b-2 font-medium text-sm {{ $activeTab == 'riwayat' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Riwayat Permintaan Anda
            </button>
            <button wire:click="setActiveTab('ketersediaan')"
                class="py-3 px-1 border-b-2 font-medium text-sm {{ $activeTab == 'ketersediaan' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Ketersediaan Barang
            </button>
        </nav>
    </div>

    {{-- Konten Tab --}}
    <div>
        {{-- Konten untuk Tab Riwayat --}}
        @if ($activeTab == 'riwayat')
            <div class="space-y-3">
                @forelse($riwayat as $item)
                    @php
                        // Logika penentuan warna class
                    @endphp
                    <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 {{-- ... (kode border color Anda) ... --}}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">
                                    @foreach ($item->barangs as $barang)
                                        {{ $barang->nama_barang }} <strong>({{ $barang->pivot->kuantitas }}
                                            unit)</strong>
                                    @endforeach
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Booking: {{ \Carbon\Carbon::parse($item->waktu_pinjam)->format('d M, H:i') }} -
                                    {{ \Carbon\Carbon::parse($item->waktu_kembali)->format('d M, H:i') }}
                                </p>
                            </div>
                            <span
                                class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{-- ... (kode badge class Anda) ... --}}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                        @if ($item->status == 'ditolak' && $item->alasan_penolakan)
                            <div class="mt-2 p-2 bg-red-50 border-l-4 border-red-400 text-red-700 text-xs">
                                <p><strong class="font-semibold">Alasan:</strong> {{ $item->alasan_penolakan }}</p>
                            </div>
                        @endif
                        @if ($item->status == 'diajukan' || $item->status == 'disetujui')
                            <div class="mt-2 text-right">
                                <button wire:click="batalkanPermintaan({{ $item->id }})" wire:confirm="Anda yakin?"
                                    class="text-xs font-semibold text-red-600 hover:underline">Batalkan</button>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Anda belum memiliki riwayat permintaan.</p>
                @endforelse
            </div>
        @endif

        {{-- Konten untuk Tab Ketersediaan Barang --}}
        @if ($activeTab == 'ketersediaan')
            <div class="space-y-6">
                @foreach ($ruangansDenganBarang as $ruangan)
                    @if ($ruangan->barangs->isNotEmpty())
                        <div>
                            <h3 class="text-md font-bold text-gray-800 mb-2 border-b pb-2">{{ $ruangan->nama_ruangan }}
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4">
                                @foreach ($ruangan->barangs as $barang)
                                    <div class="bg-white p-3 rounded-lg shadow-sm text-center">
                                        <p class="font-semibold text-sm text-gray-900">{{ $barang->nama_barang }}</p>
                                        <p class="text-xs text-gray-500">Stok: {{ $barang->jumlah_saat_ini }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
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
</div>
