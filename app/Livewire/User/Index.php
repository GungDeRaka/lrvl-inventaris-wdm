<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $user_id;
    public $name, $email, $password, $peran;
    public $showPasswordModal = false;
    public $passwordUserId;
    public $passwordUserName;
    public $new_password;
    public $userIdToDelete;
    public $userNameToDelete;
    public $search = '';
    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'peran' => 'required|in:kepala_gudang,penjaga_gudang',
    ];

    public function openModal()
    {
        $this->reset(['name', 'email', 'password', 'peran', 'user_id']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showPasswordModal = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    // fungsi simpan pengguna
    public function simpanPengguna()
    {
        $rules = [
            'name' => 'required|string|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user_id)],
            'peran' => 'required|in:kepala_gudang,penjaga_gudang',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|string|min:8';
        }

        $validatedData = $this->validate($rules);

        if ($this->user_id) {
            // Update User
            $user = User::find($this->user_id);
            $user->update($validatedData);
            session()->flash('message', 'Data pengguna berhasil diperbarui.');
        } else {
            // Create User
            $validatedData['password'] = Hash::make($this->password);
            User::create($validatedData);
            session()->flash('message', 'Pengguna baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->peran = $user->peran;
        $this->password = ''; // Kosongkan password saat edit
        $this->showModal = true;
    }

    // Method untuk modal ganti password
    public function openPasswordModal($id)
    {
        $user = User::findOrFail($id);
        $this->passwordUserId = $id;
        $this->passwordUserName = $user->name;
        $this->new_password = '';
        $this->showPasswordModal = true;
    }

    public function updatePassword()
    {
        $this->validate(['new_password' => 'required|string|min:8']);

        $user = User::find($this->passwordUserId);
        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        session()->flash('message', 'Password untuk ' . $this->passwordUserName . ' berhasil diperbarui.');
        $this->closeModal();
    }

    public function konfirmasiHapus($id)
    {
        // Cek keamanan: jangan biarkan user menghapus akunnya sendiri
        if ($id == Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::find($id);
        $this->userIdToDelete = $id;
        $this->userNameToDelete = $user->name;
    }

    // fungsi hapus pengguna
    public function hapusPengguna()
    {
        $user = User::find($this->userIdToDelete);
        if ($user) {
            $user->delete();
            session()->flash('message', 'Pengguna berhasil dihapus.');
        }

        // Tutup modal
        $this->userIdToDelete = null;
    }
    public function render()
    {
        return view('livewire.user.index', [
            'users' => User::where('name','like', '%'. $this->search . '%')->latest()->paginate(10),
        ]);
    }
}
