<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Backdrop Gelap --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" 
             wire:click="closeCreateModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Panel Modal --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            
            {{-- Header --}}
            <div class="bg-indigo-900 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Buat Pengajuan RAB Baru</h3>
                <button wire:click="closeCreateModal" class="text-indigo-200 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6 max-h-[80vh] overflow-y-auto">
                
                {{-- Form Judul & Keterangan --}}
                <div class="mb-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Judul Pengajuan*</label>
                        <input type="text" wire:model="judul" placeholder="Contoh: Pengadaan Komputer Lab A" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('judul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Keterangan / Alasan</label>
                        <textarea wire:model="keterangan" rows="2" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>
                </div>

                {{-- AREA TAMBAH ITEM --}}
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">Input Item Barang</h4>

                    {{-- Toggle Mode Input --}}
                    <div class="flex space-x-6 mb-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="modeInput" value="baru" class="form-radio text-indigo-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Barang Baru</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="modeInput" value="restock" class="form-radio text-indigo-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Tambah Stok (Restock)</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        
                        {{-- Field Nama / Pilih Barang --}}
                        <div class="lg:col-span-2">
                            @if($modeInput == 'baru')
                                <label class="block text-xs font-semibold text-gray-600 uppercase">Nama Barang Baru</label>
                                <input type="text" wire:model="newItemNama" placeholder="Nama Barang..." 
                                       class="w-full mt-1 rounded border-gray-300 text-sm focus:ring-indigo-500">
                                @error('newItemNama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @else
                                <label class="block text-xs font-semibold text-gray-600 uppercase">Pilih Barang Lama</label>
                                <select wire:model="existingBarangId" class="w-full mt-1 rounded border-gray-300 text-sm focus:ring-indigo-500">
                                    <option value="">-- Cari Barang --</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama_barang }}</option>
                                    @endforeach
                                </select>
                                @error('existingBarangId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        {{-- Spesifikasi (Hanya Barang Baru) --}}
                        <div class="{{ $modeInput == 'restock' ? 'hidden' : '' }}">
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Spesifikasi</label>
                            <input type="text" wire:model="newItemSpec" class="w-full mt-1 rounded border-gray-300 text-sm focus:ring-indigo-500">
                        </div>

                        {{-- Sumber Dana --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Sumber Dana</label>
                            <select wire:model="newItemSumberId" class="w-full mt-1 rounded border-gray-300 text-sm focus:ring-indigo-500">
                                <option value="">-- Pilih --</option>
                                @foreach($sumberDanas as $sd)
                                    <option value="{{ $sd->id }}">{{ $sd->nama_sumber }}</option>
                                @endforeach
                            </select>
                            @error('newItemSumberId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Jumlah --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Jumlah</label>
                            <input type="number" wire:model="newItemJumlah" class="w-full mt-1 rounded border-gray-300 text-sm focus:ring-indigo-500">
                        </div>

                        {{-- Harga --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Harga Satuan</label>
                            <input type="number" wire:model="newItemHarga" class="w-full mt-1 rounded border-gray-300 text-sm focus:ring-indigo-500">
                        </div>
                        
                        {{-- Tombol Tambah --}}
                        <div class="flex items-end">
                            <button wire:click="addItem" class="w-full bg-gray-800 text-white py-2 rounded text-sm hover:bg-black transition">
                                + Tambah
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tabel Daftar Item Sementara --}}
                @if(count($items) > 0)
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Barang</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Spek</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Jml</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($items as $index => $item)
                            <tr>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                    {{ $item['nama'] }}
                                    @if(isset($item['barang_id']) && $item['barang_id'])
                                        <span class="ml-1 text-[10px] bg-green-100 text-green-800 px-1 rounded">Restock</span>
                                    @else
                                        <span class="ml-1 text-[10px] bg-blue-100 text-blue-800 px-1 rounded">Baru</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $item['spesifikasi'] }}</td>
                                <td class="px-4 py-2 text-sm text-center">{{ $item['jumlah'] }}</td>
                                <td class="px-4 py-2 text-sm text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 text-xs">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @error('items') <p class="text-red-500 text-sm mt-2 text-center font-bold">{{ $message }}</p> @enderror

            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                <button wire:click="closeCreateModal" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                <button wire:click="ajukanRab" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-lg transform active:scale-95 transition">
                    Ajukan RAB Sekarang
                </button>
            </div>
        </div>
    </div>
</div>