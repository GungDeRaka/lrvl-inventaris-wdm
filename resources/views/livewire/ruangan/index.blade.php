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
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Ruangan</h1>
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
                        <td class="px-5 py-4 text-sm">{{ $ruangan->nama_ruangan }}</td>
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            <button wire:click="edit({{ $ruangan->id }})"
                                class="font-semibold text-yellow-600 hover:text-yellow-900">Edit</button>
                            <button wire:click="konfirmasiHapus({{ $ruangan->id }})"
                                class="font-semibold text-red-600 hover:text-red-900 ml-4">Hapus</button>
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
</div>
