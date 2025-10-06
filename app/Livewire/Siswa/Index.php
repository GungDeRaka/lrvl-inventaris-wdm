<?php

namespace App\Livewire\Siswa;

use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $siswa_id;
    public $nis, $nama, $email, $kelas, $no_hp, $password;
    public $showPasswordModal = false;
    public $passwordSiswaId;
    public $passwordSiswaNama;
    public $new_password;
    public $siswaIdToDelete;

    public function openModal()
    {
        $this->reset();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showPasswordModal = false;
    }

    public function simpanSiswa()
    {
        $rules = [
            'nis'   => ['required', 'string', Rule::unique('siswas')->ignore($this->siswa_id)],
            'nama'  => 'required|string|min:3',
            'email' => ['required', 'email', Rule::unique('siswas')->ignore($this->siswa_id)],
            'kelas' => 'required|string',
            'no_hp' => 'required|string',
        ];

        // Hanya validasi password saat membuat siswa baru
        if (!$this->siswa_id) {
            $rules['password'] = 'required|string|min:6';
        }

        $validatedData = $this->validate($rules);

        if ($this->siswa_id) {
            // Update
            $siswa = Siswa::find($this->siswa_id);
            $siswa->update($validatedData);
            session()->flash('message', 'Data siswa berhasil diperbarui.');
        } else {
            // Create
            $validatedData['password'] = Hash::make($this->password);
            Siswa::create($validatedData);
            session()->flash('message', 'Siswa baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->siswa_id = $id;
        $this->nis = $siswa->nis;
        $this->nama = $siswa->nama;
        $this->email = $siswa->email;
        $this->kelas = $siswa->kelas;
        $this->no_hp = $siswa->no_hp;
        $this->password = ''; // Kosongkan password saat edit
        $this->showModal = true;
    }

    public function openPasswordModal($id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->passwordSiswaId = $id;
        $this->passwordSiswaNama = $siswa->nama;
        $this->reset('new_password');
        $this->showPasswordModal = true;
    }

    // simpan password baru
    public function updatePassword()
    {
        $this->validate(['new_password' => 'required|string|min:6']);

        $siswa = Siswa::find($this->passwordSiswaId);
        $siswa->update([
            'password' => Hash::make($this->new_password)
        ]);

        session()->flash('message', 'Password untuk siswa ' . $this->passwordSiswaNama . ' berhasil diperbarui.');
        $this->closeModal();
    }
    public function konfirmasiHapus($id)
    {
        $this->siswaIdToDelete = $id;
    }

    public function hapusSiswa()
    {
        $siswa = Siswa::find($this->siswaIdToDelete);

        // Cek apakah siswa memiliki pinjaman yang belum dikembalikan
        if ($siswa && $siswa->transaksis()->where('status', '!=', 'dikembalikan')->exists()) {
            session()->flash('error', 'Gagal! Siswa ini masih memiliki pinjaman yang aktif.');
            $this->siswaIdToDelete = null;
            return;
        }

        if ($siswa) {
            $siswa->delete();
            session()->flash('message', 'Siswa berhasil dihapus.');
        }
        $this->siswaIdToDelete = null;
    }

    public function batalTangguhan($id)
    {
        $siswa = Siswa::find($id);
        if ($siswa) {
            $siswa->update(['is_ditangguhkan' => false]);
            session()->flash('message', 'Penangguhan untuk siswa ' . $siswa->nama . ' telah dibatalkan.');
        }
    }

    public function render()
    {
        return view('livewire.siswa.index', [
            'siswas' => Siswa::latest()->paginate(10)
        ]);
    }
}
