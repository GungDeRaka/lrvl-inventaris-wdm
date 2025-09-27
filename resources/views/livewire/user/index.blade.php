<div>
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h1>
        <button wire:click="openModal()" class=" bg-primary hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
            Tambah Pengguna Baru
        </button>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
             <thead>
                <tr class="bg-gray-200">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Peran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-5 py-4 text-sm">{{ $user->name }}</td>
                    <td class="px-5 py-4 text-sm">{{ $user->email }}</td>
                    <td class="px-5 py-4 text-sm">{{ $user->peran == 'kepala_gudang' ? 'Kepala Gudang' : 'Penjaga Gudang' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Pengguna --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <form wire:submit.prevent="simpanPengguna">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Pengguna Baru</h3>
                <div class="space-y-4">
                    {{-- Form inputs --}}
                    <div>
                        <label for="name" class="block text-sm font-medium">Nama</label>
                        <input type="text" wire:model="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium">Email</label>
                        <input type="email" wire:model="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium">Password</label>
                        <input type="password" wire:model="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="peran" class="block text-sm font-medium">Peran</label>
                        <select wire:model="peran" id="peran" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="">Pilih Peran</option>
                            <option value="kepala_gudang">Kepala Gudang</option>
                            <option value="penjaga_gudang">Penjaga Gudang</option>
                        </select>
                        @error('peran') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" wire:click="closeModal()" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>