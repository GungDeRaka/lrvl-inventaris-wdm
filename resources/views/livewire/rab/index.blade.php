{{-- //T0DO Perbaiki RAB pengajuan pengadaan barang: ketika input form RAB, berikan detail barang yang ingin diadakan akan dibawa ke ruangan mana, jumlah yang ingin diadakan, kode barang, etc yang sesuai dengan form penambahan barang --}}
{{-- ! kemungkinan sulit ✅ --}}

<div>
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- ============================================= --}}
    {{-- Tampilan untuk Penjaga Gudang --}}
    {{-- ============================================= --}}
    @if (auth()->user()->peran === 'penjaga_gudang')
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Riwayat Pengajuan RAB Anda</h1>

        {{-- Form Pengajuan RAB --}}
    
        {{-- Riwayat Pengajuan RAB Saya --}}
        <hr class="my-5 border-t-2">
        
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tgl. Pengajuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Diproses Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tgl. Keputusan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Catatan Kepala</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rabSaya as $rab)
                        <tr wire:click="showDetail({{ $rab->id }})" class="hover:bg-gray-100 cursor-pointer">
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($rab->tanggal_pengajuan)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span @class([
                                    'inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full',
                                    'bg-green-100 text-green-800' => $rab->status == 'disetujui',
                                    'bg-red-100 text-red-800' => $rab->status == 'ditolak',
                                    'bg-yellow-100 text-yellow-800' => $rab->status == 'diajukan',
                                    'bg-gray-100 text-gray-800' => !in_array($rab->status, [
                                        'disetujui',
                                        'ditolak',
                                        'diajukan',
                                    ]),
                                ])>
                                    {{ ucfirst($rab->status) }}
                                </span>

                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->peninjau->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $rab->tanggal_keputusan ? \Carbon\Carbon::parse($rab->tanggal_keputusan)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->catatan_kepala ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">Anda belum pernah mengajukan RAB.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($rabSaya->hasPages())
                <div class="p-4 border-t">{{ $rabSaya->links() }}</div>
            @endif
        </div>

        <button wire:click="openCreateModal"
            class="fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-purple-800 transition transform hover:scale-110 focus:outline-none z-50 flex items-center justify-center"
            title="Buat Pengajuan RAB Baru">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="mx-2 text-sm font-semibold">Ajukan RAB</span>
        </button>

        {{-- ============================================= --}}
        {{-- Tampilan untuk Kepala Gudang --}}
        {{-- ============================================= --}}
    @elseif(auth()->user()->peran === 'kepala_gudang')
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Pengajuan RAB</h1>

        {{-- Tabel RAB Menunggu Persetujuan --}}
        @if (isset($rabDiajukan) && $rabDiajukan->isNotEmpty())
            <div class="bg-white shadow-xl rounded-lg overflow-x-auto mb-8 border border-orange-300">
                <h3 class="text-lg font-bold p-4 border-b text-orange-600 bg-orange-50">Menunggu Persetujuan</h3>
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Tgl. Pengajuan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Diajukan Oleh</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Keterangan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($rabDiajukan as $rab)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($rab->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->pengaju->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->keterangan ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    <button wire:click="showDetail({{ $rab->id }})"
                                        class="font-semibold text-indigo-600 hover:underline">Lihat Detail &
                                        Proses</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($rabDiajukan->hasPages())
                    <div class="p-4 bg-gray-50 border-t">
                        {{ $rabDiajukan->links('livewire::tailwind', ['pageName' => 'diajukanPage']) }}</div>
                @endif
            </div>
        @endif

        {{-- Tabel Riwayat RAB Diproses --}}
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Riwayat RAB Diproses</h2>
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-fuchsia-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary uppercase tracking-wider">
                            Tgl. Pengajuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary uppercase tracking-wider">
                            Diajukan Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary uppercase tracking-wider">
                            Diproses Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary uppercase tracking-wider">
                            Tgl. Keputusan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary uppercase tracking-wider">
                            Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rabDiproses as $rab)
                        <tr wire:click="showDetail({{ $rab->id }})" class="hover:bg-gray-100 cursor-pointer">
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($rab->tanggal_pengajuan)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->pengaju->name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $rab->status == 'disetujui' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($rab->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->peninjau->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $rab->tanggal_keputusan ? \Carbon\Carbon::parse($rab->tanggal_keputusan)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $rab->catatan_kepala ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">Belum ada riwayat RAB yang
                                diproses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($rabDiproses->hasPages())
                <div class="p-4 border-t">
                    {{ $rabDiproses->links('livewire::tailwind', ['pageName' => 'diprosesPage']) }}</div>
            @endif
        </div>
    @endif

    {{-- Modal Detail & Proses RAB (Untuk Kepala Gudang) --}}
    @if ($showDetailModal && $selectedRab)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">Detail Pengajuan RAB: {{ $selectedRab->judul }}</h3>
                    @if (session()->has('message'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm"
                            role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm"
                            role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <button wire:click="closeModal"
                        class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
                </div>

                <div class="overflow-y-auto p-6 flex-1">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <p class="text-sm"><strong>Tgl.
                                Pengajuan:</strong><br>{{ \Carbon\Carbon::parse($selectedRab->tanggal_pengajuan)->format('d M Y') }}
                        </p>
                        <p class="text-sm"><strong>Diajukan Oleh:</strong><br>{{ $selectedRab->pengaju->name }}</p>
                    </div>
                    <p class="text-sm mb-4"><strong>Keterangan:</strong><br>{{ $selectedRab->keterangan ?: '-' }}</p>

                    <h4 class="font-semibold text-md mb-2">Item Barang Diajukan:</h4>
                    <div class="border rounded-md overflow-hidden">
                        <table class="w-full table-auto text-sm">
                            <thead class="bg-gray-50">
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
                                @foreach ($selectedRab->items as $item)
                                    @php $grandTotal += $item->harga_total; @endphp
                                    <tr>
                                        <td class="px-3 py-2">{{ $item->nama_barang_baru }}</td>
                                        <td class="px-3 py-2">{{ $item->spesifikasi ?: '-' }}</td>
                                        <td class="px-3 py-2 text-center">{{ $item->jumlah }}</td>
                                        <td class="px-3 py-2 text-right">Rp
                                            {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-right">Rp
                                            {{ number_format($item->harga_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="4" class="px-3 py-2 text-right text-base">Grand Total:</td>
                                    <td class="px-3 py-2 text-right text-base">Rp
                                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <label for="catatan_kepala" class="block text-sm font-medium text-gray-700">
                            @if ($selectedRab->status == 'diajukan' && auth()->user()->peran === 'kepala_gudang')
                                Catatan (Opsional)
                            @else
                                Catatan Kepala Gudang
                            @endif
                        </label>
                        <textarea wire:model="catatan_kepala" id="catatan_kepala" rows="2"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary
                           {{-- Tambahkan class ini untuk menonaktifkan tampilan --}}
                           @if ($selectedRab->status != 'diajukan' || auth()->user()->peran === 'penjaga_gudang') bg-gray-100 cursor-not-allowed @endif
                    "
                            {{-- Tambahkan atribut disabled --}} @if ($selectedRab->status != 'diajukan' || auth()->user()->peran === 'penjaga_gudang') disabled @endif></textarea>
                        @error('catatan_kepala')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div
                    class="p-6 border-t flex {{ $selectedRab->status == 'diajukan' && auth()->user()->peran === 'kepala_gudang' ? 'justify-end' : 'justify-center' }} space-x-3 flex-shrink-0">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">Tutup</button>

                    {{-- Tombol aksi hanya muncul jika status 'diajukan' dan user adalah Kepala Gudang --}}
                    @if ($selectedRab->status == 'diajukan' && auth()->user()->peran === 'kepala_gudang')
                        <button type="button" wire:click="prosesKeputusan('ditolak')"
                            class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700">Tolak</button>
                        <button type="button" wire:click="prosesKeputusan('disetujui')"
                            class="px-4 py-2 bg-primary text-white rounded-md shadow-sm hover:bg-purple-800">Setujui</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Buat RAB Baru --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-4xl max-h-[92vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">

                {{-- Header --}}
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">Buat Pengajuan RAB Baru</h2>
                    <button wire:click="closeCreateModal"
                        class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-6 py-5">

                    <form wire:submit.prevent="ajukanRab">

                        {{-- Judul --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Pengajuan*</label>
                            <input type="text" wire:model="judul"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600"
                                placeholder="Contoh: Pengadaan Alat Tulis Semester Genap">
                            @error('judul')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Panjang judul 5 - 30 karakter.</p>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-7">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan / Alasan</label>
                            <textarea rows="2" wire:model="keterangan"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600"></textarea>
                            @error('keterangan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tambah Item --}}
                        <div class="border border-indigo-200 rounded-xl bg-indigo-50 px-5 py-4 mb-6">
                            <h3 class="text-lg font-bold text-indigo-700 mb-3">Tambah Item Barang</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                                {{-- Nama --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Nama Barang*</label>
                                    <input type="text" wire:model="newItemNama"
                                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('newItemNama')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Spec --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Spesifikasi</label>
                                    <input type="text" wire:model="newItemSpec"
                                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('newItemSpec')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Jumlah --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Jumlah*</label>
                                    <input type="number" wire:model="newItemJumlah"
                                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('newItemJumlah')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Harga --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Harga Satuan (Rp)*</label>
                                    <input type="number" wire:model="newItemHarga"
                                        class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('newItemHarga')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="text-right mt-4">
                                <button type="button" wire:click="addItem"
                                    class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm font-semibold shadow hover:bg-gray-800">
                                    Tambah ke Daftar
                                </button>
                            </div>
                        </div>

                        {{-- Daftar Item --}}
                        @if (!empty($items))
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3">Daftar Barang Diajukan</h3>

                                <div class="overflow-x-auto rounded-xl border shadow-sm">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Nama</th>
                                                <th class="px-4 py-3 text-left">Spesifikasi</th>
                                                <th class="px-4 py-3 text-center">Jumlah</th>
                                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                                                <th class="px-4 py-3 text-right">Total</th>
                                                <th class="px-4 py-3 text-center">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($items as $index => $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3">{{ $item['nama'] }}</td>
                                                    <td class="px-4 py-3">{{ $item['spesifikasi'] ?: '-' }}</td>
                                                    <td class="px-4 py-3 text-center">{{ $item['jumlah'] }}</td>
                                                    <td class="px-4 py-3 text-right">
                                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        Rp {{ number_format($item['total'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button wire:click="removeItem({{ $index }})"
                                                            class="text-red-600 hover:text-red-800 font-semibold text-xs">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @error('items')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-3 border-t pt-4">
                            <button type="button" wire:click="closeCreateModal"
                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Batal
                            </button>

                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg shadow hover:bg-indigo-700">
                                Ajukan RAB
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    @endif

</div>
