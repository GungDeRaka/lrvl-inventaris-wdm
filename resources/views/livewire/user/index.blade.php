<div>
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 -z-10 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex justify-between items-center space-x-2 w-full md:w-auto mb-6">
        <div class="flex justify-around items-center space-x-2">
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h1>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama admin..."
                class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm mr-4">
        </div>
        <button wire:click="openModal()" class="bg-primary hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
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
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-5 py-4 text-sm">{{ $user->name }}</td>
                        <td class="px-5 py-4 text-sm">{{ $user->email }}</td>
                        <td class="px-5 py-4 text-sm">
                            {{ $user->peran == 'kepala_gudang' ? 'Kepala Gudang' : 'Penjaga Gudang' }}</td>
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            <button wire:click="edit({{ $user->id }})"
                                class="font-semibold text-yellow-600 hover:text-yellow-900">Edit</button>
                            <button wire:click="openPasswordModal({{ $user->id }})"
                                class="font-semibold text-indigo-600 hover:text-indigo-900 ml-4">Ganti Password</button>
                            <button wire:click="konfirmasiHapus({{ $user->id }})"
                                class="font-semibold text-red-600 hover:text-red-900 ml-4 disabled:opacity-50 disabled:cursor-not-allowed"
                                {{-- Tombol akan nonaktif jika ID user sama dengan ID yang sedang login --}} {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah/Edit Pengguna --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="simpanPengguna">
                    <input type="hidden" wire:model="user_id">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $user_id ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium">Nama</label>
                            <input type="text" wire:model="name" id="name"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input type="email" wire:model="email" id="email"
                                class="mt-1 block w-full border-gray-300 rounded-md" {{ $user_id ? 'readonly' : '' }}>
                            @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$user_id)
                            <div>
                                <label for="password" class="block text-sm font-medium">Password</label>
                                <input type="password" wire:model="password" id="password"
                                    class="mt-1 block w-full border-gray-300 rounded-md">
                                @error('password')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        <div>
                            <label for="peran" class="block text-sm font-medium">Peran</label>
                            <select wire:model="peran" id="peran"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="">Pilih Peran</option>
                                <option value="kepala_gudang">Kepala Gudang</option>
                                <option value="penjaga_gudang">Penjaga Gudang</option>
                            </select>
                            @error('peran')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded">{{ $user_id ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Ganti Password --}}
    @if ($showPasswordModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="updatePassword">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ganti Password untuk: {{ $passwordUserName }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="new_password" class="block text-sm font-medium">Password Baru</label>
                            <input type="password" wire:model="new_password" id="new_password"
                                class="mt-1 block w-full border-gray-300 rounded-md" autofocus>
                            @error('new_password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal konfirmasi hapus admin --}}
    @if ($userIdToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus Pengguna</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Anda yakin ingin menghapus pengguna <strong
                        class="text-primary">"{{ $userNameToDelete }}"</strong>? Tindakan ini tidak bisa
                    dibatalkan.
                </p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('userIdToDelete', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="hapusPengguna" class="px-4 py-2 bg-red-600 text-white rounded">Ya,
                        Hapus</button>
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
</div>
