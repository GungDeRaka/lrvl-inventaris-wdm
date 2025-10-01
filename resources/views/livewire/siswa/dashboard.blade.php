<div>
    {{-- Tombol Aksi --}}
    <div class="mb-6">
        <button wire:click="$set('showRequestModal', true)"
            class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg shadow-md">
            Buat Permintaan Peminjaman
        </button>
    </div>

    {{-- Riwayat Peminjaman --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Permintaan Anda</h2>
    <div class="space-y-4">
        @forelse($riwayat as $item)
            @php
                // Tentukan class untuk border kiri berdasarkan status
                $borderColorClass = match ($item->status) {
                    'dipinjam', 'disetujui' => 'border-blue-500',
                    'dikembalikan' => 'border-green-500',
                    'ditolak' => 'border-red-500',
                    default => 'border-gray-400',
                };

                // Tentukan class untuk badge status
                $statusBadgeClass = match ($item->status) {
                    'dipinjam', 'disetujui' => 'bg-blue-100 text-blue-800',
                    'dikembalikan' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp

            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 {{ $borderColorClass }}">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-800">{{ $item->barang->nama_barang }}</span>
                    <span class="text-sm font-semibold px-2 py-1 rounded-full {{ $statusBadgeClass }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Booking: {{ \Carbon\Carbon::parse($item->waktu_pinjam)->format('d M Y, H:i') }} -
                    {{ \Carbon\Carbon::parse($item->waktu_kembali)->format('H:i') }}
                </p>
            </div>
        @empty
            <p class="text-gray-500">Anda belum memiliki riwayat permintaan.</p>
        @endforelse
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
                            <label for="searchBarangSiswa" class="block text-sm font-medium text-gray-700">Cari
                                Barang</label>

                            <input type="text" id="searchBarangSiswa" wire:model.live.debounce.300ms="searchBarang"
                                placeholder="Ketik nama barang..." autocomplete="off" {{-- Mencegah autocomplete browser --}}
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                            {{-- Bagian ini akan menampilkan hasil pencarian --}}
                            @if (!empty($barangDitemukan) && !$selectedBarangId)
                                <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border">
                                    <ul class="max-h-40 overflow-auto">
                                        @forelse($barangDitemukan as $barang)
                                            <li wire:click="selectBarang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}')"
                                                class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $barang->nama_barang }} (Stok: {{ $barang->jumlah_saat_ini }})
                                            </li>
                                        @empty
                                            <li class="px-4 py-2 text-gray-500">Barang tidak ditemukan.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endif
                        </div>

                        @if ($selectedBarangId)
                            <div class="bg-gray-50 p-3 rounded-md border">
                                <p class="font-semibold text-sm">Barang Dipilih: {{ $selectedBarangNama }}</p>
                            </div>
                        @endif
                        @error('selectedBarangId')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror

                        {{-- Input lainnya --}}
                        <div>
                            <label for="ruang_pemakaian" class="block text-sm font-medium text-gray-700">Rencana Ruang
                                Penggunaan</label>
                            <input type="text" wire:model="ruang_pemakaian" id="ruang_pemakaian"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
