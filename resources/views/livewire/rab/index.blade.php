<div>
    {{-- ✅ Notifikasi --}}
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 border border-green-300">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-3 rounded-md bg-red-100 text-red-800 border border-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- ✅ Judul Halaman --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Persetujuan Pengajuan RAB</h1>

    {{-- ✅ Tabel RAB Diajukan --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-indigo-100 to-indigo-50 border-b border-indigo-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal Pengajuan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Diajukan Oleh</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rabDiajukan as $rab)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($rab->tanggal_pengajuan)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $rab->pengaju->name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $rab->keterangan ?: '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <button 
                                wire:click="showDetail({{ $rab->id }})" 
                                class="text-indigo-600 font-medium hover:underline">
                                Lihat Detail & Proses
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500 italic">
                            Tidak ada pengajuan RAB baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t bg-gray-50">
            {{ $rabDiajukan->links() }}
        </div>
    </div>

    {{-- ✅ Modal Detail & Proses RAB --}}
    @if($showDetailModal && $selectedRab)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-30 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-3xl max-h-[85vh] flex flex-col border border-gray-200">
                
                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-xl font-semibold text-gray-800">Detail Pengajuan RAB</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                {{-- Isi Modal --}}
                <div class="overflow-y-auto mb-4 space-y-3">
                    <p><span class="font-medium">Tanggal Pengajuan:</span> {{ \Carbon\Carbon::parse($selectedRab->tanggal_pengajuan)->format('d M Y') }}</p>
                    <p><span class="font-medium">Diajukan Oleh:</span> {{ $selectedRab->pengaju->name }}</p>
                    <p><span class="font-medium">Keterangan:</span> {{ $selectedRab->keterangan ?: '-' }}</p>

                    <h4 class="font-semibold text-md mt-4 mb-2 border-b pb-1">Item Barang Diajukan:</h4>
                    <div class="border rounded-lg overflow-hidden shadow-sm">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">Nama Barang</th>
                                    <th class="px-3 py-2 text-left">Spesifikasi</th>
                                    <th class="px-3 py-2 text-center">Jumlah</th>
                                    <th class="px-3 py-2 text-right">Harga Satuan</th>
                                    <th class="px-3 py-2 text-right">Harga Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @php $grandTotal = 0; @endphp
                                @foreach($selectedRab->items as $item)
                                    @php $grandTotal += $item->harga_total; @endphp
                                    <tr>
                                        <td class="px-3 py-2">{{ $item->nama_barang_baru }}</td>
                                        <td class="px-3 py-2">{{ $item->spesifikasi ?: '-' }}</td>
                                        <td class="px-3 py-2 text-center">{{ $item->jumlah }}</td>
                                        <td class="px-3 py-2 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-right">Rp {{ number_format($item->harga_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-indigo-50 font-semibold">
                                    <td colspan="4" class="px-3 py-2 text-right">Grand Total:</td>
                                    <td class="px-3 py-2 text-right text-indigo-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Catatan Kepala Gudang --}}
                    <div class="mt-4">
                        <label for="catatan_kepala" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan (Opsional)
                        </label>
                        <textarea wire:model="catatan_kepala" id="catatan_kepala" rows="2"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('catatan_kepala')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="pt-4 border-t flex justify-end space-x-2">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium transition">
                        Tutup
                    </button>
                    <button type="button" wire:click="prosesKeputusan('ditolak')"
                        class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white font-medium transition">
                        Tolak
                    </button>
                    <button type="button" wire:click="prosesKeputusan('disetujui')"
                        class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition">
                        Setujui
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
