<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}</div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Siswa</h1>
        <button wire:click="openModal()" class="bg-primary text-white font-bold py-2 px-4 rounded">Tambah Siswa
            Baru</button>
    </div>

    {{-- Tabel Siswa --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">NIS</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">No.Hp</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Kelas</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Status Akun</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-5 py-4 text-sm">{{ $siswa->nis }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->nama }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->email }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->no_hp }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->kelas }}</td>
                        {{-- status akun --}}
                        <td class="px-5 py-4 text-sm">
                            @if ($siswa->is_ditangguhkan)
                                <span
                                    class="px-2 py-1 font-semibold leading-tight rounded-full bg-red-200 text-red-900">Ditangguhkan</span>
                            @else
                                <span
                                    class="px-2 py-1 font-semibold leading-tight rounded-full bg-green-200 text-green-900">Aktif</span>
                            @endif
                        </td>
                        {{-- tombol aksi --}}
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            @if ($siswa->is_ditangguhkan)
                                <button wire:click="batalTangguhan({{ $siswa->id }})"
                                    class="font-semibold text-green-600 hover:text-green-900">Batal Tangguhan</button>
                            @endif
                            <button wire:click="edit({{ $siswa->id }})"
                                class="font-semibold text-yellow-600 hover:text-yellow-900">Edit</button>

                            <button wire:click="openPasswordModal({{ $siswa->id }})"
                                class="font-semibold text-indigo-600 hover:text-indigo-900 ml-4">Ganti Password</button>

                            <button wire:click="konfirmasiHapus({{ $siswa->id }})"
                                class="font-semibold text-red-600 hover:text-red-900 ml-4">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $siswas->links() }}</div>
    </div>

    {{-- Modal Tambah/Edit Siswa --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="simpanSiswa">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $siswa_id ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium">NIS</label>
                            <input type="text" wire:model="nis" id="nis"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('nis')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="nama" class="block text-sm font-medium">Nama Lengkap</label>
                            <input type="text" wire:model="nama" id="nama"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('nama')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input type="email" wire:model="email" id="email"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="kelas" class="block text-sm font-medium">Kelas</label>
                            <input type="text" wire:model="kelas" id="kelas"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('kelas')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium">No. HP</label>
                            <input type="text" wire:model="no_hp" id="no_hp"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('no_hp')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$siswa_id)
                            <div>
                                <label for="password" class="block text-sm font-medium">Password</label>
                                <input type="password" wire:model="password" id="password"
                                    class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Min. 6 karakter">
                                @error('password')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded">{{ $siswa_id ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Ganti Password Siswa --}}
    @if ($showPasswordModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="updatePassword">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ganti Password untuk: {{ $passwordSiswaNama }}
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
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Update
                            Password</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($siswaIdToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <p>Anda yakin ingin menghapus siswa ini?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('siswaIdToDelete', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="hapusSiswa" class="px-4 py-2 bg-red-600 text-white rounded">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
