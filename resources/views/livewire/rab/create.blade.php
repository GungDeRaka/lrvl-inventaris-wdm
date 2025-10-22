<div class="max-w-6xl mx-auto p-6 bg-white shadow-xl rounded-2xl">
    {{-- 🔔 Notifikasi --}}
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-300 text-green-800 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-300 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- 🧾 Judul --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-8 border-b border-gray-200 pb-3">
        Form Pengajuan RAB Pengadaan Barang
    </h1>

    <form wire:submit.prevent="ajukanRab" class="space-y-8">
        {{-- 📝 Keterangan Umum --}}
        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                Keterangan / Alasan Pengajuan
            </label>
            <textarea wire:model="keterangan" id="keterangan" rows="3"
                class="w-full border-gray-300 focus:border-[#620F55] focus:ring-[#620F55] rounded-md shadow-sm text-sm">
            </textarea>
            @error('keterangan')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- ➕ Tambah Item --}}
        <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Item Barang</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="newItemNama"
                        class="w-full rounded-md border-gray-300 focus:border-[#620F55] focus:ring-[#620F55] text-sm">
                    @error('newItemNama')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Spesifikasi
                    </label>
                    <input type="text" wire:model="newItemSpec"
                        class="w-full rounded-md border-gray-300 focus:border-[#620F55] focus:ring-[#620F55] text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="newItemJumlah"
                        class="w-full rounded-md border-gray-300 focus:border-[#620F55] focus:ring-[#620F55] text-sm">
                    @error('newItemJumlah')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Satuan (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="newItemHarga"
                        class="w-full rounded-md border-gray-300 focus:border-[#620F55] focus:ring-[#620F55] text-sm">
                    @error('newItemHarga')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="text-right mt-5">
                <button type="button" wire:click="addItem"
                    class="bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-800 transition">
                    Tambah ke Daftar
                </button>
            </div>
        </div>

        {{-- 📋 Daftar Item --}}
        @if (!empty($items))
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Daftar Barang Diajukan</h3>

                <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#620F55] text-white text-sm uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Nama Barang</th>
                                <th class="px-4 py-3 text-left font-semibold">Spesifikasi</th>
                                <th class="px-4 py-3 text-center font-semibold">Jumlah</th>
                                <th class="px-4 py-3 text-right font-semibold">Harga Satuan</th>
                                <th class="px-4 py-3 text-right font-semibold">Harga Total</th>
                                <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($items as $index => $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-sm">{{ $item['nama'] }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $item['spesifikasi'] ?: '-' }}</td>
                                    <td class="px-4 py-3 text-center text-sm">{{ $item['jumlah'] }}</td>
                                    <td class="px-4 py-3 text-right text-sm">Rp
                                        {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-sm">Rp
                                        {{ number_format($item['total'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium transition">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @error('items')
                    <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                @enderror
            </div>
        @endif

        {{-- 🚀 Tombol Submit --}}
        <div class="text-center pt-6 border-t border-gray-200">
            @if (auth()->user()->peran === 'penjaga_gudang')
                <button type="submit"
                    class="bg-[#620F55] text-white px-6 py-2 rounded-md text-sm font-semibold hover:bg-[#4d0c44] transition">
                    Ajukan RAB
                </button>
            @else
                <p class="text-sm text-gray-500">
                    Hanya <strong>Penjaga Gudang</strong> yang dapat mengajukan RAB.
                </p>
            @endif
        </div>
    </form>
</div>
